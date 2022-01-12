<?php
namespace BeanGTA\TCore\Commands;
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 10/26/2016
 * Time: 10:25 PM
 */

use pocketmine\command\CommandSender;
use pocketmine\Player;
use BeanGTA\TCore\Main;
use BeanGTA\TCore\Tasks\NapTheTask;
use jojoe77777\FormAPI;

class NapTheCommand extends BaseCommand
{
    /**
     * NapTheCommand constructor.
     */
    public function __construct()
    {
        parent::__construct(
            "napthe",
            "Plugins Nạp Thẻ của TCore",
            "Usage:/NapThe",
            false,
            []
        );
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $sender, $commandLabel, array $args)
    {
        if (!empty($args)) {
            return false;
        }
		
		if($sender instanceof Player) {					 					    
						$api = $this->getPlugin()->getServer()->getPluginManager()->getPlugin("FormAPI");
						if($api === null || $api->isDisabled()){
						
						}
						$form = $api->createSimpleForm(function (Player $sender, array $data){
						$result = $data[0];
						
						if($result === null){
						
						}
							switch($result){
				
								case 0:
								break;
				case 1:
				$this->getPlugin()->vinaui->init($sender);
				break;
				case 2:
				$this->getPlugin()->mbui->init($sender);
				break;
				case 3:
				$this->getPlugin()->vtui->init($sender);
				break;
				case 4:
				$this->getPlugin()->zingui->init($sender);
				break;
				case 5:
				$this->getPlugin()->gateui->init($sender);
				break;
			}
	  });
            $form->setTitle("§l§aHãy Chọn Nhà Mạng Mà Bạn Muốn Nạp");
            $form->setContent("§l§dNạp Thẻ Đa Dạng Cho Bạn Chọn Tùy Thích");
		
		$form->addButton("§l§cẤn Vào Để Thoát Ra");
		$form->addButton("§l§3VinaPhone");
		$form->addButton("§l§fMobiFone");
		$form->addButton("§l§6Viettel");
		$form->addButton("§l§eZing");
		$form->addButton("§l§cGate");
            $form->sendToPlayer($sender);
}}}
