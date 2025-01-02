<?php

declare(strict_types=1);

namespace arkania\listener\player;

use arkania\Main;
use arkania\session\Session;
use arkania\utils\Utils;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;

class PlayerQuitListener implements Listener {

    public function onPlayerQuit(PlayerQuitEvent $event): void {
        $player = $event->getPlayer();
        $session = Session::get($player);

        Main::getInstance()->getServersManager()->removePlayer(Utils::getName());

        $event->setQuitMessage('');
        $player->sendPopup('§c- ' . $player->getName() . ' §c-');
        $session->save();

    }

}