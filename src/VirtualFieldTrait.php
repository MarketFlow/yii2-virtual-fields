<?php
declare(strict_types=1);

namespace SamIT\Yii2\VirtualFields;


trait  VirtualFieldTrait
{
    abstract public function addSelect($columns);

    public function withField(string $name)
    {
        $this->addSelect([$name => new VirtualFieldExpression()]);
    }


}