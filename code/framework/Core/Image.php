<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Image
 * @version     :   201008
 * @copyright   :   My company
 * @todo        :   Using to images
 */
class Core_Image
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
                    throw new Core_Image_Exception("No instance of empty class when call Core_Image");
            }

            //Switch to get classname
            switch(strtolower($className))
            {
                    case 'magick':
                            $className = 'Core_Image_Adapter_Imagemagick';
                            break;
                    case 'gd2':
                            $className = 'Core_Image_Adapter_Gd2';
                            break;
                    default:
                            throw new Core_Image_Exception("No instance of class $className");
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