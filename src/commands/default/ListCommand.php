<?php
declare(strict_types=1);

namespace arkania\commands\default;

use arkania\commands\CommandBase;
use arkania\Main;
use arkania\session\permissions\DefaultsPermissions;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\Server;

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
            $playerNames = array_column($connectedPlayers, 'rank');
            $uniquePrefixes = $this->getUniquePrefixes($playerNames);

            $message = Utils::getPrefix() .  "Liste des joueurs connectés (§e" . count(Server::getInstance()->getOnlinePlayers()) . '§f/§e' . Server::getInstance()->getMaxPlayers() . '§f) :' . "\n";
            foreach ($connectedPlayers as $player) {
                $prefix = $uniquePrefixes[$player['rank']];
                $message .= '§f[' . $player['color'] . $prefix . '§f] ' . $player['name'] . '§7, §r';
            }
            $sender->sendMessage($message);
        });
    }

    protected function getUniquePrefixes(array $words): array {
        $prefixes = [];
        $uniquePrefixes = [];

        foreach ($words as $word) {
            $prefix = mb_substr($word, 0, 1);
            $index = 1;

            while (in_array($prefix, $uniquePrefixes)) {
                $index++;
                $prefix = mb_substr($word, 0, $index);
            }

            $uniquePrefixes[] = $prefix;
            $prefixes[$word] = $prefix;
        }

        return $prefixes;
    }

}