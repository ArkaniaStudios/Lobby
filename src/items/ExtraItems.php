<?php
declare(strict_types=1);

namespace arkania\items;

use arkania\items\default\LobbyItems;
use arkania\items\default\MoneyItem;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTypeIds;
use pocketmine\utils\CloningRegistryTrait;

/**
 * This doc-block is generated automatically, do not modify it manually.
 * This must be regenerated whenever registry members are added, removed or changed.
 * @see build/generate-registry-anotations.php
 * @generate-registry-docblock
 *
 * @method static MoneyItem ITEM_MONEY()
 * @method static LobbyItems ITEM_LOBBY()
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

    protected static function setup() : void {
        self::register('item_money', new MoneyItem(new ItemIdentifier(ItemTypeIds::newId()), 'billet', 'cash'));
        self::register('ITEM_LOBBY', new LobbyItems(new ItemIdentifier(ItemTypeIds::newId()), 'lobby', 'lobby'));
    }

}