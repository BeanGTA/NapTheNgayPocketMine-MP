<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 10/27/2016
 * Time: 8:03 AM
 */

namespace BeanGTA\TCore;


use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;

class Events implements Listener
{
    /** @var Main */
    private $plugin;
    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * @return mixed
     */
    public function getPlugin()
    {
        return $this->plugin;
    }
    public function onsign(SignChangeEvent $event){
        $event->setLine(0,str_replace('@' ,'§', $event->getLine(0)));
        $event->setLine(1,str_replace('@' ,'§', $event->getLine(1)));
        $event->setLine(2,str_replace('@' ,'§', $event->getLine(2)));
        $event->setLine(3,str_replace('@' ,'§', $event->getLine(3)));
    }
    public function onjoin(PlayerJoinEvent $event){
        $event->setJoinMessage("");
    }
    public function onleave(PlayerQuitEvent $event){
        $event->setQuitMessage("");
    }
}
