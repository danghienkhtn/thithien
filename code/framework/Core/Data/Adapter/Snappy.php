<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Data_Adapter_Snappy
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to serialize and unserialize data
 */
class Core_Data_Adapter_Snappy extends Core_Data_Adapter_Abstract
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
        //Check number data
        if(is_numeric($objectData))
        {
            return $objectData;
        }
        
        //Return data
        return snappy_compress($objectData);
    }

    /**
     * Unserialize data
     * @param <string> $stringData
     * @return <object>
     */
    public function unserialize($stringData)
    {
        //Check number data
        if(is_numeric($stringData))
        {
            return $stringData;
        }
        
        //Return data
        return snappy_uncompress($stringData);
    }
}

