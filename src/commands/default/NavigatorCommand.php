<?php
declare(strict_types=1);

namespace arkania\commands\default;

use arkania\commands\CommandBase;
use arkania\gui\class\DoubleChestMenu;
use arkania\gui\InventoryContent;
use arkania\gui\transaction\MenuTransaction;
use arkania\session\permissions\DefaultsPermissions;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\world\sound\DoorSound;


class NavigatorCommand extends CommandBase {

    public function __construct() {
        parent::__construct(
            'navigator',
            'Permet d\'ouvrir la carte.',
            '/navigator'
        );
        $this->setPermission(DefaultsPermissions::getPermission('base'));
    }

    public function getCommandParameters() : array {
        return [];
    }

    protected function onRun(CommandSender $sender, array $parameters) : void {

        $content = [
            new InventoryContent(13, VanillaItems::COMPASS()->setCustomName('§r§fLobby')->setLore(['§r§7Cliquez pour vous téléporter au lobby'])),
            new InventoryContent(30, VanillaItems::COMPASS()->setCustomName('§r§fFaction')->setLore(['§r§7Cliquez pour vous téléporter au Faction (§eDev§7)'])),
            new InventoryContent(32, VanillaItems::COMPASS()->setCustomName('§r§fMinage')->setLore(['§r§7Cliquez pour vous téléporter au Minage (§eDev§7)'])),
        ];

        if(!$sender instanceof Player) {
            $sender->sendMessage(Utils::getErrorPrefix() . "Vous devez être un joueur pour exécuter cette commande.");
            return;
        }
        $menu = new DoubleChestMenu(
            '§8Carte ouverte pour (§9' . $sender->getName() . '§8)',
            true,
            $content,
            function(Player $player, MenuTransaction $transaction) : void {
                if ($transaction->getSlot() === 13) {
                    $player->sendMessage(Utils::getPrefix() . "Téléportation vers le lobby...");
                    $player->broadcastSound(new DoorSound());
                    $player->transfer('lobby');
                }
                if ($transaction->getSlot() === 30) {
                    $player->sendMessage(Utils::getPrefix() . "Téléportation vers le Faction (§eDev§f)...");
                    $player->broadcastSound(new DoorSound());
                    $player->transfer('factiondev');
                }
                if ($transaction->getSlot() === 32) {
                    $player->sendMessage(Utils::getPrefix() . "Téléportation vers le Minage (§eDev§f)...");
                    $player->broadcastSound(new DoorSound());
                    $player->transfer('minagedev');
                }
            }
        );

        $menu->send($sender);

    }
}