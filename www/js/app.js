$(function (){
    $.fn.enterKey = function (fnc) {
        return this.each(function () {
            $(this).keypress(function (ev) {
                var keycode = (ev.keyCode ? ev.keyCode : ev.which);
                if (keycode == '13') {
                    fnc.call(this, ev);
                }
            })
        })
    }

    $.fn.escKey = function (fnc) {
        return this.each(function () {
            $(this).keypress(function (ev) {
                var keycode = (ev.keyCode ? ev.keyCode : ev.which);
                if (keycode == '27') {
                    fnc.call(this, ev);
                }
            })
        })
    }

    var api = function (url, data, fnc) {
        $.ajax({
            type: 'post',
            url: url,
            data: data
        }).done(function(data) {
            if(data.data.status === 'success') {
                fnc.call(this, data);
            }
        }).fail(function(error) {
            alert(error.responseJSON.error);
        });
    }

    $(document).on('change', '.toggle', function() {
        var li = $(this).closest('li'),
            checked = $(this).prop('checked');

        var status = (checked) ? 1 : 0,
            data = {'itemId': li.data('id'),'status': status},
            url = '/api/change-status';
        api(url, data, function() {
            if(checked) {
                li.addClass('completed');
                if($('.toggle:checked').length === $('.todo-list li').length) {
                    $('.toggle-all').prop('checked', true);
                }
            } else {
                li.removeClass('completed');
                if($('.toggle:checked').length !== $('.todo-list li').length) {
                    $('.toggle-all').prop('checked', false);
                }
            }
        });

    });

    $(document).on('change', '.todo-list', function() {
        $('.todo-count strong').text($('.todo-list li:visible').length);
    });

    $('.todo-list').on('click', '.destroy', function(event) {
        var li = $(event.target).closest('li'),
            data = {'itemId': li.attr('data-id')},
            url = '/api/delete';

        api(url, data, function() {
            li.remove();
            $('.todo-list').trigger('change');
        });
    });

    $('.filters').on('click', 'a', function(event) {
        var element = $(event.target),
            filter = element.attr('href');

        $('.filters a').removeClass('selected');
        element.addClass('selected');

        switch(filter) {
            case '#/':
                $('.todo-list li').show();
                break;

            case '#/active':
                $('.todo-list li').hide();
                $('.todo-list li:not(.completed)').show();
                break;

            case '#/completed':
                $('.todo-list li').hide();
                $('.todo-list li.completed').show();
                break;
        }

        return false;
    });

    $('.clear-completed').click(function() {
        var url = '/api/clear-completed';
        if($('li.completed').length > 0) {
            api(url, {}, function() {
                $('.todo-list li.completed').remove();
                $('.todo-list').trigger('change');
            });
        }
    });

    $('.new-todo').enterKey(function() {
        var text = $(this).val(),
            url = '/api/create',
            data = {'text': text},
            self = this;

        if(text.length !== 0) {
            api(url, data, function(data) {
                var element = $($('#list_item')[0].innerHTML).clone(true);
                element.attr('data-id', data.data.id);
                element.find('.edit').val(text);
                element.find('label').text(text);
                $('.todo-list').prepend(element).trigger('change');
                $(self).val('');

            });
        }
    });

    $(document).on('dblclick', '.view', function() {
        $(this).hide();
        $(this).next('.edit').show();
    });

    $(document).on('focusout', '.edit', function() {
        $('.edit').hide();
        $('.view').show();
    });

    $(document).on('keypress', '.edit', function(ev) {
        var keycode = (ev.keyCode ? ev.keyCode : ev.which);
        if (keycode == '13') {
            var element = $(this).prev('.view').find('label');
            var oldtext = element.text();
            var newtext = $(this).val();
            $(this).trigger('focusout');

            if (oldtext !== newtext) {
                var url = '/api/change-name',
                    data = {'itemId': $(this).closest('li').data('id'), 'text': newtext};

                api(url, data, function() {
                    element.text(newtext);
                });
            }
        }
    });

    $('body').escKey(function(){
        $('.edit:visible').trigger('focusout');
    });

    $('.toggle-all').click(function() {
        if($(this).prop('checked')) {
            $('.toggle').prop('checked', true).trigger('change');
        } else {
            $('.toggle').prop('checked', false).trigger('change');
        }
    });

});