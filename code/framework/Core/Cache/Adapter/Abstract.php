<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Cache_Adapter_Abstract
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to caching
 */
abstract class Core_Cache_Adapter_Abstract
{    
    /**
    * Flag use for debug
    * @var boolean
    */
    protected $debug = false;
            
    /**
    * Flag use for compression
    * @var boolean
    */
    protected $compression = false;

    /**
    * Prefix of caching
    * @var string
    */
    protected $prefix = '';

    /**
    * Profiler debug
    * @var Core_Cache_Profiler
    */
    protected $profiler = null;

    /**
    * Set debug
    * @param boolean $debug
    */
    protected function setDebug($debug)
    {
        $this->debug = $debug;
    }
    
    /**
    * Set prefix of caching (prefix of caching)
    * @param string $prefix
    */
    public function setCachingPrefix($prefix='')
    {
        $this->prefix = $prefix;
    }

    /**
    * Set key for caching
    * @param string $key
    */
    protected function setKeyPrefix($key)
    {
        //Add prefix data
        $key = (!empty($this->prefix))?$this->prefix.':'.$key:$key;
        
        //Return data
        //return md5($key);
        return $key;
    }

    /**
     * Check key prefix
     * @return <bool>
     */
    protected function isEmptyKeyPrefix()
    {
        return (empty($this->prefix))?true:false;
    }

    /**
    * Set compression
    * @param boolean $compression
    */
    protected function setCompression($compression)
    {
        $this->compression = $compression;
    }
    
    /**
     * Get options child
     * @param <array> $options
     * @return <array>
     */
    protected function getOptions($options)
    {
        return $options[$options['adapter']];
    }

    /**
     * Set profiler
     * @param <Core_Cache_Profiler> $profiler
     */
    protected function setProfiler($profiler)
    {
        $this->profiler = $profiler;
    }

    /**
    * Get single cache
    *
    * @param string $key
    * @return var
    */
    abstract protected function read($key);

    /**
    * Get List cache
    *
    * @param array $arrKeys
    * Example : array('key1', 'key2')
    * @return var
    */
    abstract protected function readMulti($arrKeys);

    /**
    * Write single cache    
    * @param string $key
    * @param var $data
    * @param int $expire
    * @param int $flag
    * @return boolean
    */
    abstract protected function write($key, $data, $expire=0, $flag=0);

    /**
    * Write list cache
    * @param array $arrKeys
    * Example : array('key1'=>'value1', 'key2'=>'value2')
    * @param int $expire
    * @param int $flag
    */
    abstract protected function writeMulti($arrKeys, $expire=0, $flag=0);

    /**
    * Delete single cache
    *
    * @param string $key
    * @param int $timeout
    * @return boolean
    */
    abstract protected function delete($key, $timeout=0);

    /**
    * Delete list cache
    * @param array $arrKeys
    * Example : array('key1', 'key2')
    * @param int $timeout
    */
    abstract protected function deleteMulti($arrKeys, $timeout=0);

    /**
    * Increment single
    * @param string $key
    * @param int $value
    */
    abstract protected function increment($key, $value=1);

    /**
    * Increment multiple
    * @param array $arrKeys
    * Example : array('key1', 'key2')
    * @param int $value
    */
    abstract protected function incrementMulti($arrKeys, $value=1);

    /**
    * Decrement single
    * @param string $key
    * @param int $value
    */
    abstract protected function decrement($key, $value=1);

    /**
    * Decrement multiple
    * @param array $arrKeys
    * Example : array('key1', 'key2')
    * @param int $value
    */
    abstract protected function decrementMulti($arrKeys, $value=1);

    /**
     * The filling percentage of the caching storage
     * @return int integer between 0 and 100
     */
    abstract protected function getFillingPercentage();

    /**
     * Save some string datas into a cache record
     *
     * Note : $data is always "string" (serialization is done by the
     * core not by the backend)
     *
     * @param  string $data             Datas to cache
     * @param  string $id               Cache id
     * @param  array  $tags             Array of strings, the cache record will be tagged by each string entry
     * @param  int    $specificLifetime If != false, set a specific lifetime for this cache record (null => infinite lifetime)
     * @return boolean True if no problem
     */
    public function save($data, $id, $tags = array(), $specificLifetime = false)
    {
        return $this->write($id, $data, $specificLifetime, 0);
    }

    /**
     * Remove a cache record
     *
     * @param  string $id Cache id
     * @return boolean True if no problem
     */
    public function remove($id)
    {
        return $this->delete($id);
    }

    /**
     * Test if a cache is available for the given id and (if yes) return it (false else)
     *
     * @param  string  $id Cache id
     * @param  boolean $doNotTestCacheValidity If set to true, the cache validity won't be tested
     * @return string|false cached datas
     */
    public function load($id, $doNotTestCacheValidity = false)
    {
        return $this->read($id);
    }
}

