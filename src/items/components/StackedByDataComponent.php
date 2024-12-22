<?php
declare(strict_types=1);

namespace arkania\items\components;

use arkania\items\interface\ItemComponent;

class StackedByDataComponent implements ItemComponent {

    private bool $stackedByData;

    public function __construct(
        bool $stackedByData
    ) {
        $this->stackedByData = $stackedByData;
    }

    public function getComponentName() : string {
        return "minecraft:stacked_by_data";
    }

    public function getValue() : bool {
        return $this->stackedByData;
    }

    public function isProperty() : bool {
        return true;
    }

}