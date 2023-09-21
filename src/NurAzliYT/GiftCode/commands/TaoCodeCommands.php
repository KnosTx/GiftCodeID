<?php

declare(strict_types=1);

namespace NurAzliYT\GiftCode\commands;

use pocketmine\player\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginOwned;
use NurAzliYT\GiftCode\GiftCode-GB;
use NurAzliYT\GiftCode\form\FormManager;

class TaoCodeCommands extends Command implements PluginOwned {

	private GiftCode $plugin;

	public function __construct(GiftCode $plugin){
		$this->plugin = $plugin;
		parent::__construct("buatkode", "buat GiftCode", null, ["createcode"]);
		$this->setPermission("giftcode.createcode");
	}

	public function execute(CommandSender $sender, string $label, array $args){
		if(!$sender instanceof Player){
			$sender->sendMessage("§l§cMenggunakan Perintah Dalam Game");
			return true;
		}
		if(!$sender->hasPermission("giftcode.createcode")){
			$sender->sendMessage("§l§c
Anda Tidak Memiliki Hak Untuk Menggunakan Command Ini!");
			return true;
		}
		$form = new FormManager($this->getOwningPlugin());
		$form->menuCreateCode($sender);
	}

	public function getOwningPlugin() : GiftCode {
		return $this->plugin;
	}
}
