[![Total Downloads](https://poser.pugx.org/githubjeka/yii2-gui-rbac/downloads)](https://packagist.org/packages/githubjeka/yii2-gui-rbac)
[![](https://img.shields.io/badge/see-DEMO-green.svg?style=flat)](https://basic-rbac-githubjeka.c9.io/basic/web/index.php?r=rbac)
[![](https://img.shields.io/badge/to_yii2_issue-42-blue.svg?style=flat)](https://github.com/yiisoft/yii2/issues/42 )
[![Build Status](https://travis-ci.org/githubjeka/gui-rbac-yii2.svg)](https://travis-ci.org/githubjeka/gui-rbac-yii2)

![http://i.imgur.com/BXTKymp.jpg](http://i.imgur.com/BXTKymp.jpg)

## How to install

:baby_chick: Follow the commands: 
- Check that the component `authManager` has been configured.
- Add to your composer.json `"githubjeka/yii2-gui-rbac": "1.0.2"`
- Run `composer update`
- If your project doesn't have to implement rbac then run `yii migrate --migrationPath=@yii/rbac/migrations/` 
- Add to `@app/config/main.php` the code:
```php
// '/config/web.php' for Basic or '/backend/config/main' - Advanced Yii2 application.
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
