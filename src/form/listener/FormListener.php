<?php

declare(strict_types=1);

namespace arkania\form\listener;

use arkania\Main;
use arkania\utils\Utils;
use pocketmine\entity\Attribute;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\scheduler\CancelTaskException;
use pocketmine\scheduler\ClosureTask;

use function json_decode;

final class FormListener implements Listener {
    public function onFormReceive(DataPacketSendEvent $event) : void {
        $packets = $event->getPackets();
        foreach ($packets as $packet) {
            if($packet instanceof ModalFormRequestPacket) {
                $data = json_decode($packet->formData, true);
                if($data === null) {
                    return;
                }
                foreach ($event->getTargets() as $networkSession) {
                    $player = $networkSession->getPlayer();
                    if(!isset($data['permission'])) {
                        if(!$player->hasPermission($data['permission'])) {
                            $player->sendMessage(Utils::getErrorPrefix() . "Vous n'avez pas la permission d'effectuer cette action.");
                            $event->cancel();
                        }
                    }
                    if($player === null || !$player->isConnected()) {
                        continue;
                    }
                    Main::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($player, $networkSession) : void {
                        $times = 5;
                        Main::getInstance()->getScheduler()->scheduleRepeatingTask(new ClosureTask(static function () use ($player, $networkSession, &$times) : void {
                            --$times >= 0 || throw new CancelTaskException("Maximum retries exceeded");
                            $networkSession->isConnected() || throw new CancelTaskException("Maximum retries exceeded");
                            $networkSession->getEntityEventBroadcaster()->syncAttributes([$networkSession], $player, [
                                $player->getAttributeMap()->get(Attribute::EXPERIENCE_LEVEL)
                            ]);
                        }), 10);
                    }), 1);
                }
            }
        }
    }
}