<?php

/*
 *     _      ____    _  __     _      _   _   ___      _              _____   _   _    ____   ___   _   _   _____
 *    / \    |  _ \  | |/ /    / \    | \ | | |_ _|    / \            | ____| | \ | |  / ___| |_ _| | \ | | | ____|
 *   / _ \   | |_) | | ' /    / _ \   |  \| |  | |    / _ \    _____  |  _|   |  \| | | |  _   | |  |  \| | |  _|
 *  / ___ \  |  _ <  | . \   / ___ \  | |\  |  | |   / ___ \  |_____| | |___  | |\  | | |_| |  | |  | |\  | | |___
 * /_/   \_\ |_| \_\ |_|\_\ /_/   \_\ |_| \_| |___| /_/   \_\         |_____| |_| \_|  \____| |___| |_| \_| |_____|
 *
 * ArkaniaStudios-ANGE, une API conçue pour simplifier le développement.
 * Fournissant des outils et des fonctionnalités aux développeurs.
 * Cet outil est en constante évolution et est régulièrement mis à jour,
 * afin de répondre aux besoins changeants de la communauté.
 *
 * @author Julien
 * @link https://arkaniastudios.com
 * @version 0.2.0-beta
 *
 */

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

namespace arkania\libs\muqsit\simplepackethandler;

use arkania\libs\muqsit\simplepackethandler\interceptor\IPacketInterceptor;
use arkania\libs\muqsit\simplepackethandler\interceptor\PacketInterceptor;
use arkania\libs\muqsit\simplepackethandler\monitor\IPacketMonitor;
use arkania\libs\muqsit\simplepackethandler\monitor\PacketMonitor;
use InvalidArgumentException;
use pocketmine\event\EventPriority;
use pocketmine\plugin\Plugin;

final class SimplePacketHandler {
	public static function createInterceptor(Plugin $registerer, int $priority = EventPriority::NORMAL, bool $handle_cancelled = false): IPacketInterceptor {
		if($priority === EventPriority::MONITOR) {
			throw new InvalidArgumentException("Cannot intercept packets at MONITOR priority");
		}
		return new PacketInterceptor($registerer, $priority, $handle_cancelled);
	}

	public static function createMonitor(Plugin $registerer, bool $handle_cancelled = false): IPacketMonitor {
		return new PacketMonitor($registerer, $handle_cancelled);
	}
}
