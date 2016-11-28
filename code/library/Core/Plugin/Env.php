<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Plugin_Env
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Envirement plugin
 */
class Core_Plugin_Env extends Zend_Controller_Plugin_Abstract
{
    //Module
    private $module = 'default';
    
    //Controller
    private $controller = 'index';
    
    //Check is mobile
    private $isMobile = false;

          
    /**
     * Register params
     * @param <string> $request 
     */
    private function registerParams(&$request)
    {
        //Get URL Request
        $urlRequest = $_SERVER['REQUEST_URI'];
                
        //Get browser information
        $browserInstance = Core_Browser::getInstance();

        //Check mobile header
        $this->isMobile = $browserInstance->isMobile();

        //Set register
        if($this->isMobile)
        {
            //Registry data
            Zend_Registry::set(GLOBAL_MOBILE_FLAG, $this->isMobile);
        }
        
        
        //Check params
        if(!empty($urlRequest) || ($urlRequest != '/'))
        {
            $arrRequest = explode('?', $urlRequest);

            //Loop to set param
            if(isset($arrRequest[1]))
            {
                //Explode param
                $arrRequest = explode('&', $arrRequest[1]);

                //Loop to set param
                foreach($arrRequest as $reqData)
                {
                    //Explode data
                    $arrRequestValue = explode('=', $reqData);
                    
                    //Check params
                    if(isset($arrRequestValue[0]) && isset($arrRequestValue[1]))
                    {
                        $request->setParam($arrRequestValue[0], $arrRequestValue[1]);
                    }                
                }
            }
        }
    }
    
    /**
     * Called before Zend_Controller_Front calls on the router 
     * to evaluate the request against the registered routes.	 
     * @param Zend_Controller_Request_Abstract $request
     */
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        //Register params
        $this->registerParams($request);
    }
 
    /**
     * Called after the router finishes routing the request.
     * @param Zend_Controller_Request_Abstract $request
     */
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        //Get module
        $module = $request->getParam('module');

        //If empty then set default        
        if(empty($module))
        {
            $module = 'default';
        }
        
        //Tolower module
        $module = strtolower($module);

        //Setup Include Paths
        set_include_path(implode(PATH_SEPARATOR,array(
            APPLICATION_PATH.'/models',            
            APPLICATION_PATH.'/modules/'.$module.'/models',
            get_include_path()
        )));

        //Check module
        if($module != 'default')
        {
            //Set boostrap name
            $moduleBootstrapName = ucfirst($module) . 'Bootstrap';

            /*Init Boostrap*/
            if(method_exists($moduleBootstrapName, 'init'))
            {
                call_user_func(array($moduleBootstrapName, 'init'));
            }

        }
    }
 
    /**
     * Called before Zend_Controller_Front enters its dispatch loop.
     * @param Zend_Controller_Request_Abstract $request
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        //Load config layout
        global $globalArrLayout;
        
        //Get bAjax controll
        $bAjax = $request->getParam('bAjax', 0);
               
        //Get module
        $module = $request->getParam('module');

        //Get application configuration
        $defaultConfiguration = Core_Global::getApplicationIni();
                
        //Get controller
        $controller = $request->getParam('controller');
        
        //Tolower controller
        $controller = strtolower($controller);
        
        //Get action
        $action = $request->getParam('action');
        
        //Tolower action
        $action = strtolower($action);

        //Set layout
        $layout = 'layout';

        //Check configuration layout
        if(isset($globalArrLayout[$module][$controller . '-' . $action]))
        {
            $layout = $globalArrLayout[$module][$controller . '-' . $action];
        }

        //Layout setup
        $layoutInstance = Zend_Layout::startMvc(
            array(
                'layout'     => $layout,
                'layoutPath' => APPLICATION_PATH.'/layouts/'.$module,
                'contentKey' => 'content'
            )
        );
        
        //set array
        $arrLanguage = array(LANGUAGE_EN, LANGUAGE_JP, LANGUAGE_VI);

        $sLanguage = isset($_SESSION['language']) 
                && in_array($_SESSION['language'], $arrLanguage) ? 
                $_SESSION['language'] : DEFAULT_LANGUAGE;
        
        $_SESSION['language'] = $sLanguage;
        
        //Set register
        Zend_Registry::set(LANG_CONFIG, $sLanguage);

        //Get locals
        $arrlocales = new Zend_Config_Ini(DATA_PATH . '/locales/' . $module . '/' . $sLanguage . '.ini');

        //Set register
        Zend_Registry::set(LOCALE_CONFIG, $arrlocales);

        //Get current view
        $viewInstance = $layoutInstance->getView();

        //Set view title separator
        $viewInstance->headTitle()->setSeparator(' - ');

        //Add view phtml
        $viewInstance->addBasePath(APPLICATION_PATH .'/modules/'.$module.'/views');

        //Set helpers path
        $viewInstance->addHelperPath(APPLICATION_PATH .'/modules/'.$module.'/views/helpers');
        
        //Set partials path
        $viewInstance->addScriptPath(APPLICATION_PATH .'/partials');
        
        //Set partials path
        $viewInstance->addScriptPath(APPLICATION_PATH .'/modules/'.$module.'/views/partials');
        
        //Set controller path
        $viewInstance->addScriptPath(APPLICATION_PATH .'/modules/'.$module.'/views/scripts/'.$controller);
            
        //Set view params
        $arrViewParams = array(
            'locales'           =>  $arrlocales,
            'static'            =>  $defaultConfiguration->app->static,
            'env'               =>  APP_ENV,            
            'actionName'        =>  $action,
            'controllerName'    =>  $controller,
            'currLangSetting'   =>  $sLanguage . '/' . STATIC_LOCALE_VERSION
        );

        //Set language
        Zend_Registry::set(VIEWS_CONFIG, $arrViewParams);
        
        //Add views params
        Core_Global::addToView($viewInstance, $arrViewParams);
        
        //Cleanup data
        unset($layoutInstance, $viewInstance);
    }
 
    /**
     * Called before an action is dispatched by the dispatcher.
     * This callback allows for proxy or filter behavior.
     * By altering the request and resetting its dispatched flag
     * via Zend_Controller_Request_Abstract::setDispatched(false),
     * the current action may be skipped and/or replaced.
     * @param Zend_Controller_Request_Abstract $request
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        //Parent preDispatch
        parent::preDispatch($request);

        //Check ajax post
        if($request->isXmlHttpRequest())
        {
            $viewHelper = Zend_Controller_Action_HelperBroker::getStaticHelper("ViewRenderer");
            $viewHelper->setNoRender(true);
            Zend_Layout::getMvcInstance()->disableLayout();
        }
    }
    
    /**
     * Called after an action is dispatched by the dispatcher.
     * This callback allows for proxy or filter behavior.
     * By altering the request and resetting its dispatched flag
     * via Zend_Controller_Request_Abstract::setDispatched(false)),
     * a new action may be specified for dispatching.
     * @param Zend_Controller_Request_Abstract $request
     */
    public function postDispatch(Zend_Controller_Request_Abstract $request){}
 
    /**
     * Called after Zend_Controller_Front exits its dispatch loop.
     */
    public function dispatchLoopShutdown() {}
}

