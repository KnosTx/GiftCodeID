<?php

declare(strict_types=1);

namespace NurAzliYT\GiftCode;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use NurAzliYT\GiftCode\commands\NhapCodeCommands;
use NurAzliYT\GiftCode\commands\TaoCodeCommands;
use NurAzliYT\GiftCode\form\FormManager;

class GiftCode extends PluginBase {

	public static $instance;

	public static function getInstance() : self {
		return self::$instance;
	}

	public function onEnable() : void {
		$this->registerCommands();
		$this->code = new Config($this->getDataFolder() . "codes.yml", Config::YAML);
		self::$instance = $this;
	}

	private function registerCommands() : void {
		$this->getServer()->getCommandMap()->register("/klaimkode", new NhapCodeCommands($this));
		$this->getServer()->getCommandMap()->register("/buatkode", new TaoCodeCommands($this));
	}

	public function onDisable() : void {
		$this->code->save();
	}
}
