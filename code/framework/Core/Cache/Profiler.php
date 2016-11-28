<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Cache_Profiler
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to caching
 */
class Core_Cache_Profiler
{
    /**
     * List instance
     *
     * @var array
     */
    private static $instances = array();

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
        $profiler_name = $options['name'] ;

        //If empty className
        if(empty($className))
        {
            throw new Core_Cache_Exception("No instance of empty class when call Core_Cache_Profiler");
        }
        
        //Switch to get classname
        switch(strtolower($className))
        {
            case 'html':
                $className = 'Core_Cache_Profiler_Adapter_Html';
                break;
            default:
                throw new Core_Cache_Exception("No instance of class $className");
                break;
        }

        //Put to list
        if(!isset(self::$instances[$className][$profiler_name]))
        {            
            self::$instances[$className][$profiler_name] = new $className($options);
        }

        //Return object class
        return self::$instances[$className][$profiler_name];
    }    
    
    /**
     * Clone function
     *
     */
    private final function __clone() {}
}

