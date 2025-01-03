<?php
declare(strict_types=1);

namespace arkania\session\ranks;

class Ranks {

    private string $name;
    private int $position;
    private string $format;
    private string $discord_format;
    private string $nametag;
    private array $permissions;
    private array $inheritance;
    private string $color;
    private string $prefix;

    public function __construct(
        string $name,
        int $position,
        string $format,
        string $discord_format,
        string $nametag,
        array $permissions,
        array $inheritance,
        string $color,
        string $prefix
    ) {
        $this->name = $name;
        $this->position = $position;
        $this->format = $format;
        $this->discord_format = $discord_format;
        $this->nametag = $nametag;
        $this->permissions = $permissions;
        $this->inheritance = $inheritance;
        $this->color = $color;
        $this->prefix = $prefix;
    }

    /**
     * @return string
     */
    public function getName() : string {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getColor() : string {
        return $this->color;
    }

    /**
     * @return string
     */
    public function getDiscordFormat() : string {
        return $this->discord_format;
    }

    /**
     * @return string
     */
    public function getFormat() : string {
        return $this->format;
    }

    /**
     * @return array
     */
    public function getInheritance() : array {
        return $this->inheritance;
    }

    /**
     * @return string
     */
    public function getNametag() : string {
        return $this->nametag;
    }

    /**
     * @return array
     */
    public function getPermissions() : array {
        return $this->permissions;
    }

    /**
     * @return int
     */
    public function getPosition() : int {
        return $this->position;
    }

    /**
     * @return string
     */
    public function getPrefix() : string {
        return $this->prefix;
    }

}