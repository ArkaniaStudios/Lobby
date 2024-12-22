<?php
declare(strict_types=1);

namespace arkania\listener\player;

use arkania\Main;
use arkania\session\Session;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerLoginEvent;

class PlayerLoginListener implements Listener {

    public function onPlayerLogin(PlayerLoginEvent $event) : void {
        $player = $event->getPlayer();
        Session::create($player->getNetworkSession());
        $session = Session::get($player);

        $session->load();
        Main::getInstance()->getRanksManager()->register($player);
        $session->getRank()->then(function (string $rank) use ($player) {
            Main::getInstance()->getRanksManager()->getNametagFormat($rank)->then(function (array $format) use ($player) {
                $player->setNameTag(str_replace(['{FAC_NAME}', '{COLOR}', '{LINE}', '{NAME}'], ['Dev', $format['color'], "\n", $player->getName()], $format['format']));
            });
        });
    }

}