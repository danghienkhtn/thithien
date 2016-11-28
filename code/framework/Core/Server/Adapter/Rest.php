<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Server_Adapter_Rest
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to utility
 */
class Core_Server_Adapter_Rest extends Core_Server_Adapter_Abstract
{
    /**
     * Constructor
     *
     */
    public function __construct()
    {
    	parent::__construct();
    }
	
    /**
     * Destructor
     *
     */
	public function __destruct()
	{
		parent::__destruct();
	}	
	
	/**
	 * Handle class
	 * <param> $request
	 */
	public function handle($request, $arrLogger=null)
	{
        //Get logger data
        $isLogger = false;
        $arrLoggerParams = array();
        
        //Time start
        $startTime = microtime(true);
        
        //Check logger
        if(isset($arrLogger))
        {
           //Set flag logger
           $isLogger = true; 
        }
        
        //Set data request URL
        if($request instanceof Zend_Controller_Request_Abstract)
        {
            $request = $request->getParams();
        }
        else
        {
            if(empty($request))
            {
                $request = $_REQUEST;
            }
        }
        
        //var_dump($request); exit;
        
        //Try to get data
		try
		{
            //Set params data
			$funcArg = array();
			$arrResult = array();
			$errorCode = 0;
			$errorMessage = "Successful.";
			            
			//Check method
			if(isset($request['method'])) 
			{
                //Get method
				$_method = $request['method'];
                                
                //Get classname data
				$classInstance = new ReflectionClass($this->_class);
                
                //Put data logger
                $arrLoggerParams['api_url'] = $this->_class . '/' . $_method;
                
                //Check method
				if($classInstance->hasMethod($_method) && !isset(self::$magicMethods[$_method]))
				{
                    //Set params detail
					$request_keys = array_keys($request);
					array_walk($request_keys, array(__CLASS__, "lowerCase"));
					$request = array_combine($request_keys, $request);
                    
                    //Set function detail
					$method = $classInstance->getMethod($_method);
					$params = $method->getParameters();
					$number = count($params);
					
                    //Check params counter
					if($number > 0)
					{
						for($i=0;$i<$number;$i++)
						{
                            //Get params
							$paramName=strtolower($params[$i]->getName());
                            
                            //Get params position
							$paramIndex = $params[$i]->getPosition();
                            
                            //Check params
							if(!isset($request[$paramName])) 
							{
								if ($params[$i]->isDefaultValueAvailable()) 
								{
									$paramValue = $params[$i]->getDefaultValue();
								} 
								else 
								{
									throw new Core_Server_Exception('Required parameter "'.$paramName.'" is not specified.', -200);
								}
							} 
							else 
							{
								$paramValue = $request[$paramName];
							}
                            
                            //Add function params
							$funcArg[$paramIndex] = $paramValue;
						}
					}
					
                    //Check static method
					if($method->isStatic())
					{
                        //Call API function
						if($number > 0)
						{
                            $arrResult = forward_static_call_array(array($this->_class, $_method), $funcArg);
						}
						else 
						{		
                            $arrResult = forward_static_call(array($this->_class, $_method));
						}
					} 
					elseif($method->isPublic())
					{
                        //Init instance
						$instance = $classInstance->newInstance();
                        
                        //Call API
						$arrResult = $method->invokeArgs($instance, $funcArg);
					}
				}
				else 
				{
					throw new Core_Server_Exception('Request method not found', -201);
				}
			 }
			 else
			 {
				throw new Core_Server_Exception('No method given.', -202);
			 }
		}
		catch(Exception $e)
		{
            //Set data
			$errorMessage = $e->getMessage();
			$errorCode = $e->getCode();
			$arrResult = array();

            //Echo data and exit
            $arrResult = Core_Server::setOutputData($errorCode, $errorMessage, $arrResult);
		}

        //Time start
        $endTime = microtime(true);
        
        //Get timestamp in seconds
        $iTime = ($endTime - $startTime) + 0.02;
        
        //Set more execute time
        $arrResult['api_exec_time'] = $iTime * 1000;
        
		//Format data
        $responseData = Zend_Json::encode($arrResult);
        
        //Filter data
        $responseData = Core_Filter::stripBuffer($responseData);
        
        //Check to put logger
        if($isLogger)
        {
            //Get logger data
            $classLogger = $arrLogger['class'];
            $methodLogger = $arrLogger['function'];
            $argsLogger = $arrLogger['args'];
            
            //Put data for logger
            $arrLoggerParams['category'] = isset($argsLogger['category'])?$argsLogger['category']:'LoggerDefault';
            $arrLoggerParams['app'] = isset($argsLogger['app'])?$argsLogger['app']:'MUSIC';
            $arrLoggerParams['execute_time'] = $iTime;
            $arrLoggerParams['api_response'] = 'OK';
                        
            //Class static to call logger
            forward_static_call_array(array($classLogger, $methodLogger), array($arrLoggerParams));
        }
        
        //Flush buffer
        if(ob_get_length() > 0)
        {
            ob_end_flush();
        }
        
        //Turn on buffer
        ob_start();
        
        //Send json header        
        header("OK", true, 200);
        header("Content-Type: application/json");
        
		//Return result
       	echo $responseData;
        
        //Flush buffer
        ob_end_flush();
        
        //Exit render
        exit();
	}
    
    /**
	 * Handle class
	 * <param> $request
	 */
	public function handleCLI($request, $arrLogger=null)
	{
        //Get logger data
        $isLogger = false;
        $arrLoggerParams = array();
        
        //Time start
        $startTime = microtime(true);
        
        //Check logger
        if(isset($arrLogger))
        {
           //Set flag logger
           $isLogger = true; 
        }
        
        //Set data request URL
        if(empty($request))
        {
            throw new Core_Server_Exception('Request method not found', -201);
        }       
                
        //Try to get data
		try
		{
            //Set params data
			$funcArg = array();
			$arrResult = array();
			$errorCode = 0;
			$errorMessage = "Successful.";
			            
			//Check method
			if(isset($request['method'])) 
			{
                //Get method
				$_method = $request['method'];
                                
                //Get classname data
				$classInstance = new ReflectionClass($this->_class);
                
                //Put data logger
                $arrLoggerParams['api_url'] = $this->_class . '/' . $_method;
                
                //Check method
				if($classInstance->hasMethod($_method) && !isset(self::$magicMethods[$_method]))
				{
                    //Set params detail
					$request_keys = array_keys($request);
					array_walk($request_keys, array(__CLASS__, "lowerCase"));
					$request = array_combine($request_keys, $request);
                    
                    //Set function detail
					$method = $classInstance->getMethod($_method);
					$params = $method->getParameters();
					$number = count($params);
					
                    //Check params counter
					if($number > 0)
					{
						for($i=0;$i<$number;$i++)
						{
                            //Get params
							$paramName=strtolower($params[$i]->getName());
                            
                            //Get params position
							$paramIndex = $params[$i]->getPosition();
                            
                            //Check params
							if(!isset($request[$paramName])) 
							{
								if ($params[$i]->isDefaultValueAvailable()) 
								{
									$paramValue = $params[$i]->getDefaultValue();
								} 
								else 
								{
									throw new Core_Server_Exception('Required parameter "'.$paramName.'" is not specified.', -200);
								}
							} 
							else 
							{
								$paramValue = $request[$paramName];
							}
                            
                            //Add function params
							$funcArg[$paramIndex] = $paramValue;
						}
					}
					
                    //Check static method
					if($method->isStatic())
					{
                        //Call API function
						if($number > 0)
						{
                            $arrResult = forward_static_call_array(array($this->_class, $_method), $funcArg);
						}
						else 
						{		
                            $arrResult = forward_static_call(array($this->_class, $_method));
						}
					} 
					elseif($method->isPublic())
					{
                        //Init instance
						$instance = $classInstance->newInstance();
                        
                        //Call API data
                        $arrResult = $method->invokeArgs($instance, $funcArg);
					}
				}
				else 
				{
					throw new Core_Server_Exception('Request method not found', -201);
				}
			 }
			 else
			 {
				throw new Core_Server_Exception('No method given.', -202);
			 }
		}
		catch(Exception $e)
		{
            //Set data
			$errorMessage = $e->getMessage();
			$errorCode = $e->getCode();
			$arrResult = array();

            //Echo data and exit
            $arrResult = Core_Server::setOutputData($errorCode, $errorMessage, $arrResult);
		}

        //Time start
        $endTime = microtime(true);
        
        //Get timestamp in seconds
        $iTime = ($endTime - $startTime) + 0.02;
        
        //Set more execute time
        $arrResult['api_exec_time'] = $iTime * 1000;
                
        //Check data
        $responseData = Zend_Json::encode($arrResult);	
        
        //Filter data
        $responseData = Core_Filter::stripBuffer($responseData);
        
        //Check to put logger
        if($isLogger)
        {
            //Get logger data
            $classLogger = $arrLogger['class'];
            $methodLogger = $arrLogger['function'];
            $argsLogger = $arrLogger['args'];
            
            //Put data for logger
            $arrLoggerParams['category'] = isset($argsLogger['category'])?$argsLogger['category']:'LoggerDefault';
            $arrLoggerParams['app'] = isset($argsLogger['app'])?$argsLogger['app']:'MUSIC';
            $arrLoggerParams['execute_time'] = $iTime;
            $arrLoggerParams['api_response'] = 'OK';
                        
            //Class static to call logger
            forward_static_call_array(array($classLogger, $methodLogger), array($arrLoggerParams));
        }
                
		//Return result
       	return $responseData;
	}
}