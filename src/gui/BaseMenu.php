<?php

declare(strict_types=1);

namespace arkania\gui;

use pocketmine\inventory\Inventory;
use pocketmine\permission\Permission;
use pocketmine\player\Player;

interface BaseMenu {
	public function isViewOnly() : bool;
	public function getName() : string;
	public function getClickHandler() : ?callable;
	public function getCloseHandler() : ?callable;
	public function getPermission() : null|Permission|string;
	public function closeInventory(Player $player) : void;
	public function getInventory() : Inventory;
}
