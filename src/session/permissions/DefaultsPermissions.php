<?php
declare(strict_types=1);

namespace arkania\session\permissions;

enum DefaultsPermissions: string {
    case PERMISSION_DEFAULT = 'arkania.permission.default';
    case PERMISSION_STOP = 'arkania.permission.stop';
    case PERMISSION_ADDRANK = 'arkania.permission.addrank';
    case PERMISSION_REMOVERANK = 'arkania.permission.removerank';
    case PERMISSION_SETRANK = 'arkania.permission.setrank';
    case PERMISSION_EDITRANK = 'arkania.permission.editrank';
    case PERMISSION_RANKSLIST = 'arkania.permission.rankslist';
    case PERMISSION_PERMISSIONS = 'arkania.permission.permissions';
    case PERMISSION_LOBBY = 'arkania.permission.lobby';
    case PERMISSION_FACTION = 'arkania.permission.faction';
    case PERMISSION_SPAWN = 'arkania.permission.spawn';
    case PERMISSION_ADDINHERITANCE = 'arkania.permission.addinheritance';
    case PERMISSION_REMOVEINHERITANCE = 'arkania.permission.removeinheritance';

    public static function getPermission(string $name): string {
        return match ($name) {
            'base'  => self::PERMISSION_DEFAULT->value,
            'stop' => self::PERMISSION_STOP->value,
            'addrank' => self::PERMISSION_ADDRANK->value,
            'removerank' => self::PERMISSION_REMOVERANK->value,
            'setrank' => self::PERMISSION_SETRANK->value,
            'editrank' => self::PERMISSION_EDITRANK->value,
            'rankslist' => self::PERMISSION_RANKSLIST->value,
            'permissions' => self::PERMISSION_PERMISSIONS->value,
            'lobby' => self::PERMISSION_LOBBY->value,
            'faction' => self::PERMISSION_FACTION->value,
            'addinheritance' => self::PERMISSION_ADDINHERITANCE->value,
            'removeinheritance' => self::PERMISSION_REMOVEINHERITANCE->value,
            'spawn' => self::PERMISSION_SPAWN->value,
            default => throw new MissingPermissionException("Permission $name not found")
        };
    }
}