<?php
declare(strict_types=1);

namespace arkania\listener\player;

use arkania\session\Session;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\item\VanillaItems;

class PlayerJoinListener implements Listener {

    public function onPlayerJoin(PlayerJoinEvent $event) : void {
        $player = $event->getPlayer();
        $session = Session::get($player);

        /*Proxy*/
        if($player->getNetworkSession()->getIp() !== "172.18.0.1" && $player->getNetworkSession()->getIp() !== "127.0.0.1"){
            $player->kick("§cVous avez été kick du serveur car vous n'êtes pas passé par le lobby !\n§fSi vous pensez que ceci est une erreur merci de contacter l'équipe du staff d'arkania : https://discord.gg/ZU7CJ3PtZj");
        }

        /*Messages*/

        if (!$player->hasPlayedBefore()) {
            $player->sendMessage("\n§cArkaniaStudios §f(§7Lobby§f)\n\n§7§l-§r§7 Vote : §evote.arkaniastudios.com\n§7§l-§r§7 §7Boutique : §estore.arkaniastudios.com\n§7§l-§r§7 §7Discord : §ediscord.arkaniastudios.com\n\n");
        }else{
            $player->sendTitle("§fBienvenue sur", "§cArkaniaStudios");
            $player->sendMessage("\n§cArkaniaStudios §f(§7Lobby§f)\n\n§7§l-§r§7 Vote : §evote.arkaniastudios.com\n§7§l-§r§7 §7Boutique : §estore.arkaniastudios.com\n§7§l-§r§7 §7Discord : §ediscord.arkaniastudios.com\n\n");
        }
        $event->setJoinMessage('');
        $player->sendPopup('[§a+§f] ' . $player->getName());

        /*Inventory*/

        $player->getInventory()->clearAll();

        $compass = VanillaItems::COMPASS();
        $compass->setCustomName("§r§fCarte");
        $lore = [
            "§r§7Clique-droit pour interagir avec la carte !",
        ];
        $compass->setLore($lore);
        $player->getInventory()->setItem(4, $compass);

    }

}