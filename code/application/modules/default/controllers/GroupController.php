<?php
/**
 * @author      :   HoaiTN
 * @name        :   IndexController
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   controller default 
 */
class GroupController extends Core_Controller_Action
{
    private $arrLogin;
     public function init() 
     {
        parent::init();
        
        global $globalConfig;
        
        //Get Controller
        $controller = $this->_request->getParam('controller');
        $this->view->controller = $controller;
        $this->arrLogin = $this->view->arrLogin;
     }

    public function orgChartAction()
    {
        $groupId = $this->_getParam('group_id', 0);
        $Group = Group::getInstance()->getGroupByID($groupId);
        $Obj = Core_Common::groupProcess($Group);

        $projectId = $this->_getParam('project_id', 0);
        if($groupId == 0 && $projectId > 0) {
            $Project = Project::getInstance()->selectById($projectId);
            $Obj = Core_Common::projectProcess($Project);
        }
        $this->view->groupid = $groupId;
        $this->view->Obj = $Obj;
    }

    public function groupAlbumAction()
    {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $offset = $this->_getParam('offset',0);

        $limit = 4;
        $name = '';
        $iYear = '';
        $type = 0;
        $iActive = 1;
        $offsetTmp = 0;


        if($offset == 1)
            $offsetTmp = $offset + $limit;
        else if($offset > 1)
            $offsetTmp = $offset * ($limit+1);

        
        $arrResult = GroupMember::getInstance()->getGroupMemberByMemberId($this->arrLogin['accountID'], $offsetTmp, $limit);
        
        $arrMyGroups = $arrGroupMember = $arrResult['data'];
//        Core_Common::var_dump($arrMyGroups);
        $arrHtml = array();
        if(!empty($arrMyGroups))
        {
            $offset++;
            foreach($arrMyGroups as $key=>$group)
            {
                $html = '';

                $AlbumGroup = Album::getInstance()->getAlbumGroup(array($group), '', $iYear, $type, $iActive, 0, 4);
                $params = array(
                    'arrTotalAlbum' => $AlbumGroup['arrTotalAlbum'], 'arrAlbumTeam' => $AlbumGroup['arrAlbumTeam'],
                    'arrGroupMember'=> array($group), 'urlImage' => PATH_IMAGES_URL . '/original/'

                );
//                var_dump($params);die;
                $html   =  $this->view->partial('group/lst-group-album.phtml',$params);
                $html   = htmlspecialchars($html, ENT_QUOTES, "UTF-8");
                if(trim($html) != '')
                    $arrHtml []= $html;

            }
        }

        echo Zend_Json::encode(array('offset' => $offset, 'html' =>$arrHtml));
    }

     /**
     * group suggest
     */


    public function indexAction()
    {
    }

    function myfunction_key($a,$b)
    {
        if ($a===$b)
        {
            return 0;
        }
        return ($a>$b)?1:-1;
    }

    public function scrollPaginationAction(){
    	//Disable layout
    	$this->_helper->layout()->disableLayout();
    	 
    	$arrResults = array();
    	
    	$iStart = $this->_getParam('offset', 0);
    	$iEnd = $this->_getParam('number', 9);
    	$query = $this->_getParam('query', '');
        $iEnd*=2;
    	$iTotal = 0;
    	$arrGroupSuggestion = array();

        if(trim($query) != '')
        {
            $arrGroups = array();
            $arrGroupTmp = Group::getInstance()->getGroupAlls(1,0,0,ADMIN_PAGE_SIZE,$query);
            $arrGroupSuggestionTmp = GroupMember::getInstance()->getGroupSuggestion($this->arrLogin['accountID'], 0, MAX_QUERY_LIMIT);
           foreach($arrGroupSuggestionTmp['data'] as $sug)
           {
               foreach($arrGroupTmp['data'] as $gr)
               {
                   if($sug['group_id'] == $gr['group_id'] && $gr['group_type'] != Group::$GroupTeam && $gr['group_type'] != Group::$GroupProject) {
                       $arrGroups['data'][] = $gr;
                       break;
                   }
               }
           }
            $arrGroups['total'] = (isset($arrGroups['data'])) ? count($arrGroups['data']) : 0;

        }
        else {
            //get  Suggestion Group
            $arrGroups = GroupMember::getInstance()->getGroupSuggestion($this->arrLogin['accountID'], $iStart, $iEnd);
        }
    	if(!empty($arrGroups) && $arrGroups['total'] > 0){

    		$iTotal = count($arrGroups['data']);

    		//get list requested
//    		$requestedGroup = Core_Common::selectRedis(0, MAX_QUERY_LIMIT, REDIS_GROUP_REQUEST_MEMBER_LIST, $this->arrLogin['accountID']);

	    	foreach ($arrGroups['data'] as $group){
                if( $group['group_type'] != Group::$GroupTeam && $group['group_type'] != Group::$GroupProject) {
                    $group = Core_Common::groupProcess($group);
                    $arrTmp = $group;
                    $arrTmp['is_pending'] = 0;
                    $invite = GroupInvitation::getInstance()->getGroupInviteationInviteByAccountFromAndGroupId($this->arrLogin['accountID'], 0, $group['group_id'], 0, 1);
                    if (!empty($invite['data'])) {
                        $arrTmp['is_pending'] = 1;
                    }
                    $arrTmp['account_to'] = $this->arrLogin['accountID'];
                    $arrGroupSuggestion[] = $arrTmp;
                }
	    	}

    	}
    	
    	//$data = json_encode(array('groups' => $arrGroups['data']));
    	//exit("{$_GET['jsonp']}($data)");
    	
    	$output = array('total' => intval($iTotal), 'groups' => $arrGroupSuggestion);
    	echo Zend_Json::encode($output);
    	exit();
    	
    	
    	
    }
    
    /**
     * create new group
     */
    public function createAction(){

    	if ($this->_request->isPost()) {
            $params = $this->_request->getPost();
            $arrError = array();
            if (!empty($params['data'])) {

                $group = json_decode($params['data'], true);
                if (!empty($group)) {

                    $group['content'] = $group['desc'];
                    $group['image_url'] = (trim($group['image_url']) == '') ? AvatarGroupOther : $group['image_url'];
                    $sGroupName = trim($group['name']);
                    //validate
                    if (strlen($sGroupName) < 4 || strlen($sGroupName) > 30 || empty($sGroupName)) {
                        $arrError = array('error' => true, 'message' => 'Group name \'s length range(4-30) characters.');
                    }else{
                        //check group name
                        $DuplicateGroup = Group::getInstance()->selectByName($sGroupName);
                        $DuplicateGroup = ($DuplicateGroup) ? $DuplicateGroup : array();
                        if (!empty($DuplicateGroup)) {
                            $arrError = array('error' => true, 'message' => 'Group already exists.');
                        }
                    }

                    if (strlen($group['content']) > 255) {
                        $arrError = array('error' => true, 'message' => 'Group description \'s length < 255 a characters.');
                    }

                    if ($group['is_public'] != 0 && $group['is_public'] != 1) {
                        $arrError = array('error' => true, 'message' => 'Privacy invalid.');
                    }



                    if (!empty($arrError)) {
                        echo Zend_Json::encode($arrError);
                        exit();
                    }
                    //create new group

                    $group['account_id'] = $this->arrLogin['accountID'];
                    $group['manager_id'] = $this->arrLogin['accountID'];
                    $group['admin_id'] = $this->arrLogin['accountID'];
                    $group['is_bom'] = 0;
                    $group['active'] = 1;
                    $group['group_type'] = 4; //4 is other
                    $group['sort_order'] = 0;
                    $group['country_id'] = 0;
                    $group['group_name'] = $sGroupName;
                    $members = $group['account_ids'];

                    if ($group['is_public'] == 1)
                        Group::getInstance()->addGroup($group, array(), $members);
                    else
                        Group::getInstance()->addGroup($group, $members);
                }

                $arrError = array('error' => false, 'message' => 'create group success.');
            }
        } else
            $arrError = array('error' => true, 'message' => 'post support only.');

        echo Zend_Json::encode($arrError);
        exit();
    }
    
    /**
     * update info group
     */
    public function updateAction(){
    	$arrError = array();
    	if($this->_request->isPost()) {
            $this->_helper->layout()->disableLayout();

            $params = $this->_request->getPost();
            $iGroupId = empty($params['groupid']) ? 0 : $params['groupid'];
            $sName = empty($params['name']) ? '' : trim($params['name']);
            $sDesc = $params['desc'];
            $iIsPublic = $params['is_public'];

            $group = Group::getInstance()->getGroupByID($iGroupId);

            $DuplicateGroup = Group::getInstance()->selectByName($sName);
            $DuplicateGroup = ($DuplicateGroup) ? $DuplicateGroup : array();

            //validate
            if (empty($group))
                $arrError = array('error' => true, 'message' => 'Group not exists.');
            else {
                $group = Core_Common::groupProcess($group);
                if (!$group['is_admin'] && !$group['is_manager'])
                    $arrError = array('error' => true, 'message' => 'Access is dined.');
            }
            if (strlen($sName) < 4 || strlen($sName) > 30 || empty($sName)) {
                $arrError = array('error' => true, 'message' => 'Group name \'s length range(4-30) characters.');
            }else{
                //check group name
                $DuplicateGroup = Group::getInstance()->selectByName($sName);
                $DuplicateGroup = ($DuplicateGroup) ? $DuplicateGroup : array();
                if (!empty($DuplicateGroup)) {
                    if($DuplicateGroup['group_id'] != $group['group_id'])
                        $arrError = array('error' => true, 'message' => 'Group already exists.');
                }
            }
            // check permission
            if (strlen($group['content']) > 255) {
                $arrError = array('error' => true, 'message' => 'Group description \'s length < 255 a characters.');
            }

            if ($group['is_public'] != 0 && $group['is_public'] != 1) {
                $arrError = array('error' => true, 'message' => 'Privacy invalid.');
            }



            if (!empty($arrError)) {
                echo Zend_Json::encode($arrError);
                exit();
            }

            // Do update
            $group['group_name'] = $sName;
            $group['content'] = $sDesc;
            $group['is_public'] = $iIsPublic;
            $group['image_url'] =  empty($params['image_url']) ?  $group['image_url'] : $params['image_url'];
            Group::getInstance()->updateGroup($group);
            $arrError = array('error' => false, 'message' => 'Update Success');

            echo Zend_Json::encode($arrError);
            exit();
        }

    	$iGroupId = $this->_request->getParam('id', 0);
    	
    	$arrGroup = Group::getInstance()->getGroupByID($iGroupId);
        $arrGroup = Core_Common::groupProcess($arrGroup);
    	$this->view->groupid = $iGroupId;
    	$this->view->arrGroup = $arrGroup;

    	//set group name to view
		$groupName = '';
        $groupType = '';
		isset($arrGroup['group_name']) && $groupName = $arrGroup['group_name'];
		isset($arrGroup['group_type']) && $groupType = $arrGroup['group_type'];
        
		$tempArrgroupName = array (
			$iGroupId => array (
				'group_name' => $groupName
			)
		);
    	$this->view->arrGroupList = $tempArrgroupName;
    	$this->view->groupType = $groupType;


    }
    
    /**
     * my group
     */
    public function myAction()
    {
    	$iStart = 0;
    	$iLimit = MAX_QUERY_LIMIT;
    	
    	$total = 0;
    	$arrMyGroups = array();
    	$arrGroupCounts = array();
    	$arrAccountAdminInfos = array();

    	//get my group
    	$arrResults = GroupMember::getInstance()->getGroupMemberByMemberId($this->arrLogin['accountID'], 0, MAX_QUERY_LIMIT);

    	if(!empty($arrResults)){
    		
    		//get parse my group
    		$arrTmp = $this->getMyGroupInfos($arrResults['data'], $this->arrLogin['accountID']);
    		$arrMyGroups = $arrTmp['myGroup'];
    		$arrAccountAdminIds = $arrTmp['accountAdminId'];
   	
    		//clear data
    		$arrTmp = array();

    		$arrAccountAdminInfos = AccountInfo::getInstance()->getAccountListShort($arrAccountAdminIds);
    	
    	}

    	$this->view->total = count($arrResults['data']);
    	$this->view->MyGroups = $arrMyGroups;
    	$this->view->AccountAdminInfos = $arrAccountAdminInfos;
    }

    public function searchMyGroupAction()
    {
        if($this->_request->isPost()) {
            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);

            $name = $this->_getParam('name', '');
            $arrGroups = array();
            $arrGroupTmp = Group::getInstance()->getGroupAlls(1, 0, 0, MAX_QUERY_LIMIT, $name);
            $arrMyGroupTmp = GroupMember::getInstance()->getGroupMemberByMemberId($this->arrLogin['accountID'], 0, MAX_QUERY_LIMIT);
            foreach ($arrMyGroupTmp['data'] as $my) {
                foreach ($arrGroupTmp['data'] as $gr) {
                    if ($my['group_id'] == $gr['group_id']) {
                        $arrGroups['data'][] = $gr;
                        break;
                    }
                }
            }
            $iTotal = 0;
            $arrMyGroups = array();
            $arrAccountAdminInfos = array();
            if (!empty($arrGroups)) {
//                $arrGroups['total'] = count($arrGroups['data']);
                $iTotal = count($arrGroups['data']);
                //get parse my group
                $arrTmp = $this->getMyGroupInfos($arrGroups['data'], $this->arrLogin['accountID']);

                $arrMyGroups = $arrTmp['myGroup'];
                $arrAccountAdminIds = $arrTmp['accountAdminId'];

                //clear data
                $arrTmp = array();

                $arrAccountAdminInfos = AccountInfo::getInstance()->getAccountListShort($arrAccountAdminIds);

            }
            $html = $this->view->partial('group/lst-my-group.phtml',array('total'=>$iTotal,'MyGroups'=>$arrMyGroups,'AccountAdminInfos'=>$arrAccountAdminInfos, 'arrLogin'=>$this->arrLogin));
            $html = htmlspecialchars($html, ENT_QUOTES, "UTF-8");
            echo Zend_Json::encode(array('offset'=>0,'show_more'=>false,'html'=>$html,'total'=>$iTotal));
            exit();
//            $this->view->total = $arrGroups['total'];
//            $this->view->MyGroups = $arrMyGroups;
//            $this->view->AccountAdminInfos = $arrAccountAdminInfos;
        }

    }
    
    public function joinAction(){
        if ($this->_request->isPost()) {
            global $globalConfig;
            $arrError = array();
            $params = $this->_request->getPost();
            $iGroupId = empty($params['g']) ? 0 : $params['g'];

            //get info group by group id
            $group = Group::getInstance()->getGroupByID($iGroupId);
            $groupMember = GroupMember::getInstance()->getGroupMemberByAccountAndGroupId($this->arrLogin['accountID'], $iGroupId);
            $groupInvitations = GroupInvitation::getInstance()->getGroupInviteationRequestByAccountFromAndGroupId($this->arrLogin['accountID']
                , $globalConfig['group_request']['request'], $iGroupId);

            if (empty($group))
                $arrError = array('error' => true, 'message' => 'Group not exists.');

            if (!empty($groupMember))
                $arrError = array('error' => true, 'message' => 'You are already member of Group.');

            if (!empty($groupInvitations['data'])) {
                $arrError = array('error' => true, 'message' => 'Request has been sent.');
            }

            if ($group['group_type'] != 0 && $group['group_type'] < 4) {
                $arrError = array('error' => true, 'message' => 'Support group type "Other" only.');
            }

            if (!empty($arrError)) {
                echo Zend_Json::encode($arrError);
                exit();
            }

            //if group is public
            if ($group['is_public'] == 1) {
                //insert group member
                $arrData = array();
                $arrData['account_id'] = $this->arrLogin['accountID'];
                $arrData['group_id'] = $iGroupId;
                $arrData['level'] = GroupMember::$staff;
                $arrData['create_date'] = time();

                GroupMember::getInstance()->addGroupMember($arrData);
                $arrError = array('error' => false, 'message' => 'Join Group success.', 'is_public' => true);
            } else {
                //insert group invite with type is request (0)
                $arrData = array();
                $arrData['group_id'] = $iGroupId;
                $arrData['account_from'] = $this->arrLogin['accountID'];
                $arrData['account_to'] = $group['admin_id'];
                $arrData['is_pending'] = 1;
                $arrData['content'] = '';
                $arrData['create_date'] = time();
                $arrData['type'] = $globalConfig['group_request']['request'];
                $arrData['group_name'] = $group['group_name'];
                GroupInvitation::getInstance()->insertGroupInvitation($arrData);
                $arrError = array('error' => false, 'message' => 'Your Request Has Been Sent Successfully.', 'is_public' => false);
            }

        } else
            $arrError = array('error' => true, 'message' => 'post support only.');

        echo Zend_Json::encode($arrError);
        exit();
    }


    public function cancelInvitationAction()
    {
        if ($this->_request->isPost()) {
            global $globalConfig;
            $this->_helper->layout()->disableLayout();
            $params = $this->_request->getPost();
            $iGroupId = empty($params['g']) ? 0 : $params['g'];
            $arrError = array();
            //get info group by group id
            $group = Group::getInstance()->getGroupByID($iGroupId);
            $groupInvitation = GroupInvitation::getInstance()->getGroupInviteationInviteByAccountToAndGroupId($this->arrLogin['accountID']
                , $globalConfig['group_request']['invite'], $iGroupId, 0, MAX_QUERY_LIMIT);
            if (empty($group))
                $arrError = array('error' => true, 'message' => 'Group not exists.');
            if (empty($groupInvitation['data'])) {
                $arrError = array('error' => true, 'message' => 'Request not exists.');
            }else{
                $groupInvitationOwner = $groupInvitation['data'][0];
                if ($groupInvitationOwner['account_to'] != $this->arrLogin['accountID'])
                    $arrError = array('error' => true, 'message' => 'Access is dined.');
            }

            if (!empty($arrError)) {
                echo Zend_Json::encode($arrError);
                exit();
            }

            if (!empty($groupInvitation['data'])) {
                $groupInvitation = $groupInvitation['data'][0];
                //delete request invite
                GroupInvitation::getInstance()->deleteGroupInvitation($groupInvitation['request_id']);
                $arrError = array('error' => false, 'message' => 'request success.');
            }
        }else
            $arrError = array('error' => true, 'message' => 'support post only.');

        echo Zend_Json::encode($arrError);
        exit;
    }

    public function cancelRequestAction()
    {
        if ($this->_request->isPost()) {
            global $globalConfig;
            $this->_helper->layout()->disableLayout();
            $params = $this->_request->getPost();
            $iGroupId = empty($params['g']) ? 0 : $params['g'];
            $arrError = array();
            //get info group by group id
            $group = Group::getInstance()->getGroupByID($iGroupId);
            $groupInvitation = GroupInvitation::getInstance()->getGroupInviteationInviteByAccountFromAndGroupId($this->arrLogin['accountID']
                , $globalConfig['group_request']['request'], $iGroupId, 0, MAX_QUERY_LIMIT);

            if (empty($group))
                $arrError = array('error' => true, 'message' => 'Group not exists.');
            if (empty($groupInvitation['data'])) {
                $arrError = array('error' => true, 'message' => 'Request not exists.');
            }else{
                $groupInvitationOwner = $groupInvitation['data'][0];
                if ($groupInvitationOwner['account_from'] != $this->arrLogin['accountID'])
                    $arrError = array('error' => true, 'message' => 'Access is dined.');
            }

            if (!empty($arrError)) {
                echo Zend_Json::encode($arrError);
                exit();
            }

            if (!empty($groupInvitation['data'])) {
                $groupInvitation = $groupInvitation['data'][0];
                //delete request invite
                GroupInvitation::getInstance()->deleteGroupInvitation($groupInvitation['request_id']);
                $arrError = array('error' => false, 'message' => 'request success.');
            }
        }else
            $arrError = array('error' => true, 'message' => 'support post only.');

        echo Zend_Json::encode($arrError);
        exit;
    }

    /*
     * accept a group
     */
    public function acceptAction(){
    	 
    	if ($this->_request->isPost()) {
            $this->_helper->layout()->disableLayout();
            global $globalConfig;

            $params = $this->_request->getPost();
            $iGroupId = empty($params['g']) ? 0 : $params['g'];
            $arrError = array();


            //get info group by group id
            $group = Group::getInstance()->getGroupByID($iGroupId);

            $groupMember = GroupMember::getInstance()->getGroupMemberByAccountAndGroupId($this->arrLogin['accountID'], $iGroupId);
            $groupInvitation = GroupInvitation::getInstance()->getGroupInviteationInviteByAccountToAndGroupId($this->arrLogin['accountID']
                , $globalConfig['group_request']['invite'], $iGroupId, 0, MAX_QUERY_LIMIT);

            if (empty($group))
                $arrError = array('error' => true, 'message' => 'Group not exists.');
            if (!empty($groupMember))
                $arrError = array('error' => true, 'message' => 'You are already member of Group.');

            if (empty($groupInvitation['data'])) {
                $arrError = array('error' => true, 'message' => 'Request not exists.');
            }else{
                $groupInvitationOwner = $groupInvitation['data'][0];
                if ($groupInvitationOwner['account_to'] != $this->arrLogin['accountID'])
                    $arrError = array('error' => true, 'message' => 'Access is dined.');
            }

            if (!empty($arrError)) {
                echo Zend_Json::encode($arrError);
                exit();
            }


            //insert group member
            $groupMember = array('account_id' => $this->arrLogin['accountID'], 'group_id' => $iGroupId, 'level' => GroupMember::$staff);
            $iGroupMemberId = GroupMember::getInstance()->addGroupMember($groupMember);

            if (!empty($groupInvitation['data']) && $iGroupMemberId) {
                $groupInvitation = $groupInvitation['data'][0];
                //delete request invite
                GroupInvitation::getInstance()->deleteGroupInvitation($groupInvitation['request_id']);

                //send notification to admin
                $sContent = 'has been accepted invite of you to join ' . $group['group_name'];
                $sLink = 'javascript:void(0);';
                PhotoFeed::getInstance()->addJobNotification(NOTIFY_GROUP_APP, NOTIFY_GROUP_ACCEPT_INVITE, $iGroupMemberId, $iGroupId
                    , $sContent, $groupInvitation['account_to'], $groupInvitation['account_from'], array(), $sLink, '');

                $arrError = array('error' => false, 'message' => 'Join Group success.');

            } else {
                $arrError = array('error' => true, 'message' => 'request not sent.');
            }
        } else
            $arrError = array('error' => true, 'message'=> 'post support only.');

        echo Zend_Json::encode($arrError);
        exit();
    	 
    }
    
    /*
     * delete group
     */
    public function deleteAction()
    {
        $iGroupId = $this->_request->getParam('g', 0);
        $group = Group::getInstance()->getGroupByID($iGroupId);
        if (empty($group))
            $arrError = array('error' => true, 'message' => 'Group not exists.');
        else {
            $group = Core_Common::groupProcess($group);
            if (!$group['is_admin'] && !$group['is_manager'])
                $arrError = array('error' => true, 'message' => 'Access is dined.');
        }
        if (!empty($arrError)) {
            echo Zend_Json::encode($arrError);
            exit();
        }

        Group::getInstance()->deleteGroup($iGroupId);
        $arrError = array('error' => false, 'message' => 'request success.');
    	
    	echo Zend_Json::encode($arrError);
    	exit();
    }

    public function editAction()
    {
        global $globalConfig;
        $id = $this->_getParam('id',0);
        $group = Group::getInstance()->getGroupByID($id);

        $this->view->groupType = array_flip($globalConfig['group_type']);
        $this->view->countries  = $globalConfig['country'];
        $this->view->group  = $group;
        $bomGroup = Group::getInstance()->selectByIsBom(IS_BOM);
        if (!empty($bomGroup))
            $this->view->bomMembers = GroupMember::getInstance()->getGroupMemberID($bomGroup['group_id'], 0, MAX_QUERY_LIMIT);
    }

    public function leaveAction()
    {
        $arrError = array();
        if($this->_request->isPost())
        {
            $iGroupID = $this->_request->getParam('id', 0);
            $group = Group::getInstance()->getGroupByID($iGroupID);
            if(!empty($group))
            {
                if ($group['group_type'] == 0 || $group['group_type'] == 4) {
                    GroupMember::getInstance()->deleteGroupMember($this->arrLogin['accountID'], $iGroupID);
                    $arrError = array('error' => false, 'message' => 'request success.');
                }else
                    $arrError = array('error'=>true,'message'=>'support group type "Other" only.');
            }else
                $arrError = array('error'=>true,'message'=>'group not exits.');
        }else
            $arrError = array('error'=>true,'message'=>'post support only.');

    	echo Zend_Json::encode($arrError);
    	exit();
    }
    
    public function invitelistAction(){
    	
    	global $globalConfig;
    	$arrResults = array();
    	$arrGroupCounts = array();
    	$total = 0;
    	
    	$iStart = 0;
    	$iLimit = MAX_QUERY_LIMIT;
    	
    	$arrGroupAll = array();
    	
    	$arrGroupAlls = Group::getInstance()->getGroupAll($iStart, $iLimit);//get group list all
    	
    	$arrResults = GroupInvitation::getInstance()->getGroupInviteationByAccountTo($this->arrLogin['accountID']
    			, $globalConfig['group_request']['invite'], $iStart, $iLimit);
    	
    	
    	if(!empty($arrResults) && $arrResults['total'] > 0){
    		
    		$arrGroupIds = array();
    		
    		foreach ($arrResults['data'] as $group){

                $groupTmp = Group::getInstance()->getGroupByID($group['group_id']);
                if(empty($groupTmp))
                    GroupInvitation::getInstance()->deleteGroupInvitation($group['request_id']);

                else {
                    $arrGroupIds[] = $group['group_id'];
                    $total++;
                }
    		}
    		
    		if(!empty($arrGroupIds)){
    			//get group
    			foreach ($arrGroupAlls['data'] as $value){
    				if(in_array($value['group_id'], $arrGroupIds)){
    					$arrTmp = array();
    					$arrTmp = $value;
    					$arrGroupAll[$value['group_id']] = Core_Common::groupProcess($arrTmp);
    				}
    			}
    			
    		}
    	}

    	$this->view->Groups = !empty($arrResults) ? $arrResults['data'] : array();
    	$this->view->GroupAlls = $arrGroupAll;
    	$this->view->total = $total;
    	
    }
    
    /*
     * request peding
     */
    public function requestedlistAction(){
    	 
    	global $globalConfig;

    	//int param
    	$iPage = $this->_request->getParam('page', 1);
    	$iPageSize = 10;
    	
    	$iStart = ($iPage - 1) * $iPageSize;
    	$iTotal = 0;
    	$arrDatas = array();
    	$arrAccountAdminInfos = array();
    	  	
    	//get list
    	$arrResults = GroupInvitation::getInstance()->getGroupInviteationByAccountTo($this->arrLogin['accountID']
    			, $globalConfig['group_request']['request'], $iStart, $iPageSize);
    
    	
    	if(!empty($arrResults) && $arrResults['total'] > 0){
    		
    		$iTotal = $arrResults['total'];

    		$arrAccountAdminIds = array();
    		$arrGroups = Group::getInstance()->getGroupAll($iStart, MAX_QUERY_LIMIT);//get all group
    		
    		if(!empty($arrGroups)){
    			
    			//group data
	    		foreach ($arrResults['data'] as $group){
	    			
	    			$arrTmp = array();
	    			$arrTmp = $group;
	    			
	    			$arrAccountAdminIds[] = $group['account_from'];
	    			
	    			foreach ($arrGroups['data'] as $value){
	    				if($group['group_id'] == $value['group_id']){
	    					$arrTmp['group_name'] = $value['group_name'];
	    					break;
	    				}
	    			}
	    			
	    			$arrDatas[] = $arrTmp;
	    			
	    		}
    		}
    		
    		if(!empty($arrAccountAdminIds)){
    			$arrAccountAdminInfos = AccountInfo::getInstance()->getAccountListShort($arrAccountAdminIds);
    		}
    	}
    	
    	// paging
    	$paginator = new Zend_Paginator(new Page($arrDatas, $iTotal));
    	$paginator->setCurrentPageNumber($iPage);
    	$paginator->setItemCountPerPage($iPageSize);
    	
    	Zend_Paginator :: setDefaultScrollingStyle('Sliding');
    	Zend_View_Helper_PaginationControl :: setDefaultViewPartial('/pagination.phtml');
    	
    	$this->view->AccountInfos = $arrAccountAdminInfos;
    	//Assign view paging
    	$this->view->paginator = $paginator;
    	$this->view->page = $iPage;
    	 
    }
    
    public function acceptrequestAction(){
    	
    	if($this->_request->isPost())
    	{
            $params = $this->_request->getPost();
            $iRequestId = empty($params['request_id']) ? 0 : $params['request_id'];
            $arrError = GroupInvitation::getInstance()->doAcceptRequest($iRequestId);

        } else
            $arrError = array('error' => true, 'message' => 'support post only.');

        echo Zend_Json::encode($arrError);
        exit();
    }
    
    public function deleterequestAction(){
    	
    	if($this->_request->isPost())
    	{
    		$params = $this->_request->getPost();
    		$iRequestId = empty($params['request_id']) ? 0 : $params['request_id'];
            $arrError = GroupInvitation::getInstance()->doDeleteRequest($iRequestId);
    	}else
            $arrError = array('error' => true, 'message' => 'support post only.');

        echo Zend_Json::encode($arrError);
        exit;

    }
    
 
    public function deleterequestlistAction(){
    	 
    	if($this->_request->isPost())
    	{
    		$params = $this->_request->getPost();
    		$data = json_decode($params['data'], true);
            $sRequestId = isset($data['request_ids']) ? $data['request_ids'] : '';
            $requestIds = (is_array($sRequestId)) ? $sRequestId :explode(',',$sRequestId);
            if(empty($requestIds))
                $arrError []= array('error' => true,'message' => 'invalid data.');
            else
            {
                foreach($requestIds as $iRequestId)
                {
                    $arrError []= GroupInvitation::getInstance()->doDeleteRequest($iRequestId);
                }
            }

    	}else
            $arrError []= array('error' => true, 'message' => 'support post only.');

        echo Zend_Json::encode($arrError);
        exit();
    }
    
    public function acceptrequestlistAction(){
    	
    	if($this->_request->isPost()){

            $params = $this->_request->getPost();
            $data = json_decode($params['data'], true);
            $sRequestId = isset($data['request_ids']) ? $data['request_ids'] : '';
            $requestIds = (is_array($sRequestId)) ? $sRequestId :explode(',',$sRequestId);
            if(empty($requestIds))
                $arrError []= array('error' => true,'message' => 'invalid data.');
            else
            {
                foreach($requestIds as $iRequestId)
                {
                    $arrError []= GroupInvitation::getInstance()->doAcceptRequest($iRequestId);
                }
            }

        }else
            $arrError []= array('error' => true, 'message' => 'support post only.');

    	echo Zend_Json::encode($arrError);
        exit;
    }
    
    /**
     * list member of group
     */
    public function scrollmemberpaginationAction()
    {


    	//Disable layout
    	$this->_helper->layout()->disableLayout();
    	 
    	$arrResults = array();
    	 
    	 
    	if($this->_request->isPost())
    	{
    		$params = $this->_request->getPost();
    	
    		$iStart = is_numeric($params['offset']) ? $params['offset'] : 0;
    		$iEnd = is_numeric($params['number']) ? $params['number'] : ADMIN_PAGE_SIZE;
    		$iGroupId = !empty($params['groupId']) ? $params['groupId'] : 0;
    		$sName = empty($params['name']) ? '' : $params['name'];
    		
    		if($iGroupId > 0){
    			
    			
    			//get group detail by groupid
    			$arrGroup = Group::getInstance()->getGroupByID($iGroupId);

    			$arrTmp = GroupMember::getInstance()->getGroupMembeList($iGroupId, $iStart, $iEnd, $sName);

    			$arrMembers = $arrTmp;
    			
    			
    			if(!empty($arrMembers)){
    				
//	    			$arrGroupIds = array();
//	    			$arrGroupIds[] = $iGroupId;
//	    			$arrAccountAdminIds = array();
//	    			$arrGroupLists = array();
//	    			$arrAccountAdminInfos = array();
	    			
//	    			foreach($arrMembers['data'] as $value)
//	    			{
//	    				if(!in_array($value['team_id'], $arrGroupIds))
//	    				{
//	    					$arrGroupIds[] = $value['team_id'];
//	    				}
//
//	    				if(!in_array($value['manager_id'], $arrAccountAdminIds) && $value['manager_id'] > 0){
//	    					$arrAccountAdminIds[] = $value['manager_id'];
//	    				}
//
//	    			}
//
//
//	    			//get info admin
//	    			if(!empty($arrAccountAdminIds)){
//	    				$arrAccountAdminInfos = AccountInfo::getInstance()->getAccountListShort($arrAccountAdminIds);
//	    			}
//
//	    			if(!empty($arrGroupIds))
//	    			{
//	    				$arrGroupLists = Group::getInstance()->getGroupList2($arrGroupIds);
//	    			}
	    			
	    			
	    			$manager = $admin = array();
	    			foreach ($arrMembers['data'] as $groupMember){

                        $groupMember = Core_Common::groupMemberProcess($groupMember);
                        if($groupMember['accountInfo']['active'] == 1)
	    				    $arrResults[] = $groupMember;
	    			}
    			}
    			
    		}
//            $members = $manager + $admin + $arrResults;
    		$output = array('members'=>$arrResults);
    		echo Zend_Json::encode($output);
    		exit();
    	}
    	
    }
    
    public function settingrequestAction(){
    	
    	global $globalConfig;
    	
    	$iGroupId = $this->_request->getParam('id', 0);
    	 
    	$group = Group::getInstance()->getGroupByID($iGroupId);
    	
    	if(!empty($group)){

    		$iPage = $this->_request->getParam('page', 1);
    		$iPageSize = ADMIN_PAGE_SIZE;

    		$iStart = ($iPage - 1) * $iPageSize;




    		$groupInvites = GroupInvitation::getInstance()->getGroupInviteationInviteByAccountFromAndGroupId($this->arrLogin['accountID']
    				, $globalConfig['group_request']['invite'], $iGroupId, $iStart, $iPageSize);

            $iTotal = $groupInvites['total'];
	    		//result
                $lstGroupInvite = array();
	    		foreach ($groupInvites['data'] as $key=>$groupInvite){
                    $lstGroupInvite[] = Core_Common::groupInviteProcess($groupInvite);
	    		}
	    	}

	    	// paging
	    	$pagination = new Zend_Paginator(new Page($lstGroupInvite, $iTotal));
            $pagination->setCurrentPageNumber($iPage);
            $pagination->setItemCountPerPage($iPageSize);

	    	Zend_Paginator :: setDefaultScrollingStyle('Sliding');
	    	Zend_View_Helper_PaginationControl :: setDefaultViewPartial('/pagination.phtml');

        $group = Core_Common::groupProcess($group);
	    	//Assign view paging
	    	$this->view->groupInvites = $pagination;
	    	$this->view->page = $iPage;
            $this->view->group = $group;
            $this->view->arrGroup = $group;
            $this->view->groupid = $group['group_id'];

    }
    
    public function settingmemberAction(){
    	
    	global $globalConfig;
    	$arrResults = array();
    	$iGroupId = $this->_request->getParam('id', 0);
    	$arrGroup = Group::getInstance()->getGroupByID($iGroupId);
    	$isAdmin = false;
    	 
    	if(!empty($arrGroup)){
    	
    		$iPage = $this->_request->getParam('page', 1);
    		$iPageSize = ADMIN_PAGE_SIZE;
    		 
    		$iStart = ($iPage - 1) * $iPageSize;
    		$iEnd = $iPageSize;
    		
    		$iTotal = 0;
    		$sName = '';
    		
    		//is admin
    		if($this->arrLogin['accountID'] == $arrGroup['admin_id']){
    			$isAdmin = true;
    		}
    		
    		$arrTmp = GroupMember::getInstance()->getGroupMembeList($iGroupId, $iStart, $iEnd, $sName);
    		$arrMembers = $arrTmp;
    		

    		$arrAccountAdminInfos = array();
    		
    		if(!empty($arrMembers)){
    		
    			$iTotal = $arrMembers['total'];
    			
    			$arrGroupIds = array();
    			$arrGroupIds[] = $iGroupId;
    			$arrAccountAdminIds = array();
    			$arrGroupLists = array();
    			
    		
    			foreach($arrMembers['data'] as $value)
    			{
    				if(!in_array($value['team_id'], $arrGroupIds))
    				{
    					$arrGroupIds[] = $value['team_id'];
    				}
    				 
    				if(!in_array($value['manager_id'], $arrAccountAdminIds) && $value['manager_id'] > 0){
    					$arrAccountAdminIds[] = $value['manager_id'];
    				}
    		
    			}
    		
    		
    			//get info admin
    			if(!empty($arrAccountAdminIds)){
    				$arrAccountAdminInfos = AccountInfo::getInstance()->getAccountListShort($arrAccountAdminIds);
    			}
    		
    			if(!empty($arrGroupIds))
    			{
    				$arrGroupLists = Group::getInstance()->getGroupList2($arrGroupIds);
    			}
    		
    		
    		
    			foreach ($arrMembers['data'] as $value){
    				$arrTmp = array();
    				$arrTmp = $value;
    				$arrTmp['is_admin'] = false;
    				$arrTmp['manager_name'] = '';
    				$arrTmp['group_name'] = $arrGroup['group_name'];

    				//team name
    				if(isset($arrGroupLists[$value['team_id']]['group_name'])){
    					$arrTmp['group_name'] = $arrGroupLists[$value['team_id']]['group_name'];
    				}
    				 
    				//picture
                    $arrTmp['picture'] = Core_Common::avatarProcess($value['picture']);

    				 
    				//is admin
    				if($value['account_id'] == $arrGroup['admin_id']){
    					$arrTmp['is_admin'] = true;
    				}
    				 
    				//manager name
    				if(isset($arrAccountAdminInfos[$value['manager_id']]['name'])){
    					$arrTmp['manager_name'] = $arrAccountAdminInfos[$value['manager_id']]['name'];
    				}
    				 
    				$arrResults[] = $arrTmp;
    			}
    		}
    		
    	
    		// paging
    		$paginator = new Zend_Paginator(new Page($arrResults, $iTotal));
    		$paginator->setCurrentPageNumber($iPage);
    		$paginator->setItemCountPerPage($iPageSize);
    	
    		Zend_Paginator :: setDefaultScrollingStyle('Sliding');
    		Zend_View_Helper_PaginationControl :: setDefaultViewPartial('/pagination.phtml');
    	
    		$this->view->AccountInfos = $arrAccountAdminInfos;
    		//Assign view paging
    		$this->view->paginator = $paginator;
    		$this->view->page = $iPage;
    		$this->view->groupid = $iGroupId;
    		$this->view->isadmin = $isAdmin;
    		
    	}
    }
    
    public function invitememberAction(){
    	if($this->_request->isPost())
    	{
    		global $globalConfig;

    		$params = $this->_request->getPost();
            $params = json_decode($params['data'], true);
            $iGroupId = $params['groupid'];
            $arrAccountIds = array_unique($params['account_ids']);
    		$arrError = array();


            $group = Group::getInstance()->getGroupByID($iGroupId);


            if (empty($group))
                $arrError = array('error' => true, 'message' => 'Group not exists.');
            else{
                if ($group['group_type'] != 0 && $group['group_type'] < 4) {
                    $arrError = array('error' => true, 'message' => 'Support group type "Other" only.');
                }
                else if($this->arrLogin['accountID'] != $group['manager_id'] && $this->arrLogin['accountID'] != $group['admin_id'])
                    $arrError = array('error' => true, 'message' => 'access is denied.');
            }


            if(!is_array($arrAccountIds))
                $arrError = array('error' => true, 'message' => 'data invalid.');

            if (!empty($arrError)) {
                echo Zend_Json::encode($arrError);
                exit();
            }



            foreach ($arrAccountIds as $id){
                //insert group invite with type is request (0)
                $arrData = array();
                $arrData['group_id'] = $iGroupId;
                $arrData['account_from'] = $this->arrLogin['accountID'];
                $arrData['account_to'] = $id;
                $arrData['is_pending'] = 1;
                $arrData['content'] = '';
                $arrData['type'] = $globalConfig['group_request']['invite'];
                $arrData['group_name'] = $group['group_name'];
                GroupInvitation::getInstance()->insertGroupInvitation($arrData);
                $arrError = array('error' => false, 'message' => 'Your Request Has Been Sent Successfully.', 'is_public' => false);

            }
    	}else
            $arrError = array('error'=>true,'message'=>'post support only.');

        echo Zend_Json::encode($arrError);
        exit();
    }
    
    public function checkgroupmemberexistAction(){
    	
    	if($this->_request->isPost())
    	{ 
    		$params = $this->_request->getPost();
    		
    		$iAccountId = $params['m_id'];
    		$iGroupId = $params['g_id'];
    		
    		if(is_numeric($iAccountId) && is_numeric($iGroupId)){
    			//check member is exists in groupmbmer
    			$arrGroupMember = GroupMember::getInstance()->getGroupMemberByAccountAndGroupId($iAccountId, $iGroupId);
    			if(!empty($arrGroupMember)){
    				$output = array('error' => 1, 'message' => 'Member is already exists.');
    				echo Zend_Json::encode($output);
    				exit();
    			}else{
    				$output = array('error' => 0, 'message' => '');
    				echo Zend_Json::encode($output);
    				exit();
    			}
    		}else{
    			if(!empty($arrGroupMember)){
    				$output = array('error' => 1, 'message' => 'Data invalid.');
    				echo Zend_Json::encode($output);
    				exit();
    			}
    		}
    	}
    	//getGroupMemberByAccountAndGroupId
    }
    
    /**
     * 
     */
    public function backFolderAction(){
  
    	if($this->_request->isPost()) 
    	{
    		$this->_helper->layout()->disableLayout();
    
    		$params = $this->_request->getPost();
    		$iGroupId = empty($params['g']) ? 0 : $params['g'];
    		$iParent = !empty($params['p']) ? $params['p'] : 0;

    		if($iGroupId > 0){

    			//get group
    			$arrGroup = Group::getInstance()->getGroupByID($iGroupId);

    			if(empty($arrGroup)){
    				$output = array('error' => 1, 'message' => 'Group Name is not exists.');
    				echo Zend_Json::encode($output);
    				exit();
    			}
    		
    			//check permision user in group
    			$groupMember = GroupMember::getInstance()->getGroupMemberByAccountAndGroupId($this->arrLogin['accountID'], $iGroupId);
    		
    			if(empty($groupMember) && $arrGroup['admin_id'] != $this->arrLogin['accountID']){

    				$output = array('error' => 1, 'message' => 'Permission deny.');
    				echo Zend_Json::encode($output);
    				exit();
    			}
    		
    			//back -1 level
    			//get file by id
    			$backFile = File::getInstance()->selectOne($iParent);
    			
    			if($iParent > 0 && empty($backFile)){
    				$output = array('error' => 1, 'message' => 'Group Name is not exists.');
    				echo Zend_Json::encode($output);
    				exit();
    			}
    			
    			$iParent = $backFile['parent'];

    			$output = array('error' => 0, 'message' => 'Success.', 'p' => $iParent);
    			echo Zend_Json::encode($output);
    			exit();
    		
    		}else{
    			$output = array('error' => 1, 'message' => 'Group Name is not exists.');
    			echo Zend_Json::encode($output);
    			exit();
    		}
    		
    	}
    }
    
    public function documentAction(){

    	$iPage = $this->_request->getParam('page', 1);
    	if($iPage < 1){
    		$iPage = 1;
    	}
    	$iGroupId = $this->_request->getParam('g', 0);
    	$iParent = $this->_request->getParam('p', 0);
    			
    	$iLimit = ADMIN_PAGE_SIZE;   
    	//$iLimit = 2;
    	$iStart = ($iPage - 1) * $iLimit;
    	
    	$arrFiles = array();
    	$arrGroup = array();
    	$pathFolder = '/';
    	$backFile = array();
    	
    	if($iGroupId > 0){
    		
    		//get group 
    		$arrGroup = Group::getInstance()->getGroupByID($iGroupId);

    		if(empty($arrGroup)){
    			exit("Group is not exists.");
    		}

    		//check permision user in group
    		$groupMember = GroupMember::getInstance()->getGroupMemberByAccountAndGroupId($this->arrLogin['accountID'], $iGroupId);

    		if(empty($groupMember) && $arrGroup['admin_id'] != $this->arrLogin['accountID']){
    			exit("Permission deny.");
    		}
    		
    		//get list file of group
    		if($iParent == 0){
    			$arrFiles = File::getInstance()->selectByGroupId($iStart, $iLimit, $iGroupId);
    		}else{
    			$arrFiles = File::getInstance()->selectByParent($iStart, $iLimit, $iParent);
    		}
    		
    	}else{
    		exit("Group is not exits.");
    	}
    	
    	//show path
    	if($iParent > 0){
    		if($arrFiles['total'] == 0){
    			$backFile = File::getInstance()->selectOne($iParent);
    		}else{
    			foreach ($arrFiles['data'] as $key => $file){
    				$backFile = $file;
    				break;
    			}
    		}
    	}
//        Core_Common::var_dump($arrFiles,false);




    	$pathFolder = $this->showPath($arrFiles, $backFile);
        $nameFolders = explode('/',$pathFolder);
        $nameFolder = (count($nameFolders) > 1) ? end($nameFolders) : '';

        $groupType = '';
        isset($arrGroup['group_type']) && $groupType = $arrGroup['group_type'];
        
    	$this->view->data = $arrFiles['data'];
    	$this->view->iTotal = $arrFiles['total'];
    	$this->view->iTotalPage = ceil($arrFiles['total'] / $iLimit);
    	$this->view->iPageSize = $iLimit;
    	$this->view->iPage = $iPage;  	
    	   	
    	$this->view->arrFiles = $arrFiles;
    	$this->view->groupId = $iGroupId;
    	$this->view->parent = $iParent;
    	$this->view->arrGroup = $arrGroup;
    	$this->view->pathFolder = $pathFolder;
    	$this->view->nameFolder = $nameFolder;
    	$this->view->groupType = $groupType;
    }
    
    /**
     * get my group
     */
    public function getMyGroupAction(){
    	
    	$arrDatas = array();
    	
    	$arrMyGroups = GroupMember::getInstance()->getGroupMemberByMemberId($this->arrLogin['accountID'], 0, MAX_QUERY_LIMIT);
    	
    	if(empty($arrMyGroups)){
    		$output = array( 'error' => 1, 'message' => 'Please join a Team');
    		echo Zend_Json::encode($output);
    		exit();
    	}
    	
    	
    	foreach ($arrMyGroups['data'] as $group){
    		$tmp = array();
    		$tmp['group_id'] = $group['group_id'];
    		$tmp['group_name'] = $group['group_name'];
    		$arrDatas[] = $tmp;
    	}
    	
    	$output = array('error' => 0, 'data' => $arrDatas);
    	echo Zend_Json::encode($output);
    	exit();
    	
    }
    
    public function personalDocumentAction(){
    
    	$iStart = $this->_request->getParam('page', 0);
    	$iLimit = $this->_request->getParam('limit', 10);
    	
    	
    	$iGroupId = $this->_request->getParam('g', 0);
    	$iParent = $this->_request->getParam('p', 0);

    	$arrFiles = array();
    	$arrTmps = array();
        if($iParent ==  0 && $iGroupId > 0) {

            //get group
            $arrGroup = Group::getInstance()->getGroupByID($iGroupId);

            if (empty($arrGroup)) {
                $output = array('error' => 1, 'message' => 'Group is not exists.');
                echo Zend_Json::encode($output);
                exit();
            }

            //check permision user in group
            $groupMember = GroupMember::getInstance()->getGroupMemberByAccountAndGroupId($this->arrLogin['accountID'], $iGroupId);

            if (empty($groupMember) && $arrGroup['admin_id'] != $this->arrLogin['accountID']) {
                $output = array('error' => 1, 'message' => 'Permission deny.');
                echo Zend_Json::encode($output);
                exit();
            }

            $arrTmps = File::getInstance()->selectByGroupId($iStart, $iLimit, $iGroupId);

        }else
            $arrTmps = File::getInstance()->selectByParent($iStart, $iLimit, $iParent);


    	if($arrTmps['total'] > 0){
    		foreach ($arrTmps['data'] as $file){
    			$arr = $file;
    			if($file['type'] == FILE){
                    $processFileName = Core_Common::getExtensionAndFileName( $file['name']);
                    $ext = $processFileName['extent'];
    				$arr['ext'] = strtolower($ext);
    			}
    			$arrFiles[] = $arr;
    		}
    	}
    	
    	$output = array( 'start'=> $iStart, 'limit' => $iLimit, 'total' => $arrTmps['total'], 'data' => $arrFiles);
    	echo Zend_Json::encode($output);
    	exit();
    	
    }
    
    public function getDocumentByIdAction(){

        $sPathUpload = PATH_FILES_UPLOAD_DIR;
    	$id = $this->_request->getParam('id', 0);

    	$file = File::getInstance()->selectOne($id);

        if(empty($file)) {
            $arrError = array('error' => true, 'message' => 'file or folder not exits');
            echo Zend_Json::encode($arrError);
            exit();
        }

        $pathItem =  $sPathUpload.$file['path'].'/'.$file['name'];
    	if(!file_exists($pathItem))
        {
            File::getInstance()->delete($id);
            $arrError = array('error'=>true,'message'=>'file or folder not exits');
        }else
            $arrError = array('error'=>false,'message'=>'success','file'=>$file);
    	 

    	echo Zend_Json::encode($arrError);
    	exit();
    	 
    }
    
    /**
     * get team id
     * return int
     */
    public function getTeamIdAction(){
    	$accountId = $this->arrLogin['accountID'];
    	$arr = AccountInfo::getInstance()->getAccountInfoByAccountID($accountId);
    	
    	$iTeamId = 0;
    	
    	if(!empty($arr)){
    		$iTeamId =  $arr['team_id'];
    	}
    	$output = array( 'team_id'=> $iTeamId);
    	echo Zend_Json::encode($output);
    	exit();
    }
    
    /**
     * add file to feed
     * @param array file ids
     * return true or false
     */
    public function addFileToFeedAction(){
    	if($this->_request->isPost())
    	{
    		$this->_helper->layout()->disableLayout();
    	
    		$params = $this->_request->getPost();
	    	$arrDatas = json_decode($params['data'], true);
	    	
	    	

	    	if(!empty($arrDatas['file_ids'])){

	    		//check feed
                $feedId = intval($arrDatas['feed_id']);
//	    		$arrFeed = Feed::getInstance()->getFeedById($feedId);

//	    		if(empty($arrFeed)){
//                    Core_Common::var_dump($arrFeed);
//	    			$output = array( 'error' => 1);
//	    			echo Zend_Json::encode($output);
//	    			exit();
//	    		}
	    		
		    	File::getInstance()->addFileToFeed($feedId, $arrDatas['file_ids']);
		    	 
		    	
		    	$output = array( 'error' => 0);
		    	echo Zend_Json::encode($output);
		    	exit();
	    	}
	    	
	    	$output = array( 'error' => 1);
	    	echo Zend_Json::encode($output);
	    	exit();
    	}
    	
    	
    }
    
    /**
     * delete document
     */
    public function deleteDocumentAction(){

    	if($this->_request->isPost())
    	{
            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $sPathUpload = PATH_FILES_UPLOAD_DIR;
    		$params = $this->_request->getPost();
    		$iFileId = empty($params['id']) ? 0 : $params['id'];
            $arrFile = File::getInstance()->selectOne($iFileId);
    		if(!empty($arrFile)){
    			
    			$arrFile = File::getInstance()->selectOne($iFileId);
                $pathItem =  $sPathUpload.$arrFile['path'].'/'.$arrFile['name'];
    			if(empty($arrFile)){
    				$output = array('error' => 1, 'message' => 'Folder is not exists.');
    				echo Zend_Json::encode($output);
    				exit();
    			}
    			
    			//check permission
    			if($arrFile['owner'] != $this->arrLogin['accountID']){
    				$output = array('error' => 1, 'message' => 'Permission deny.');
    				echo Zend_Json::encode($output);
    				exit();
    			}
    			
    			if($arrFile['type'] == FOLDER){
                    $files = File::getInstance()->select(0, MAX_QUERY_LIMIT, '', array(), (int)$iFileId, 0, 0);
//                    Core_Common::var_dump($files);
                    if (!empty($files) && $files['total']>0) {
                        $this->deleteChildDocument($files);
                        File::getInstance()->delete($iFileId);
                        if (is_dir($pathItem)) {
                            rmdir($pathItem);
                        }
                    }
                    else
    				    File::getInstance()->delete($iFileId);
    			}else{

                    unlink($pathItem);
    				File::getInstance()->delete($iFileId);
    			}
    			
    			$output = array('error' => 0, 'message' => 'Success!', 'g' => $arrFile['group_id'], 'p'=> $arrFile['parent']);

    		}else
                $output = array('error' => 0, 'message' => 'File not found');
    	}else
            $output = array('error' => 1, 'message' => 'post support only');

        echo Zend_Json::encode($output);
        exit();
    	
    }

    public function deleteChildDocument($files = array())
    {
        $sPathUpload = PATH_FILES_UPLOAD_DIR;
        if(empty($files['data'])){
            return false;
        }

        foreach($files['data'] as $file)
        {
            $pathItem =  $sPathUpload.$file['path'].'/'.$file['name'];
            if($file['type'] == FOLDER){
                $childs = File::getInstance()->select(0, MAX_QUERY_LIMIT, '', array(), (int)$file['_id'], 0, 0);
                $this->deleteChildDocument($childs, $file['_id']);
                if (is_dir($pathItem)) {
                    rmdir($pathItem);
                }
            }else{

                unlink($pathItem);
                File::getInstance()->delete($file['_id']);
            }
        }


    }

    /**
     * if type is folder then create a new folder else create a new file
     */
    public function createFolderAction(){

    	if($this->_request->isPost())
    	{
    		$this->_helper->layout()->disableLayout();
    
    		$params = $this->_request->getPost();
    		$iGroupId = empty($params['g']) ? 0 : $params['g'];
    		$iParent = !empty($params['p']) ? $params['p'] : 0;
    		$sName = isset($params['f']) ? $params['f'] : '';
            $sOriginalName = isset($params['original_name']) ? $params['original_name'] : '';
    		$iType = isset($params['t']) ? $params['t'] : FOLDER;
    		$sPath = isset($params['path']) ? $params['path'] : '';
    		$sPathFolder = '';
    		
    		$arrGroup = Group::getInstance()->getGroupByID($iGroupId);
    		
    		//validate
    		if(empty($arrGroup)){
    			$output = array('error' => 1, 'message' => 'Group Name is not exists.');
    			echo Zend_Json::encode($output);
    			exit();
    		}
    		
    		if(empty($sName)){
    			$output = array('error' => 1, 'message' => 'Please enter the name.');
    			echo Zend_Json::encode($output);
    			exit();
    		}
    		
    		//check path 
    		if($iType == FILE && !file_exists($sPath)){
    			$output = array('error' => 1, 'message' => 'Upload File error!');
    			echo Zend_Json::encode($output);
    			exit();
    		}
    
    		//check permision user in group
    		$groupMember = GroupMember::getInstance()->getGroupMemberByAccountAndGroupId($this->arrLogin['accountID'], $iGroupId);
    		if(empty($groupMember) && $arrGroup['admin_id'] != $this->arrLogin['accountID']){
    			$output = array('error' => 1, 'message' => 'Permission deny.');
    			echo Zend_Json::encode($output);
    			exit();
    		}
    		
    		//check exist folder in db
    		$arrFile = File::getInstance()->selectOneByNameAndTypeAndGroup($sName, $iType, $iGroupId, $iParent);
    		if(!empty($arrFile)){
    			$output = array('error' => 1, 'message' => 'You cannot save the folder. This node does not accept two nodes with the same name.');
    			echo Zend_Json::encode($output);
    			exit();
    		}
    		
    		//check parent
    		if($iParent > 0){
    			$arrFile = File::getInstance()->selectOne($iParent);

    			if(empty($arrFile)){
    				$output = array('error' => 1, 'message' => 'Folder is not exists.');
    				echo Zend_Json::encode($output);
    				exit();
    			}
    			
    			//check owner
    			if($arrFile['owner'] != $this->arrLogin['accountID']){
    				$output = array('error' => 1, 'message' => "Permission deny. Please choose your folder.");
    				echo Zend_Json::encode($output);
    				exit();
    			}
    			
    			//set path
    			$sPathFolder = $arrFile['path'];
    			if($arrFile['type'] == FOLDER){
    				$sPathFolder = $sPathFolder .'/'.$arrFile['name'];
    			}
    			
    		}else if ($iParent < 0){
    			$output = array('error' => 1, 'message' => 'Folder is not exists.');
    			echo Zend_Json::encode($output);
    			exit();
    		}else{
    			$sPathFolder = '/'.$arrGroup['group_id'];
    		}
    		
    		//create folder in server
    		Core_Common::createFolder(PATH_FILES_UPLOAD_DIR .'/'.$arrGroup['group_id']);
    		
    		if($iType == FOLDER){//create folder
    			Core_Common::createFolder(PATH_FILES_UPLOAD_DIR .$sPathFolder.'/'.$sName);
    		}else{//create file
    			
    			//user must create a folder before upload
    			if($iParent == 0){
    				$output = array('error' => 1, 'message' => 'Please select or create a folder before upload files.');
    				echo Zend_Json::encode($output);
    				exit();
    			}
    			
    			if(copy($sPath, PATH_FILES_UPLOAD_DIR . $sPathFolder.'/'.$sName)){
    				unlink($sPath);
    			}
    			else {
    				$output = array('error' => 1, 'message' => 'Upload File error.');
    				echo Zend_Json::encode($output);
    				exit();
    			}
    		}

            //insert file
            $iFileId = File::getInstance()->insert($sName, $sPathFolder, $iType, $iParent, $this->arrLogin['accountID'], $iGroupId, 0, $sOriginalName);

            if ($iFileId > 0) {
                $output = array('error' => 0, 'message' => 'Success!');
            } else {
                $output = array('error' => 1, 'message' => 'Create Failed.');
            }

            echo Zend_Json::encode($output);
            exit();
    	}
    	 
    }
    
    private function getMyGroupIds($arrMyGroups){
    	$arrMyGroupIds = array();
    	
    	foreach ($arrMyGroups as $group){
    		$arrMyGroupIds[] = $group['group_id'];
    	}
    	
    	return $arrMyGroupIds;
    }
    
    private function getGroupRequestIds($iAccountId){
    	
    	global $globalConfig;
    	$arrGroupIds = array();
    	 
    	$arrGroupTmps = GroupInvitation::getInstance()->getGroupInviteationRequestByAccountFrom($iAccountId
    	, $globalConfig['group_request']['request']);
    	
    	if(!empty($arrGroupTmps) && $arrGroupTmps['total'] > 0){
	    	foreach ($arrGroupTmps['data'] as $group){
	    		$arrGroupIds[] = $group['group_id'];
	    	}
    	}
    	return $arrGroupIds;
    }
    
    private function getSuggestedGroups($arrGroups, $arrMyGroupIds, $iAccountId){
    	
    	if(empty($arrGroups)){
    		return array();
    	}
    	
    	$arrSuggestedGroups = array();
    	$arrSuggestedGroupIds = array();
    	$arrAccountAdminIds = array();
    	$total = 0;
    	
    	$arrGroupPendingIds = $this->getGroupRequestIds($iAccountId);
    	
    	foreach ($arrGroups as $group){
    		
    		if (!in_array($group['group_id'], $arrMyGroupIds)) {
    			
    			$groupTmp = array();
    			$groupTmp = $group;
    			
    			//check group pending
    			if(in_array($group['group_id'], $arrGroupPendingIds)){
    				$groupTmp['is_pending'] = 1;
    			}else{
    				$groupTmp['is_pending'] = 0;
    			}
    			
    			$arrSuggestedGroups[] = $groupTmp;
    			$arrSuggestedGroupIds[] = $group['group_id'];
    			$arrAccountAdminIds[] = $group['admin_id'];
    			$total++;
    		}

    	}
    	
    	if(empty($arrSuggestedGroups) || empty($arrSuggestedGroupIds) || empty($arrAccountAdminIds)){
    		return array();
    	}
    	
    	return array('suggestedgroup' => $arrSuggestedGroups,
    				  'suggestedgroupid' => $arrSuggestedGroupIds,
    				  'accountadminid' => $arrAccountAdminIds,
    				  'total' => $total);
    	
    }
    
    /**
     * 
     * @param unknown $arrGroups
     * @param unknown $iAccountId
     * @return multitype:|multitype:multitype:multitype:  multitype:unknown
     */
    private function getMyGroupInfos($arrGroups, $iAccountId){
    	
    	

    	$arrResults = array();
    	$arrAccountAdminIds = array();
    	$total = 0;
    	
    	foreach ($arrGroups as $group){
    
    		
    			 
    			$groupTmp = array();
    			$groupTmp = $group;
    			 
    			//check is admin
    			if($group['admin_id'] == $iAccountId){
    				$groupTmp['is_admin'] = 1;
    			}else{
    				$groupTmp['is_admin'] = 0;
    			}
    			
    			$arrResults[] = $groupTmp;
    			$arrAccountAdminIds[] = $group['admin_id'];
    			$total++;
    		
    
    	}
    	 
    	if(empty($arrResults) || empty($arrAccountAdminIds)){
    		return array('myGroup' => $arrResults, 'accountAdminId' => $arrAccountAdminIds);
    	}
    	 
    	return array('myGroup' => $arrResults, 'accountAdminId' => $arrAccountAdminIds);
    	 
    }
  
    private function showPath($arrFiles, $arrBackFile){
    	if(!empty($arrFiles) && $arrFiles['total'] > 0){
    		foreach ($arrFiles['data'] as $key =>$file){
    			if($file['parent'] == 0){
    				return str_replace($file['group_id'],"",$file['path']);
    			}else{
    				return str_replace('/'.$file['group_id'],"",$file['path']);
    			}
    			
    		}
    		 
    	}else{
    		if(!empty($arrBackFile)){
    			return str_replace('/'.$arrBackFile['group_id'],"",$arrBackFile['path'] . '/'. $arrBackFile['name']);
    		}
    	}
    	return '/';
    }
    
    public function calendarAction()
    {
    	$iGroupId = $this->_request->getParam('groupid', 0);
    	$groupType = $this->_request->getParam('group-type', 0);
    	
    	$arrGroup = Group::getInstance()->getGroupByID($iGroupId);
        $arrGroup = Core_Common::groupProcess($arrGroup);
    	$this->view->groupid = $iGroupId;
    	$this->view->arrGroup = $arrGroup;

    	//set group name to view
		$groupName = '';
		isset($arrGroup['group_name']) && $groupName = $arrGroup['group_name'];
		
		$tempArrgroupName = array (
			$iGroupId => array (
				'group_name' => $groupName
			)
		);
    	$this->view->arrGroupList = $tempArrgroupName;
    	$this->view->groupType = $groupType;
    }
}

