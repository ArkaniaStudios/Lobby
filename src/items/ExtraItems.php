<?php
declare(strict_types=1);

namespace arkania\items;

use arkania\items\default\gui\BackItem;
use arkania\items\default\gui\EmptyShoppingCartItem;
use arkania\items\default\gui\FactionItem;
use arkania\items\default\gui\LeftArrowItem;
use arkania\items\default\gui\LobbyItem;
use arkania\items\default\gui\MapItem;
use arkania\items\default\gui\MinageItem;
use arkania\items\default\gui\RightArrowItem;
use arkania\items\default\gui\ShoppingCartItem;
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
 * @method static LobbyItem ITEM_LOBBY()
 * @method static FactionItem ITEM_FACTION()
 * @method static MinageItem ITEM_MINAGE()
 * @method static LeftArrowItem ITEM_LEFTARROW()
 * @method static RightArrowItem ITEM_RIGHTARROW()
 * @method static MapItem ITEM_MAP()
 * @method static ShoppingCartItem ITEM_SHOPPINGCART()
 * @method static EmptyShoppingCartItem ITEM_EMPTYSHOPPINGCART()
 * @method static BackItem ITEM_BACK()
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
        self::register('ITEM_LOBBY', new LobbyItem(new ItemIdentifier(ItemTypeIds::newId()), 'lobby', 'lobby'));
        self::register('ITEM_FACTION', new FactionItem(new ItemIdentifier(ItemTypeIds::newId()), 'faction', 'faction'));
        self::register('ITEM_MINAGE', new MinageItem(new ItemIdentifier(ItemTypeIds::newId()), 'minage', 'minage'));
        self::register('item_leftarrow', new LeftArrowItem(new ItemIdentifier(ItemTypeIds::newId()), 'leftarrow', 'leftarrow'));
        self::register('item_rightarrow', new RightArrowItem(new ItemIdentifier(ItemTypeIds::newId()), 'rightarrow', 'rightarrow'));
        self::register('item_map', new MapItem(new ItemIdentifier(ItemTypeIds::newId()), 'map', 'map_item'));
        self::register('item_shoppingcart', new ShoppingCartItem(new ItemIdentifier(ItemTypeIds::newId()), 'shoppingcart', 'shoppingcart'));
        self::register('item_emptyshoppingcart', new EmptyShoppingCartItem(new ItemIdentifier(ItemTypeIds::newId()), 'emptyshoppingcart', 'emptyshoppingcart'));
        self::register('item_back', new BackItem(new ItemIdentifier(ItemTypeIds::newId()), 'back', 'back'));

    }

}