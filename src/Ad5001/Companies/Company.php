<?php
namespace Ad5001\Companies;

use pocketmine\utils\Config;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\item\Item;
use pocketmine\entity\Human;

use Ad5001\Companies\CompanyOwner;

class Company {
    
    protected $name;
    protected $owner;
    protected $members;
    protected $money;
    protected $inventory;
    protected $inventory;
    
    public function __construct(string $name, Player $owner, array $members, array $trustedMembers, array $traders = [], int $money = 0, array $inventory = [], bool $created = true) {
        $this->name = $name;
        $this->owner = new CompanyOwner($this->name);
        $this->playerowner = $owner;
        $this->members = $members;
        $this->trustmembers = $trustedMembers;
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
                return new Company($company["name"], Server::getInstance()->getPlayer($company["playerowner"]), $members, $company["trustedMembers"], $traders, $company["inventory"], $company["money"]);
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
        $cfg->set("name" => $this->name, "playerowner" => $this->playerowner->getName(), "members" => $members, "trustedMembers" => $this->trustmembers, "traders" => $traders, "money" => $this->money);
    }
    
    
    public function hasItem(Item $item) {
        
        if(isset($this->inventory[$item->getId() . ":" . $item->getDamage()])) {
            if($item->getCount() <= $this->inventory[$item->getId() . ":" . $item->getDamage()]["count"]) {
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
            $this->inventory[$item->getId() . ":" . $item->getDamage()] = ["Id" => $item->getId(), "Damage" => $item->getDamage(), "count" => $item->getCount()];
            return true;
        }
        return false;
    }
    
    
    public function removeItem(Item $item) {
        
        if(isset($this->inventory[$item->getId() . ":" . $item->getDamage()])) {
            if($item->getCount() < $this->inventory[$item->getId() . ":" . $item->getDamage()]["count"]) {
                $this->inventory[$item->getId() . ":" . $item->getDamage()]["count"] -= $item->getCount();
                return true;
            } elseif($item->getCount() == $this->inventory[$item->getId() . ":" . $item->getDamage()]["count"]) {
                unset($this->inventory[$item->getId() . ":" . $item->getDamage()]["count"]);
                return true;
            }
        }
        return false;
    }
    
    
    public function getInventory() {
        return $this->inventory;
    }
    
    
    public function addMoney(int $money) {
        $this->money += $money;
        return true;
    }
    
    
    public function takeMoney(int $money) {
        $this->money -= $money;
        return true;
    }
    
    
    public function getMoney() {
        return $this->money;
    }
    
    
    public function setName(string $name) {
        $this->name = $name;
        return true;
    }
    
    
    public function getName() {
        return $this->name;
    }
    
    
    public function getPlayerOwner() {
        return $this->playerowner;
    }
    
    
    public function setPlayerOwner(Player $player) {
        $this->playerowner = $owner;
    }
    
    
    
    public function addMember(Player $member) {
        
        if(!in_array($this->members, $member)) {
            array_push($this->members, $member);
            return true;
        }
        return false;
    }
    
    
    
    public function removeMember(Player $member) {
        
        if(in_array($this->members, $member)) {
            $id = 0;
            foreach($this->members as $members) {
                if($members->getName() == $member->getName()) {
                    unset($this->members[$id]);
                    return true;
                }
            }
        }
        return false;
    }
    
    
    
    public function isMember(Player $member) {
        
        return in_array($this->members, $member);
    }
    
    
    
    public function getMembers() {
        
        return $this->members;
    }
    
    
    
    public function addTrustedMember(Player $member) {
        
        if(!in_array($this->trustmembers, $member) and $this->isMember($player)) {
            array_push($this->members, $member);
            return true;
        }
        return false;
    }
    
    
    
    public function removeTrustedMember(Player $member) {
        
        if(in_array($this->trustmembers, $member)) {
            $id = 0;
            foreach($this->trustmembers as $members) {
                if($members->getName() == $member->getName()) {
                    unset($this->trustmembers[$id]);
                    return true;
                }
            }
        }
        return false;
    }
    
    
    
    public function isTrustedMember(Player $member) {
        
        return in_array($this->trustmembers, $member);
    }
    
    
    
    public function getTrustedMembers() {
        
        return $this->trustmembers;
    }
}