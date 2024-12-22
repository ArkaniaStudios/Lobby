<?php
declare(strict_types=1);

namespace arkania\items\components;

use arkania\items\interface\ItemComponent;

class ArmorComponent implements ItemComponent {

    public const TEXTURE_TYPE_CHAIN = "chain";
    public const TEXTURE_TYPE_DIAMOND = "diamond";
    public const TEXTURE_TYPE_ELYTRA = "elytra";
    public const TEXTURE_TYPE_GOLD = "gold";
    public const TEXTURE_TYPE_IRON = "iron";
    public const TEXTURE_TYPE_LEATHER = "leather";
    public const TEXTURE_TYPE_NETHERITE = "netherite";
    public const TEXTURE_TYPE_NONE = "none";
    public const TEXTURE_TYPE_TURTLE = "turtle";

    private int $protection;
    private string $textureType;

    public function __construct(int $protection, string $textureType = self::TEXTURE_TYPE_NONE) {
        $this->protection = $protection;
        $this->textureType = $textureType;
    }

    public function getValue(): array {
        return [
            "protection" => $this->protection,
            "texture_type" => $this->textureType
        ];
    }

    public function isProperty(): bool {
        return false;
    }

    public function getComponentName() : string {
        return "minecraft:armor";
    }

}