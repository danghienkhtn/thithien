<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Search
 * @version     :   201008
 * @copyright   :   My company
 * @todo        :   Using to search
 */
class Core_Search
{ 
    /**
     * List instance
     *
     * @var array
     */
    private static $instances = array();

    /**
     * Get CLI control 
     */
    private static $isCli = false;
    
    /**
    * Constructor
    *
    */
    private final function __construct(){}

    /**
     * Get instance of class
     *
     * @param string $className
     * @return object
     */
    public final static function getInstance($options = array())
    {
        //Check class name
        $className = $options['adapter'] ;

        //Get core
        $coreName = $options[$options['adapter']]['core'] ;
        
        //If empty className
        if(empty($className))
        {
            throw new Core_Search_Exception("No instance of empty class when call Core_Search");
        }

        //Switch to get classname
        switch(strtolower($className))
        {
            case 'solr':
                    $className = 'Core_Search_Adapter_Solr';
                    break;
            default:
                    throw new Core_Search_Exception("No instance of class $className");
                    break;
        }

        //Check CLI console
        self::$isCli = Core_Valid::isCli();
        
        //Check instance with not CLI
        if(isset(self::$instances[$className][$coreName]) && (is_object(self::$instances[$className][$coreName])) && (!self::$isCli))
        {
            //Return instance
            return self::$instances[$className][$coreName];
        }
        
        //Put to list after init
        self::$instances[$className][$coreName] = new $className($options);

        //Return object class
        return self::$instances[$className][$coreName];
    }

    /**
     * Clone function
     *
     */
    private final function __clone() {}
}

