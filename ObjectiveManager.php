<?php

declare(strict_types=1);

namespace SkyblockPlugin;

use pocketmine\player\Player;

class ObjectiveManager {
    private Main $plugin;
    private array $objectives = [];

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
        $this->initializeObjectives();
    }

    private function initializeObjectives(): void {
        // Define your objectives here
        $this->objectives = [
            "Talk to Jerry",
            "Gather Wood",
            "Build a Crafting Table",
            "Expand Your Island"
            // Add more objectives as needed
        ];
    }

    public function advanceObjective(Player $player): void {
        // Load player's current objective index from storage (database, file, etc.)
        $currentObjectiveIndex = $this->getPlayerObjectiveIndex($player);

        // Check if there are more objectives to assign
        if ($currentObjectiveIndex < count($this->objectives)) {
            $nextObjective = $this->objectives[$currentObjectiveIndex];
            $player->sendMessage("§aNew Objective: §e$nextObjective");

            // Save player's new objective index
            $this->setPlayerObjectiveIndex($player, $currentObjectiveIndex + 1);
        } else {
            $player->sendMessage("§aYou have completed all objectives!");
        }
    }

    private function getPlayerObjectiveIndex(Player $player): int {
    $path = $this->plugin->getDataFolder() . "objectives/" . $player->getName() . ".json";
    if (file_exists($path)) {
        $data = json_decode(file_get_contents($path), true);
        return $data['objectiveIndex'] ?? 0;
    }
    return 0;
}

private function setPlayerObjectiveIndex(Player $player, int $index): void {
    $path = $this->plugin->getDataFolder() . "objectives/";
    if (!is_dir($path)) {
        mkdir($path, 0777, true);
    }
    $data = ['objectiveIndex' => $index];
    file_put_contents($path . $player->getName() . ".json", json_encode($data));
}
}
