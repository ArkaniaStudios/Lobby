<?php
declare(strict_types=1);

namespace arkania\commands\default;

use arkania\commands\CommandBase;
use arkania\Main;
use arkania\session\permissions\DefaultsPermissions;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use Throwable;

class ListCommand extends CommandBase {

    public function __construct() {
        parent::__construct(
            'list',
            'Permet de voir la liste des joueurs connectés.',
            '/list'
        );
        $this->setPermission(DefaultsPermissions::getPermission('base'));
    }

    public function getCommandParameters() : array {
        return [];
    }

    protected function onRun(CommandSender $sender, array $parameters) : void {
        Main::getInstance()->getRanksManager()->orderPlayerList()->then(function (array $result) use ($sender) : void {
            $connectedPlayers = $result['connectedPlayers'];

            if (empty($connectedPlayers)) {
                $sender->sendMessage(Utils::getErrorPrefix() . "§cAucun joueur connecté n'a été trouvé.");
                return;
            }

            $message = Utils::getPrefix() .  "Liste des joueurs connectés (§e" . count(Server::getInstance()->getOnlinePlayers()) . '§f/§e' . Server::getInstance()->getMaxPlayers() . '§f) :' . "\n";
            foreach ($connectedPlayers as $player) {
                $message .= $player['color'].$player['rank'] . ' §f- ' . $player['color'] . $player['name'] .'§7, §r';
            }
            $sender->sendMessage($message);
        });
    }
}