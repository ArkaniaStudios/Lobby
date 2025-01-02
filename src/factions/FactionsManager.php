<?php
declare(strict_types=1);

namespace arkania\factions;

use arkania\database\result\SqlSelectResult;
use arkania\Main;
use arkania\utils\promise\PromiseInterface;

class FactionsManager {

    public function __construct() {
        Main::getInstance()->getDatabase()->getConnector()->executeGeneric(
            "CREATE TABLE IF NOT EXISTS factions(name VARCHAR(10), description TEXT, creation_date TEXT, ownerName VARCHAR(20), allies TEXT, members TEXT, power INT, money INT, logs TEXT, home TEXT, ranks TEXT, level INT, xp FLOAT);"
        );
        Main::getInstance()->getDatabase()->getConnector()->executeGeneric(
            "CREATE TABLE IF NOT EXISTS players_faction(name VARCHAR(20), faction VARCHAR(16), faction_rank VARCHAR(16));"
        );
        Main::getInstance()->getDatabase()->getConnector()->executeGeneric(
            "CREATE TABLE IF NOT EXISTS claims(factionName VARCHAR(10), chunkX INT, chunkZ INT, world TEXT, server TEXT);"
        );
    }

    public function getPlayerFaction(string $playerName) : PromiseInterface {
        return Main::getInstance()->getDatabase()->getConnector()->executeSelect(
            "SELECT * FROM players_faction WHERE name= ?",
            [$playerName]
        )->then(function (SqlSelectResult $result) : ?array {
            if($result->count() <= 0) {
                return null;
            }
            return $result->getRows()[0];
        });
    }

}