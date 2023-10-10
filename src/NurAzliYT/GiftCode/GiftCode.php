<?php

namespace NurAzliYT\GiftCode;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use jojoe77777\FormAPI\CustomForm;
use cooldogedev\BedrockEconomy\BedrockEconomy;

class GiftCode extends PluginBase {

    private BedrockEconomy $economy;

    public function onEnable(): void {
        $this->economy = $this->getServer()->getPluginManager()->getPlugin(BedrockEconomy::class);

        if ($this->economy === null) {
            $this->getLogger()->error("Plugin BedrockEconomy not found. Please make sure it's installed.");
            $this->getServer()->getPluginManager()->disablePlugin($this);
        }
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        if ($command->getName() === "code") {
            if ($sender instanceof Player) {
                $this->openCodeForm($sender);
            } else {
                $sender->sendMessage("§cThis command can only be used by players.");
            }
        }
        return true;
    }

    public function openCodeForm(Player $player) {
        $form = new CustomForm(function (Player $player, $data) {
            if ($data === null) {
                return;
            }
            $formType = (int)$data[0];

            if ($formType === 0) {
                $this->openCreateCodeForm($player);
            } elseif ($formType === 1) {
                $this->openSendCodeForm($player);
            }
        });

        $form->setTitle("Code Form");
        $form->addDropdown("Select form type:", ["Create Code", "Send Code"]);
        $player->sendForm($form);
    }

    public function openCreateCodeForm(Player $player) {
        $form = new CustomForm(function (Player $player, $data) {
            if ($data === null) {
                return;
            }
            $code = $data[0];
            $numOfPlayers = (int)$data[1];
            $moneyAmount = (int)$data[2];
            $coinAmount = (int)$data[3];

            // Implement your code creation logic here

            $player->sendMessage("§aCode '$code' has been created with input: Players: $numOfPlayers, Money: $moneyAmount, Coins: $coinAmount");
        });

        $form->setTitle("Create Code");
        $form->addInput("Code", "Example: ABC123");
        $form->addInput("Number of Players Who Can Use the Code", "Example: 5");
        $form->addInput("Amount of Money in the Code", "Example: 100 (Enter 0 if no money)");
        $form->addInput("Amount of Coins in the Code", "Example: 50 (Enter 0 if no coins)");
        $player->sendForm($form);
    }

    public function openSendCodeForm(Player $player) {
        $form = new CustomForm(function (Player $player, $data) {
            if ($data === null) {
                return;
            }
            $codeToSend = $data[0];
            $recipient = $data[1];
            $message = $data[2];

            if ($this->economy !== null) {
                $this->sendMoneyUsingBedrockEconomy($player, $recipient, $codeToSend);
            } else {
                $player->sendMessage("§cBedrockEconomy plugin not found. Please contact the server admin.");
            }

            $player->sendMessage("§aCode '$codeToSend' has been sent to '$recipient' with message: '$message'");
        });

        $form->setTitle("Send Code");
        $form->addInput("Code", "Example: ABC123");
        $form->addInput("Recipient", "Name of the player who will receive the code");
        $form->addInput("Message", "Additional message (optional)");
        $player->sendForm($form);
    }

    private function sendMoneyUsingBedrockEconomy(Player $sender, $recipient, $amount) {
        $recipientPlayer = $this->getServer()->getPlayerExact($recipient);

        if ($recipientPlayer instanceof Player) {
            $this->economy->addMoney($recipientPlayer, $amount);
            $sender->sendMessage("§aYou have sent $amount currency to $recipient.");
        } else {
            $sender->sendMessage("§cRecipient not found or currently offline.");
        }
    }
}
