<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Data_Adapter_Abstract
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to serialize and unserialize data
 */
abstract class Core_Data_Adapter_Abstract
{
    /**
     * Serialize data
     * @param <object> $objectData
     */
    abstract protected function serialize($objectData);

    /**
     * Unserialize data
     * @param <string> $stringData
     */
    abstract protected function unserialize($stringData);
}

