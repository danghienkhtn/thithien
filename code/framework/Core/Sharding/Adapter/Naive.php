<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Sharding_Adapter_Naive
 * @version     :   201008
 * @copyright   :   My company
 * @todo        :   Using to shading storage
 */
class Core_Sharding_Adapter_Naive extends Core_Sharding_Adapter_Abstract
{
    /**
    * Constructor
    * @param array $options
    */
    public function __construct(array $options=array())
    {
        //Get options child
        $options = $this->getOptions($options);
        
        //Check backend list
        if(empty($options))
        {
            throw new Core_Sharding_Exception('Input list backends.');
        }

        //Set backend list
        $this->setBackends($options);

        //Set default replicas
        $this->setReplicas(256);

        //Set default max cache
        $this->setCacheMax(256);

        //Check backend number
        if($this->backends_count > 1)
        {
            $this->setHashring();
        }
    }

    /**
    * Destructor
    */
    public function __destruct() {}

    /**
     * Set hashring information
     */
    protected function setHashring()
    {
        // Initialize the hashring and the count.
        $this->hashring = array();
        $this->hashring_count = 0;

        // Iterate over the backends.
        foreach($this->backends as $idex => $backend)
        {
            //Get weight
            $weight = isset($backend['weight'])?$backend['weight']:1;

            // Add to the hashring count.
            $this->hashring_count += $weight;

            //Create as many replicas as $weight.
            for($i = 0; $i < $weight; $i++)
            {
                $this->hashring[] = $idex;
            }
        }
    }

    /**
     * Get map of key
     * @param <string> $key     
     * @return <int>
     */
    protected function getMap($key)
    {
        //If we have only one backend, return it.
        if($this->backends_count === 1)
        {
            return 0;
        }
        
        //Very basic CRC32 + modulus.
        $key = sprintf("%u\n", crc32($key));
        $position = ((abs($key) >> 16) & 0x7fff) % $this->hashring_count;
        
        //Return map detail
        return $this->hashring[$position];
    }    
}

