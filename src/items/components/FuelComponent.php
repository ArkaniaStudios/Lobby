<?php
declare(strict_types=1);

namespace arkania\items\components;

use arkania\items\interface\ItemComponent;

class FuelComponent implements ItemComponent {

    private float $duration;

    public function __construct(float $duration) {
        $this->duration = $duration;
    }

    public function getComponentName(): string {
        return "minecraft:fuel";
    }

    public function getValue(): array {
        return [
            "duration" => $this->duration
        ];
    }

    public function isProperty(): bool {
        return false;
    }

}