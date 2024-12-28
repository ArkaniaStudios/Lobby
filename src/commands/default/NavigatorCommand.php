<?php
declare(strict_types=1);

namespace arkania\commands\default;

use arkania\commands\CommandBase;
use arkania\gui\class\DoubleChestMenu;
use arkania\gui\InventoryContent;
use arkania\gui\transaction\MenuTransaction;
use arkania\gui\transaction\MenuTransactionResult;
use arkania\Main;
use arkania\network\servers\ServersStatus;
use arkania\session\permissions\DefaultsPermissions;
use arkania\session\Session;
use arkania\utils\Date;
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
        Main::getInstance()->getServersManager()->getServers()->then(function (array $servers) use ($sender) {
            foreach ($servers as $server) {
                if($server['name'] === 'Lobby') {
                    $lobby = $server;
                }elseif($server['name'] === 'Faction') {
                    $faction = $server;
                }elseif($server['name'] === 'Minage') {
                    $minage = $server;
                }else{
                    $sender->sendMessage(Utils::getErrorPrefix() . "Erreur lors de la récupération des serveurs.");
                    $sender->removeCurrentWindow();
                    return;
                }
            }
            if(!isset($lobby)) {
                $lobby = [
                    'status' => serialize(['status' => ServersStatus::OFFLINE, 'time' => '?']),
                    'players' => 0
                ];
            }
            if(!isset($faction)) {
                $faction = [
                    'status' => serialize(['status' => ServersStatus::OFFLINE, 'time' => '?']),
                    'players' => 0
                ];
            }
            if(!isset($minage)) {
                $minage = [
                    'status' => serialize(['status' => ServersStatus::OFFLINE, 'time' => '?']),
                    'players' => 0
                ];
            }
            $content = [
                new InventoryContent(13, VanillaItems::COMPASS()->setCustomName('§r§c§l»§r§f Lobby§c§l «')->setLore(
                    [
                        '§r§e' . $lobby['players'] . ' §7joueur(s)',
                        '§r' . $this->getColorStatus(unserialize($lobby['status'])['status'])
                    ]
                )),
                new InventoryContent(30, VanillaItems::COMPASS()->setCustomName('§r§c§l»§r§f Faction§c§l «')->setLore(
                    [
                        '§r§e' . $faction['players'] . ' §7joueur(s)',
                        '§r' . $this->getColorStatus(unserialize($faction['status'])['status'])
                    ]
                )),
                new InventoryContent(32, VanillaItems::COMPASS()->setCustomName('§r§c§l»§r§f Minage§c§l «')->setLore(
                    [
                        '§r§e' . $minage['players'] . ' §7joueur(s)',
                        '§r' . $this->getColorStatus(unserialize($minage['status'])['status'])
                    ]
                )),
            ];
            $menu = new DoubleChestMenu(
                '§8Carte du voyageur',
                true,
                $content,
                function(Player $player, MenuTransaction $transaction) use ($lobby, $faction, $minage) : MenuTransactionResult {
                    if ($transaction->getSlot() === 13) {
                        if($player->getServer()->getPort() !== 19133) {
                            $player->removeCurrentWindow();
                            $lobbyStatus = unserialize($lobby['status'])['status'];
                            if($lobbyStatus === ServersStatus::ONLINE) {
                                $player->sendMessage(Utils::getPrefix() . "Téléportation vers le lobby...");
                                Session::get($player)->sendSound('portal.travel');
                                Main::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(
                                    function () use ($player) : void {
                                        $player->transfer('lobby');
                                    }), 15);
                            }elseif($lobbyStatus === ServersStatus::MAINTENANCE){
                                if(!$player->hasPermission(DefaultsPermissions::getPermission('maintenance'))) {
                                    $player->sendMessage(Utils::getErrorPrefix() . 'Le serveur du §eLobby §cest actuellement en maintenance.');
                                }
                            }else{
                                $player->sendMessage(Utils::getErrorPrefix() . 'Le serveur du §eLobby §cest actuellement hors ligne.');
                                $player->removeCurrentWindow();
                                return $transaction->discard();
                            }
                        }else{
                            $player->removeCurrentWindow();
                            $player->sendMessage(Utils::getErrorPrefix() . 'Vous êtes déjà connecté au serveur du lobby.');
                        }
                    }
                    if ($transaction->getSlot() === 30) {
                        if($player->getServer()->getPort() !== 19134) {
                            $factionStatus = unserialize($faction['status'])['status'];
                            if($factionStatus === ServersStatus::ONLINE) {
                                $player->sendMessage(Utils::getPrefix() . "Téléportation vers le serveur de faction...");
                                Session::get($player)->sendSound('portal.travel');
                                $player->removeCurrentWindow();
                                Main::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(
                                    function () use ($player) : void {
                                        $player->transfer('faction');
                                    }), 15);
                            }elseif($factionStatus === ServersStatus::MAINTENANCE){
                                if(!$player->hasPermission(DefaultsPermissions::getPermission('maintenance'))) {
                                    $player->sendMessage(Utils::getErrorPrefix() . 'Le serveur de §eFaction §cest actuellement en maintenance.');
                                }
                            }else{
                                $player->sendMessage(Utils::getErrorPrefix() . 'Le serveur de §eFaction §cest actuellement hors ligne.');
                                $player->removeCurrentWindow();
                                return $transaction->discard();
                            }
                            $player->removeCurrentWindow();
                            $player->sendMessage(Utils::getErrorPrefix() . 'Vous êtes déjà connecté au serveur de faction.');
                        }
                    }
                    if ($transaction->getSlot() === 32) {
                        if($player->getServer()->getPort() !== 19135) {
                            $minageStatus = unserialize($minage['status'])['status'];
                            if($minageStatus === ServersStatus::ONLINE) {
                                $player->sendMessage(Utils::getPrefix() . "Téléportation vers le Minage...");
                                Session::get($player)->sendSound('portal.travel');
                                $player->removeCurrentWindow();
                                Main::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(
                                    function () use ($player) : void {
                                        $player->transfer('minage');
                                    }), 15);
                            }elseif($minageStatus === ServersStatus::MAINTENANCE){
                                if(!$player->hasPermission(DefaultsPermissions::getPermission('maintenance'))) {
                                    $player->sendMessage(Utils::getErrorPrefix() . 'Le serveur de §eMinage §cest actuellement en maintenance.');
                                }
                            }else {
                                $player->sendMessage(Utils::getErrorPrefix() . 'Le serveur de §eMinage §cest actuellement hors ligne.');
                                $player->removeCurrentWindow();
                                return $transaction->discard();
                            }
                            $player->removeCurrentWindow();
                            $player->sendMessage(Utils::getErrorPrefix() . 'Vous êtes déjà connecté au serveur de minage.');
                        }
                    }
                    return $transaction->discard();
                }
            );

            $menu->send($sender);
        });
    }

    protected function getColorStatus(string $status) : string {
        if($status === ServersStatus::ONLINE) {
            return '§aEn ligne';
        }elseif($status === ServersStatus::MAINTENANCE) {
            return '§6Maintenance';
        }else{
            return '§cHors ligne';
        }
    }

}