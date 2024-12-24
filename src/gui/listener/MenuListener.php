<?php

declare(strict_types=1);

namespace arkania\gui\listener;

use arkania\gui\BaseMenu;
use arkania\gui\transaction\MenuTransaction;
use arkania\gui\transaction\MenuTransactionResult;
use arkania\session\Session;
use arkania\utils\Utils;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\inventory\InventoryCloseEvent;
use pocketmine\event\inventory\InventoryOpenEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\inventory\transaction\action\SlotChangeAction;

use function is_null;

final class MenuListener implements Listener {
	public function onInventoryTransaction(InventoryTransactionEvent $event) : void {
		$transaction = $event->getTransaction();
		$player      = $transaction->getSource();
		foreach ($transaction->getActions() as $action) {
			if ($action instanceof SlotChangeAction) {
				$inventory = $action->getInventory();
				if ($inventory instanceof BaseMenu) {
					$clickCallback = $inventory->getClickHandler();
					if ($clickCallback !== null) {
						$result = $clickCallback($player, new MenuTransaction($inventory, $action->getSourceItem(), $action->getTargetItem(), $action->getSlot()));
						if ($result instanceof MenuTransactionResult) {
							if($result->isCancelled()) {
								$event->cancel();
							}
						}
						return;
					}
					if ($inventory->isViewOnly()) {
						$event->cancel();
					}
				}
			}
		}
	}

	public function onInventoryOpen(InventoryOpenEvent $event) : void {
		$inventory = $event->getInventory();
		if ($inventory instanceof BaseMenu) {
			if($inventory->getPermission() !== null && !$event->getPlayer()->hasPermission($inventory->getPermission())) {
				$event->getPlayer()->sendMessage(Utils::getPrefix() . '§cVous n\'avez pas la permission d\'ouvrir ce menu.');
				$event->cancel();
			}
		}
	}

	public function onInventoryClose(InventoryCloseEvent $event) : void {
		$player  = $event->getPlayer();
		$session = Session::get($player);
		$current = $session->getCurrent();

		if($current instanceof BaseMenu) {
			$current->closeInventory($player);
		}elseif (!is_null($current)) {
            $session->setCurrent(null);
            Utils::sendFakeBlock($player, VanillaBlocks::AIR(), 0, 3, 0);
        }
	}
}
