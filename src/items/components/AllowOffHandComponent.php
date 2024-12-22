<?php
declare(strict_types=1);

namespace arkania\items\components;

use arkania\items\interface\ItemComponent;

/**
 * @description Ce component permet de définir si l'item peut être tenu dans la main gauche.
 */
class AllowOffHandComponent implements ItemComponent {

    private bool $offHand;

    public function __construct(
        bool $offHand = true
    ) {
        $this->offHand = $offHand;
    }

    public function getComponentName(): string {
        return "allow_off_hand";
    }

    public function getValue(): bool {
        return $this->offHand;
    }

    public function isProperty(): bool {
        return true;
    }
}