<?php

declare(strict_types=1);

namespace arkania\listener\player;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDropItemEvent;

class PlayerInventoryListener implements Listener {

    public function onPlayerDrop(PlayerDropItemEvent $event): void {
        $event->cancel();
    }
}