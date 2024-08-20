<?php

declare(strict_types=1);

namespace SkyblockPlugin;

use pocketmine\block\VanillaBlocks;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\player\Player;

class PlayerEventListener implements Listener {

    private Main $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function onPlayerMove(PlayerMoveEvent $event): void {
        $player = $event->getPlayer();
        $block = $player->getPosition()->getWorld()->getBlock($player->getPosition());

        // Check if the player is in the portal (you can customize this check based on your portal setup)
        if ($block->getTypeId() === VanillaBlocks::NETHER_PORTAL()->getTypeId()) {
            $this->teleportToHub($player);
        }
    }

    private function teleportToHub(Player $player): void {
        // Get the default world set in the server configuration
        $defaultWorld = $this->plugin->getServer()->getWorldManager()->getDefaultWorld();

        if ($defaultWorld !== null) {
            // Teleport the player to the spawn location of the default world
            $player->teleport($defaultWorld->getSpawnLocation());
            $player->sendMessage("§2 To Skyblock Hub");
        } else {
            $player->sendMessage("§2 default world is not set or loaded.");
        }
    }
}
