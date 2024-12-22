<?php

declare(strict_types=1);

namespace arkania\gui;

use pocketmine\item\Item;

final class InventoryContent {
	public int $slot;
	public Item $item;

	public function __construct(int $slot, Item $item) {
		$this->slot = $slot;
		$this->item = $item;
	}

	public function getSlot() : int {
		return $this->slot;
	}

	public function getItem() : Item {
		return $this->item;
	}

}
