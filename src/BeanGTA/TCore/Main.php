<?php

namespace BeanGTA\TCore;

use onebone\economyapi\EconomyAPI;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use BeanGTA\TCore\Commands\NapTheCommand;
use TCoin\TCoin;
use pocketmine\utils\TextFormat;
use BeanGTA\TCore\UIS\vinaui;
use BeanGTA\TCore\UIS\mbui;
use BeanGTA\TCore\UIS\vtui;
use BeanGTA\TCore\UIS\zingui;
use BeanGTA\TCore\UIS\gateui;

class Main extends PluginBase
{

	public $formss;
    /** @var  Config */
    public $baokimapi;
    /** @var  Config */
    public $popup;
    private static $instance = null;
    /** @var  EconomyAPI */
    public $economy;
    /** @var  TCoin */
    public $TCoin;

    public static function getInstance()
    {
        return self::$instance;
    }

    public function onLoad()
    {
        self::$instance = $this;
    }

    public function onEnable()
    {
		$this->RegisterShortcut();
        $this->getLogger()->notice("TCore đang kích hoạt ");
        $this->getServer()->getCommandMap()->registerAll("Main",
            [new NapTheCommand()]
        );
        //kiểm tra version config
        @mkdir($this->getDataFolder());
		$this->getLogger()->info(TextFormat::AQUA . 'TBuy loading..');
        $this->vip = new Config($this->getDataFolder() . 'vip.yml', Config::YAML);
        $this->tbuy = new Config($this->getDataFolder()."tbuy.yml",Config::YAML,[
			"onjoinex" => "§8►§6Vip §acủa bạn đã hết hạn§8, §aXin hãy mua thêm để ủng hộ §dServer§8!",
			"onjoinre" => "§8►§6Vip §acủa bạn còn: §a {days} ngày §8!",
            "vipsucess" => "§8►§b{player} §ađã mua thành công gói §f{type}",
			"viptotal" => "§8►Tổng số ngày VIP §8.§a còn lại §8:§f {total}§8 Ngày!",
            "xusucess" => "§8►§b{player} §ađổi thành công §f{coin} §6Coin §athành §f{money} §exu",
            "1cointoxu" => 2000,
            "muaxu" => "§8►§aHãy nhập số coin để đổi ra xu§8.",
            "muavip" => "§8►§aHãy chọn gói §6Vip §acần mua§8.§d/giavip §ađể xem các gói vipd§8.",
            "number" => "§8►§6Chỉ được nhập số!",
            "nocoin" => "§8►§aBạn không có đủ coin để mua §eXu §8, §avui lòng §d/napthe§8!",
			"vipfail" => "§8►§aBạn không có đủ coin để mua §6Vip§8, §avui lòng §d/napthe§8!",
			"morethanzero" => "§8►§aBạn phải nhập số coin lớn hơn 0!",
            "pricevip1" => 650,
            "pricevip2" => 1225,
            "pricevip3" => 2300,
            "pricevip4" => 4300,
            "pricevip5" => 5800,
            "pricevip6" => 7500,
            "VIP1" => 7,
            "VIP2" => 21,
            "VIP3" => 105,
            "VIP4" => 210,
            "VIP5" => 420,
            "VIP6" => 630
        ]);

        $this->getLogger()->warning("Kiểm tra plugin cần thiết cho TCore");
        $this->getLogger()->notice("Kiểm tra plugins TCoin");
        if (!$this->getServer()->getPluginManager()->getPlugin("TCoin")) {
            $this->getLogger()->warning("Không tìm thấy plugin TCoin .");
            $this->getServer()->getPluginManager()->disablePlugin($this->getServer()->getPluginManager()->getPlugin("TCore"));
            $this->getLogger()->notice("Shutting Down...");
            $this->getServer()->shutdown();
        }

        //check economy
        $this->getLogger()->notice("Kiểm tra plugins EconomyAPI.");
        if (!$this->getServer()->getPluginManager()->getPlugin("EconomyAPI")) {
            $this->getLogger()->warning("Không tìm thấy plugin EConomyAPI");
            $this->getServer()->getPluginManager()->disablePlugin($this->getServer()->getPluginManager()->getPlugin("TCore"));
            $this->getLogger()->notice("Shutting Down...");
            $this->getServer()->shutdown();
        }
		$this->formss = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $this->economy = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
        //check TCoin
        $this->getLogger()->notice("Kiểm tra plugins TCoin.");
        if (!$this->getServer()->getPluginManager()->getPlugin("TCoin")) {
            $this->getLogger()->warning("Không tìm thấy plugin TCoin");
            $this->getServer()->getPluginManager()->disablePlugin($this->getServer()->getPluginManager()->getPlugin("TCore"));
            $this->getLogger()->notice("Shutting Down...");
            $this->getServer()->shutdown();
        }
        $this->TCoin = $this->getServer()->getPluginManager()->getPlugin("TCoin");
        $this->getLogger()->notice("Load config Nạp Thẻ Ngay !");
        $this->baokimapi = new Config($this->getDataFolder() . "NapTheNgay.yml", Config::YAML, [
            ###mess
            'diendayduthongtin' => '§c¸.•♥•.¸¸.•♥•[§eDonate§c]•♥•.¸¸.•♥•.¸{LINE}§aHãy nhập đầy đủ thông tin và thử lại.{LINE}§0►§aDùng Lệnh§8: §d/napthe §b[mobi|vina|gate|vietel] §3[Số Seri] §3[Mã Cào]{LINE}§c============§9[̲̅§1(̲$)§9̲̅]§c============',
            'boardcast' => '§0►§a§lNgười chơi §8:§b {PLAYER} §ađã nạp §e{MENHGIA} §9{LOAITHE} §avào server§0•\'',
            'napthedung' => '§0►§aTài Khoản§8:§b {PLAYER}{LINE}§0►§aLoại thẻ§8:§9 {LOAITHE} {LINE}§0►§aMệnh giá §8:§e {MENHGIA}',
            'napthesai' => '§0►§cTài Khoản:§b {PLAYER}{LINE}§0►§cMã Cào§8: §e{PIN}§0►Seri§8: §9{SERI}{LINE}§0►§eLỗi§8: §a{ERROL}',
            'mangloi' => '§e==========•>[§cLỗi§e]§e<•========== {LINE} §0►§cTên nhà mạng phải là§8: §b[vina|viettel]§0•',
            ###rewards khi nạp đúng
            '10000' => '700',
            '20000' => '1400',
            '50000' => '3500',
            '100000' => '7000',
            '200000' => '14000',
            '500000' => '35000',
            '1000000' => '100000',
        ]);
		 $this->economy = EconomyAPI::getInstance();
        $this->tcoin = $this->getServer()->getPluginManager()->getPlugin("TCoin");
		$this->saveDefaultConfig();
    }

	public function RegisterShortcut(){
		$this->vtui = new vtui($this);
		$this->mbui = new mbui($this);
		$this->vinaui = new vinaui($this);
		$this->zingui = new zingui($this);
		$this->gateui = new gateui($this);
	}
	
	public function onCommand(CommandSender $sender, Command $command, $label, array $args): bool
    {
        $cmd = strtolower($command->getName());
        switch ($cmd) {
			case "muavip": {
				$sender->sendMessage("");
                $sender->sendMessage("§c============§9[§bInfo§9]§c============");
                $sender->sendMessage("Sử dụng /buyvip để mua vip");
                $sender->sendMessage("Sử dụng /napthe để mua thêm Coin");
                $sender->sendMessage("Sử dụng /giavip để xem thêm thông tin");
                $sender->sendMessage("§c============§9[§fEnd§9]§c============");
               # $sender->sendMessage("");
            }
                return true;
			case "buyvip":		
$needcoin = $this->tbuy->get("pricevip1");
$needcoin1 = $this->tbuy->get("pricevip2");
$needcoin2 = $this->tbuy->get("pricevip3");
$needcoin3 = $this->tbuy->get("pricevip4");
$needcoin4 = $this->tbuy->get("pricevip5");
$needcoin5 = $this->tbuy->get("pricevip6");	
				if($sender instanceof Player) {					 					    
						$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
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
	         $p = $sender->getPlayer();
$name = $sender->getName();
$this->formss->getServer()->dispatchCommand($sender,"muavips vip1");
									break;
								case 2:
         $p = $sender->getPlayer();
$name = $sender->getName();
$this->formss->getServer()->dispatchCommand($sender,"muavips vip2");
									break;
								case 3:
         $p = $sender->getPlayer();
$name = $sender->getName();
$this->formss->getServer()->dispatchCommand($sender,"muavips vip3");
									break;
								case 4:
         $p = $sender->getPlayer();
$name = $sender->getName();
$this->formss->getServer()->dispatchCommand($sender,"muavips vip4");
									break;
								case 5:
         $p = $sender->getPlayer();
$name = $sender->getName();
$this->formss->getServer()->dispatchCommand($sender,"muavips vip5");
									break;
									case 6:
         $p = $sender->getPlayer();
$name = $sender->getName();
$this->formss->getServer()->dispatchCommand($sender,"muavips vip6");
									break;
							}
});
					$form->setTitle("§7§l꧁༺§bVIP§7༻꧂");
					$form->setContent("§6♦§l§eVip §bđược tính bằng ngày và sẽ cộng dồn ngày và tự động nâng §eVip §bkhi bằng số ngày của hạng §eVip §bcao hơn");
					$form->addButton("§l§cẤn Vào Để Thoát Ra");
				    $form->addButton("§l§9♠§3βг๏ภzє§9♠ §r§7-§a $needcoin §6Coin§7+§b7 §aNgày", 1, "https://i.loli.net/2019/02/28/5c773f4d3e884.png");
					$form->addButton("§l§d♣§9Ŝℓȋνєř§d♣ §r§7-§a $needcoin1 §6Coin§7+§b21 §aNgày", 1, "https://i.loli.net/2019/02/28/5c773f4d3e900.png");
					$form->addButton("§l§6♦§eĞ๏ℓď§6♦ §r§7-§a $needcoin2 §7- §6Coin§7+§b105 §aNgày", 1, "https://i.loli.net/2019/02/28/5c773f4d44e6c.png");
					$form->addButton("§l§6♥§bƤℓคtเηɥɱ§6♥ §r§7-§a $needcoin3 §7- §6Coin§7+§b210 §aNgày", 1, "https://i.loli.net/2019/02/28/5c773f4d473af.png");
					$form->addButton("§l§e☬§4Ď§cȋ§5ą§d๓§6๏§eη§fȡ§e☬ §r §7-§a $needcoin4 §7- §6Coin§7+§b420 §aNgày", 1, "https://i.loli.net/2019/02/28/5c773f4d482e7.png");
					$form->addButton("§l§e♚§1Ĕ§9๓§2є§aг§3ค§bℓ§fȡ§e♚ §r§7-§a $needcoin5 §7- §6Coin§7+§b630 §aNgày", 1, "https://i.loli.net/2019/02/28/5c773f4d4e30a.png");			
					$form->sendToPlayer($sender);
				}
				else{
					$sender->sendMessage("§l§cerror");
					return true;
				}
			break;
			case "muaxu":
			$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
						$form = $api->createCustomForm(function (Player $sender, array $data){
						$result = $data[0];
            if($result != null){
                if (!isset($data[0])){
                    $sender->sendMessage($this->tbuy->get("muaxu"));
                    return true;
                }
                if (!is_numeric($data[0])){
					$sender->sendMessage($this->tbuy->get("number"));
                    return true;
                }
                if ($data[0] <= 0){
				 $sender->sendMessage($this->tbuy->get("morethanzero"));
                    return true;
                }
				
                $coin = $this->tcoin->getMoney($sender->getName());
                if ($coin >= $data[0]){
                    $money = $data[0] * $this->tbuy->get("1cointoxu");
                    $this->tcoin->grantMoney($sender->getName(),-$data[0],true);
                    $this->getEconomy()->addMoney($sender->getName(),$money,true);
                    $this->getServer()->broadcastMessage(str_replace(
                        array(
                            "{coin}",
                            "{money}",
                            "{player}",
                        ),
                        array(
                            $data[0],
                            $money,
                            $sender->getName(),
                        ),
                        $this->tbuy->get("xusucess")
                    ));
                    return true;
                }else{
                    $sender->sendMessage($this->tbuy->get("nocoin"));
                    return true;
                }
				            }
        });
        $form->setTitle("§l§eĐổi Coin Ra Xu");
        $form->addInput("§l§aSố Xu Cần Đổi:");
		$form->addLabel("§l1 Coin bằng 2000 Xu");
        $form->sendToPlayer($sender);
			break;
            case "muavips":
                if (!isset($args[0])){
                    $sender->sendMessage($this->tbuy->get("muavip"));
                    return true;
                }
				#
				$coin = $this->tcoin->getMoney($sender->getName());
				switch($args[0]){
					case "vip1":
					$needcoin = $this->tbuy->get("pricevip1");
					$days = $this->tbuy->get("VIP1");
					   if ($coin >= $needcoin){
							$this->tcoin->grantMoney($sender->getName(),-$needcoin,true);
							$player = $this->getServer()->getPlayer($sender->getName());
							$this->savevip($player,$days);
							$this->getServer()->broadcastMessage(str_replace(
								array(
									"{type}",
									"{player}",
								),
								array(
									"VIP1",
									$sender->getName(),
								),
								$this->tbuy->get("vipsucess")
							));
						}else{
							$sender->sendMessage($this->tbuy->get("vipfail"));
						}
					break;
					case "vip2":
					$needcoin = $this->tbuy->get("pricevip2");
					$days = $this->tbuy->get("VIP2");
					   if ($coin >= $needcoin){
							$this->tcoin->grantMoney($sender->getName(),-$needcoin,true);
							$player = $this->getServer()->getPlayer($sender->getName());
							$this->savevip($player,$days);
							$this->getServer()->broadcastMessage(str_replace(
								array(
									"{type}",
									"{player}",
								),
								array(
									"VIP2",
									$sender->getName(),
								),
								$this->tbuy->get("vipsucess")
							));
						}else{
							$sender->sendMessage($this->tbuy->get("vipfail"));
						}
					break;
					case "vip3":
					$needcoin = $this->tbuy->get("pricevip3");
					$days = $this->tbuy->get("VIP3");
					   if ($coin >= $needcoin){
							$this->tcoin->grantMoney($sender->getName(),-$needcoin,true);
							$player = $this->getServer()->getPlayer($sender->getName());
							$this->savevip($player,$days);
							$this->getServer()->broadcastMessage(str_replace(
								array(
									"{type}",
									"{player}",
								),
								array(
									"VIP3",
									$sender->getName(),
								),
								$this->tbuy->get("vipsucess")
							));
						}else{
							$sender->sendMessage($this->tbuy->get("vipfail"));
						}
					break;
					case "vip4":
					$needcoin = $this->tbuy->get("pricevip4");
					$days = $this->tbuy->get("VIP4");
					   if ($coin >= $needcoin){
							$this->tcoin->grantMoney($sender->getName(),-$needcoin,true);
							$player = $this->getServer()->getPlayer($sender->getName());
							$this->savevip($player,$days);
							$this->getServer()->broadcastMessage(str_replace(
								array(
									"{type}",
									"{player}",
								),
								array(
									"VIP4",
									$sender->getName(),
								),
								$this->tbuy->get("vipsucess")
							));
						}else{
							$sender->sendMessage($this->tbuy->get("vipfail"));
						}
					break;
					case "vip5":
					$needcoin = $this->tbuy->get("pricevip5");
					$days = $this->tbuy->get("VIP5");
					   if ($coin >= $needcoin){
							$this->tcoin->grantMoney($sender->getName(),-$needcoin,true);
							$player = $this->getServer()->getPlayer($sender->getName());
							$this->savevip($player,$days);
							$this->getServer()->broadcastMessage(str_replace(
								array(
									"{type}",
									"{player}",
								),
								array(
									"VIP5",
									$sender->getName(),
								),
								$this->tbuy->get("vipsucess")
							));
						}else{
							$sender->sendMessage($this->tbuy->get("vipfail"));
						}
					break;
					case "vip6":
					$needcoin = $this->tbuy->get("pricevip6");
					$days = $this->tbuy->get("VIP6");
					   if ($coin >= $needcoin){
							$this->tcoin->grantMoney($sender->getName(),-$needcoin,true);
							$player = $this->getServer()->getPlayer($sender->getName());
							$this->savevip($player,$days);
							$this->getServer()->broadcastMessage(str_replace(
								array(
									"{type}",
									"{player}",
								),
								array(
									"VIP6",
									$sender->getName(),
								),
								$this->tbuy->get("vipsucess")
							));
						}else{
							$sender->sendMessage($this->tbuy->get("vipfail"));
						}
					break;
            }
                return true;
        }
		return true;
    }
	
	
	 public function onPlayerJoin(PlayerJoinEvent $event){
        $t = $this->vip->getAll();
        $p = $event->getPlayer();
        $n = $p->getName();
        if(isset($t[$n])){
            $date1 = strtotime($t[$n]["date"]);
            $date2 = strtotime(date("y-m-d"));
            $date3 = ceil(($date2 - $date1)/86400);
            $date4 = ($t[$n]["day"]-$date3);
            if($date4 < 1){
                $p->sendMessage($this->tbuy->get("onjoinex"));
                $this->vip->remove($n);
                $this->vip->save();
                $this->getServer()->dispatchCommand(new ConsoleCommandSender(),'setrank '.$n.' Vip');
            }else{
				$p->sendMessage(str_replace(
                    array(
                        "{days}",
                    ),
                    array(
                        $date4,
                    ),
                    $this->tbuy->get("onjoinre")
                ));
			}
		}
    }
	
	/**
     * @return EconomyAPI
     */
    public function getEconomy(): EconomyAPI
    {
        return $this->economy;
    }

    private function savevip(Player $player, $days){
        $t = $this->vip->getAll();
        $name = strtolower($player->getName());
        if (!isset($t[$name])) {
            $allday = $days;
            $datevip = $days;
        } else {
            $date1 = strtotime($t[$name]["date"]);
            $date2 = strtotime(date("y-m-d"));
            $date3 = ceil(($date2 - $date1) / 86400);
            $date4 = ($t[$name]["day"] - $date3);
            $datevip = $date4 + $days;
            $allday = $days + $t[$name]["day"];
        }
        $t[$name]["date"] = date("Y-m-d");
        $t[$name]["day"] = $datevip;
        $t[$name]["allday"] = $allday;
        $this->vip->setAll($t);
        $this->vip->save();
        $player->sendMessage (str_replace(
                    array(
                        "{total}",
                    ),
                    array(
                        $datevip,
                    ),
                    $this->tbuy->get("viptotal")
                ));
        $this->giverank($player,$allday);
    }

    private function giverank(Player $player, $allday){
        if ($allday >= $this->tbuy->get("VIP6")){
            $this->getServer()->dispatchCommand(new ConsoleCommandSender(),"setrank ".$player->getName()." vip6");
        }elseif ($allday >= $this->tbuy->get("VIP5")){
            $this->getServer()->dispatchCommand(new ConsoleCommandSender(),"setrank ".$player->getName()." vip5");
        }elseif ($allday >= $this->tbuy->get("VIP4")){
            $this->getServer()->dispatchCommand(new ConsoleCommandSender(),"setrank ".$player->getName()." vip4");
        }elseif ($allday >= $this->tbuy->get("VIP3")) {
            $this->getServer()->dispatchCommand(new ConsoleCommandSender(),"setrank ".$player->getName()." vip3");
        }elseif ($allday >= $this->tbuy->get("VIP2")) {
            $this->getServer()->dispatchCommand(new ConsoleCommandSender(),"setrank ".$player->getName()." vip2");
        }elseif ($allday >= $this->tbuy->get("VIP1")) {
            $this->getServer()->dispatchCommand(new ConsoleCommandSender(),"setrank ".$player->getName()." vip");
        }
    }
    /**
     * @return Config
     */
    public function onDisable()
    {
        $this->getLogger()->warning("TCore is Disable");
    }
}
