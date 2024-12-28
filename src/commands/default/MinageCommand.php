<?php
declare(strict_types=1);

namespace arkania\commands\default;

use arkania\commands\CommandBase;
use arkania\commands\parameters\PlayerParameter;
use arkania\session\permissions\DefaultsPermissions;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class MinageCommand extends CommandBase {

    public function __construct() {
        parent::__construct(
            'minage',
            'Permet de rejoindre le serveur minage.',
            '/minage [player]'
        );
        $this->setPermission(DefaultsPermissions::getPermission('base'));
    }

    public function getCommandParameters() : array {
        return [
            new PlayerParameter('target', true)
        ];
    }

    protected function onRun(CommandSender $sender, array $parameters) : void {
        if(!isset($parameters['target'])) {
            if(!$sender instanceof Player) {
                $sender->sendMessage(Utils::getErrorPrefix() . "Vous devez être un joueur pour exécuter cette commande.");
                return;
            }
            $sender->sendMessage(Utils::getPrefix() . "Téléportation vers le minange...");
            $sender->transfer('minagedev');
        }else{
            if(!$sender->hasPermission(DefaultsPermissions::getPermission('minage'))) {
                $sender->sendMessage(Utils::getErrorPrefix() . "Vous n'avez pas la permission d'utiliser cette commande.");
                return;
            }
            $target = $parameters['target'];
            if(!$target instanceof Player) {
                $sender->sendMessage(Utils::getErrorPrefix() . "§e" . $parameters['target'] . "§c n'est pas connecté.");
                return;
            }
            $target->sendMessage(Utils::getPrefix() . "Téléportation vers le minage...");
            $target->transfer('minagedev');
        }
    }
}