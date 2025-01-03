<?php
declare(strict_types=1);

namespace arkania\network\servers;

use arkania\database\result\SqlSelectResult;
use arkania\Main;
use arkania\utils\Date;
use arkania\utils\promise\PromiseInterface;

class ServersManager {

    public function __construct() {
        Main::getInstance()->getDatabase()->getConnector()->executeGeneric(
            "CREATE TABLE IF NOT EXISTS servers (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                ip VARCHAR(255) NOT NULL,
                port INT NOT NULL,
                status TEXT NOT NULL,
                players INT NOT NULL,
                max_players INT NOT NULL
            )"
        );
        $this->getServer(Main::getInstance()->getConfig()->get('name'))->then(function (?array $result) : void {
            if ($result === null) {
                $this->addServer(
                    Main::getInstance()->getConfig()->get('name'),
                    Main::getInstance()->getServer()->getIp(),
                    Main::getInstance()->getServer()->getPort(),
                    ['status' => ServersStatus::OFFLINE, 'time' => Date::now(false)],
                    0,
                    Main::getInstance()->getServer()->getMaxPlayers()
                );
            }
        });
    }

    public function addServer(string $name, string $ip, int $port, array $status, int $players, int $max_players) : PromiseInterface {
        return Main::getInstance()->getDatabase()->getConnector()->executeGeneric(
            "INSERT INTO servers (name, ip, port, status, players, max_players) VALUES (?, ?, ?, ?, ?, ?)",
            [$name, $ip, $port, serialize($status), $players, $max_players]
        );
    }

    public function updateServer(string $name, array $status, int $players, int $max_players) : PromiseInterface {
        return Main::getInstance()->getDatabase()->getConnector()->executeGeneric(
            "UPDATE servers SET status = ?, players = ?, max_players = ? WHERE name = ?",
            [serialize($status), $players, $max_players, $name]
        );
    }

    public function getServer(string $name) : PromiseInterface {
        return Main::getInstance()->getDatabase()->getConnector()->executeSelect(
            "SELECT * FROM servers WHERE name = ?",
            [$name]
        )->then(function (SqlSelectResult $result) : ?array {
            if ($result->count() <= 0) {
                return null;
            }
            return $result->getRows()[0];
        });
    }
    public function getServers() : PromiseInterface {
        return Main::getInstance()->getDatabase()->getConnector()->executeSelect(
            "SELECT * FROM servers"
        )->then(function (SqlSelectResult $result) : array {
            return $result->getRows();
        });
    }

    public function addPlayer(string $server) : PromiseInterface {
        return $this->getServer($server)->then(function (?array $result) use ($server) : void {
            if ($result !== null) {
                $this->updateServer(
                    $server,
                    unserialize($result['status']),
                    $result['players'] + 1,
                    $result['max_players']
                );
            }
        });
    }

    public function removePlayer(string $server) : PromiseInterface {
        return $this->getServer($server)->then(function (?array $result) use ($server) : void {
            if ($result !== null) {
                $this->updateServer(
                    $server,
                    unserialize($result['status']),
                    $result['players'] - 1,
                    $result['max_players']
                );
            }
        });
    }

}