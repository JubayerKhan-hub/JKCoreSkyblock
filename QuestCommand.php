<?php

declare(strict_types=1);

namespace SkyblockPlugin;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class QuestCommand extends Command {
    private $plugin;

    public function __construct($plugin) {
        parent::__construct("quest", "Manage and view quests", "/quest");
        $this->plugin = $plugin;
        $this->setPermission("skyblockplugin.command.quest");
    }

    public function execute(CommandSender $sender, string $label, array $args): bool {
        if ($sender instanceof Player) {
            $this->plugin->showQuestUI($sender);
            return true;
        } else {
            $sender->sendMessage("This command can only be used by players.");
            return false;
        }
    }
}
