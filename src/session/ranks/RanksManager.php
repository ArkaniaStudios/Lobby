<?php
declare(strict_types=1);

namespace arkania\session\ranks;

use arkania\database\result\SqlSelectResult;
use arkania\Main;
use arkania\session\Session;
use arkania\utils\Date;
use arkania\utils\promise\PromiseInterface;
use pocketmine\player\Player;
use pocketmine\Server;

class RanksManager {

    private array $attachable = [];

    public function __construct() {
        Main::getInstance()->getDatabase()->getConnector()->executeGeneric(
            "CREATE TABLE IF NOT EXISTS ranks (
                    name VARCHAR(50) NOT NULL,
                    position INTEGER NOT NULL,
                    format TEXT NOT NULL,
                    discord_format TEXT NOT NULL,
                    nametag TEXT NOT NULL,
                    permissions TEXT NOT NULL,
                    inheritance TEXT NOT NULL,
                    color VARCHAR(3) NOT NULL,
                    prefix VARCHAR(50) NOT NULL,
                    created_at TEXT NOT NULL
            )"
        )->then(function () : void {
            Main::getInstance()->getDatabase()->getConnector()->executeSelect(
                "SELECT * FROM ranks WHERE name = ?",
                ['Joueur']
            )->then(function (SqlSelectResult $result) : void {
                if($result->count() <= 0) {
                    $date = Date::now(false);
                    Main::getInstance()->getDatabase()->getConnector()->executeInsert(
                        "INSERT INTO ranks (name, position, format, discord_format, nametag, permissions, inheritance, color, prefix, created_at)
    VALUES ('Joueur', 1, '§8[§7{FAC_RANK}{FAC_NAME}§8]§f[§7Joueur§f] {COLOR}{NAME} §l§8» §r§7{MESSAGE}', '[{FAC_RANK}{FAC_NAME}][Joueur] **{NAME}** » {MESSAGE}', '{COLOR}[§e{FAC_NAME}{COLOR}] {LINE} §f{NAME} ', 'a:0:{}', 'a:0:{}', '§8', 'Joueur', '" . $date . "')
    ON DUPLICATE KEY UPDATE name = name;"
                    );
                }
            });
        });
    }

    public function getRank(string $name) : PromiseInterface {
        return Main::getInstance()->getDatabase()->getConnector()->executeSelect(
            "SELECT * FROM ranks WHERE name = ? OR prefix = ?",
            [$name, $name]
        )->then(function (SqlSelectResult $result) : ?Ranks {
            if($result->count() <= 0) {
                return null;
            }
            $values = $result->getRows()[0];
            return new Ranks(
                $values['name'],
                $values['position'],
                $values['format'],
                $values['discord_format'],
                $values['nametag'],
                unserialize($values['permissions']),
                unserialize($values['inheritance']),
                $values['color'],
                $values['prefix']
            );
        });
    }

    public function getChatFormat(string $name) : PromiseInterface {
        return Main::getInstance()->getDatabase()->getConnector()->executeSelect(
            "SELECT * FROM ranks WHERE name = ?",
            [$name]
        )->then(function (SqlSelectResult $result) : array {
            if($result->count() <= 0) {
                return ['format' => '§f[§e{FAC_RANK}{FAC_NAME}§f]§f[§8Joueur§f] {COLOR}{NAME} §l§7» §r{MESSAGE}', 'color' => '§8'];
            }
            return ['format' => $result->getRows()[0]['format'], 'color' => $result->getRows()[0]['color']];
        });
    }

    public function getNametagFormat(string $name) : PromiseInterface {
        return Main::getInstance()->getDatabase()->getConnector()->executeSelect(
            "SELECT * FROM ranks WHERE name = ?",
            [$name]
        )->then(function (SqlSelectResult $result) : array {
            if($result->count() <= 0) {
                return ['format' => '{COLOR}[§e{FAC_NAME}{COLOR}] {LINE} §f{NAME} ', 'color' => '§8'];
            }
            return ['format' => $result->getRows()[0]['nametag'], 'color' => $result->getRows()[0]['color']];
        });
    }

    public function orderPlayerList() : PromiseInterface {
        return Main::getInstance()->getDatabase()->getConnector()->executeSelect(
            "SELECT name, position, color FROM ranks;"
        )->then(function (SqlSelectResult $rankResult) : PromiseInterface {
            return Main::getInstance()->getDatabase()->getConnector()->executeSelect(
                "SELECT name, rank FROM players;"
            )->then(function (SqlSelectResult $playerResult) use ($rankResult) {
                $rankPositions = [];
                foreach ($rankResult->getRows() as $rankRow) {
                    $rankPositions[$rankRow['name']] = ['position' => $rankRow['position'], 'color' => $rankRow['color']];
                }

                $playerList = [];
                foreach ($playerResult->getRows() as $playerRow) {
                    $playerRank = $playerRow['rank'];
                    if (isset($rankPositions[$playerRank])) {
                        $playerList[] = [
                            'name' => $playerRow['name'],
                            'rank' => $playerRank,
                            'position' => $rankPositions[$playerRank]['position'],
                            'color' => $rankPositions[$playerRank]['color']
                        ];
                    }
                }

                usort($playerList, function ($a, $b) {
                    return $a['position'] <=> $b['position'];
                });

                return $playerList;
            });
        })->then(function (array $sortedList) : array {
            $connectedPlayers = [];
            foreach (Server::getInstance()->getOnlinePlayers() as $player) {
                foreach ($sortedList as $entry) {
                    if ($player->getName() === $entry['name']) {
                        $connectedPlayers[] = $entry;
                    }
                }
            }
            return [
                'allPlayers' => $sortedList,
                'connectedPlayers' => $connectedPlayers,
            ];
        });
    }

    public function register(Player $player) : void {
        if(!isset($this->attachable[$player->getName()])) {
            $this->attachable[$player->getName()] = $player->addAttachment(Main::getInstance());
            $this->update(Session::get($player));
        }
    }

    public function unregister(Player $player) : void {
        if(isset($this->attachable[$player->getName()])) {
            $this->attachable[$player->getName()]->remove();
            unset($this->attachable[$player->getName()]);
        }
    }

    public function update(Session $session) : void {
        $this->updateNameTag($session->getPlayer());
        $session->getRank()->then(function ($playerRank) use ($session) {
            $this->getRank($playerRank)->then(function (?Ranks $rank) use ($session) {
                if ($rank === null) {
                    return;
                }
                $this->register($session->getPlayer());
                $attachment = $this->attachable[$session->getName()];
                $attachment->clearPermissions();
                foreach ($rank->getPermissions() as $permission) {
                    $attachment->setPermission($permission, true);
                    foreach ($rank->getInheritance() as $inherit) {
                        $this->getRank($inherit)->then(function (?Ranks $rank) use ($attachment) {
                            if ($rank === null) {
                                return;
                            }
                            foreach ($rank->getPermissions() as $permission) {
                                $attachment->setPermission($permission, true);
                            }
                        });
                    }
                }
            });
        });
    }

    public function updateNameTag(Player $player) : void {
        $session = Session::get($player);
        $session->getRank()->then(function ($playerRank) use ($session, $player) {
            $this->getRank($playerRank)->then(function (?Ranks $rank) use ($session, $player) {
                if ($rank === null) {
                    return;
                }
                $player->setNameTag(str_replace(
                    ['{FAC_NAME}', '{COLOR}', '{LINE}', '{NAME}'],
                    ['Dev', $rank->getColor(), "\n", $player->getName()],
                    $rank->getNametag()
                ));
            });
        });
    }

    public function updateRank(Ranks $rank) : PromiseInterface {
        return Main::getInstance()->getDatabase()->getConnector()->executeChange(
            'UPDATE ranks SET format = ?, discord_format = ?, nametag = ?, permissions = ?, inheritance = ?, color = ?, position = ?, prefix = ? WHERE name = ?',
            [
                $rank->getFormat(),
                $rank->getDiscordFormat(),
                $rank->getNametag(),
                serialize($rank->getPermissions()),
                serialize($rank->getInheritance()),
                $rank->getColor(),
                $rank->getPosition(),
                $rank->getPrefix(),
                $rank->getName()
            ]
        )->then(function () use ($rank) : void {
            Main::getInstance()->getDatabase()->getConnector()->executeSelect(
                "SELECT position FROM ranks WHERE name = ?",
                [$rank->getName()]
            )->then(function (SqlSelectResult $result) use ($rank) : void {
                if($result->count() <= 0) {
                    return;
                }
                $position = $result->getRows()[0]['position'];
                Main::getInstance()->getDatabase()->getConnector()->executeSelect(
                    "SELECT * FROM ranks WHERE position = ? AND name != ?",
                    [$position, $rank->getName()]
                )->then(function (SqlSelectResult $result) use ($rank) : void {
                    if($result->count() <= 0) {
                        return;
                    }
                    $otherRank = $result->getRows()[0];
                    Main::getInstance()->getDatabase()->getConnector()->executeGeneric(
                        "UPDATE ranks SET position = ? WHERE name = ?",
                        [$rank->getPosition(), $otherRank['name']]
                    );
                });
            });
        });
    }

    public function getNextPosition() : PromiseInterface {
        return Main::getInstance()->getDatabase()->getConnector()->executeSelect(
            'SELECT MAX(position) as position FROM ranks;'
        )->then(function (SqlSelectResult $result) : int {
            if($result->count() <= 0) {
                return 1;
            }
            return $result->getRows()[0]['position'] + 1;
        });
    }

    public function addRank(string $rank) : PromiseInterface {
        return $this->getNextPosition()->then(function (int $position) use ($rank) : void {
            Main::getInstance()->getDatabase()->getConnector()->executeInsert(
                'INSERT INTO ranks (name, position, format, discord_format, nametag, permissions, inheritance, color, prefix, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [
                    $rank,
                    $position,
                    '§f[§e{FAC_RANK}{FAC_NAME}§f]§f[§9' . $rank . '§f] {COLOR}{NAME} §l§7» §r{MESSAGE}',
                    '[{FAC_RANK}{FAC_NAME}][' . $rank . '] {NAME} » {MESSAGE}',
                    '{COLOR}[§e{FAC_NAME}{COLOR}] {LINE} §f{NAME} ',
                    serialize([]),
                    serialize([]),
                    '§9',
                    $rank,
                    Date::now(false)
                ]
            );
        });
    }

    public function removeRank(string $name) : PromiseInterface {
        return $this->getRank($name)->then(function (?Ranks $rank) : bool{
            if ($rank === null) {
                return false;
            }
            Main::getInstance()->getDatabase()->getConnector()->executeGeneric(
                'DELETE FROM ranks WHERE name = ?',
                [$rank->getName()]
            )->then(function () use ($rank) {
                Main::getInstance()->getDatabase()->getConnector()->executeGeneric(
                    'UPDATE players SET rank = ? WHERE rank = ?',
                    ['Joueur', $rank->getName()]
                )->then(function () {
                    foreach (Main::getInstance()->getServer()->getOnlinePlayers() as $player) {
                        $this->update(Session::get($player));
                    }
                });
            });
            return true;
        });
    }


    private function updateRanksPosition() : void {
        Main::getInstance()->getDatabase()->getConnector()->executeSelect(
            "SELECT * FROM ranks ORDER BY position ASC"
        )->then(function (SqlSelectResult $result) : void {
            $position = 1;
            foreach ($result->getRows() as $row) {
                Main::getInstance()->getDatabase()->getConnector()->executeGeneric(
                    "UPDATE ranks SET position = ? WHERE name = ?",
                    [$position, $row['name']]
                );
                $position++;
            }
        });
    }

    public function setRank(string $name, Ranks $ranks) : PromiseInterface {
        return Main::getInstance()->getDatabase()->getConnector()->executeGeneric(
            "UPDATE players SET rank = ? WHERE name = ?",
            [$ranks->getName(), $name]
        )->then(function () use ($name, $ranks) : void {
            $player = Server::getInstance()->getPlayerExact($name);
            if($player !== null) {
                $this->update(Session::get($player));
            }
        });
    }

    public function getRanksList() : PromiseInterface {
        return Main::getInstance()->getDatabase()->getConnector()->executeSelect(
            "SELECT r.*, COUNT(p.rank) as player_count
         FROM ranks r
         LEFT JOIN players p ON r.name = p.rank
         GROUP BY r.name
         ORDER BY r.position ASC"
        )->then(function (SqlSelectResult $result) : array {
            $ranks = [];
            foreach ($result->getRows() as $row) {
                $ranks[] = [
                    'rank' => new Ranks(
                        $row['name'],
                        $row['position'],
                        $row['format'],
                        $row['discord_format'],
                        $row['nametag'],
                        unserialize($row['permissions']),
                        unserialize($row['inheritance']),
                        $row['color'],
                        $row['prefix']
                    ),
                    'player_count' => $row['player_count']
                ];
            }
            return $ranks;
        });
    }
    public function addInheritance(mixed $child, mixed $parent) : PromiseInterface {
        return Main::getInstance()->getDatabase()->getConnector()->executeSelect(
            "SELECT * FROM ranks WHERE name = ?",
            [$child]
        )->then(function (SqlSelectResult $result) use ($child, $parent) : void {
            if($result->count() <= 0) {
                return;
            }
            $childRank = $result->getRows()[0];
            $inheritance = unserialize($childRank['inheritance']);
            $inheritance[] = $parent;
            Main::getInstance()->getDatabase()->getConnector()->executeGeneric(
                "UPDATE ranks SET inheritance = ? WHERE name = ?",
                [serialize($inheritance), $child]
            )->then(function () : void {
                foreach (Server::getInstance()->getOnlinePlayers() as $player) {
                    $this->update(Session::get($player));
                }
            });
        });
    }
    public function removeInheritance(mixed $child, mixed $parent) : PromiseInterface {
        return Main::getInstance()->getDatabase()->getConnector()->executeSelect(
            "SELECT * FROM ranks WHERE name = ?",
            [$child]
        )->then(function (SqlSelectResult $result) use ($child, $parent) : void {
            if($result->count() <= 0) {
                return;
            }
            $childRank = $result->getRows()[0];
            $inheritance = unserialize($childRank['inheritance']);
            $key = array_search($parent, $inheritance);
            if($key !== false) {
                unset($inheritance[$key]);
            }
            Main::getInstance()->getDatabase()->getConnector()->executeGeneric(
                "UPDATE ranks SET inheritance = ? WHERE name = ?",
                [serialize($inheritance), $child]
            )->then(function () : void {
                foreach (Server::getInstance()->getOnlinePlayers() as $player) {
                    $this->update(Session::get($player));
                }
            });
        });
    }
}