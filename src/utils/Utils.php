<?php
declare(strict_types=1);

namespace arkania\utils;

use arkania\Main;
use pocketmine\block\Block;
use pocketmine\block\tile\Nameable;
use pocketmine\block\tile\Tile;
use pocketmine\block\tile\TileFactory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\BlockActorDataPacket;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\player\Player;

final class Utils {

    public static function getPrefix() : string {
        return Main::getInstance()->getConfig()->get('prefix');
    }

    public static function getErrorPrefix() : string {
        return "§cErreur §4§l» §r§c";
    }

    public static function getName() : string {
        return Main::getInstance()->getConfig()->get('name', 'Lobby');
    }

    public static function sendFakeBlock(Player $player, Block $blocks, int $positionX, int $positionY, int $positionZ, ?string $customName = null, ?string $class = null): void {
        $position = $player->getPosition();
        $position->x += $positionX;
        $position->y += $positionY;
        $position->z += $positionZ;
        $blockPosition = BlockPosition::fromVector3($position);
        $player->getNetworkSession()->sendDataPacket(UpdateBlockPacket::create(
            $blockPosition,
            TypeConverter::getInstance()->getBlockTranslator()->internalIdToNetworkId($blocks->getStateId()),
            UpdateBlockPacket::FLAG_NETWORK,
            UpdateBlockPacket::DATA_LAYER_NORMAL
        ));
        if ( ! is_null($customName) && ! is_null($class)) {
            $player->getNetworkSession()->sendDataPacket(
                BlockActorDataPacket::create(
                    $blockPosition,
                    new CacheableNbt(
                        CompoundTag::create()
                            ->setString(Tile::TAG_ID, TileFactory::getInstance()->getSaveId($class))
                            ->setString(Nameable::TAG_CUSTOM_NAME, $customName)
                    )
                )
            );
        }
    }

    public static function getFullVersion() : array {
        return [
            'version' => '1.0.0-beta',
            'date' => 'Lundi 2 Décembre 2024'
        ];
    }
}