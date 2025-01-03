<?php

declare(strict_types=1);

namespace arkania\listener\player;

use arkania\items\ExtraItems;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;

class PlayerClickListener implements Listener {

    public function onNavigatorUse(PlayerItemUseEvent $event): void {
        $player = $event->getPlayer();
        $item = $event->getItem();

        if ($item->equals(ExtraItems::ITEM_MAP()->setCustomName("§r§fCarte de navigation")->setLore(["§r§7Clique-droit pour interagir avec la carte et ne pas perdre son chemin !"]))) {
            $player->getServer()->dispatchCommand($player, "navigator");
        }
    }
}