<?php
namespace Ad5001\Companies ; 

use pocketmine\Server;
use pocketmine\Player;

class Economy {
    
    public function __construct(string $EconomyName) {
        $this->plugin = $this->getServer()->getPluginManager()->getPlugin($EconomyName);
        $this->name = $EconomyName;
    }
    
    
    
    public function getServer() {
        return Server::getInstance();
    }
    
    
    
    public function getMoney(Player $player) {
        
        switch($this->name) {
             case "EconomyAPI":
             case "EconomyPlus":
                 return $this->plugin->myMoney($player);
             break;
             case "PocketMoney":
                 return $this->plugin->getMoney($player);
             break;
        }
    }
    
    
    
    public function addMoney(Player $player, int $money) {
        
        switch($this->name) {
             case "EconomyAPI":
             case "EconomyPlus":
                 return $this->plugin->addMoney($player, $money);
             break;
             case "PocketMoney":
                 return $this->plugin->grantMoney($player, $money);
             break;
        }
    }
    
    
    
    public function rmMoney(Player $player, int $money) {
        
        switch($this->name) {
             case "EconomyAPI":
             case "EconomyPlus":
                 return $this->plugin->takeMoney($player, $money);
             break;
             case "PocketMoney":
                 return $this->plugin->grantMoney($player, -$money);
             break;
        }
    }
    
    
    public function setMoney(Player $player, int $money) {
        
        switch($this->name) {
            return $this->plugin->setMoney($player, $money);
        }
    }
}