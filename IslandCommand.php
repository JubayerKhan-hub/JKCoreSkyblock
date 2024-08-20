<?php

declare(strict_types=1);

namespace SkyblockPlugin;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class IslandCommand extends Command {

    private Main $plugin;

    public function __construct(Main $plugin) {
        parent::__construct("is", "Manage your skyblock island.", "/is", []);
        $this->plugin = $plugin;
        $this->setPermission("skyblockplugin.command.is");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$sender instanceof Player) {
            $sender->sendMessage("This command can only be used by players.");
            return false;
        }

        // Check if arguments are provided and handle accordingly
        if (isset($args[0])) {
            switch ($args[0]) {
                case "create":
                    // Code to create an island
                    break;
                case "teleport":
                    // Code to teleport to an island
                    break;
                // Add more cases as needed
            }
        } else {
            $sender->sendMessage("Usage: /is <create|teleport|reset|delete>");
        }
        return true;
    }
}
