<?php
declare(strict_types=1);

namespace arkania\commands\default;

use arkania\commands\CommandBase;
use arkania\commands\parameters\PlayerParameter;
use arkania\database\result\SqlSelectResult;
use arkania\Main;
use arkania\session\economy\EconomyManager;
use arkania\session\permissions\DefaultsPermissions;
use arkania\session\ranks\Ranks;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class PlayerInfoCommand extends CommandBase {

    public function __construct() {
        parent::__construct(
            'playerinfo',
            'Affiche les informations d\'un joueur',
            '/playerinfo [joueur]'
        );
        $this->setPermission(DefaultsPermissions::getPermission('base'));
    }

    public function getCommandParameters() : array {
        return [
            new PlayerParameter('target', true)
        ];
    }

    protected function onRun(CommandSender $sender, array $parameters) : void {
        if (!isset($parameters['target'])) {
            $target = $sender->getName();
        }elseif($parameters['target'] instanceof Player) {
            $target = $parameters['target']->getName();
        }else{
            $target = $parameters['target'];
        }
        Main::getInstance()->getDatabase()->getConnector()->executeSelect(
            'SELECT * FROM players WHERE name = ?',
            [$target]
        )->then(function (SqlSelectResult $result) use ($sender, $target) : void {
            if($result->count() <= 0) {
                $sender->sendMessage(Utils::getErrorPrefix() . 'Le joueur §e' . $target . ' §cn\'existe pas');
                return;
            }
            $infos = $result->getRows()[0];
            if(($online = Server::getInstance()->getPlayerExact($target)) instanceof Player) {
                $ping = $online->getNetworkSession()->getPing();
                if($ping < 50) {
                    $ping = '§a' . $ping . 'ms';
                }elseif($ping < 100) {
                    $ping = '§e' . $ping . 'ms';
                }elseif($ping < 200) {
                    $ping = '§c' . $ping . 'ms';
                }else{
                    $ping = '§4' . $ping . 'ms';
                }
            }else{
                $ping = null;
            }
            Main::getInstance()->getRanksManager()->getRank($infos['rank'])->then(function (?Ranks $ranks) use ($infos, $sender, $target, $ping) : void {
                Main::getInstance()->getEconomyManager()->getMoney($target)->then(function (?float $money) use ($infos, $target, $sender, $ranks, $ping) : void {
                    Main::getInstance()->getEconomyManager()->getPlayerPositionInClassement($target)->then(function (int $result) use ($infos, $sender, $target, $ping, $ranks, $money) : void {
                        Main::getInstance()->getFactionsManager()->getPlayerFaction($target)->then(function (?array $faction) use ($result, $infos, $sender, $ping, $ranks, $money, $target) : void {
                            $message = Utils::getPrefix() . 'Voici les informations de §e' . $target . ' §r:' . "\n";
                            $message .= "\n§fGrade: §r" . $ranks->getColor() . $ranks->getName();
                            $message .= "\n§fFaction: §r§e" . ($faction !== null ? $faction['name'] : '§cAucune');
                            $message .= "\n§fPièce(s) d'or: §r§e" . EconomyManager::formatNumber($money) . ' §f(§7#' . $result . '§f)';
                            $message .= "\n§fTitre: §r§e" . $infos['title'];
                            $message .= "\n§fDernière connexion: §r§e" . $infos['last_login'];
                            $message .= "\n§fPremière connexion: §r§e" . $infos['first_login'];
                            $status = unserialize($infos['online']);
                            if($status['status'] === '§aEn ligne') {
                                $msg = '§aEn ligne §f(§e' . $status['server'] . '§f)';
                            }else{
                                $msg = '§cHors ligne';
                            }
                            $message .= "\n§fStatus: §r" . $msg;
                            if($ping !== null) {
                                $message .= "\n§fPing: §r" . $ping;
                            }
                            $sender->sendMessage($message);
                        });
                    });
                });
            });
        });
    }
}