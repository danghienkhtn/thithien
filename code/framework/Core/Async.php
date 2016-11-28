<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Async
 * @version     :   201209
 * @copyright   :   My company
 * @todo        :   Using to async data
 */
class Core_Async extends Thread
{
    /**
     * Thread handler variable
     */
    private $className = null;
    private $methodName = null;
    private $arrParams = null;
    private $arrResponse = array();
    private $iJoined = false;
    
    /**
     * Provide a passthrough to call_user_func_array
     * @param <string> $method
     * @param <string> $params 
     */
	private final function __construct($className, $methodName, $arrParams)
    {
        $this->className = $className;
		$this->methodName = $methodName;
		$this->arrParams = $arrParams;
		$this->arrResponse = array();
		$this->iJoined = false;
	}
    
    /**
     * Destructor
     */
	public function __destruct()
	{
        //Unset all data
        unset($this->className, $this->methodName, $this->arrParams, $this->arrResponse, $this->iJoined);
        
        //Parent destruct
		parent::__destruct();
	}
	
	/**
     * The smallest thread in the world
     * @return <boolean>
     */
	public function run()
    {
        //Check params to callback
        if(sizeof($this->arrParams) > 0)
        {
            if(($this->arrResponse = forward_static_call_array(array($this->className, $this->methodName), $this->arrParams)))
            {
                return true;
            }
        }
        else
        {
            if(($this->arrResponse = forward_static_call(array($this->className, $this->methodName))))
            {
                return true;
            }
        }
        
        //Return data
        return false;
	}
	
	/**
     * Static method to create your threads from functions
     * @param <string> $className
     * @param <string> $methodName
     * @param <array> $arrParams
     * @return <Core_Async> 
     */
	public final static function execute($className, $methodName, $arrParams)
    {
        //Create thread instance
		$threadInstance = new Core_Async($className, $methodName, $arrParams);
        
        //Start thread instance
		if($threadInstance->start())
        {
			return $threadInstance;
		}
	}
	
	/**
     * Response data
     * @return <array> 
     */
	public function toArray()
    { 
        //Try to joined thread
		if(!$this->iJoined)
        {
            //Set control flag
			$this->iJoined = true;
            
            //Join the thread
			$this->join();
		}
		
        //Return data
		return $this->arrResponse;
	}
    
    /**
     * Clone function
     *
     */
    private final function __clone() {}
}

