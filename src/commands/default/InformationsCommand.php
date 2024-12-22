<?php

declare(strict_types=1);

namespace arkania\commands\default;
use arkania\commands\CommandBase;
use arkania\session\permissions\DefaultsPermissions;
use arkania\utils\Date;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\Server;
use pocketmine\VersionInfo;

class InformationsCommand extends CommandBase {
    public function __construct() {
        parent::__construct(
            'informations',
            'Affiche les informations du serveur',
            '/informations',
            aliases: ['info'],
        );
        $this->setPermission(DefaultsPermissions::getPermission('base'));
    }
    public function getCommandParameters() : array {
        return [];
    }
    protected function onRun(CommandSender $sender, array $parameters) : void {
        $onlinePlayer = count(Server::getInstance()->getOnlinePlayers());
        $maxPlayer = Server::getInstance()->getMaxPlayers();
        if($onlinePlayer / $maxPlayer >= 0.75) {
            $format = '§c' . $onlinePlayer . '§f/§c' . $maxPlayer;
        }elseif($onlinePlayer / $maxPlayer >= 0.50) {
            $format = '§6' . $onlinePlayer . '§f/§6' . $maxPlayer;
        }elseif($onlinePlayer / $maxPlayer >= 0.25) {
            $format = '§e' . $onlinePlayer . '§f/§e' . $maxPlayer;
        }else{
            $format = '§a' . $onlinePlayer . '§f/§a' . $maxPlayer;
        }
        $tps = Server::getInstance()->getTicksPerSecond();
        if($tps >= 15) {
            $tps = '§a' . $tps;
        }elseif($tps >= 10) {
            $tps = '§6' . $tps;
        }elseif($tps >= 5) {
            $tps = '§c' . $tps;
        }else{
            $tps = '§4' . $tps;
        }
        $sender->sendMessage(Utils::getPrefix() . 'Voici les informations du serveur ' . Utils::getName() . '§f:');
        $sender->sendMessage('Version: §9' . Utils::getFullVersion()['version']);
        $sender->sendMessage('Minecraft Protocol: §9' . ProtocolInfo::MINECRAFT_VERSION);
        $sender->sendMessage('PocketMine-MP API: §9' . VersionInfo::BASE_VERSION);
        $sender->sendMessage('PHP Version: §9' . PHP_VERSION);
        $sender->sendMessage(' ');
        $sender->sendMessage('Joueurs en ligne: §9' . $format);
        $sender->sendMessage('OS: §9' . \pocketmine\utils\Utils::getOS());
        $sender->sendMessage('TPS: §9' . $tps);
        $sender->sendMessage('En ligne depuis: §9' . Date::create((int)Server::getInstance()->getStartTime())->__toString());
    }
}