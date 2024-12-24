<?php
declare(strict_types=1);

namespace arkania\commands\default;

use arkania\commands\CommandBase;
use arkania\commands\parameters\PlayerParameter;
use arkania\session\permissions\DefaultsPermissions;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\world\Position;
use pocketmine\Server;

class SpawnCommand extends CommandBase {

    public function __construct() {
        parent::__construct(
            'spawn',
            'Téléporte au spawn du serveur.',
            '/spawn [player]'
        );
        $this->setPermission(DefaultsPermissions::getPermission('base'));
    }

    public function getCommandParameters() : array {
        return [
            new PlayerParameter('target', true)
        ];
    }

    protected function onRun(CommandSender $sender, array $parameters) : void {
        $worldName = "Lobby";
        $x = 20;
        $y = 58;
        $z = 64;

        $world = Server::getInstance()->getWorldManager()->getWorldByName($worldName);
        if($world === null) {
            $sender->sendMessage(Utils::getErrorPrefix() . "Le monde §e{$worldName}§c n'est pas chargé.");
            return;
        }

        if(!isset($parameters['target'])) {
            if(!$sender instanceof Player) {
                $sender->sendMessage(Utils::getErrorPrefix() . "Vous devez être un joueur pour exécuter cette commande.");
                return;
            }

            $position = new Position($x, $y, $z, $world);
            $sender->teleport($position);
            $sender->sendMessage(Utils::getPrefix() . "Vous avez été téléporté au spawn.");
        } else {
            if(!$sender->hasPermission(DefaultsPermissions::getPermission('spawn'))) {
                $sender->sendMessage(Utils::getErrorPrefix() . "Vous n'avez pas la permission d'utiliser cette commande.");
                return;
            }

            $target = $parameters['target'];
            if(!$target instanceof Player) {
                $sender->sendMessage(Utils::getErrorPrefix() . "§e" . $parameters['target'] . "§c n'est pas connecté.");
                return;
            }

            $position = new Position($x, $y, $z, $world);
            $target->teleport($position);
            $target->sendMessage(Utils::getPrefix() . "Vous avez été téléporté au spawn.");
            $sender->sendMessage(Utils::getPrefix() . "§e{$target->getName()}§a a été téléporté au spawn.");
        }
    }
}
