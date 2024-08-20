<?php

declare(strict_types=1);

namespace SkyblockPlugin;

class Utils {

    /**
     * Recursively copy files from one directory to another
     *
     * @param string $src
     * @param string $dst
     * @return void
     */
    public static function copyDirectory(string $src, string $dst): void {
        $dir = opendir($src);
        @mkdir($dst);

        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    self::copyDirectory($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }

        closedir($dir);
    }

    /**
     * Recursively delete a directory and its contents
     *
     * @param string $dir
     * @return void
     */
    public static function deleteDirectory(string $dir): void {
        if (!file_exists($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? self::deleteDirectory("$dir/$file") : unlink("$dir/$file");
        }

        rmdir($dir);
    }

    /**
     * Extract the Skyblock template
     *
     * @param \SkyblockPlugin\Main $plugin
     * @return void
     */
    public static function extractSkyblockTemplate(\SkyblockPlugin\Main $plugin): void {
        $pluginDataPath = $plugin->getDataFolder();
        $zipPath = $pluginDataPath . "SkyblockTemplate.zip";
        $extractPath = $pluginDataPath . "SkyblockTemplate";

        if (!file_exists($extractPath) && file_exists($zipPath)) {
            $zip = new \ZipArchive();
            if ($zip->open($zipPath) === true) {
                $zip->extractTo($extractPath);
                $zip->close();
            } else {
                $plugin->getLogger()->warning("Failed to open SkyblockTemplate.zip.");
            }
        }
    }
}
