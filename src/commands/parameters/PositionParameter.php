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

use function count;
use function explode;

use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\Server;
use pocketmine\world\Position;

class PositionParameter extends Parameters {
	public function getType(): string {
		return 'position';
	}

	public function getNetworkType(): int {
		return AvailableCommandsPacket::ARG_TYPE_POSITION;
	}

	public function canParse(string $argument, CommandSender $sender): bool {
		return true;
	}

	public function parse(string $argument, CommandSender $sender): ?Position {
		$position = explode(' ', $argument);
		if (count($position) !== 4) {
			return null;
		}
		return new Position((int) $position[0], (int) $position[1], (int) $position[2], Server::getInstance()->getWorldManager()->getWorldByName($position[4]));
	}

}
