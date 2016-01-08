<?php
namespace githubjeka\rbac\controllers;

use githubjeka\rbac\models\ItemForm;
use Yii;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\rbac\Item;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
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
        $authManager = Yii::$app->authManager;
        $roles = $authManager->getRoles();
        $permissions = $authManager->getPermissions();

        $items = ArrayHelper::merge($roles, $permissions);
        $namesItems = array_keys($items);

        $links = [];

        foreach ($items as $nameItem => $item) {
            $children = $authManager->getChildren($nameItem);

            foreach ($children as $nameChild => $child) {
                $links[] = [
                    'source' => array_search($nameItem, $namesItems),
                    'target' => array_search($nameChild, $namesItems),
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
     * Deletes an existing role or permission.
     * @return boolean
     * @throws BadRequestHttpException
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function actionDelete()
    {
        $postData = Yii::$app->request->post('ItemForm');

        if (!isset($postData['oldName'])) {
            throw new BadRequestHttpException('The POST param ItemForm["oldName"] has missed.');
        }

        $item = $this->findItem($postData['oldName']);

        if ($item === null) {
            throw new NotFoundHttpException('The item(role or permission) not founded.');
        }

        return Yii::$app->getAuthManager()->remove($item);
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

    /**
     * @param $name
     * @return null|\yii\rbac\Permission|\yii\rbac\Role
     * @throws HttpException
     */
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