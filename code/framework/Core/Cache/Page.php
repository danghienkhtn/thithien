<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Cache_Page
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to caching
 */
class Core_Cache_Page
{
    /**
     * Get page caching
     * @var <array> 
     */
    private $arrPageCaching = array();
    
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
     * Caching prefix data
     * @var <string> 
     */
    private $cachingPrefix = "Mobion";
    
    /**
     * Construct api server
     * @param type $sAddr
     * @param type $iPort 
     */
    protected final function __construct($_cachingPrefix, $_cachingInstance, $_arrPageCaching)
    {
        //Set router name data
        $this->cachingPrefix = $_cachingPrefix;

        //Set caching instance
        $this->cachingInstance = $_cachingInstance;
        
        //Set caching page
        if(sizeof($_arrPageCaching) > 0)
        {
            foreach($_arrPageCaching as $pageName)
            {
                $this->arrPageCaching[$pageName] = $pageName;
                $this->arrPageCaching[$pageName.'/'] = $pageName.'/';
            }
        }
    }

    /**
     * Get instance of class
     *
     * @param string $className
     * @return object
     */
    public final static function getInstance($_cachingPrefix, $_cachingInstance, $_arrPageCaching)
    {
        if(is_null(self::$_instance))
        {
            self::$_instance = new self($_cachingPrefix, $_cachingInstance, $_arrPageCaching);
        }
        return self::$_instance;
    }
    
    /**
     * Get prefix caching
     * @return <string> 
     */
    private function getPrefixPaging()
    {
        //Get browser information
        $browserInstance = Core_Browser::getInstance();

        //Check mobile header
        $isMobile = $browserInstance->isMobile();
                
        //Get server name
        $serverName = isset($_SERVER['SERVER_NAME'])?$_SERVER['SERVER_NAME']:'pc.';
        $serverName = trim($serverName);
        
        //If is mobile
        if($isMobile || (strpos($serverName, 'm.') === 0) || (strpos($serverName, 'www.m.') === 0))
        {
            return 'mobile';
        }
        
        //If is embed
        if((strpos($serverName, 'embed.') === 0) || (strpos($serverName, 'www.embed.') === 0))
        {
            return 'embed';
        }
        
        //Return default
        return 'pc';
    }

    /**
     * Get caching data
     * @return boolean 
     */
    public function getCaching($pageName=null)
    {
        //Check caching data
        if(is_null($this->cachingInstance))
        {
            return false;
        }

        //Set page name data
        $pageName = is_null($pageName) ? $_SERVER['REQUEST_URI'] : $pageName;
        
        //Parse page name data
        $arrData = explode('?', $pageName);
        
        //Replace some data
        $arrData[0] = str_replace("//", "/", $arrData[0]);
        
        //Check page exist in list control
        if(!isset($this->arrPageCaching[$arrData[0]]))
        {
            return false;
        }
        
        //Set page data
        $pageName = $this->getPrefixPaging() . ':' . $this->cachingPrefix . ':' . md5($arrData[0]);
        
        //Return data
        return $this->cachingInstance->read($pageName);
    }

    /**
     * Set caching router
     * @param <object> $arrData
     * @return boolean 
     */
    public function setCaching($responseData, $pageName=null)
    {
        //Check caching data
        if(is_null($this->cachingInstance))
        {
            return false;
        }
        
        //Set page name data
        $pageName = is_null($pageName) ? $_SERVER['REQUEST_URI'] : $pageName;
        
        //Parse page name data
        $arrData = explode('?', $pageName);
                
        //Replace some data
        $arrData[0] = str_replace("//", "/", $arrData[0]);
        
        //Check page exist in list control
        if(!isset($this->arrPageCaching[$arrData[0]]))
        {
            return false;
        }
        
        //Set page data
        $pageName = $this->getPrefixPaging() . ':' .  $this->cachingPrefix . ':' . md5($arrData[0]);

        //Return data
        return $this->cachingInstance->write($pageName, $responseData, $this->iTimeExpired);
    }

    /**
     * Clone function
     *
     */
    private final function __clone()
    {
        
    }
}

