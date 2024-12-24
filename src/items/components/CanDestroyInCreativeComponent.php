<?php
declare(strict_types=1);

namespace arkania\items\components;

use arkania\items\interface\ItemComponent;

/**
 * @description Ce component permet de définir si l'item peut être détruit dans le mode créatif
 */
class CanDestroyInCreativeComponent implements ItemComponent {

    private bool $canDestroyInCreative;

    public function __construct(
        bool $canDestroyInCreative = true
    ) {
        $this->canDestroyInCreative = $canDestroyInCreative;
    }

    public function getComponentName(): string {
        return "can_destroy_in_creative";
    }

    public function getValue(): bool {
        return $this->canDestroyInCreative;
    }

    public function isProperty(): bool {
        return true;
    }

}