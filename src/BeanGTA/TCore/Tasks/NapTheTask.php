<?php

namespace BeanGTA\TCore\Tasks;

use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\Player;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\PluginException;
use TCoin\TCoin;
use jojoe77777\FormAPI;

/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 10/27/2016
 * Time: 1:35 PM
 */
class NapTheTask extends AsyncTask
{

    public $data;
    /** @var  TCoin */
    public $TCoin;
    public $playername;

    /**
     * NapTheTask constructor.
     * @param array $data
     * @param CommandSender|Player $sender
     */
    public function __construct($data, $sender)
    {
        $this->data = json_encode($data);
        $this->playername = $sender->getName();
    }

    /**
     * Actions to execute when run
     *
     * @return void
     */
    public function onRun()
    {

        $data = json_decode($this->data, true);
        $seri = $data["seri"];
        $sopin = $data["sopin"];
        $card_value = $data['card_value'];
        //Mã MerchantID dang kí trên napthengay.com
        $merchant_id = $data['merchant'];
        //Api email, email tai khoan dang ky tren napthengay.com
        $api_email = $data['api_email'];
        //mat khau di kem ma website dang kí trên  napthengay.com
        $secure_code = $data['securecode'];
        //mã giao dịch
        $trans_id = time();  //mã giao dịch do bạn gửi lên, Napthengay.com sẽ trả về 
        $api_url = 'http://api.napthengay.com/v2/';

        $arrayPost = array(
	'merchant_id'=>$merchant_id,
	'api_email'=>$api_email,
	'trans_id'=>$trans_id,
	'card_id'=>$data["mang"],
    'card_value'=>$card_value,
	'pin_field'=>$sopin,
	'seri_field'=>$seri,
	'algo_mode'=>'hmac'
);


$data_sign = hash_hmac('SHA1',implode('',$arrayPost),$secure_code);

$arrayPost['data_sign'] = $data_sign;

$curl = curl_init($api_url);

curl_setopt_array($curl, array(
	CURLOPT_POST=>true,
	CURLOPT_HEADER=>false,
	CURLINFO_HEADER_OUT=>true,
	CURLOPT_TIMEOUT=>120,
	CURLOPT_RETURNTRANSFER=>true,
	CURLOPT_SSL_VERIFYPEER => false,
	CURLOPT_POSTFIELDS=>http_build_query($arrayPost)
));
        $data = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        //$result = json_decode($data,true);
        $this->setResult(json_encode([$data, $status]));
        $time = time();
    }

    public function onCompletion(Server $server)
    {
        $tmp = json_decode($this->getResult(), true);
        $preData = json_decode($this->data, true);
        $data = $tmp[0];
        $result = json_decode($data,true);
        $status = $tmp[1];
        $user = $this->playername;
		        $player = Server::getInstance()->getPlayerExact($user);
        $plugin = $server->getPluginManager()->getPlugin("TCore");
		$api = $server->getPluginManager()->getPlugin("FormAPI");
						$form = $api->createCustomForm(function (Player $player, array $data){
                });
        $player = $server->getPlayer($user);
        $ten = $preData["ten"];
  if(($player = Server::getInstance()->getPlayerExact($this->playername)) instanceof Player) {
        $napthengay = new Config($plugin->getDataFolder()."NapTheNgay.yml",Config::YAML);
        $tcoin = $server->getPluginManager()->getPlugin("TCoin");
            if($status==200){
            $amount = $result['amount'];
			    if($result['code'] == 100){
            //mess and reward
                    $form->addLabel(str_replace('{PLAYER}', $user,str_replace('{LOAITHE}', $ten ,str_replace('{MENHGIA}', $amount ,str_replace('{LINE}', "\n",$napthengay->get("napthedung"))))));
            //$player->sendMessage(str_replace('{PLAYER}', $user,str_replace('{LOAITHE}', $ten ,str_replace('{MENHGIA}', $amount ,str_replace('{LINE}', "\n",$napthengay->get("napthedung"))))));
            $server->broadcastMessage(str_replace('{PLAYER}', $user,str_replace('{LOAITHE}', $ten,str_replace('{MENHGIA}', $amount,$napthengay->get("boardcast")))));
			$server->broadcastTitle("§l§6Cảm Ơn §e$user §6Đã Nạp Thẻ","§c§lLoại Thẻ:§a $ten , §cMệnh Giá:§a $amount");
            $form->sendToPlayer($player);
            //Reward
            $coin = $napthengay->get($amount);
            $tcoin->grantMoney($user,$coin,true);
            $file = $plugin->getDataFolder()."carddung.log";
            $fh = fopen($file,'a');
            if(!$fh) {
                throw new PluginException("Can't open file.");
            };
            fwrite($fh," ".$user." | ".$ten." | ".$amount." | Mã ".$preData["sopin"]." | Seri: ".$preData["seri"]." | ". $date = date("Y-m-d/H:i:s"));
            fwrite($fh,"\r\n");
            fclose($fh);
            $server->dispatchCommand(new ConsoleCommandSender(),"");
        }
        else{
            $error = $result['msg'];
			 $form->addLabel(str_replace('{PLAYER}', $user,str_replace('{SERI}', $preData["seri"],str_replace('{PIN}', $preData["sopin"],str_replace('{ERROL}', $error,str_replace('{LINE}', "\n",$napthengay->get("napthesai")))))));
            //$player->sendMessage(str_replace('{PLAYER}', $user,str_replace('{SERI}', $preData["seri"],str_replace('{PIN}', $preData["sopin"],str_replace('{ERROL}', $error,str_replace('{LINE}', "\n",$napthengay->get("napthesai")))))));
            $form->sendToPlayer($player);
			$file = $plugin->getDataFolder()."cardsai.log";
            $fh = fopen($file,'a');
            if(!$fh) {
                throw new PluginException("Can't open file.");
            };
            fwrite($fh," ".$user." | Mã ".$preData["sopin"]." | Seri: ".$preData["seri"]." | Lỗi : ".$error." | ". $date = date("Y-m-d/H:i:s"));
            fwrite($fh,"\r\n");
            fclose($fh);
            $server->dispatchCommand(new ConsoleCommandSender(),"");
				   }
			    }
            }
        }
    }