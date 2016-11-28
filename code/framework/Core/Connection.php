<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Connection
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to connection
 */
class Core_Connection
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
        
        //Set profiler defalt
        $profilerName = 'default';
        
        //Get profiler Name        
        if(isset($options[$options['adapter']]['idc']))
        {
            $profilerName = $options[$options['adapter']]['idc'];
        }        

        //If empty className
        if(empty($className))
        {
            throw new Core_Connection_Exception("No instance of empty class when call Core_Connection");
        }

        //Switch to get classname
        switch(strtolower($className))
        {
            case 'thrift':
                    $className = 'Core_Connection_Adapter_Thrift';
                    break;
            case 'curl':
                    $className = 'Core_Connection_Adapter_Curl';
                    break;
            case 'http':
                    $className = 'Core_Connection_Adapter_Http';
                    break;
            default:
                    throw new Core_Connection_Exception("No instance of class $className");
                    break;
        }

        //Set instance name
        $instanceName = $className.':'.$profilerName;
        
        //Check host server
        if(isset($options[$options['adapter']]['host']))
        {
            $instanceName .= ':' . $options[$options['adapter']]['host'];
        }
        
        //Check host server
        if(isset($options[$options['adapter']]['port']))
        {
            $instanceName .= ':' . $options[$options['adapter']]['port'];
        }
        
        //Set instance name
        $instanceName = md5($instanceName);
        
        //Check CLI console
        self::$isCli = Core_Valid::isCli();
        
        //Check instance with not CLI
        if(isset(self::$instances[$instanceName]) && (is_object(self::$instances[$instanceName])) && (!self::$isCli))
        {
            //Return instance
            return self::$instances[$instanceName];
        }
        
        //Put to list after init
        self::$instances[$instanceName] = new $className($options);

        //Return object class
        return self::$instances[$instanceName];
    }

    /**
     * Clone function
     *
     */
    private final function __clone() {}
}

