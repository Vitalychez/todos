<?php

use app\assets\TodoAsset;
use yii\helpers\Html;

TodoAsset::register($this);

$this->title = 'Список задач';

?>

<section class="todoapp">
    <header class="header">
        <h1>todos</h1>
        <input class="new-todo" placeholder="What needs to be done?" autofocus>
    </header>
    <!-- This section should be hidden by default and shown when there are todos -->
    <section class="main">
        <input id="toggle-all" class="toggle-all" type="checkbox">
        <label for="toggle-all">Mark all as complete</label>
        <ul class="todo-list">
            <?php foreach($data as $row): ?>
                <?php
                    $options = ['data-id' => $row->id];
                    if($row->status) {
                        Html::addCssClass($options, 'completed');
                    }
                    $checkbox = Html::checkbox('', $row->status, ['class' => 'toggle']);
                    $html = <<<HTML
                        <div class="view">
                            {$checkbox}
                            <label>{$row->text}</label>
                            <button class="destroy"></button>
                        </div>
                        <input class="edit" value="{$row->text}">
HTML;

                    echo Html::tag('li', $html, $options)
                ?>

            <?php endforeach; ?>
        </ul>
    </section>
    <!-- This footer should hidden by default and shown when there are todos -->
    <footer class="footer">
        <!-- This should be `0 items left` by default -->
        <span class="todo-count"><strong><?= count($data) ?></strong> item left</span>
        <!-- Remove this if you don't implement routing -->
        <ul class="filters">
            <li>
                <a class="selected" href="#/">All</a>
            </li>
            <li>
                <a href="#/active">Active</a>
            </li>
            <li>
                <a href="#/completed">Completed</a>
            </li>
        </ul>
        <!-- Hidden if no completed items are left ↓ -->
        <button class="clear-completed">Clear completed</button>
    </footer>
</section>

<footer class="info">
    <p><a href="/login/logout">Выход</a></p>
</footer>

<script type="text/html" id="list_item">
    <li data-id="">
        <div class="view">
            <input class="toggle" type="checkbox">
            <label></label>
            <button class="destroy"></button>
        </div>
        <input class="edit" value="">
    </li>
</script>
