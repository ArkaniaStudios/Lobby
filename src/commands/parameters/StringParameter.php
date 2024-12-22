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

class StringParameter extends Parameters {
	public function getType(): string {
		return 'string';
	}

	public function getNetworkType(): int {
		return AvailableCommandsPacket::ARG_TYPE_STRING;
	}

	public function canParse(string $argument, CommandSender $sender): bool {
		return true;
	}

	public function parse(string $argument, CommandSender $sender): string {
		return $argument;
	}
}
