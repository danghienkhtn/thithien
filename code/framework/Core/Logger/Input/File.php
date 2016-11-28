<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Logger_Input_File
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to logger
 */
class Core_Logger_Input_File extends Core_Logger_Input_Abstract
{
    /**
     * Message to write log
     * @var <string>
     */
    public $message;

    /**
    * Constructor
    *
    */
    public function __construct() {}

    /**
    * Destructor
    */
    public function __destruct()
    {
       $this->message = null;
    }

    /**
     * Convert object to type using input to logger
     * @return <type>
     */
    public function getData()
    {
        return $this->message;
    }
}

