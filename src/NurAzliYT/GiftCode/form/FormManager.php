<?php

declare(strict_types=1);

namespace NurAzliYT\GiftCode\form;

use pocketmine\player\Player;
use jojoe77777\FormAPI\CustomForm;
use cooldogepm\BedrockEconomy\BedrockEconomy;
use onebone\coinapi\CoinAPI;
use NurAzliYT\GiftCode\GiftCode-GB;

class FormManager {

	private GiftCode $plugin;

	public function __construct(GiftCode $plugin){
		$this->plugin = $plugin;
	}

	public function menuCreateCode(Player $player){
		$form = new CustomForm(function(Player $player, $data){
			if(!isset($data)){
				return true;
			}
			// Mã Code = $data[1]
			// Số Người Nhập = $data[2]
			// Số Tiền Tặng ( Ghi 0 nếu Ko Tặng Tiền ) = $data[3]
			// Giống 3 Nhưng Đổi Thành Coin = $data[4]
			if(!isset($data[1])){
				$player->sendMessage("§l§cPlease enter the code you want to create in the first box");
				return true;
			}
			if(!isset($data[2])){
				$player->sendMessage("§l§cPlease enter the number of people who can enter this code in the 2nd box");
				return true;
			}
			if(!isset($data[3])){
				$player->sendMessage("§bPlease enter the amount of Money you want to give in this gift code (Enter 0 if not giving Money)");
				return true;
			}
			if(!isset($data[4])){
				$player->sendMessage("§bPlease enter the number of Coins you want to give away in this gift code (Enter 0 if not giving Coins)");
				return true;
			}
			if(!is_numeric($data[2]) or !is_numeric($data[3]) or !is_numeric($data[4])){
				$player->sendMessage("§bThe second box that must be entered is a number");
				return true;
			}
			if($this->plugin->code->exists($data[1])){
				$count = $this->plugin->code->get($data[1])["count"];
				$player->sendMessage("§cThe $data[1] code already exists and has $count of entries remaining");
				return true;
			}
			$this->plugin->code->set($data[1], [
				"count" => (int)$data[2],
				"enter-by-player-list" => "$data[1]",
				"money" => (int)$data[3],
				"coin" => (int)$data[4]
			]);
			$this->plugin->code->save();
			$player->sendMessage("§bSuccessfully create Giftcode $data[1] with input $data[2], the prize is $data[3] Coin and $data[4] Coin
");
		});
		$form->setTitle("Create a Gift Code");
		$form->addLabel("Please fill in what you need to fill in below");
		$form->addInput("Code:", "For example: QNLYYO7588");
		$form->addInput("Number of people who can enter:", "Example: 5");
		$form->addInput("Jumlah yang diberikan:", "Contoh: 1 (Tulis 0 jika tidak memberikan Uang)");
		$form->addInput("Jumlah koin yang disumbangkan:", "Contoh: 2 (Tulis 0 jika Anda tidak menyumbangkan Koin)");
		$form->sendToPlayer($player);
	}

	public function menuEnterCode(Player $player){
		$form = new CustomForm(function(Player $player, $data){
			if(!isset($data)){
				$player->sendMessage("§bSilakan masukkan kode yang ingin Anda masukkan untuk menerima hadiah");
				return true;
			}
			if(!$this->plugin->code->exists($data[0])){
				$player->sendMessage("§cThis Code Doesn't Exist!");
				return true;
			}
			if((int)$this->plugin->code->get($data[0])["count"] < 1){
				$player->sendMessage("§cThis Code Has Expired!!");
				return true;
			}
			$ex = explode(", ", $this->plugin->code->get($data[0])["enter-by-player-list"]);
			if(!in_array($player->getDisplayName(), $ex)){
				$im = implode(", ", $ex);
				$add = "$im, " . $player->getDisplayName();
				$this->plugin->code->setNested($data[0] . ".enter-by-player-list", $add);
				$this->plugin->code->save();
				$money = (int)$this->plugin->code->get($data[0])["money"];
				$coin = (int)$this->plugin->code->get($data[0])["coin"];
				$player->sendMessage("§bEnter the code successfully and you have received $money Money and $coin Coin");
                                BedrockEconomy::getInstance()->addMoney($player, $money);
				CoinAPI::getInstance()->addCoin($player, $coin);
				$count = (int)$this->plugin->code->get($data[0])["count"];
				$this->plugin->code->setNested($data[0] . ".count", $count - 1);
				$this->plugin->code->save();
			}else{
				$player->sendMessage("§cAnda telah memasukkan kode ini sebelumnya!");
			}
		});
		$form->setTitle("Enter Code");
		$form->addInput("Enter the code you want to enter below:", "Example: BD148SF8GJQ");
		$form->sendToPlayer($player);
	}
}
