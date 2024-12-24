<?php
declare(strict_types=1);

namespace arkania\commands\default;

use arkania\commands\CommandBase;
use arkania\commands\parameters\StringParameter;
use arkania\Main;
use arkania\session\permissions\DefaultsPermissions;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;

class RemoveInheritanceCommand extends CommandBase {

    public function __construct() {
        parent::__construct(
            'removeinheritance',
            'Permet de retirer une classe parente à une classe enfant',
            '/removeinheritance <classe enfant> <classe parente>'
        );
        $this->setPermission(DefaultsPermissions::getPermission('removeinheritance'));
    }

    public function getCommandParameters() : array {
        return [
            new StringParameter('parent'),
            new StringParameter('child')
        ];
    }

    protected function onRun(CommandSender $sender, array $parameters) : void {
        $parent = $parameters['parent'];
        $child = $parameters['child'];
        Main::getInstance()->getRanksManager()->removeInheritance($child, $parent)->then(function () use ($sender) : void {
            $sender->sendMessage(Utils::getPrefix() . "La classe parente a bien été retirée de la classe enfant.");
        });
    }

}