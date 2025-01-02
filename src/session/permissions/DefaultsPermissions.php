<?php
declare(strict_types=1);

namespace arkania\session\permissions;

enum DefaultsPermissions: string {
    case PERMISSION_DEFAULT = 'arkania.permission.default';
    case PERMISSION_STOP = 'arkania.permission.stop';
    case PERMISSION_SETRANK = 'arkania.permission.setrank';
    case PERMISSION_RANKSLIST = 'arkania.permission.rankslist';
    case PERMISSION_SPAWN = 'arkania.permission.spawn';
    case PERMISSION_MINAGE = 'arkania.permission.minage';
    case FACTION = 'arkania.permission.faction';
    case PERMISSION_MAINTENANCE = 'arkania.permission.maintenance';

    public static function getPermission(string $name): string {
        return match ($name) {
            'base'  => self::PERMISSION_DEFAULT->value,
            'stop' => self::PERMISSION_STOP->value,
            'setrank' => self::PERMISSION_SETRANK->value,
            'rankslist' => self::PERMISSION_RANKSLIST->value,
            'faction' => self::FACTION->value,
            'spawn' => self::PERMISSION_SPAWN->value,
            'minage' => self::PERMISSION_MINAGE->value,
            'maintenance' => self::PERMISSION_MAINTENANCE->value,
            default => throw new MissingPermissionException("Permission $name not found")
        };
    }
}