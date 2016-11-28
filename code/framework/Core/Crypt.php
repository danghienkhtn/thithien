<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Crypt
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to crypt
 */
class Core_Crypt
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
            throw new Core_Crypt_Exception("No instance of empty class when call Core_Crypt");
        }

        //Switch to get classname
        switch(strtolower($className))
        {
            case 'base64':
                $className = 'Core_Crypt_Adapter_Base64';
                break;
            case 'xor':
                $className = 'Core_Crypt_Adapter_Xor';
                break;
            case 'mcrypt':
                $className = 'Core_Crypt_Adapter_Mcrypt';
                break;
            default:
                throw new Core_Crypt_Exception("No instance of class $className");
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

