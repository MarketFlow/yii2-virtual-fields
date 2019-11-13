<?php
namespace MarketFlow\Yii2VirtualFields;
use yii\base\Exception;

class ActiveRecord extends \yii\db\ActiveRecord
{
    // Cache for the virtual fields, same as $_attributes but then for virtual fields.
    private $_virtualFields = [];

    /**
     * Allow setting of the greedy virtual fields so populate records will attempt to set them.
     * @param string $name
     * @param bool $checkVars
     * @param bool $checkBehaviors
     * @return bool
     */
    public function canSetProperty($name, $checkVars = true, $checkBehaviors = true)
    {
        return isset(static::virtualFields()[$name]['greedy']) ||
            parent::canSetProperty($name, $checkVars, $checkBehaviors);
    }

    /**
     * If we are setting a virtual field store it in our cache.
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        if(isset(static::virtualFields()[$name]['greedy'])) {
            $this->_virtualFields[$name] = $value;
        } else {
            parent::__set($name, $value);
        }
    }

    /**
     * Check the virtual fields cache first, after that check lazy virtual fields.
     * @param string $name
     * @return mixed
     * @throws \Exception
     */
    public function __get($name)
    {
        if(array_key_exists($name, $this->_virtualFields)) {
            $result = $this->_virtualFields[$name];
        } elseif (([] != $virtualFields = static::virtualFields()) && isset($virtualFields[$name])) {
            if(!isset($virtualFields[$name]['lazy'])) {
                throw new \Exception('Virtual field ' . get_class($this) . '::' . $name . ' not implemented for lazy loading');
            }
            $result = $virtualFields[$name]['lazy']($this);
        } else {
            $result = parent::__get($name);
        }
        return $result;
    }

    /**
     * Use this function to define virtual fields.
     * The key is the name of the field, the value is an array with keys greedy and optionally lazy.
     * If you want to implement lazy but not greedy, use a normal getter instead.
     * The lazy value must be a Closure.
     * The greedy value must be a Query (or ActiveQuery) object.
     * @return array The virtual fields for this model.
     */
    public static function virtualFields()
    {
        return [];
    }


    /**
     * @return ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public static function find()
    {
        return \Yii::createObject(ActiveQuery::className(), [get_called_class()]);
    }
}