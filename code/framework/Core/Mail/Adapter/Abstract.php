<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Mail_Adapter_Abstract
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to send email
 */
abstract class Core_Mail_Adapter_Abstract
{
    /**
     * Zend_Mail object
     */
    protected $connection;
    
    /**
     * Set sender email
     * @param <string> $email
     * @param <string> $alias
     */
    protected function setFrom($email, $alias)
    {
        $this->connection->setFrom($email, $alias);
    }

    /**
     * Set receiver email
     * @param <string> $email
     * @param <string> $alias
     */
    protected function setTo($email, $alias)
    {
        $this->connection->addTo($email, $alias);
    }

    /**
     * Set cc email
     * @param <string> $email
     * @param <string> $alias
     */
    protected function setCc($email, $alias)
    {
        $this->connection->addCc($email, $alias);
    }

    /**
     * Set mail connection
     * @param <Zend_Mail> $connection
     */
    protected function setConnection($connection)
    {
        $this->connection = $connection;
    }

    /**
     * Set bcc email
     * @param <string> $email
     * @param <string> $alias
     */
    protected function setBcc($email, $alias)
    {
        $this->connection->addBcc($email, $alias);
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

    /**
     * Set content body text to send email
     * @param <string> $body
     */
    protected function setBodyText($body)
    {
        $this->connection->setBodyText($body);
    }

    /**
     * Set content body html to send email
     * @param <string> $body
     */
    protected function setBodyHtml($body)
    {
        $this->connection->setBodyHtml($body);
    }

    /**
     * Set content subject to send email
     * @param <string> $subject
     */
    protected function setSubject($subject)
    {
        $this->connection->setSubject($subject);
    }

    /**
     * Set attachment file
     * @param <string> $filename
     */
    protected function setAttachment($filename)
    {
        $fileStream = file_get_contents($filename);
        $attachment = $this->connection->createAttachment($fileStream);
        $attachment->filename = basename($filename);
    }

    /**
     * Get connection mail object
     * @return <Zend_Mail>
     */
    public function getConnection()
    {
        return $this->connection;
    }
    
    /**
     * Send email
     * @param <array> $options
     * @param <boolean> $isHtml
     * @return <boolean>
     */
    public function send($options, $isHtml=true)
    {
        //Check sender
        if(empty($options['sender']))
        {
            throw new Core_Mail_Exception('Input sender to send email.');
        }

        //Check alias_sender
        if(empty($options['alias_sender']))
        {
            //Get email
            $arr = explode('@', $options['sender']);
            $options['alias_sender'] = $arr[0];
        }

        //Set sender
        $this->setFrom($options['sender'], $options['alias_sender']);

        //List receiver
        foreach($options['reciever'] as $reciever)
        {
            //Check reciever
            if(empty($reciever['email']))
            {
                throw new Core_Mail_Exception('Input reciever to send email.');
            }

            //Check alias_reciever
            if(empty($reciever['alias']))
            {
                //Get email
                $arr = explode('@', $reciever['email']);
                $reciever['alias'] = $arr[0];
            }

            //Set reciever
            $this->setTo($reciever['email'], $reciever['alias']);
        }
        
        //List cc
        if(!empty($options['cc']))
        {
            foreach($options['cc'] as $cc)
            {
                //Check cc
                if(empty($cc['email']))
                {
                    throw new Core_Mail_Exception('Input cc to send email.');
                }

                //Check alias_cc
                if(empty($cc['alias']))
                {
                    //Get email
                    $arr = explode('@', $cc['email']);
                    $cc['alias'] = $arr[0];
                }

                //Set cc
                $this->setCc($cc['email'], $cc['alias']);
            }
        }        
        
        //List bcc
        if(!empty($options['bcc']))
        {
            foreach($options['bcc'] as $bcc)
            {
                //Check bcc
                if(empty($bcc['email']))
                {
                    throw new Core_Mail_Exception('Input bcc to send email.');
                }

                //Check alias_bcc
                if(empty($bcc['alias']))
                {
                    //Get email
                    $arr = explode('@', $bcc['email']);
                    $bcc['alias'] = $arr[0];
                }

                //Set bcc
                $this->setBcc($bcc['email'], $bcc['alias']);
            }
        }        

        //Check subject
        if(empty($options['subject']))
        {
            throw new Core_Mail_Exception('Input subject to send email.');
        }

        //Set subject
        $this->setSubject($options['subject']);

        //Check body
        if(empty($options['body']))
        {
            throw new Core_Mail_Exception('Input body to send email.');
        }

        //List attachment
        if(!empty($options['attachment']))
        {
            foreach($options['attachment'] as $fileAttachment)
            {
                //Check file attachment
                if(empty($fileAttachment))
                {
                    throw new Core_Mail_Exception('Input attachment to send email.');
                }

                //Set bcc
                $this->setAttachment($fileAttachment);
            }
        }        

        //Set body
        if($isHtml)
        {
            $this->setBodyHtml($options['body'], 'utf-8');
        }
        else
        {
            $this->setBodyText($options['body'], 'utf-8');
        }

        //Send email
        return $this->connection->send();
    }
    
     /**
     * Send email
     * @param <array> $options
     * @param <boolean> $isHtml
     * @return <boolean>
     */
    public function send2($options)
    {
        //Check sender
        if(empty($options['sender']))
        {
            throw new Core_Mail_Exception('Input sender to send email.');
        }

        //Check alias_sender
        if(empty($options['alias_sender']))
        {
            //Get email
            $arr = explode('@', $options['sender']);
            $options['alias_sender'] = $arr[0];
        }

        //Set sender
        $this->setFrom($options['sender'], $options['alias_sender']);

        //List receiver
        foreach($options['reciever'] as $reciever)
        {
            //Check reciever
            if(empty($reciever['email']))
            {
                throw new Core_Mail_Exception('Input reciever to send email.');
            }

            //Check alias_reciever
            if(empty($reciever['alias']))
            {
                //Get email
                $arr = explode('@', $reciever['email']);
                $reciever['alias'] = $arr[0];
            }

            //Set reciever
            $this->setTo($reciever['email'], $reciever['alias']);
        }
        
        //List cc
        if(!empty($options['cc']))
        {
            foreach($options['cc'] as $cc)
            {
                //Check cc
                if(empty($cc['email']))
                {
                    throw new Core_Mail_Exception('Input cc to send email.');
                }

                //Check alias_cc
                if(empty($cc['alias']))
                {
                    //Get email
                    $arr = explode('@', $cc['email']);
                    $cc['alias'] = $arr[0];
                }

                //Set cc
                $this->setCc($cc['email'], $cc['alias']);
            }
        }        
        
        //List bcc
        if(!empty($options['bcc']))
        {
            foreach($options['bcc'] as $bcc)
            {
                //Check bcc
                if(empty($bcc['email']))
                {
                    throw new Core_Mail_Exception('Input bcc to send email.');
                }

                //Check alias_bcc
                if(empty($bcc['alias']))
                {
                    //Get email
                    $arr = explode('@', $bcc['email']);
                    $bcc['alias'] = $arr[0];
                }

                //Set bcc
                $this->setBcc($bcc['email'], $bcc['alias']);
            }
        }        

        //Check subject
        if(empty($options['subject']))
        {
            throw new Core_Mail_Exception('Input subject to send email.');
        }

        //Set subject
        $this->setSubject($options['subject']);

        //Check body
        if(empty($options['body_html']) && empty($options['body_text']))
        {
            throw new Core_Mail_Exception('Input body to send email.');
        }

        //List attachment
        if(!empty($options['attachment']))
        {
            foreach($options['attachment'] as $fileAttachment)
            {
                //Check file attachment
                if(empty($fileAttachment))
                {
                    throw new Core_Mail_Exception('Input attachment to send email.');
                }

                //Set bcc
                $this->setAttachment($fileAttachment);
            }
        } 
        
        //set html mail
        if(!empty($options['body_html']))
        {
            $this->setBodyHtml($options['body_html'], 'utf-8');
            
        }
        
         //set html mail
        if(!empty($options['body_text']))
        {
            $this->setBodyText($options['body_text'], 'utf-8');
            
        }


        //Send email
        return $this->connection->send();
    }
}

