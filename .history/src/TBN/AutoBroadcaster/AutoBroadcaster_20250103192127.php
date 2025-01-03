<?php

declare(strict_types=1);

namespace TBN\AutoBroadcaster;

use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\Task;
use pocketmine\utils\Config;

class AutoBroadcaster extends PluginBase {

    private Config $config;
    protected array $messages;
    private int $interval;

    public function onEnable(): void {
        $this->saveDefaultConfig();
        $this->config = $this->getConfig();
        $this->messages = $this->config->get("messages", []);
        $this->interval = $this->config->get("interval", 300);

        if (empty($this->messages)) {
            $this->getLogger()->warning("No messages found in config!");
            return;
        }

        $this->getScheduler()->scheduleRepeatingTask(new class($this) extends Task {
            private AutoBroadcaster $plugin;
            private int $index = 0;

            public function __construct(AutoBroadcaster $plugin) {
                $this->plugin = $plugin;
            }

            public function onRun(): void {
                $messages = $this->plugin->messages;
                if (empty($messages)) return;

                $message = $messages[$this->index];
                $this->plugin->getServer()->broadcastMessage($message);

                $this->index = ($this->index + 1) % count($messages);
            }
        }, $this->interval * 20); 
    }
} 