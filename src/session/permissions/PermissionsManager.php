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

namespace arkania\session\permissions;

use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\Permission;
use UnitEnum;

final class PermissionsManager {
    private RegistryPermissionCache $registryPermissionCache;
    public function getRegistryPermissionCache(): RegistryPermissionCache {
        return $this->registryPermissionCache ?? $this->registryPermissionCache = new RegistryPermissionCache();
    }

    public function registerPermission(Permission|string $permission, string $name = null, string $defaultGroup = DefaultPermissions::ROOT_OPERATOR): void {
        $consoleRoot = DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT_CONSOLE));
        $operatorRoot = DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT_OPERATOR, '', [$consoleRoot]));
        $everyone = DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT_USER, '', [$operatorRoot]));
        $permissionsCache = $this->getRegistryPermissionCache();

        if (in_array($name, $permissionsCache->getPermissions(), true)) {
            return;
        }

        if (is_string($permission)) {
            if ($name === null) {
                $name = str_replace('.', '_', $permission);
            }
            $permission = new Permission($permission);
        }

        $permissionsCache->addPermission($name, $permission);

        switch ($defaultGroup) {
            case DefaultPermissions::ROOT_USER:
                DefaultPermissions::registerPermission($permission, [$everyone]);
                break;
            case DefaultPermissions::ROOT_OPERATOR:
                DefaultPermissions::registerPermission($permission, [$operatorRoot]);
                break;
            case DefaultPermissions::ROOT_CONSOLE:
                DefaultPermissions::registerPermission($permission, [$consoleRoot]);
                break;
            default:
                throw new MissingPermissionException("Invalid default group: $defaultGroup");
        }
    }

    /**
     * @param UnitEnum[] $enums
     */
    public function registerPermissionClass(array $enums): void {
        foreach ($enums as $enum) {
            if ($enum instanceof UnitEnum) {
                $this->registerPermission($enum->value, $enum->name);
            }
        }
    }

    /**
     * @return Permission[]
     */
    public function getPermissions(): array {
        return $this->getRegistryPermissionCache()->getPermissions();
    }

    public function getPermission(string $name): Permission {
        foreach ($this->getPermissions() as $permission) {
            if ($permission->getName() === $name) {
                return $permission;
            }
        }
        throw new MissingPermissionException("Permission $name not found");
    }

}