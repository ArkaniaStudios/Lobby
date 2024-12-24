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

use InvalidArgumentException;
use pocketmine\command\CommandSender;

class BooleanParameter extends EnumParameter {
    public function __construct(string $name, string $enumName, bool $isOptional = false) {
        parent::__construct($name, $enumName, $isOptional);
        $this->addValue('on', true);
        $this->addValue('off', false);
    }

    public function parse(string $argument, CommandSender $sender): bool {
        $value = $this->getValue($argument);
        if ($value === null) {
            throw new InvalidArgumentException('Invalid boolean value');
        }
        return $value;
    }

}