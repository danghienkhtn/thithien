<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Job_Worker_Adapter_Gearman
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to job gearman
 */
class Core_Job_Worker_Adapter_Gearman extends Core_Job_Worker_Adapter_Abstract
{
   /**
     * GearmanWorker instance
     *
     * @var GearmanWorker
     */
    private $worker = null;
    
    /**
     * Set debug variable
     * @var <boolean> 
     */
    private $debug = false;
    
    /**
     * Number action 
     */
    private $iActionNumber = 0;
    
    /**
     * Set timeout
     */
    private $isJobTimeout = 5;

    /**
    * Constructor
    *
    */
    public function __construct($options = array())
    {
        //Set connect options
        $this->setConnectOption($options);
        
        //Reset number action
        $this->iActionNumber = 0;
    }

    /**
    * Destructor
    *
    */
    public function __destruct()
    {
        //Nothing
    }
    
    /**
     * Connection to gearman
     */
    private function connect()
    {
        //Check worker instance
        if(is_null($this->worker))
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

            //Check debug
            if(!empty($options['debug']))
            {
                $this->debug = $options['debug'];
            }

            //Return object class
            $this->worker = new GearmanWorker();
            
            //Check non-blocking
            if(empty($options['nonblocking']))
            {
                $options['nonblocking'] = false;
            }
            
            //Set non-blocking control
            if($options['nonblocking'])
            {
                $this->worker->addOptions(GEARMAN_WORKER_NON_BLOCKING);
            }

            //Get list server and port
            $arrServerHost = explode(",", $options['host']);
            $arrServerPort = explode(",", $options['port']);

            //Add host and port
            foreach($arrServerHost as $iLoop => $sServerHost)
            {
                $this->worker->addServer($sServerHost, $arrServerPort[$iLoop]);
            }
            
            //Check timeout
            if(empty($options['timeout']))
            {
                $options['timeout'] = 5000;
            }

            //Set timeout        
            if($options['timeout'] > 0)
            {
                $this->worker->setTimeout($options['timeout']);
            }
            
            //Check timeout
            if(empty($options['job_timeout']))
            {
                $options['job_timeout'] = 5;
            }
            
            //Set timeout
            $this->isJobTimeout = $options['job_timeout'];
        }
    }

    /**
     * Add function to worker
     * @param string $register_function
     * @param string $callback_function
     * @param var $args
     */
    public function addFunction($register_function, $callback_function, $args=null)
    {
        //Connection to gearman
        $this->connect();
                
        //Add function
        $this->worker->addFunction($register_function, $callback_function, $args);
    }

    /**
     * Run worker
     */
    public function run()
    {
        //Connection to gearman
        $this->connect();
        
		//Start worker
        echo " == Waiting for job...\n";
        
        //Try to loop worker
        try
        {
            //Loop to detect jobs
            while((@$this->worker->work()) || ($this->worker->returnCode() == GEARMAN_TIMEOUT))
            {          
                try
                {
                    //Check worker
                    $this->connect();

                    //Work timeout                
                    if($this->worker->returnCode() == GEARMAN_TIMEOUT)
                    {
                        continue;
                    }

                    //Check status not success                    
                    if($this->worker->returnCode() != GEARMAN_SUCCESS)
                    {
                        //Check debug
                        if($this->debug)
                        {
                            echo "=== Failure with error code=" . $this->worker->returnCode() . ":" . $this->worker->getErrno() . ":" . $this->worker->error() ."\n";
                        }
                        
                        //Broken loop
                        break;
                    }

                    //Wait sometime
                    if(!@$this->worker->wait())
                    {
                        if($this->worker->returnCode() == GEARMAN_NO_ACTIVE_FDS)
                        {
                            usleep(500);
                        }
                    }                    
                }
                catch(Exception $exLoop)
                {
                    echo " == Error : " . $exLoop->getMessage() . " with code=" . $this->worker->getErrno() . "\n";
                }
            }
        }
        catch(Exception $ex)
        {
            echo " == Error : " . $ex->getMessage() . "\n";
        }
    }

    /**
     * Get Notify Data in worker
     * @param GearmanJob $job
     */
    public function getNotifyData($job, $isType='php')
    {
        //Increment action number
        $this->iActionNumber++;
        
        //Get worker job
        $workload = $job->workload();
        $workload = trim($workload);
        
        //Get data
        if($isType == 'php')
        {
            $arrParams = unserialize($workload);
        }
        else
        {
            $arrParams = json_decode($workload, true);
        }
        
        //Debug information
        if($this->debug)
        {
            echo "=== Received job: " . $job->handle() . " with Number=" . $this->iActionNumber . "\n";
            echo "=== Workload: $workload" . " with Number=" . $this->iActionNumber . "\n";
            print_r($arrParams);
            echo "\n";
        }
        
        //Return data
        return $arrParams;
    }
}

