<?php

namespace yiiunit;

use githubjeka\rbac\controllers\ItemController;
use githubjeka\rbac\models\ItemForm;
use Yii;
use yii\helpers\ArrayHelper;
use yii\rbac\Item;
use yii\rbac\Permission;
use yii\rbac\Role;

class ItemControllerTest extends TestCase
{
    public $itemFile = __DIR__ . '/data/rbac/items.php';
    public $assignmentFile = __DIR__ . '/data/rbac/assignments.php';
    public $ruleFile = __DIR__ . '/data/rbac/rules.php';

    /**
     * @var ItemController
     */
    private $itemController;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();

        file_put_contents($this->itemFile, file_get_contents(__DIR__ . '/data/_rbac/items.php'));
        file_put_contents($this->assignmentFile, file_get_contents(__DIR__ . '/data/_rbac/assignments.php'));
        file_put_contents($this->ruleFile, file_get_contents(__DIR__ . '/data/_rbac/rules.php'));

        $this->mockWebApplication(
            [
                'components' => [
                    'authManager' => [
                        'class' => \yii\rbac\PhpManager::className(),
                        'itemFile' => $this->itemFile,
                        'assignmentFile' => $this->assignmentFile,
                        'ruleFile' => $this->ruleFile,
                    ],
                ],
            ]
        );

        $this->itemController = new ItemController('item', Yii::$app);
        $this->itemController->detachBehavior('verbs');
    }

    /**
     * Checks init list roles and permissions
     * @throws \yii\base\InvalidRouteException
     */
    public function testGetStartItems()
    {
        $result = $this->itemController->runAction('list');

        $this->assertArrayHasKey('nodes', $result);
        $this->assertArrayHasKey('links', $result);

        $items = require __DIR__ . '/data/_rbac/items.php';

        $names = array_keys($items);
        $mapInfo = ArrayHelper::map($items, 'type', 'description');
        $types = array_keys($mapInfo);
        $description = array_values($mapInfo);

        foreach ($names as $i => $name) {
            /* @var Role|Permission $itemResult */
            $itemResult = $result['nodes'][$i];

            $this->assertEquals($name, $itemResult->name);
            $this->assertEquals($types[$i], $itemResult->type);
            $this->assertEquals($description[$i], $itemResult->description);
        }
    }

    /**
     * Checks creating of a role
     */
    public function testCreateRole()
    {
        $postData = [
            'name' => 'Test Role',
            'type' => Item::TYPE_ROLE,
            'description' => 'The first new test role',
        ];

        $this->setPostParam($postData);

        /** @var ItemForm $result */
        $result = $this->itemController->runAction('save');
        $this->assertTrue($result instanceof ItemForm);
        $this->assertTrue($result->item instanceof Role);
        $this->assertEquals($postData['name'], $result->item->name);
        $this->assertEquals($postData['type'], $result->item->type);
        $this->assertEquals($postData['description'], $result->item->description);
    }

    /**
     * Checks updating of a role
     */
    public function testUpdateRole()
    {
        $postData = [
            'oldName' => 'Super Administrator',
            'name' => 'Updated Super Administrator',
            'type' => Item::TYPE_ROLE,
            'description' => 'The new description',
        ];

        $this->setPostParam($postData);

        /** @var ItemForm $result */
        $result = $this->itemController->runAction('save');
        $this->assertTrue($result instanceof ItemForm);
        $this->assertTrue($result->item instanceof Role);
        $this->assertEquals($postData['name'], $result->item->name);
        $this->assertEquals($postData['type'], $result->item->type);
        $this->assertEquals($postData['description'], $result->item->description);
    }

    /**
     * Checks creating of a permission
     */
    public function testCreatePermission()
    {
        $postData = [
            'name' => 'Test Permission',
            'type' => Item::TYPE_PERMISSION,
            'description' => 'The first new test permission',
        ];

        $this->setPostParam($postData);

        /** @var ItemForm $result */
        $result = $this->itemController->runAction('save');
        $this->assertTrue($result instanceof ItemForm);
        $this->assertTrue($result->item instanceof Permission);
        $this->assertEquals($postData['name'], $result->item->name);
        $this->assertEquals($postData['type'], $result->item->type);
        $this->assertEquals($postData['description'], $result->item->description);
    }

    /**
     * Checks updating of a permission
     */
    public function testUpdatePermission()
    {
        $postData = [
            'oldName' => 'create user',
            'name' => 'update create user',
            'type' => Item::TYPE_PERMISSION,
            'description' => 'The new description of permission.',
        ];

        $this->setPostParam($postData);

        /** @var ItemForm $result */
        $result = $this->itemController->runAction('save');
        $this->assertTrue($result instanceof ItemForm);
        $this->assertTrue($result->item instanceof Permission);
        $this->assertEquals($postData['name'], $result->item->name);
        $this->assertEquals($postData['type'], $result->item->type);
        $this->assertEquals($postData['description'], $result->item->description);
    }

    /**
     * Checks deleting of a role
     */
    public function testDeleteRole()
    {
        $oldName = 'Super Administrator';
        $this->setPostParam(['oldName' => $oldName,]);

        $authManager = Yii::$app->authManager;

        $this->assertNotNull($authManager->getRole($oldName));
        $this->assertTrue($this->itemController->runAction('delete'));
        $this->assertNull($authManager->getRole($oldName));
    }

    /**
     * Checks deleting of a permission
     */
    public function testDeletePermission()
    {
        $oldName = 'create user';
        $this->setPostParam(['oldName' => $oldName,]);

        $authManager = Yii::$app->authManager;

        $this->assertNotNull($authManager->getPermission($oldName));
        $this->itemController->runAction('delete');
        $this->assertNull($authManager->getPermission($oldName));
    }

    /**
     * Checks removing permission from role
     */
    public function testRemovePermissionFromRoleAndRevert()
    {
        $roleName = 'Super Administrator';
        $permissionName = 'create user';

        $authManager = Yii::$app->getAuthManager();

        Yii::$app->request->setBodyParams(
            [
                'source' => ['name' => $roleName],
                'target' => ['name' => $permissionName],
            ]
        );

        $role = $authManager->getRole($roleName);
        $permission = $authManager->getPermission($permissionName);

        $this->assertTrue($authManager->hasChild($role, $permission));
        $this->assertTrue($this->itemController->runAction('remove-child'));
        $this->assertFalse($authManager->hasChild($role, $permission));
        $this->assertTrue($this->itemController->runAction('add-child'));
        $this->assertTrue($authManager->hasChild($role, $permission));
    }

    /**
     * @param $params
     * @param string $formName
     */
    private function setPostParam($params, $formName = 'ItemForm')
    {
        Yii::$app->request->setBodyParams([$formName => $params]);
    }
}