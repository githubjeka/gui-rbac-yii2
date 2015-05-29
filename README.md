Demo https://basic-rbac-githubjeka.c9.io/basic/web/index.php?r=rbac

For  https://github.com/yiisoft/yii2/issues/42 

## Use on local
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

## Concepts (Layout)

### **Force-based layout**
![](http://i.imgur.com/BtWx9Gd.jpg)


### **Directed acyclic graphs**

![](http://i.imgur.com/utTru1W.jpg)

### **My algorithms graphs**

![](https://camo.githubusercontent.com/e1703bc665478a91bb7e09e12c5ae25500c2a9ef/687474703a2f2f692e696d6775722e636f6d2f554e774a546a382e6a7067)

### **Forms layout**
![](https://camo.githubusercontent.com/f1ab4d4f28ca379cfd64d089e46e5206aa2f2d65/687474703a2f2f692e696d6775722e636f6d2f6c6843516442682e6a7067)
