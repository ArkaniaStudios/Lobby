<?php
declare(strict_types=1);

namespace arkania\commands\default;

use arkania\commands\CommandBase;
use arkania\commands\parameters\PlayerParameter;
use arkania\commands\parameters\StringParameter;
use arkania\Main;
use arkania\session\permissions\DefaultsPermissions;
use arkania\session\ranks\Ranks;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class SetRankCommand extends CommandBase {

    public function __construct() {
        parent::__construct(
            'setrank',
            'Définir le rang d\'un joueur',
            '/setrank <joueur> <rang>'
        );
        $this->setPermission(DefaultsPermissions::getPermission('setrank'));
    }

    public function getCommandParameters() : array {
        return [
            new PlayerParameter('target'),
            new StringParameter('rank')
        ];
    }

    protected function onRun(CommandSender $sender, array $parameters) : void {
        $target = $parameters['target'];
        $rank = $parameters['rank'];

        Main::getInstance()->getRanksManager()->getRank($rank)->then(function (?Ranks $ranks) use ($sender, $target, $rank) : void {
            if ($ranks === null) {
                $sender->sendMessage(Utils::getErrorPrefix() . 'Le rang §e' . $rank . '§c n\'existe pas.');
                return;
            }

            if($target instanceof Player) {
                $targetName = $target->getName();
            }else{
                $targetName = $target;
            }

            Main::getInstance()->getRanksManager()->setRank($targetName, $ranks)->then(function () use ($sender, $target, $ranks) : void {
                if($target instanceof Player) {
                    $target->sendMessage(Utils::getPrefix() . 'Votre rang a été défini sur ' . $ranks->getColor() . $ranks->getName() . '§f.');
                }
                $sender->sendMessage(Utils::getPrefix() . 'Le rang de ' . $ranks->getColor() . $target->getName() . '§f a été défini sur ' . $ranks->getColor() . $ranks->getName() . '§f.');
            });
        });
    }
}