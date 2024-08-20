<?php

declare(strict_types=1);

namespace SkyblockPlugin;

use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class EconomyManager {

    private PluginBase $plugin;
    private Config $balanceConfig;

    public function __construct(PluginBase $plugin) {
        $this->plugin = $plugin;
        $this->balanceConfig = new Config($plugin->getDataFolder() . "balances.yml", Config::YAML);
    }

    public function getBalance(Player $player): float {
        return $this->balanceConfig->get($player->getName(), 0.0);
    }

    public function deposit(Player $player, float $amount): void {
        $balance = $this->getBalance($player);
        $newBalance = $balance + $amount;
        $this->balanceConfig->set($player->getName(), $newBalance);
        $this->balanceConfig->save();
        $player->sendMessage("Deposited $amount. New balance: $newBalance");
    }

    public function withdraw(Player $player, float $amount): void {
        $balance = $this->getBalance($player);
        if ($amount > $balance) {
            $player->sendMessage("Insufficient funds.");
            return;
        }
        $newBalance = $balance - $amount;
        $this->balanceConfig->set($player->getName(), $newBalance);
        $this->balanceConfig->save();
        $player->sendMessage("Withdrew $amount. New balance: $newBalance");
    }

    public function transfer(Player $from, Player $to, float $amount): void {
        $fromBalance = $this->getBalance($from);
        if ($amount > $fromBalance) {
            $from->sendMessage("Insufficient funds.");
            return;
        }

        $toBalance = $this->getBalance($to);
        $newFromBalance = $fromBalance - $amount;
        $newToBalance = $toBalance + $amount;

        $this->balanceConfig->set($from->getName(), $newFromBalance);
        $this->balanceConfig->set($to->getName(), $newToBalance);
        $this->balanceConfig->save();

        $from->sendMessage("Transferred $amount to " . $to->getName() . ". New balance: $newFromBalance");
        $to->sendMessage("Received $amount from " . $from->getName() . ". New balance: $newToBalance");
    }
}
