<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Cache_Adapter_Xcache
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to caching
 */
class Core_Cache_Adapter_Xcache extends Core_Cache_Adapter_Abstract
{        
    /**
    * Constructor
    *
    */
    public function __construct($options)
    {
        //Set debug
        $this->setDebug($options['debug']);

        //Set profiler
        $oprofiler = $options['profiler'];        

        //Get options child
        $options = $this->getOptions($options);
        
        //Set caching prefix
        $this->setCachingPrefix($options['prefix']);

        //If debug mode
        if($this->debug)
        {            
            //Get Profiler
            $profiler = Core_Cache_Profiler::getInstance($oprofiler);

            //Set Profiler
            $this->setProfiler($profiler);
        }

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
        //If empty
        if(empty($key))
        {
            return false;
        }

        //Set key prefix
        $key = $this->setKeyPrefix($key);

        //Time start
        if($this->debug)
        {
            $start = microtime(true);
        }

        //Get data
        $data = xcache_get($key);

        //Check hit
        if($data === false || $data === NULL)
        {
            //Write Profiler
            if($this->debug)
            {
                $this->profiler->addTotalMissesCache($key, 1);
            }
        }
        else
        {
            //Write Profiler
            if($this->debug)
            {
                $this->profiler->addTotalHitsCache($key, 1);
            }
        }

        //Add time ellapsed
        if($this->debug)
        {
            //Write Profiler
            $end = microtime(true);
            $this->profiler->addTotalEllapsedTime($key, ($end - $start));
            $this->profiler->addDataCache($key, $data);
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
        //If empty
        if(empty($arrKeys))
        {
            return false;
        }

        //Check array
        if(!is_array($arrKeys))
        {
            return false ;
        }

        //Add prefix key and get data
        $data = array();
        foreach($arrKeys as $key => $value)
        {
            $value = $this->setKeyPrefix($value);
            $arrKeys[$key] = $value;
            $data[$value] = xcache_get($value);
        }

        //Time start
        if($this->debug)
        {
            $start = microtime(true);
            $listkey= implode(' ,', $arrKeys);
        }

        //Check hit
        if(empty($data))
        {
            //Write Profiler
            if($this->debug)
            {
                $this->profiler->addTotalMissesCache($listkey, 1);
            }
        }
        else
        {
            //Write Profiler
            if($this->debug)
            {
                $this->profiler->addTotalHitsCache($listkey, 1);
            }
        }

        //Add time ellapsed
        if($this->debug)
        {
            //Write Profiler
            $end = microtime(true);
            $this->profiler->addTotalEllapsedTime($listkey, ($end - $start));
            $this->profiler->addDataCache($listkey, $data);
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
        //If empty
        if(empty($key))
        {
            return false;
        }

        //Set key prefix
        $key = $this->setKeyPrefix($key);

        return xcache_set($key, $data, $expire);
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
        //If empty
        if(empty($arrKeys))
        {
            return false;
        }

        //Check array
        if(!is_array($arrKeys))
        {
            return false ;
        }

        //Add prefix key and write caching
        foreach($arrKeys as $key => $value)
        {
            $key = $this->setKeyPrefix($key);
            xcache_set($key, $value, $expire);
        }

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
        //If empty
        if(empty($key))
        {
            return false;
        }

        //Set key prefix
        $key = $this->setKeyPrefix($key);

        return xcache_unset($key);
    }

    /**
    * Delete list cache
    * @param array $arrKeys
    * Example : array('key1', 'key2')
    * @param int $timeout
    */
    public function deleteMulti($arrKeys, $timeout=0)
    {
        //If empty
        if(empty($arrKeys))
        {
            return false;
        }

        //Check array
        if(!is_array($arrKeys))
        {
            return false ;
        }

        //Add prefix key and delete key
        foreach($arrKeys as $key)
        {
            $key = $this->setKeyPrefix($key);
            xcache_unset($key);
        }

        return true;
    }

    /**
    * Increment single
    * @param string $key
    * @param int $add
    */
    public function increment($key, $add=1)
    {
        //If empty
        if(empty($key))
        {
            return false;
        }

        //Set key prefix
        $key = $this->setKeyPrefix($key);

        //Get data
        $value = $this->read($key);

        //Increment
        if($value === false)
        {
            $value = 0;
        }
        $value += $add;

        //Write data again
        return $this->write($key, $value);
    }

    /**
    * Increment multiple
    * @param array $arrKeys
    * Example : array('key1', 'key2')
    * @param int $add
    */
    public function incrementMulti($arrKeys, $add=1)
    {
        //If empty
        if(empty($arrKeys))
        {
            return false;
        }

        //Check array
        if(!is_array($arrKeys))
        {
            return false ;
        }

        //Add prefix key
        foreach($arrKeys as $key)
        {
            //Set key prefix
            $key = $this->setKeyPrefix($key);

            //Get data
            $value = $this->read($key);

            //Increment
            if($value === false)
            {
                $value = 0;
            }
            $value += $add;

            //Write data again
            $this->write($key, $value);
        }

        return true;
    }

    /**
    * Decrement single
    * @param string $key
    * @param int $add
    */
    public function decrement($key, $add=1)
    {
        //If empty
        if(empty($key))
        {
            return false;
        }

        //Set key prefix
        $key = $this->setKeyPrefix($key);

        //Get data
        $value = $this->read($key);

        //Increment
        if($value === false)
        {
            $value = 0;
        }
        $value -= $add;

        //Write data again
        return $this->write($key, $value);
    }

    /**
    * Decrement multiple
    * @param array $arrKeys
    * Example : array('key1', 'key2')
    * @param int $value
    */
    public function decrementMulti($arrKeys, $value=1)
    {
        //If empty
        if(empty($arrKeys))
        {
            return false;
        }

        //Check array
        if(!is_array($arrKeys))
        {
            return false ;
        }

        //Add prefix key
        foreach($arrKeys as $key)
        {
            //Set key prefix
            $key = $this->setKeyPrefix($key);

             //Get data
            $value = $this->read($key);

            //Increment
            if($value === false)
            {
                $value = 0;
            }
            $value -= $add;

            //Write data again
            $this->write($key, $value);
        }

        return true;
    }

    /**
     * The filling percentage of the caching storage
     * @return int integer between 0 and 100
     */
    public function getFillingPercentage()
    {        
        //Return percent
        return 0;
    }
}

