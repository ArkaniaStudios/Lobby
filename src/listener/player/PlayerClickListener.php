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

        if ($item->equals(VanillaItems::COMPASS()->setCustomName("§r§fCarte")->setLore(["§r§7Clique-droit pour interagir avec la carte !"]))) {
            $player->getServer()->dispatchCommand($player, "navigator");
        }
    }
}