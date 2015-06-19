<?php
namespace githubjeka\rbac;

use yii\helpers\Url;

class Module extends \yii\base\Module
{    
    protected $guiActions = [];

    public $defaultRoute = 'default/index';


    public function init()
    {
        $this->setActionsUrls();
        parent::init();
    }
    
    protected function setActionsUrls()
    {
        $this->guiActions['items'] = Url::to(["/{$this->id}/default/items"]);
        $this->guiActions['saveItem'] = Url::to(["/$this->id/default/save-item"]);
        $this->guiActions['deleteItem'] = Url::to(["/$this->id/default/delete-item"]);
        $this->guiActions['addChild'] = Url::to(["/$this->id/default/add-child"]);
        $this->guiActions['removeChild'] = Url::to(["/$this->id/default/remove-child"]);
    }
    
    public function getActionsUrls()
    {
        return $this->guiActions;
    }
}