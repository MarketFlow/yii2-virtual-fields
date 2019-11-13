<?php

namespace MarketFlow\Yii2VirtualFields;

class ActiveQuery extends \yii\db\ActiveQuery
{
    /**
     * @param \yii\db\QueryBuilder $builder
     * @return \yii\db\Query
     */
    public function prepare($builder)
    {
        $model = $this->modelClass;
        $fields = method_exists($model, 'virtualFields') ? $model::virtualFields() : [];
        foreach ((array)$this->select as $key => $field) {
            if (is_string($field) && isset($fields[$field]['greedy'])) {
                unset($this->select[$key]);
                $this->select[$field] = $fields[$field]['greedy'];
            }
        }
        return parent::prepare($builder);
    }
}


