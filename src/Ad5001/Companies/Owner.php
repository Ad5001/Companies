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

abstract class Owner {
    
    
    public function __construct(Owner $owner) {
        $this->owner = $owner;
    }
    
    
    public function __toString() { // Change this in your owner !
        return "\\Ad5001\\Companies\\" . $this->owner . "//" . $this->owner->getName();
    }
    
    
    public static function __fromString(String $owner) {
        list($ownerclass, $name) = explode("//", $owner);
        return new $ownerclass($name);
    }
    
    
    public function getName() {}
    
    
    public function hasItem(Item $item) {}
    
    
    public function removeItem(Item $item) {}
    
    
    public function addItem(Item $item) {}
}