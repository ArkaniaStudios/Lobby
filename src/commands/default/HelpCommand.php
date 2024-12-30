<?php
declare(strict_types=1);

namespace arkania\commands\default;

use arkania\commands\CommandBase;
use arkania\session\permissions\DefaultsPermissions;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class HelpCommand extends CommandBase {

    public function __construct() {
        parent::__construct(
            'help',
            'Permet d\'obtenir de l\'aide.',
            '/help'
        );
        $this->setPermission(DefaultsPermissions::getPermission('help'));
    }

    public function getCommandParameters() : array {
        return [];
    }

    protected function onRun(CommandSender $sender, array $parameters) : void {
        if(!$sender instanceof Player) {
            $sender->sendMessage(Utils::getErrorPrefix() . "Vous devez être un joueur pour exécuter cette commande.");
            return;
        }
        $sender->sendMessage(Utils::getPrefix() . "Voici le help askip...");
    }
}