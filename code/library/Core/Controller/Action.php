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

    /**
     * Init
     */
    public function init() {
        // $this->_helper->layout->disableLayout();
        // $this->_helper->viewRenderer('login/login', null, true);

        // $arrLogin = Admin::getInstance()->getLogin();
        // $adminDetail = Admin::getInstance()->checkLogin();
        // $isAdmin = ($adminDetail) ? true : false;

        //get and set timetime
        /*$getTimezoneParam = array(
            'accountId' => $arrLogin['accountID'],
            'key' => USER_CONFIG_TIMEZONE,
        );*/

        $timezone = DEFAULT_TIMEZONE;
        // $timezoneConfig = UserConfig::getInstance()->getUserConfigByKey($getTimezoneParam);
        // isset($timezoneConfig['value']) && $timezone = $timezoneConfig['value'];

        date_default_timezone_set($timezone);

        $this->view->headTitle()->setSeparator(' - ');
        // $this->view->isAdmin = $isAdmin;
        $title = empty($this->title) ? 'ThiThien' : $this->title;
        $this->view->headTitle()->append($title);
        // $this->view->arrLogin = $arrLogin;
//        if($this->_request->isXmlHttpRequest()){
//            echo 'ajax';
//        }
       
        if(!$this->_request->isXmlHttpRequest()){
            global $globalConfig;

            // $arrLogin = Admin::getInstance()->getLogin();
            /**tr
             * Deny call by inner action make by action helper.
             * action helper have param in request is inner
             */
            if ($this->_request->getParam("inner", "") != "")
                return;

/*            if (!isset($arrLogin['accountID']) || empty($arrLogin['accountID'])) {

                $this->_redirect("/login");
                exit();
            }*/

            /* Check Login

             */
            //get Login
//        echo $_SESSION['username'];die;
//            $arrLogin = Admin::getInstance()->getLogin();

//            if ($this->_request->getControllerName() != "login" && $this->_request->getControllerName() != "logout") {
//
////            Core_Common::var_dump($arrLogin);
////            $arrLogin['avatar'] = !empty($arrLogin['avatar']) ? $arrLogin['avatar'] : PATH_AVATAR_URL.'/avatar_default.png';
//
//                $remoteIp = $this->_request->getServer('REMOTE_ADDR');
//                if (preg_match('/^192.168/', $remoteIp) && !preg_match('/^192.168.30.51$/', $remoteIp)) {
//                    if (!$this->_request->getServer('HTTPS')) {
//                        $host = $this->_request->getServer('HTTP_HOST');
//                        $this->_redirect("https://" . $host);
//                        exit;
//                    }
//                }
//
//                if ($this->_request->getActionName() != 'mail') {
//

//                }
//
//            }

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
//            $this->view->arrGroupMember = $arrGroupMember;
//            $this->view->groupTypes = array_flip($globalConfig['group_type']);
           // echo "54656";
        }


//        $myGroups = Core_Common::array_sort($arrGroupMember, 'group_type');


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

