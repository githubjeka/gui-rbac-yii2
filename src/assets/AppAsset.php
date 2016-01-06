<?php
namespace githubjeka\rbac\assets;

use yii\web\AssetBundle;

/**
 * Class AppAsset represent main AssetBundle for the GUI RBAC module.
 */
class AppAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = __DIR__ . '/files/';

    /**
     * @inheritdoc
     */
    public $css = [
        'css/site.css',
    ];

    /**
     * @inheritdoc
     */
    public $js = [
        "https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.12/d3.min.js",
        "https://cdnjs.cloudflare.com/ajax/libs/d3-tip/0.6.7/d3-tip.min.js",
        "js/app.js",
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
