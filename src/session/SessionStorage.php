<?php
declare(strict_types=1);

namespace arkania\session;

use arkania\database\result\SqlSelectResult;
use arkania\Main;
use arkania\utils\Date;
use arkania\utils\promise\Promise;
use arkania\utils\promise\PromiseInterface;
use arkania\utils\Utils;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\promise\PromiseResolver;
use ReflectionException;

trait SessionStorage {

    protected Player $player;

    /** @var \WeakMap<Player, self> */
    private static \WeakMap $data;

    public static function get(Player $player) : Session {
        if(!isset(self::$data[$player])) {
            /** @var \WeakMap<Player, Session> $weakMap */
            $weakMap = new \WeakMap();
            self::$data = $weakMap;
        }
        return self::$data[$player] ?? new Session($player->getNetworkSession());
    }

    /**
     * @throws ReflectionException
     */

    public static function create(NetworkSession $networkSession) : PromiseInterface {
        $session = new Session($networkSession);
        return self::creationHook($session)->then(fn() => $session);
    }

    /**
     * @throws \ReflectionException
     */
    protected static function creationHook(Session $session) : PromiseInterface {
        Main::getInstance()->getDatabase()->getConnector()->executeGeneric(
            "CREATE TABLE IF NOT EXISTS cooldowns (player VARCHAR(16), type TEXT, end INT, PRIMARY KEY(player));"
        );
        Main::getInstance()->getDataBase()->getConnector()->executeGeneric(
            "CREATE TABLE IF NOT EXISTS players (name VARCHAR(16), ip TEXT, last_ip TEXT, first_login TEXT, last_login TEXT, rank TEXT, title TEXT, inventory TEXT, gamemode VARCHAR(16), enderchest TEXT, xplevel INT, xpProgress FLOAT, effects TEXT, settings TEXT, permissions TEXT, bans TEXT, kicks TEXT, mutes TEXT, warns TEXT, op INT, online TEXT, PRIMARY KEY(name))"
        );
        Main::getInstance()->getDataBase()->getConnector()->executeSelect(
            "SELECT * FROM players WHERE name = ?",
            [$session->getPlayer()->getName()]
        )->then(function(SqlSelectResult $result) use ($session) {
            if($result->count() <= 0) {
                Main::getInstance()->getDataBase()->getConnector()->executeGeneric(
                    "INSERT INTO players (name, ip, last_ip, first_login, last_login, rank, title, inventory, gamemode, enderchest, xplevel, xpProgress, effects, settings, permissions, bans, kicks, mutes, warns, op, online) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                    [
                        $session->getPlayer()->getName(),
                        $session->getPlayer()->getNetworkSession()->getIp(),
                        $session->getPlayer()->getNetworkSession()->getIp(),
                        Date::now(false),
                        Date::now(false),
                        'Joueur',
                        'Aucun',
                        serialize([]),
                        GameMode::SURVIVAL()->getEnglishName(),
                        serialize([]),
                        0,
                        0.0,
                        serialize([]),
                        serialize([]),
                        serialize([]),
                        serialize([]),
                        serialize([]),
                        serialize([]),
                        serialize([]),
                        0,
                        serialize([
                            'status' => '§aEn ligne',
                            'server' => Utils::getName()
                        ])
                    ]
                );
            }
        });
        return new Promise(fn(PromiseResolver $resolve) => $resolve->resolve($session));
    }

    public function __construct(private readonly NetworkSession $networkSession) {
        $this->player = $networkSession->getPlayer();
    }

    /**
     * @throws ReflectionException
     */

    protected static function save(Session $session) : PromiseInterface {
        return new Promise(fn(PromiseResolver $resolve) => $resolve->resolve($session));
    }

    public function getPlayer() : Player {
        return $this->player;
    }

    public function getName() : string {
        return $this->player->getName();
    }

}