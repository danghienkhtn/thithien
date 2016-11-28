<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Connection_Adapter_Curl
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to connection
 */
class Core_Connection_Adapter_Curl extends Core_Connection_Adapter_Abstract
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
            throw new Core_Connection_Exception('Input host for Curl.');
        }

        //Check port
        if(empty($options['port']))
        {
            throw new Core_Connection_Exception('Input port for Curl.');
        }

        //Set dsn string
        $this->setDsn('http://'.$options['host'].':'.$options['port']);

        //Set instance
        $curl = curl_init();
        $this->setInstance($curl);
    }

    /**
    * Destructor
    */
    public function __destruct()
    {
        //Close connection
        if($this->instance)
        {
            curl_close($this->instance);
        }
        
        //Clean nup
        unset($this->dsn, $this->instance);
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
     * @param string $dsn
     */
    public function setDsn($dsn)
    {
        $this->dsn = $dsn;
    }
}

