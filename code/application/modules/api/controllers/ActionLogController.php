<?php
/**
 * Created by PhpStorm.
 * User: hien.nd
 * Date: 077/07/2016
 * Time: 14:33 AM
 */

class Api_ActionLogController extends Zend_Controller_Action{

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
        $serverInstance->setClass('ApiActionLog');

        //Hanlde instance
        $serverInstance->handle($this->_request);
    }    

    /**
        get action log Docs
        input:            
            AccountId = -1
            parentId = 0
    **/
    public function getActionLogDocsAction()
    {
        $iOffset = $this->_getParam('offset', 0);
        $iLimit = $this->_getParam('limit', 10);

        $arrLogin = Admin::getInstance()->getLogin();
        if(!isset($arrLogin['accountID']) || empty($arrLogin['accountID']))
            return Core_Server::setOutputData(true, 'You must login!', array());               

        $actionlogDocsType = 20;
        $arrReturn = array();
        // $arrLog = array();
        // $arrReturntmp = array();
        $arrReturn = ActionLog::getInstance()->getActionLog($arrLogin['accountID'], $actionlogDocsType, $iAction=0, $iOffset, $iLimit);            
        return Core_Server::setOutputData(false, 'OK', $arrReturn);        
    }

}

