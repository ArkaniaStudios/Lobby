<?php
declare(strict_types=1);

namespace arkania\listener\player;

use arkania\Main;
use arkania\session\Session;
use arkania\utils\Utils;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\item\VanillaItems;
use pocketmine\Server;

class PlayerJoinListener implements Listener {

    public function onPlayerJoin(PlayerJoinEvent $event) : void {
        $player = $event->getPlayer();
        $session = Session::get($player);
        Main::getInstance()->getServersManager()->addPlayer(Utils::getName());

        /*Proxy*/
        if($player->getNetworkSession()->getIp() !== "172.18.0.1" && $player->getNetworkSession()->getIp() !== "127.0.0.1"){
            $player->kick("  §cVous avez été kick du serveur car vous n'êtes pas passé par le lobby !\n§cSi ceci est une erreur merci de nous contacter (§ediscord.arkaniastudios.com§c)");
        }

        /*Messages*/

        if (!$player->hasPlayedBefore()) {
            $player->sendMessage("\n§cArkaniaStudios §f(§7Lobby§f)\n\n§7§l»§r§7 Vote : §evote.arkaniastudios.com\n§7§l»§r§7 §7Boutique : §estore.arkaniastudios.com\n§7§l»§r§7 §7Discord : §ediscord.arkaniastudios.com\n\n");
        }else{
            //rien pour l'instant
        }
        $event->setJoinMessage('');
        $player->sendTitle("§l§4» §r§cArkaniaStudios §l§4«", "§7Bienvenue §e" . $player->getName() . " §7sur le lobby !");
        $player->sendPopup('§a+ ' . $player->getName() . ' §a+');

        /*Inventory*/

        $player->getInventory()->clearAll();
        $player->getHungerManager()->setFood(20);
        $player->setHealth(20);

        $compass = VanillaItems::COMPASS();
        $compass->setCustomName("§r§fCarte de navigation");
        $lore = [
            "§r§7Clique-droit pour interagir avec la carte et ne pas perdre son chemin !",
        ];
        $compass->setLore($lore);
        $player->getInventory()->setItem(4, $compass);

    }

}