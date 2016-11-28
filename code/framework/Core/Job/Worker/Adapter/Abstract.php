<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Job_Worker_Adapter_Abstract
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to worker
 */
abstract class Core_Job_Worker_Adapter_Abstract
{
    /**
     * Options configuration
     */
    protected $options = array();
    
    /**
     * Get options
     * @return <array> 
     */
    protected function getConnectOption()
    {
        return $this->options;
    }
    
    /**
     * Set options
     * @return <array> 
     */
    protected function setConnectOption($options)
    {
        $this->options = $options;
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
     * Add function to worker
     * @param string $register_function
     * @param string $callback_function
     * @param var $args
     */
    abstract protected function addFunction($register_function, $callback_function, $args=null);

    /**
     * Get Notify Data in worker
     * @param GearmanJob $job Or memcacheq key
     */
    abstract protected function getNotifyData($job);

    /**
     * Run worker
     */
    abstract protected function run();
}

