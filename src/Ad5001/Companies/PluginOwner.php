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

class PluginOwner extends Owner {
    
    
    public function __construct(String $name) {
        parent::__construct($this);
        $this->name = $name;
    }
    
    
    public function __toString() : string { // Change this in your owner !
        return "\\Ad5001\\Companies\\PluginOwner//" . $this->name;
    }
    
    
    
    public function hasItem(Item $item) : bool {
        return true;
    }
    
    
    public function addItem(Item $item) : bool {
        return true;
    }
    
    
    public function removeItem(Item $item) : bool {
        return true;
    }
    
    
    public function hasAccess(Player $player) : bool {
        return $player->hasPermission("company.traders.plugin");
    }
    
    public function getName() : string {
        return "PluginOwner";
    }
}