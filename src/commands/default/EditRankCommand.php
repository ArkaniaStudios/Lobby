<?php
declare(strict_types=1);

namespace arkania\commands\default;

use arkania\commands\CommandBase;
use arkania\commands\parameters\StringParameter;
use arkania\form\class\CustomForm;
use arkania\form\elements\customs\Input;
use arkania\Main;
use arkania\session\permissions\DefaultsPermissions;
use arkania\session\ranks\Ranks;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class EditRankCommand extends CommandBase {

    public function __construct() {
        parent::__construct(
            'editrank',
            'Editer un rang',
            '/editrank <rank>'
        );
        $this->setPermission(DefaultsPermissions::getPermission('editrank'));
    }

    public function getCommandParameters() : array {
        return [
            new StringParameter('rank')
        ];
    }

    protected function onRun(CommandSender $sender, array $parameters) : void {
        if(!$sender instanceof Player) {
            $sender->sendMessage(Utils::getErrorPrefix() . 'Cette commande ne peut être utilisée que par un joueur');
            return;
        }

        $rank = $parameters['rank'];
        Main::getInstance()->getRanksManager()->getRank($rank)->then(function (?Ranks $ranks) use ($rank, $sender) : void {
            if($ranks === null) {
                $sender->sendMessage(Utils::getErrorPrefix() . 'Le rang §e' . $rank . ' §cn\'existe pas');
                return;
            }

            $form = new CustomForm(
                "Edition du rang " . $ranks->getColor() . $rank,
                [
                    new Input('position', '§l' . $ranks->getColor() . '» §rPosition :', (string)$ranks->getPosition(), (string)$ranks->getPosition()),
                    new Input('format', '§l' . $ranks->getColor() . '» §rFormat :', $ranks->getFormat(), $ranks->getFormat()),
                    new Input('nametag', '§l' . $ranks->getColor() . '» §rNametag :', $ranks->getNametag(), $ranks->getNametag()),
                    new Input('prefix', '§l' . $ranks->getColor() . '» §rPrefix :', $ranks->getPrefix(), $ranks->getPrefix()),
                    new Input('color', '§l' . $ranks->getColor() . '» §rCouleur :', $ranks->getColor(), $ranks->getColor())
                ],
                function (Player $player, array $data) use ($ranks) : void {
                    var_dump($data['position']);
                    $ranks->setPosition((int)$data['position']);
                    $ranks->setFormat($data['format']);
                    $ranks->setNametag($data['nametag']);
                    $ranks->setPrefix($data['prefix']);
                    $ranks->setColor($data['color']);
                    Main::getInstance()->getRanksManager()->updateRank($ranks);
                    $player->sendMessage(Utils::getPrefix() . 'Le rang ' . $ranks->getColor() . $ranks->getName() . ' §fa été modifié.');
                }
            );
            $sender->sendForm($form);
        });

    }

}