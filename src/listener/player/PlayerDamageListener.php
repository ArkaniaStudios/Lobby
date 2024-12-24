<?php

declare(strict_types=1);

namespace arkania\listener\player;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\player\Player;

class PlayerDamageListener implements Listener {

    public function onVoid(PlayerMoveEvent $event): void {
        $player = $event->getPlayer();
        $pos = $player->getPosition();
        if ($pos->getY() < 0) {
            $player->teleport($player->getWorld()->getSpawnLocation());
        }
    }

    private function cancelDamage(EntityDamageEvent $event): void {
        $entity = $event->getEntity();
        if ($entity instanceof Player) {
            $event->cancel();
        }
    }

    public function onEntityDamage(EntityDamageEvent $event): void {
        $this->cancelDamage($event);
    }

}