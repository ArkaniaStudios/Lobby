<?php

declare(strict_types=1);

namespace arkania\gui;

use arkania\form\trait\PermissibleTrait;
use arkania\session\Session;
use pocketmine\inventory\Inventory;
use pocketmine\player\Player;

trait MenuTrait {
	use PermissibleTrait;

	private string $name;

	private bool $viewOnly;

	/** @var ?callable */
	private $clickHandler;

	/** @var ?callable */
	private $closeHandler;

	private Inventory $inventory;

	/**
	 * @param InventoryContent[]|null $contents
	 */
	public function __construct(
		Inventory $inventory,
		string $name,
		bool $viewOnly = false,
		?array $contents = null,
		?callable $clickHandler = null,
		?callable $closeHandler = null,
		?string $permission = null
	) {
		$this->inventory = $inventory;
		$this->name      = $name;
		$this->viewOnly  = $viewOnly;
		if($contents !== null) {
			foreach ($contents as $content) {
				$this->setItem($content->getSlot(), $content->getItem());
			}
		}
		$this->clickHandler = $clickHandler;
		$this->closeHandler = $closeHandler;
		if($permission !== null) {
			$this->setPermission($permission);
		}
	}

	public function getClickHandler() : ?callable {
		return $this->clickHandler;
	}

	public function getCloseHandler() : ?callable {
		return $this->closeHandler;
	}

	public function getName() : string {
		return $this->name;
	}

	public function isViewOnly() : bool {
		return $this->viewOnly;
	}

	public function closeInventory(Player $player) : void {
		if($this->closeHandler !== null) {
			($this->closeHandler)($player, $this);
		}
		$session = Session::get($player);
		$session->setCurrent(null);
	}

	protected function sendInv(Player $player) : void {
		$session = Session::get($player);
		$session->setCurrent($this);
	}

	public function getInventory() : Inventory {
		return $this->inventory;
	}
}
