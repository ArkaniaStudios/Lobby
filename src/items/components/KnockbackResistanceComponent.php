<?php
declare(strict_types=1);

namespace arkania\items\components;

use arkania\items\interface\ItemComponent;

class KnockbackResistanceComponent implements ItemComponent {

    private float $protection;

    public function __construct(float $protection) {
        $this->protection = $protection;
    }

    public function getComponentName(): string {
        return "minecraft:knockback_resistance";
    }

    public function getValue(): array {
        return [
            "protection" => $this->protection
        ];
    }

    public function isProperty(): bool {
        return false;
    }

}