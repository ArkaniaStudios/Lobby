<?php
declare(strict_types=1);

namespace arkania\items\default;

use arkania\items\components\AllowOffHandComponent;
use arkania\items\components\CanDestroyInCreativeComponent;
use arkania\items\components\HandEquippedComponent;
use arkania\items\components\MaxStackSizeComponent;
use arkania\items\ItemBase;

class MoneyItem extends ItemBase {

    private int $amount;

    public function getComponents() : array {
        return [
            new MaxStackSizeComponent(64),
            new CanDestroyInCreativeComponent(true),
            new AllowOffHandComponent(false),
            new HandEquippedComponent(false)
        ];
    }


}