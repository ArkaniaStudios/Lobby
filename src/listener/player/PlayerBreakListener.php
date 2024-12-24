<?php

declare(strict_types=1);

namespace arkania\listener\player;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;

class PlayerBreakListener implements Listener {

    public function onPlayerBreak(BlockBreakEvent $event): void {
        if (!$event->getPlayer()->isCreative()) {
            $event->cancel();
        }
    }
}