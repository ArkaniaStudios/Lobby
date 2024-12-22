<?php
declare(strict_types=1);

namespace arkania\items;

use pocketmine\item\Item;
use pocketmine\utils\CloningRegistryTrait;

/**
 * This doc-block is generated automatically, do not modify it manually.
 * This must be regenerated whenever registry members are added, removed or changed.
 * @see build/generate-registry-anotations.php
 * @generate-registry-docblock
 *
 */
class ExtraItems {
    use CloningRegistryTrait;

    protected static function register(string $name, Item $item) : void {
        self::_registryRegister($name, $item);
    }

    /**
     * @return Item[]
     * @phpstan-return array<string, Item>
     */
    public static function getAll() : array {
        /** @var Item[] $result */
        $result = self::_registryGetAll();
        return $result;
    }

    protected static function setup() : void {}

}