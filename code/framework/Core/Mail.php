<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Mail
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to send email
 */
class Core_Mail
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
            throw new Core_Mail_Exception("No instance of empty class when call Core_Mail");
        }

        //Switch to get classname
        switch(strtolower($className))
        {
            case 'basic':
                $className = 'Core_Mail_Adapter_Basic';
                break;
            case 'smtp':
                $className = 'Core_Mail_Adapter_Smtp';
                break;
            default:
                throw new Core_Mail_Exception("No instance of class $className");
                break;
        }

        //Put to list
        self::$instances[$className] = new $className($options);

        //Return object class
        return self::$instances[$className];
    }

    /**
     * Clone function
     *
     */
    private final function __clone() {}
}

