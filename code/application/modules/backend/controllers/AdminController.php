<?php
/**
 * @author      :   HoaiTN
 * @name        :   IndexController
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   controller default 
 */
class Backend_AdminController extends Core_Controller_ActionBackend
{
	
	public function init()
	{
		parent::init();
		$this->arrLogin     = $this->view->arrLogin;
	}
	
	public function indexAction(){
		//get Role list
		$arrRole = $this->getIdNameRole();
		$this->view->arrRole = $arrRole;
	}
	
	/**
	 * get list Admin
	 */
	public function listAction()
	{		
		$this->_helper->layout()->disableLayout();
	
		$draw =  $this->_getParam('draw',0);
		$limit  = $this->_getParam('length', ADMIN_PAGE_SIZE);
		$offset = $this->_getParam('start',0);
	
		$search = $this->_getParam('search',array());
        $sName    = isset($search['value']) ? $search['value'] : '';

        $columns = $this->_getParam('columns',array());
        $sRoleID = isset($columns[0]['search']['value']) ? $columns[0]['search']['value'] : '';
		
		//get admin list
		$arrAdminInfos = Admin::getInstance()->select($sName, $sRoleID, $offset, $limit);
		
		//get Role list
		$arrRole = $this->getIdNameRole();
		
		// parse account to json
		$arrAccount = array();
		$result = array();

		if($arrAdminInfos)
		{
			foreach ($arrAdminInfos['data'] as $accountInfo) {
				
				$key = $accountInfo['admin_id'];
				// process account
				$account = Core_Common::accountProcess($accountInfo);
				$avatar = '<img src="' . $account['image_tag'] . '" width="45"/>';
				$actions = '';
				
				//check super admin
				if($accountInfo['super_admin'] != SUPER_ADMIN){
					$actions    =  ' <a href="'.BASE_ADMIN_URL.'/admin/edit?admin_id='.$accountInfo['admin_id'].'" data-action="group-edit" data-value="'.$key.'"><i class="fa fa-pencil-square-o"></i></a> ';
					$actions   .=  ' <a href="javascript:void(0);" data-action="group-delete" data-value="'.$key.'"><i class="fa fa-trash-o"></i></a> ';
				}
				
				$accountName = '<a href="' . BASE_ADMIN_URL . '/user/summary?account_id=' . $account['account_id'] . '">' . $account['name'] . '</a>';
				$account['first_name'] = '<a href="' . BASE_ADMIN_URL . '/user/summary?account_id=' . $account['account_id'] . '">' . $account['first_name'] . '</a>';
				$arrAccount[] = array('id' => $account['id']
						, 'firstName' => $account['first_name']
						, 'lastName' => $account['last_name']
						, 'name' => $accountName
						, 'email' => $account['email']
						, 'avatar' => $avatar
						, 'team' => $account['team_name']
						, 'roleName' => $this->getRoleName($arrRole, $accountInfo)
						, 'actions' => $actions);
			}
			$result = array('draw'=>$draw, 'recordsFiltered'=>$arrAdminInfos['total'],'recordsTotal'=>$arrAdminInfos['total'],'data'=>$arrAccount);
		}
		else
			$result = array('draw'=>$draw, 'recordsFiltered'=>0,'recordsTotal'=>0,'data'=>array());
	
		echo  Zend_Json::encode($result);
		exit();
	}
	
	/**
	 * delete admin
	 */
	public function deleteAction()
	{
	
		$this->_helper->layout()->disableLayout();
		$error  = array('error' => false, 'message' => '');
		
		if($this->getRequest()->isPost()) {

			$arrParam = $this->_request->getParams();
			$iAdminId = isset($arrParam['admin_id']) ? $arrParam['admin_id'] : 0;
			
			if(is_numeric($iAdminId)){
				
				//get Admin
				$arrAdmin = Admin::getInstance()->getAdminByAdminID($iAdminId);
				
				if(!empty($arrAdmin) && $arrAdmin['super_admin'] != SUPER_ADMIN){
					
					//delete admin by admin_id
					$result = Admin::getInstance()->deleteAdminByAdminID($arrAdmin['admin_id'], $arrAdmin['account_id']);
					
					if($result > 0){
						//write log
						ActionLog::getInstance()->insert($this->arrLogin['id'],ActionLog::$delete,ActionLog::$admin
							,$this->arrLogin['accountID'],$this->arrLogin['nickName'],$arrAdmin['account_id'].' admin');
					}else{
						$error  = array('error' => true, 'message' => 'Delete failed.');
					}
				}else{
					$error  = array('error' => true, 'message' => 'Admin is not exists.');
				}
				
			}
			
		}
	
		echo Zend_Json::encode($error);
		exit();
	}
	
    /**
     * manage Role
     */
	public function manageRoleAction(){
		
		global $adminConfig;
		
		$sError   = '';
		$sSuccess = '';
		
		//get root permission
		$arrFullPermission = $adminConfig['permission'];
		
		//get Role
		$arrRole = Role::getInstance()->select(0, MAX_QUERY_LIMIT);
		$arrRole = $arrRole['total'] > 0 && !empty($arrRole['data']) ? $arrRole['data'] : array();
		
		//get top 1 Role
		$arrOneRole = array();
		$sID = '';
		$iActive = 1;
		$arrPermission = array();
		
		foreach ($arrRole as $role){
			$arrOneRole = $role;
			break;
		}
		
		if($this->_request->isPost()){
			
			$params = $this->_request->getPost();
			$isChange = isset($params['is_change']) ? $params['is_change'] : 0;
			$sID = isset($params['role']) ? $params['role'] : '';
			$iActive = isset($params['active']) ? $params['active'] : 0;
			
			//select role
			if($isChange){
				foreach ($arrRole as $role){
					if($role["_id"] == $params['role']){
						$arrOneRole = $role;
						break;
					}
					
				}
			}else{//button submit is clicked
				
				$arrOneRole = array();
				$arrControllers = array_keys($arrFullPermission);
				
				//get permission
				$arrPermission = $this->getPermission($arrControllers, $arrPermission, $params);
				
				if($iActive != 0 && $iActive != 1){
					$sError .= 'Active invalid.<br/>';
				}

				if(empty($arrPermission)){
					$sError .= 'Please select permission.<br/>';
				}
				
				//get Role by ID
				$arrRoleTmp = Role::getInstance()->selectOne($sID);
				
				if(empty($arrRoleTmp)){
					$sError .= 'Role Name is not exists.';
				}
				
				//update Role
				if(empty($sError)){
					$isSuccess = Role::getInstance()->update($sID, $arrPermission, $iActive);
					
					if(!$isSuccess){
						$sError .= 'Save failed.';
					}else{
						$sSuccess = 'Save succeeded';
					}
				}
			}

		}
			
		if(!empty($arrOneRole)){
			$sID = $arrOneRole['_id'];
			$arrPermission = $arrOneRole['permission'];
			$iActive = $arrOneRole['active'];
		}

		
		$this->view->arrFullPermission = $arrFullPermission;
		$this->view->arrRole = $arrRole;
		$this->view->sID = $sID;
		$this->view->isActive = $iActive;
		$this->view->arrPermission = $arrPermission;
		$this->view->sError = $sError;
		$this->view->sSuccess = $sSuccess;
	}
	
	/**
	 * create a new Role
	 */
	public function createRoleAction(){
	
		global $adminConfig;
	
		$sError   = '';
		$sSuccess = '';
		
		//get root permission
		$arrFullPermission = $adminConfig['permission'];
		$arrPermission = array();
		$sName = '';
		
		if($this->_request->isPost()){
				
			$params = $this->_request->getPost();
	
			$sName = trim($params['name']);
			$iActive = $params['active'];
			$arrControllers = array_keys($arrFullPermission);
	
			//validate
			if(empty($sName)){
				$sError .= 'Role Name is required.<br/>';
			}else{//check duplicate role name
	
				$arrRole = Role::getInstance()->selectOneByName($sName);
				 
				if(!empty($arrRole)){
					$sError .= 'Role Name already is exist.<br/>';
				}
				 
			}
	
			//get permission
			$arrPermission = $this->getPermission($arrControllers, $arrPermission, $params);
	
			if(empty($arrPermission)){
				$sError .= 'Please select permission.<br/>';
			}		
			
			//insert Role
			if(empty($sError)){
				$sID = Role::getInstance()->insert($sName, $iActive, $arrPermission);
				
				if(!empty($sID)){
					$this->_redirect(BASE_ADMIN_URL.'/admin/manage-role');
				}
			}
	
		}
	
		$this->view->arrFullPermission = $arrFullPermission;
		$this->view->arrPermission = $arrPermission;
		$this->view->sName = $sName;
		$this->view->sError = $sError;
		$this->view->sSuccess = $sSuccess;
	
	}
	
	/**
	 * update role of Admin
	 */
	public function editAction(){
		
		$sError   = '';
		
		$iAdminId = $this->_request->getParam('admin_id', 0);
		
		if(!is_numeric($iAdminId)){
			$this->_redirect(BASE_ADMIN_URL.'/admin');;
		}
		
		//get admin 
		$arrAdmin = Admin::getInstance()->getAdminByAdminID($iAdminId);
			
		if(empty($arrAdmin)){//not exist Admin
			$this->_redirect(BASE_ADMIN_URL.'/admin');
		}
		
		//get AccountInfo
		$arrAccountInfo = AccountInfo::getInstance()->getAccountInfoByAccountID($arrAdmin['account_id']);
		
		if(empty($arrAccountInfo)){//not exist AccountInfo
			$this->_redirect(BASE_ADMIN_URL.'/admin');
		}
		
		//get Role
		$arrRole = Role::getInstance()->select(0, MAX_QUERY_LIMIT);
		$arrRole = $arrRole['total'] > 0 && !empty($arrRole['data']) ? $arrRole['data'] : array();
		
		if(empty($arrRole)){//not exist Role
			$this->_redirect(BASE_ADMIN_URL.'/admin');
		}
		
		if($this->_request->isPost()){
			
			$params = $this->_request->getPost();
			$sID = isset($params['role']) ? $params['role'] : '';
			
			$arrOneRole = Role::getInstance()->selectOne($sID);
			
			if(empty($arrOneRole)){
				$sError .= 'Role is not exist.';
			}
			
			$arrAdmin['role_id'] = $sID;
			
			if(empty($sError)){//update Admin Role
				$isSuccess = Admin::getInstance()->updateAdmin($arrAdmin);
				
				if(!$isSuccess){
					$sError .= 'Update failed.';
				}else{
					$this->_redirect(BASE_ADMIN_URL.'/admin');
				}
			}
			
		}
		
		$arrAccountInfo = Core_Common::accountProcess($arrAccountInfo);
		
		$this->view->arrRole = $arrRole;
		$this->view->arrAccountInfo = $arrAccountInfo;
		$this->view->arrAdmin = $arrAdmin;
		$this->view->sError = $sError;
	}
	
     /**
      * Add user to admin role
      * @author Le Thanh Tai <tai.lt@vn.gnt-global.com>
      * @return none
      */
    public function addAdminAction()
    {
        $rolesInstance  = Role::getInstance();
        $rolesList = $rolesInstance->select(0, MAX_QUERY_LIMIT);

        $arrRolesList = array();
        if (count($rolesList)) {
            $arrRolesList = $rolesList['data'];
        }

        $groupInstance = Group::getInstance();
        $arrGroup = $groupInstance->getGroupAll(0, MAX_QUERY_LIMIT);
//        Core_Common::var_dump($arrGroup);
//        !is_array($arrGroup) && $arrGroup = array();
        

        $this->view->arrRolesList = $arrRolesList;
        $this->view->arrGroup     = $arrGroup;
        
        if ($this->_request->isPost())
        {
            $accountId = (int)$this->_request->getPost('id');
            $roleId = $this->_request->getPost('role_id');
            if ($accountId === NULL || $roleId === NULL)
            {
                $this->_redirect(BASE_ADMIN_URL.'/admin');
            }
            
            $accountInfoInstance = AccountInfo::getInstance();
            $accountInfo = $accountInfoInstance->getAccountInfoByAccountID($accountId);
            // account not exist
            if (!isset($accountInfo['account_id'])) {
                $this->_redirect(BASE_ADMIN_URL.'/admin');
            }
            
            $roleInfo = $rolesInstance->selectOne($roleId);
            //role_id not exist
            if (!isset($roleInfo['_id'])) {
                $this->_redirect(BASE_ADMIN_URL.'/admin');
            }
            
            $adminInstance = Admin::getInstance();
            $arrAdmin = $adminInstance->getAdminByID($accountId);
            
            //account was added into admin
            if ($this->addedRole($arrAdmin, $roleId)) {
                $this->_redirect(BASE_ADMIN_URL.'/admin');
            }
            $userInfo = array(
                'account_id'       => $accountId,
                'role_id'          => $roleId,
                'super_admin'      => NORMAL_ADMIN,
                'active'           => ACTIVE_ADMIN_STATUS,
                'create_date'      => time(),
                'update_date'      => time()
            );
            $adminInstance->insertAdmin($userInfo);
            
            $this->_redirect(BASE_ADMIN_URL.'/admin');
        }
    }
    
     /**
      * Get team name by team id
      * @author Le Thanh Tai <tai.lt@vn.gnt-global.com>
      * @return json
      */
    public function getTeamNameByTeamIdAction()
    {
        
        $result = array(
            'total' => 0,
            'data'  => array()
        );
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        if(!$this->_request->isPost()) {
            return $result;
        }
        
        $teamId = $this->_request->getPost('team-id');
        $groupInstance = Group::getInstance();
        $arrGroup = $groupInstance->getGroupByID($teamId);

        $result = array(
            'total' => !is_array($arrGroup) ? 0 : count($arrGroup),
            'data'  => $arrGroup
        );
        echo json_encode($result);
        exit;
    }
    
    private function addedRole($arrAdmin, $roleId)
    {
    	$arrRoleIds = explode(',', $arrAdmin['role_id']);
    	
        foreach ($arrRoleIds as $id) {
            if ($id == $roleId) {
                return TRUE;
            }
        }
        RETURN FALSE;
    }
    
    /**
     * set permission
     * @param array $arrControllers
     * @param array $arrPermission
     * @param array $arrError
     * return  array;
     */
    private function getPermission($arrControllers, $arrPermission, $params){
    	
    	foreach ($arrControllers as $controller) {
    	
    		$value = 0;
    	
    		if (isset($params[$controller]) && !empty($params[$controller])) {
    	
    			foreach ($params[$controller] as $perValue) {
    				$value += $perValue;
    			}
    		}
    	
    		if($value > 0){
    			$arrPermission[$controller] = $value;
    		}
    	}
    	
    	return $arrPermission;
    }
    
    /**
     * get Id and Name of Role
     * @param array $arrRole
     * @return array(id => name)
     */
    private function getIdNameRole(){
    	
    	$arrResult = array();
    	//get Role list
    	$arrRole = Role::getInstance()->select(0, MAX_QUERY_LIMIT);
    	$arrRole = $arrRole['total'] > 0 && !empty($arrRole['data']) ? $arrRole['data'] : array();
    	
    	foreach ($arrRole as $role){
			$sId = (string)$role['_id'];
    		$arrResult[$sId] = $role['name'];
    	}
    	
    	return $arrResult;
    }
    
    /**
     * get Role Name
     * @param array $arrRole
     * @param array $arrAccount
     * @return string
     */
    private function getRoleName($arrRole, $arrAccount){
    	
    	if(empty($arrAccount)){
    		return '';
    	}
    	
    	if($arrAccount['super_admin'] == SUPER_ADMIN){
    		return 'Super Admin';
    	}
    	
    	return isset($arrRole[$arrAccount['role_id']]) ? $arrRole[$arrAccount['role_id']] : '';
    }
   
}

