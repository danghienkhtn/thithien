<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Logger_Adapter_Scribe
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to logger by scribe server
 */
class Core_Logger_Adapter_Scribe extends Core_Logger_Adapter_Abstract
{
    /**
    * Constructor
    *
    */
    public function __construct($options)
    {
        //Get options child
        $options = $this->getOptions($options);
        
        //Check host
        if(empty($options['host']))
        {
            throw new Core_Logger_Exception('Input host of scribe server.');
        }

        //Check port
        if(empty($options['port']))
        {
            throw new Core_Logger_Exception('Input port of scribe server.');
        }
        
        //Set connection options
        $connection_options = array(
            'adapter'   =>  'thrift',
            'thrift'    =>  array(
                'host'      =>  $options['host'],
                'port'      =>  $options['port'],
                'debug'     =>  $options['debug'],
                'callback'  =>  'scribeClient',
                'package'   =>  'Scribe'
            )
        );
        $connection = Core_Connection::getInstance($connection_options);
        
        //Set logger
        $this->setLogger($connection->getInstance());
    }

    /**
    * Destructor
    */
    public function __destruct()
    {
        //Set logger
        $this->setLogger(null);
    }

    /**
     * Write message to logger
     * @param <array> $arrData
     */
    public function write($arrData)
    {
        try
        {
            //Check information
            if(sizeof($arrData) != 2)
            {                
                return false;
            }

            //Create new LogEntry
            $messages = array();
            $msg = new Core_Logger_Input_Scribe();
            $msg->category = $arrData['category'];
            $msg->message = $arrData['message'];
            $messages[] = $msg->getData();

            //Write log
            $iResult = $this->logger->Log($messages);
            
            //Return data
            return $iResult;
        }
        catch(Core_Logger_Exception $ex)
        {
             echo "Failed logging with exception: ".$ex->getMessage()." \n";
        }
        
        //Return default
        return false;
    }
}

