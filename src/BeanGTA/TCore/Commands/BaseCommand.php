<?php

namespace BeanGTA\TCore\Commands;

use pocketmine\command\Command;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\utils\TextFormat;
use BeanGTA\TCore\Main;
use pocketmine\plugin\Plugin;

abstract class BaseCommand extends Command implements PluginIdentifiableCommand
{
    /** @var Main */
    private $plugin;
    /** @var null|string */
    private $consoleUsageMessage = null;

    /**
     * @param string $name
     * @param string $description
     * @param null|string $usageMessage
     * @param bool|null|string $consoleUsageMessage
     * @param array $aliases
     */
    public function __construct($name, $description = "", $usageMessage = null, $consoleUsageMessage = null, array $aliases = [])
    {
        parent::__construct($name, $description, $usageMessage, $aliases);
        $this->plugin = Main::getInstance();
        $this->consoleUsageMessage = $consoleUsageMessage;
    }

    /**
     * @return Main
     
    public final function getPlugin(): plugin
    {
        return $this->plugin;
    }
*/
	public final function getPlugin(): Plugin{
        return $this->plugin;
    }
    /**
     * @return string
     */
    public function getConsoleUsage()
    {
        if ($this->consoleUsageMessage === null) {
            $message = "Usage: " . str_replace("[player]", "<player>", $this->getUsage());
        } elseif (!$this->consoleUsageMessage) {
            $message = "[Error] Please run this command in-game";
        } else {
            $message = $this->consoleUsageMessage;
        }
        return TextFormat::RED . $message;
    }

    /**
     * @return string
     */
    public function getUsage(): string
    {
        return TextFormat::AQUA . parent::getUsage();
    }
}
