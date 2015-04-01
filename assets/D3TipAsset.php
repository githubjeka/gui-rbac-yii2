<?php
namespace githubjeka\rbac\assets;

use yii\web\AssetBundle;

class D3TipAsset extends AssetBundle
{
    public $sourcePath = '@bower/d3-tip/';
    public $js = ['index.js',];
    public $depends = [
        'githubjeka\rbac\assets\D3Asset',
    ];
}
