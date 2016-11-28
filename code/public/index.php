<?php
/**
 * @author      :   Linuxpham
 * @name        :   index.php
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Run main php
 */

define('DOCUMENT_ROOT', realpath(dirname(__FILE__)));
define('APPLICATION_PATH', realpath(DOCUMENT_ROOT.'/../application'));
define('LIBS_PATH', realpath(DOCUMENT_ROOT . '/../library'));
define('DATA_PATH', realpath(DOCUMENT_ROOT.'/../data'));
// define('EXCHANGE_PATH', realpath(DOCUMENT_ROOT . '/../exchange'));

//Setup Include Paths
set_include_path(implode(PATH_SEPARATOR,array(    
    LIBS_PATH,
    // EXCHANGE_PATH,
    get_include_path()
)));

//Load defined configuration
require_once APPLICATION_PATH . '/configs/environment.php';
require_once APPLICATION_PATH . '/configs/defined-'.APP_ENV.'.php';
require_once APPLICATION_PATH . '/configs/global.php';
require_once APPLICATION_PATH . '/configs/layout.php';
require_once APPLICATION_PATH . '/configs/admin.php';
require_once APPLICATION_PATH . '/../vendor/autoload.php';

//Load bootstrap
try
{	    
    //Require library
    require_once 'Zend/Application.php';
	
	//Load Application    
    $application = new Zend_Application(
        APP_ENV,
        array(
            'bootstrap' => array(
                'path'   => APPLICATION_PATH.'/Bootstrap.php',
                'class'  => 'Bootstrap'
            ),
            'phpSettings' => array(
                'display_startup_errors' => (APP_ENV == 'development')?1:0,
                'display_errors'         => (APP_ENV == 'development')?1:0
            )
        )
    );    
    
    //Init and run boostrap
    $application->bootstrap()->run();   
    
    //Garbage collection
    gc_collect_cycles();
} 
catch(Zend_Exception $exception) 
{
    //Check DEV enviroment
   // if(APP_ENV == 'development')
   // {
        echo '<html><body><center>'
            .'An exception occured while bootstrapping the application.';    
        echo '<br /><br />'.$exception->getMessage().'<br />'
            .'<div align="left">Stack Trace:'
            .'<pre>'.$exception->getTraceAsString().'</pre></div>';
        echo '</center></body></html>';
   /* }
    else
    {
        $htmlContent = file_get_contents('./50x.html');
        echo $htmlContent;        
    }*/
        
    exit(1);
}

