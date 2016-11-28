<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Server
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to utility
 */
class Core_Server
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
			throw new Core_Server_Exception("No instance of empty class when call Core_Server");
		}
		
		//Switch to get classname
		switch(strtolower($className))
		{
			case 'rest':				
				$className = 'Core_Server_Adapter_Rest';
				break;			
			default:
				throw new Core_Server_Exception("No instance of class $className");
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
     * Set output data
     * @param <int> $errorCode
     * @param <string> $message
     * @param <array> $dataBody
     * @return <array>
     */
    public static function setOutputData($errorCode, $message, $dataBody)
    {
        return array(
            'error'     =>  $errorCode,
            'message'   =>  $message,
            'body'      =>  $dataBody,
            'timestamp' =>  time()
        );
    }
	
	/**
	 * Clone function
	 *
	 */
	private final function __clone() {}
}

