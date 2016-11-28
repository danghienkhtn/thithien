<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Cache_Manager
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to caching
 */
class Core_Cache_Manager
{
    /**
     * Register cache clean
     * config_id : id index server caching in config.ini
     * @param Core_JobClient $jobClient
     * @param <array> $options
     * @return <bool>
     */
    private static function addClearLocalCache(Core_JobClient $jobClient, $options)
    {
        //Check options
        if(empty($options))
        {
            return false;
        }

        //Add workload
        $workload = array(
            'class'         =>  $options['class'],
            'function'      =>  $options['function'],            
            'args'      =>  array(
                'key'       =>  $options['key'],
                'expire'    =>  $options['expire']
            )
        );

        //Register signal map function
        $jobClient->doHightBackgroundTask($this->register_function, $workload, Core_Utility::genGuidKey());

        //Return data
        return true;
    }

    /**
     * Send signal to job server
     * @param Core_JobClient $jobClient
     * @param <array> $options
     * @param <string> $key
     * @param <int> $expire
     * @return <bool>
     */
    public static function signalClearLocalCache(Core_JobClient $jobClient, $options, $key, $expire)
    {
        //Check options
        if(empty($options))
        {
            return false;
        }

        //Check function options
        if(empty($options['function']))
        {
            return false;
        }

        //Get array function map
        $arrMapFunctions = explode(',', $options['function']);

        //Loop and register
        foreach($arrMapFunctions as $function)
        {
            $signalOptions = array(
                'class'     =>  $options['class'],
                'function'  =>  $options['function'],
                'key'       =>  $key,
                'expire'    =>  $expire
            );

            //Add map function to job server
            self::addClearLocalCache($jobClient, $signalOptions);
        }

        //Return data
        return true;
    }
}

