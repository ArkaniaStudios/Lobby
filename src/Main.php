<?php
declare(strict_types=1);

namespace arkania;

use arkania\commands\CommandCache;
use arkania\commands\default\AddInheritanceCommand;
use arkania\commands\default\AddRankCommand;
use arkania\commands\default\EditRankCommand;
use arkania\commands\default\FactionCommand;
use arkania\commands\default\InformationsCommand;
use arkania\commands\default\ListCommand;
use arkania\commands\default\LobbyCommand;
use arkania\commands\default\PermissionsCommand;
use arkania\commands\default\PlayerInfoCommand;
use arkania\commands\default\RanksListCommand;
use arkania\commands\default\RedemCommand;
use arkania\commands\default\RemoveInheritanceCommand;
use arkania\commands\default\RemoveRankCommand;
use arkania\commands\default\SetRankCommand;
use arkania\commands\default\SpawnCommand;
use arkania\commands\default\StopCommand;
use arkania\commands\listener\CommandDataPacketListener;
use arkania\database\DataBaseManager;
use arkania\listener\player\PlayerBreakListener;
use arkania\listener\player\PlayerChatListener;
use arkania\listener\player\PlayerDamageListener;
use arkania\listener\player\PlayerInventoryListener;
use arkania\listener\player\PlayerJoinListener;
use arkania\listener\player\PlayerLoginListener;
use arkania\listener\player\PlayerPlaceListener;
use arkania\listener\player\PlayerQuitListener;
use arkania\session\permissions\DefaultsPermissions;
use arkania\session\permissions\MissingPermissionException;
use arkania\session\permissions\PermissionsManager;
use arkania\session\ranks\RanksManager;
use arkania\session\Session;
use Exception;
use pocketmine\permission\DefaultPermissionNames;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;

require_once  __DIR__.'/utils/promise/functions.php';
class Main extends PluginBase {
    use SingletonTrait;

    private DataBaseManager $database;
    private RanksManager $ranksManager;

    /**
     * @throws MissingPermissionException
     * @throws Exception
     */
    protected function onLoad() : void {
        self::setInstance($this);

        $this->saveResource('config.yml', true);

        $permissions = new PermissionsManager();
        $permissions->registerPermissionClass(DefaultsPermissions::cases());
        $permissions->registerPermission(DefaultsPermissions::PERMISSION_DEFAULT->value, 'Permet d\'avoir la permission de base', DefaultPermissionNames::GROUP_USER);

        $this->database = new DataBaseManager($this);
        $this->ranksManager = new RanksManager();

    }

    protected function onEnable() : void {

        $listeners = [
            new PlayerLoginListener(),
            new PlayerJoinListener(),
            new PlayerChatListener(),
            new PlayerQuitListener(),
            new PlayerInventoryListener(),
            new PlayerPlaceListener(),
            new PlayerBreakListener(),
            new PlayerDamageListener()
        ];

        $server = $this->getServer()->getPluginManager();
        foreach ($listeners as $listener) {
            $server->registerEvents($listener, $this);
        }

        $commands = new CommandCache($this);
        $commands->unregisterCommands(
            //'stop',
            'list'
        );
        $commands->registerCommands(
            new AddInheritanceCommand(),
            new AddRankCommand(),
            new EditRankCommand(),
            new ListCommand(),
            new LobbyCommand(),
            new FactionCommand(),
            new PermissionsCommand(),
            new RanksListCommand(),
            new RemoveInheritanceCommand(),
            new RemoveRankCommand(),
            new SetRankCommand(),
            new SpawnCommand(),
            new StopCommand(),
            new RedemCommand(),
            new InformationsCommand(),
            new PlayerInfoCommand()
        );
        new CommandDataPacketListener($this);
    }

    protected function onDisable() : void {
        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
            Session::get($player)->save();
            $player->transfer('lobby');
        }
    }

    public function getDatabase() : DataBaseManager {
        return $this->database;
    }

    public function getRanksManager() : RanksManager {
        return $this->ranksManager;
    }

}