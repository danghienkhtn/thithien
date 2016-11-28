<?php

/**
 * @author      :   Cang Ta
 * @name        :   Core_Business_Api_Event
 * @version     :   20130502
 * @copyright   :   My company
 * @todo        :   Using for event service
 */
class Core_Business_Api_Admin {

    /**
     *
     * @var <type>
     */
    protected static $_instance = null;

    /**
     * Constructor of class
     * we don't permit an explicit call of the constructor! (like $v = new Singleton())
     */
    protected function __construct() {
        //Nothing
    }

    /**
     * Get singletom instance
     * @return <object>
     */
    public final static function getInstance() {
        // check instance
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        // return instance
        return self::$_instance;
    }
    
    /**
     
     
     
    /**
     * @todo  Remove event
     * @param <int> $iEventId
     * @return <int>
     */
    public function removeAdmin($iUserID) {
        try {
            # Get Data Master Global
            $storage = Core_Global::getDbGlobalMaster();

            # Prepare store procude
            $stmt = $storage->prepare("CALL sp_admin_delete(:p_account_id, @p_RowCount)");
            $stmt->bindParam('p_account_id', $iUserID, PDO::PARAM_INT);
            $stmt->execute();

            # Fetch All Result
            $stmt = $storage->query("SELECT @p_RowCount");
            $result = $stmt->fetchColumn();

            # Free cursor
            $stmt->closeCursor();
        } catch (Exception $ex) {
            ErrorLog::getInstance()->insert(__CLASS__, __FUNCTION__, $ex->getMessage(), $iUserID);
            $result = -1;
        }

        // return data
        return $result;
    }

    /**
     * update Admin info
     * @param array $arrAdmin
     * @return boolean
     */
    public function updateAdmin($arrAdmin) {
    	
    	$result = -1;
    	
    	try {
    		# Get Data Master Global
    		$storage = Core_Global::getDbGlobalMaster();
    
    		# Prepare store procude
    		$stmt = $storage->prepare("CALL sp_admin_update(:p_admin_id, :p_role_id, :p_updated, @p_RowCount)");
    		$stmt->bindParam('p_admin_id', $arrAdmin['admin_id'], PDO::PARAM_INT);
    		$stmt->bindParam('p_updated', $arrAdmin['update_date'], PDO::PARAM_INT);
    		$stmt->bindParam('p_role_id', $arrAdmin['role_id'], PDO::PARAM_STR);
    		$stmt->execute();
    
    		# Fetch All Result
    		$stmt = $storage->query("SELECT @p_RowCount");
    		$result = $stmt->fetchColumn();
    
    		# Free cursor
    		$stmt->closeCursor();
    	} catch (Exception $ex) {
    		ErrorLog::getInstance()->insert(__CLASS__, __FUNCTION__, $ex->getMessage(), $arrAdmin['account_id']);
    		$result = -1;
    	}
    
    	// return data
    	return $result > -1 ? TRUE : FALSE;
    }
    
    /**
     * get admin list
     * @param string $sName
     * @param string $sRoleID
     * @param int $iOffset
     * @param int $iLimit
     * @return array('total' => int, 'data' => array)
     */
    public function select($sName, $sRoleID, $iOffset, $iLimit) {
    	 
    	$arrResult = array();
    	 
    	try {
    
    		# Get Data Master Global
    		$storage = Core_Global::getDbGlobalSlave();
    
    		# Prepare store procude
    		$stmt = $storage->prepare("CALL sp_admin_account_info_select(:p_name, :p_role_id, :p_offset, :p_limit, @p_RowCount)");
    		
    		$stmt->bindParam('p_name', $sName, PDO::PARAM_STR);
    		$stmt->bindParam('p_role_id', $sRoleID, PDO::PARAM_STR);
    		$stmt->bindParam('p_offset', $iOffset, PDO::PARAM_INT);
    		$stmt->bindParam('p_limit', $iLimit, PDO::PARAM_INT);
    		
    		$stmt->execute();
    
    		# Fetch All Result
    		$arrResult = $stmt->fetchAll();
    
    		# Free cursor
    		$stmt->closeCursor();
    
    		//Fetch Total Result
    		$stmt = $storage->query("SELECT @p_RowCount");
    
    		//Get total data
    		$iTotal = $stmt->fetchColumn();
    
    				//Free cursor
    				$stmt->closeCursor();
    
    				//Return data
    				$arrResult = array(
	    				'total' => $iTotal,
	    				'data' => $arrResult
    				);
    
    
    
    				# Free cursor
    				$stmt->closeCursor();
    	} catch (Exception $ex) {
    			ErrorLog::getInstance()->insert(__CLASS__, __FUNCTION__, $ex->getMessage());
    	}
    
    	// return data
    	return $arrResult;
    }
    
    /**
     * delete admin by admin_id
     * @param int $iAdminID
     * @return 0 or 1
     */
    public function deleteAdminByAdminID($iAdminID) {
    	$result = 0;
    	try {
    		# Get Data Master Global
    		$storage = Core_Global::getDbGlobalMaster();
    
    		# Prepare store procude
    		$stmt = $storage->prepare("CALL sp_admin_delete_by_admin_id(:p_admin_id, @p_RowCount)");
    		$stmt->bindParam('p_admin_id', $iAdminID, PDO::PARAM_INT);
    		$stmt->execute();
    
    		# Fetch All Result
    		$stmt = $storage->query("SELECT @p_RowCount");
    		$result = $stmt->fetchColumn();
    
    		# Free cursor
    		$stmt->closeCursor();
    	} catch (Exception $ex) {
    		ErrorLog::getInstance()->insert(__CLASS__, __FUNCTION__, $ex->getMessage(), $iAdminID);
    	}
    
    	// return data
    	return $result;
    }
    
    /**
     * account_id, GROUP_CONCAT(role_id)
     */
     public function getAdminByID($iUserID) {
     	
     	$arrResult = array();
     	
        try {

            # Get Data Master Global
            $storage = Core_Global::getDbGlobalSlave();

            # Prepare store procude
            $stmt = $storage->prepare("CALL sp_admin_selectbyid(:p_account_id)");
            $stmt->bindParam('p_account_id', $iUserID, PDO::PARAM_INT);
            $stmt->execute();

            # Fetch All Result
            $arrResult = $stmt->fetch();
            # Free cursor
            $stmt->closeCursor();
            
        } catch (Exception $ex) {
            ErrorLog::getInstance()->insert(__CLASS__, __FUNCTION__, $ex->getMessage(), $iUserID);
            $arrResult = '';
        }

        // return data
        return $arrResult;
    }
    
    /**
     * 
     * @param int $iAdminID
     * @return array
     */
    public function getAdminByAdminID($iAdminID) {
    
    	$arrResult = array();
    
    	try {
    
    		# Get Data Master Global
    		$storage = Core_Global::getDbGlobalSlave();
    
    		# Prepare store procude
    		$stmt = $storage->prepare("CALL sp_admin_select_by_admin_id(:p_admin_id)");
    		$stmt->bindParam('p_admin_id', $iAdminID, PDO::PARAM_INT);
    		$stmt->execute();
    
    		# Fetch All Result
    		$arrResult = $stmt->fetch();
    		# Free cursor
    		$stmt->closeCursor();
    
    	} catch (Exception $ex) {
    		ErrorLog::getInstance()->insert(__CLASS__, __FUNCTION__, $ex->getMessage(), $iAdminID);
    		$arrResult = '';
    	}
    
    	// return data
    	return $arrResult;
    } 

     /**
      * Insert user info to admin table
      * @author Le Thanh Tai <tai.lt@vn.gnt-global.com>
      * @param array $userInfo
      * @return int  Returns number > 0 on success or return number < 0 on failure
      */
    public function insertAdmin($userInfo) 
    {
        $result = 0;
        
        try {
            # Get Data Master Global
            $storage = Core_Global::getDbGlobalMaster();

            $sql = 'INSERT INTO admin(account_id, role_id, super_admin, active, create_date, update_date) VALUES(:account_id, :role_id, :super_admin, :active, :create_date, :update_date)';
            # Prepare store procude
            $stmt = $storage->prepare($sql);
            
            $stmt->execute(array(
                ':account_id'       => $userInfo['account_id'],
                ':role_id'          => $userInfo['role_id'],
                ':super_admin'      => $userInfo['super_admin'],
                ':active'           => $userInfo['active'],
                ':create_date'      => $userInfo['create_date'],
                ':update_date'      => $userInfo['update_date'],
            ));

            $result = $storage->lastInsertId(); 
            # Free cursor
            $stmt->closeCursor();
        } catch (Exception $ex) {
            ErrorLog::getInstance()->insert(__CLASS__, __FUNCTION__, $ex->getMessage(),$iUserID);
        }
 
        // return data
        return $result;
    }}