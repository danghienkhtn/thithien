<?php
/**
 * @author      :   HoaiTN
 * @name        :   IndexController
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   controller default 
 */


class UserController extends Core_Controller_Action
{
     
    public function init() {
         
        parent::init();
        
        global $globalConfig;
        
        //Asign manager Type
        $this->view->arrManagerType = $globalConfig['manager_type']; 
        
        //Get Controller
        $controller = $this->_request->getParam('controller');
        $this->view->controller = $controller;
    }


    public function profileAction()
    {

    }

    public function getUsersAction()
    {
        $this->_helper->layout()->disableLayout();
        if ($this->_request->isPost()) {
            $userNames = $this->_getParam('key','');
            if(!is_array($userNames)) {
                $arrError = array('error' => true, 'message' => 'data must be array');
                echo Zend_Json::encode($arrError);
                exit();
            }
            $users = array();
            $accountIds = array();
            foreach($userNames as $userName)
            {
                $accountInfo = AccountInfo::getInstance()->getAccountInfoByUserName($userName);
                $accountInfo['replaceText'] = '[un='.$userName.']';
                if(!empty($accountInfo)) {
                    if (!in_array($accountInfo['account_id'],$accountIds)) {
                        $accountIds []= $accountInfo['account_id'];
                        $users  [] = Core_Common::accountProcess($accountInfo);
                    }
                }
            }
            $users = array_filter($users);
            $arrError = array('error' => false, 'message'=>'success', 'users' => $users);
        }
        else
            $arrError = array('error' => true, 'message'=>'post support only');
        echo Zend_Json::encode($arrError);
        exit();
    }

    public function searchUserTagInputTemplateAction()
    {
        $this->_helper->layout()->disableLayout();
        $arrAccount = array();
//        if ($this->_request->isPost()) {

            // parse params to Json
//        $key = $this->_getParam('term','');
        $queryString = Core_Common::getQueryString();
        $key = $queryString['term'];
//        $key = mb_strtolower(($key),'UTF-8');
//        if (get_magic_quotes_gpc()) $key = stripslashes($key);
//        $key = urldecode($key);

            if(trim($key) != '') {
                $accountsInfoEmail = AccountInfo::getInstance()->getAccountInfoListByLikeEmail($key, 0, ADMIN_PAGE_SIZE);
                $accountsInfoName = AccountInfo::getInstance()->getAccountInfoByLikeName($key, 0, ADMIN_PAGE_SIZE);
                $arrId = array();

                // set data for Account Info
                foreach ($accountsInfoEmail['data'] as $accountInfo) {
                    $arrId []= $accountInfo['account_id'];
                    $img = Core_Common::avatarProcess($accountInfo['picture']);
                    $arrAccount [] = array('uid' => $accountInfo['account_id'], 'value' => $accountInfo['name'], 'email' => $accountInfo['email'], 'image' => $img);
                }

                foreach ($accountsInfoName as $accountInfo) {
                    if(!in_array($accountInfo['account_id'],$arrId)) {

                        $arrId []= $accountInfo['account_id'];
                        $img = Core_Common::avatarProcess($accountInfo['picture']);
                        $arrAccount [] = array('uid' => $accountInfo['account_id'], 'value' => $accountInfo['name'], 'email' => $accountInfo['email'], 'image' => $img);
                    }
                }
//                $arrAccount = array_unique($arrAccount);
            }
//            Core_Common::var_dump($arrAccount);

//        }

        // return to view with Json type
        echo Zend_Json::encode($arrAccount);
        exit();
//

    }

    public function searchUserTagInputAction()
    {
        $this->_helper->layout()->disableLayout();
        $arrAccount = array();
        // if ($this->_request->isPost()) {

            // parse params to Json
            // $key = $this->_getParam('key','');
            $key = $this->_request->getParam('key');
            if (empty($key)) {
                $key = '';
            }
            if(trim($key) != '') {
                $accountsInfoEmail = AccountInfo::getInstance()->getAccountInfoListByLikeEmail($key, 0, ADMIN_PAGE_SIZE);
                $accountsInfoName = AccountInfo::getInstance()->getAccountInfoByLikeName($key, 0, ADMIN_PAGE_SIZE);
                $arrId = array();

                // set data for Account Info
                foreach ($accountsInfoEmail['data'] as $accountInfo) {
                    $arrId []= $accountInfo['account_id'];
                    $img = Core_Common::avatarProcess($accountInfo['picture']);
                    $arrAccount []= array('account_id' => $accountInfo['account_id'], 'name' =>$accountInfo['name'], 'email' => $accountInfo['email'], 'image_tag' =>$img);
                }

                foreach ($accountsInfoName as $accountInfo) {
                    if(!in_array($accountInfo['account_id'],$arrId)) {

                        $arrId []= $accountInfo['account_id'];
                        $img = Core_Common::avatarProcess($accountInfo['picture']);
                        $arrAccount [] = array('account_id' => $accountInfo['account_id'], 'name' => $accountInfo['name'], 'email' => $accountInfo['email'], 'image_tag' => $img);
                    }
                }
//                $arrAccount = array_unique($arrAccount);
            }
//            Core_Common::var_dump($arrAccount);

        // }

        // return to view with Json type
        echo Zend_Json::encode($arrAccount);
        exit();
//
//        $this->_helper->layout()->disableLayout();
//        $this->_helper->viewRenderer->setNoRender(true);
//        $arrAccount = array();
//        $html = '';
//        if ($this->_request->isPost()) {
//            // set param value
//            $name = $this->_getParam('name','');
//
//            $offsetTmp = $offset * $limit;
//
//            $accountsInfo = MemberSearch::getInstance()->getMemberSearch($offsetTmp, $limit, '', $name, $locationAddress, $iTennis, $iFootball, $iRunning, $iPoint, 0, $dLatitude, $dLongitude);
////            Core_Common::var_dump($accountsInfo);
//            foreach($accountsInfo['data'] as $acc)
//            {
//                $acc['account_id'] = explode('_',$acc['id'])[0];
//                $arrAccount []= Core_Common::accountProcess($acc);
//            }
//
//
//        }
//
//        // return to view with Json type
//        echo Zend_Json::encode($arrAccount);
//        exit();
    }

    /**
     * Default action
     */
    public function indexAction()
    {
        $iGroupId = $this->_request->getParam('groupid', 0);

    	//set group name to view
        $arrGroup = Group::getInstance()->getGroupByID($iGroupId);
        $arrGroup = Core_Common::groupProcess($arrGroup);
        $this->view->groupid = $iGroupId;
        $this->view->arrGroup = $arrGroup;

		$groupName = '';
        $groupType = '';
		isset($arrGroupList['group_name']) && $groupName = $arrGroupList['group_name'];
		isset($arrGroupList['group_type']) && $groupType = $arrGroupList['group_type'];
		
		$tempArrgroupName = array (
			$iGroupId => array (
				'group_name' => $groupName
			)
		);
    	$this->view->arrGroupList = $tempArrgroupName;
    	$this->view->groupType = $groupType;
    }

    public function feedgroupAction()
    {
        
    }
  
    /**
     * Detail action
     */
    
    public function detailAction()
    {
         //get Param
         $iAccountID = $this->_request->getParam('id', 0);
         $arrAccountInfo  = array();
         $arrGeneralAttHash = array();
         $arrAccountManager = array();
         $arrAccountExtra = array();
         
          //check params
         if($iAccountID>0)
         {
              //get data
             $arrAccountInfo = AccountInfo::getInstance()->getAccountInfoByAccountID($iAccountID);
             
             if(!empty($arrAccountInfo['direct_manager']) && $arrAccountInfo['direct_manager']>0)
             {
                    //get account manager
                   $arrAccountManager = AccountInfo::getInstance()->getAccountInfoByAccountID($arrAccountInfo['direct_manager']);
             }
             
             //get account extra info 
             //$arrAccountExtra=  AccountExtra::getInstance()->getAccountExtraDetail($iAccountID);
                   
             //General attribute
             $arrGeneralAttHash = General::getInstance()->getGeneralAttHash();
         }
         
         //get Att
         $arrGeneral = General::getInstance()->getGeneralAtt();
         
         
         //set to view
         $this->view->arrAccountInfo    = $arrAccountInfo;
         $this->view->arrGeneralAttHash = $arrGeneralAttHash;
         $this->view->arrAccountManager = $arrAccountManager;
         $this->view->arrGeneral        = $arrGeneral;
        // $this->view->arrAccountExtra = $arrAccountExtra;
    }

    public function autocompleteAction()
    {
        
        $arrResult = array();
        
        $this->_helper->layout()->disableLayout();
        //Disable render
        $this->_helper->viewRenderer->setNoRender();
        
        $sName= $this->_request->getParam('tag', '');
        
        if(!empty($sName))
        {
            $sName = urldecode(trim($sName));

             $iStart =0;
             $iPageSize = 50;
             
             $sSort ='';
             
             //Search Init
             $arrSearch= array(
                            'name'      => $sName,
                            'email'     => '',
                            'id'        => 0,
                            'identity'  => 0,
                            'taxcode'   => '',
                            'position'  => 0,
                            'departmentid'  => 0,
                            'teamid'        => 0  
                 );
             
        
            //get data search
           $arrTmp = Search::getInstance()->getProfileSearch($arrSearch, $iStart, $iPageSize, $sSort);

           /*  //init instance account
            $instanceAccountInfo= AccountInfo::getInstance();

            //get result
             $arrTmp = $instanceAccountInfo->getAccountInfoSuggestion($sName,$sPosition,$sEmail, $iStart, $iPageSize);
            
            */
            if(!empty($arrTmp['data']))
            {
                  //Asign data
                  $arrTmp = $arrTmp['data'];
                  
                  foreach($arrTmp as $value)
                  {

                      $arrResult[] = array('id' => $value['account_id'], 'fullname' => $value['name']);
                  }
            }
            
        }
        
        echo json_encode($arrResult);
        
        exit();
    }

    public function searchAction()
    {
        
        //int param
        $iPageSize = 300;
        
        //get Param search 
        $sName = $this->_request->getParam('name','');
        $iDepartmentID = $this->_request->getParam('departmentid',0);
        $iTeamID = $this->_request->getParam('teamid',0);
        $iAccountID = $this->_request->getParam('userid',0);
        $sSearchName = '';
        
        
        //filter space name
        if(!empty($sName))
        {
            $sName = urldecode(trim($sName));
        }
        
        
        $arrSearch = array(
            'name'      => $sName,
            'email'     => '',
            'id'        => 0,
            'identity'  => 0,
            'taxcode'   => '',
            'position'  => 0,
            'departmentid'  => $iDepartmentID,
            'teamid'        => $iTeamID  
        );
        
       
        $arrGeneralAttHash = array();
        //check 
       
        $iStart = 0;
    
        //get data search
        $arrResult = Search::getInstance()->getProfileSearch($arrSearch, $iStart, $iPageSize, '');
        $arrResult = isset($arrResult['data'])?$arrResult['data']:array();
        
        //check empty
        if(!empty($arrResult))
        {
            //Random array
            shuffle($arrResult);
            
            $arrGeneralAttHash = General::getInstance()->getGeneralAttHash();
        }
       
        
        //Assign view
        $this->view->arrResult  = $arrResult;
        $this->view->arrGeneralAttHash  = $arrGeneralAttHash;
        $this->view->iAccountID = $iAccountID;
        
        echo $this->view->render('user/search.phtml');
        
        exit;  
    }
    
    public function feedAction()
    {
    	
    	$iGroupID = $this->_request->getParam('groupid',6);
    	
    	$arrGroupList = Group::getInstance()->getGroupByID($iGroupID);

        Core_Common::deleteRedis(REDIS_FEED_GROUP_NOTIFY, $iGroupID);
    	$this->view->arrGroupList = $arrGroupList;
    	$this->view->groupid = $iGroupID;

    }

    public function searchuserAction()
    {
        $this->_helper->layout()->disableLayout();
        $arrAccount = array('total' => 0, 'data' => '');
        if ($this->_request->isPost()) {
            $params = $this->_request->getPost();
            if (!empty($params['data'])) {
                // parse params to Json
                $arrData = json_decode($params['data'], true);

                // set param value
//                $key = $arrData['key'];
                $teamId = isset($arrData['team']) ? $arrData['team'] : 0;
                $sEmail = isset($arrData['email']) ? $arrData['email'] : '';
                $key = isset($arrData['key']) ? $arrData['key'] : '';


                // search accounts info
                if(empty($sEmail))
                    $accountsInfo = AccountInfo::getInstance()->getAccountInfoList($key, '', 0, 0, '', 0, '',intval($teamId), 0,0,'','', 0, ADMIN_PAGE_SIZE);
                else
                    $accountsInfo = AccountInfo::getInstance()->getAccountInfoListByLikeEmail($sEmail, 0, ADMIN_PAGE_SIZE);

                // set total for arrAccount
                $arrAccount['total'] = $accountsInfo['total'];

                // set data for Account Info
                foreach ($accountsInfo['data'] as $accountInfo) {
                    $arrAccount['data'] [] = Core_Common::accountProcess($accountInfo);
                }
            }
        }

        // return to view with Json type
        echo Zend_Json::encode($arrAccount);
        exit();
    }

    public function userGuideAction()
    {        
        $this->_helper->layout->setLayout('user_guide');
        echo $this->_helper->layout->render();
        exit;
    }    

}

