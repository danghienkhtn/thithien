<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Connection_Adapter_Thrift
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to connection
 */
class Core_Connection_Adapter_Thrift extends Core_Connection_Adapter_Abstract
{
    /**
     * Transport instance
     * @var <object>
     */
    private $transport = null;
    
    /**
     * Retry number to loop connection
     */
    private $iRetry = 1;
    
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
            throw new Core_Connection_Exception('Input host for Thrift.');
        }

        //Check port
        if(empty($options['port']))
        {
            throw new Core_Connection_Exception('Input port for Thrift.');
        }

        //Check callback
        if(empty($options['callback']))
        {
            throw new Core_Connection_Exception('Input callback class for Thrift.');
        }

        //Check debug
        if(empty($options['debug']))
        {
            $options['debug'] = 0;
        }
        
        //Check persist
        if(empty($options['persist']))
        {
            $options['persist'] = false;
        }

        //Check send_timeout
        if(empty($options['send_timeout']))
        {
            $options['send_timeout'] = 3500;
        }

        //Check recv_timeout
        if(empty($options['recv_timeout']))
        {
            $options['recv_timeout'] = 4500;
        }        
        
        //Get retry number
        $this->iRetry = isset($options['retry'])?$options['retry']:3;
        
        //Set dsn string
        $this->setDsn(array($options['host'], $options['port']));

        //Get package
        $package = strtolower($options['package']);
        
        //Socket status handler
        $isLive = false;
        $socket = NULL;
        $iLoop = 0;
        
        //Auto load all files        
        Core_Loader::register($GLOBALS['THRIFT_ROOT'] . '/packages/' . ucfirst($package));
        
        //Loop to connect to our server
        while((($socket == NULL) || ($this->transport == NULL) || ($isLive == false)) && ($iLoop < $this->iRetry))
        {
            //Try to connect
            try
            {
                //Get list server data
                $arrServerHost = explode(",", $options['host']);
                $arrServerPort = explode(",", $options['port']);
                
                //Make a connection to the Thrift interface        
                if((sizeof($arrServerHost) > 1) || ($package == 'scribe'))
                {
                    $socket = new TSocketPool($arrServerHost, $arrServerPort, $options['persist']);
                }
                else
                {
                    $socket = new TSocket($options['host'], $options['port'], $options['persist']);
                }       

                //Set option socket
                $socket->setDebug($options['debug']);
                $socket->setSendTimeout($options['send_timeout']);
                $socket->setRecvTimeout($options['recv_timeout']); 

                //Set transport and protocol        
                switch($package)
                {                    
                    case 'youtube':                        
                        $this->transport = new TBufferedTransport($socket, 8192, 8192);
                        $protocol = new TBinaryProtocolAccelerated($this->transport);
                        break;
                    default:                        
                        $this->transport = new TFramedTransport($socket);      
                        $protocol = new TBinaryProtocol($this->transport);
                        break;
                } 
                
                //Set instance for socket
                $clientInstance = new $options['callback']($protocol);
                
                //Set client instance
                $this->setInstance($clientInstance);

                //Open transport
                $this->transport->open();
                
                //Set status is OK
                $isLive =  true;
            }
            catch(Exception $ex)
            {
                //Debug information
                if($options['debug'] == 1)
                {
                    var_dump('<pre>', $ex->getMessage());
                    exit();
                }
                
                //Close connection
                if($this->transport)
                {
                    $this->transport->close();
                    $this->transport = null;
                }
                
                //Set status is failure
                $isLive =  false;
            }
            
            //Increase looping
            $iLoop++;
        }
    }

    /**
    * Destructor
    */
    public function __destruct()
    {
        //Close connection
        if($this->transport)
        {
            $this->transport->close();
        }

        //Clean nup
        unset($this->dsn, $this->transport, $this->instance);
    }

    /**
     * Get dsn of current connection
     */
    public function getDsn()
    {
        return $this->dsn;
    }

    /**
     * Set dsn string
     * @param array $dsn
     */
    public function setDsn($dsn)
    {
        $this->dsn = $dsn;
    }
}

