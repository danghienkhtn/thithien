<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Logger_Input_Scribe
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to logger
 */
class Core_Logger_Input_Scribe extends Core_Logger_Input_Abstract
{
    /**
     * Message to write log
     * @var <string>
     */
    public $message;

    /**
     * Category to logger
     * @var <string>
     */
    public $category;

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
       $this->category = null;
    }

    /**
     * Convert object to type using input to logger
     * @return <type>
     */
    public function getData()
    {
        //Create new LogEntry
        $msg = new LogEntry;
        $msg->category = $this->category;
        $msg->message = $this->message;
        return $msg;
    }
}

