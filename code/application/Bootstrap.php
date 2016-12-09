<?php
/**
 * @author      :   Dang Hien
 * @name        :   Bootstrap
 * @version     :   201611
 * @copyright   :   My company
 * @todo        :   Main Bootstrap
 * http://framework.zend.com/manual/en/zend.controller.actionhelpers.html
 */
require_once '../framework/Zend/Loader/Autoloader.php';

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	/**
	 * Application configuration	 
	 */
	private static $configuration = null;
    
    /**
     * Frontend controller 
     */
    private static $currFrontEnd = null;
	    
	/**
	 * Registration of my name space
	 */
    protected function initNamespace()
    {    
        //Get autoload config
        $autoloadInstance = Zend_Loader_Autoloader::getInstance();
        
        //Register namespace
        $autoloadInstance->registerNamespace('Core_');
        //$autoloadInstance->registerNamespace(array('Core_', 'Shanty_'));
        //Set fallback
        $autoloadInstance->setFallbackAutoloader(true)
                         ->suppressNotFoundWarnings(false);
    }	
    
    /**
     * Init application configuration
     */
    protected function initAppConfiguration()
    { 
        //Get file configuration
    	self::$configuration = new Zend_Config_Ini(APPLICATION_PATH.'/configs/application-'.APP_ENV.'.ini');
        
        //Get registry
		Zend_Registry::set(APP_CONFIG, self::$configuration);        
    }
    
    /**
     * Session setup
     */
    protected function initSession()
    {
        //Set session options
        Zend_Session::setOptions(array(
            'cookie_domain' => DOMAIN,
            'name'          => DOMAIN_COOKIE_NAME
        ));
        
        //Set notifyID control session
        $session = new Zend_Session_Namespace('default');
        //Zend_Session::start();
        $session->notifyid = Core_Utility::genGuidKey();
    }
    
    /**
     * Routers main setup    
     */
    protected function initMainRouters()
    {
        //Get routers
        $routers = self::$currFrontEnd->getRouter();
        
        //Remove default router
        $routers->removeDefaultRoutes();
        
    	//Default module
        $default_router = new Zend_Controller_Router_Route(':controller/:action/*',array('controller' => 'index', 'action' => 'index', 'module' => 'default'));
     
        
        //Add dynamic router
        $routers->addRoute('default', $default_router);

        //Set new router
        self::$currFrontEnd->setRouter($routers);
    }
    
    
    
    
    protected function initBackendRouters()
    {
        $routers = self::$currFrontEnd->getRouter();
    	
    	//Define Vhost Master chain
    	$masterChainRouter = new Zend_Controller_Router_Route_Hostname(BASE_ADMIN, array('module' => 'backend'));
    	
    	//Vhost master
    	$master_router = new Zend_Controller_Router_Route('backend/:controller/:action/*', array('controller' => 'index', 'action' => 'index', 'module' => 'backend'));
    	
    	//Add dynamic router
    	$routers->addRoute('backend', $masterChainRouter->chain($master_router));

    	//Set new router
    	self::$currFrontEnd->setRouter($routers);

    }
    
    protected function initApiRouters()
    {
         
        //Get routers
        $routers = self::$currFrontEnd->getRouter();
        
        //Forum module
        $apiRoute = new Zend_Controller_Router_Route('api/:controller/:action/*',array('controller' => 'index', 'action' => 'index', 'module' => 'api'));

        //Add dynamic router        
        $routers->addRoute('api', $apiRoute);
    
        
        //Set new router
        self::$currFrontEnd->setRouter($routers);
    }
        
    /**
     * Zend_Front_Controller created on each request
     */
    protected function initFrontController()
    {
        // Ensure front controller instance is present, and fetch it
        $this->bootstrap('FrontController');
        
    	//Init frontController
        self::$currFrontEnd = $this->getResource('FrontController');
        
        //Check frontController
        if(empty(self::$currFrontEnd))
        {            
            self::$currFrontEnd = Zend_Controller_Front::getInstance();
        }
    	
    	//Set the current environment
    	self::$currFrontEnd->setParam('env', APP_ENV);
    	    	
        //Enable error controller plugin
        self::$currFrontEnd->throwExceptions(false);    
        
        //Set module
        self::$currFrontEnd->setControllerDirectory(array(
            'default' => APPLICATION_PATH . '/modules/default/controllers',
            'backend' => APPLICATION_PATH . '/modules/backend/controllers',
            'api' => APPLICATION_PATH . '/modules/api/controllers'
        ));
        
        //Global Default Controller
        self::$currFrontEnd->setParam('useDefaultControllerAlways', true);
        
          //Add main router
        $this->initMainRouters();
        
        //Add backend router
        $this->initBackendRouters();
        
        //Add api router
        $this->initApiRouters();
    
        #Set controller plug
        self::$currFrontEnd->registerPlugin(
            new Core_Plugin_Env(),
            'CORE_PLUGIN_ENV'
        );
                        
        //Set bootstrap params
        if(null === self::$currFrontEnd->getParam('bootstrap'))            
        {
            // var_dump($this);
            self::$currFrontEnd->setParam('bootstrap', $this);
        }                    
        
        //Dispatch HTML
        self::$currFrontEnd->dispatch();        
        // exit("s7");        
    }
                    
    /**
     * Run the application
     *
     * Checks to see that we have a default controller directory. If not, an
     * exception is thrown.
     *
     * If so, it registers the bootstrap with the 'bootstrap' parameter of
     * the front controller, and dispatches the front controller.
     *
     * @return void
     * @throws Zend_Exception
     */    
    public function run()
    {                 
    	//Init namspace
    	$this->initNamespace();

        //init session
        $this->initSession();

    	//Init Application Configuration
    	$this->initAppConfiguration();

    	//Init Front Controller
    	$this->initFrontController();
        
    }
}

