<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Job_Client_Adapter_Gearman
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to job gearman
 */
class Core_Job_Client_Adapter_Gearman extends Core_Job_Client_Adapter_Abstract
{
    /**
    * GearmanClient instance
    *
    * @var GearmanClient
    */
    private $client = null;

    /**
    * Constructor
    *
    */
    public function __construct($options = array())
    {
        //Set connect options
        $this->setConnectOption($options);
    }

    /**
    * Destructor
    *
    */
    public function __destruct()
    {
        //Cleanup
        unset($this->client);
    }
    
    /**
     * Connection to gearman
     */
    private function connect()
    {
        //Check client instance
        if(is_null($this->client))
        {
            //Get options
            $options = $this->getConnectOption();

            //Get options child
            $options = $this->getOptions($options);

            //Check host
            if(empty($options['host']))
            {
                throw new Core_Job_Exception('Please input Host for Gearman.');
            }

            //Check Port
            if(empty($options['port']))
            {
                throw new Core_Job_Exception('Please input Port for Gearman.');
            }

            //Return object class
            $this->client = new GearmanClient();
            
            //Get list server and port
            $arrServerHost = explode(",", $options['host']);
            $arrServerPort = explode(",", $options['port']);

            //Add host and port
            foreach($arrServerHost as $iLoop => $sServerHost)
            {
                $this->client->addServer($sServerHost, $arrServerPort[$iLoop]);
            }      
            
            //Check timeout
            if(empty($options['timeout']))
            {
                $options['timeout'] = 5000;
            }

            //Set timeout        
            if($options['timeout'] > 0)
            {
                $this->client->setTimeout($options['timeout']);
            }
        }
    }

    /**
    * Run background register task to server job
    * @param string $register_function
    * @param array $array_data
    * @param int $unique
    */
    public function doBackgroundTask($register_function, $array_data, $unique = null)
    {        
        //Check uniqueID
        if(empty($unique))
        {            
            $unique = Core_Guuid::generateNoSpace(Core_Guuid::UUID_TIME, Core_Guuid::FMT_STRING, $register_function, Core_Utility::getAltIp());
        }
        
        //Connection to gearman
        $this->connect();
        
        //Run job background to server
        $job_handle = $this->client->doBackground($register_function, serialize($array_data), $unique);

        //If error
        if ($this->client->returnCode() != GEARMAN_SUCCESS)
        {
            throw new Core_Job_Exception("Add Job unsuccess",$this->client->returnCode());
        }

        //Return value
        return array('jobhandle'=>$job_handle);
    }
    
    /**
    * Run background register task to server job
    * @param string $register_function
    * @param array $array_data
    * @param int $unique
    */
    public function doBackgroundTaskInJson($register_function, $array_data, $unique = null)
    {        
        //Check uniqueID
        if(empty($unique))
        {            
            $unique = Core_Guuid::generateNoSpace(Core_Guuid::UUID_TIME, Core_Guuid::FMT_STRING, $register_function, Core_Utility::getAltIp());
        }
        
        //Connection to gearman
        $this->connect();
        
        //Run job background to server
        $job_handle = $this->client->doBackground($register_function, json_encode($array_data), $unique);

        //If error
        if ($this->client->returnCode() != GEARMAN_SUCCESS)
        {
            throw new Core_Job_Exception("Add Job unsuccess",$this->client->returnCode());
        }

        //Return value
        return array('jobhandle'=>$job_handle);
    }

    /**
    * Run background register task to server job
    * @param string $register_function
    * @param array $array_data
    * @param int $unique
    */
    public function doHighBackgroundTask($register_function, $array_data, $unique = null)
    {        
        //Check uniqueID
        if(empty($unique))
        {
            $unique = Core_Guuid::generateNoSpace(Core_Guuid::UUID_TIME, Core_Guuid::FMT_STRING, $register_function, Core_Utility::getAltIp());
        }
        
        //Connection to gearman
        $this->connect();
        
        //Run job background to server
        $job_handle = $this->client->doHighBackground($register_function, serialize($array_data), $unique);

        //If error
        if ($this->client->returnCode() != GEARMAN_SUCCESS)
        {
            throw new Core_Job_Exception("Add Job unsuccess",$this->client->returnCode());
        }

        //Return value
        return array('jobhandle'=>$job_handle);
    }

    /**
    * Run background register task to server job
    * @param string $register_function
    * @param array $array_data
    * @param int $unique
    */
    public function doLowBackgroundTask($register_function, $array_data, $unique = null)
    {        
        //Check uniqueID
        if(empty($unique))
        {
            $unique = Core_Guuid::generateNoSpace(Core_Guuid::UUID_TIME, Core_Guuid::FMT_STRING, $register_function, Core_Utility::getAltIp());
        }
        
        //Connection to gearman
        $this->connect();
        
        //Run job background to server
        $job_handle = $this->client->doLowBackground($register_function, serialize($array_data), $unique);

        //If error
        if ($this->client->returnCode() != GEARMAN_SUCCESS)
        {
            throw new Core_Job_Exception("Add Job unsuccess",$this->client->returnCode());
        }

        //Return value
        return array('jobhandle'=>$job_handle);
    }

    /**
    * Run foreground register task to server job
    * @param string $register_function
    * @param array $array_data
    * @param int $unique
    */
    public function doTask($register_function, $array_data, $unique = null)
    {        
        //Check uniqueID
        if(empty($unique))
        {
            $unique = Core_Guuid::generateNoSpace(Core_Guuid::UUID_TIME, Core_Guuid::FMT_STRING, $register_function, Core_Utility::getAltIp());
        }
        
        //Connection to gearman
        $this->connect();
        
        //Do task forceground
        do
        {
            //Run job background to server
            $job_handle = $this->client->do($register_function, serialize($array_data), $unique);

            //Check error
            switch($this->client->returnCode())
            {
                case GEARMAN_WORK_DATA:
                    break;
                case GEARMAN_SUCCESS:
                    break;
                case GEARMAN_WORK_FAIL:
                    return array('status'=>false);
                    break;
                case GEARMAN_WORK_STATUS:
                    return array('status'=>$this->client->doStatus());
                    break;
                default:
                    throw new Core_Job_Exception("Add Job unsuccess",$this->client->error());
            }
        }
        while($this->client->returnCode() != GEARMAN_SUCCESS);

        //Return value
        return array('jobhandle'=>$job_handle);
    }

    /**
    * Run foreground register task to server job
    * @param string $register_function
    * @param array $array_data
    * @param int $unique
    */
    public function doHighTask($register_function, $array_data, $unique = null)
    {        
        //Check uniqueID
        if(empty($unique))
        {
            $unique = Core_Guuid::generateNoSpace(Core_Guuid::UUID_TIME, Core_Guuid::FMT_STRING, $register_function, Core_Utility::getAltIp());
        }
        
        //Connection to gearman
        $this->connect();
        
        //Do task forceground
        do
        {
            //Run job background to server
            $job_handle = $this->client->doHigh($register_function, serialize($array_data), $unique);

            //Check error
            switch($this->client->returnCode())
            {
                case GEARMAN_WORK_DATA:
                    break;
                case GEARMAN_SUCCESS:
                    break;
                case GEARMAN_WORK_FAIL:
                    return array('status'=>false);
                    break;
                case GEARMAN_WORK_STATUS:
                    return array('status'=>$this->client->doStatus());
                    break;
                default:
                    throw new Core_Job_Exception("Add Job unsuccess",$this->client->error());
            }
        }
        while($this->client->returnCode() != GEARMAN_SUCCESS);

        //Return value
        return array('jobhandle'=>$job_handle);
    }

    /**
    * Run foreground register task to server job
    * @param string $register_function
    * @param array $array_data
    * @param int $unique
    */
    public function doLowTask($register_function, $array_data, $unique = null)
    {        
        //Check uniqueID
        if(empty($unique))
        {
            $unique = Core_Guuid::generateNoSpace(Core_Guuid::UUID_TIME, Core_Guuid::FMT_STRING, $register_function, Core_Utility::getAltIp());
        }
        
        //Connection to gearman
        $this->connect();
        
        //Do task forceground
        do
        {
            //Run job background to server
            $job_handle = $this->client->doLow($register_function, serialize($array_data), $unique);

            //Check error
            switch($this->client->returnCode())
            {
                case GEARMAN_WORK_DATA:
                    break;
                case GEARMAN_SUCCESS:
                    break;
                case GEARMAN_WORK_FAIL:
                    return array('status'=>false);
                    break;
                case GEARMAN_WORK_STATUS:
                    return array('status'=>$this->client->doStatus());
                    break;
                default:
                    throw new Core_Job_Exception("Add Job unsuccess",$this->client->error());
            }
        }
        while($this->client->returnCode() != GEARMAN_SUCCESS);

        //Return value
        return array('jobhandle'=>$job_handle);
    }
}

