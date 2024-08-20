<?php

declare(strict_types=1);

namespace SkyblockPlugin;

use pocketmine\event\Listener;
use pocketmine\event\entity\EntityItemPickupEvent;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class CollectionManager implements Listener {

    private PluginBase $plugin;
    private array $playerCollections = [];

    public function __construct(PluginBase $plugin) {
        $this->plugin = $plugin;
        $this->plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
    }

    public function onEntityItemPickup(EntityItemPickupEvent $event): void {
        $entity = $event->getEntity();

        if (!$entity instanceof Player) {
            return;
        }

        $item = $event->getItem();
        $itemName = $item->getName();

        if (!$this->hasPlayerCollectedItem($entity, $itemName)) {
            $this->unlockCollection($entity, $itemName);
        }
    }

    private function hasPlayerCollectedItem(Player $player, string $itemName): bool {
        $playerName = $player->getName();
        return isset($this->playerCollections[$playerName]) && in_array($itemName, $this->playerCollections[$playerName]);
    }

    private function unlockCollection(Player $player, string $itemName): void {
        $playerName = $player->getName();
        $this->playerCollections[$playerName][] = $itemName;
        $message = TextFormat::GOLD . "COLLECTION UNLOCKED: " . TextFormat::YELLOW . $itemName;
        $player->sendMessage($message);
    }
}
