<?php
declare(strict_types=1);

namespace arkania\commands\default;

use arkania\commands\CommandBase;
use arkania\gui\class\DoubleChestMenu;
use arkania\gui\InventoryContent;
use arkania\gui\transaction\MenuTransaction;
use arkania\Main;
use arkania\session\permissions\DefaultsPermissions;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
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
        if(!$sender instanceof Player) {
            $sender->sendMessage(Utils::getErrorPrefix() . "Vous devez être un joueur pour exécuter cette commande.");
            return;
        }
        $content = [
            new InventoryContent(13, VanillaItems::COMPASS()->setCustomName('§r§fLobby')->setLore(['§r§7Cliquez pour vous téléporter au lobby'])),
            new InventoryContent(30, VanillaItems::COMPASS()->setCustomName('§r§fFaction')->setLore(['§r§7Cliquez pour vous téléporter au Faction (§eDev§7)'])),
            new InventoryContent(32, VanillaItems::COMPASS()->setCustomName('§r§fMinage')->setLore(['§r§7Cliquez pour vous téléporter au Minage (§eDev§7)'])),
        ];
        $menu = new DoubleChestMenu(
            '§8Carte du voyageur',
            true,
            $content,
            function(Player $player, MenuTransaction $transaction) : void {
                if ($transaction->getSlot() === 13) {
                    if($player->getServer()->getPort() !== 19133) {
                        $player->removeCurrentWindow();
                        $player->sendMessage(Utils::getPrefix() . "Téléportation vers le lobby...");
                        $player->broadcastSound(new DoorSound());
                        Main::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(
                            function () use ($player) : void {
                                $player->transfer('lobby');
                            }), 15);
                    }else{
                        $player->removeCurrentWindow();
                        $player->sendMessage(Utils::getErrorPrefix() . 'Vous êtes déjà connecté au serveur du lobby.');
                    }
                }
                if ($transaction->getSlot() === 30) {
                    if($player->getServer()->getPort() !== 19134) {
                        $player->sendMessage(Utils::getPrefix() . "Téléportation vers le Faction (§eDev§f)...");
                        $player->broadcastSound(new DoorSound());
                        $player->removeCurrentWindow();
                        Main::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(
                            function () use ($player) : void {
                                $player->transfer('factiondev');
                            }), 15);
                    }else{
                        $player->removeCurrentWindow();
                        $player->sendMessage(Utils::getErrorPrefix() . 'Vous êtes déjà connecté au serveur de faction.');
                    }
                }
                if ($transaction->getSlot() === 32) {
                    if($player->getServer()->getPort() !== 19135) {
                        $player->sendMessage(Utils::getPrefix() . "Téléportation vers le Minage (§eDev§f)...");
                        $player->broadcastSound(new DoorSound());
                        $player->removeCurrentWindow();
                        Main::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(
                            function () use ($player) : void {
                                $player->transfer('minagedev');
                            }), 15);
                    }else{
                        $player->removeCurrentWindow();
                        $player->sendMessage(Utils::getErrorPrefix() . 'Vous êtes déjà connecté au serveur de minage.');
                    }
                }
            }
        );

        $menu->send($sender);

    }

}