<?php
declare(strict_types=1);

namespace arkania\listener\player;

use arkania\Main;
use arkania\session\Session;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\player\chat\StandardChatFormatter;
use pocketmine\Server;

class PlayerChatListener implements Listener {

    public function onPlayerChat(PlayerChatEvent $event) : void {
        $player = $event->getPlayer();
        $session = Session::get($player);

        $event->cancel();
        $session->getRank()->then(function (string $name) use ($event, $player) : void {
            Main::getInstance()->getRanksManager()->getChatFormat($name)->then(function (array $format) use ($event, $player) : void {
                Server::getInstance()->broadcastMessage(str_replace(['{FAC_RANK}', '{FAC_NAME}', '{COLOR}', '{NAME}', '{MESSAGE}'], ['**', 'Dev', $format['color'], $player->getName(), $event->getMessage()], $format['format']));
            });
        });

    }

}