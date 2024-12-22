<?php
declare(strict_types=1);

namespace arkania\items\interface;

interface ItemComponent {

    public function getComponentName() : string;

    public function getValue() : mixed;

    public function isProperty() : bool;

}