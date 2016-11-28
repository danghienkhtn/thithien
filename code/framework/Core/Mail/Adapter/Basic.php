<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Mail_Adapter_Basic
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to send email
 */
class Core_Mail_Adapter_Basic extends Core_Mail_Adapter_Abstract
{
    /**
    * Constructor
    *
    */
    public function __construct($options)
    {
        //Get mail instance
        $mail = new Zend_Mail('utf-8');

        //Set mail instance
        $this->setConnection($mail);
    }

    /**
    * Destructor
    */
    public function __destruct()
    {
        //Set mail instance
        $this->setConnection(null);
    }
}

