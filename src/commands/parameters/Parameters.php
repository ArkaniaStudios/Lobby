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
use pocketmine\network\mcpe\protocol\types\command\CommandParameter;

abstract class Parameters {
	private string $name;
	private bool $isOptional;
	private CommandParameter $commandParameter;

	public function __construct(
		string $name,
		bool $isOptional = false
	) {
		$this->name = $name;
		$this->isOptional = $isOptional;
		$this->commandParameter = new CommandParameter();
		$this->commandParameter->paramName = $name;
		$this->commandParameter->isOptional = $isOptional;
		$this->commandParameter->paramType = AvailableCommandsPacket::ARG_FLAG_VALID;
		$this->commandParameter->paramType |= $this->getNetworkType();
	}

	public function getName(): string {
		return $this->name;
	}

	public function isOptional(): bool {
		return $this->isOptional;
	}

	public function getCommandParameter(): CommandParameter {
		return $this->commandParameter;
	}

	public function getSpanLength(): int {
		return 1;
	}

	abstract public function getType(): string;

	abstract public function getNetworkType(): int;

	abstract public function parse(string $argument, CommandSender $sender): mixed;

	abstract public function canParse(string $argument, CommandSender $sender): bool;
}
