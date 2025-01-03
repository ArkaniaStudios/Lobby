<?php
declare(strict_types=1);

namespace arkania\commands\default;

use arkania\commands\CommandBase;
use arkania\commands\parameters\PlayerParameter;
use arkania\Main;
use arkania\network\servers\ServersStatus;
use arkania\session\permissions\DefaultsPermissions;
use arkania\session\permissions\MissingPermissionException;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class FactionCommand extends CommandBase {

    public function __construct() {
        parent::__construct(
            'faction',
            'Permet d\'aller au serveur faction',
            '/faction [player]'
        );
        $this->setPermission(DefaultsPermissions::getPermission('base'));
    }

    public function getCommandParameters() : array {
        return [
            new PlayerParameter('target', true)
        ];
    }

    protected function onRun(CommandSender $sender, array $parameters) : void {
        if (!in_array($sender->getName(), ['TEZULS', 'Julien8436'])) {
            Main::getInstance()->getServersManager()->getServer('Faction')->then(function (?array $server) use ($sender, $parameters) {
                if ($server === null) {
                    $sender->sendMessage(Utils::getErrorPrefix() . 'Le serveur §eFaction §cest introuvable.');
                    return;
                }
                $status = unserialize($server['status'])['status'];
                if ($status === ServersStatus::OFFLINE) {
                    $sender->sendMessage(Utils::getErrorPrefix() . 'Le serveur §eFaction §cest hors-ligne.');
                    return;
                }
                $this->extracted($parameters, $sender);
            });
        }else{
            $this->extracted($parameters, $sender);
        }
    }
    /**
     * @param array $parameters
     * @param CommandSender $sender
     * @return void
     * @throws MissingPermissionException
     */
    protected function extracted(array $parameters, CommandSender $sender) : void {
        if(!isset($parameters['target'])) {
            if(!$sender instanceof Player) {
                $sender->sendMessage(Utils::getErrorPrefix() . "Vous devez être un joueur pour exécuter cette commande.");
                return;
            }
            $sender->sendMessage(Utils::getPrefix() . "Téléportation vers le §eFaction§f...");
            $sender->transfer('factiondev');
        } else {
            if(!$sender->hasPermission(DefaultsPermissions::getPermission('faction'))) {
                $sender->sendMessage(Utils::getErrorPrefix() . "Vous n'avez pas la permission d'utiliser cette commande.");
                return;
            }
            $target = $parameters['target'];
            if(!$target instanceof Player) {
                $sender->sendMessage(Utils::getErrorPrefix() . "§e" . $parameters['target'] . "§c n'est pas connecté.");
                return;
            }
            $target->sendMessage(Utils::getPrefix() . "Téléportation vers le §eFaction§f...");
            $target->transfer('factiondev');
        }
    }
}