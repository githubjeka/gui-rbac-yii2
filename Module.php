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
     * @inheritdoc
     */
    public function init()
    {
        if (\Yii::$app->authManager === null) {
            throw new InvalidConfigException('You should configure the authManager component.');
        }
        parent::init();
    }
}