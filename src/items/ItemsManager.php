<?php
declare(strict_types=1);

namespace arkania\items;

use arkania\items\utils\ItemTypeNames;
use Exception;
use pocketmine\data\bedrock\item\SavedItemData;
use pocketmine\inventory\CreativeInventory;
use pocketmine\item\StringToItemParser;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\Tag;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\network\mcpe\protocol\types\ItemComponentPacketEntry;
use pocketmine\network\mcpe\protocol\types\ItemTypeEntry;
use pocketmine\world\format\io\GlobalItemDataHandlers;
use ReflectionClass;
use ReflectionException;

class ItemsManager {

    /** @var (ItemTypeEntry|mixed)[] */
    private array $componentsEntries = [];
    /** @var ItemTypeEntry[] */
    private array $itemsEntries = [];

    /**
     * @throws ReflectionException
     */
    public function __construct() {
        $this->registerDefaultItems();
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    private function registerDefaultItems() : void {
        self::registerCustomItem(ItemTypeNames::ITEM_MONEY, ExtraItems::ITEM_MONEY(), [ItemTypeNames::ITEM_MONEY, "item_money"]);
        self::registerCustomItem(ItemTypeNames::ITEM_LOBBY, ExtraItems::ITEM_LOBBY(), [ItemTypeNames::ITEM_LOBBY, "item_lobby"]);
        self::registerCustomItem(ItemTypeNames::ITEM_FACTION, ExtraItems::ITEM_FACTION(), [ItemTypeNames::ITEM_FACTION, "item_faction"]);
        self::registerCustomItem(ItemTypeNames::ITEM_MINAGE, ExtraItems::ITEM_MINAGE(), [ItemTypeNames::ITEM_MINAGE, "item_minage"]);
        self::registerCustomItem(ItemTypeNames::ITEM_LEFTARROW, ExtraItems::ITEM_LEFTARROW(), [ItemTypeNames::ITEM_LEFTARROW, "item_leftarrow"]);
        self::registerCustomItem(ItemTypeNames::ITEM_RIGHTARROW, ExtraItems::ITEM_RIGHTARROW(), [ItemTypeNames::ITEM_RIGHTARROW, "item_rightarrow"]);
        self::registerCustomItem(ItemTypeNames::ITEM_MAP, ExtraItems::ITEM_MAP(), [ItemTypeNames::ITEM_MAP, "item_map"]);
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function registerCustomItem(string $id, ItemBase $item, array $stringToItemParserNames) : void {
        GlobalItemDataHandlers::getDeserializer()->map($id, fn () => clone $item);
        GlobalItemDataHandlers::getSerializer()->map($item, fn () => new SavedItemData($id));
        foreach ($stringToItemParserNames as $name) {
            StringToItemParser::getInstance()->register($name, fn () => clone $item);
        }

        $this->registerCustomItemMapping($id, $item->getTypeId());
        $this->registerCustomItemPacketsCache($id, $item);
        CreativeInventory::getInstance()->add($item);
    }

    /**
     * @throws ReflectionException
     */
    private function registerCustomItemMapping(string $id, int $itemTypeId) : void {
        $dictionary = TypeConverter::getInstance()->getItemTypeDictionary();
        $reflection = new ReflectionClass($dictionary);
        $properties = [
            ["intToStringIdMap", [$itemTypeId => $id]],
            ["stringToIntMap", [$id => $itemTypeId]]
        ];

        foreach ($properties as $data) {
            $property = $reflection->getProperty($data[0]);
            $property->setValue($dictionary, $property->getValue($dictionary) + $data[1]);
        }
    }

    private function registerCustomItemPacketsCache(string $id, ItemBase $item) : void {
        $this->componentsEntries[] = new ItemComponentPacketEntry($id, new CacheableNbt($item->getCompoundTag()));
        $this->itemsEntries[] = new ItemTypeEntry($id, $item->getTypeId(), true);
    }

    /**
     * @return ItemTypeEntry[]
     */
    public function getItemsEntries(): array {
        return $this->itemsEntries;
    }

    /**
     * @return ItemComponentPacketEntry[]
     */
    public function getComponentsEntries(): array {
        return $this->componentsEntries;
    }

    public static function getTagType($type): ?Tag {
        return match (true) {
            is_array($type) => self::getArrayTag($type),
            is_bool($type) => new ByteTag($type ? 1 : 0),
            is_float($type) => new FloatTag($type),
            is_int($type) => new IntTag($type),
            is_string($type) => new StringTag($type),
            $type instanceof CompoundTag => $type,
            default => null,
        };
    }

    private static function getArrayTag(array $array): Tag {
        if(array_keys($array) === range(0, count($array) - 1)) {
            return new ListTag(array_map(fn($value) => self::getTagType($value), $array));
        }
        $tag = CompoundTag::create();
        foreach($array as $key => $value){
            $tag->setTag($key, self::getTagType($value));
        }
        return $tag;
    }
}