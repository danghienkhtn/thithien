<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Data
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to serialize and unserialize data
 */
class Core_Data
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

        //If empty className
        if(empty($className))
        {
            throw new Core_Data_Exception("No instance of empty class when call Core_Data");
        }

        //Switch to get classname
        switch(strtolower($className))
        {            
            case 'default':
                $className = 'Core_Data_Adapter_Default';
                break;
            case 'msgpack':
                $className = 'Core_Data_Adapter_MsgPack';
                break; 
            case 'snappy':
                $className = 'Core_Data_Adapter_Snappy';
                break;
            default:
                throw new Core_Data_Exception("No instance of class $className");
                break;
        }

        //Put to list
        if(!isset(self::$instances[$className]))
        {
            self::$instances[$className] = new $className($options);
        }

        //Return object class
        return self::$instances[$className];
    }

    /**
     * Clone function
     *
     */
    private final function __clone() {}
}

