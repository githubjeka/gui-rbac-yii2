<?php
namespace githubjeka\rbac;

use yii\base\InvalidConfigException;

/**
 * Graphical user interface (GUI) module for Role Based Access Control (RBAC) Yii2 Module.
 * It also allows to perform basic operations RBAC.
 *
 * Using in the your web config:
 * ~~~
 * ```php
 *   'modules' => [
 *       'rbac' => [
 *           'class' => 'githubjeka\rbac\Module',
 *               'as access' => [
 *                   'class' => 'yii\filters\AccessControl',
 *                   'rules' => [['allow' => true,'roles' => ['@']],
 *              ]
 *           ]
 *       ],
 *   ],
 * ~~~
 *
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class Module extends \yii\base\Module
{
    /**
     * The main assetBundle for GUI. This asset bundle will be load in main layout.
     * By default AppAsset uses content delivery network (cdnjs.com) for scripts that used in GUI.
     * If you can't use cdn then you should configure own Asset Bundle and set it via this attribute.
     * @see \githubjeka\rbac\assets\AppAsset to determine the minimum versions of libraries.
     * @var string
     * @since 2.0.0
     */
    public $mainAssetBundle = 'githubjeka\rbac\assets\AppAsset';

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function init()
    {
        if (\Yii::$app->authManager === null) {
            throw new InvalidConfigException('You forgot configure the "authManager" component.');
        }
        parent::init();
    }
}