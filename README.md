[![](https://img.shields.io/badge/see-DEMO-green.svg?style=flat)](https://basic-rbac-githubjeka.c9.io/basic/web/index.php?r=rbac)
[![](https://img.shields.io/badge/to_yii2_issue-42-blue.svg?style=flat)](https://github.com/yiisoft/yii2/issues/42 )

![http://i.imgur.com/BXTKymp.jpg](http://i.imgur.com/BXTKymp.jpg)

## How to install

:baby_chick: Follow the commands: 
- Check that the component `authManager` has been configured.
- Add to your composer.json `"githubjeka/yii2-gui-rbac": "*"`
- Run `composer update`
- If your project doesn't have to implement rbac then run `yii migrate --migrationPath=@yii/rbac/migrations/` 
- Add to `config/web.php `(for basic app) or `common/config/main.php` (for advanced app)
```php
'modules' => [
  'rbac' => [
    'class' => 'githubjeka\rbac\Module',
    'as access' => [ // if you need to set access
      'class' => 'yii\filters\AccessControl',
      'rules' => [
          [
              'allow' => true,
              'roles' => ['@'] // all auth users 
          ],
      ]
    ]
  ],
],
```
- go to url `/index.php?r=rbac`