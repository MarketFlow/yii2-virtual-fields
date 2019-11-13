<?php
declare(strict_types=1);

namespace SamIT\Yii2\VirtualFields;


use yii\base\Behavior;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\db\ActiveQueryInterface;

/**
 * Class VirtualFieldBehavior
 * Attach this to activequery.
 * @package SamIT\Yii2\VirtualFields
 * @property \yii\db\ActiveQuery $owner
 */
class VirtualFieldQueryBehavior extends Behavior
{
    /**
     * @param \yii\db\ActiveQuery $owner
     */
    public function attach($owner)
    {
        parent::attach($owner);
        if (!$owner instanceof \yii\db\ActiveQuery) {
            throw new InvalidConfigException('Owner must be an instance of ' . \yii\db\ActiveQuery::class);
        }
    }

    public function withField(string $name)
    {
        /** @var \yii\db\ActiveRecord $model */
        $model = $this->owner->modelClass::instance();
        if (empty($this->owner->select)) {
            $this->owner->addSelect('*');
        }
        $this->owner->addSelect([$name => $model->getVirtualExpression($name)]);
    }

}