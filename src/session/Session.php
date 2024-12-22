<?php
declare(strict_types=1);

namespace arkania\session;

use arkania\database\result\SqlSelectResult;
use arkania\Main;
use arkania\utils\Date;
use arkania\utils\Utils;
use arkania\utils\promise\PromiseInterface;
use pocketmine\player\GameMode;
use pocketmine\Server;

class Session {
    use SessionStorage;

    public function getRank() : PromiseInterface {
        return Main::getInstance()->getDatabase()->getConnector()->executeSelect(
            "SELECT rank FROM players WHERE name= ?",
            [$this->getName()]
        )->then(function (SqlSelectResult $result) : string {
            if ($result->count() <= 0) {
                return 'Joueur';
            }
            return $result->getRows()[0]['rank'];
        });
    }

    public function hasCooldown(string $type) : PromiseInterface {
        return Main::getInstance()->getDatabase()->getConnector()->executeSelect(
            "SELECT * FROM cooldowns WHERE player= ? AND type= ? AND end > ?",
            [$this->getName(), $type, time()]
        )->then(function (SqlSelectResult $result) use ($type) : array {
            if ($result->count() <= 0) {
                Main::getInstance()->getDatabase()->getConnector()->executeGeneric(
                    "DELETE FROM cooldowns WHERE player= ? AND type= ?",
                    [$this->getName(), $type]
                );
                return ['hasCooldown' => false, 'timeLeft' => 0];
            }
            return ['hasCooldown' => true, 'timeLeft' => $result->getRows()[0]['end'] - time()];
        });
    }

    public function addCooldown(string $type, int $time) : PromiseInterface {
        return Main::getInstance()->getDatabase()->getConnector()->executeInsert(
            "INSERT INTO cooldowns (player, type, end) VALUES (?, ?, ?)",
            [$this->getName(), $type, time() + $time]
        );
    }

    public function getPermission() : PromiseInterface {
        return Main::getInstance()->getDatabase()->getConnector()->executeSelect(
            "SELECT permissions FROM players WHERE name= ?",
            [$this->getName()]
        )->then(function (SqlSelectResult $result) : string {
            if ($result->count() <= 0) {
                return 'Joueur';
            }
            return unserialize($result->getRows()[0]['permissions']);
        });
    }

    public function save() : void {
        Main::getInstance()->getDatabase()->getConnector()->executeChange(
            "UPDATE players SET last_ip = ?, last_login = ?, gamemode = ?, settings = ?, op = ?, online = ? WHERE name = ?",
            [
                $this->getPlayer()->getNetworkSession()->getIp(),
                Date::create()->__toString(),
                $this->getPlayer()->getGamemode()->getEnglishName(),
                serialize([]),
                (int)Server::getInstance()->isOp($this->getPlayer()->getName()),
                serialize(['status' => '§cHors ligne', 'server' => Utils::getName()]),
                $this->getPlayer()->getName()
            ]
        );
    }

    public function load() : void {
        Main::getInstance()->getDatabase()->getConnector()->executeSelect(
            'SELECT * FROM players WHERE name = ?',
            [$this->getPlayer()->getName()],
        )->then(function (SqlSelectResult $result) : void {
            if ($result->count() <= 0) {
                return;
            }
            $data = $result->getRows()[0];
            $this->getPlayer()->setGamemode(GameMode::fromString($data['gamemode']));
            if ($data['op'] === 1) {
                Server::getInstance()->addOp($this->getPlayer()->getName());
            }
            Main::getInstance()->getDatabase()->getConnector()->executeChange(
                'UPDATE players SET last_ip = ?, last_login = ?, online = ? WHERE name = ?',
                [
                    $this->getPlayer()->getNetworkSession()->getIp(),
                    Date::create()->__toString(),
                    serialize(['status' => '§aEn ligne', 'server' => Utils::getName()]),
                    $this->getPlayer()->getName()
                ]
            );
        });
    }
}