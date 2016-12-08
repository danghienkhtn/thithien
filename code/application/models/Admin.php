<?php

/**
 * @author      :   HienND
 * @name        :   Admin model
 * @version     :   201611
 * @copyright   :   DAHI
 * @todo        :   Admin Model
 */
class Admin {

    /**
     * Parent instance
     * @var <object>
     */
    private $_modeParent= null;

    /**
     *
     * @var <type>
     */
    protected static $_instance = null;

    //key cache  
    const ADMIN_DETAIL_KEY = 'admin_detail_key';
    const ADMIN_DETAIL_EXPIRED = 'admin_detail_expired';
    
    /**
     * Constructor of class
     * we don't permit an explicit call of the constructor! (like $v = new Singleton())
     */
    protected function __construct() {
        //Init joined fan
         $this->_modeParent = Core_Business_Api_Admin::getInstance();        
    }

    /**
     * Get singletom instance
     * @return <object>
     */
    public final static function getInstance() {
        //Check instance
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        //Return instance
        return self::$_instance;
    }

   
    /**
     * check is Fan of an Object
     * @param <string> $sObjectID
     * @param <string> $sUserID
     * @return <int> $bIsFan
     */
    public function isAdmin($sUserID) 
    {
        // Generate key
        $arrAdmin= $this->getAdminByID($sUserID);
       
        if(!empty($arrAdmin))
        {
            return 1;
        }
        
        return 0;
        
    }
    
 
    /**
     * check is Fan of an Object
     * @param <string> $sObjectID
     * @param <string> $sUserID
     * @return <int> $bIsFan
     */
    public function getPermission($sUserID) 
    {
        $iPermission =0;
        $arrAdmin= $this->getAdminByID($sUserID);
        if(!empty($arrAdmin))
        {
              return $arrAdmin['permission'];
        }
        
        return $iPermission;
    }
    
    
    
    public function getPermissionAccessMenu($myPermission = array()) {
    
    	global $adminConfig;
    	$fullPermission = $adminConfig['permission'];
        $fullPermission = (!is_null($fullPermission)) ? $fullPermission : array();
    	$data = array();

    	foreach ($fullPermission as $controller => $permission) {
    		if (!empty($myPermission[$controller]) && $permission['visible']) {
    
    			$data[$controller]['icon'] = $permission['icon'];
    			$data[$controller]['name'] = $permission['name'];
    			$data[$controller]['type'] = $permission['type'];//pages or tools
    			
    			$actions = array();
    
    			foreach ($permission['action'] as $action => $per) {
    				if ($per['visible']) {
    					if ($myPermission[$controller] & intval($per['value'])) {
    						
    						$param = isset($per['param']) ? '?'. $per['param'] : '';//querystring
    						$actions[$action]['name'] = $per['name'];
    						$actions[$action]['link'] = '/' . $controller . '/' . $action . $param;
    					
    					}
    				}
    			}
    
    			if (empty($actions)) {
    				unset($data[$controller]);
    			} elseif (count($actions) == 1 && key($actions) == 'index') {
    				$data[$controller]['link'] = $actions[key($permission['action'])]['link'];
    			} elseif (count($actions) >= 1) {
    				$data[$controller]['action'] = $actions;
    			}
    		}
    	}

    	return $data;
    }
    
    public function checkLogin() {
    
    	$sAuthToken = Core_Cookie::getCookie(AUTH_LOGIN_TOKEN);
    
    	if(empty($sAuthToken)) {
    		return false;
    	}
    
    	$accountId = isset($_SESSION[$sAuthToken]['accountID']) ? $_SESSION[$sAuthToken]['accountID'] : 0;
    
    	if(empty($accountId) || !is_numeric($accountId)) {
    
    		return false;
    	}
    
    	$data = $this->getAdminByID($accountId);
    
    	if(empty($data)) {
    		return false;
    	} else {
    		return $data;
    	}
    
    
    }
    
    /**
     * get login
     */
    
    public function getLogin($sAuthToken = '')
    {
        $sAuthToken = trim($sAuthToken);
          //Check cookie data
        if(empty($sAuthToken)) {
            $sAuthToken = Core_Cookie::getCookie(AUTH_LOGIN_TOKEN);
        }

        //Check token
        if (empty($sAuthToken)) {
            return false;
        }

        /* Session expired*/
        if(!isset($_SESSION[$sAuthToken]))
        {
            return false;
        }
        //Return data
        return $_SESSION[$sAuthToken];

    }
    
    public function setLogin($sAuthToken, $iAccountID)
    {

        $accountInfo = AccountInfo::getInstance()->getAccountInfoByAccountID($iAccountID);
        $_SESSION[$sAuthToken] = array(
            'account_id'      => $iAccountID,
        	// 'id'			 => $iID,
            'name'       => $accountInfo['name'],
            'avatar'         => Core_Common::avatarProcess($accountInfo['avatar']),
            'token'          => $sAuthToken,
            'is_admin'     => $accountInfo['is_admin'],
            'email'          => $accountInfo['email'],
            'email1'   => $accountInfo['email1'],
            'username'   => $accountInfo['username'],
            'last_login_date'   => $accountInfo['last_login_date'],
            'update_date'   => $accountInfo['update_date'],
            'active'   => $accountInfo['active'],
            // 'avatar'   => $accountInfo['avatar'],
            'ps'             => $accountInfo['ps']
        );


//        Core_Common::var_dump($_SESSION[$sAuthToken],false);
//        echo 'setLogin '.$sAuthToken.'<br/>';
//       Core_Common::var_dump($this->getLogin());
        
        
    }

    public function resetLogin($arrLogin, $accountInfo)
    {
//        if(!isset($accountInfo['account_id']))
//            return false;
//
//        $this->setLogin($arrLogin['token'], $arrLogin['accountID'], $accountInfo['id'],
//            $accountInfo['name'], $accountInfo['email'], $accountInfo['personal_email'],
//            $arrLogin['ps'], Core_Common::avatarProcess($accountInfo['picture']) );

        return true;
    }
 
    /**
     * @todo  Remove GiveAway
     * @return <int>
     */
    public function removeAdmin($iUserID)
    {
        //Get data
        $arrResult = $this->_modeParent->removeAdmin($iUserID);
        
        if($arrResult > 0){
        	Core_Common::clearCache(self::ADMIN_DETAIL_KEY, $iUserID);
        }
        
        //Return result data
        return $arrResult;
    }
    
    /**
     * update Admin info
     * @param array $arrAdmin
     * @return boolean
     */
    public function updateAdmin($arrAdmin) {
    	
    	$result = $this->_modeParent->updateAdmin($arrAdmin);
    	 
    	if($result > 0){
    		Core_Common::clearCache(self::ADMIN_DETAIL_KEY, $arrAdmin['account_id']);
    	}
    	 
    	return $result;
    }
    
    /**
     * delete admin by admin_id
     * @param int $iAdminID
     * @return 0 or 1
     */
    public function deleteAdminByAdminID($iAdminID, $iAccountID) {
    	
    	$result = $this->_modeParent->deleteAdminByAdminID($iAdminID);
    	
    	if($result > 0){
    		Core_Common::clearCache(self::ADMIN_DETAIL_KEY, $iAccountID);
    	}
    	
    	return $result;
    }
    
    /**
     * get admin list
     * @param string $sName
     * @param string $sRoleID
     * @param int $iOffset
     * @param int $iLimit
     * @return array('total' => int, 'data' => array)
     **/
    public function select($sName, $sRoleID, $iOffset, $iLimit) {
    	return  $this->_modeParent->select($sName, $sRoleID, $iOffset, $iLimit);
    }
    
    /**
     *
     * @param int $iAdminID
     * @return array
     */
    public function getAdminByAdminID($iAdminID) {
    	return  $this->_modeParent->getAdminByAdminID($iAdminID);
    }
     /**
     * @todo Get Admin by AccountId
     * @return array(account_id, GROUP_CONCAT(role_id))
     */
    public function getAdminByID($iAccountID)
    {
    	$keyCaching = Core_Global::getKeyPrefixCaching(self::ADMIN_DETAIL_KEY) . $iAccountID;
    	$caching = Core_Global::getCacheInstance();

    	//Get data from caching
    	$arrResult = $caching->read($keyCaching);

    	if (!$arrResult) {
    		 
    		$arrResult = $this->_modeParent->getAdminByID($iAccountID);
//            Core_Common::var_dump($iAccountID);
    		if (!empty($arrResult)) {
    			 
    			$time = Core_Global::getKeyPrefixCaching(self::ADMIN_DETAIL_EXPIRED);
    			$caching->write($keyCaching, $arrResult, $time);
    		}
    	}
    	 
    	return $arrResult;
        
    }
    
     /**
      * insert user to admin
      * @author Le Thanh Tai <tai.lt@vn.gnt-global.com>
      * @param int $userInfo
      * @return int  Returns number > 0 on success or return number < 0 on failure
      */
    public function insertAdmin($userInfo) 
    {
        return $this->_modeParent->insertAdmin($userInfo);
    }
    
    /**
     * check permission
     * @param array $myPermission
     * @param string $controller
     * @param string $action
     * @return boolean
     */
    public function isAlow($myPermission,  $controller = "", $action = "") {
    
    	global $adminConfig;
    	$fullPermission = $adminConfig['permission'];
    
    	//check permission
    	if (!empty($myPermission) && isset($fullPermission[$controller]['action'][$action]['value'])) {
    		
    		$value = $fullPermission[$controller]['action'][$action]['value'];
    
    		if (!empty($myPermission) && !empty($myPermission[$controller])) {
    			if ($myPermission[$controller] & $value) {
    				return true;
    			}
    		}
    	}
    
    	return false;
    }
    
}

