<?php
/**
 * @name        :   UserController
 * @version     :   20161215
 * @copyright   :   Dahi
 * @todo        :   controller default 
 */

class TaiKhoanController extends Core_Controller_Action
{
     //var arr login
     // private $arrLogin;l
     
     public function init() {
        parent::init();
        //Asign login
        // $this->arrLogin = $this->view->arrLogin;
        
        //Get Controller
        $controller = $this->_request->getParam('controller');
        $this->view->controller = $controller;

        // $this->_helper->layout()->disableLayout();
       
    }

    public function comingSoonAction()
    {

    }
    /**
     * Default action
     */
    public function indexAction()
    {
        error_log("___index user__");
       // $this->_redirect('/feed');
       // exit();   
    }

    public function logoutAction()
    {
        $redirectPage = $this->_getParam('redirect',BASE_URL);
        //Delete cookies
        Core_Cookie::clearCookies(AUTH_USER_LOGIN_TOKEN, '/', DOMAIN);

        Zend_Session::destroy(true);        
  
        $this->_redirect($redirectPage);
        
        exit(1);
    }
       
}

