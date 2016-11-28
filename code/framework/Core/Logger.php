<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Logger
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to log information
 */
class Core_Logger
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
            throw new Core_Logger_Exception("No instance of empty class when call Core_Logger");
        }

        //Switch to get classname
        switch(strtolower($className))
        {
            case 'file':
                $className = 'Core_Logger_Adapter_File';
                break;
            case 'scribe':
                $className = 'Core_Logger_Adapter_Scribe';
                break;
            default:
                throw new Core_Logger_Exception("No instance of class $className");
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
     * Get message API
     * @param <array> $arrParams
     * @return <string> 
     */
    public static function getApiMessage($arrParams)
    {
        //Set arrMessage
        $arrMessage = array();
        
        //Check virtual_id
        if(!isset($arrParams['virtual_id']))
        {
            $arrParams['virtual_id'] = 0;
        }
        $arrMessage['virtual_id'] = $arrParams['virtual_id'];
        
        //Check real_id
        if(!isset($arrParams['real_id']))
        {
            $arrParams['real_id'] = 0;
        }
        $arrMessage['real_id'] = $arrParams['real_id'];
        
        //Check uid
        if(!isset($arrParams['uid']))
        {
            $arrParams['uid'] = 0;
        }
        $arrMessage['uid'] = $arrParams['uid'];
        
        //Check time_stamp
        if(!isset($arrParams['time_stamp']))
        {
            $arrParams['time_stamp'] = time();
        }
        $arrMessage['time_stamp'] = $arrParams['time_stamp'];
        
        //Check api_url
        if(!isset($arrParams['api_url']))
        {
            $arrParams['api_url'] = '';
        }
        $arrMessage['api_url'] = $arrParams['api_url'];
        
        //Check execute_time
        if(!isset($arrParams['execute_time']))
        {
            $arrParams['execute_time'] = 1;
        }
        $arrMessage['execute_time'] = $arrParams['execute_time'];
        
        //Check api_response
        if(!isset($arrParams['api_response']))
        {
            $arrParams['api_response'] = 0;
        }
        $arrMessage['api_response'] = $arrParams['api_response'];
                  
        //Check api_response
        if(!isset($arrParams['app']))
        {
            $arrParams['app'] = 0;
        }
        $arrMessage['app'] = $arrParams['app'];
        
        //Return data
        return implode("\t", $arrMessage);
    }
    
    /**
     * Get message MYSQL
     * @param <array> $arrParams
     * @return <string> 
     */
    public static function getMysqlMessage($arrParams)
    {
        //Set arrMessage
        $arrMessage = array();
        
        //Check virtual_id
        if(!isset($arrParams['virtual_id']))
        {
            $arrParams['virtual_id'] = 0;
        }
        $arrMessage['virtual_id'] = $arrParams['virtual_id'];
        
        //Check real_id
        if(!isset($arrParams['real_id']))
        {
            $arrParams['real_id'] = 0;
        }
        $arrMessage['real_id'] = $arrParams['real_id'];
        
        //Check uid
        if(!isset($arrParams['uid']))
        {
            $arrParams['uid'] = 0;
        }
        $arrMessage['uid'] = $arrParams['uid'];
        
        //Check time_stamp
        if(!isset($arrParams['time_stamp']))
        {
            $arrParams['time_stamp'] = time();
        }
        $arrMessage['time_stamp'] = $arrParams['time_stamp'];
                        
        //Check exception_message
        if(!isset($arrParams['exception_message']))
        {
            $arrParams['exception_message'] = 0;
        }
        $arrMessage['exception_message'] = $arrParams['exception_message'];
        
        //Check api_response
        if(!isset($arrParams['app']))
        {
            $arrParams['app'] = 0;
        }
        $arrMessage['app'] = $arrParams['app'];
        
        //Return data
        return implode("\t", $arrMessage);
    }
    
    /**
     * Get message for PC message
     * @param <array> $arrParams
     * @return <string> 
     */
    public static function getPcActionMessage($arrParams)
    {
        //Set arrMessage
        $arrMessage = array();
        
        //Check virtual_id
        if(!isset($arrParams['virtual_id']))
        {
            $arrParams['virtual_id'] = 0;
        }
        $arrMessage['virtual_id'] = $arrParams['virtual_id'];
        
        //Check real_id
        if(!isset($arrParams['real_id']))
        {
            $arrParams['real_id'] = 0;
        }
        $arrMessage['real_id'] = $arrParams['real_id'];
        
        //Check uid
        if(!isset($arrParams['uid']))
        {
            $arrParams['uid'] = 0;
        }
        $arrMessage['uid'] = $arrParams['uid'];
        
        //Check time_stamp
        if(!isset($arrParams['time_stamp']))
        {
            $arrParams['time_stamp'] = time();
        }
        $arrMessage['time_stamp'] = $arrParams['time_stamp'];
                        
        //Check action_id
        if(!isset($arrParams['action_id']))
        {
            $arrParams['action_id'] = 0;
        }
        $arrMessage['action_id'] = $arrParams['action_id'];
        
        //Check object_to
        if(!isset($arrParams['object_to']))
        {
            $arrParams['object_to'] = 0;
        }
        $arrMessage['object_to'] = $arrParams['object_to'];
        
        //Check object_from
        if(!isset($arrParams['object_from']))
        {
            $arrParams['object_from'] = 0;
        }
        $arrMessage['object_from'] = $arrParams['object_from'];
         
        //Check api_response
        if(!isset($arrParams['app']))
        {
            $arrParams['app'] = 0;
        }
        $arrMessage['app'] = $arrParams['app'];
        
        //Return data
        return implode("\t", $arrMessage);
    }
    
    /**
     * Get message for action message
     * @param <array> $arrParams
     * @return <string> 
     */
    public static function getActionMessage($arrParams)
    {
        //Set arrMessage
        $arrMessage = array();
        
        //Check virtual_id
        if(!isset($arrParams['virtual_id']))
        {
            $arrParams['virtual_id'] = 0;
        }
        $arrMessage['virtual_id'] = $arrParams['virtual_id'];
        
        //Check real_id
        if(!isset($arrParams['real_id']))
        {
            $arrParams['real_id'] = 0;
        }
        $arrMessage['real_id'] = $arrParams['real_id'];
        
        //Check account_id
        if(!isset($arrParams['account_id']))
        {
            $arrParams['account_id'] = 0;
        }
        $arrMessage['account_id'] = $arrParams['account_id'];
        
        //Check user_id
        if(!isset($arrParams['user_id']))
        {
            $arrParams['user_id'] = 0;
        }
        $arrMessage['user_id'] = $arrParams['user_id'];
        
        //Check time_stamp
        if(!isset($arrParams['time_stamp']))
        {
            $arrParams['time_stamp'] = time();
        }
        $arrMessage['time_stamp'] = $arrParams['time_stamp'];
                        
        //Check action_id
        if(!isset($arrParams['action_id']))
        {
            $arrParams['action_id'] = 0;
        }
        $arrMessage['action_id'] = $arrParams['action_id'];
        
        //Check object_to
        if(!isset($arrParams['object_to']))
        {
            $arrParams['object_to'] = 0;
        }
        $arrMessage['object_to'] = $arrParams['object_to'];
        
        //Check object_from
        if(!isset($arrParams['object_from']))
        {
            $arrParams['object_from'] = 0;
        }
        $arrMessage['object_from'] = $arrParams['object_from'];
        
        //Check object_data
        if(!isset($arrParams['object_data']))
        {
            $arrParams['object_data'] = 0;
        }
        $arrMessage['object_data'] = $arrParams['object_data'];
        
        //Check app_id
        if(!isset($arrParams['app_id']))
        {
            $arrParams['app_id'] = 0;
        }
        $arrMessage['app_id'] = $arrParams['app_id'];
        
        //Check country_code
        if(!isset($arrParams['country_code']))
        {
            $arrParams['country_code'] = 0;
        }
        $arrMessage['country_code'] = $arrParams['country_code'];
        
        //Check  action_score
        if(!isset($arrParams['action_score']))
        {
            $arrParams['action_score'] = 0;
        }
        $arrMessage['action_score'] = $arrParams['action_score'];
        
        //Return data
        return implode("\t", $arrMessage);
    }

    /**
     * Get IP for user message
     * @param <array> $arrParams
     * @return <string> 
     */
    public static function getIPMessage($arrParams)
    {
        //Set arrMessage
        $arrMessage = array();
        
        //Check virtual_id
        if(!isset($arrParams['virtual_id']))
        {
            $arrParams['virtual_id'] = 0;
        }
        $arrMessage['virtual_id'] = $arrParams['virtual_id'];
        
        //Check real_id
        if(!isset($arrParams['real_id']))
        {
            $arrParams['real_id'] = 0;
        }
        $arrMessage['real_id'] = $arrParams['real_id'];
        
        //Check uid
        $arrMessage['ip'] = $arrParams['ip'];          
        
        //Check  country code
        if(!isset($arrParams['country_code']))
        {
            $arrParams['country_code'] = 'jp';
        }
        $arrMessage['country_code'] = $arrParams['country_code'];
        
        //Check  AppID
        $arrMessage['app'] = $arrParams['app'];
        
        //Return data
        return implode("\t", $arrMessage);
    }
    
    /**
     * Get message for Forum message
     * @param <array> $arrParams
     * @return <string> 
     */
    public static function getForumActionMessage($arrParams)
    {
        //Set arrMessage
        $arrMessage = array();
        
        //Check virtual_id
        if(!isset($arrParams['virtual_id']))
        {
            $arrParams['virtual_id'] = 0;
        }
        $arrMessage['virtual_id'] = $arrParams['virtual_id'];
        
        //Check real_id
        if(!isset($arrParams['real_id']))
        {
            $arrParams['real_id'] = 0;
        }
        $arrMessage['real_id'] = $arrParams['real_id'];
        
        //Check uid
        if(!isset($arrParams['uid']))
        {
            $arrParams['uid'] = 0;
        }
        $arrMessage['uid'] = $arrParams['uid'];
        
        //Check time_stamp
        if(!isset($arrParams['time_stamp']))
        {
            $arrParams['time_stamp'] = time();
        }
        $arrMessage['time_stamp'] = $arrParams['time_stamp'];
                        
        //Check action_id
        if(!isset($arrParams['action_id']))
        {
            $arrParams['action_id'] = 0;
        }
        $arrMessage['action_id'] = $arrParams['action_id'];
        
        //Check action_score
        if(!isset($arrParams['action_score']))
        {
            $arrParams['action_score'] = 1;
        }
        $arrMessage['action_score'] = $arrParams['action_score'];
        
        //Check object_to
        if(!isset($arrParams['object_to']))
        {
            $arrParams['object_to'] = 0;
        }
        $arrMessage['object_to'] = $arrParams['object_to'];
        
        //Check object_from
        if(!isset($arrParams['object_from']))
        {
            $arrParams['object_from'] = 0;
        }
        $arrMessage['object_from'] = $arrParams['object_from'];
        
        //Check api_response
        if(!isset($arrParams['app']))
        {
            $arrParams['app'] = 0;
        }
        $arrMessage['app'] = $arrParams['app'];
        
        //Check country_code
        if(!isset($arrParams['country_code']))
        {
            $arrParams['country_code'] = 0;
        }
        $arrMessage['country_code'] = $arrParams['country_code'];
        
        //Check category_id
        if(!isset($arrParams['category_id']))
        {
            $arrParams['category_id'] = 0;
        }
        $arrMessage['category_id'] = $arrParams['category_id'];
        
        //Return data
        return implode("\t", $arrMessage);
    }
    
    /**
     * Clone function
     *
     */
    private final function __clone() {}
}

