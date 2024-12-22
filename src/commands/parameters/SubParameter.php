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

use arkania\commands\EnumStore;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\network\mcpe\protocol\types\command\CommandEnum;
use pocketmine\network\mcpe\protocol\types\command\CommandParameter;

use function strtolower;

class SubParameter extends Parameters {
	public function __construct(
		string $name,
		bool $isOptional = false
	) {
		parent::__construct($name, $isOptional);
		$this->getCommandParameter()->paramType = AvailableCommandsPacket::ARG_FLAG_VALID | AvailableCommandsPacket::ARG_FLAG_ENUM;
		EnumStore::addEnum($this->getCommandParameter()->enum = new CommandEnum(strtolower($name), [strtolower($name)]));
	}

	public function getType(): string {
		return "sub";
	}

	public function getNetworkType(): int {
		return CommandParameter::FLAG_FORCE_COLLAPSE_ENUM;
	}

	public function canParse(string $argument, CommandSender $sender): bool {
		return true;
	}

	public function parse(string $argument, CommandSender $sender): mixed {
		return $argument;
	}

}
