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

namespace arkania\commands;

use pocketmine\network\mcpe\protocol\types\command\CommandEnum;
use pocketmine\network\mcpe\protocol\UpdateSoftEnumPacket;
use pocketmine\Server;

class EnumStore {
	/** @var CommandEnum[] */
	private static array $enum = [];

	public static function getEnum(string $name): ?CommandEnum {
		return self::$enum[$name] ?? null;
	}

	/**
	 * @return CommandEnum[]
	 */
	public static function getEnums(): array {
		return self::$enum;
	}

	public static function addEnum(CommandEnum $enum): void {
		self::$enum[$enum->getName()] = $enum;
		self::broadcastEnum($enum, UpdateSoftEnumPacket::TYPE_ADD);
	}

	public static function broadcastEnum(CommandEnum $enum, int $type): void {
		$pk = new UpdateSoftEnumPacket();
		$pk->enumName = $enum->getName();
		$pk->values = $enum->getValues();
		$pk->type = $type;
		foreach (Server::getInstance()->getOnlinePlayers() as $player) {
			$player->getNetworkSession()->sendDataPacket($pk);
		}
	}

}
