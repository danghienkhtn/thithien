<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Nosql_Adapter_Redis
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to storage (http://github.com/owlient/phpredis)
 */
class Core_Nosql_Adapter_Redis extends Core_Nosql_Adapter_Abstract
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
            throw new Core_Nosql_Exception('Input host of redis server.');
        }

        //Check port
        if(empty($options['port']))
        {
            throw new Core_Nosql_Exception('Input port of redis server.');
        }

        //Check timout connect
        if(empty($options['timeout']))
        {
            $options['timeout'] = 0;
        }

        //Set connection options
        $connection = new Redis();

        //Check timeout to connect
        if($options['timeout'] > 0)
        {
            $connection->connect($options['host'], $options['port'], $options['timeout']);
        }
        else
        {
            $connection->connect($options['host'], $options['port']);
        }

        //Check password
        if(!empty($options['pwd']))
        {
            if(!$connection->auth($options['pwd']))
            {
                throw new Core_Nosql_Exception('Input password of redis server is wrong.');
            }            
        }

        //Set storage
        $this->setStorage($connection);
    }
    
    /**
    * Destructor
    */
    public function __destruct()
    {
          //Close connection
        if($this->storage)
        {
            $this->storage->close();
        }

        //Set storage
        $this->setStorage(null);  
    }

    /**
     * Select db
     * @param <int> $dbName
     */
    public function selectDb($dbName)
    {
        $dbName = (int)$dbName;
        $this->storage->select($dbName);
    }
}

