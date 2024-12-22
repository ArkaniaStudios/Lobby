<?php
declare(strict_types=1);

namespace arkania\commands\default;

use arkania\commands\CommandBase;
use arkania\commands\parameters\StringParameter;
use arkania\Main;
use arkania\session\permissions\DefaultsPermissions;
use arkania\session\ranks\Ranks;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;

class AddRankCommand extends CommandBase {

    public function __construct() {
        parent::__construct(
            'addrank',
            'Permet de créer un grade.',
            '/addrank <rank>'
        );
        $this->setPermission(DefaultsPermissions::getPermission('addrank'));
    }

    public function getCommandParameters() : array {
        return [
            new StringParameter('rank')
        ];
    }

    protected function onRun(CommandSender $sender, array $parameters) : void {
        $rank = $parameters['rank'];
        Main::getInstance()->getRanksManager()->getRank($rank)->then(function (?Ranks $ranks) use ($sender, $rank) : void {
            if ($ranks !== null) {
                $sender->sendMessage(Utils::getErrorPrefix() . "Le grade §e$rank §cexiste déjà.");
                return;
            }
            Main::getInstance()->getRanksManager()->addRank($rank)->then(function () use ($sender, $rank) : void {
                $sender->sendMessage(Utils::getPrefix() . "Le grade §e$rank §fa bien été ajouté.");
            });
        });
    }
}