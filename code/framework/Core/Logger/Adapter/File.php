<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Logger_Adapter_File
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to logger by file
 */
class Core_Logger_Adapter_File extends Core_Logger_Adapter_Abstract
{
    /**
    * Constructor
    *
    */
    public function __construct($options)
    {
        //Get options child
        $options = $this->getOptions($options);

        //Check log_dir
        if(empty($options['log_dir']))
        {
            throw new Core_Logger_Exception('Input Log Directory.');
        }

        //Add writer
        $logger = new Zend_Log();
        $writer = new Zend_Log_Writer_Stream($options['log_dir']);
        $logger->addWriter($writer);

        //Set logger
        $this->setLogger($logger);
    }

    /**
    * Destructor
    */
    public function __destruct()
    {
        //Set logger
        $this->setLogger(null);
    }

    /**
     * Write message to logger
     * @param <string> $message
     */
    public function write($message)
    {        
        try
        {
            //Write log
            $this->logger->log($message, Zend_Log::INFO);
        }
        catch(Core_Logger_Exception $ex)
        {
             echo "Failed logging with exception: ".$ex->getMessage()." \n";
        }
    }
}

