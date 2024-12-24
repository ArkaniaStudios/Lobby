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

use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

use function preg_match;

class IntParameter extends Parameters {
	private bool $acceptsNegative;

	public function __construct(
		string $name,
		bool $acceptsNegative = false,
		bool $isOptional = false
	) {
		parent::__construct($name, $isOptional);
		$this->acceptsNegative = $acceptsNegative;
	}

	public function getType(): string {
		return 'int';
	}

	public function getNetworkType(): int {
		return AvailableCommandsPacket::ARG_TYPE_INT;
	}

	public function canParse(string $argument, CommandSender $sender): bool {
		if ($this->acceptsNegative) {
			return (bool)preg_match('/^[-+]?\d+$/', $argument);
		}
		return (bool)preg_match('/^\d+$/', $argument);

	}

	public function parse(string $argument, CommandSender $sender): int {
		return (int) $argument;
	}

}
