<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Global
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Global object
 */
class Core_Global
{
    /**
	 * Zend_Config_Ini
	 * @var Zend_Config_Ini $configuration
	 */
    private static $configuration = null;
    
    /**
	 * Zend_Config_Ini
	 * @var Zend_Config_Ini $configuration
	 */
    private static $localeConfiguration = null;
        
    /**
	 * Job client
	 * @var Core_JobClient
	 */
   private static $jobClient = null;

    /**
	 * Fpt Cache
	 * @var Core_Cache $caching
	 */
    private static $arrCaching = null;
    
    /**
	 * Zend_Config_Ini
	 * @var Zend_Config_Ini $configuration
	 */
    private static $configKeyCaching = null;        
           
    /**
	 * Crypt instance
	 * @var Core_Crypt
	 */
    private static $cryptInstance = null;
    
    /**
	 * Zend_Config_Ini
	 * @var Zend_Config_Ini $configuration
	 */
    private static $arrViewsConfiguration = null;
    
    /**
	 * API tracking logging
	 * @var <array>
	 */
    public static $arrApiTracking = array();
     
    
    /* 
     * Chat Item db storage
     * @var Core_ID
     */
    private static $storageSlaveGlobal= null;  
    
     /**
     * List connection
     * @var <array>
     */
    private static $arrStorageGlobal = array();

    /**
     * Storage of master global
     * @var Zend_Db
     */
    private static $storageMasterGlobal = null;
    
    
     /**
     * Storage of master global
     * @var Zend_Db
     */
    private static $profileSearch = null;
    
      /**
     * Cache
     */
    private static $statsCachingInstance = null;
    
     
    /**
     * Instance Search
     * @var Zend_Db
     */
    private static $instanceRedis = null;
    
    private static $instanceMongo = null;
    
    /**
     * List connection
     * @var <array>
     */
    private static $arrStorageRedis= array();
    
    /**
     * List connection
     * @var <array>
     */
    private static $arrStorageMongo= array();
    
    private static $langConfiguration ='';
     
   
           
    /**
     * Get crypt instance
     * @return <Core_Crypt>
     */
    public static function getCrypt()
    {
    	//Get Ini Configuration
        if(is_null(self::$configuration))
        {
            self::$configuration = self::getApplicationIni();
        }

        //Get caching instance
        if(is_null(self::$cryptInstance))
        {
            self::$cryptInstance =  Core_Crypt::getInstance(self::$configuration->crypt->toArray());
        }

        //Return jobclient
        return self::$cryptInstance;
    }
    
    /**
     * Remove prefix of key caching
     * @param <string> $key
     * @param <string> $prefix
     * @return <string>
     */
    public static function removeKeyPrefix(&$item, $key, $prefix)
    {
        $item = str_replace($prefix, '', $item);
    }
        
    /**
     * Get job client
     * @return <Core_JobClient>
     */
    public static function getJobClient()
    {
    	//Get Ini Configuration
        if(is_null(self::$configuration))
        {
          self::$configuration = self::getApplicationIni();
        }

        //Get caching instance
        if(is_null(self::$jobClient))
        {
                self::$jobClient =  Core_JobClient::getInstance(self::$configuration->job->toArray());
        }

        //Return jobclient
        return self::$jobClient;
    }

    /**
     * Get job function
     * @param <string> $name
     * @return <string>
     */
    public static function getJobFunction($name)
    {
        //Get Ini Configuration
        if(is_null(self::$configuration))
        {
            self::$configuration = self::getApplicationIni();
        }

        //To array
        $jobConfiguration = self::$configuration->job->toArray();

        //Return job name
        return $jobConfiguration[$jobConfiguration['adapter']]['function'][$name];
    }

    /**
     * Get key prefix of caching
     * @param <string> $prefixKey
     * @return <string>
     */
    public static function getKeyPrefixCaching($prefixKey)
    {
        //Get Ini Configuration
        if(is_null(self::$configKeyCaching))
        {
            if(Zend_Registry::isRegistered(CACHING_CONFIG))
            {
                self::$configKeyCaching = Zend_Registry::get(CACHING_CONFIG);
            }
		    else
            {
                $cachingFile = 'caching-'.APP_ENV.'.ini';
                self::$configKeyCaching = new Zend_Config_Ini(APPLICATION_PATH.'/configs/'.$cachingFile);
                Zend_Registry::set(CACHING_CONFIG, self::$configKeyCaching);
            }
        }

        //Return prefix
        return self::$configKeyCaching->$prefixKey;
    }
    
    /**
     * Get application default configuration     
     * @return <object>
     */
    public static function getApplicationIni()
    {
        //Get Ini Configuration
        if(is_null(self::$configuration))
        {
		    if(Zend_Registry::isRegistered(APP_CONFIG))
            {
                self::$configuration = Zend_Registry::get(APP_CONFIG);
            }
		    else
            {
                $applicationFile = 'application-'.APP_ENV.'.ini';
                self::$configuration = new Zend_Config_Ini(APPLICATION_PATH.'/configs/'.$applicationFile);
                Zend_Registry::set(APP_CONFIG, self::$configuration);
            }
	}

        //Return data
        return self::$configuration;
    }

    /**
     * Get locales configuration     
     * @return <object>
     */
    public static function getLocalesIni($module='default')
    {
        //Get Ini Configuration
	if(is_null(self::$localeConfiguration))
	{
            if(Zend_Registry::isRegistered(LOCALE_CONFIG))
            {
                self::$localeConfiguration = Zend_Registry::get(LOCALE_CONFIG);
            }
            
	}
        else
        {
              //module
              if(empty($module))
              {
                  $module ='default';
              }
              
              $sLanguage = self::getCurrentLanguage();
              self::$localeConfiguration  = new Zend_Config_Ini(DATA_PATH.'/locales/'.$module.'/'.$sLanguage.'.ini');
              
               Zend_Registry::set(LOCALE_CONFIG, self::$localeConfiguration);
        }
       

        //Return data
        return self::$localeConfiguration;
    }  
    
    
    public static function getCurrentLanguage()
    {
        
        if(empty(self::$langConfiguration))
        {
        
            if(Zend_Registry::isRegistered(LANG_CONFIG))
            {
                   self::$langConfiguration = Zend_Registry::get(LANG_CONFIG);
            }
            
            if(empty(self::$langConfiguration))
            {
              self::$langConfiguration = 'en';
            }
        }
         
        //Return data
        return self::$langConfiguration;
    }
    
            
    
    /**
     * Redirect to url
     * @param <string> $url
     */
    public static function redirect($url)
    {
        //Flush buffer
        ob_end_flush();

        //Redirect url
        header('Location:'.$url);

        //Exit buffer
        exit(1);
    } 
            
    /**
     * Create all directory
     * @param <string> $pathname
     * @param <int> $mode
     * @return <bool>
     */
    public static function mkAllDir($pathname, $mode = 0755)
    {      
        is_dir(dirname($pathname)) || self::mkAllDir(dirname($pathname), $mode);
        $oldumask = umask(0);
        $bool = is_dir($pathname) || mkdir($pathname, $mode);
        umask($oldumask);
        return  $bool;
    }
    
    /**
     * Return hash string
     * @param <int> $objectID
     * @return <string> 
     */
    public static function getHashStringByID($objectID)
    {
        //Get md5 string
        $md5String = md5(Core_Utility::randomWord(5) . '.' . $objectID);
        
        //Hahs string
        $hashString = $md5String{0} . '/' . $md5String{2} . '/' . $md5String{6};
        $hashString .= '/' . $md5String{3} . $md5String{7} . $md5String{1};
        
        //Return data
        return $hashString;
    }
    
    /**
     * Get dile extension
     * @param <string> $fileName
     * @return <string> 
     */
    public static function getFileExt($fileName)
    {
        return substr(strrchr($fileName, '.'), 1);
    }
    
    /**
     * Find and replace string
     * @param <string> $mainString
     * @param <string> $findString
     * @param <string> $replaceString
     * @return <string> 
     */
    public static function findLastAndReplacePath($mainString, $findString, $replaceString)
    {
        //Get main find string
        $mainFindString = strrchr($mainString, $findString);
        $replaceString = '/' . $replaceString . $mainFindString;
        
        //Replace data
        return str_replace($mainFindString, $replaceString, $mainString);
    }
    
    /**
     * Escape string when query
     * @param <string> $strQuery
     * @return <string>
     */
    public static function escapeQueryString($strQuery)
    {          
        $strQuery = iconv("UTF-8", "UTF-8//IGNORE", $strQuery);        
        $pattern = '/(\+|-|&&|\|\||!|\(|\)|\{|}|\[|]|\^|"|~|\*|\?|:|\\\)/';
        $replace = '\\\$1';
        $strQuery = preg_replace($pattern, $replace, $strQuery);
        return preg_replace('/[;,:,\\,\[,\],\{,!,^,\}]|OR|AND/', '', $strQuery) ;
    }
    
    /**
     * Fix browser data
     * @return <string> 
     */
    public static function fixBrowser()
    {
        //Get browser information
        $browserInstance = Core_Browser::getInstance();
               
        //Set default data
        $arrData = array(
            'isHTML5'       => '<script type="text/javascript">var isHTML5 = mb.util.isHTML5();</script>',
            'isIE'          =>  '<scrip$controllert type="text/javascript">var isIE = 0;</script>',
            'isIEVersion'   =>  10
        );
        
        //Check browser
        if($browserInstance->getBrowser() == Core_Browser::BROWSER_IE)
        {
            //Get IE version
            $isVersion = (int)$browserInstance->getVersion();
            
            //Put version
            $arrData['isIEVersion'] = $isVersion;
                        
            //Check version
            switch($isVersion)
            {
                case 9:
                    $arrData['isIE'] = '<script type="text/javascript">var isIE = 1;</script><link href="'.self::$configuration->app->static->frontend->css.'/styleIE9.css" media="screen, projection" rel="stylesheet" type="text/css" />';
                    break;
                case 8:
                    $arrData['isIE'] = '<script type="text/javascript">var isIE = 1;</script><link href="'.self::$configuration->app->static->frontend->css.'/styleIE8.css" media="screen, projection" rel="stylesheet" type="text/css" />';
                    break;
                default:
                    $arrData['isIE'] = '<script type="text/javascript">var isIE = 1;</script><link href="'.self::$configuration->app->static->frontend->css.'/styleIE7.css" rel="stylesheet" type="text/css" />';
                    break;
            }
        }  
                  
        //Return data
        return $arrData;
    }
    
    /**
     * Add key and value which registered to view
     * @param <Zend_View> $view
     * @param <array> $arrParams
     */
    public static function addToView(&$view, $arrParams=array())
    {
        //Check size params
        if(sizeof($arrParams) > 0)
        {
            foreach($arrParams as $key => $value)
            {
                $view->$key = $value;
            }
        }        
    }
    
    /**
     * Add key and value which registered to view
     * @param <Zend_View> $htmlView     
     */
    public static function addToDefaultView(&$htmlView, $sModule='default')
    {
        //Get Ini Configuration
        if(is_null(self::$arrViewsConfiguration))
        {
                if(Zend_Registry::isRegistered(VIEWS_CONFIG))
                {
                    self::$arrViewsConfiguration = Zend_Registry::get(VIEWS_CONFIG);
                }
        }
        
        //Assign views
        self::addToView($htmlView, self::$arrViewsConfiguration);
       
        //Add view phtml
        $htmlView->addBasePath(APPLICATION_PATH .'/modules/'.$sModule.'/views');

        //Set partials path
        $htmlView->addScriptPath(APPLICATION_PATH .'/modules/'.$sModule.'/views/partials');
    }
    
    /**
     * Get system tracking
     * @param <string> $iTimeRender
     * @return <string> 
     */
    public static function getSystemTracking($iTimeRender)
    {
        //Get key of register        
        $print = '<br/><br/><br/><table style="width:800px;" border="1" cellspacing="2" cellpadding="2"><tr><th colspan="4" bgcolor=\'#dddddd\'>Memory Block</th></tr>';
            
        //Get system information        
        $print .= '<tr><td align="left" colspan="4">The information about the operating system : ' . php_uname() . ' </td></tr>';
        
        //Get memony Peak  
        $iMemoryPeak = memory_get_peak_usage(true);
        $iMemoryPeak = $iMemoryPeak / (1024*1024);
        $print .= '<tr><td align="left" colspan="4">Total Peak Memory : ' . $iMemoryPeak . ' MB</td></tr>';
        
        //Get amount memory
        $iMemoryAmount = memory_get_usage(true);
        $iMemoryAmount = $iMemoryAmount / (1024*1024);
        $print .= '<tr><td align="left" colspan="4">Total Amount Memory : ' . $iMemoryAmount . ' MB</td></tr>';
        
        //Get current Time render       
        $print .= '<tr><td align="left" colspan="4">Total time render (include API timer) : ' . $iTimeRender . ' (miliseconds)</td></tr>';
        
        //Gets the name of the owner of the current PHP script
        $print .= '<tr><td align="left" colspan="4">The owner of the current PHP script : ' . get_current_user() . ' </td></tr>';
        
        //Get the last modification of the current page
        $print .= '<tr><td align="left" colspan="4">The last modification of the current page : ' . date("F d Y H:i:s.", getlastmod()) . ' </td></tr>';
        
        //Gets the inode of the current script
        $print .= '<tr><td align="left" colspan="4">The inode of the current script : ' . getmyinode() . ' </td></tr>';
        
        //Get the type of interface between web server and PHP
        $print .= '<tr><td align="left" colspan="4">The type of interface between web server and PHP : ' . php_sapi_name() . ' </td></tr>';
        
        //Gets the current resource usages
        $arrResources = getrusage();
        $print .= '<tr><td align="left" colspan="4">Number of swaps : ' . $arrResources["ru_nswap"] . ' </td></tr>';
        $print .= '<tr><td align="left" colspan="4">Number of page faults : ' . $arrResources["ru_majflt"] . ' </td></tr>';
        $print .= '<tr><td align="left" colspan="4">User time used (seconds) : ' . $arrResources["ru_utime.tv_sec"] . ' </td></tr>';
        $print .= '<tr><td align="left" colspan="4">User time used (microseconds) : ' . $arrResources["ru_utime.tv_usec"] . ' </td></tr>';
        
        //Add data
        $print .= "</table>";

        //Return data
        return $print;        
    }
    
    /**
     * Get api tracking
     * @return <string> 
     */
    public static function getApiTracking()
    {
        //Get key of register        
        $number = 0;
        $iTotalTime = 0;
        $print = '<br/><br/><br/><table style="width:800px;" border="1" cellspacing="2" cellpadding="2"><tr><th colspan="4" bgcolor=\'#dddddd\'>API Tracking Block</th></tr>';
        $print .= '<tr><td align="center">URL</td><td align="center" width="100">TIME (miliseconds)</td><td align="center">PARAMS</td><td align="center">RESPONSE</td></tr>';
        
        //Loop keys
        foreach(self::$arrApiTracking as $sUrl => $arrData)
        {
            $print .= '<tr><td align="center">'.$sUrl.'</td><td align="center">'.$arrData['time'].'</td><td align="left">'.Zend_Json::encode($arrData['params']).'</td><td align="left">'.Zend_Json::encode($arrData['response']).'%</td></tr>';
            $number++;
            $iTotalTime += $arrData['time'];
        }

        //If number = 0
        if($number == 0)
        {
            $print .= '<tr><td align="center" colspan="4">No index any key for caching</td></tr>';
        }
        else
        {
            $print .= '<tr><td align="left" colspan="4">Total Time ApiCaller : '.$iTotalTime.' (miliseconds)</td></tr>';
        }
          
        //Add data
        $print .= "</table>";

        //Return data
        return $print;        
    }
    
    /**
     * Parse HTML content
     * @param <string> $sContent
     * @return <array> 
     */
    public static function getHtmlContent($sContent)
    {
        //Set default data
        $arrJsFunctions = array();
        
        //Get javascript content
        preg_match_all('#<script[^>]*>.*?</script>#is', $sContent, $arrJsFunctions);

        //Set default data
        $jsFunction = '';
        
        //Check javascript list        
        if(isset($arrJsFunctions[0]))
        {            
            //Remove javascript content
            $jsFunction = implode('', $arrJsFunctions[0]);
            $jsFunction = preg_replace('#<script[^>]*>#is', '', $jsFunction);
            $jsFunction = str_replace('</script>', '', $jsFunction);
			$jsFunction = trim($jsFunction);
                    
            //Remove javascript content
            $sContent = str_replace($arrJsFunctions[0], '', $sContent);
        }
        
        //Return data
        return array(
            'content'       =>  $sContent,
            'jsCallback'    =>  $jsFunction
        );
    }
    
    
    
    /**
     * 
     * @param type $controller
     * @param type $sForumToken
     * @return boolean
     */
    public static function checkLogin($controller='')
    {
        //Check cookie data
        $sAuthToken = Core_Cookie::getCookie(AUTH_LOGIN_TOKEN);
   
        //Check token
        if (empty($sAuthToken)) {
            return false;
        }
        

        //Check Session
        if (!isset($_SESSION[$sAuthToken]) || !isset($_SESSION[$sAuthToken]['email']))
        {
            //Check controller
            if ($controller != 'logout')
            {
                $instanceAccount = Account::getInstance();
                
                //Check token
                $checkResponse = $instanceAccount->checkTokenEx($sAuthToken);

                //Check data
                if(isset($checkResponse->profile))
                {
                    //Set session
                    $_SESSION[$sAuthToken] = array(
                        'accountID' => $checkResponse->profile->accountid,
                        'nickName'  => $checkResponse->profile->nickname,
                        'avatar'    => $instanceAccount->buildAvatar($checkResponse->profile->avatar_informal_photoid, $checkResponse->profile->avatar_informal_ext, $checkResponse->profile->gender),
                        'token'     => $sAuthToken,
                        'email'     => $checkResponse->profile->email
                    );
                }
                else
                {
                    return false;
                }
            }
            else{
                return false;
            }
        }

        //Return data
        return $_SESSION[$sAuthToken];
    }
    
    
     /**
     * Get global storage master instance
     * @return <Zend_Db>
     */
    public static function getDbGlobalMaster()
    {
        //Get Ini Configuration
        if(is_null(self::$configuration))
        {
            self::$configuration = self::getApplicationIni();
        }

        //Get storage instance
        if(!isset(self::$storageMasterGlobal) || !(self::$storageMasterGlobal->isConnected()))
        {
            //Set UTF-8 Collate and Connection
            $options_storage = self::$configuration->database->global->master->toArray();

            //Set params
            if(empty($options_storage['params']['driver_options']))
            {
                $options_storage['params']['driver_options'] = array(
                    1002 => 'SET NAMES \'utf8\'',
                    12 => 0
                );
            }

            //Create object to Connect DB
            self::$storageMasterGlobal = Zend_Db::factory($options_storage['adapter'], $options_storage['params']);

            //Set some attributes for performance
            self::$storageMasterGlobal->getConnection()->setAttribute(PDO::ATTR_PERSISTENT, true);
            self::$storageMasterGlobal->getConnection()->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
            self::$storageMasterGlobal->getConnection()->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            self::$storageMasterGlobal->getConnection()->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);

            //Push to queue
            self::$arrStorageGlobal[] = self::$storageMasterGlobal;
        }

        //Return storage
        return self::$storageMasterGlobal;
    }
    
    /**
     * Get global storage master instance     
     * @return <Zend_Db>
     */
    public static function getDbGlobalSlave()
    { 
        //Get Ini Configuration
        if(is_null(self::$configuration))
        {
            self::$configuration = self::getApplicationIni();
        }
        
        //Get storage instance
        if(is_null(self::$storageSlaveGlobal) || !(self::$storageSlaveGlobal->isConnected()))
        {
            //Set UTF-8 Collate and Connection
            $options_storage = self::$configuration->database->global->toArray();
            $options_storage = $options_storage['slave'];

            //Set params
            if(empty($options_storage['params']['driver_options']))
            {
                $options_storage['params']['driver_options'] = array(
                    1002 => 'SET NAMES \'utf8\'',
                    12 => 0
                );
            }

            //Create object to Connect DB
            self::$storageSlaveGlobal = Zend_Db::factory($options_storage['adapter'], $options_storage['params']);

            //Set some attributes for performance
            self::$storageSlaveGlobal->getConnection()->setAttribute(PDO::ATTR_PERSISTENT, true);
            self::$storageSlaveGlobal->getConnection()->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
            self::$storageSlaveGlobal->getConnection()->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            self::$storageSlaveGlobal->getConnection()->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);

            //Push to queue
            self::$arrStorageGlobal[] = self::$storageSlaveGlobal;
            
        }

        //Return storage
        return self::$storageSlaveGlobal;
    }
    
    
    /**
     * Close all mysqli connection
     * @return <bool>
     */
    public static function closeAllDb()
    {
        //Loop to close connection
        if(sizeof(self::$arrStorageGlobal) > 0)
        {
            //Loop to close connection
            foreach (self::$arrStorageGlobal as $storage)
            {
                //Try close
                try
                {
                    if(is_object($storage) && ($storage->isConnected()))
                    {
                        $storage->closeConnection();
                    }
                }
                catch(Zend_Exception $ex)
                {
                    echo "=== Close DB error :" . $ex->getMessage() . "\n";
                }
            }

            //Set all list connection
            self::$arrStorageGlobal = array();
        }

        //Return default
        return true;
    }
    
    /**
     *
     * @return type
     */
    public static function getProfileSearch()
    {
        //Get Ini Configuration
        if (is_null(self::$configuration)) {
            self::$configuration = self::getApplicationIni();
        }
        

        //Get search instance
        self::$profileSearch = Core_Search::getInstance(self::$configuration->search->profile->toArray());

        //Return caching
        return self::$profileSearch;
    }
    
      
    /**
     * Get caching statistic instance
     * @return <Core_Cache>
     */
    public static function getCacheInstance()
    {
        //Get Ini Configuration
        if(is_null(self::$configuration))
        {
            self::$configuration = self::getApplicationIni();
        }

        //Get node instance
        self::$statsCachingInstance = Core_Cache::getInstance(self::$configuration->caching->statistic->server->toArray());
//        var_dump( Core_Cache::getInstance(self::$configuration->caching->statistic->server->toArray()));
        //Return id instance
        return self::$statsCachingInstance;
    }
    
    
    
     /**
     *
     * @return instance Redis
     */
    public static function getRedisInstance()
    {
        
        //check exists
        if(is_object(self::$instanceRedis) && !is_null(self::$instanceRedis) && self::$instanceRedis->ping())
        {
             return self::$instanceRedis;
        }
        
         
        //Get Ini Configuration
        if (is_null(self::$configuration)) {
            self::$configuration = self::getApplicationIni();
        }


        $options = self::$configuration->system->nosql->redis->toArray();

        if(!empty($options))
        {
             //Check host
            if(empty($options['host']))
            {
                throw new Core_Nosql_Exception('Input host of redis server.');
            }

            //Check port
            if(empty($options['port']))
            {
                throw new Core_Nosql_Exception('Input port of redis server.');
            }


             //Check timout connect
            if(empty($options['timeout']))
            {
                $options['timeout'] = 0;
            }

            //Set connection options
            self::$instanceRedis = new Redis();

            //Check timeout to connect
            if($options['timeout'] > 0)
            {
                self::$instanceRedis->connect($options['host'], $options['port'], $options['timeout']);
            }
            else
            {
                self::$instanceRedis->connect($options['host'], $options['port']);
            }  
            
            //Store connection
            $arrStorageRedis[] = self::$instanceRedis;
        }
         
                 
        //Return caching
        return self::$instanceRedis;
    }
    
    /**
     *
     * @return instance Mongo
     */
    public static function getMongoInstance()
    {
    
    
    	//check exists
    	if(!is_null(self::$instanceMongo))
    	{
    		return self::$instanceMongo;
    	}
    
    	 
    	//Get Ini Configuration
    	if (is_null(self::$configuration)) {
    		self::$configuration = self::getApplicationIni();
    	}
    
    
    	$options = self::$configuration->system->nosql->mongo->toArray();
    
    	if(!empty($options))
    	{
    		//Check host
    		if(empty($options['host']))
    		{
    			throw new Core_Nosql_Exception('Input host of mongo server.');
    		}
    
    		//Check port
    		if(empty($options['port']))
    		{
    			throw new Core_Nosql_Exception('Input port of mongo server.');
    		}
    		
    		if(empty($options['dbname']))
    		{
    			throw new Core_Nosql_Exception('Input dbname of mongo server.');
    		}
    		
    		if(empty($options['username']))
    		{
    			throw new Core_Nosql_Exception('Input username of mongo server.');
    		}
    		
    		if(empty($options['password']))
    		{
    			throw new Core_Nosql_Exception('Input password of mongo server.');
    		}
   
    		$sConnectionString = 'mongodb://'.$options['username'].':'.$options['password'].'@'.$options['host'].':'.$options['port'];
    		//Set connection options
    		self::$instanceMongo = new MongoClient($sConnectionString);
    
    		//print_r(self::$instanceMongo);
    
    		//Store connection
    		$arrStorageMongo[] = self::$instanceMongo;
    	}
    	 
    	 
    	//Return caching
    	return self::$instanceMongo;
    }
    
      /**
     * Add prefix of key caching
     * @param <string> $key
     * @param <string> $prefix
     * @return <string>
     */
    public static function addKeyPrefix(&$item, $key, $prefix) {
        $item = $prefix . $item;
    }
    
    
    public static function closeAllRedis()
    {
        //Loop and delete object
        foreach(self::$arrStorageRedis as $redisObject)
        {
            unset($redisObject);
        }
        
        //Reset all
        self::$arrStorageRedis = array();
    }
    
    public static function closeAllMongo()
    {
    	//Loop and delete object
    	foreach(self::$arrStorageMongo as $mongoObject)
    	{
    		$mongoObject->close();
    	}
    
    	//Reset all
    	self::$arrStorageMongo = array();
    }
    
}

