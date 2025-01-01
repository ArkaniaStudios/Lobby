<?php
declare(strict_types=1);

namespace arkania\listener\player;

use arkania\factions\FactionDefaultsRanks;
use arkania\Main;
use arkania\session\Session;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\Server;

class PlayerChatListener implements Listener {

    public function onPlayerChat(PlayerChatEvent $event) : void {
        $player = $event->getPlayer();
        $session = Session::get($player);

        $event->cancel();
        $session->getRank()->then(function (string $name) use ($event, $player) : void {
            Main::getInstance()->getRanksManager()->getChatFormat($name)->then(function (array $format) use ($event, $player) : void {
                Main::getInstance()->getFactionsManager()->getPlayerFaction($player->getName())->then(function (?array $result) use ($event, $player, $format) : void {
                    if($result === null) {
                        $faction = '...';
                    }else{
                        $faction = $result['faction'];
                    }
                    if($result === null) {
                        $factionRank = '';
                    }else{
                        $factionRank = $result['faction_rank'];
                        if($factionRank === FactionDefaultsRanks::OWNER) {
                            $factionRank = FactionDefaultsRanks::OWNER_SYMBOL;
                        }elseif($factionRank === FactionDefaultsRanks::OFFICER) {
                            $factionRank = FactionDefaultsRanks::OFFICER_SYMBOL;
                        }else{
                            $factionRank = FactionDefaultsRanks::MEMBER_SYMBOL;
                        }
                    }
                    Server::getInstance()->broadcastMessage(str_replace(['{FAC_RANK}', '{FAC_NAME}', '{COLOR}', '{NAME}', '{MESSAGE}'], [$factionRank, $faction, $format['color'], $player->getName(), $event->getMessage()], $format['format']));
                });
            });
        });

    }

}