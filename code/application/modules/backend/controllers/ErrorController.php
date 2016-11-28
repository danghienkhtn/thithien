<?php
/**
 * @name        :   ErrorController
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   controller when error
 */
class Backend_ErrorController extends Core_Controller_Action
{
    public function init()
    {
        
    }
	/**
	 * Error handle action
	 */
    public function errorAction()
    {
    	//Ensure the default view suffix is used so we always return good         
        $this->_helper->viewRenderer->setViewSuffix('phtml');
		$this->_helper->layout->disableLayout();
		
		 //Grab the error object from the request
        $errors = $this->_getParam('error_handler'); 

        //$errors will be an object set as a parameter of the request object     
        switch ($errors->type) 
        { 
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER: 
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION: 
                //404 error
                $this->getResponse()->setHttpResponseCode(404); 
                $this->view->message = 'Page not found'; 
                break; 
            default: 
                //Application error
                $this->view->message = $errors->exception->getMessage(); 
                break; 
        } 

        // pass the environment to the view script so we can conditionally         
        $this->view->env = APP_ENV;//$this->getInvokeArg(ENVIRONMENT);
        
        // pass the actual exception object to the view
        $this->view->exception = $errors->exception; 
        
        // pass the request to the view
        $this->view->request   = $errors->request;
    }
}