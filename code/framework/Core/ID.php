<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_ID
 * @version     :   201209
 * @copyright   :   My company
 * @todo        :   Using to ID data
 */
class Core_ID
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
        //If empty host
        if(empty($options['host']))
        {
            throw new Zend_Exception("No host when call Core_ID");
        }

        //If empty port
        if(empty($options['port']))
        {
            $options['port'] = 9090;
        }

        //Check IDC
        if(empty($options['idc']))
        {
            $options['idc'] = 'default';
        }
        
        //Set Id options
        $options = array(
            'adapter'   =>  'thrift',
            'thrift'    =>  array(
                'host'      =>  $options['host'],
                'port'      =>  $options['port'],
                'debug'     =>  isset($options['debug'])?$options['debug']:false,
                'callback'  =>  'gnt_server_numberServiceClient',
                'package'   =>  'Id',
                'idc'       =>  $options['idc'],
                'persist'   =>  isset($options['persist'])?$options['persist']:false,
                'send_timeout'  =>  isset($options['send_timeout'])?$options['send_timeout']:3500,
                'recv_timeout'  =>  isset($options['recv_timeout'])?$options['recv_timeout']:4500,
                'retry'     =>  isset($options['retry'])?$options['retry']:3
            )
        );
        
        //Check class name
        $className = $options['adapter'] . '_' . md5($options[$options['adapter']]['package']) ;
        
        //Check CLI console
        self::$isCli = Core_Valid::isCli();
        
        //Try to connect to server
        try
        {
            //Check instance with not CLI
            if(isset(self::$instances[$className]) && (is_object(self::$instances[$className])) && (!self::$isCli))
            {
                //Return instance
                return self::$instances[$className];
            }
        
            //Get connection
            $connection = Core_Connection::getInstance($options);

            //Get instance
            self::$instances[$className] = $connection->getInstance();

            //Return object class
            return self::$instances[$className];
        }
        catch(Zend_Exception $ex)
        {
            //Nothing
        }
        
        //Return data
        return NULL;
    }

    /**
     * Clone function
     *
     */
    private final function __clone() {}
}

