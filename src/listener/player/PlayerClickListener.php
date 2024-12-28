<?php

declare(strict_types=1);

namespace arkania\listener\player;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\VanillaItems;

class PlayerClickListener implements Listener {

    public function onNavigatorClick(PlayerItemUseEvent $event): void {
        $player = $event->getPlayer();
        $item = $event->getItem();

        if ($item->equals(VanillaItems::COMPASS()->setCustomName("§r§fCarte de navigation")->setLore(["§r§7Clique-droit pour interagir avec la carte et ne pas perdre son chemin !"]))) {
            $player->getServer()->dispatchCommand($player, "navigator");
        }
    }
}