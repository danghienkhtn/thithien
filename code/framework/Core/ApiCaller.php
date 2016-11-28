<?php
/**
 * @author      :   Linuxpham test
 * @name        :   Core_Valid
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to valid data
 */
class Core_ApiCaller
{
    /**
     *
     * @var <object>
     */
    protected static $_instance = null;
    
    /**
     * Get curl instance
     * @var <Curl> 
     */
    private static $curlInstance = null;
    
    /**
     * Get CLI control 
     */
    private static $isCli = false;
    
    /**
     *Set timeout
     * @var type 
     */
    private static $isTimeout = 5;
    
    /**
     * Constructor of class
     * we don't permit an explicit call of the constructor! (like $v = new Singleton())
     */
    protected function __construct()
    {        
        self::getPoolCurl();
    }
    
    /**
     * Destructor of class
     */
    public function __destruct()
    {
        //Nothing
    }
    
    /**
     * Get singletom instance
     * @return <object>
     */
    public final static function getInstance()
    {        
        //Check CLI console
        self::$isCli = Core_Valid::isCli();
        
        //Check instance with not CLI
        if(((self::$_instance instanceof self) === true) && (!self::$isCli))
        {
            //Return instance
            return self::$_instance;
        }
        
        //Init instance
        self::$_instance = new self();

        //Return instance
        return self::$_instance;
    }
    
    /**
     * Get authorization header
     * @param <array> $arrHeader
     * @return <array>
     */
    private function getAuthorizationHeader($arrHeader)
    {
        //Set authoration
        $arrOutPut = array('Content-Type:application/x-www-form-urlencoded;charset=utf-8');
        
        //Loop data
        foreach($arrHeader as $k => $v)
        {
            //If is array
            if(is_array($v))
            {
                continue;
            }
            
            //Add data
            $arrOutPut[] = self::getUrlEncodeRfc3986($k) .':' . self::getUrlEncodeRfc3986($v) .'';
        }
        
        //Return data
        return $arrOutPut;
    }
    
    /**
     * Get curl instance
     * @return <Curl> 
     */
    protected static function getPoolCurl()
    {
        //Check instance
        if(is_object(self::$curlInstance) && (!self::$isCli))
        {
            //Return instance
            return self::$curlInstance;
        }

        //Init curl
        self::$curlInstance = curl_init();
        
        //Return instance
        return self::$curlInstance;
    }
    
    /**
     * Close instance of curl 
     */
    protected static function closePoolCurl()
    {
        //Close curl instance
        if(is_object(self::$curlInstance) && (self::$isCli))
        {
            //Try to close curl instance
            @curl_close(self::$curlInstance);
            
            //Set to null pointer
            self::$curlInstance = null;
        }
    }
    
    /**
     * Fixed json string data
     * @param <string> $jsonData
     * @return <string> 
     */
    protected static function getFixedJson($jsonData)
    {
        /*
        $jsonData = preg_replace('/,\s*([\]}])/m', '$1', $jsonData);
        return preg_replace('/:\s*\'(([^\']|\\\\\')*)\'\s*([},])/e', "':'.json_encode(stripslashes('$1')).'$3'", $jsonData);
        */
        return $jsonData;
    }
    
    /**
     * Url encode rfc3986 protocol
     * @param <object> $input
     * @return <object>
     */
    protected static function getUrlEncodeRfc3986($input)
    {
        if(is_array($input))
        {
            return array_map(array('Core_ApiCaller', 'getUrlEncodeRfc3986'), $input);
        }
        else if(is_scalar($input))
        {
            return str_replace('+',' ',	str_replace('%7E', '~', rawurlencode($input)));
        }

        //Return data
        return '';
    }
    
    /**
     * Build http query
     * @param <array> $params
     * @return <string>
     */
    private static function getBuildHttpQuery(&$params)
    {
        //Check params
        if(!is_array($params) || sizeof($params) == 0)
        {
            return '';
        }

        //Filter data
        $keys 	= self::getUrlEncodeRfc3986(array_keys($params));
        $values = self::getUrlEncodeRfc3986(array_values($params));
        $params = array_combine($keys, $values);
        uksort($params, 'strcmp');

        //Loop data
        $pairs = array();
        foreach($params as $parameter => $value)
        {
            if(is_array($value))
            {
                natsort($value);
                foreach($value as $duplicate_value)
                {
                    $pairs[] = $parameter . '=' . $duplicate_value;
                }
            } 
            else
            {
                $pairs[] = $parameter . '=' . $value;
            }
        }

        //Return data
        return implode('&', $pairs);
    }
    
    /**
     * Get multi data from API
     * @param <string> $url
     * @param <array> $arrParams
     * @return <int> 
     */
    public function getMultiData($arrUrl, $arrListParams=array())
    {
        //Set data callback
        $arrData = array(
            'error'         =>  0,
            'response'      =>  array()
        );
        
        //Trying to get data
        try
        {
            //Init curl list
            $curlListInstance = curl_multi_init();

            //Array list current 
            $arrListCurl = array();

            //Loop to put data
            foreach($arrUrl as $sKey => $sUrl)
            {
                //Trim URL
                $sUrl = trim($sUrl);

                //Get params
                $arrParams = isset($arrListParams[$sKey])?$arrListParams[$sKey]:array();

                //Check params
                if(!is_array($arrParams))
                {
                    $arrParams = array();
                }

                //Check params length
                if(sizeof($arrParams) > 0)
                {
                    $sUrl .= '?' . self::getBuildHttpQuery($arrParams);
                }

                //Init curl
                $itemCurlInstance = curl_init();

                //Push list curl
                $arrListCurl[(string)$itemCurlInstance] = $sKey;

                //Set curl options        
                curl_setopt($itemCurlInstance, CURLOPT_USERAGENT, 'Mobion Core library php ' . phpversion());
                curl_setopt($itemCurlInstance, CURLOPT_URL, $sUrl);        
                curl_setopt($itemCurlInstance, CURLOPT_BUFFERSIZE, 8192);
                curl_setopt($itemCurlInstance, CURLOPT_POST, 0);
                curl_setopt($itemCurlInstance, CURLOPT_ENCODING, '');
                curl_setopt($itemCurlInstance, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
                curl_setopt($itemCurlInstance, CURLOPT_FAILONERROR, true);
                curl_setopt($itemCurlInstance, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($itemCurlInstance, CURLOPT_AUTOREFERER, true);
                curl_setopt($itemCurlInstance, CURLOPT_RETURNTRANSFER,true);
                curl_setopt($itemCurlInstance, CURLOPT_HEADER, false);
                curl_setopt($itemCurlInstance, CURLOPT_TIMEOUT, self::$isTimeout);
                
                //Add handler
                curl_multi_add_handle($curlListInstance, $itemCurlInstance);
            }

            //Set running status
            $isRunning = 0;

            //Execute the handles 
            do
            {
                //Exec URL
                $iCode = curl_multi_exec($curlListInstance, $isRunning);

                //Check code
                if($iCode == CURLM_OK)
                {
                    //Loop to read data
                    while($arrDone = curl_multi_info_read($curlListInstance))
                    {                       
                        //Set current curl instance
                        $itemCurlInstance = $arrDone['handle'];

                        //Get information of curl request
                        $httpCodeInfo = curl_getinfo($itemCurlInstance, CURLINFO_HTTP_CODE);

                        //Get key data
                        $sKey = $arrListCurl[(string)$itemCurlInstance];

                        //Check data information
                        if($httpCodeInfo == 200)
                        {
                            //Get response data
                            $arrData['response'][$sKey] = curl_multi_getcontent($itemCurlInstance);
                            
                            //Escape data HTML
                            $arrData['response'][$sKey] = self::getFixedJson($arrData['response'][$sKey]);
                            
                            //Remove handler
                            curl_multi_remove_handle($curlListInstance, $itemCurlInstance);
                        }
                        else
                        {
                            //Get response data
                            $arrData['response'][$sKey] = Zend_Json::encode(
                                array(
                                    'status' => 'failure'
                                )
                            );
                        }
                    }
                }
                elseif($iCode != CURLM_CALL_MULTI_PERFORM)
                {
                    $arrData['error'] = 1;
                }
            } while($isRunning);

            //Close curl
            curl_multi_close($curlListInstance);
        }
        catch(Zend_Exception $ex)
        {
            echo " === Error API : " . $ex->getMessage() . "\n";
        }   
        
        //Return data
        return $arrData;
    }
    
    /**
     * Get data from API
     * @param <string> $url
     * @param <array> $arrParams
     * @return <int> 
     */
    public function getData($url, $arrParams=array())
    {        
        //Set data callback
        $arrData = array(
            'error'         =>  0,
            'response'      =>  array(
                'status'    =>  'failure'
            )
        );
        
        //Trim URL
        $url = trim($url);
                
        //Check params
        if(!is_array($arrParams))
        {
            $arrParams = array();
        }

        //Check params length
        if(sizeof($arrParams) > 0)
        {
            $url .= '?' . self::getBuildHttpQuery($arrParams);
        }
        
        //Trying to get data
        try
        {
            //Get pool curl
            $curlInstance = self::getPoolCurl();

            //Set curl options        
            curl_setopt($curlInstance, CURLOPT_USERAGENT, 'Mobion Core library php ' . phpversion());
            curl_setopt($curlInstance, CURLOPT_BUFFERSIZE, 8192);
            curl_setopt($curlInstance, CURLOPT_URL, $url);        
            curl_setopt($curlInstance, CURLOPT_POST, 0);
            curl_setopt($curlInstance, CURLOPT_ENCODING, '');
            curl_setopt($curlInstance, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($curlInstance, CURLOPT_FAILONERROR, true);
            curl_setopt($curlInstance, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curlInstance, CURLOPT_AUTOREFERER, true);
            curl_setopt($curlInstance, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($curlInstance, CURLOPT_HEADER, false);
            curl_setopt($curlInstance, CURLOPT_TIMEOUT, self::$isTimeout);    

            //Get content
            $arrData['response'] = curl_exec($curlInstance);

            //Escape data HTML
            $arrData['response'] = self::getFixedJson($arrData['response']);
            
            //Check error
            if(curl_error($curlInstance))
            {  
                $arrData['error'] = 1;
            }
        }
        catch(Zend_Exception $ex)
        {
            echo " === Error API : " . $ex->getMessage() . "\n";
        }   

        //Close curl instance
        self::closePoolCurl();
        
        //Return data
        return $arrData;
    }
    
    /**
     * Post data
     * @param <string> $url
     * @param <array> $arrParams
     * @return <int> 
     */
    public function postData($url, $arrParams=array())
    {
        //Set data callback
        $arrData = array(
            'error'         =>  0,
            'response'      =>  array(
                'status'    =>  'failure'
            )
        );
        
        //Trim URL
        $url = trim($url);  
        
        //Check params
        if(!is_array($arrParams))
        {
            $arrParams = array();
        }
                
        //Check params length
        if(sizeof($arrParams) > 0)
        {
            //Set data
            $arrParams = array(
                'params'    =>  Zend_Json::encode($arrParams)
            );
            
            //Build query
            $arrParams = self::getBuildHttpQuery($arrParams);
        }
        
        //Trying to get data
        try
        {
            //Get pool curl
            $curlInstance = self::getPoolCurl();

            //Set curl options
            curl_setopt($curlInstance, CURLOPT_USERAGENT, 'Mobion Core library php ' . phpversion());
            curl_setopt($curlInstance, CURLOPT_BUFFERSIZE, 8192);
            curl_setopt($curlInstance, CURLOPT_URL, $url);        
            curl_setopt($curlInstance, CURLOPT_POST, 1);
            curl_setopt($curlInstance, CURLOPT_POSTFIELDS, $arrParams);
            curl_setopt($curlInstance, CURLOPT_ENCODING, '');
            curl_setopt($curlInstance, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($curlInstance, CURLOPT_FAILONERROR, true);
            curl_setopt($curlInstance, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curlInstance, CURLOPT_AUTOREFERER, true);
            curl_setopt($curlInstance, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($curlInstance, CURLOPT_HEADER, false);
            curl_setopt($curlInstance, CURLOPT_TIMEOUT, self::$isTimeout);

            //Set data callback
            $arrData = array(
                'error'         =>  0,
                'response'      =>  array()
            );

            //Get content
            $arrData['response'] = curl_exec($curlInstance);

            //Escape data HTML
            $arrData['response'] = self::getFixedJson($arrData['response']);
            
            //Check error
            if(curl_error($curlInstance))
            {  
                $arrData['error'] = 1;
            }
        }
        catch(Zend_Exception $ex)
        {
            echo " === Error API : " . $ex->getMessage() . "\n";
        }

        //Close curl instance
        self::closePoolCurl();
        
        //Return data
        return $arrData;
    }
    
    /**
     * Get data from API version 2
     * @param <string> $url
     * @param <array> $arrParams
     * @return <int> 
     */
    public function getDataV2($url, $arrParams=array(), $arrHeader=array())
    {        
        //Set data callback
        $arrData = array(
            'error'         =>  0,
            'response'      =>  array(
                'status'    =>  'failure'
            )
        );
        
        //Trim URL
        $url = trim($url);
                
        //Check params
        if(!is_array($arrParams))
        {
            $arrParams = array();
        }

        //Check params length
        if(sizeof($arrParams) > 0)
        {
            $url .= '?' . self::getBuildHttpQuery($arrParams);
        }
        
        //Trying to get data
        try
        {
            //Get pool curl
            $curlInstance = self::getPoolCurl();

            //Set Aouth header
            if(sizeof($arrHeader) > 0)
            {
                curl_setopt($curlInstance, CURLOPT_HTTPHEADER, $this->getAuthorizationHeader($arrHeader));
            }

            //Set curl options        
            curl_setopt($curlInstance, CURLOPT_USERAGENT, 'Mobion Core library php ' . phpversion());
            curl_setopt($curlInstance, CURLOPT_BUFFERSIZE, 8192);
            curl_setopt($curlInstance, CURLOPT_URL, $url);        
            curl_setopt($curlInstance, CURLOPT_POST, 0);
            curl_setopt($curlInstance, CURLOPT_ENCODING, '');
            curl_setopt($curlInstance, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($curlInstance, CURLOPT_FAILONERROR, true);
            curl_setopt($curlInstance, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curlInstance, CURLOPT_AUTOREFERER, true);
            curl_setopt($curlInstance, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($curlInstance, CURLOPT_HEADER, false);
            curl_setopt($curlInstance, CURLOPT_TIMEOUT, self::$isTimeout); 

            //Check debug option
            if(isset($arrParams['debug']))
            {
                curl_setopt($curlInstance, CURLOPT_VERBOSE, TRUE);
            }

            //Get content
            $arrData['response'] = curl_exec($curlInstance);

            //Escape data HTML
            $arrData['response'] = self::getFixedJson($arrData['response']);
            
            //Check error
            if(curl_error($curlInstance))
            {  
                $arrData['error'] = 1;
            }
        }
        catch(Zend_Exception $ex)
        {
            echo " === Error API : " . $ex->getMessage() . "\n";
        }

        //Close curl instance
        self::closePoolCurl();
        
        //Return data
        return $arrData;
    }
    
    /**
     * Post data version 2
     * @param <string> $url
     * @param <array> $arrParams
     * @return <int> 
     */
    public function postDataV2($url, $arrParams=array(), $arrHeader=array())
    {
        //Set data callback
        $arrData = array(
            'error'         =>  0,
            'response'      =>  array(
                'status'    =>  'failure'
            )
        );
        
        //Trim URL
        $url = trim($url);  
        
        //Check params
        if(!is_array($arrParams))
        {
            $arrParams = array();
        }
                
        //Check params length
        if(sizeof($arrParams) > 0)
        {
            //Build query
            $arrParams = self::getBuildHttpQuery($arrParams);
        }
        
        //Trying to get data
        try
        {
            //Get pool curl
            $curlInstance = self::getPoolCurl();

            //Set Aouth header
            if(sizeof($arrHeader) > 0)
            {
                curl_setopt($curlInstance, CURLOPT_HTTPHEADER, $this->getAuthorizationHeader($arrHeader));
            }

            //Set curl options
            curl_setopt($curlInstance, CURLOPT_USERAGENT, 'Mobion Core library php ' . phpversion());
            curl_setopt($curlInstance, CURLOPT_BUFFERSIZE, 8192);
            curl_setopt($curlInstance, CURLOPT_URL, $url);        
            curl_setopt($curlInstance, CURLOPT_POST, 1);
            curl_setopt($curlInstance, CURLOPT_POSTFIELDS, $arrParams);
            curl_setopt($curlInstance, CURLOPT_ENCODING, '');
            curl_setopt($curlInstance, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($curlInstance, CURLOPT_FAILONERROR, true);
            curl_setopt($curlInstance, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curlInstance, CURLOPT_AUTOREFERER, true);
            curl_setopt($curlInstance, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($curlInstance, CURLOPT_HEADER, false);
            curl_setopt($curlInstance, CURLOPT_TIMEOUT, self::$isTimeout);

            //Check debug option
            if(isset($arrParams['debug']))
            {
                curl_setopt($curlInstance, CURLOPT_VERBOSE, TRUE);
            }

            //Set data callback
            $arrData = array(
                'error'         =>  0,
                'response'      =>  array()
            );

            //Get content
            $arrData['response'] = curl_exec($curlInstance);

            //Escape data HTML
            $arrData['response'] = self::getFixedJson($arrData['response']);
            
            //Check error
            if(curl_error($curlInstance))
            {  
                $arrData['error'] = 1;
            }
        }
        catch(Zend_Exception $ex)
        {
            echo " === Error API : " . $ex->getMessage() . "\n";
        }

        //Close curl instance
        self::closePoolCurl();
        
        //Return data
        return $arrData;
    }
    
    /**
     * Get data from Music
     * @param <string> $url
     * @param <array> $arrParams
     * @return <int> 
     */
    public function getMusicData($url, $arrParams=array())
    {        
        //Set data callback
        $arrData = array(
            'error'         =>  0,
            'response'      =>  array(
                'status'    =>  'failure'
            )
        );
        
        //Trim URL
        $url = trim($url);
                
        //Check params
        if(!is_array($arrParams))
        {
            $arrParams = array();
        }

        //Check params length
        if(sizeof($arrParams) > 0)
        {
            $url .= '?' . self::getBuildHttpQuery($arrParams);
        }
        
        //Trying to get data
        try
        {
            //Get pool curl
            $curlInstance = self::getPoolCurl();

            //Set curl options        
            curl_setopt($curlInstance, CURLOPT_USERAGENT, 'Mobion Core library php ' . phpversion());
            curl_setopt($curlInstance, CURLOPT_BUFFERSIZE, 8192);
            curl_setopt($curlInstance, CURLOPT_URL, $url);        
            curl_setopt($curlInstance, CURLOPT_POST, 0);
            curl_setopt($curlInstance, CURLOPT_ENCODING, '');
            curl_setopt($curlInstance, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($curlInstance, CURLOPT_FAILONERROR, true);
            curl_setopt($curlInstance, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curlInstance, CURLOPT_AUTOREFERER, true);
            curl_setopt($curlInstance, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($curlInstance, CURLOPT_HEADER, false);
            curl_setopt($curlInstance, CURLOPT_TIMEOUT, self::$isTimeout);    

            //Get content
            $arrData['response'] = curl_exec($curlInstance);

            //Escape data HTML
            $arrData['response'] = self::getFixedJson($arrData['response']);
            
            //Check error
            if(curl_error($curlInstance))
            {  
                $arrData['error'] = 1;
            }
        }
        catch(Zend_Exception $ex)
        {
            echo " === Error API : " . $ex->getMessage() . "\n";
        }
        
        //Close curl instance
        self::closePoolCurl();
        
        //Return data
        return $arrData;
    }
    
    /**
     * Post data to Music
     * @param <string> $url
     * @param <array> $arrParams
     * @return <int> 
     */
    public function postMusicData($url, $arrParams=array())
    {
        //Set data callback
        $arrData = array(
            'error'         =>  0,
            'response'      =>  array(
                'status'    =>  'failure'
            )
        );
        
        //Trim URL
        $url = trim($url);  
        
        //Check params
        if(!is_array($arrParams))
        {
            $arrParams = array();
        }
                
        //Check params length
        if(sizeof($arrParams) > 0)
        { 
            $arrParams = self::getBuildHttpQuery($arrParams);
        }
        
        //Trying to get data
        try
        {
            //Get pool curl
            $curlInstance = self::getPoolCurl();

            //Set curl options
            curl_setopt($curlInstance, CURLOPT_USERAGENT, 'Mobion Core library php ' . phpversion());
            curl_setopt($curlInstance, CURLOPT_BUFFERSIZE, 8192);
            curl_setopt($curlInstance, CURLOPT_URL, $url);        
            curl_setopt($curlInstance, CURLOPT_POST, 1);
            curl_setopt($curlInstance, CURLOPT_POSTFIELDS, $arrParams);
            curl_setopt($curlInstance, CURLOPT_ENCODING, '');
            curl_setopt($curlInstance, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($curlInstance, CURLOPT_FAILONERROR, true);
            curl_setopt($curlInstance, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curlInstance, CURLOPT_AUTOREFERER, true);
            curl_setopt($curlInstance, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($curlInstance, CURLOPT_HEADER, false);
            curl_setopt($curlInstance, CURLOPT_TIMEOUT, self::$isTimeout);

            //Set data callback
            $arrData = array(
                'error'         =>  0,
                'response'      =>  array()
            );

            //Get content
            $arrData['response'] = curl_exec($curlInstance);

            //Escape data HTML
            $arrData['response'] = self::getFixedJson($arrData['response']);
            
            //Check error
            if(curl_error($curlInstance))
            {  
                $arrData['error'] = 1;
            }
        }
        catch(Zend_Exception $ex)
        {
            echo " === Error API : " . $ex->getMessage() . "\n";
        }
        
        //Close curl instance
        self::closePoolCurl();
        
        //Return data
        return $arrData;
    }
}

