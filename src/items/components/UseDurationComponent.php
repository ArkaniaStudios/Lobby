<?php
declare(strict_types=1);

namespace arkania\items\components;

use arkania\items\interface\ItemComponent;

class UseDurationComponent implements ItemComponent {

    private int $duration;

    public function __construct(int $duration) {
        $this->duration = $duration;
    }

    public function getComponentName(): string {
        return "use_duration";
    }

    public function getValue(): int {
        return $this->duration;
    }

    public function isProperty(): bool {
        return true;
    }

}