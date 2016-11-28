<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Cache_Profiler
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to caching
 */
abstract class Core_Cache_Profiler_Adapter_Abstract
{
    /**
     * Profiler name
     * @var <string>
     */
    protected $profiler_name = 'registry.profiler.cache';

    /**
    * Constructor
    *
    */
    public function __construct(){}

    /**
    * Destructor
    */
    public function __destruct(){}

    /**
    * getPercentMissesCache
    *
    * @param int $total_hits
    * @param int $total_misses
    * @return int
    */
    private function getPercentMissesCache($total_hits, $total_misses)
    {
        $total = $total_hits + $total_misses;
        if($total == 0)
        {
            return 100;
        }
        $percent = intval($total_misses/$total);
        return $percent * 100;
    }

    /**
    * getPercentHitsCache
    *
    * @param int $total_hits
    * @param int $total_misses
    * @return int
    */
    private function getPercentHitsCache($total_hits, $total_misses)
    {
        $total = $total_hits + $total_misses;
        if($total == 0)
        {
            return 0;
        }
        $percent = intval($total_hits/$total);
        return $percent * 100;
    }

    /**
     * Set profiler name
     * @param string $profiler_name
     */
    protected function setProfilerName($profiler_name)
    {
        $this->profiler_name = $profiler_name;
    }

    /**
     * Set keys of profiler
     * @param string $profiler_name
     * @param array $keys
     */
    protected function setKeys($profiler_name, $keys)
    {
        Zend_Registry::set($profiler_name, $keys);
    }

    /**
     * Get all keys of profiler
     * @param string $profiler_name
     */
    protected function getKeys($profiler_name)
    {
        if(Zend_Registry::isRegistered($profiler_name))
        {
            return Zend_Registry::get($profiler_name);
        }

        //Set keys
        $this->setKeys($profiler_name, array());
        return array();
    }

    /**
    * getProfilerData
    * @param string $profiler_name
    * @return string
    */
    public function getProfilerData()
    {
        //Check key of register
        $keys = array();
        
        //Check profiler
        if(Zend_Registry::isRegistered($this->profiler_name))
        {
            $keys = Zend_Registry::get($this->profiler_name);
        }

        //Get key of register        
        $number = 0;
        $print = '<br/><br/><br/><table style="width:800px;" border="1" cellspacing="2" cellpadding="2"><tr><th colspan="8" bgcolor=\'#dddddd\'>Caching '.$this->profiler_name.' Block</th></tr>';
        $print .= '<tr><td align="center" width="100">Number</td><td align="center" width="20">Key</td><td align="center">Total hits</td><td align="center">Total percent hits</td><td align="center">Total misses</td><td align="center">Total percent misses</td><td align="center">Total ellapsed time (seconds)</td><td align="center">Data cache</td></tr>';
        
        //Loop keys
        foreach($keys as $key => $data)
        {
            $data['total_hits']=isset($data['total_hits'])?$data['total_hits']:0;
            $data['total_misses']=isset($data['total_misses'])?$data['total_misses']:0;
            $getPercentHitsCache = $this->getPercentHitsCache($data['total_hits'], $data['total_misses']);
            $getPercentMissesCache = $this->getPercentMissesCache($data['total_hits'], $data['total_misses']);
            $print .= '<tr><td align="center">'.$number.'</td><td align="center">'.$key.'</td><td align="left">'.$data['total_hits'].'</td><td align="left">'.$getPercentHitsCache.'%</td><td align="left">'.$data['total_misses'].'</td><td align="left">'.$getPercentMissesCache.'%</td><td align="left">'.$data['total_time'].'</td><td align="left">'.$data['data'].'</td></tr>';
            $number++;
        }

        //If number = 0
        if($number == 0)
        {
            $print .= '<tr><td align="center" colspan="8">No index any key for caching</td></tr>';
        }
        $print .= "</table>";

        //Return data
        return $print;
    }
    
    /**
     * Add data
     * @param string $key
     * @param object
     */
    abstract protected function addDataCache($key, $data);

    /**
     * addTotalMissesCache
     * @param string $key
     * @param int $add
     */
    abstract protected function addTotalMissesCache($key, $add=0);

    /**
     * addTotalHitsCache
     * @param string $key
     * @param int $add
     */
    abstract protected function addTotalHitsCache($key, $add=0);

    /**
     * addTotalEllapsedTime
     * @param string $key
     * @param int $add
     */
    abstract protected function addTotalEllapsedTime($key, $add=0);    
}

