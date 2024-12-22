<?php

declare(strict_types=1);

namespace arkania\listener\player;

use arkania\session\Session;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;

class PlayerQuitListener implements Listener {

    public function onPlayerQuit(PlayerQuitEvent $event): void {
        $player = $event->getPlayer();
        $session = Session::get($player);

        $event->setQuitMessage('');
        $player->sendPopup('[§c-§f] ' . $player->getName());
        $session->save();

    }

}