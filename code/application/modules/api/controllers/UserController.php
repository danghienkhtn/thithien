<?php
/**
 * Created by PhpStorm.
 * User: thanh.lh
 * Date: 8/20/2015
 * Time: 10:12 AM
 */

class Api_UserController extends Zend_Controller_Action{

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
        $serverInstance->setClass('ApiUser');

        //Hanlde instance
        $serverInstance->handle($this->_request);
    }



}