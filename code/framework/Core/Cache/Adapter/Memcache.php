<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Cache_Adapter_Memcache
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to caching
 */
class Core_Cache_Adapter_Memcache extends Core_Cache_Adapter_Abstract
{
    /**
     * Memcache object
     *
     * @var object
     */
    private $memcached = null;
   
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

        //Check host
        if(empty($options['host']))
        {
            throw new Core_Cache_Exception('Input Host for Memcached server.');
        }

        //Check tcp port
        if(empty($options['tcp_port']))
        {
            $options['tcp_port'] = 0;            
        }

        //Check udp port
        if(empty($options['udp_port']))
        {
            $options['udp_port'] = 0;
        }

        //Check persistent
        if(empty($options['persistent']))
        {
            $options['persistent'] = false;
        }

        //Check weight
        if(empty($options['weight']))
        {
            $options['weight'] = 1;
        }

        //Check timeout
        if(empty($options['timeout']))
        {
            $options['timeout'] = 10;
        }

        //Check retry_interval
        if(empty($options['retry_interval']))
        {
            $options['retry_interval'] = 15;
        }

        //Check retry_interval
        if(empty($options['status']))
        {
            $options['status'] = false;
        }

        //Check prefix
        if(empty($options['prefix']))
        {
            $options['prefix'] = '';
        }       

        //Set compression
        $this->setCompression($options['compression']);

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

        //Create instance memcached
        $this->memcached = new MemcachePool();

        //Get list server data
        $arrServerHost = explode(",", $options['host']);
        $arrServerPort = explode(",", $options['tcp_port']);
        $arrServerUDPPort = explode(",", $options['udp_port']);
        $arrServerWeight = explode(",", $options['weight']);
                
        //Loop to put data to add server
        foreach($arrServerHost as $iLoop => $sServerHost)
        {
            $this->memcached->addServer(
                $sServerHost,
                $arrServerPort[$iLoop],
                $arrServerUDPPort[$iLoop],
                $options['persistent'],
                $arrServerWeight[$iLoop],
                $options['timeout'],
                $options['retry_interval'],
                $options['status']
            );
        }
        
        //Cleanup
        unset($options);
    }

    /**
    * Destructor
    */
    public function __destruct()
    {
        //Close memcache
        if($this->memcached)
        {
            $this->memcached->close();
        }

        //Cleanup
        unset($this->memcached);
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
        $data = $this->memcached->get($key);

        //Check hit        
        if($data === false)
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
            $this->profiler->addTotalEllapsedTime($key,($end - $start));
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

        //Add prefix key
        if(!$this->isEmptyKeyPrefix())
        {
            foreach($arrKeys as $key => $value)
            {
                $arrKeys[$key] = $this->setKeyPrefix($value);
            }
        }        

        //Time start
        if($this->debug)
        {
            $start = microtime(true);
            $listkey= implode(' ,', $arrKeys);
        }

        //Get data
        $data = $this->memcached->get($arrKeys);

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
            $this->profiler->addTotalEllapsedTime($listkey,($end - $start));
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
        //Set flag
        $flag = $this->compression;

        //If empty
        if(empty($key))
        {
            return false;
        }

        //Set key prefix
        $key = $this->setKeyPrefix($key);
        
        return $this->memcached->set($key, $data, $flag, $expire);
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
        //Set flag
        $flag = $this->compression;

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
        if(!$this->isEmptyKeyPrefix())
        {
            foreach($arrKeys as $key => $value)
            {
                $arrKeys[$key] = $this->setKeyPrefix($value);
            }
        }        
        
        return $this->memcached->set($arrKeys, null, $flag, $expire);
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
        
        return $this->memcached->delete($key, $timeout);
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

        //Add prefix key
        if(!$this->isEmptyKeyPrefix())
        {
            foreach($arrKeys as $key => $value)
            {
                $arrKeys[$key] = $this->setKeyPrefix($value);
            }
        }

        return $this->memcached->delete($arrKeys, $timeout);
    }

    /**
    * Increment single
    * @param string $key
    * @param int $value
    */
    public function increment($key, $value=1)
    {
        //If empty
        if(empty($key))
        {
            return false;
        }

        //Set key prefix
        $key = $this->setKeyPrefix($key);
        
        return $this->memcached->increment($key, $value);
    }

    /**
    * Increment multiple
    * @param array $arrKeys
    * Example : array('key1', 'key2')
    * @param int $value
    */
    public function incrementMulti($arrKeys, $value=1)
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
        if(!$this->isEmptyKeyPrefix())
        {
            foreach($arrKeys as $key => $value)
            {
                $arrKeys[$key] = $this->setKeyPrefix($value);
            }
        }
        
        return $this->memcached->increment($arrKeys, $value);
    }

    /**
    * Decrement single
    * @param string $key
    * @param int $value
    */
    public function decrement($key, $value=1)
    {
        //If empty
        if(empty($key))
        {
            return false;
        }

        //Set key prefix
        $key = $this->setKeyPrefix($key);

        return $this->memcached->decrement($key, $value);
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
        if(!$this->isEmptyKeyPrefix())
        {
            foreach($arrKeys as $key => $value)
            {
                $arrKeys[$key] = $this->setKeyPrefix($value);
            }
        }

        return $this->memcached->decrement($arrKeys, $value);
    }

    /**
     * The filling percentage of the caching storage
     * @return int integer between 0 and 100
     */
    public function getFillingPercentage()
    {
        $mems = $this->memcached->getExtendedStats();

        //Loop data
        $memSize = null;
        $memUsed = null;
        foreach($mems as $key => $mem)
        {
            if($mem === false)
            {
                continue;
            }

            $eachSize = $mem['limit_maxbytes'];
            $eachUsed = $mem['bytes'];
            if($eachUsed > $eachSize)
            {
                $eachUsed = $eachSize;
            }

            $memSize += $eachSize;
            $memUsed += $eachUsed;
        }

        //Error warning
        if($memSize === null || $memUsed === null)
        {
            Core_Cache_Exception::throwException('Can\'t get filling percentage');
        }

        //Return percent
        return ((int)(100. *($memUsed / $memSize)));
    }
}

