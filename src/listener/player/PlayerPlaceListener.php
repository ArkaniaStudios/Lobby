<?php

declare(strict_types=1);

namespace arkania\listener\player;

use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;

class PlayerPlaceListener implements Listener {

    public function onPlayerPlace(BlockPlaceEvent $event): void {
        if (!$event->getPlayer()->isCreative()) {
            $event->cancel();
        }
    }
}