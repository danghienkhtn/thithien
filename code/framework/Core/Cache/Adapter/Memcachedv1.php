<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Cache_Adapter_Memcachedv1
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to caching by memcache client version < 3.0
 */
class Core_Cache_Adapter_Memcachedv1 extends Core_Cache_Adapter_Abstract
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
        if(empty($options['port']))
        {
            throw new Core_Cache_Exception('Input TCP Port for Memcached server.');
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
    	$this->memcached = new Memcached();

        //Set options
        $this->memcached->setOption(Memcached::OPT_HASH, Memcached::HASH_MURMUR);
        
        //Check compress options
        if($this->compression)
        {
            $this->memcached->setOption(Memcached::OPT_COMPRESSION, true);
        }
        
        //Set connection timeout
        $this->memcached->setOption(Memcached::OPT_CONNECT_TIMEOUT, 1500);        
        
        //Set read and write timeout
        $this->memcached->setOption(Memcached::OPT_RECV_TIMEOUT, 1000);
        $this->memcached->setOption(Memcached::OPT_SEND_TIMEOUT, 3000);
        
        //Set binary protocol
        $this->memcached->setOption(Memcached::OPT_BINARY_PROTOCOL, true);
        
        //Set consistent hashing and other options
        $this->memcached->setOption(Memcached::OPT_LIBKETAMA_COMPATIBLE, true);
        $this->memcached->setOption(Memcached::OPT_NO_BLOCK, true);
        $this->memcached->setOption(Memcached::OPT_TCP_NODELAY, true);
        //$this->memcached->setOption(Memcached::OPT_SERIALIZER, Memcached::SERIALIZER_IGBINARY);
                
        //Get list server data
        $arrServerHost = explode(",", $options['host']);
        $arrServerPort = explode(",", $options['port']);
        $arrServerWeight = explode(",", $options['weight']);
        
        //Set array server data
        $arrServers = array();
        
        //Loop to put data to server
        foreach($arrServerHost as $iLoop => $sServerHost)
        {
            $arrServers[] = array(
                $sServerHost,
                $arrServerPort[$iLoop],
                $arrServerWeight[$iLoop]
            );
        }
        
    	//Add server
    	$this->memcached->addServers($arrServers);

        //Cleanup
        unset($options);
    }

    /**
    * Destructor
    */
    public function __destruct()
    {
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
        if($this->memcached->getResultCode() === Memcached::RES_NOTFOUND)
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

        //Cas null data
        $nullCas = null;
        
        //Get data
        $data = $this->memcached->getMulti($arrKeys, $nullCas, Memcached::GET_PRESERVE_ORDER);

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

        // Return data
        return $this->memcached->set($key, $data, $expire);
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
        
        // Set data
        $this->memcached->setMulti($arrKeys, $expire);
        
        // Retur data
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
        
        //Delete multi data
        $this->memcached->deleteMulti($arrKeys, $timeout);

        //Return data
        return true;
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

        //Loop increment caching
        foreach($arrKeys as $key)
        {
            //Add prefix key
            if(!$this->isEmptyKeyPrefix())
            {
                $key = $this->setKeyPrefix($key);
            }
            $this->memcached->increment($key, $value);
        }

        return true;
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

        //Loop decrement caching
        foreach($arrKeys as $key)
        {
            //Add prefix key
            if(!$this->isEmptyKeyPrefix())
            {
                $key = $this->setKeyPrefix($key);
            }
            $this->memcached->decrement($key, $value);
        }

        return true;
    }

    /**
     * The filling percentage of the caching storage
     * @return int integer between 0 and 100
     */
    public function getFillingPercentage()
    {
        // Get stat data
        $mems = $this->memcached->getStats();

        //Loop data
        $memSize = null;
        $memUsed = null;
        foreach($mems as $mem)
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

