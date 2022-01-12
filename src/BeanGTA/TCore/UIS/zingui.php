<?php

namespace BeanGTA\TCore\UIS;

use pocketmine\Player;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\utils\TextFormat;
use BeanGTA\TCore\Tasks\NapTheTask;


use BeanGTA\TCore\Main;

class zingui{

    private $plugin;

    private $menhgia = [10000 => "10.000 VND", 20000 => "20.000 VND", 50000 => "50.000 VND", 100000 => "100.000 VND", 200000 => "200.000 VND", 500000 => "500.000 VND", 1000000 => "1.000.000 VND"];

    public function __construct(Main $plugin){
        $this->plugin = $plugin;
    }

    public function init(Player $sender){					 					    
						$api = $this->plugin->getServer()->getPluginManager()->getPlugin("FormAPI");
						$form = $api->createCustomForm(function (Player $sender, array $data){
						$result = $data[0];
            if($result != null){
							
            $mang = "4";
            $ten = "Zing";
              $data = [
                  "merchant" => 'Mã ID',
                  "api_email" => 'Email',
                  "securecode" => 'MẬt Khẩu',
            "mang" => $mang,
            "ten" => $ten,
            "seri" => $data[0],
            "sopin" => $data[1],
            "card_value" => $data[2],
        ]; 
		$this->plugin->getServer()->getAsyncPool()->submitTask(new NapTheTask($data, $sender));
            }
        });
        $form->setTitle("§l§6Viettel");
        $form->addInput("§l§dNhập Số Seri:");
        $form->addInput("§l§dNhập Mã Pin:");
        $form->addDropdown("§l§dNhập Mệnh Giá:", array_values($this->menhgia));
        $form->addLabel("§l§dLưu Ý: §c§lSai Mệnh Giá Nạp Sẽ Mất Thẻ");
        $form->addLabel("§l§dẤn Nút Dưới Để Đồng Ý Nạp Nhé");
        $form->sendToPlayer($sender);
}
}
