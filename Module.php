<?php
namespace githubjeka\rbac;

use yii\base\InvalidConfigException;

/**
 * Graphical user module for Role Based Access Control Yii2 Module.
 * It also allows to perform basic operations RBAC.
 *
 * Using in config:
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
 * @since 2.0
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        if (\Yii::$app->authManager === null) {
            throw new InvalidConfigException('You should configure authManager component');
        }
        parent::init();
    }
}