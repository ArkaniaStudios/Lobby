<?php
declare(strict_types=1);
namespace arkania\network\servers;
interface ServersStatus {
    const ONLINE = 'En ligne';
    const OFFLINE = 'Hors ligne';
    const MAINTENANCE = 'Maintenance';
}