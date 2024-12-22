<?php
declare(strict_types=1);

namespace arkania\commands\default;

use arkania\commands\CommandBase;
use arkania\form\class\CustomForm;
use arkania\form\class\SimpleForm;
use arkania\form\elements\buttons\SimpleButton;
use arkania\form\elements\customs\Label;
use arkania\form\elements\customs\Toggle;
use arkania\Main;
use arkania\session\permissions\DefaultsPermissions;
use arkania\session\ranks\Ranks;
use arkania\session\Session;
use arkania\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\permission\PermissionManager;
use pocketmine\player\Player;
use pocketmine\Server;

class PermissionsCommand extends CommandBase {

    public function __construct() {
        parent::__construct(
            'permissions',
            'Permet de gérer les permissions d\'un grade ou d\'un joueur.',
            '/permissions'
        );
        $this->setPermission(DefaultsPermissions::getPermission('permissions'));
    }

    public function getCommandParameters() : array {
        return [];
    }

    protected function onRun(CommandSender $sender, array $parameters) : void {
        if(!$sender instanceof Player) {
            $sender->sendMessage(Utils::getErrorPrefix() . "Vous devez être un joueur pour exécuter cette commande.");
            return;
        }

        $form = new SimpleForm(
            'Gestion des permissions',
            'Sélectionnez une action à effectuer.',
            [
                new SimpleButton('ranks', '§l§9» §rGérer les permissions des grades'),
                new SimpleButton('players', '§l§9» §rGérer les permissions des joueurs')
            ],
            function (Player $player, string $name) : void {
                if($name === 'ranks') {
                    Main::getInstance()->getRanksManager()->getRanksList()->then(function (array $result) use ($player) {
                        $ranks = [];
                        foreach ($result as $rank) {
                            $ranks[] = new SimpleButton($rank['rank']->getName(), '§l§9» §r' . $rank['rank']->getColor() . $rank['rank']->getName() . ' §f(' . $rank['rank']->getColor() . $rank['player_count'] . '§f)');
                        }
                        $form = new SimpleForm(
                            'Gestion des permissions',
                            'Sélectionnez un grade à modifier.',
                            $ranks,
                            function (Player $player, string $name) : void {
                                Main::getInstance()->getRanksManager()->getRank($name)->then(function (?Ranks $ranks) use ($player) : void {
                                    $buttons = [];
                                    foreach (PermissionManager::getInstance()->getPermissions() as $permission) {
                                        $buttons[] = new Toggle($permission->getName(), $permission->getName(), $ranks->hasPermission($permission->getName()));
                                    }
                                    $form = new CustomForm(
                                        'Gestion des permissions',
                                        $buttons,
                                        function (Player $player, array $data) use ($ranks) : void {
                                            foreach ($data as $permission => $value) {
                                                if (in_array($permission, $ranks->getPermissions()) && !$value) {
                                                    $ranks->removePermission($permission);
                                                } elseif (!in_array($permission, $ranks->getPermissions()) && $value) {
                                                    $ranks->addPermission($permission);
                                                }
                                            }
                                            Main::getInstance()->getRanksManager()->updateRank($ranks);
                                            foreach (Server::getInstance()->getOnlinePlayers() as $players) {
                                                $session = Session::get($players);
                                                Main::getInstance()->getRanksManager()->update($session);
                                            }
                                            $player->sendMessage(Utils::getPrefix() . 'Les permissions ont été mises à jour pour le grade ' . $ranks->getColor() . $ranks->getName() . '§f.');
                                        }
                                    );
                                    $player->sendForm($form);
                                });
                            }
                        );
                        $player->sendForm($form);
                    });
                }elseif($name === 'players') {
                    $players = [];
                    foreach (Server::getInstance()->getOnlinePlayers() as $player) {
                        $players[] = new SimpleButton($player->getName(), '§l§9» §r' . $player->getName());
                    }
                    $form = new SimpleForm(
                        'Gestion des permissions',
                        'Sélectionnez un joueur à modifier.',
                        $players,
                        function (Player $player, string $name) : void {
                            $p = Server::getInstance()->getPlayerExact($name);
                            if(!$p instanceof Player) {
                                $player->sendMessage(Utils::getErrorPrefix() . 'Le joueur §e' . $name . ' §cn\'est pas en ligne.');
                                return;
                            }
                            $session = Session::get($p);
                            $buttons = [];
                            $categories = [
                                "arkania" => [],
                                "default" => []
                            ];

                            foreach (PermissionManager::getInstance()->getPermissions() as $permission) {
                                $permissionName = $permission->getName();
                                if (str_contains($permissionName, "arkania")) {
                                    $categories["arkania"][] = $permissionName;
                                } else {
                                    $categories["default"][] = $permissionName;
                                }
                            }

                            if (!empty($categories["arkania"])) {
                                $buttons[] = new Label("Catégorie : Arkania");
                                foreach ($categories["arkania"] as $permissionName) {
                                    $buttons[] = new Toggle($permissionName, $permissionName, $player->hasPermission($permissionName));
                                }
                            }

                            if (!empty($categories["default"])) {
                                $buttons[] = new Label("Catégorie : Autres Permissions");
                                foreach ($categories["default"] as $permissionName) {
                                    $buttons[] = new Toggle($permissionName, $permissionName, $player->hasPermission($permissionName));
                                }
                            }
                            $form = new CustomForm(
                                'Gestion des permissions',
                                $buttons,
                                function (Player $player, array $data) use ($session) : void {
                                    $session->getPermission()->then(function (array $result) use ($player, $data, $session) : void {
                                        $permissions = $result;
                                        foreach ($data as $permission => $value) {
                                            if (in_array($permission, $permissions) && !$value) {
                                                $permissions = array_diff($permissions, [$permission]);
                                            } elseif (!in_array($permission, $permissions) && $value) {
                                                $permissions[] = $permission;
                                            }
                                        }
                                        Main::getInstance()->getDatabase()->getConnector()->executeGeneric(
                                            "UPDATE players SET permissions= ? WHERE name= ?",
                                            [serialize($permissions), $session->getName()]
                                        );
                                        Main::getInstance()->getRanksManager()->update($session);
                                    });
                                    $player->sendMessage(Utils::getPrefix() . 'Les permissions ont été mises à jour pour le joueur §e²²' . $session->getName() . '§f.');
                                }
                            );
                            $player->sendForm($form);
                        }
                    );
                    $player->sendForm($form);
                }
            }
        );
        $sender->sendForm($form);
    }
}