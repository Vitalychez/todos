<?php

namespace app\assets;

use yii\web\AssetBundle;

class TodoAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/base.css',
        'css/index.css',
        'css/app.css'
    ];
    public $js = [
        'js/base.js',
        'js/app.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
