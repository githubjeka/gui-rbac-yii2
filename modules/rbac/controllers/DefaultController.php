<?php
namespace app\modules\rbac\controllers;

use yii\web\Controller;
use Yii;
use app\modules\rbac\models\ItemForm;
use \yii\helpers\Json;
use yii\helpers\Html;
use yii\web\HttpException;

class DefaultController extends Controller
{
    public function actionIndex()
    {
        $this->layout = "main.php";
        return $this->render('index');
    }
    
    public function actionItems()
    {
        $roles = Yii::$app->authManager->getRoles();
        $permissions = Yii::$app->authManager->getPermissions();
        $items = array_merge($roles, $permissions);
        return Json::encode(
            ['nodes'=>array_values($items),'links'=>[]]
        );
    }
    
    /**
     * Action of create or update item. 
     **/
    public function actionSaveItem() {
        
        $item = null;
        
        if (isset($_POST['ItemForm']['oldName'])) {
            $id = $_POST['ItemForm']['oldName'];
            $item = Yii::$app->getAuthManager()->getRole($id);
            if ($item === null) {
                 $item = Yii::$app->getAuthManager()->getPermission($id);
                 if ($item === null) {
                    throw new HttpException(404);
                 }
            }
        }
        
        $model = new ItemForm($item);
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return Json::encode($model);
        }
        
        throw new HttpException(406,  Json::encode($model->getErrors()));
    }
}