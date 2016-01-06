<?php
namespace githubjeka\rbac\controllers;

use yii\web\Controller;

class DefaultController extends Controller
{
    public function actionIndex()
    {
        $this->layout = "main.php";
        return $this->render('index');
    }
}