<?php

/**
 * @author      :   DangHien
 * @name        :   Core_Controller_Action
 * @version     :   20161208
 * @copyright   :   Dahi
 */
abstract class Core_Controller_Action extends Zend_Controller_Action {

    //Configuration information
    protected $_configuration = null;
    //Storage
    protected $_storage = null;
    //User
    protected $_user = null;
    //Product
    protected $_product = null;

    // public $isLogin;
    // public $isAdmin;
    // public $arrlogin;

    /*function __construct() {
        //error_log("construction");
    }*/
    /**
     * Init
     */
    public function init() {        
        // $this->_helper->layout->disableLayout();
        // $this->_helper->viewRenderer('login/login', null, true);
// exit("sdasd");        
        $this->isLogin = FALSE;
        $this->isAdmin = FALSE;
        $this->arrLogin = array();

        $this->isLogin = AccountInfo::getInstance()->checkUserLogin();
        if($this->isLogin){
            $this->arrLogin = AccountInfo::getInstance()->getUserLogin();
            $this->isAdmin = ((is_array($this->arrLogin)) && (sizeof($this->arrLogin) > 0) && $this->arrLogin["is_admin"] == 1) ?  TRUE : FALSE;
        }     
        $timezone = DEFAULT_TIMEZONE;
        // $timezoneConfig = UserConfig::getInstance()->getUserConfigByKey($getTimezoneParam);
        // isset($timezoneConfig['value']) && $timezone = $timezoneConfig['value'];
        date_default_timezone_set($timezone);

        $this->view->headTitle()->setSeparator(' - ');
        
        $title = empty($this->title) ? 'ThiThien' : $this->title;
        $this->view->headTitle()->append($title);
// error_log(Zend_Json::encode($this->view));        
// request URL not Ajax
        if(!$this->_request->isXmlHttpRequest()){
            global $globalConfig;
            // $arrLogin = Admin::getInstance()->getLogin();
            /**tr
             * Deny call by inner action make by action helper.
             * action helper have param in request is inner
             */
            if ($this->_request->getParam("returnUrl", "") != "")
            {    
                // error_log("returnUrl here");
                $this->view->returnUrl = $this->_request->getParam("returnUrl");
            }    


            /*
            // write logs all action in frontend
            $controllerName = $this->_request->getControllerName();
            $sParams = '';
            foreach($this->_request->getParams() as $key=>$param)
            {
                if(!is_array($param)) {
    //                Core_Common::var_dump($param);
                    $sParams .= ' <br/> ' . $key . ' => ' . urldecode($param) . ' <br/>';
                }
            }
            ActionLog::getInstance()->insert($arrLogin['id'],$controllerName,'Front END', ' '.$arrLogin['accountID'], ' '.$arrLogin['nickName'], ' params: '.$sParams);
           //write active
           */
//        UserActive::getInstance()->addJobUserActiveUpsert($arrLogin['accountID'], intval(date("Y")), intval(date("m")), intval(date("d")));

//            $arrGroupTeam = $arrGroupProject = $arrResult = array('total' => 0, 'data' => array());
            //get Group Team
//        $arrGroupTeam = Group::getInstance()->getGroupListAll(1,2);

            //get Group Project
//        $arrGroupProject = Group::getInstance()->getGroupListAll(1,3);

            //test
//            $this->repairDataToViewAndController();


            //get GroupMember
//        $arrResult = GroupMember::getInstance()->getGroupMemberByMemberId($arrLogin['accountID']);

//            $arrGroupMember = $arrResult['data'];
            // check permission



            //Set title separator


            //Add title
            // $arrListAllowMenu = array(1, 2, 4, 6, 8, 16, 32, 64, 128, 256, 512, 1024, 2048);
            // $this->view->arrMenu = $arrListAllowMenu;
// echo "44544";
//            $this->view->arrGroupTeam = $arrGroupTeam;
//            $this->view->arrGroupProject = $arrGroupProject;
            $this->view->controllerName = $this->_request->getControllerName();
            $this->view->acctionName = $this->_request->getActionName();
            $this->view->isLogin = $this->isLogin;
            $this->view->isAdmin = $this->isAdmin;
            $this->view->arrLogin = $this->arrLogin;
        }

    }

    private function repairDataToViewAndController() 
    {
//        //Get configuration
//        $this->_configuration = Zend_Registry::get(APP_CONFIG);
//
//
//        $this->view->request = $this->_request;
//        $this->view->static = $this->_configuration->app->static;
//        $this->static = $this->_configuration->app->static;
    }

    public function requireJqueryUI() {
        $this->view->headLink()->prependStylesheet($this->static->frontend->css . '/jquery-ui.css');
      
    }

}

