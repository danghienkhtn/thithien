<?php

/**
 * @author      :   Linuxpham
 * @name        :   Core_Loader
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to loader
 */
if(!isset($GLOBALS['THRIFT_ROOT']))
{
    $GLOBALS['THRIFT_ROOT'] = realpath(realpath(dirname(__FILE__)) . '/../Apache/Thrift');
    include $GLOBALS['THRIFT_ROOT'] . '/Thrift.php';
    include $GLOBALS['THRIFT_ROOT'] . '/transport/TSocket.php';
    include $GLOBALS['THRIFT_ROOT'] . '/transport/TSocketPool.php';
    include $GLOBALS['THRIFT_ROOT'] . '/transport/TFramedTransport.php';
    include $GLOBALS['THRIFT_ROOT'] . '/transport/TBufferedTransport.php';
    include $GLOBALS['THRIFT_ROOT'] . '/protocol/TBinaryProtocol.php';
    include $GLOBALS['THRIFT_ROOT'] . '/transport/TMemoryBuffer.php';
    include $GLOBALS['THRIFT_ROOT'] . '/packages/Common/gntcommon_types.php';
}

class Core_Loader
{

    /**
     * List instance
     *
     * @var array
     */
    private static $instances = array();

    /**
     * List loader
     * @var <string> 
     */
    private static $arrLoaded = array();

    /**
     * Autoload library
     * @param <string> $path
     * @return <string> 
     */
    public static function autoload($path)
    {
        //Include path data
        include str_replace('_', '/', $path) . '.php';

        //Return path
        return $path;
    }

    /**
     * Registers as an SPL class loader.
     * Inserts self first, retains existing loaders and __autoload()
     *
     * @param string $path Path to classes directory
     */
    public static function register($path)
    {
        //Check loaded path
        if(!isset(self::$arrLoaded[$path]))
        {
            //Get all file in folder
            $arrFiles = glob("$path/*.php", GLOB_NOSORT);

            //Loop to include file
            foreach($arrFiles as $filename)
            {
                //Get base name
                $basename = Core_Utility::baseName($filename);

                //Check instance register
                if(!isset(self::$instances[$basename]))
                {
                    //Put to handle
                    self::$instances[$basename] = $filename;

                    //Include this file
                    include_once $filename;
                }
            }
            
            //Remember loaded
            self::$arrLoaded[$path] = 1;
        }
    }

}

