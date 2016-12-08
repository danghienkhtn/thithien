l<?php

/**
 * @author      :   DANG HIEN
 * @name        :   Core_Business_Api_Event
 * @version     :   20161206
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
     * @todo  Remove event
     * @param <int> $iEventId
     * @return <int>
     */
    public function removeAdmin($iUserID) {
        try {
            // Get Data Master Global
            $storage = Core_Global::getDbGlobalMaster();
            $sql = "DELETE FROM `admin`
                    WHERE `account_id` = :p_account_id
                    LIMIT 1";
            // Prepare store procude
            $stmt = $storage->prepare($sql);
            $stmt->bindParam(':p_laccount_id', $iUserID, PDO::PARAM_INT);
            $stmt->execute();

            // Fetch All Result
            $result = 1;

            // Free cursor
            $stmt->closeCursor();
        } catch (Exception $ex) {
            // ErrorLog::getInstance()->insert(__CLASS__, __FUNCTION__, $ex->getMessage(), $iUserID);
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
    		// Get Data Master Global
    		$storage = Core_Global::getDbGlobalMaster();
            $sql = "UPDATE `admin` SET 
                        `modules` = :p_modules,
                        `roles` = :p_roles,
                        `super_admin` = :p_super_admin,
                        `active` = :p_active,
                        `update_date` = :p_update_date
                    WHERE `admin_id` = :p_admin_id
                    LIMIT 1     
                        ";
    		// Prepare store procude
    		$stmt = $storage->prepare($sql);
    		$stmt->bindParam(':p_admin_id', $arrAdmin['admin_id'], PDO::PARAM_INT);
    		$stmt->bindParam(':p_update_date', time(), PDO::PARAM_INT);
    		$stmt->bindParam(':p_roles', $arrAdmin['roles'], PDO::PARAM_STR);
            $stmt->bindParam(':p_modules', $arrAdmin['modules'], PDO::PARAM_STR);
            $stmt->bindParam(':p_super_admin', $arrAdmin['super_admin'], PDO::PARAM_INT);
            $stmt->bindParam(':p_active', $arrAdmin['active'], PDO::PARAM_INT);
    		$stmt->execute();
    
    		// Fetch All Result    		
    		$result = $stmt->rowCount();
    
    		// Free cursor
    		$stmt->closeCursor();
    	} catch (Exception $ex) {
    		// ErrorLog::getInstance()->insert(__CLASS__, __FUNCTION__, $ex->getMessage(), $arrAdmin['account_id']);
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
    public function select($sName, $sRoles, $iOffset = 0, $iLimit = 20) {
    	 
    	$arrResult = array();
    	 
    	try {
    
    		// Get Data Master Global
    		$storage = Core_Global::getDbGlobalSlave();
            $sql = "SELECT `name`, 
                            `username`, 
                            `email`, 
                            `modules`, 
                            `roles`, 
                            `super_admin`, 
                            `active`, 
                            `create_date`, 
                            `update_date`
                    FROM admin ad, account_info ac ";
            $sqlWhere = " WHERE ad.`account_id` = ac.`account_id` ";
            $arrParams = array();
            if(!empty($sName))
            {
                $sqlWhere .= "AND ac.`name` LIKE :p_name ";
                $arrParams[':p_name'] = "%".$sName."%";
            }    
            if(!empty($sRoles))
            {
                $sqlWhere .= "AND ac.`name` LIKE :p_roles ";
                $arrParams[':p_roles'] = "%".$sRoles."%";
            }

            $sql .= " LIMIT :p_offset, :p_limit ";
            $arrParams[":p_offset"] = $iOffset;
            $arrParams[":p_limit"] = $iLimit;

    		// Prepare store procude
    		$stmt = $storage->prepare($sql);    		    		
    		
    		$stmt->execute($arrParams);
    
    		// Fetch All Result
    		$arrResult = $stmt->fetchAll();
    
    		// Free cursor
    		$stmt->closeCursor();
    
    	} catch (Exception $ex) {
    			// ErrorLog::getInstance()->insert(__CLASS__, __FUNCTION__, $ex->getMessage());
            $arrResult = array();
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
    		// Get Data Master Global
    		$storage = Core_Global::getDbGlobalMaster();
            $sql = "DELETE FROM `admin` 
                    WHERE `admin_id` = :p_admin_id
                    LIMIT 1";
    		// Prepare store procude
    		$stmt = $storage->prepare($sql);
    		$stmt->bindParam(':p_admin_id', $iAdminID, PDO::PARAM_INT);
    		$stmt->execute();
        	
    		$result = 1;
    
    		// Free cursor
    		$stmt->closeCursor();
    	} catch (Exception $ex) {
    		// ErrorLog::getInstance()->insert(__CLASS__, __FUNCTION__, $ex->getMessage(), $iAdminID);
            $result = 0;
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

            // Get Data Master Global
            $storage = Core_Global::getDbGlobalSlave();
            $sql = "SELECT * 
                    FROM `admin`
                    WHERE account_id = :p_account_id
                    LIMIT 1";
            // Prepare store procude
            $stmt = $storage->prepare($sql);
            $stmt->bindParam(':p_account_id', $iUserID, PDO::PARAM_INT);
            $stmt->execute();

            // Fetch All Result
            $arrResult = $stmt->fetch();
            // Free cursor
            $stmt->closeCursor();
            
        } catch (Exception $ex) {
            // ErrorLog::getInstance()->insert(__CLASS__, __FUNCTION__, $ex->getMessage(), $iUserID);
            $arrResult = array();
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
    
    		// Get Data Master Global
    		$storage = Core_Global::getDbGlobalSlave();
            $sql = "SELECT * 
                    FROM `admin`
                    WHERE admin_id = :p_admin_id
                    LIMIT 1";
    		// Prepare store procude
    		$stmt = $storage->prepare($sql);
    		$stmt->bindParam(':p_admin_id', $iAdminID, PDO::PARAM_INT);
    		$stmt->execute();
    
    		// Fetch All Result
    		$arrResult = $stmt->fetch();
    		// Free cursor
    		$stmt->closeCursor();
    
    	} catch (Exception $ex) {
    		// ErrorLog::getInstance()->insert(__CLASS__, __FUNCTION__, $ex->getMessage(), $iAdminID);
    		$arrResult = array();
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
            // Get Data Master Global
            $storage = Core_Global::getDbGlobalMaster();

            $sql = 'INSERT INTO `admin` (
                            `account_id`, `modules`, `roles`, `super_admin`, `active`, `create_date`, `update_date` ) 
                    VALUES(:p_account_id, :p_roles, :p_super_admin, :p_active, :p_create_date, :p_update_date)';
            // Prepare store procude
            $stmt = $storage->prepare($sql);
            
            $stmt->execute(array(
                ':p_account_id'       => $userInfo['account_id'],
                ':p_modules'            => $userInfo['modules'],
                ':p_roles'            => $userInfo['roles'],
                ':p_super_admin'      => $userInfo['super_admin'],
                ':p_active'           => $userInfo['active'],
                ':p_create_date'      => $userInfo['create_date'],
                ':p_update_date'      => $userInfo['update_date'],
            ));

            $result = $storage->lastInsertId(); 
            // Free cursor
            $stmt->closeCursor();
        } catch (Exception $ex) {
            ErrorLog::getInstance()->insert(__CLASS__, __FUNCTION__, $ex->getMessage(),$iUserID);
        }
 
        // return data
        return $result;
    }
}