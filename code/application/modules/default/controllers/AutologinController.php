<?php

/**
 * @author      :   HoaiTN
 * @name        :   LoginController
 * @version     :   201010
 * @copyright   :   GNT
 */
    
class AutologinController extends Zend_Controller_Action {

    protected $title = 'GNT Portal';
    /**
     * init of controller
     */
    public function init()
    {
       
        //Disable layout
        $this->_helper->layout()->disableLayout();
    }

    /**
     * Default action
     */
    public function indexAction()
    {
       
        //get Login
        $arrLogin = Admin::getInstance()->checkPermission();
        
        //Asign to view
        $this->view->arrLogin = $arrLogin;
    }
}


/*
curl -d "token=86C8E294-9BE3-11E0-B976-7886CD3C0818&email=vothanhvuphong123@ezweb.ne.jp&password=ng123456&sex=0&birth=19880915&nickname=vuphong" http://api.mobion.jp:8080/api/register/reg_member
*/