<?php
declare(strict_types=1);
namespace arkania\network\servers;
interface ServersStatus {
    const ONLINE = 'online';
    const OFFLINE = 'offline';
    const MAINTENANCE = 'maintenance';
}