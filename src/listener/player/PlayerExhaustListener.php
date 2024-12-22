<?php

declare(strict_types=1);

namespace arkania\listener\player;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerExhaustEvent;

class PlayerExhaustListener implements Listener {

    public function onExhaust(PlayerExhaustEvent $event): void{
        $event->cancel();
    }
}