<?php
declare(strict_types=1);

namespace arkania\commands\default;

use arkania\commands\CommandBase;
use arkania\Main;
use arkania\session\permissions\DefaultsPermissions;
use pocketmine\command\CommandSender;

class StopCommand extends CommandBase {

    public function __construct() {
        parent::__construct(
            'stop',
            'Permet de stopper le serveur',
            '/stop'
        );
        $this->setPermission(DefaultsPermissions::getPermission('stop'));
    }

    public function getCommandParameters() : array {
        return [];
    }

    protected function onRun(CommandSender $sender, array $parameters) : void {
        $sender->sendMessage('§cServer §l§4» §r§cArrêt du serveur...');
        Main::getInstance()->getServer()->shutdown();
    }
}