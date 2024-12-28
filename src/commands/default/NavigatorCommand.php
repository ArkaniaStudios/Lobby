<?php
declare(strict_types=1);

namespace arkania\commands\default;

use arkania\commands\CommandBase;
use arkania\gui\class\DoubleChestMenu;
use arkania\gui\InventoryContent;
use arkania\gui\transaction\MenuTransaction;
use arkania\gui\transaction\MenuTransactionResult;
use arkania\Main;
use arkania\session\permissions\DefaultsPermissions;
use arkania\session\Session;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;

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
            new InventoryContent(13, VanillaItems::COMPASS()->setCustomName('§r§c§l»§r§f Lobby§c§l «')->setLore(
                [
                    '§r§e §7joueur(s)',
                    '§r§e(idk pour le status)'
                ]
            )),
            new InventoryContent(30, VanillaItems::COMPASS()->setCustomName('§r§c§l»§r§f Faction§c§l «')->setLore(
                [
                    '§r§e0 §7joueur(s)',
                    '§r§e(idk pour le status)'
                ]
            )),
            new InventoryContent(32, VanillaItems::COMPASS()->setCustomName('§r§c§l»§r§f Minage§c§l «')->setLore(
                [
                    '§r§e 0 §7joueur(s)',
                    '§r§e(idk pour le status)'
                ]
            )),
        ];
        $menu = new DoubleChestMenu(
            '§8Carte du voyageur',
            true,
            $content,
            function(Player $player, MenuTransaction $transaction) : MenuTransactionResult {
                if ($transaction->getSlot() === 13) {
                    if($player->getServer()->getPort() !== 19133) {
                        $player->removeCurrentWindow();
                        $player->sendMessage(Utils::getPrefix() . "Téléportation vers le lobby...");
                        Session::get($player)->sendSound('portal.travel');
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
                        Session::get($player)->sendSound('portal.travel');
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
                        Session::get($player)->sendSound('portal.travel');
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
                return $transaction->discard();
            }
        );

        $menu->send($sender);

    }

}