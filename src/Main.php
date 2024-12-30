<?php
declare(strict_types=1);

namespace arkania;

use arkania\commands\CommandCache;
use arkania\commands\default\FactionCommand;
use arkania\commands\default\HelpCommand;
use arkania\commands\default\InformationsCommand;
use arkania\commands\default\ListCommand;
use arkania\commands\default\LobbyCommand;
use arkania\commands\default\MinageCommand;
use arkania\commands\default\NavigatorCommand;
use arkania\commands\default\PlayerInfoCommand;
use arkania\commands\default\RanksListCommand;
use arkania\commands\default\RedemCommand;
use arkania\commands\default\SetRankCommand;
use arkania\commands\default\SpawnCommand;
use arkania\commands\default\StopCommand;
use arkania\commands\listener\CommandDataPacketListener;
use arkania\database\DataBaseManager;
use arkania\items\ItemsManager;
use arkania\form\listener\FormListener;
use arkania\gui\listener\MenuListener;
use arkania\listener\data\DataPacketSendListener;
use arkania\listener\player\PlayerBreakListener;
use arkania\listener\player\PlayerChatListener;
use arkania\listener\player\PlayerClickListener;
use arkania\listener\player\PlayerDamageListener;
use arkania\listener\player\PlayerExhaustListener;
use arkania\listener\player\PlayerInventoryListener;
use arkania\listener\player\PlayerJoinListener;
use arkania\listener\player\PlayerLoginListener;
use arkania\listener\player\PlayerPlaceListener;
use arkania\listener\player\PlayerQuitListener;
use arkania\network\servers\ServersManager;
use arkania\network\servers\ServersStatus;
use arkania\pack\ResourcePack;
use arkania\session\permissions\DefaultsPermissions;
use arkania\session\permissions\MissingPermissionException;
use arkania\session\permissions\PermissionsManager;
use arkania\session\ranks\RanksManager;
use arkania\session\Session;
use arkania\utils\Date;
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
    private ItemsManager $itemsManager;
    private ServersManager $serversManager;


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
        $this->itemsManager = new ItemsManager();
        $this->serversManager = new ServersManager();

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
            new PlayerDamageListener(),
            new DataPacketSendListener(),
            new MenuListener(),
            new FormListener(),
            new PlayerExhaustListener(),
            new PlayerClickListener()
        ];

        $server = $this->getServer()->getPluginManager();
        foreach ($listeners as $listener) {
            $server->registerEvents($listener, $this);
        }

        $resourcePack = new ResourcePack($this);
        $resourcePack->loadResourcePack();

        $commands = new CommandCache($this);
        $commands->unregisterCommands(
            'stop',
            'list',
            'transferserver',
            'checkperm',
            'me',
            'suicide',
            'clear',
            'msg',
            'about',
            'plugins',
            'genplugin',
            'defaultgamemode',
            'difficulty',
            'extractplugin',
            'handlers',
            'handlersbyplugin',
            'listperms',
            'makeplugin',
            'particle',
            'save-on',
            'save-off',
            'save-all',
            'say',
            'seed',
            'setworldspawn',
            'spawnpoint',
            'status',
            'timings',
            'xp'
        );
        $commands->registerCommands(
            new ListCommand(),
            new LobbyCommand(),
            new FactionCommand(),
            new RanksListCommand(),
            new SetRankCommand(),
            new SpawnCommand(),
            new StopCommand(),
            new RedemCommand(),
            new InformationsCommand(),
            new PlayerInfoCommand(),
            new NavigatorCommand(),
            new MinageCommand(),
            new HelpCommand()
        );
        new CommandDataPacketListener($this);

        $this->getServersManager()->updateServer(
            $this->getConfig()->get('name'),
            ['status' => ServersStatus::ONLINE, 'time' => Date::now(false)],
            0,
            $this->getServer()->getMaxPlayers()
        );
    }

    protected function onDisable() : void {
        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
            Session::get($player)->save();
            $player->transfer('lobby');
        }

        $this->getServersManager()->updateServer(
            $this->getConfig()->get('name'),
            ['status' => ServersStatus::OFFLINE, 'time' => Date::now(false)],
            0,
            $this->getServer()->getMaxPlayers()
        );
    }

    public function getDatabase() : DataBaseManager {
        return $this->database;
    }

    public function getRanksManager() : RanksManager {
        return $this->ranksManager;
    }

    public function getItemsManager() : ItemsManager {
        return $this->itemsManager;
    }

    public function getServersManager() : ServersManager {
        return $this->serversManager;
    }


}