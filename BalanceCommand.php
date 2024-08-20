<?php

declare(strict_types=1);

namespace SkyblockPlugin;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class BalanceCommand extends Command {
    private $plugin;

    public function __construct($plugin) {
        parent::__construct("balance", "Check your balance", "/balance");
        $this->plugin = $plugin;
        $this->setPermission("skyblockplugin.command.balance");
    }

    public function execute(CommandSender $sender, string $label, array $args): bool {
        if ($sender instanceof Player) {
            $balance = $this->plugin->getEconomyManager()->getBalance($sender);
            $sender->sendMessage("Your balance: $balance");
            return true;
        } else {
            $sender->sendMessage("This command can only be used by players.");
            return false;
        }
    }
}
