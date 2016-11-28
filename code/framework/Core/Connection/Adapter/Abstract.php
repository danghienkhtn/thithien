<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Connection_Adapter_Abstract
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to connection
 */
abstract class Core_Connection_Adapter_Abstract
{
    /**
     * Current connection object
     * @var object
     */
    public $instance = null;

    /**
     * Current Dsn string
     */
    protected $dsn = null;

    /**
     * Get dsn of current connection
     * @return <string>
     */
    abstract protected function getDsn();

    /**
     * Set dsn object
     * @param <object> $dsn
     */
    abstract protected function setDsn($dsn);

    /**
     * Set instance of current connection
     * @param <object> $instance
     */
    protected function setInstance($instance)
    {
        $this->instance = $instance;
    }

    /**
     * Get instance of current connection
     * @return $instance
     */
    public function getInstance()
    {
        return $this->instance;
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
}

