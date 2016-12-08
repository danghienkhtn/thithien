<?php
/**
 * @name        :   IndexController
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   controller default 
 */

class TinRaoController extends Core_Controller_Action
{
     //var arr login
     private $arrLogin;
     
     public function init() {
        parent::init();
        
        //Asign login
        // $this->arrLogin = $this->view->arrLogin;
        
        //Get Controller
        $controller = $this->_request->getParam('controller');
        $this->view->controller = $controller;
       
    }

    public function comingSoonAction()
    {

    }
    /**
     * Default action
     */
    public function indexAction()
    {
       // $this->_redirect('/feed');
       // exit();   
    }
     
}

