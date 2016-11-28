<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_JobClient
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to job client
 */
class Core_JobClient
{
   /**
	 * List instance
	 *
	 * @var array
	 */
	private static $instances = array();
	
    /**
     * Get CLI control 
     */
    private static $isCli = false;
    
	/**
     * Constructor
     *
     */
	private final function __construct(){}
	
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
			throw new Core_Job_Exception("No instance of empty class when call Core_JobClient");
		}
		
		//Switch to get classname
		switch(strtolower($className))
		{
			case 'gearman':				
				$className = 'Core_Job_Client_Adapter_Gearman';
				break;
			default:
				throw new Core_Job_Exception("No instance of class $className");
				break;
		}
		
        //Check CLI console
        self::$isCli = Core_Valid::isCli();
        
        //Check instance with not CLI
        if(isset(self::$instances[$className]) && (is_object(self::$instances[$className])) && (!self::$isCli))
        {
            //Return instance
            return self::$instances[$className];
        }
        
		//Put to list after init
		self::$instances[$className] = new $className($options);
		
		//Return object class
		return self::$instances[$className];
	}
    
    /**
     * Close all connection
     */
    public static function closeAll()
    {
        //Loop and delete object
        foreach(self::$instances as $gearmanObject)
        {
            $gearmanObject = null;
        }
        
        //Reset all
        self::$instances = array();
    }
	
	/**
	 * Clone function
	 *
	 */
	private final function __clone() {}
}

