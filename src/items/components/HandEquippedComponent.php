<?php
declare(strict_types=1);

namespace arkania\items\components;

use arkania\items\interface\ItemComponent;

class HandEquippedComponent implements ItemComponent {

    private bool $handEquipped;

    public function __construct(bool $handEquipped = true) {
        $this->handEquipped = $handEquipped;
    }

    public function getComponentName(): string {
        return "hand_equipped";
    }

    public function getValue(): bool {
        return $this->handEquipped;
    }

    public function isProperty(): bool {
        return true;
    }

}