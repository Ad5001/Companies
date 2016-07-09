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
use Ad5001\Companies\Company;

class CompanyOwner extends Owner {
    
    
    public function __construct(String $name) {
        parent::__construct($this);
        $this->name = $name;
        $this->company = Company::getCompanyByName($name);
    }
    
    
    public function __toString() { // Change this in your owner !
        return "\\Ad5001\\Companies\\CompanyOwner//" . $this->name;
    }
    
    
    public static function __fromString(String $owner) {
        list($ownerclass, $name) = explode("//", $owner);
        return new $ownerclass($name);
    }
    
    
    public function hasItem(Item $item) {
        return $this->company->hasItem($item);
    }
    
    
    public function getName() {
        return "CompanyOwner";
    }
    
    
    public function addItem(Item $item) {
         $this->company->addItem($item);
    }
    
    
    public function removeItem(Item $item) {
         $this->company->removeItem($item);
    }
    
    
    public function haveAccess(Player $player) {
        return $player == $this->player;
    }
}