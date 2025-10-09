<?php
namespace dr\classes\patterns;

/**
 * Description of TSingleton
 *
 * This class implements the singleton design pattern: only one instance of a
 * class is allowed
 *
 * use TSingleton::getInstance() to get the instance of the class
 *
 * howto implement in child class:
 * 1) just override the getInstance() function with
 * the code below (don't forget to replace TSingleton with your own classname):
 *  public static function getInstance()
 *   {
 *       if (!TSingleton::$objInstance instanceof self) {
 *           TSingleton::$objInstance = new self();
 *       }
 *       return TSingleton::$objInstance;
 *   }
 * 2) add  declaration to the header of your class : private static $objInstance;
 *
 * @since 14-10-2009
 * @author dennis renirie
 */
abstract class TSingleton
{

    protected function __construct() {}
    private function __clone() {}
    public function getClone() {}

    /**
     * gets the only instance of this class
     */
    abstract public static function getInstance();

    
}

?>
