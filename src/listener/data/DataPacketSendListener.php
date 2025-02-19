<?php
declare(strict_types=1);

namespace arkania\listener\data;

use arkania\Main;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\protocol\BiomeDefinitionListPacket;
use pocketmine\network\mcpe\protocol\ItemRegistryPacket;
use pocketmine\network\mcpe\protocol\ResourcePackStackPacket;
use pocketmine\network\mcpe\protocol\StartGamePacket;
use pocketmine\network\mcpe\protocol\types\BlockPaletteEntry;
use pocketmine\network\mcpe\protocol\types\Experiments;
use pocketmine\network\mcpe\protocol\types\ItemTypeEntry;
use ReflectionClass;

class DataPacketSendListener implements Listener {

    /** @var ItemTypeEntry[] */
    private array $cachedItemTable = [];
    /** @var BlockPaletteEntry[] */
    private array $cachedBlockPalette = [];
    private Experiments $experiments;

    public function __construct() {
        $this->experiments = new Experiments(["data_driven_items" => true,], true);
    }

    public function onSendDataPacket(DataPacketSendEvent $event) : void {
        $customItemManager = Main::getInstance()->getItemsManager();
        foreach ($event->getPackets() as $packet) {
            if($packet instanceof StartGamePacket) {
                if(count($this->cachedItemTable) === 0) {
                    $this->cachedItemTable = $customItemManager->getItemsEntries();
                }
                $packet->levelSettings->experiments = $this->experiments;
                $packet->blockPalette = $this->cachedBlockPalette;
            } elseif($packet instanceof ResourcePackStackPacket) {
                $packet->experiments = $this->experiments;
            } elseif($packet instanceof ItemRegistryPacket){
                $entries = (new ReflectionClass($packet))->getProperty("entries");
                $value = $entries->getValue($packet);
                $entries->setValue($packet, array_merge($value, $customItemManager->getItemsEntries()));
            }
        }
    }

}