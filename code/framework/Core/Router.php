<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Router
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to router
 */
class Core_Router
{

    /**
     * Create router name
     * @var <string> 
     */
    private $routerName = "ROUTER_";

    /**
     * Caching instance
     * @var <object> 
     */
    private $cachingInstance = null;

    /**
     * Router instance
     * @var <object> 
     */
    protected static $_instance = null;
    
    /**
     * Time expired
     * @var <int> 
     */
    private $iTimeExpired = 604800;

    /**
     * Construct api server
     * @param type $sAddr
     * @param type $iPort 
     */
    protected final function __construct($_routerName, $_cachingInstance)
    {
        //Set router name data
        $this->routerName .= $_routerName;

        //Set caching instance
        $this->cachingInstance = $_cachingInstance;
    }

    /**
     * Get instance of class
     *
     * @param string $className
     * @return object
     */
    public final static function getInstance($_routerName, $_cachingInstance)
    {
        if(is_null(self::$_instance))
        {
            self::$_instance = new self($_routerName, $_cachingInstance);
        }
        return self::$_instance;
    }

    /**
     * Get router name data
     * @return <string> 
     */
    public function getRouterName()
    {
        return $this->routerName;
    }

    /**
     * Get caching data
     * @return boolean 
     */
    public function getCaching()
    {
        //Check caching data
        if(is_null($this->cachingInstance))
        {
            return false;
        }

        //Return data
        return $this->cachingInstance->read($this->routerName);
    }

    /**
     * Set caching router
     * @param <object> $arrData
     * @return boolean 
     */
    public function setCaching($arrData)
    {
        //Check caching data
        if(is_null($this->cachingInstance))
        {
            return false;
        }

        //Return data
        return $this->cachingInstance->write($this->routerName, $arrData, $this->iTimeExpired);
    }

    /**
     * Clone function
     *
     */
    private final function __clone()
    {
        
    }

}

