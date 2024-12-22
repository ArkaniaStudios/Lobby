<?php

declare(strict_types=1);

namespace arkania\commands\default;
use arkania\commands\CommandBase;
use arkania\commands\parameters\SubParameter;
use arkania\Constantes;
use arkania\Main;
use arkania\session\permissions\DefaultsPermissions;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
class RedemCommand extends CommandBase {
    public function __construct() {
        parent::__construct(
            'redem',
            'Permet de redémarrer le serveur.',
            '/redem [force]'
        );
        $this->setPermission(DefaultsPermissions::getPermission('stop'));
    }
    public function getCommandParameters() : array {
        return [
            new SubParameter('force', true)
        ];
    }
    protected function onRun(CommandSender $sender, array $parameters) : void {
        if(Constantes::$onStop) {
            $sender->sendMessage(Utils::getPrefix() . '§cLe serveur est déjà en cours d\'arrêt.');
            return;
        }
        if(!isset($parameters['force'])){
            $time = 30;
        }else{
            $time = 5;
        }
        $task = Main::getInstance()->getScheduler()->scheduleRepeatingTask(
            new ClosureTask(
                function () use ($sender, &$time, &$task) {
                    Constantes::$onStop = true;
                    if($time === 0) {
                        Server::getInstance()->broadcastMessage("§cServer §l§4» §r§cRedémarrage du serveur...");
                        Main::getInstance()->getServer()->shutdown();
                    }
                    if (in_array($time, [30, 20, 10, 5, 4, 3, 2, 1], true)) {
                        Server::getInstance()->broadcastMessage('§cServer §l§4» §r§cRedémarrage du serveur dans §e' . $time . ' seconde(s)§c.');
                    }
                    $time--;
                }
            ), 20
        );
    }
}