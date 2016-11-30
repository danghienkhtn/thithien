<?php
/**
 * @author      :   Hoaitn
 * @name        :   ApiController
 * @version     :   20110214
 * @copyright   :   My company
 * @todo        :   controller API 
 */
class Api_TokenController extends Zend_Controller_Action
{
    
    /**
     * init of controller
     */
    public function init()
    {
        //Disale layout
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
    }
    
    public function indexAction()
    {

    	//Set option for server
    	$options = array(
    			'adapter' => 'rest'
    	);
    
    	//Get server instance
    	$serverInstance = Core_Server::getInstance($options);
    
    	//Register class call
    	$serverInstance->setClass('ApiToken');
    
    	//Hanlde instance
    	$serverInstance->handle($this->_request);
    }
    public function generateAction()
    {
        $sToken = Core_Guuid::generateNoSpace(Core_Guuid::UUID_TIME, Core_Guuid::FMT_STRING, "InternalProject", Core_Utility::getAltIp());
        $iExpired = 0;
        Core_Cookie::setCookie(TOKEN_API, $sToken, $iExpired, '/', DOMAIN, false, true);

        echo $sToken;
        exit();
    }
    
   
    
}

