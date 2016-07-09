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
    
    
    public function __toString()  : string { // Change this in your owner !
        return "\\Ad5001\\Companies\\CompanyOwner//" . $this->name;
    }
    
    
    public function hasItem(Item $item) : bool {
        return $this->company->hasItem($item);
    }
    
    
    public function getName() : string {
        return "CompanyOwner";
    }
    
    
    public function addItem(Item $item) : bool {
         $this->company->addItem($item);
         return true;
    }
    
    
    public function removeItem(Item $item) : bool {
         $this->company->removeItem($item);
         return true;
    }
    
    
    public function haveAccess(Player $player) : bool {
        return $this->company->isTrustedMember($player);
    }
}