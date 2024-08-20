<?php

declare(strict_types=1);

namespace SkyblockPlugin;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use ZipArchive;

class Main extends PluginBase implements Listener {
    private SkyblockManager $skyblockManager;
    private ScoreBoardManager $scoreBoardManager;
    private CollectionManager $collectionManager;
    private EconomyManager $economyManager;

public function onEnable(): void {
    $this->getLogger()->info("SkyblockPlugin has been enabled!");

    // Create configuration and text file
    $this->saveDefaultConfig();
    $this->saveResource("example.txt");

    // Extract SkyblockTemplate.zip
    $this->extractSkyblockTemplate();

    // Initialize managers
    $this->skyblockManager = new SkyblockManager($this);
    $this->scoreBoardManager = new ScoreBoardManager($this);  // Pass the plugin instance here
    $this->collectionManager = new CollectionManager($this);
    $this->economyManager = new EconomyManager($this);

    // Register events
    $this->getServer()->getPluginManager()->registerEvents($this, $this);
    $this->getServer()->getPluginManager()->registerEvents(new PlayerEventListener($this), $this);

    // Register commands
    $this->getServer()->getCommandMap()->register("quest", new QuestCommand($this));
    $this->getServer()->getCommandMap()->register("balance", new BalanceCommand($this));
    $this->getServer()->getCommandMap()->register("pay", new PayCommand($this));
    $this->getServer()->getCommandMap()->register("deposit", new DepositCommand($this));
    $this->getServer()->getCommandMap()->register("withdraw", new WithdrawCommand($this));
    $this->getServer()->getCommandMap()->register("is", new IslandCommand($this));
}


    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        if ($sender instanceof Player) {
            if ($command->getName() === "is") {
                $this->skyblockManager->showIslandManagementUI($sender);
                return true;
            }
        } else {
            $sender->sendMessage("This command can only be used by players.");
            return false;
        }
        return false;
    }

    private function extractSkyblockTemplate(): void {
        $pluginDataPath = $this->getDataFolder();
        $extractPath = $pluginDataPath . "SkyblockTemplate";

        $this->getLogger()->info("Looking for SkyblockTemplate.zip in the resources folder.");

        $resourceStream = $this->getResource("SkyblockTemplate.zip");
        if ($resourceStream === null) {
            $this->getLogger()->warning("SkyblockTemplate.zip not found in the resources folder.");
            return;
        }

        $tempZipPath = $pluginDataPath . "SkyblockTemplate_temp.zip";
        file_put_contents($tempZipPath, $resourceStream);

        $zip = new ZipArchive();
        if ($zip->open($tempZipPath) === TRUE) {
            $zip->extractTo($extractPath);
            $zip->close();
            $this->getLogger()->info("SkyblockTemplate.zip extracted successfully.");
        } else {
            $this->getLogger()->error("Failed to open SkyblockTemplate.zip.");
        }

        // Clean up
        unlink($tempZipPath);
    }

    public function showQuestUI(Player $player): void {
        // Dummy implementation for demonstration
        $player->sendMessage("Quest UI coming soon!");
    }

    public function onPlayerJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();
        $this->scoreBoardManager->createScoreboard($player);
    }

    public function getSkyblockManager(): SkyblockManager {
        return $this->skyblockManager;
    }

    public function getScoreBoardManager(): ScoreBoardManager {
        return $this->scoreBoardManager;
    }

    public function getCollectionManager(): CollectionManager {
        return $this->collectionManager;
    }

    public function getEconomyManager(): EconomyManager {
        return $this->economyManager;
    }
}
