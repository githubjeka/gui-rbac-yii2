<?php
namespace githubjeka\rbac\models;

use Yii;
use yii\rbac\Item;
use yii\helpers\Json;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "tbl_auth_item".
 *
 * @property string $name
 * @property integer $type
 * @property string $description
 * @property string $ruleName
 * @property string $data
 *
 * @property Item $item
 */
class ItemForm extends \yii\base\Model
{
    public $oldName;
    public $name;
    public $type;
    public $description;
    public $ruleName;
    public $data;
    /**
     * @var Item
     */
    private $_item;


    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * Initialize object
     * @param Item $item
     * @param array $config
     */
    public function __construct($item, $config = [])
    {
        $this->_item = $item;

        if ($item !== null) {
            $this->name = $this->_item->name;
            $this->type = $this->_item->type;
            $this->description = $this->_item->description;
            $this->ruleName = $this->_item->ruleName;
            $this->data = $this->_item->data === null ? null : Json::encode($this->_item->data);
        }

        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                ['ruleName'],
                'in',
                'range' => array_keys(Yii::$app->authManager->getRules()),
                'message' => 'Rule not exists'
            ],
            [['name', 'type'], 'required'],
            [
                ['name'],
                'uniqueName',
                'when' => function () {
                    return $this->isNewRecord;
                }
            ],
            [
                ['oldName'],
                'uniqueName',
                'when' => function () {
                    return !$this->isNewRecord && $this->name != $this->oldName;
                }
            ],
            [['type'], 'integer'],
            [['description', 'data', 'ruleName'], 'default'],
            [['name'], 'string', 'max' => 64]
        ];
    }

    public function uniqueName()
    {
        $authManager = Yii::$app->authManager;
        $value = $this->name;
        if ($authManager->getRole($value) !== null || $authManager->getPermission($value) !== null) {
            $message = Yii::t('yii', '{attribute} "{value}" has already been taken.');
            $params = [
                'attribute' => $this->getAttributeLabel('name'),
                'value' => $value,
            ];
            $this->addError('name', Yii::$app->getI18n()->format($message, $params, Yii::$app->language));
        }
    }

    /**
     * Check if is new record.
     * @return boolean
     */
    public function getIsNewRecord()
    {
        return $this->_item === null;
    }

    /**
     * Find role
     * @param string $id
     * @return null|\self
     */
    public static function find($id)
    {
        $item = Yii::$app->authManager->getRole($id);
        if ($item !== null) {
            return new self($item);
        }
        return null;
    }

    /**
     * Save role to [[\yii\rbac\authManager]]
     * @return boolean
     */
    public function save()
    {
        if ($this->validate()) {
            $manager = Yii::$app->authManager;
            if ($this->_item === null) {
                if ($this->type == Item::TYPE_ROLE) {
                    $this->_item = $manager->createRole($this->name);
                } else {
                    $this->_item = $manager->createPermission($this->name);
                }
                $isNew = true;
            } else {
                $isNew = false;
            }
            $this->_item->name = $this->name;
            $this->_item->description = $this->description;
            $this->_item->type = $this->type;
            $this->_item->ruleName = $this->ruleName;
            $this->_item->data = $this->data === null || $this->data === '' ? null : Json::decode($this->data);
            if ($isNew) {
                $manager->add($this->_item);
            } else {
                $manager->update($this->oldName, $this->_item);
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get item
     * @return Item
     */
    public function getItem()
    {
        return $this->_item;
    }

    /**
     * Get type name
     * @param  mixed $type
     * @return string|array
     */
    public static function getTypeName($type = null)
    {
        $result = [
            Item::TYPE_PERMISSION => 'Permission',
            Item::TYPE_ROLE => 'Role'
        ];
        if ($type === null) {
            return $result;
        }
        return $result[$type];
    }
}