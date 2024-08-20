<?php

declare(strict_types=1);

namespace SkyblockPlugin;

use pocketmine\plugin\PluginBase;
use pocketmine\player\Player;
use SkyblockPlugin\khansb\ScoreBoardAPI;

class ScoreBoardManager {

    private array $config;
    private PluginBase $plugin;

    // Constructor requires a PluginBase instance
    public function __construct(PluginBase $plugin) {
        $this->plugin = $plugin;

        // Path to the config file in the plugin's data folder
        $configPath = $this->plugin->getDataFolder() . 'scoreboard_config.php';

        // Check if the config file exists, if not create it with default values
        if (!file_exists($configPath)) {
            $this->createDefaultConfig($configPath);
        }

        // Load the config file
        $this->config = include $configPath;
    }

    private function createDefaultConfig(string $path): void {
        $defaultConfig = [
            'scoreboard' => [
                'title' => 'SKYBLOCK',
                'lines' => [
                    1 => date("m/d/y"),
                    2 => "",
                    3 => "Early Autumn 3rd",
                    4 => "1:20pm",
                    5 => "",
                    6 => "§a@ Your Island",
                    7 => "",
                    8 => "Purse: 75",
                    9 => "",
                    10 => "Objective",
                    11 => "Craft a workbench",
                    12 => "",
                    13 => "www.hypixel.net",
                ]
            ]
        ];

        if (!is_dir($this->plugin->getDataFolder())) {
            mkdir($this->plugin->getDataFolder(), 0755, true);
        }

        file_put_contents($path, "<?php\n\nreturn " . var_export($defaultConfig, true) . ";\n");
    }

    public function createScoreboard(Player $player): void {
        $scoreboardConfig = $this->config['scoreboard'];
        ScoreBoardAPI::sendScore($player, $scoreboardConfig['title']);
        foreach ($scoreboardConfig['lines'] as $line => $text) {
            ScoreBoardAPI::setScoreLine($player, $line, $text);
        }
    }

    public function removeScoreboard(Player $player): void {
        ScoreBoardAPI::removeScore($player);
    }
}
