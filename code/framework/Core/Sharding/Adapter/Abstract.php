<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Sharding_Adapter_Abstract
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to shading storage
 */
abstract class Core_Sharding_Adapter_Abstract
{
    /**
     * Array backends
     * @var <array>
     */
    protected $backends = array();

    /**
     * Number of backends
     * @var <int>
     */
    protected $backends_count = 0;

    /**
     * Array hashrings
     * @var <array>
     */
    protected $hashring = array();

    /**
     * Number of hashrings
     * @var <int>
     */
    protected $hashring_count = 0;

    /**
     * Number of replicas
     * @var <int>
     */
    protected $replicas = 256;

    /**
     * Number of slices_count
     * @var <int>
     */
    protected $slices_count = 0;

    /**
     * Number of slices_div
     * @var <int>
     */
    protected $slices_div = 0;

    /**
     * Array cache
     * @var <array>
     */
    protected $cache = array();

    /**
     * Number of cache_count
     * @var <int>
     */
    protected $cache_count = 0;

    /**
     * Number of cache_max
     * @var <int>
     */
    protected $cache_max = 256;

    /**
     * Set list backend
     * @param <array> $backends
     */
    protected function setBackends($backends)
    {
        //Set backends
        $this->backends = $backends;

        //Set backend count
        $this->backends_count = count($backends);        
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
     * Set replicas number
     * @param <int> $replicas
     */
    public function setReplicas($replicas)
    {
        $this->replicas = $replicas;

        //Make sure that $this->replicas is a multiple of 8.
        if(($this->replicas % 8) !== 0)
        {
            $this->replicas = round($this->replicas / 8) * 8;
        }
    }

    /**
     * Set max of cache
     * @param <int> $cache_max
     */
    public function setCacheMax($cache_max)
    {
        $this->cache_max = $cache_max;
    }

    /**
     * Set hashring information
     */
    abstract protected function setHashring();

    /**
     * Get map of key
     * @param <string> $key The key to map     
     * [optional: only with redundant consistent hashing]
     * @return <int>
     */
    abstract protected function getMap($key);

    /**
     * Get map storage
     * @param <string> $key The key to map
     * @param <int> $mapKey
     * [optional: only with redundant consistent hashing]
     * @return <array>
     */
    public function getPartition($key, &$mapKey)
    {
        //Get key to map
        $mapKey = $this->getMap($key);

        //Return to array storage
        return $this->backends[$mapKey];
    }    
}

