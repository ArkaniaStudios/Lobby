<?php

declare(strict_types=1);

namespace arkania\items\default\gui;

use arkania\items\components\AllowOffHandComponent;
use arkania\items\components\CanDestroyInCreativeComponent;
use arkania\items\components\HandEquippedComponent;
use arkania\items\components\MaxStackSizeComponent;
use arkania\items\ItemBase;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class LeftArrowItem extends ItemBase {


    public function getComponents() : array {
        return [
            new MaxStackSizeComponent(64),
            new CanDestroyInCreativeComponent(true),
            new AllowOffHandComponent(false),
            new HandEquippedComponent(false)
        ];
    }

    public function onClickAir(Player $player, Vector3 $directionVector, array &$returnedItems) : ItemUseResult {
        return ItemUseResult::NONE();
    }

}