<?php
namespace Ad5001\Companies;

# To extends this you will need to do :
#
# class <YOUR_CLASS_NAME> extends Owner {
#      
#      public function __construct(String $name) {
#           $this->name = $name;
#      }
#      
#      public function __fromString(Owner $owner) {}
#      
#      public function __toString() {
#           return __NAMESPACE__ . "\\<YOUR_CLASS_NAME>//" . $this->name;
#      }
#      
#      public function getName() {
#           return "<YOUR_CLASS_NAME>";
#      }
#      
#      public function hasItem(Item $item) {
#          // Return true if it has, false if it haven't.
#      }
#      
#      public function removeItem(Item $item) {
#          // Remove the item.
#      }
#      
#      public function addItem(Item $item) {
#          // Add the item.
#      }
#      
# }
#
#
#

use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\Server;

use Ad5001\Companies\Main;

class PlayerOwner extends Owner {
    
    
    public function __construct(String $name) {
        parent::__construct($this);
        $this->name = $name;
        $this->player = Server::getInstance()->getPlayer($name);
    }
    
    
    public function __toString() { // Change this in your owner !
        return "\\Ad5001\\Companies\\PlayerOwner//" . $this->name;
    }
    
    
    public static function __fromString(String $owner) {
        list($ownerclass, $name) = explode("//", $owner);
        return new $ownerclass($name);
    }
    
    
    public function hasItem(Item $item) {
        return Main::hasItem($this->player);
    }
    
    
    public function getName() {
        return "PlayerOwner";
    }
    
    
    public function addItem(Item $item) {
         $this->player->addItem($item);
    }
    
    
    public function removeItem(Item $item) {
         $this->player->removeItem($item);
    }
    
    
    public function haveAccess(Player $player) {
        return $player == $this->player;
    }
}