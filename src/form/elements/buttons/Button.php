<?php

declare(strict_types=1);

namespace arkania\form\elements\buttons;

use JsonSerializable;
use arkania\form\elements\icon\Icon;
use arkania\form\trait\PermissibleTrait;
use pocketmine\permission\Permission;

abstract class Button implements JsonSerializable {
	use PermissibleTrait;

	private string $name;
	private string $text;
	private null|Icon $icon;

	public function __construct(
		string $name,
		string $text,
		null|Permission|string $permission = null,
		null|Icon $icon = null
	) {
		$this->name = $name;
		$this->text = $text;
		$this->icon = $icon;
		if($permission !== null) {
			$this->setPermission($permission);
		}
	}

	public function getName() : string {
		return $this->name;
	}

	/**
	 * @return array<string, string|array>
	 */
	public function jsonSerialize() : array {
		return [
			"text"       => $this->text,
			"image"      => $this->icon?->jsonSerialize() ?? null,
			"permission" => $this->permission instanceof Permission ? $this->permission->getName() : $this->permission
		];
	}

}
