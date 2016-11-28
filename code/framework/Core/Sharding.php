<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Sharding
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to shading storage
 */
class Core_Sharding
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
            throw new Core_Sharding_Exception("No instance of empty class when call Core_Sharding");
        }

        //Switch to get classname
        switch(strtolower($className))
        {
            case 'naive':
                $className = 'Core_Sharding_Adapter_Naive';
                break; 
            case 'ring':
                $className = 'Core_Sharding_Adapter_Ring';
                break;
            default:
                throw new Core_Sharding_Exception("No instance of class $className");
                break;
        }

        //Put to list
        self::$instances[$className] = new $className($options);

        //Return object class
        return self::$instances[$className];
    }

    /**
     * Get Table ID by ID of ID Interger
     * @param <int> $key
     * @param <int> $maxTable
     * @return <int>
     */
    public static function getTableByID($key, $maxTable = 32)
    {
        //Check number
        if(Core_Valid::isNumber($key) === true)
        {
            $key = floor($key / 2000000);
        }
        else
        {            
            $key = sprintf("%u\n", crc32($key));
            $key = floor(abs($key) / 2000000);
        }
        
        //Return data
        return (abs($key) % $maxTable);
    }

    /**
     * Get Table redis sharding
     * @param <int> $key
     * @param <int> $maxTable
     * @param <int> $shardingRand
     * @return <int>
     */
    public static function getTableRedisByID($key, $maxTable = 256, $shardingRand = 100)
    {
        //Check number
        if(Core_Valid::isNumber($key) === true)
        {
            //Sharding key
            $key = ($key % $shardingRand) % $maxTable;
        }
        else
        {
            //Sharding key
            $key = sprintf("%u\n", crc32($key));
            $key = (abs($key) % $shardingRand) % $maxTable;
        }
                        
        //return data
        return $key;
    }
    
    /**
     * Clone function
     *
     */
    private final function __clone() {}
}

