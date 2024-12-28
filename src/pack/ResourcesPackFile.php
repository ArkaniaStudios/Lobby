<?php
declare(strict_types=1);

namespace arkania\pack;

use arkania\Main;
use Exception;
use Symfony\Component\Filesystem\Path;
use ZipArchive;

final class ResourcesPackFile {

    const INCREMENT = false;
    private string $resourcePackPath;

    public function __construct(string $resourcePackPath) {
        $this->resourcePackPath = $resourcePackPath;
    }

    /**
     * @return string
     */
    public function getResourcePackPath() : string {
        return $this->resourcePackPath;
    }

    public function savePackInData(string $path, string $addPath = '') : void {
        $fullPath = Path::join($path, $addPath);
        if (!is_dir($fullPath)) {
            mkdir($fullPath, 0777, true);
        }
        $dir = opendir($fullPath);
        if ($dir === false) {
            return;
        }
        $main = Main::getInstance();
        while ($file = readdir($dir)) {
            if ($file !== '.' && $file !== '..') {
                if (is_dir(Path::join($path, $addPath, $file))) {
                    $this->savePackInData($path, Path::join($addPath, $file));
                } else {
                    $main->saveResource(Path::join($addPath, $file), true);
                }
            }
        }
        closedir($dir);
    }

    public function addToArchive(string $path, string $type, ZipArchive $zip, string $dataPath = '') : void {
        $dir = opendir(Path::join($path, $dataPath));
        if ($dir === false) {
            return;
        }
        while ($file = readdir($dir)) {
            if ($file !== '.' && $file !== '..') {
                if (is_dir(Path::join($path, $dataPath, $file))) {
                    $this->addToArchive($path, $type, $zip, Path::join($dataPath, $file));
                } else {
                    $zip->addFile(Path::join($path, $dataPath, $file), Path::join($type, $dataPath, $file));
                }
            }
        }
    }

    /**
     * @throws Exception
     */
    public function zipPack(string $path, string $zipPath, string $type) : void {
        $manifestPath = Path::join($path, 'manifest.json');
        if(self::INCREMENT) {
            $this->incrementVersionInManifest($manifestPath);
        }
        $zip = new ZipArchive();
        $zip->open(Path::join($zipPath, $type . '.zip'), ZipArchive::CREATE);
        $this->addToArchive($path, $type, $zip);
        $zip->close();
    }

    /**
     * @throws Exception
     */
    private function incrementVersionInManifest(string $manifestPath) : void {
        $manifestContent = file_get_contents($manifestPath);
        if ($manifestContent === false) {
            throw new Exception("Unable to read the manifest file.");
        }

        $manifest = json_decode($manifestContent, true);
        if (!isset($manifest['header']['version'])) {
            throw new Exception("The manifest does not contain a version header.");
        }

        $manifest['header']['version'][2] = $manifest['header']['version'][2] + 1;

        $newManifestContent = json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        if (file_put_contents($manifestPath, $newManifestContent) === false) {
            throw new Exception("Unable to write the updated manifest file.");
        }
    }

}