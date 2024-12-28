<?php
declare(strict_types=1);

namespace arkania\listener\data;

use arkania\Main;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\protocol\BiomeDefinitionListPacket;
use pocketmine\network\mcpe\protocol\ItemComponentPacket;
use pocketmine\network\mcpe\protocol\ResourcePackStackPacket;
use pocketmine\network\mcpe\protocol\StartGamePacket;
use pocketmine\network\mcpe\protocol\types\Experiments;

class DataPacketSendListener implements Listener {

    public function onSendDataPacket(DataPacketSendEvent $event) : void {
        $customItemManager = Main::getInstance()->getItemsManager();
        foreach ($event->getPackets() as $packet) {
            if ($packet instanceof BiomeDefinitionListPacket) {
                $sessions = $event->getTargets();
                foreach ($sessions as $session){
                    $session->getPlayer()->getNetworkSession()->sendDataPacket(ItemComponentPacket::create($customItemManager->getComponentsEntries()));
                }
            }elseif ($packet instanceof StartGamePacket) {
                $packet->itemTable = array_merge($packet->itemTable, $customItemManager->getItemsEntries());
            } elseif($packet instanceof ResourcePackStackPacket) {
                $packet->experiments = new Experiments([
                    "data_driven_items" => true
                ], true);
            }
        }
    }

}