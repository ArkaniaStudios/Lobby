<?php
declare(strict_types=1);

namespace arkania\items\components;

use arkania\items\interface\ItemComponent;

class FoilComponent implements ItemComponent {

    private bool $foil;

    public function __construct(bool $foil = true) {
        $this->foil = $foil;
    }

    public function getComponentName(): string {
        return "foil";
    }

    public function getValue(): bool {
        return $this->foil;
    }

    public function isProperty(): bool {
        return true;
    }

}