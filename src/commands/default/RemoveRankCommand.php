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

class RemoveRankCommand extends CommandBase {

    public function __construct() {
        parent::__construct(
            'removerank',
            'Permet de supprimer un grade.',
            '/removerank <rank>'
        );
        $this->setPermission(DefaultsPermissions::getPermission('removerank'));
    }

    public function getCommandParameters() : array {
        return [
            new StringParameter('rank')
        ];
    }

    protected function onRun(CommandSender $sender, array $parameters) : void {
        $rank = $parameters['rank'];
        if($rank === 'Joueur') {
            $sender->sendMessage(Utils::getErrorPrefix() . 'Vous ne pouvez pas supprimer le grade §eJoueur§c.');
            return;
        }
        Main::getInstance()->getRanksManager()->getRank($rank)->then(function (?Ranks $ranks) use ($sender, $rank) : void {
            if ($ranks === null) {
                $sender->sendMessage(Utils::getErrorPrefix() . 'Le grade §e' . $rank . ' §cn\'existe pas.');
                return;
            }
            Main::getInstance()->getRanksManager()->removeRank($ranks->getName())->then(function () use ($sender, $rank, $ranks) : void {
                $sender->sendMessage(Utils::getPrefix() . 'Le grade ' . $ranks->getColor() . $rank . ' §fa bien été supprimé.');
            });
        });
    }
}