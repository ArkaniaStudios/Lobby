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

    /**
     * @param string $color
     */
    public function setColor(string $color) : void {
        $this->color = $color;
    }

    /**
     * @param string $format
     */
    public function setFormat(string $format) : void {
        $this->format = $format;
    }

    /**
     * @param string $name
     */
    public function setName(string $name) : void {
        $this->name = $name;
    }

    /**
     * @param string $nametag
     */
    public function setNametag(string $nametag) : void {
        $this->nametag = $nametag;
    }

    /**
     * @param array $permissions
     */
    public function setPermissions(array $permissions) : void {
        $this->permissions = $permissions;
    }

    /**
     * @param int $position
     */
    public function setPosition(int $position) : void {
        $this->position = $position;
    }

    /**
     * @param string $prefix
     */
    public function setPrefix(string $prefix) : void {
        $this->prefix = $prefix;
    }

    public function hasPermission(string $permission) : bool {
        return in_array($permission, $this->permissions);
    }
    public function removePermission(int|string $permission) : void {
        $key = array_search($permission, $this->permissions);
        if ($key !== false) {
            unset($this->permissions[$key]);
        }
    }

    public function addPermission(int|string $permission) : void {
        $this->permissions[] = $permission;
    }

}