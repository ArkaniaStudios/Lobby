<?php
declare(strict_types=1);

namespace arkania\session\economy;

use arkania\database\result\SqlSelectResult;
use arkania\Main;
use arkania\utils\promise\PromiseInterface;

class EconomyManager {

    public function __construct() {
        Main::getInstance()->getDatabase()->getConnector()->executeGeneric(
            'CREATE TABLE IF NOT EXISTS economy (player VARCHAR(16), money FLOAT, logs TEXT);'
        );
    }

    public function getMoney(string $player) : PromiseInterface {
        return Main::getInstance()->getDatabase()->getConnector()->executeSelect(
            'SELECT money FROM economy WHERE player = ?',
            [$player]
        )->then(function (SqlSelectResult $result) : ?float {
            if($result->count() <= 0) {
                return null;
            }
            return $result->getRows()[0]['money'];
        });
    }

    public function getPlayerPositionInClassement(string $player) : PromiseInterface {
        return Main::getInstance()->getDatabase()->getConnector()->executeSelect(
            'SELECT COUNT(*) as position FROM economy WHERE money > (SELECT money FROM economy WHERE player = ?)',
            [$player]
        )->then(function (SqlSelectResult $result) : int {
            return $result->getRows()[0]['position'] + 1;
        });
    }

    public static function formatNumber(float $number) : string {
        if ($number >= 1000000000) {
            return number_format($number / 1000000000, 2) . 'B';
        } elseif ($number >= 1000000) {
            return number_format($number / 1000000, 2) . 'M';
        } elseif ($number >= 1000) {
            return number_format($number / 1000, 2) . 'K';
        } else {
            return (string)$number;
        }
    }
}