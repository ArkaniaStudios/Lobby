<?php
declare(strict_types=1);

namespace arkania\listener\player;

use arkania\Main;
use arkania\session\Session;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerLoginEvent;
use ReflectionException;

class PlayerLoginListener implements Listener {

    /**
     * @throws ReflectionException
     */
    public function onPlayerLogin(PlayerLoginEvent $event) : void {
        $player = $event->getPlayer();
        Session::create($player->getNetworkSession());
        $session = Session::get($player);

        $session->load();
        Main::getInstance()->getRanksManager()->register($player);
        $session->getRank()->then(function (string $rank) use ($player) {
            Main::getInstance()->getRanksManager()->getNametagFormat($rank)->then(function (array $format) use ($player) {
                Main::getInstance()->getFactionsManager()->getPlayerFaction($player->getName())->then(function (?array $result) use ($player, $format) : void {
                    if ($result === null) {
                        $faction = '...';
                    }else{
                        $faction = $result['faction'];
                    }
                    $player->setNameTag(str_replace(['{FAC_NAME}', '{COLOR}', '{LINE}', '{NAME}'], [$faction, $format['color'], "\n", $player->getName()], $format['format']));
                });
            });
        });
    }

}