<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Mail_Adapter_Smtp
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to send email
 */
class Core_Mail_Adapter_Smtp extends Core_Mail_Adapter_Abstract
{
    /**
    * Constructor
    *
    */
    public function __construct($options)
    {
        //Get options child
        $options = $this->getOptions($options);
        
        //Check host type
        if(empty($options['host']))
        {
            $options['host'] = 'smtp.gmail.com';
        }

        //Set smtp options
        $smtpOption = array();

        //Check port type
        if(empty($options['port']))
        {
            $options['port'] = 587;
        }

        //Put port
        $smtpOption['port'] = $options['port'];

        //Check secure
        if(!empty($options['secure']))
        {
            //Check auth type
            if(empty($options['auth']))
            {
                $options['auth'] = 'login';
            }
            
            //Put authenticate
            $smtpOption['auth'] = $options['auth'];

            //Check username type
            if(empty($options['username']))
            {
                throw new Core_Mail_Exception('Input username for SMTP Server.');
            }

            //Check auth type
            if(empty($options['password']))
            {
                throw new Core_Mail_Exception('Input password for SMTP Server.');
            }
            
            //Check name
            if(!empty($options['name']))
            {
                $smtpOption['name'] = $options['name'];
            }

            //Check ssl type
            if(!empty($options['ssl']))
            {
                $smtpOption['ssl'] = $options['ssl'];
            }

            //Put username
            $smtpOption['username'] = $options['username'];

            //Put password
            $smtpOption['password'] = $options['password'];
        }

        //Set transport
        $transport = new Zend_Mail_Transport_Smtp($options['host'], $smtpOption);
        Zend_Mail::setDefaultTransport($transport);
        
        $charset = empty($options['charset']) ? 'utf-8' : $options['charset'];
        
        //Get mail instance
        $mail = new Zend_Mail($charset);

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

