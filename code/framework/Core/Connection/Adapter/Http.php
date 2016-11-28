<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Connection_Adapter_Http
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to connection
 */
class Core_Connection_Adapter_Http extends Core_Connection_Adapter_Abstract
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
            throw new Core_Connection_Exception('Input host for HttpClient.');
        }

        //Check port
        if(empty($options['port']))
        {
            throw new Core_Connection_Exception('Input port for HttpClient.');
        }

        //Set dsn string
        $this->setDsn('http://'.$options['host'].':'.$options['port']);

        //Cleanup params
        unset($options['host'], $options['port']);

        //Set instance
        $http = new Zend_Http_Client($this->getDsn(), $options);
        $this->setInstance($http);
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

