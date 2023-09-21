<?php

declare(strict_types=1);

namespace NurAzliYT\GiftCode\commands;

use pocketmine\player\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginOwned;
use NurAzliYT\GiftCode\GiftCode;
use NurAzliYT\GiftCode\form\FormManager;

class NhapCodeCommands extends Command implements PluginOwned {

	private GiftCode $plugin;

	public function __construct(GiftCode $plugin){
		$this->plugin = $plugin;
		parent::__construct("ec", "Enter Code | GiftCode", null, ["ec"]);
		$this->setPermission("giftcode.entercode");
	}

	public function execute(CommandSender $sender, string $label, array $args){
		if(!$sender instanceof Player){
			$sender->sendMessage("§l§cUsing In-Game Commands. Cannot be used on console");
			return true;
		}
		if(!$sender->hasPermission("giftcode.entercode")){
			$sender->sendMessage("§c§lYou Have No Rights To Use This Code!
You Have No Rights To Use This Code!");
			return true;
		}
		$form = new FormManager($this->getOwningPlugin());
		$form->menuEnterCode($sender);
	}

	public function getOwningPlugin() : GiftCode {
		return $this->plugin;
	}
}
