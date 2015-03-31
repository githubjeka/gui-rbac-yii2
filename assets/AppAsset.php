<?php
namespace githubjeka\rbac\assets;

use yii\web\AssetBundle;

class AppAsset extends AssetBundle
{
    public $sourcePath = '@vendor/githubjeka/rbac/web/';
    public $css = [
        'css/site.css',
    ];
    public $js = [
        "js/app.js"
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
