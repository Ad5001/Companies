<?php
namespace Ad5001\Companies;

use pocketmine\utils\Config;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\item\Item;
use pocketmine\entity\Human;

use Ad5001\Companies\CompanyOwner;

class Company {
    
    
    public function __construct(string $name, Player $owner, array $members, array $traders = [], int $money = 0, array $inventory = [], bool $created = true) {
        $this->name = $name;
        $this->owner = new CompanyOwner($this->name);
        $this->playerowner = $owner;
        $this->members = $members;
        $this->inventory = $inventory;
        if(!$created) {
            Server::getInstance()->broadcastMessage($owner->getName() " created a new company : {$name} ! To join this company, use /company join {$name}");
        }
    }
    
    
    public static function getCompanyByName(string $name) {
        
        $cfg = new Config(Server::getInstance()->getPluginPath() . "Companies/companies.json", Config::JSON);
        foreach($cfg->getAll() as $company) {
            if($company["name"] == $name) {
                $members = [];
                foreach($company["member"] as $membername) {
                    array_push($members, Server::getInstance()->getPlayer($membername));
                }
                $traders = [];
                foreach(Server::getInstance()->getLevels() as $level) {
                    foreach($level->getEntities() as $entity) {
                        if($entity instanceof Human and isset($entity->TradersStore)) {
                            if(in_array($company["traders"]), $entity->TradersStore["Id"]) {
                                array_push($traders, $entity)
                            }
                        }
                    }
                }
                return new Company($company["name"], Server::getInstance()->getPlayer($company["playerowner"]), $members, $traders, $company["inventory"], $company["money"]);
            }
        }
        return null;
    }
    
    
    public function __destruct() {
        $members = [];
        foreach($this->members as $member) {
            array_push($members, $member->getName());
        }
        $traders = [];
        foreach($this->traders as $trader) {
            array_push($traders, $trader->TradersStore["Id"]);
        }
        $cfg = new Config(Server::getInstance()->getPluginPath() . "Companies/companies.json", Config::JSON);
        $cfg->set("name" => $this->name, "playerowner" => $this->playerowner->getName(), "members" => $members, "traders" => $traders, "money" => $this->name)
    }
    
    
    public function hasItem(Item $item) {
        
        if(isset($this->inventory[$item->getId() . ":" . $item->getDamage()])) {
            if($item->getCount() < $this->inventory[$item->getId() . ":" . $item->getDamage()]["count"]) {
                return true;
            }
        }
        return false;
    }
    
    
    public function addItem(Item $item) {
        
        if(isset($this->inventory[$item->getId() . ":" . $item->getDamage()])) {
            $this->inventory[$item->getId() . ":" . $item->getDamage()]["count"] += $item->getCount();
            return true;
        } else {
            $this->inventory[$item->getId() . ":" . $item->getDamage()] = ["Id" => $item->getId(), "Damage" => $item->getDamage(), "count" => $item->getCount();
            return true;
        }
        return false;
    }
}