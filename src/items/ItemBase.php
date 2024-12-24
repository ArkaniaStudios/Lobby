<?php
declare(strict_types=1);

namespace arkania\items;

use arkania\items\components\ComponentsTrait;
use arkania\items\interface\ItemComponent;
use arkania\items\utils\CreativeInventoryInfo;
use Exception;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\nbt\tag\CompoundTag;

abstract class ItemBase extends Item {
    use ComponentsTrait;

    private string $texture;
    private string $creativeInventoryInfo;
    private string $creativeGroup;

    public function __construct(
        ItemIdentifier $identifier,
        string $name,
        string $texture,
        string $creativeInventoryInfo = CreativeInventoryInfo::CATEGORY_EQUIPMENT,
        string $creativeGroup = "",
        array $enchantmentTags = []
    ) {
        parent::__construct($identifier, $name, $enchantmentTags);
        $this->texture = $texture;
        $this->creativeInventoryInfo = $creativeInventoryInfo;
        $this->creativeGroup = $creativeGroup;
        foreach ($this->getComponents() as $component) {
            $this->addComponent($component);
        }
    }

    final public function getCompoundTag() : CompoundTag {
        $components = CompoundTag::create();
        $properties = CompoundTag::create();
        $properties->setInt("creative_category", $this->convertCreativeInfoToInt());
        $properties->setString("creative_group", $this->creativeGroup);
        $properties->setTag(
            "minecraft:icon",
            CompoundTag::create()
                ->setTag("textures", CompoundTag::create()->setString("default", $this->getTexture()))
        );
        foreach($this->components as $component) {
            $tag = ItemsManager::getTagType($component->getValue());
            if ($tag === null) {
                throw new Exception("Failed to get tag type for component " . $component->getComponentName());
            }
            if ($component->isProperty()) {
                $properties->setTag($component->getComponentName(), $tag);
                continue;
            }
            $components->setTag($component->getComponentName(), $tag);
        }
        $components->setTag(
            "item_properties",
            $properties
        );
        return CompoundTag::create()
            ->setTag("components", $components)
            ->setInt("id", $this->getTypeId())
            ->setString("name", $this->getName());
    }

    /**
     * @return ItemComponent[]
     */
    abstract public function getComponents() : array;

    final public function getTexture() : string {
        return $this->texture;
    }

    final public function getCreativeInventoryInfo() : string {
        return $this->creativeInventoryInfo;
    }

    private function convertCreativeInfoToInt() : int {
        return match ($this->getCreativeInventoryInfo()) {
            CreativeInventoryInfo::CATEGORY_ALL => 0,
            CreativeInventoryInfo::CATEGORY_CONSTRUCTION => 1,
            CreativeInventoryInfo::CATEGORY_NATURE => 2,
            CreativeInventoryInfo::CATEGORY_EQUIPMENT => 3,
            CreativeInventoryInfo::CATEGORY_ITEMS => 4,
            CreativeInventoryInfo::CATEGORY_COMMANDS => 5
        };
    }

}