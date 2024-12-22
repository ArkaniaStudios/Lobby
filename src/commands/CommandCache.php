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

use arkania\Main;
use pocketmine\command\SimpleCommandMap;

class CommandCache {
	private SimpleCommandMap $commandMap;

	public function __construct(
		readonly Main $plugin
	) {
		$this->commandMap = $plugin->getServer()->getCommandMap();
	}

	public function registerCommands(CommandBase ...$commands): void {
		$this->commandMap->registerAll('ArkaniaStudios', $commands);
	}
	public function unregisterCommands(string ...$string): void {
		foreach ($string as $command) {
			$this->commandMap->unregister($this->commandMap->getCommand($command));
		}
	}

}
