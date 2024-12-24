<?php

declare(strict_types=1);

/*
 *     _      ____    _  __     _      _   _   ___      _
 *    / \    |  _ \  | |/ /    / \    | \ | | |_ _|    / \
 *   / _ \   | |_) | | ' /    / _ \   |  \| |  | |    / _ \
 *  / ___ \  |  _ <  | . \   / ___ \  | |\  |  | |   / ___ \
 * /_/   \_\ |_| \_\ |_|\_\ /_/   \_\ |_| \_| |___| /_/   \_\
 *
 * Nous sommes un serveur Minecraft : Bedrock Edition avec plus de 1000 joueurs inscrits !
 * L'équipe de développement est composée d'environ 5 personnes, toutes françaises.
 * ArkaniaStudios nous permet d'élargir notre expérience tout en construisant quelque chose de remarquable.
 *
 * @author Julien
 * @link github.com/ArkaniaStudios
 * @version 1.0.0
 *
 */

namespace arkania\commands\parameters;

use function array_keys;
use function array_map;

use function implode;

use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\types\command\CommandEnum;

use function preg_match;
use function strtolower;

abstract class EnumParameter extends Parameters {
	/** @var string[]|bool[]|int[]|float[] */
	protected array $value = [];

	public function __construct(
		string $name,
		string $enumName,
		bool $isOptional = false
	) {
		parent::__construct($name, $isOptional);
		$this->getCommandParameter()->enum = new CommandEnum($enumName, $this->getEnumValues());
	}

	public function getType(): string {
		return 'enum';
	}

	public function getNetworkType(): int {
		return -1;
	}

	public function canParse(string $argument, CommandSender $sender): bool {
		return (bool) preg_match(
			"/^(".implode("|", array_map('\\strtolower', $this->getEnumValues())).")$/iu",
			$argument
		);
	}

	public function addValue(string $string, bool|float|int|string $value): void {
		$this->value[strtolower($string)] = $value;
	}

	public function getValue(string $string): null|bool|float|int|string {
		return $this->value[strtolower($string)];
	}

	/**
	 * @return string[]|bool[]|int[]|float[]
	 */
	public function getEnumValues(): array {
		return array_keys($this->value);
	}

    public function getValues(): array {
        return $this->value;
    }

}
