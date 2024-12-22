<?php
declare(strict_types=1);

namespace arkania\items\components;

use arkania\items\interface\ItemComponent;

trait ComponentsTrait {

    /** @var ItemComponent[] */
    private array $components = [];

    public function addComponent(ItemComponent $component) : void {
        $this->components[$component->getComponentName()] = $component;
    }

    public function removeComponent(string $componentName) : void {
        unset($this->components[$componentName]);
    }

    public function hasComponent(string $componentName) : bool {
        return isset($this->components[$componentName]);
    }

}