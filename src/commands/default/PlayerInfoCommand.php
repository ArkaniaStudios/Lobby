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

class PlayerInfoCommand extends CommandBase {

    public function __construct() {
        parent::__construct(
            'playerinfo',
            'Affiche les informations d\'un joueur',
            '/playerinfo [joueur]',
            aliases: ['pi'],
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
            Main::getInstance()->getRanksManager()->getRank($infos['rank'])->then(function (?Ranks $ranks) use ($infos, $sender, $target) : void {
                Main::getInstance()->getDatabase()->getConnector()->executeSelect(
                    "SELECT money FROM economy WHERE player = ?",
                    [$target]
                )->then(function (SqlSelectResult $result) use ($ranks, $infos, $target, $sender) : void {
                    $message = Utils::getPrefix() . 'Voici les informations de ' . $ranks->getColor() . $target . ' §r:' . "\n";
                    $message .= "\n§7- §fGrade: §r" . $ranks->getColor() . $ranks->getName();
                    $message .= "\n§7- §fPièce(s) d'or: §r" . $ranks->getColor() . self::formatNumber($result->getRows()[0]['money']);
                    $message .= "\n§7- §fTitre: §r" . $ranks->getColor() . $infos['title'];
                    $message .= "\n§7- §fDernière connexion: §r" . $ranks->getColor() . $infos['last_login'];
                    $message .= "\n§7- §fPremière connexion: §r" . $ranks->getColor() . $infos['first_login'];
                    $status = unserialize($infos['online']);
                    if($status['status'] === '§aEn ligne') {
                        $msg = '§aEn ligne §f(' . $ranks->getColor() . $status['server'] . '§f)';
                    }else{
                        $msg = '§cHors ligne';
                    }
                    $message .= "\n§7- §fStatus: §r" . $msg;
                    $sender->sendMessage($message);
                });
            });
        });
    }

    public static function formatNumber(float $number) : string {
        if ($number >= 1000000000) {
            return number_format($number / 1000000000, 2) . 'B';
        } elseif ($number >= 1000000) {
            return number_format($number / 1000000, 2) . 'M';
        } elseif ($number >= 1000) {
            return number_format($number / 1000, 2) . 'K';
        } else {
            return (string)$number;
        }
    }
}