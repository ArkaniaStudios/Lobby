<?php
declare(strict_types=1);

namespace arkania\commands\default;

use arkania\commands\CommandBase;
use arkania\Main;
use arkania\session\permissions\DefaultsPermissions;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;

class RanksListCommand extends CommandBase {

    public function __construct() {
        parent::__construct(
            'rankslist',
            'Liste des rangs',
            '/rankslist'
        );
        $this->setPermission(DefaultsPermissions::getPermission('rankslist'));
    }

    public function getCommandParameters() : array {
        return [];
    }

    protected function onRun(CommandSender $sender, array $parameters) : void {
        Main::getInstance()->getRanksManager()->getRanksList()->then(function (array $result) use ($sender) : void {
            $sender->sendMessage(Utils::getPrefix() . 'Voici la liste des rangs :');
            foreach ($result as $infos) {
                $sender->sendMessage($infos['rank']->getColor() . $infos['rank']->getName() . ' §7: ' . $infos['rank']->getColor() . $infos['rank']->getPrefix() . ' §f(' . $infos['rank']->getColor() . $infos['player_count'] . '§f).');
            }
        });
    }

}