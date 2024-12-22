<?php
declare(strict_types=1);

namespace arkania\items\components;

use arkania\items\interface\ItemComponent;

class FoodComponent implements ItemComponent {

    private bool $canAlwaysEat;
    private int $nutrition;

    public function __construct(int $nutrition, bool $canAlwaysEat = false) {
        $this->nutrition = $nutrition;
        $this->canAlwaysEat = $canAlwaysEat;
    }

    public function getComponentName(): string {
        return "minecraft:food";
    }

    public function getValue(): array {
        return [
            'nutrition' => $this->nutrition,
            "can_always_eat" => $this->canAlwaysEat
        ];
    }

    public function isProperty(): bool {
        return false;
    }

}