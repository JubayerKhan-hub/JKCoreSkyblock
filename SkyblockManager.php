<?php

declare(strict_types=1);

namespace SkyblockPlugin;

use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\world\WorldManager;
use jojoe77777\FormAPI\SimpleForm;

class SkyblockManager {
    private $plugin;

    public function __construct($plugin) {
        $this->plugin = $plugin;
    }

    public function showIslandManagementUI(Player $player): void {
        $form = new SimpleForm(function (Player $player, ?int $data) {
            if ($data === null) {
                return;
            }

            switch ($data) {
                case 0:
                    $worldName = $player->getName();
                    $this->createSkyblockWorld($worldName, $player);
                    break;
                case 1:
                    $worldName = $player->getName();
                    $this->teleportToIsland($player, $worldName);
                    break;
                case 2:
                    $worldName = $player->getName();
                    $this->resetSkyblockWorld($player, $worldName);
                    break;
                case 3:
                    $worldName = $player->getName();
                    $this->deleteSkyblockWorld($player, $worldName);
                    break;
            }
        });

        $form->setTitle("Skyblock Management");
        $form->setContent("Choose an option:");
        $form->addButton("Create Island");
        $form->addButton("Teleport to Island");
        $form->addButton("Reset Island");
        $form->addButton("Delete Island");
        $player->sendForm($form);
    }

    public function createSkyblockWorld(string $worldName, Player $player): void {
        $worldManager = $this->plugin->getServer()->getWorldManager();

        if ($worldManager->isWorldLoaded($worldName) || $worldManager->isWorldGenerated($worldName)) {
            $player->sendMessage("§4World $worldName already exists!");
            return;
        }

        $pluginDataPath = $this->plugin->getDataFolder();
        $sourceWorldPath = $pluginDataPath . "SkyblockTemplate";
        $destinationWorldPath = $this->plugin->getServer()->getDataPath() . "worlds/" . $worldName;

        if (!is_dir($sourceWorldPath)) {
            $player->sendMessage("§4Skyblock template world not found.");
            return;
        }

        $this->copyDirectory($sourceWorldPath, $destinationWorldPath);

        $this->plugin->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($worldName, $player): void {
            $worldManager = $this->plugin->getServer()->getWorldManager();
            if (!$worldManager->isWorldLoaded($worldName)) {
                $worldManager->loadWorld($worldName);
            }
            $world = $worldManager->getWorldByName($worldName);
            if ($world !== null) {
                $player->sendMessage("§2Skyblock island created!");
                $this->teleportToIsland($player, $worldName);
            } else {
                $player->sendMessage("§4Failed to load island '$worldName'.");
            }
        }), 20 * 5); // Delay to ensure world loading is complete
    }

    public function resetSkyblockWorld(Player $player, string $worldName): void {
        $worldManager = $this->plugin->getServer()->getWorldManager();

        if (!$worldManager->isWorldGenerated($worldName)) {
            $player->sendMessage("§4Skyblock Island does not exist!");
            return;
        }

        if ($worldManager->isWorldLoaded($worldName)) {
            $worldManager->unloadWorld($worldManager->getWorldByName($worldName), true);
        }

        $pluginDataPath = $this->plugin->getDataFolder();
        $sourceWorldPath = $pluginDataPath . "SkyblockTemplate";
        $destinationWorldPath = $this->plugin->getServer()->getDataPath() . "worlds/" . $worldName;

        $this->deleteDirectory($destinationWorldPath);
        $this->copyDirectory($sourceWorldPath, $destinationWorldPath);

        $this->plugin->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($worldName, $player): void {
            $worldManager = $this->plugin->getServer()->getWorldManager();
            if (!$worldManager->isWorldLoaded($worldName)) {
                $worldManager->loadWorld($worldName);
            }
            $world = $worldManager->getWorldByName($worldName);
            if ($world !== null) {
                $player->sendMessage("§dSkyblock world '$worldName' has been reset!");
                $this->teleportToIsland($player, $worldName);
            } else {
                $player->sendMessage("§4Failed to load island, '$worldName'.");
            }
        }), 20 * 5); // Delay to ensure world loading is complete
    }

    public function deleteSkyblockWorld(Player $player, string $worldName): void {
        $worldManager = $this->plugin->getServer()->getWorldManager();

        if (!$worldManager->isWorldGenerated($worldName)) {
            $player->sendMessage("§4Skyblock island does not exist!");
            return;
        }

        if ($worldManager->isWorldLoaded($worldName)) {
            $worldManager->unloadWorld($worldManager->getWorldByName($worldName), true);
        }

        $destinationWorldPath = $this->plugin->getServer()->getDataPath() . "worlds/" . $worldName;
        $this->deleteDirectory($destinationWorldPath);
        $player->sendMessage("§2Skyblock Island has been deleted!");
    }

    private function copyDirectory(string $source, string $destination): void {
        $dir = opendir($source);
        @mkdir($destination);

        while (($file = readdir($dir)) !== false) {
            if ($file !== '.' && $file !== '..') {
                $srcFilePath = $source . DIRECTORY_SEPARATOR . $file;
                $destFilePath = $destination . DIRECTORY_SEPARATOR . $file;

                if (is_dir($srcFilePath)) {
                    $this->copyDirectory($srcFilePath, $destFilePath);
                } else {
                    copy($srcFilePath, $destFilePath);
                }
            }
        }
        closedir($dir);
    }

    private function deleteDirectory(string $path): void {
        if (!is_dir($path)) {
            return;
        }
        $files = array_diff(scandir($path), ['.', '..']);
        foreach ($files as $file) {
            $filePath = $path . DIRECTORY_SEPARATOR . $file;
            if (is_dir($filePath)) {
                $this->deleteDirectory($filePath);
            } else {
                unlink($filePath);
            }
        }
        rmdir($path);
    }

    public function teleportToIsland(Player $player, string $worldName): void {
        $worldManager = $this->plugin->getServer()->getWorldManager();
        if (!$worldManager->isWorldLoaded($worldName)) {
            $worldManager->loadWorld($worldName);
        }
        $world = $worldManager->getWorldByName($worldName);
        if ($world !== null) {
            $player->teleport($world->getSafeSpawn());
            $player->sendMessage("§eTeleported to your Skyblock island!");
        } else {
            $player->sendMessage("§4Failed to teleport to your Skyblock island.");
        }
    }
}
