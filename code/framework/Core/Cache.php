<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Cache
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to caching
 */
class Core_Cache
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
        $profilerName = $options['profiler']['name'];
        
        //If empty className
        if(empty($className))
        {
            throw new Core_Cache_Exception("No instance of empty class when call Core_Cache");
        }

        //Switch to get classname
        switch(strtolower($className))
        {
            case 'memcache':
                $className = 'Core_Cache_Adapter_Memcache';
                break;
            case 'memcachev1':
                $className = 'Core_Cache_Adapter_Memcachev1';
                break;
            case 'memcachedv1':
                $className = 'Core_Cache_Adapter_Memcachedv1';
                break;
            case 'apc':
                $className = 'Core_Cache_Adapter_Apc';
                break;
            case 'xcache':
                $className = 'Core_Cache_Adapter_Xcache';
                break;
            case 'twolevels':
                $className = 'Core_Cache_Adapter_TwoLevels';
                break;
            case 'nocache':
                $className = 'Core_Cache_Adapter_Nocache';
                break;
            default:
                throw new Core_Cache_Exception("No instance of class $className");
                break;
        }

        //Set instance name
        $instanceName = md5($className.'.'.$profilerName);
        
        //Check CLI console
        self::$isCli = Core_Valid::isCli();
        
        //Check instance with not CLI
        if(isset(self::$instances[$instanceName]) && (is_object(self::$instances[$instanceName])) && (!self::$isCli))
        {
            //Return instance
            return self::$instances[$instanceName];
        }
        
        //Put to list after create instance
        self::$instances[$instanceName] = new $className($options);

        //Return object class
        return self::$instances[$instanceName];
    }

    /**
     * Close all connection
     */
    public static function closeAll()
    {
        //Loop and delete object
        foreach(self::$instances as $cachingObject)
        {
            $cachingObject = null;
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

