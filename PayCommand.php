<?php

declare(strict_types=1);

namespace SkyblockPlugin;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class PayCommand extends Command {
    private $plugin;

    public function __construct($plugin) {
        parent::__construct("pay", "Pay another player", "/pay <player> <amount>");
        $this->plugin = $plugin;
        $this->setPermission("skyblockplugin.command.pay");
    }

    public function execute(CommandSender $sender, string $label, array $args): bool {
        if ($sender instanceof Player) {
            if (isset($args[0]) && isset($args[1]) && is_numeric($args[1])) {
                $targetPlayerName = $args[0];
                $amount = floatval($args[1]);

                $targetPlayer = $this->plugin->getServer()->getPlayerExact($targetPlayerName);
                if ($targetPlayer instanceof Player) {
                    $this->plugin->getEconomyManager()->transfer($sender, $targetPlayer, $amount);
                    return true;
                } else {
                    $sender->sendMessage("Player not found.");
                    return false;
                }
            } else {
                $sender->sendMessage("Usage: /pay <player> <amount>");
                return false;
            }
        } else {
            $sender->sendMessage("This command can only be used by players.");
            return false;
        }
    }
}
