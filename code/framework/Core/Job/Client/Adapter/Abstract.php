<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Job_Client_Adapter_Abstract
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to worker
 */
abstract class Core_Job_Client_Adapter_Abstract
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
    * Run background register task to server job
    * @param string $register_function
    * @param array $array_data
    * @param int $unique
    */
    abstract protected function doBackgroundTask($register_function, $array_data, $unique = null);

    /**
    * Run background register task to server job
    * @param string $register_function
    * @param array $array_data
    * @param int $unique
    */
    abstract protected function doHighBackgroundTask($register_function, $array_data, $unique = null);

    /**
    * Run background register task to server job
    * @param string $register_function
    * @param array $array_data
    * @param int $unique
    */
    abstract protected function doLowBackgroundTask($register_function, $array_data, $unique = null);

    /**
    * Run foreground register task to server job
    * @param string $register_function
    * @param array $array_data
    * @param int $unique
    */
    abstract protected function doTask($register_function, $array_data, $unique = null);

    /**
    * Run foreground register task to server job
    * @param string $register_function
    * @param array $array_data
    * @param int $unique
    */
    abstract protected function doHighTask($register_function, $array_data, $unique = null);

    /**
    * Run foreground register task to server job
    * @param string $register_function
    * @param array $array_data
    * @param int $unique
    */
    abstract protected function doLowTask($register_function, $array_data, $unique = null);    
}

