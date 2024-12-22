<?php
declare(strict_types=1);

namespace arkania\items\components;

use arkania\items\interface\ItemComponent;

/**
 * @description Ce component permet de définir le nombre d'item maximum par stack
 */
class MaxStackSizeComponent implements ItemComponent {

    private int $maxStackSize;

    public function __construct(
        int $maxStackSize = 1
    ) {
        if ($maxStackSize > 64)
            $maxStackSize = 64;
        else if ($maxStackSize < 1)
            $maxStackSize = 1;
        $this->maxStackSize = $maxStackSize;
    }

    public function getComponentName(): string {
        return 'max_stack_size';
    }

    public function getValue(): int {
        return $this->maxStackSize;
    }

    public function isProperty(): bool {
        return true;
    }
}