<?php

use githubjeka\rbac\assets\AppAsset;
use yii\helpers\Html;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>

<body>
  <?php $this->beginBody() ?>
  <div class="container-fluid">
     <?= $content ?>
  </div>
  <script src="//cdnjs.cloudflare.com/ajax/libs/d3/3.5.2/d3.js"></script>
  <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>