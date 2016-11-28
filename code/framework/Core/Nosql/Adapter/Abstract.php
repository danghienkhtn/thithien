<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Nosql_Adapter_Abstract
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to storage
 */
abstract class Core_Nosql_Adapter_Abstract
{
    /**
    * Connection instance
    * @var Core_Connection
    */
    public $storage = null;

    /**
    * Set Connection
    * @param Core_Connection $logger
    */
    protected function setStorage($storage)
    {
        $this->storage = $storage;
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
     * Get storage instance
     * @return <redis>
     */
    public function getStorage()
    {
        return $this->storage;
    }
}

