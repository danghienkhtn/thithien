<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Cache_Profiler_Adapter_Html
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to caching
 */
class Core_Cache_Profiler_Adapter_Html extends Core_Cache_Profiler_Adapter_Abstract
{        
    /**
    * List Keys
    * @var array
    */
    private $_keys = array();

    /**
    * Constructor
    *
    */
    public function __construct($options=array())
    {        
        //Check name
        if(empty($options['name']))
        {
            throw new Core_Cache_Exception('Input Name of Profiler.');
        }

        //Get keys
        $this->_keys = $this->getKeys($options['name']);
        
        //Set profiler name
        $this->setProfilerName($options['name']);
    }

    /**
    * Destructor
    *
    */
    public function __destruct()
    {
        unset($this->_keys);
    }

    /**
    * Add data
    *
    * @param string $key
    * @param object
    */
    public function addDataCache($key, $data)
    {
        //Check false data
        if($data === false)
        {
            $data = NULL;
        }

        //Check empty data
        if(empty($this->_keys[$key]['data']))
        {
            $this->_keys[$key]['data'] = NULL;
        }
        
        //Check data is object
        if(is_object($data))
        {
            $data = serialize($data);
        }
        
        //Export data
        $this->_keys[$key]['data'] = var_export($data, true);

        //Update keys
        $this->setKeys($this->profiler_name, $this->_keys);
    }

    /**
    * addTotalMissesCache
    * @param string $key
    * @param int $add
    */
    public function addTotalMissesCache($key, $add=0)
    {
        if(!isset($this->_keys[$key]['total_misses']))
        {
            $this->_keys[$key]['total_misses'] = 0;
        }
        $this->_keys[$key]['total_misses'] += $add;

        //Update keys
        $this->setKeys($this->profiler_name, $this->_keys);
    }

    /**
    * addTotalHitsCache
    * @param string $key
    * @param int $add
    */
    public function addTotalHitsCache($key, $add=0)
    {
        if(!isset($this->_keys[$key]['total_hits']))
        {
            $this->_keys[$key]['total_hits'] = 0;
        }
        $this->_keys[$key]['total_hits'] += $add;

        //Update keys
        $this->setKeys($this->profiler_name, $this->_keys);
    }

    /**
    * addTotalEllapsedTime
    * @param string $key
    * @param int $add
    */
    public function addTotalEllapsedTime($key, $add=0)
    {
        if(!isset($this->_keys[$key]['total_time']))
        {
            $this->_keys[$key]['total_time'] = 0;
        }
        $this->_keys[$key]['total_time'] += $add;

        //Update keys
        $this->setKeys($this->profiler_name, $this->_keys);
    } 
}

