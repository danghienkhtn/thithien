<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Data_Adapter_Default
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to serialize and unserialize data
 */
class Core_Data_Adapter_Default extends Core_Data_Adapter_Abstract
{
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
     * Serialize data
     * @param <object> $objectData
     * @return <string>
     */
    public function serialize($objectData)
    {
        return serialize($objectData);
    }

    /**
     * Unserialize data
     * @param <string> $stringData
     * @return <object>
     */
    public function unserialize($stringData)
    {
        return unserialize($stringData);
    }
}

