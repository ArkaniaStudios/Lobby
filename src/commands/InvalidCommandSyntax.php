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

use arkania\commands\parameters\Parameters;
use MongoDB\Driver\Exception\CommandException;
use Throwable;

class InvalidCommandSyntax extends CommandException {
    private Parameters $parameter;

    public function __construct(
        Parameters $parameter,
        string $message = "",
        int $code = 0,
        ?Throwable $previous = null
    ) {
        $this->parameter = $parameter;
        parent::__construct($message, $code, $previous);
    }

    public function getParameter(): Parameters {
        return $this->parameter;
    }

}