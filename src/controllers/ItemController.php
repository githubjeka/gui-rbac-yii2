<?php
namespace githubjeka\rbac\controllers;

use githubjeka\rbac\models\ItemForm;
use Yii;
use yii\base\InvalidParamException;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\rbac\Permission;
use yii\rbac\Role;
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
                    'add-child' => ['post'],
                    'remove-child' => ['post'],
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

        if (isset($_POST['ItemForm']['oldName']) && $_POST['ItemForm']['oldName'] !== '') {
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
        return Yii::$app->getAuthManager()->remove($item);
    }

    /**
     * Adds a child item to a parent item.
     * @return boolean
     * @throws BadRequestHttpException
     */
    public function actionAddChild()
    {
        list($source, $target) = $this->getSourceAndTarget();

        try {
            return Yii::$app->getAuthManager()->addChild($source, $target);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }

    /**
     * Removes a child item from a parent item.
     * @return boolean
     */
    public function actionRemoveChild()
    {
        list($source, $target) = $this->getSourceAndTarget();
        return Yii::$app->getAuthManager()->removeChild($source, $target);
    }

    /**
     * Returns source and target as a Role or a Permission.
     * The helper method for actionAddChild & actionRemoveChild.
     * @return array of source and target.
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    protected function getSourceAndTarget()
    {
        $postData = Yii::$app->getRequest()->post();

        if (!isset($postData['source']['name'], $postData['target']['name'])) {
            throw new BadRequestHttpException('The POST "source" and "target" params has missed.');
        }

        return [$this->findItem($postData['source']['name']), $this->findItem($postData['target']['name'])];
    }

    /**
     * Returns a role or a permission by name.
     * @param $name
     * @return Permission|Role
     * @throws HttpException
     */
    protected function findItem($name)
    {
        $authManager = Yii::$app->getAuthManager();

        $role = $authManager->getRole($name);
        if ($role !== null) {
            return $role;
        }

        $permission = $authManager->getPermission($name);
        if ($permission !== null) {
            return $permission;
        }

        throw new NotFoundHttpException('The item(role or permission) not founded.');
    }
}