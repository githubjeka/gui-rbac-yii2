<?php
namespace githubjeka\rbac\controllers;

use yii\web\Controller;
use Yii;
use githubjeka\rbac\models\ItemForm;
use \yii\helpers\Json;
use yii\helpers\Html;
use yii\web\HttpException;
use yii\helpers\ArrayHelper;

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

        $items = ArrayHelper::merge($roles, $permissions);

        $links = [];

        $_keys = array_keys($items);
        foreach ($items as $np => $oP) {
            foreach ($c = Yii::$app->authManager->getChildren($np) as $nC => $oC) {
                $links[] = [
                    'source' => array_search($np, $_keys),
                    'target' => array_search($nC, $_keys),
                ];
            }
        }

        return Json::encode(
            ['nodes' => array_values($items), 'links' => $links]
        );
    }

    /**
     * Action of create or update item.
     **/
    public function actionSaveItem()
    {

        $item = null;

        if (isset($_POST['ItemForm']['oldName'])) {
            $item = $this->findItem($_POST['ItemForm']['oldName']);
        }

        $model = new ItemForm($item);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return Json::encode($model);
        }

        throw new HttpException(406, Json::encode($model->getErrors()));
    }

    /**
     * Deletes an existing Item.
     * @param  string $id
     * @return mixed
     */
    public function actionDeleteItem()
    {
        if (isset($_POST['ItemForm']['oldName'])) {

            $item = $this->findItem($_POST['ItemForm']['oldName']);

            if ($item !== null) {
                return Yii::$app->getAuthManager()->remove($item);
            }
        }
    }

    /**
     * Remove child item
     */
    public function actionAddChild()
    {
        $post = Yii::$app->getRequest()->post();

        if (isset($post['source']['name'], $post['target']['name'])) {
            $source = $this->findItem($post['source']['name']);
            $target = $this->findItem($post['target']['name']);

            if ($source !== null && $target !== null && !Yii::$app->getAuthManager()->hasChild($source, $target)) {
                Yii::$app->getAuthManager()->addChild($source, $target);
            } else {
                throw new HttpException(406);
            }
        }

    }


    /**
     * Remove child item
     */
    public function actionRemoveChild()
    {
        $post = Yii::$app->getRequest()->post();

        if (isset($post['source']['name'], $post['target']['name'])) {
            $source = $this->findItem($post['source']['name']);
            $target = $this->findItem($post['target']['name']);

            if ($source !== null && $target !== null && Yii::$app->getAuthManager()->hasChild($source, $target)) {
                Yii::$app->getAuthManager()->removeChild($source, $target);
            } else {
                throw new HttpException(406);
            }
        }

    }

    protected function findItem($name)
    {
        $item = null;

        if (!empty($name)) {
            $item = Yii::$app->getAuthManager()->getRole($name);
            if ($item === null) {
                $item = Yii::$app->getAuthManager()->getPermission($name);
                if ($item === null) {
                    throw new HttpException(404);
                }
            }
        }

        return $item;
    }
}