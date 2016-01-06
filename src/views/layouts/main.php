<?php

use githubjeka\rbac\Module;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */

$mainAssetBundle = Module::getInstance()->mainAssetBundle;
$this->registerAssetBundle($mainAssetBundle);
?>

<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html>

    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php $this->head() ?>
    </head>

    <body>
    <?php $this->beginBody() ?>
    <div class="container-fluid">
        <?= $content ?>
    </div>
    <?php $this->endBody() ?>
    </body>

    </html>
<?php $this->endPage() ?>