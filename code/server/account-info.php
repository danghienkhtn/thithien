<?php
/**
 * @author      :   Linuxpham
 * @name        :   server.php
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Run job server php
 */
//Define data
define('DOCUMENT_ROOT', realpath(dirname(__FILE__)));
define('APPLICATION_PATH', realpath(DOCUMENT_ROOT.'/../../application'));
define('LIBS_PATH', realpath(DOCUMENT_ROOT . '/../../library'));
define('DATA_PATH', realpath(DOCUMENT_ROOT.'/../../data'));

//Setup Include Paths
set_include_path(implode(PATH_SEPARATOR,array(
    LIBS_PATH,
    APPLICATION_PATH.'/modules/default/models',
    APPLICATION_PATH.'/models',
    DOCUMENT_ROOT.'/models',
    get_include_path()
)));

//Load defined configuration
require_once APPLICATION_PATH . '/configs/environment.php';
require_once APPLICATION_PATH . '/configs/defined-'.APP_ENV.'.php';

//Load Autoloader
require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance()->setFallbackAutoloader(true);

require_once APPLICATION_PATH . '/configs/global.php';

//statistic active user
Core_Common::addJob(array('function'=>'account-info'), 'JobAccount', 'addNewUserFromLdap', 'account-info');
Core_Common::addJob(array('function'=>'account-info'), 'JobAccount', 'updateActive', 'account-info');

echo date('d-m-Y H:i:s').'  ';
