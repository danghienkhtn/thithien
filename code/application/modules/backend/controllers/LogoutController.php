<?php

/**
 * @author      :   HoaiTN
 * @name        :   LoginController
 * @version     :   201010
 * @copyright   :   GNT
 */
class Backend_LogoutController extends Core_Controller_Action {

    /**
     * init of controller
     */
    public function init()
    {
        //Disable layout
        $this->_helper->layout()->disableLayout();      
        $this->_helper->viewRenderer->setNoRender();

        //Set title separator
        $this->view->headTitle()->setSeparator(' - ');

        //Add title
        $this->view->headTitle()->append('Hệ thống quản lý user + Analytics');

        //Add title
        $this->view->headTitle()->append('Logout');
    }

    /**
     * Default action
     */
    public function indexAction()
    {
        //Delete cookies
        Core_Cookie::clearCookies(AUTH_LOGIN_TOKEN, '/', DOMAIN);        
        Zend_Session::destroy(true);
        
  
        $this->_redirect(BASE_URL.'/login');
        
        exit(1);
    }
}

