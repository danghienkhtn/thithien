<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Nosql_Adapter_Mongo
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to storage http://pecl.php.net/package/mongo
 */
class Core_Nosql_Adapter_Mongo extends Core_Nosql_Adapter_Abstract
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
            throw new Core_Nosql_Exception('Input host of mongo server.');
        }

        //Check port
        if(empty($options['port']))
        {
            throw new Core_Nosql_Exception('Input port of mongo server.');
        }

        //Check connected
        if(empty($options['connected']))
        {
            $options['connected'] = true;
        }

        //Check persistent
        if(empty($options['persistent']))
        {
            $options['persistent'] = true;
        }

        //Add host with port
        $options['host'] .= ':' . $options['port'];

        //Set connection options
        $connection = new Mongo($options['host'], $options['connected'], $options['persistent']);

        //Check user
        if(!empty($options['user']))
        {
            if(!$connection->auth($options['user'], $options['pwd']))
            {
                throw new Core_Nosql_Exception('Input user and password of mongo server is wrong.');
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
     * Check auth user and password login
     * @param <string> $username
     * @param <string> $password
     * @return <bool>
     */
    private function auth($username, $password)
    {
        //Get nonce
        $nonce = $this->storage->command(array('getnonce'=>1));
        $nonce = $nonce['nonce'];

        // create a digest of nonce/username/pwd
        $digest = md5($nonce . $username . md5("$username:mongo:$password"));
        $data   = array('authenticate' => 1,
                        'user' => $username,
                        'nonce' => $nonce,
                        'key' => $digest);

        //Execute command
        $auth = $this->storage->command($data);

        //Return data
        return $auth['ok'];
    }

    /**
     * Select db
     * @param <string> $dbName
     */
    public function selectDb($dbName)
    {       
        $this->storage->selectDB($dbName);
    }

    /**
     * Gets a database collection
     * @param <type> $collection
     * @return <object> new collection object
     */
    public function selectCollection($collectionName)
    {
        return $this->storage->selectCollection($collectionName);
    }

    /**
     * Querys this collection, returning a single element
     * @param <MongoCollection> $collectionObject
     * @param <array> $query
     * @param <array> $fields
     * @return <array>
     */
    public function queryRow($collectionObject, $query, $fields = array())
    {
        if(is_array($fields) && sizeof($fields) > 0)
        {
            return $collectionObject->findOne($query, $fields);
        }
        return $collectionObject->findOne($query);
    }

    /**
     * Querys this collection
     * @param <MongoCollection> $collectionObject
     * @param <array> $query
     * @param <array> $fields
     * @return <array>
     */
    public function queryAll($collectionObject, $query, $fields = array())
    {
        if(is_array($fields) && sizeof($fields) > 0)
        {
            return $collectionObject->findOne($query, $fields);
        }
        return $collectionObject->findOne($query);
    }

    /**
     * Inserts an array into the collection
     * @param <MongoCollection> $collectionObject
     * @param <array> $criteria
     * @return <MongoCollection>
     */
    public function insert($collectionObject, $criteria, $options = array())
    {
        return $collectionObject->insert($criteria, $options);
    }

    /**
     * Inserts multiple documents into this collection
     * @param <MongoCollection> $collectionObject
     * @param <array> $criteria
     * @return <MongoCollection>
     */
    public function batchInsert($collectionObject, $criteria, $options = array())
    {
        return $collectionObject->batchInsert($criteria, $options);
    }

    /**
     * Saves an object to this collection
     * @param <MongoCollection> $collectionObject
     * @param <array> $criteria
     * @return <MongoCollection>
     */
    public function save($collectionObject, $criteria, $options = array())
    {
        return $collectionObject->save($criteria, $options);
    }

    /**
     * Remove records from this collection
     * @param <MongoCollection> $collectionObject
     * @param <array> $criteria
     * @return <MongoCollection>
     */
    public function remove($collectionObject, $criteria, $options = array())
    {
        return $collectionObject->remove($criteria, $options);
    }

    /**
     * Update records based on a given criteria
     * @param <MongoCollection> $collectionObject
     * @param <array> $criteria
     * @param <array> $newobj
     * @param <array> $options
     * @return <MongoCollection>
     */
    public function update($collectionObject, $criteria, $newobj, $options = array())
    {
        return $collectionObject->update($criteria, $newobj, $options);
    }

    /**
     * Performs an operation similar to SQL's GROUP BY command
     * @param <MongoCollection> $collectionObject
     * @param <mixed> $keys
     * @param <array> $initial
     * @param <MongoCode> $reduce
     * @param <array> $options
     * @return <MongoCollection>
     */
    public function group($collectionObject, $keys, $initial, $reduce, $options = array())
    {
        return $collectionObject->group($collectionObject, $keys, $initial, $reduce, $options);
    }
}

