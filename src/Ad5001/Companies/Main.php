<?php
namespace Ad5001\Companies ; 
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\Player;
use pocketmine\item\Item;
use pocketmine\utils\Config;
use pocketmine\math\Vector3;

 use pocketmine\nbt\DoubleTag;
 use pocketmine\nbt\CompoundTag;
 use pocketmine\nbt\ListTag;
 use pocketmine\nbt\FloatTag;
 use pocketmine\nbt\ByteTag;
 use pocketmine\utils\TextFormat as C;
 use pocketmine\nbt\LongTag;
 use pocketmine\nbt\ShortTag;
 use pocketmine\nbt\StringTag;
 
 
 use Ad5001\Companies\PlayerOwner;
 use Ad5001\Companies\PluginOwner;
 
 
    define("PREF_TRAIDERS", C::DARK_GREEN . "[" . C::AQUA . C::BOLD . "Companies" . C::RESET . C::DARK_GREEN . "] ");
    define("PROF_DEFAULT", ["Traider", imagecreatefrompng(__DIR__ . "\\default.png")]);
    define("PROF_BUTCHER", ["Butcher", imagecreatefrompng(__DIR__ . "\\butcher.png")]);
    define("PROF_FARMER", ["Farmer", imagecreatefrompng(__DIR__ . "\\farmer.png")]);
    define("PROF_COOKER", ["Cooker", imagecreatefrompng(__DIR__ . "\\cooker.png")]);
 

class Main extends PluginBase implements Listener{
    
public function onEnable(){
    $this->reloadConfig();
    $this->getServer()->getPluginManager()->registerEvents($this, $this);
    $this->session = [];
    $this->traders = [];
    $this->trade = [];
    foreach(json_decode(file_get_contents($this->getDataFolder() . "traders.json"), true) as $world => $tradersarray) {
        foreach($tradersarray as $traderarr) {
            $trader = $this->createTraider(new Vector3($traderarr[0], $traderarr[1], $traderarr[2]), $traderarr[5], $traderarr[4], $traderarr[6]);
            $trader->setNameTag($tradearr[3]);
        }
    }
 }
 
 
public function onLoad(){
    $this->saveDefaultConfig();
}


public function onEntityDamage(EntityDamageEvent $event) {
    
    if($event instanceof EntityDamageByEntityEvent) {
        
        if(($trader = $event->getEntity()) instanceof Human and !($trader instanceof Player) and ($sender = $event->getDamager()) instanceof Player) {
            
            if(isset($trader->TradersStore) and !isset($this->trade[$sender->getName()])) {
                
                if(isset($this->session[$sender->getName()]) and $trader->TradersStore["Owner"]->hasAccess($trader, $sender)) {
                    
                    switch($this->session[$sender->getName()][0]) {
                        
                        case "setName":
                        $trader->setNameTag($this->session[$sender->getName()][1]);
                        break;
                        
                        case "setTrade":
                        $trader->TradersStore["Trades"][$this->session[$sender->getName()][1]] = [$this->session[$sender->getName()][2], $this->session[$sender->getName()][3]];
                        break;
                        
                        case "addTrade":
                        array_push($trader->TradersStore["Trades"], [$this->session[$sender->getName()][1], $this->session[$sender->getName()][2]]);
                        break;
                        
                        case "rmTrade":
                        unset($trader->TradersStore["Trades"][$this->session[$sender->getName()][1]]);
                        break;
                        
                        case "viewTrade":
                        $sender->sendMessage("You offer {$trader->TradersStore["Trades"][$this->session[$sender->getName()][1]][0]},  {$trader->getNameTag()} offers {$trader->TradersStore["Trades"][$this->session[$sender->getName()][1]][1]}");
                        break;
                        
                        case "setOwner":
                        if($this->session[$sender->getName()][1] == "Plugin") {
                            $trader->TradersStore["Owner"] == new PluginOwner("Companies");
                        } elseif($this->getServer()->getPlayer($this->session[$sender->getName()][1]) instanceof Player) {
                            $trader->TradersStore["Owner"] == new PlayerOwner($this->session[$sender->getName()][1]);
                        }
                        break;
                    }
                    unset($this->session[$sender->getName()]);
                } else {
                    
                    $sender->sendMessage("<" . $trader->getNameTag() . "> You want to trade with me ? Ok. here are my first trade: I offer ". $trader->TradersStore["Trades"][0][1] . " for " . $trader->TradersStore["Trades"][0][0] . ". Type A in the chat to accept the trade, N to see the next trade, P for the previous trade, or Q to quit the trade.");
                    $this->trade[$sender->getName()] = ["Trader" => $trader, "TradeNum" => 0];
                }
            }
            $event->setCancelled();
        } elseif(($player = $event->getEntity()) instanceof Player and isset($this->trade[$player->getName()])) {
            $event->getDamager()->sendMessage("This player is trading ! Be faiplay !");
        }
    }
}




   public static function hasItem(Player $player, Item $item) {
       $count = 0;
       for ($index = 0; $index < $player->getInventory()->getSize(); ++$index) {
           if ($item->getId() === $player->getInventory()->getItem($index)->getId() and $item->getDamage() === $player->getInventory()->getItem($index)->getDamage()) {
               $c = $player->getInventory()->getItem($index)->getCount();
               $count = $count + $c;
           }
       }
       if($count >= $item->getCount()) {
           return true;
       } else {
           return false;
       }
   }


   
   
public function onPlayerChat(PlayerChatEvent $event) {
    
    if(isset($this->trade[$event->getPlayer()->getName()])) {
        
        $trader = $this->trade[$event->getPlayer()->getName()][0];
        $t = $this->trade[$event->getPlayer()->getName()][1];
        $player = $event->getPlayer();
        switch($event->getMessage()) {
            
            case "A":
            $i = explode(":", $trader->TradersStore["Trades"][$t][0]);
            $item = Item::get($i[0], $i[1]);
            $item->setCount($i[2]);
            $i2 = explode(":", $trader->TradersStore["Trades"][$t][1]);
            $item2 = Item::get($i2[0], $i2[1]);
            $item2->setCount($i2[2]);
            if(self::hasItem($player, $item)) {
                if($trader->TradersStore["Owner"]->hasItem($item2)) {
                    $player->getInventory()->addItem($item2);
                    $trader->TradersStore["Owner"]->addItem($item);
                    $player->getInventory()->addItem($item);
                    $trader->TradersStore["Owner"]->addItem($item2);
                    $player->sendMessage("<" . $trader->getNameTag() . "> Thanks you ! Do you need anything else ?");
                } else {
                    $player->sendMessage("<" . $trader->getNameTag() . "> Oops sorry! I don't have anymore of this ! Do you need something else ?");
                }
            } else {
                $player->sendMessage("<" . $trader->getNameTag() . "> You don't have the items ! If you want this trade please come back later. Do you need something else ?");
            }
            $event->setCancelled();
            break;
            
            case "N":
            if(isset($trader->TradersStore["Trades"][$t + 1])) {
                $t = ++$this->trade[$event->getPlayer()->getName()][1];
                $player->sendMessage("<" . $trader->getNameTag() . "> Ok. here are my next trade: I offer ". $trader->TradersStore["Trades"][$t][1] . " for " . $trader->TradersStore["Trades"][$t][0] . ". Type A in the chat to accept the trade, N to see the next trade, P for the previous trade, or Q to quit the trade.");
            } else {
                $player->sendMessage("<" . $trader->getNameTag() . "> Sorry, I don't have any other trades for now.");
            }
            $event->setCancelled();
            break;
            
            case "P":
            if(isset($trader->TradersStore["Trades"][$t - 1])) {
                $t = --$this->trade[$event->getPlayer()->getName()][1];
                $player->sendMessage("<" . $trader->getNameTag() . "> Ok. here are my next trade: I offer ". $trader->TradersStore["Trades"][$t][1] . " for " . $trader->TradersStore["Trades"][$t][0] . ". Type A in the chat to accept the trade, N to see the next trade, P for the previous trade, or Q to quit the trade.");
            } else {
                $player->sendMessage("<" . $trader->getNameTag() . "> Sorry, I don't have any other trades for now.");
            }
            $event->setCancelled();
            break;
            
            case "Q":
            unset($this->trade[$player->getName()]);
            $player->sendMessage("<" . $trader->getNameTag() . "> Thanks ! That was a pleasure to trade with you !");
            $event->setCancelled();
            break;
        }
    }
}




public function onPlayerMove(PlayerMoveEvent $event) {
    if(isset($this->trade[$event->getPlayer()->getName()])) {
        $event->setCancelled();
    }
}




public function onCommand(CommandSender $sender, Command $cmd, $label, array $args){
switch(strtolower($cmd->getName())){
    case "traders":
    if(isset($args[0])) {
        switch(strtolower($args[0])) {
            case "create":
            $this->createTraider(new Vector3($sender->x, $sender->y, $sender->z), [["0:0:1", "0:0:1"]], PROF_DEFAULT, new PlayerOwner($sender->getName()));
            $sender->sendMessage(PREF_TRAIDERS . C::GREEN . "Traider has been created at your position ! Customize it with : /traders modify <subcommand> <value> then tap this traider !");
            break;
            case "modify":
            if(isset($args[2])) {
                switch(strtolower($args[1])) {
                    case "setname":
                    $this->session[$sender->getName()] = ["setName", $args[2]];
                    break;
                    case "settrade":
                    if(isset($args[4])) {
                        $this->session[$sender->getName()] = ["setTrade", $args[2], $args[3], $args[4]];
                    }
                    break;
                    case "addtrade":
                    if(isset($args[3])) {
                        $this->session[$sender->getName()] = ["addTame", $args[2], $args[3]];
                    }
                    break;
                    case "rmtrade":
                    $this->session[$sender->getName()] = ["rmTrade", $args[2]];
                    break;
                    case "viewtrade":
                    $this->session[$sender->getName()] = ["viewTrade", $args[2]];
                    break;
                    case "setowner":
                    if($sender->hasPermission("traders.setowner")) {
                        $this->session[$sender->getName()] = ["setOwner", $args[2]];
                    }
                    default:
                    
                    case "help":
                    $sender->sendMessage("Commands: - /traders modify setname <name>\n- /traders modify settrade <id> <Trader purpose> <Player purpose>\n- /traders modify addtrade <Trader purpose> <You purpose>\n- /traders modify rmtrade <id>\n- /traders modify viewtrade <id>\n- /traders modify setowner <Plugin / player name>");
                    break;
                }
            }
        }
    }
    break;
}
return false;
 }
 
 
 
 
 public function createTraider(Vector3 $pos, array $trades, $profession = PROF_DEFAULT, Owner $owner) {
		$nbt = new CompoundTag ("", [
            "NameTag" => new StringTag("NameTag", $profession[0] . count($this->traders)),
            "Pos" => new ListTag("Pos", [
                 new DoubleTag("x", $pos->x),
			     new DoubleTag("y", $pos->y),
			     new DoubleTag("z", $pos->z)
		    ]),
            "Motion" => new ListTag("Motion", [
			     new DoubleTag(0, 0),
			     new DoubleTag(1, 0),
			     new DoubleTag(2, 0)
		    ]),
            "Rotation" => new ListTag("Rotation", [
			     new FloatTag(0, 0),
			     new FloatTag(1, 0)
		    ]),
            "FallDistance" => new FloatTag("FallDistance", 0.0),
            "Fire" => new ShortTag("Fire", (int) 0),
            "Air" => new ShortTag("Air", 0),
            "OnGround" => new ByteTag("OnGround", 1),
            "Invulnerable" => new ByteTag("Invulnerable", 1),
            "Health" => new ShortTag("Health", (int) 20),
            "Inventory" => new ListTag("Inventory", [new CompoundTag(false, [
			     new Short("id", explode(":", $trades[0])[0]),
			     new Short("Damage", explode(":", $trades[1])),
			     new Byte("Count", explode(":", $trades[0])[2]),
			     new Byte("Slot", 9),
			     new Byte("TrueSlot", 9)
		    ])]),
            "TradersStore" => new ListTag("TradersStore", [
                 "Profession" => new StringTag("Profession", $profession),
                 "Trades" => new ListTag("Trades", $trades),
                 "Id" => new LongTag("Id", $i = count($this->traders) + 1),
                 "Owner" => new StringTag("Owner", $owner)
            ])
        ]);
        $this->traders[$i] = Entity::createEntity ( "Human", $pos->chunk, $nbt, $pos );
        $this->traders[$i]->setSkin($profession[1]);
        $this->traders[$i]->spawnToAll();
        return $this->traders[$i];
 }
 
 
 
 
 
 public function onDisable() {
     $sertraders = [];
     foreach($this->traders as $trader) {
         if(!isset($sertraders[$trader->getLevel()])) {
             $sertraders[$trader->getLevel()] = [];
         }
         array_push($sertraders[$trader->getLevel()], [$trader->x, $trader->y, $trader->z, $trader->getNameTag(), $trader->TradersStore["Profession"], $trader->TradersStore["Trades"], $trader->TradersStore["Owner"]]);
     }
     file_put_contents($this->getDataFolder() . "traders.json", json_encode($sertraders));
     
 }
}