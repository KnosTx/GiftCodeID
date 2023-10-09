<?php

namespace NurAzliYT\GiftCode;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use jojoe77777\FormAPI\CustomForm;
use cooldogedev\BedrockEconomy\BedrockEconomy; // Import namespace BedrockEconomy

class GiftCode extends PluginBase {

    private BedrockEconomy $economy;

        // Inisialisasi BedrockEconomy
    public function onEnable():void {
        $this->economy = $this->getServer()->getPluginManager()->getPlugin(BedrockEconomy::class);

        if ($this->economy === null) {
            $this->getLogger()->error("BedrockEconomy plugin not found. Make sure it's installed.");
            $this->getServer()->getPluginManager()->disablePlugin($this);
        }
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        if ($command->getName() === "code") {
            if ($sender instanceof Player) {
                $this->openCodeForm($sender);
            } else {
                $sender->sendMessage("§cPerintah ini hanya dapat digunakan oleh pemain.");
            }
        }
        return false;
    }

    public function openCodeForm(Player $player) {
        $form = new CustomForm(function (Player $player, $data) {
            if ($data === null) {
                return;
            }
            $formType = (int)$data[0]; // Mendapatkan jenis formulir yang dipilih oleh pemain

            if ($formType === 0) {
                // Pilihan "Buat Kode" dipilih
                $this->openCreateCodeForm($player);
            } elseif ($formType === 1) {
                // Pilihan "Kirim Kode" dipilih
                $this->openSendCodeForm($player);
            }
        });

        $form->setTitle("Form Kode");
        $form->addDropdown("Pilih jenis formulir:", ["Buat Kode", "Kirim Kode"]); // Menampilkan pilihan "Buat Kode" dan "Kirim Kode"
        $player->sendForm($form);
    }

    public function openCreateCodeForm(Player $player) {
        $form = new CustomForm(function (Player $player, $data) {
            if ($data === null) {
                return;
            }
            $code = $data[0]; // Mendapatkan kode yang dimasukkan oleh pemain dari formulir
            $numOfPlayers = (int)$data[1]; // Mendapatkan jumlah pemain yang dimasukkan oleh pemain dari formulir
            $moneyAmount = (int)$data[2]; // Mendapatkan jumlah uang yang dimasukkan oleh pemain dari formulir
            $coinAmount = (int)$data[3]; // Mendapatkan jumlah koin yang dimasukkan oleh pemain dari formulir

            // Di sini, Anda dapat mengimplementasikan logika pembuatan kode sesuai dengan kebutuhan Anda

            // Memberikan pemain pesan konfirmasi
            $player->sendMessage("§aKode '$code' telah dibuat dengan input: Pemain: $numOfPlayers, Uang: $moneyAmount, Koin: $coinAmount");
        });

        $form->setTitle("Buat Kode");
        $form->addInput("Kode", "Contoh: ABC123");
        $form->addInput("Jumlah Pemain yang Dapat Memasukkan Kode", "Contoh: 5");
        $form->addInput("Jumlah Uang dalam Kode", "Contoh: 100 (Tulis 0 jika tidak ada uang)");
        $form->addInput("Jumlah Koin dalam Kode", "Contoh: 50 (Tulis 0 jika tidak ada koin)");
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

            // Di sini Anda dapat mengimplementasikan logika pengiriman kode ke penerima
            // serta mengupdate saldo pemain menggunakan BedrockEconomy
            if ($this->economy !== null) {
                $this->sendMoneyUsingBedrockEconomy($player, $recipient, $codeToSend);
            } else {
                $player->sendMessage("§cPlugin BedrockEconomy tidak ditemukan. Hubungi admin server.");
            }

            // Memberikan pemain pesan konfirmasi
            $player->sendMessage("§aKode '$codeToSend' telah dikirim ke '$recipient' dengan pesan: '$message'");
        });

        $form->setTitle("Kirim Kode");
        $form->addInput("Kode", "Contoh: ABC123");
        $form->addInput("Penerima", "Nama pemain yang akan menerima kode");
        $form->addInput("Pesan", "Pesan tambahan (opsional)");
        $player->sendForm($form);
    }

    // Metode untuk mengirim uang menggunakan BedrockEconomy
    private function sendMoneyUsingBedrockEconomy(Player $sender, $recipient, $amount) {
        // Pastikan penerima ada di server sebelum mengirim uang
        $recipientPlayer = $this->getServer()->getPlayerExact($recipient);

        if ($recipientPlayer instanceof Player) {
            $this->economy->addMoney($recipientPlayer, $amount);
            $sender->sendMessage("§aAnda telah mengirim $amount mata uang kepada $recipient.");
        } else {
            $sender->sendMessage("§cPenerima tidak ditemukan atau sedang offline.");
        }
    }
}
}
