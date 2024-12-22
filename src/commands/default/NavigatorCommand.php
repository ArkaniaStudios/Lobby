<?php
declare(strict_types=1);

namespace arkania\commands\default;

use arkania\commands\CommandBase;
use arkania\gui\class\DoubleChestMenu;
use arkania\Main;
use arkania\session\permissions\DefaultsPermissions;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\Server;
use Throwable;

class NavigatorCommand extends CommandBase {

    public function __construct() {
        parent::__construct(
            'navigator',
            'Permet d\'ouvrir la carte.',
            '/navigator'
        );
        $this->setPermission(DefaultsPermissions::getPermission('base'));
    }

    public function getCommandParameters() : array {
        return [];
    }

    protected function onRun(CommandSender $sender, array $parameters) : void {

        $content = [];

        if(!$sender instanceof Player) {
            $sender->sendMessage(Utils::getErrorPrefix() . "Vous devez être un joueur pour exécuter cette commande.");
            return;
        }
        $menu = new DoubleChestMenu(
            '§8Carte ouverte pour (§9' . $sender->getName() . '§8)',
            true,
            $content
        );

        $menu->send($sender);
    }
}