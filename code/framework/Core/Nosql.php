<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Nosql
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to crypt
 */
class Core_Nosql
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
    private final function __construct() {}
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
        
        //Get profiler Name
        $profilerName = 'default';
        if(isset($options[$options['adapter']]['idc']))
        {
            $profilerName = $options[$options['adapter']]['idc'];
        }        

        //If empty className
        if(empty($className))
        {
            throw new Core_Nosql_Exception("No instance of empty class when call Core_Nosql");
        }

        //Switch to get classname
        switch(strtolower($className))
        {
            case 'redis':
                $className = 'Core_Nosql_Adapter_Redis';
                break;
            case 'mongo':
                $className = 'Core_Nosql_Adapter_Mongo';
                break;
            default:
                throw new Core_Nosql_Exception("No instance of class $className");
                break;
        }

        //Set ID with classname
        $instanceID = md5($className.'.'.$profilerName);
        
        //Check CLI console
        self::$isCli = Core_Valid::isCli();
        
        //Check instance with not CLI
        if(isset(self::$instances[$instanceID]) && (is_object(self::$instances[$instanceID])) && (!self::$isCli))
        {
            //Return instance
            return self::$instances[$instanceID];
        }
        
        //Put to list after init
        self::$instances[$instanceID] = new $className($options);
         
        //Return object class
        return self::$instances[$instanceID];
    }

    /**
     * Close all connection
     */
    public static function closeAll()
    {
        //Loop and delete object
        foreach(self::$instances as $nosqlObject)
        {
            unset($nosqlObject);
        }
        
        //Reset all
        self::$instances = array();
    }

    /**
     * Clone function
     *
     */
    private final function __clone() {}
}

