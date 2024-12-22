<?php
declare(strict_types=1);

namespace arkania\items\components;

use arkania\items\interface\ItemComponent;

class CooldownComponent implements ItemComponent {

    private string $category;
    private float $duration;

    public function __construct(string $category, float $duration) {
        $this->category = $category;
        $this->duration = $duration;
    }

    public function getComponentName(): string {
        return "minecraft:cooldown";
    }

    public function getValue(): array {
        return [
            "category" => $this->category,
            "duration" => $this->duration
        ];
    }

    public function isProperty(): bool {
        return false;
    }

}