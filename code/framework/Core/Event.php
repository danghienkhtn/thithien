<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Event
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to api async
 */
class Core_Event
{
    /**
     * Host data    
     */
    private $sAddr = "127.0.0.1";
    
    /**
     * Port data    
     */
	private $iPort = 8080;
        
    /**
     * Unix socket path
     */
    private $sUnixSocketPath = "";
    
    /**
     * Owner ID 
     */
    private $sOwner = "dev.vn";
    
    /**
     * Access denied data 
     */
    private $sDeniedData = '{"error":401,"message":"Access Denied","body":[]}';
    
    /**
     * List API caller
     * @var <array> 
     */
    private $arrListApi = array();
    
    /**
     * Log data
     */
    private $arrLogData = array();
    
    /**
     * Content tyep data
     */
    private $contentType = "application/json";
    
    /**
     * Debug data    
     */
	private $bDebug = false;
    
    /**
     * Performance debug
     */
    private $bXhp = false;
    
    /**
     * Set max limit
     */
    private $iMaxLimit = 30;
    
    /**
     * Callback function data
     */
    private $sCallbackFunc = "";
    
    /**
     * Control check API key
     */
    private $isCheckApiKey = false;
    
    /**
     * List api key
     * @var <array> 
     */
    private $arrListApiKey = array();
    
    /**
     * Server instance
     * @var <object> 
     */
    private $serverInstance = null;
        
    /**
     * Construct api server
     * @param type $sAddr
     * @param type $iPort 
     */
    public function __construct($sAddr, $iPort, $sDeniedData, $arrLogData = array())
    {
        $this->sAddr = $sAddr;
        $this->iPort = $iPort;
        $this->sDeniedData = $sDeniedData;
        $this->arrLogData = $arrLogData;
        
        //Set option for server
        $arrOptions = array(
            'adapter' => 'rest'
        );

        //Get server instance
        $this->serverInstance = Core_Server::getInstance($arrOptions);
    }
    
    /**
     * Set list API data
     * @param <array> $arrListAPi 
     */
    public function setListApi($arrListAPi)
    {
        $this->arrListApi = $arrListAPi;
    }
    
    /**
     * Set content type data
     * @param <string> $contentType 
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }
    
    /**
     * Debug mode
     * @param <bool> $bDebug 
     */
    public function setDebug($bDebug)
    {
        $this->bDebug = $bDebug;
    }    
        
    /**
     * Set XHP performance
     * @param <bool> $bXhp 
     */
    public function setXhp($bXhp)
    {
        $this->bXhp = $bXhp;
    }
    
    /**
     * Set unix socket path
     * @param <string> $sUnixSocketPath 
     */
    public function setUnixSocket($sUnixSocketPath)
    {
        $this->sUnixSocketPath = $sUnixSocketPath;
    }
    
    /**
     * Set owner for unix socket
     * @param <string> $sOwner 
     */
    public function setOwner($sOwner)
    {
        $this->sOwner = $sOwner;
    }
    
    /**
     * Set max limit
     * @param <int> $iMaxLimit 
     */
    public function setMaxLimit($iMaxLimit)
    {
        $this->iMaxLimit = $iMaxLimit;
    }
    
    /**
     * Set callback function
     * @param <string> $sCallbackFunc 
     */
    public function setCallback($sCallbackFunc)
    {
        $this->sCallbackFunc = $sCallbackFunc;
    }
    
    /**
     * Check API Key
     * @param <bool> $isCheckApiKey 
     */
    public function checkApiKey($isCheckApiKey)
    {
        $this->isCheckApiKey = $isCheckApiKey;
    }
    
    /**
     * Add API KEY
     * @param <array> $arrListApiKey 
     */
    public function addApiKey($arrListApiKey)
    {
        $this->arrListApiKey = $arrListApiKey;
    }
    
    /**
     * Parse cookie
     * @param <string> $sRawCookie 
     */
    protected function parseCookieRequest($sRawCookie)
    {
        if(sizeof($sRawCookie) > 0)
        {
            //Parse to array data
            $arrCookie = explode(';', $sRawCookie);  
            
            //Loop to put data
            foreach($arrCookie as $cookieDetail)
            {
                //Explode data
                $arrCookieDetail = explode("=", $cookieDetail);
                
                //Check cookie data
                $cookieName = trim($arrCookieDetail[0]);
                unset($arrCookieDetail[0]);
                
                //Check cookie data
                if(sizeof($arrCookieDetail) > 1)
                {
                    $_COOKIE[$cookieName] = trim(implode('=', $arrCookieDetail));
                }
                else
                {
                    $_COOKIE[$cookieName] = trim($arrCookieDetail[1]);
                }
            }
        }
    }
    
    /**
     * Parse post data from request
     * @param array $a_data 
     */
    protected function parsePostRequest(&$arrPost, $sRawData, $sContentType)
    {
        //Set default match data
        $arrMatches = array();
        
        // Grab multipart boundary from content type header
        preg_match('/boundary=(.*)$/', $sContentType, $arrMatches);
        
        //Get boundary data
        $boundary = (empty($arrMatches) || empty($arrMatches[1])) ? "" : $arrMatches[1];

        //Check boundary
        if(empty($boundary))
        {
            parse_str($sRawData, $arrPost);
            return;
        }
        
        //Split content by boundary and get rid of last -- element
        $arrBlocks = preg_split("/-+$boundary/", $sRawData);
        
        //Pop the end element
        array_pop($arrBlocks);

        //Loop data blocks and put to array POST
        foreach($arrBlocks as $blockData)
        {
            //Check block data
            if(empty($blockData))
            {
                continue;
            }
           
            //Parse uploaded files
            if(strpos($blockData, 'application/octet-stream') !== FALSE)
            {
                preg_match("/name=\"([^\"]*)\".*stream[\n|\r]+([^\n\r].*)?$/s", $blockData, $arrMatches);
            }            
            else
            {
                preg_match('/name=\"([^\"]*)\"[\n|\r]+([^\n\r].*)?\r$/s', $blockData, $arrMatches);
            }
            
            //Check data to put
            if(empty($arrMatches) || empty($arrMatches[1]))
            {
                continue;
            }
            
            //Put to post data
            $arrPost[$arrMatches[1]] = $arrMatches[2];
        }        
    }
    
    /**
     * Parse data from request
     * @param <object> $request 
     */
    protected function parseDataRequest($request, &$allHeaders)
    {    
        //Clear global data
        $this->clearGlobal();
        
        //Set manual data for server
        $_SERVER["SCRIPT_FILENAME"] = "index.php";
        $_SERVER["PHP_SELF"] = "index.php";
        $_SERVER["SCRIPT_NAME"] = 'index.php';
        $_SERVER["SERVER_ADDR"] = "{$this->sAddr}:{$this->iPort}";
        $_SERVER["SERVER_NAME"] = "{$this->sAddr}:{$this->iPort}";
        $_SERVER["SERVER_SOFTWARE"] = "Mobion Server 1.0";
		$_SERVER["SERVER_PROTOCOL"] = 'HTTP 1.1';
        $_SERVER["REQUEST_TIME"] = time();
                        
        //Get header information
        $arrHeaders = evhttp_request_headers($request);
        
        //Check and loop to push header data
        if(sizeof($arrHeaders) > 0)
        {
            foreach($arrHeaders as $headerName => $headerValue)
            {
                $headerName = "HTTP_" . str_replace("-", "_", strtoupper($headerName));                
                $allHeaders[$headerName] = $headerValue; 
                $_SERVER[$headerName] = $headerValue;
            }
        }        
                
        //Parse cookie
        if(isset($_SERVER['HTTP_COOKIE']))
        {
            unset($_SERVER['HTTP_COOKIE']);
        }
    }
    
    /**
     * Clear global data 
     */
    protected function clearGlobal()
    {
        $_GET = array();
        $_REQUEST = array();
        $_SERVER = array();
        $_POST = array();
        $_COOKIE = array();
    }
                    
    /**
     * Process data form request
     * @param type $request
     * @return type 
     */
    public function processRequest($request)
    { 
        //Set response data
        $sResponseData = $this->sDeniedData;
            
        //Try to execute data
        try
        {  
            //Debug information
            if($this->bDebug)
            {
                $iStart = microtime(true);
            }
            
            //All header data
            $allHeaders = array();
            
            //Parse all request data
            $this->parseDataRequest($request, $allHeaders);
            
            //Set request method data
            $REQUEST_METHOD = evhttp_request_method($request);

            //Parse data information
            $arrPart = parse_url(evhttp_request_get_uri($request));

            //Debug information
            if($this->bDebug)
            {
                echo "=== Log (" . date("Y-m-d H:i:s") . "): URL=" . var_export($arrPart, true) . "\n";                
            }

            //Set query string data
            $QUERY_STRING = isset($arrPart['query'])?$arrPart['query']:'';

            //Set query path data
            $QUERY_HANDLE_PATH = isset($arrPart['path'])?$arrPart['path']:'/';

            //Set GET data
            $GET = array();
            
            //Set $_GET data
            if(!empty($QUERY_STRING))
            {
                parse_str($QUERY_STRING, $GET);
            }

            //Check max limit
            if(isset($GET['iLimit']) && ($GET['iLimit'] > $this->iMaxLimit))
            {
                $GET['iLimit'] = $this->iMaxLimit;
            }
            
            //Set POST data
            $POST = array();
            
            //Check method data and Get POST information data
            if(strtoupper($REQUEST_METHOD) == 'POST')
            {
                //Get POST data
                $sPostData = evhttp_request_body($request);

                //Parse POST data
                $this->parsePostRequest($POST, $sPostData, isset($allHeaders["HTTP_CONTENT_TYPE"])?$allHeaders["HTTP_CONTENT_TYPE"]:"text/html; charset=UTF-8");
            }

            //Merge all GET and POST data
            $REQUEST = array_merge($GET, $POST);
            
            //Debug information
            if($this->bDebug)
            {
                var_dump("===End:", $REQUEST);
            }
            
            //Check API key
            if($this->isCheckApiKey)
            {
                //Get authenticate control data
                $apiKey = isset($REQUEST['apiKey'])?$REQUEST['apiKey']:(isset($allHeaders['HTTP_APIKEY'])?$allHeaders['HTTP_APIKEY']:"");
                $apiSecret = isset($REQUEST['apiSecret'])?$REQUEST['apiSecret']:(isset($allHeaders['HTTP_APISECRET'])?$allHeaders['HTTP_APISECRET']:"");
                
                //Debug information
                if($this->bDebug)
                {
                    var_dump("<pre>apiKey:", $apiKey, "</pre>");
                    var_dump("<pre>apiSecret:", $apiSecret, "</pre>");
                    var_dump("<pre>arrListApiKey:", $REQUEST, "</pre>");                    
                }
                
                //Check in list authenticate
                if(empty($apiKey) || !isset($this->arrListApiKey[$apiKey]) || (isset($this->arrListApiKey[$apiKey]) && ($this->arrListApiKey[$apiKey] != $apiSecret)))
                {
                    //Debug information
                    if($this->bDebug)
                    {
                        var_dump("<pre>Authenticate failure !!!!");
                    }
                    
                    //Clear global data
                    $this->clearGlobal();
                    
                    //Add header data for response
                    evhttp_response_add_header($request, "Server", "mobion");
                    evhttp_response_add_header($request, "Content-Type", $this->contentType);
                                        
                    //Response data
                    return evhttp_response_set($sResponseData, 200, "OK");
                }
            }
            
            //Check URL path allowed
            if(isset($this->arrListApi[$QUERY_HANDLE_PATH]))
            {
                //Register class call
                $this->serverInstance->setClass($this->arrListApi[$QUERY_HANDLE_PATH]);

                //Hanlde instance
                $sResponseData = $this->serverInstance->handleCLI($REQUEST, $this->arrLogData);
            }	
            
            //Callback function to release resource
            if(!empty($this->sCallbackFunc) && is_string($this->sCallbackFunc))
            {
                //Explode function caller
                $arrCallerParams = explode('::', $this->sCallbackFunc);
                
                //Call function static
                forward_static_call($arrCallerParams);
            }    
            
            //Clear global data
            $this->clearGlobal();
            
            //Clear all data
            unset($REQUEST);
            
            //Debug information
            if($this->bDebug)
            {
                $iStop = microtime(true);
                $iElp = round(1000*($iStop - $iStart), 0);
                print_r("====Total time: $iElp ms\n");
            }
                        
            //Add header data for response
            evhttp_response_add_header($request, "Server", "mobion");
            evhttp_response_add_header($request, "Content-Type", $this->contentType);
            
            //Response data
            return evhttp_response_set($sResponseData, 200, "OK");
        }
        catch(Exception $ex)
        {
            //Set response data
        	$sResponseData = $ex->__toString();		
            
            //Debug information
            if(!$this->bDebug)
            {
                echo "=== Logger(" . date("Y-m-d H:i:s") . ") : use with memory: " . round(memory_get_usage()/1048576, 2) . " MB And Response=" . $sResponseData . "\n";
            }
            
            //Clear global data
            $this->clearGlobal();
            
            //Add header data for response
            evhttp_response_add_header($request, "Server", "mobion");
            evhttp_response_add_header($request, "Content-Type", $this->contentType);
            
            //Response data
            return evhttp_response_set($sResponseData, 500, "Error");
        }  
    }
    
    /**
     * Run server in port and host 
     */
    public function dispatch()
	{        
        //Check port and host
        if(empty($this->sAddr) && empty($this->iPort) && empty($this->sUnixSocketPath))
        {
            echo "=== Please input host and port or unix socket...\n";
            exit();
        }
        
        //Delete the old socket
        if(!empty($this->sUnixSocketPath))
        {
            @unlink($this->sUnixSocketPath);
        }
        
        //Init event handler
        event_init();
        
        //Start event handler
		$this->httpd = evhttp_start($this->sAddr, $this->iPort, $this->sUnixSocketPath);
        
        //Set callback function handler
		evhttp_set_gencb($this->httpd, array($this, 'processRequest'));
        
        //Debug information
        if(empty($this->sUnixSocketPath))
        {
            echo "=== Start server started at http://{$this->sAddr}:{$this->iPort}...\n";
        }
        else
        {
            //Set owner for socket
            @chown($this->sUnixSocketPath, $this->sOwner);
            
            //Debug information
            echo "=== Start server started at socket {$this->sUnixSocketPath}...\n";
        }
        
        //Loop to listen in socket
		event_dispatch();
    }
}

