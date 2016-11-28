<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Cache_Adapter_TwoLevels
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to caching
 */
class Core_Cache_Adapter_TwoLevels extends Core_Cache_Adapter_Abstract
{
    /**
     * Slow Backend
     *
     * @var Core_Cache_Adapter_Abstract
     */
    protected $_slowBackend;

    /**
     * Fast Backend
     *
     * @var Core_Cache_Adapter_Abstract
     */
    protected $_fastBackend;
    
    /**
    * Constructor
    *
    */
    public function __construct($options)
    {
        //Get options child
        $options = $this->getOptions($options);
       
        //Check fastBackend options
        if(empty($options['fast_backend']))
        {
            throw new Core_Cache_Exception('Input fastBackend options.');
        }

        //Check fastBackend (only use apc or xcache)
        $arrFastBackend = array('apc', 'xcache');
        if(in_array(strtolower($options['fast_backend']['adapter']), $arrFastBackend) === false)
        {
            throw new Core_Cache_Exception('Input fastBackend only APC or Xcache.');
        }
        
        //Get fastBackend
        $this->_fastBackend = Core_Cache::getInstance($options['fast_backend']);

        //Check slowBackend options
        if(empty($options['slow_backend']))
        {
            throw new Core_Cache_Exception('Input slowBackend options.');
        }

        //Get slowBackend
        $this->_slowBackend = Core_Cache::getInstance($options['slow_backend']);

        //Cleanup
        unset($options);
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
        //Get data from fastBackend
        $data = $this->_fastBackend->read($key);
        
        //If empty data
        if(empty($data))
        {           
            $data = $this->_slowBackend->read($key);
        }

        return $data;
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
        //Get data from fastBackend
        $data = $this->_fastBackend->readMulti($arrKeys);

        //If empty data
        if(empty($data))
        {
            $data = $this->_slowBackend->readMulti($arrKeys);
        }

        return $data;
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
        //Write data
        $boolFast = $this->_fastBackend->write($key, $data, $expire, $flag);
        $boolSlow = $this->_slowBackend->write($key, $data, $expire, $flag);

        //Return data
        return $boolFast && $boolSlow;
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
        //Write data
        $boolFast = $this->_fastBackend->writeMulti($arrKeys, $expire, $flag);
        $boolSlow = $this->_slowBackend->writeMulti($arrKeys, $expire, $flag);

        //Return data
        return $boolFast && $boolSlow;
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
        //Delete data
        $boolFast = $this->_fastBackend->delete($key, $timeout);
        $boolSlow = $this->_slowBackend->delete($key, $timeout);

        //Return data
        return $boolFast && $boolSlow;
    }

    /**
    * Delete list cache
    * @param array $arrKeys
    * Example : array('key1', 'key2')
    * @param int $timeout
    */
    public function deleteMulti($arrKeys, $timeout=0)
    {
        //Delete data
        $boolFast = $this->_fastBackend->deleteMulti($arrKeys, $timeout);
        $boolSlow = $this->_slowBackend->deleteMulti($arrKeys, $timeout);

        //Return data
        return $boolFast && $boolSlow;
    }

    /**
    * Increment single
    * @param string $key
    * @param int $add
    */
    public function increment($key, $add=1)
    {
        //Increment data
        $boolFast = $this->_fastBackend->increment($key, $add);
        $boolSlow = $this->_slowBackend->increment($key, $add);

        //Return data
        return $boolFast && $boolSlow;
    }

    /**
    * Increment multiple
    * @param array $arrKeys
    * Example : array('key1', 'key2')
    * @param int $add
    */
    public function incrementMulti($arrKeys, $add=1)
    {
        //Increment data
        $boolFast = $this->_fastBackend->incrementMulti($arrKeys, $add);
        $boolSlow = $this->_slowBackend->incrementMulti($arrKeys, $add);

        //Return data
        return $boolFast && $boolSlow;
    }

    /**
    * Decrement single
    * @param string $key
    * @param int $add
    */
    public function decrement($key, $add=1)
    {
        //Decrement data
        $boolFast = $this->_fastBackend->decrement($key, $add);
        $boolSlow = $this->_slowBackend->decrement($key, $add);

        //Return data
        return $boolFast && $boolSlow;
    }

    /**
    * Decrement multiple
    * @param array $arrKeys
    * Example : array('key1', 'key2')
    * @param int $value
    */
    public function decrementMulti($arrKeys, $value=1)
    {
        //Decrement data
        $boolFast = $this->_fastBackend->decrementMulti($key, $add);
        $boolSlow = $this->_slowBackend->decrementMulti($key, $add);

        //Return data
        return $boolFast && $boolSlow;
    }

    /**
     * The filling percentage of the caching storage
     * @return int integer between 0 and 100
     */
    public function getFillingPercentage()
    {
        //Return percent
        return $this->_fastBackend->getFillingPercentage();
    }
}

