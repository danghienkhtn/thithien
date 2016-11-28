<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Logger_Adapter_Abstract
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to logger
 */
abstract class Core_Logger_Adapter_Abstract
{
    /**
    * Logger instance
    * @var Core_Logger
    */
    protected $logger = null;

    /**
    * Set Logger
    * @param Core_Logger $logger
    */
    protected function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * Write message to logger
     * @param <string> $message
     */
    abstract protected function write($message);

    /**
     * Get options child
     * @param <array> $options
     * @return <array>
     */
    protected function getOptions($options)
    {
        return $options[$options['adapter']];
    }
}

