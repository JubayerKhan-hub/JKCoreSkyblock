<?php

declare(strict_types=1);

namespace SkyblockPlugin;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class DepositCommand extends Command {
    private $plugin;

    public function __construct($plugin) {
        parent::__construct("deposit", "Deposit money", "/deposit <amount>");
        $this->plugin = $plugin;
        $this->setPermission("skyblockplugin.command.deposit");
    }

    public function execute(CommandSender $sender, string $label, array $args): bool {
        if ($sender instanceof Player) {
            if (isset($args[0]) && is_numeric($args[0])) {
                $amount = floatval($args[0]);
                $this->plugin->getEconomyManager()->deposit($sender, $amount);
                return true;
            } else {
                $sender->sendMessage("Usage: /deposit <amount>");
                return false;
            }
        } else {
            $sender->sendMessage("This command can only be used by players.");
            return false;
        }
    }
}
