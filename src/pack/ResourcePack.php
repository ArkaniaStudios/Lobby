<?php
declare(strict_types=1);

namespace arkania\pack;

use pocketmine\plugin\Plugin;
use pocketmine\resourcepacks\ResourcePackException;
use pocketmine\resourcepacks\ZippedResourcePack;
use Symfony\Component\Filesystem\Path;

final class ResourcePack {

    /** @var string[] */
    protected array $resourcePackPath;
    private Plugin $plugin;

    public function getPlugin() : Plugin {
        return $this->plugin;
    }

    public function __construct(Plugin $plugin) {
        $this->plugin = $plugin;
        $this->resourcePackPath = [];
        $this->registerResourcePack(
            'ArkaniaStudios',
            new ResourcesPackFile(
                Path::join($this->getPackPath(), 'ArkaniaStudios')
            )
        );
    }

    public function getPackPath() : string {
        return Path::join($this->getPlugin()->getServer()->getPluginPath(), $this->getPlugin()->getName(), 'resources', 'pack');
    }

    public function registerResourcePack(string $packName, ResourcesPackFile $packFile) : void {
        $this->resourcePackPath[$packName] = $packFile->getResourcePackPath();
        $packFile->savePackInData($packFile->getResourcePackPath());
        $packFile->zipPack(
            $packFile->getResourcePackPath(),
            $this->getPackPath(),
            $packName
        );
    }

    public function loadResourcePack() : void {
        $resourcePackManager = $this->getPlugin()->getServer()->getResourcePackManager();
        $resourcePacks = [];
        foreach ($this->resourcePackPath as $packName => $packPath) {
            $zipPath = $packPath . '.zip';
            if (!file_exists($zipPath)) {
                throw new ResourcePackException("File not found: " . $zipPath);
            }
            $resourcePacks[] = new ZippedResourcePack($zipPath);
        }
        $resourcePackManager->setResourcePacksRequired(true);
        $resourcePackManager->setResourceStack($resourcePacks);
    }

}