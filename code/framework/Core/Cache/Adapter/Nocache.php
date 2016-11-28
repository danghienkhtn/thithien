<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Cache_Adapter_Nocache
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to caching
 */
class Core_Cache_Adapter_Nocache extends Core_Cache_Adapter_Abstract
{
    /**
    * Constructor
    *
    */
    public function __construct($options)
    {
        //Nothing
    }

    /**
    * Destructor
    */
    public function __destruct()
    {
        //Nothing
    }

    /**
    * Get single cache
    *
    * @param string $key
    * @return var
    */
    public function read($key)
    {
        return false;
    }

    /**
    * Get List cache
    *
    * @param array $arrKeys
    * Example : array('key1', 'key2')
    * @return var
    */
    public function readMulti($arrKeys)
    {
        return false;
    }

    /**
    * Write single cache
    *
    * @param string $key
    * @param var $data
    * @param int $expire
    * @return boolean
    */
    public function write($key, $data, $expire=0, $flag=0)
    {
        return true;
    }

    /**
    * Write list cache
    * @param array $arrKeys
    * Example : array('key1'=>'value1', 'key2'=>'value2')
    * @param int $flag
    * @param int $expire
    */
    public function writeMulti($arrKeys, $expire=0, $flag=0)
    {
        return true;
    }

    /**
    * Delete single cache
    *
    * @param string $key
    * @param int $timeout
    * @return boolean
    */
    public function delete($key, $timeout=0)
    {
        return true;
    }

    /**
    * Delete list cache
    * @param array $arrKeys
    * Example : array('key1', 'key2')
    * @param int $timeout
    */
    public function deleteMulti($arrKeys, $timeout=0)
    {        
        return true;
    }

    /**
    * Increment single
    * @param string $key
    * @param int $add
    */
    public function increment($key, $add=1)
    {
        return true;
    }

    /**
    * Increment multiple
    * @param array $arrKeys
    * Example : array('key1', 'key2')
    * @param int $add
    */
    public function incrementMulti($arrKeys, $add=1)
    {
        return true;
    }

    /**
    * Decrement single
    * @param string $key
    * @param int $add
    */
    public function decrement($key, $add=1)
    {
        return true;
    }

    /**
    * Decrement multiple
    * @param array $arrKeys
    * Example : array('key1', 'key2')
    * @param int $value
    */
    public function decrementMulti($arrKeys, $value=1)
    {
        return true;
    }

    /**
     * The filling percentage of the caching storage
     * @return int integer between 0 and 100
     */
    public function getFillingPercentage()
    {
        //Return percent
        return 100;
    }
}

