<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_FacebookSearch
 * @version     :   201008
 * @copyright   :   My company
 * @todo        :   Using to search friend from facebook
 */
class Core_FacebookSearch
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
    private final function __construct(){}

    /**
     * Get instance of class
     *
     * @param int    $appid
     * @param string $secret
     * @param array  $options
     * @return object
     */
    public final static function getInstance($appid, $secret, $caching, $options = array())
    {
        //Check class name
        $className = $options['adapter'];

        //If empty className
        if(empty($className))
        {
            throw new Core_FacebookSearch_Exception("No instance of empty class when call Core_FacebookSearch");
        }

        //If empty fbAppID
        if(empty($appid))
        {
            throw new Core_FacebookSearch_Exception("Input Facebook AppID for Core_FacebookSearch.");
        }

        //If empty fbSecret
        if(empty($secret))
        {
            throw new Core_FacebookSearch_Exception("Input Facebook Secrect for Core_FacebookSearch.");
        }

        //If empty fbSecret
        if(is_null($caching) || !is_object($caching))
        {
            throw new Core_FacebookSearch_Exception("Input Caching Object Instance for Core_FacebookSearch.");
        }

        //Switch to get classname
        switch(strtolower($className))
        {
            case 'solr':
                    $className = 'Core_FacebookSearch_Adapter_Solr';
                    break;
            default:
                    throw new Core_Search_Exception("No instance of class $className");
                    break;
        }

        //Put to list
        if(!isset(self::$instances[$className]))
        {
            self::$instances[$className] = new $className($appid, $secret, $caching, $options);
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

