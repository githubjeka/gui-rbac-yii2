<?php
namespace githubjeka\rbac\controllers;

use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\web\Controller;
use Yii;
use githubjeka\rbac\models\ItemForm;
use yii\web\HttpException;
use yii\helpers\ArrayHelper;
use yii\web\Response;

/**
 * Class ItemController
 * Represent class for work with CRUD operations by Item
 */
class ItemController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'contentNegotiator' => [
                'class' => ContentNegotiator::className(),
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'save' => ['post'],
                    'delete' => ['post'],
                ],
            ],

        ];
    }

    /**
     * Returns an array of nodes(roles and permissions) and links by them
     * @return array
     */
    public function actionList()
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

        return ['nodes' => array_values($items), 'links' => $links];
    }

    /**
     * Action of create or update a item(role or permission).
     **/
    public function actionSave()
    {
        $item = null;

        if (isset($_POST['ItemForm']['oldName'])) {
            $item = $this->findItem($_POST['ItemForm']['oldName']);
        }

        $model = new ItemForm($item);

        if (!$model->load(Yii::$app->request->post())) {
            Yii::$app->response->setStatusCode(406);
            return ['errors' => ['Wrong Post datum']];
        }

        if (!$model->load(Yii::$app->request->post()) || !$model->save()) {
            Yii::$app->response->setStatusCode(406);
            return ['errors' => $model->getErrors()];
        }

        return $model;
    }

    /**
     * Deletes an existing Item.
     * @return mixed
     */
    public function actionDelete()
    {
        $postData = Yii::$app->request->post('ItemForm');

        if (isset($postData['oldName'])) {

            $item = $this->findItem($postData['oldName']);

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