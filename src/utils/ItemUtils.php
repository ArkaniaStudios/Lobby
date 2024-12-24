<?php
declare(strict_types=1);

namespace arkania\utils;

use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\Durable;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\LegacyStringToItemParser;
use pocketmine\item\StringToItemParser;
use pocketmine\utils\TextFormat;

class ItemUtils {
    /**
     * @param array $itemData
     * @return Item
     */
    public static function dataToItem(array $itemData): Item {
        if(is_int($itemData["id"])) {
            $item = (clone LegacyStringToItemParser::getInstance()->parse($itemData["id"].":".($itemData["damage"] ?? 0)))->setCount($itemData["count"] ?? 1);
        } else {
            $item = (clone StringToItemParser::getInstance()->parse($itemData["id"]))->setCount($itemData["count"] ?? 1);
        }
        if(isset($itemData["enchants"])) {
            foreach($itemData["enchants"] as $ename => $level) {
                $ench = EnchantmentIdMap::getInstance()->fromId((int)$ename);
                if(is_null($ench)) {
                    continue;
                }
                $item->addEnchantment(new EnchantmentInstance($ench, $level));
            }
        }
        if(isset($itemData["display_name"])) {
            $item->setCustomName(TextFormat::colorize($itemData["display_name"]));
        }
        if(isset($itemData["damage"]) && $item instanceof Durable) {
            $item->setDamage(intval($itemData["damage"]));
        }
        if(isset($itemData["lore"])) {
            $lore = [];
            foreach($itemData["lore"] as $key => $ilore) {
                $lore[$key] = TextFormat::colorize($ilore);
            }
            $item->setLore($lore);
        }
        return $item;
    }

    /**
     * @param Item $item
     * @return array
     */
    public static function itemToData(Item $item): array {
        $serialized = StringToItemParser::getInstance();
        $itemData["id"] = $serialized->lookupAliases($item)[0];
        $itemData["count"] = $item->getCount();
        if($item->hasCustomName()) {
            $itemData["display_name"] = $item->getCustomName();
        }
        if($item->getLore() !== []) {
            $itemData["lore"] = $item->getLore();
        }
        if($item instanceof Durable) {
            $itemData["damage"] = $item->getDamage();
        }
        if($item->hasEnchantments()) {
            foreach($item->getEnchantments() as $enchantment) {
                $itemData["enchants"][(string)EnchantmentIdMap::getInstance()->toId($enchantment->getType())] = $enchantment->getLevel();
            }
        }
        return $itemData;
    }

}