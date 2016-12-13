<?php
/**
 * @author      :   Hiennd
 * @name        :   Core_Business_Api_AccountInfo
 * @version     :   20161206
 * @copyright   :   My company
 * @todo        :   Using for account service
 */
class Core_Business_Api_AccountInfo
{
   /**
     *
     * 
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
     * 
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
     * 
     */
    public function insertAccountInfo($arrData)
    {
         //init return result
        $result = 0;
        $arrData = Validate::encodeValues($arrData);
        try {
            
            $iCreateDate = time();
            // Get Data Master Global
            $storage = Core_Global::getDbGlobalMaster();
            $sql = "INSERT INTO `account_info` ( 
                `username`,
                `password`,
                `name`,
                `email`,
                `phone`,
                `address`,
                `level`,
                `is_admin`,
                `avatar`,
                `active`,
                `status`,
                `create_date`,
                `update_date` ) VALUES (
                :p_username ,
                :p_password ,
                :p_name,
                :p_email,
                :p_phone,
                :p_address,
                :p_level,
                :p_is_admin,
                :p_avatar,
                :p_active,
                :p_status,
                :p_create_date,
                :p_update_date
                )";
// error_log($sql);                    
// error_log(Zend_Json::encode($arrData));
            // Prepare store procude
            $stmt = $storage->prepare($sql);
            
            $stmt->bindParam(':p_username', $arrData['username'], PDO::PARAM_STR);
            $stmt->bindParam(':p_password', $arrData['password'], PDO::PARAM_STR);
            $stmt->bindParam(':p_name', $arrData['name'], PDO::PARAM_STR);
            $stmt->bindParam(':p_email', $arrData['email'], PDO::PARAM_STR);
            $stmt->bindParam(':p_phone', $arrData['phone'], PDO::PARAM_STR);
            $stmt->bindParam(':p_avatar', $arrData['avatar'], PDO::PARAM_STR);
            $stmt->bindParam(':p_address', $arrData['address'], PDO::PARAM_STR);
            $stmt->bindParam(':p_level', $arrData['level'], PDO::PARAM_INT);
            $stmt->bindParam(':p_is_admin', $arrData['is_admin'], PDO::PARAM_INT);
            $stmt->bindParam(':p_active', $arrData['active'], PDO::PARAM_INT);
            $stmt->bindParam(':p_status', $arrData['status'], PDO::PARAM_INT);
            $stmt->bindParam(':p_create_date', $iCreateDate, PDO::PARAM_INT);
            $stmt->bindParam(':p_update_date', $iCreateDate, PDO::PARAM_INT);
                        
            $stmt->execute();

            // Fetch Result            
            // $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $result = $storage->lastInsertId(); 
            // Free cursor
            $stmt->closeCursor();
        } catch (Exception $ex) {
            Core_Common::var_dump($ex);
            // ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),'',$arrData['name']);
            error_log("error_exception");            
            $result = 0;
        }

        // return data
        return $result;
    }
    
    
    
        /**
     * @return <int>
     */
    public function updateAccountInfo($arrData) 
    {
         //init return result
        $result = -1;
        $arrData = Validate::encodeValues($arrData);
        try {
            
            $iUpdateDate = time();
            $storage = Core_Global::getDbGlobalMaster();
            $sql = "UPDATE `account_info` SET 
                    `gender` = :p_gender,
                    `birthday` = :p_birthday,
                    `address` = :address,
                    `email` = :p_email,
                    `phone` = :p_phone,
                    `picture` = :p_picture,
                    `avatar` = :p_avatar,
                    `skype_account` = :p_skype_account,
                    `google_account` = :p_google_account,
                    `facebook_account` = :p_facebook_account,
                    `yahoo_account` = :p_yahoo_account,
                    `country_id` = :p_country_id,
                    `name` = :p_name,
                    `email1` = :p_email1,
                    `contact_name` = :p_contact_name,
                    `contact_relationship` = :p_contact_relationship,
                    `contact_address` = :p_contact_address,
                    `contact_phone` = :p_contact_phone,
                    `description` = :p_description,
                    `update_date` = :p_update_date
                    WHERE account_id = :p_account_id
                    LIMIT 1;
                    ";        
            // Prepare store procude
            $stmt = $storage->prepare($sql);

            $stmt->bindParam(':p_account_id', $arrData['account_id'], PDO::PARAM_INT);
            $stmt->bindParam(':p_name', $arrData['name'], PDO::PARAM_STR);
            $stmt->bindParam(':p_email', $arrData['email'], PDO::PARAM_STR);
            $stmt->bindParam(':p_phone', $arrData['phone'], PDO::PARAM_STR);
            $stmt->bindParam(':p_birthday', $arrData['birthday'], PDO::PARAM_STR);
            $stmt->bindParam(':p_picture', $arrData['picture'], PDO::PARAM_STR);
            $stmt->bindParam(':p_avatar', $arrData['avatar'], PDO::PARAM_STR);
            
            $stmt->bindParam(':p_address', $arrData['address'], PDO::PARAM_STR);
            $stmt->bindParam(':p_skype_account', $arrData['skype_account'], PDO::PARAM_STR);
            $stmt->bindParam(':p_google_account', $arrData['google_account'], PDO::PARAM_STR);
            $stmt->bindParam(':p_facebook_account', $arrData['facebook_account'], PDO::PARAM_STR);
            $stmt->bindParam(':p_yahoo_account', $arrData['yahoo_account'], PDO::PARAM_STR); 
            $stmt->bindParam(':p_country_id', $arrData['country_id'], PDO::PARAM_INT);
            $stmt->bindParam(':p_description', $arrData['description'], PDO::PARAM_STR);
            $stmt->bindParam(':p_update_date', $iUpdateDate, PDO::PARAM_INT);
            
            $stmt->bindParam(':p_gender', $arrData['gender'], PDO::PARAM_INT);            
            $stmt->bindParam(':p_email1', $arrData['email1'], PDO::PARAM_STR);
            $stmt->bindParam(':p_contact_name', $arrData['contact_name'], PDO::PARAM_STR);
            $stmt->bindParam(':p_contact_address', $arrData['contact_address'], PDO::PARAM_STR);
            $stmt->bindParam(':p_contact_phone', $arrData['contact_phone'], PDO::PARAM_STR);
            $stmt->bindParam(':p_contact_relationship', $arrData['contact_relationship'], PDO::PARAM_STR);
            $stmt->execute();

            // Fetch All Result
            $result = $stmt->rowCount();

            // Free cursor
            $stmt->closeCursor();
        } catch (Exception $ex) {
            Core_Common::var_dump($ex);
            // ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),$arrData['account_id'],$arrData['name']);
        	// print_r($ex->getMessage());
            $result = -1;
            // exit();
        }

        // return data
        return $result;
    }
    
    
     /**
     * @return <int>
     */
    public function updateUpperTimes($iAccountID, $times)
    {
         //init return result
        $result = -1;
        // $arrData = Validate::encodeValues($arrData);
        try {
            
            $iUpdateDate = time();
            
            // Get Data Master Global
            $storage = Core_Global::getDbGlobalMaster();
            
            $sql = "UPDATE `account_info` SET 
                        `upper_times` = :p_upper_time,
                        `update_date` = :p_update_date
                    WHERE `account_id` = :p_account_id
                    LIMIT 1    
                    ";

            // Prepare store procude
            $stmt = $storage->prepare($sql);
                        
            $stmt->bindParam(':p_account_id', $iAccountID, PDO::PARAM_INT);
            $stmt->bindParam(':p_upper_time', $times, PDO::PARAM_INT);
            $stmt->bindParam(':p_update_date', $iUpdateDate, PDO::PARAM_INT);
            $stmt->execute();

            // Fetch All Result
            $result = $stmt->rowCount();

            // Free cursor
            $stmt->closeCursor();
        } catch (Exception $ex) {
            // ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),$arrData['account_id'], $arrData['name']);
            $result = -1;
        }

        // return data
        return $result;
    }

    public function updateLastLogin($iAccountID)
    {
         //init return result
        $result = -1;
        try {            
            $iUpdateDate = time();
            
            // Get Data Master Global
            $storage = Core_Global::getDbGlobalMaster();
            
            $sql = "UPDATE `account_info` SET 
                        `last_login_date` = :p_last_login_date,
                        `update_date` = :p_update_date
                    WHERE `account_id` = :p_account_id
                    LIMIT 1    
                    ";

            // Prepare store procude
            $stmt = $storage->prepare($sql);
                        
            $stmt->bindParam(':p_account_id', $iAccountID, PDO::PARAM_INT);
            $stmt->bindParam(':p_last_login_date', $iUpdateDate, PDO::PARAM_INT);
            $stmt->bindParam(':p_update_date', $iUpdateDate, PDO::PARAM_INT);
            $stmt->execute();

            // Fetch All Result
            $result = $stmt->rowCount();

            // Free cursor
            $stmt->closeCursor();
        } catch (Exception $ex) {
            // ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),$arrData['account_id'], $arrData['name']);
            $result = -1;
        }

        // return data
        return $result;
    }
    
        
    /**
     * @return <int>
     */
    public function updateActiveStatus($iAccountID, $iActive) 
    {
         //init return result
        $result = -1;
        
        try {
            
            $iUpdateDate = time();
            
            // Get Data Master Global
            $storage = Core_Global::getDbGlobalMaster();
            
            $sql = "UPDATE `account_info` SET 
                        `active` = :p_active,
                        `update_date` = :p_update_date
                    WHERE `account_id` = :p_account_id
                    LIMIT 1    
                    ";

            // Prepare store procude
            $stmt = $storage->prepare($sql);
            
            
            $stmt->bindParam(':p_account_id', $iAccountID, PDO::PARAM_INT);
            $stmt->bindParam(':p_active', $iActive, PDO::PARAM_INT);
            $stmt->bindParam(':p_update_date', $iUpdateDate, PDO::PARAM_INT);
            $stmt->execute();

            // Fetch All Result            
            $result = $stmt->rowCount();

            // Free cursor
            $stmt->closeCursor();
        } catch (Exception $ex) {
            // ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),$iAccountID);
            $result = -1;
        }

        // return data
        return $result;
    }
    

    /**
     * @todo  Remove event
     * @param <int> $iEventId
     * @return <int>
     */
    public function removeAccountInfo($iAccountID) {
        $result = -1;
        try {
            // Get Data Master Global
            $storage = Core_Global::getDbGlobalMaster();
            $sql = "DELETE FROM `account_info` 
                    WHERE `account_id` = :p_account_id
                    LIMIT 1";
            // Prepare store procude
            $stmt = $storage->prepare($sql);
            $stmt->bindParam(':p_account_id', $iAccountID, PDO::PARAM_INT);
            $stmt->execute();

            // Fetch All Result            
            $result = 1;

            // Free cursor
            $stmt->closeCursor();
        } catch (Exception $ex) {
            ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),$iAccountID);
            $result = -1;
        }

        // return data
        return $result;
    }

    public function countAccountInfoList($sAccountID, $sName, $sEmail, $sPhone, $sAddress, $iGender, $iActive, $iLevel, $sSortField, $sSortType) {
       
        $arrResult = array();
        $queryWhere = " WHERE 1=1 ";
        $arrParams = array(); 
        $totals = 0;
        try {          
                        
            // Get Data Master Global
            $storage = Core_Global::getDbGlobalSlave();

            $sql = "SELECT COUNT(`account_id`) as totals 
                    FROM `account_info`                    
                    ";

            //search by accountId
            if(!empty($sAccountID)){
                $queryWhere .= " AND `account_id` LIKE :p_sAccountID ";
                $arrParams[":p_sAccountID"] = "%".$sAccountID."%";
            }
            //search by name, username
            if(!empty($sName)){
                $queryWhere .= " AND ( `name` LIKE :p_sName OR `username` LIKE :p_username ) ";
                $arrParams[":p_sName"] = "%".$sName."%";
                $arrParams[":p_username"] = "%".$sName."%";
            }
            //search by email
            if(!empty($sEmail)){
                $queryWhere .= " AND ( `email` LIKE :p_email OR `email1` LIKE :p_email1 ) ";
                $arrParams[":p_email"] = "%".$sEmail."%";
                $arrParams[":p_email1"] = "%".$sEmail."%";
            }
            //search by phone
            if(!empty($sPhone)){
                $queryWhere .= " AND `phone` LIKE :p_phone ";
                $arrParams[":p_phone"] = "%".$sPhone."%";
            }
            //search by address
            if(!empty($sAddress)){
                $queryWhere .= " AND `address` LIKE :p_sAddress ";
                $arrParams[":p_sAddress"] = "%".$sAddress."%";
            }
            //search by gender
            if(!empty($iGender)){
                $queryWhere .= " AND `gender` = :p_gender ";
                $arrParams[":p_gender"] = $iGender;
            }
            //search by active
            if(!empty($iActive)){
                $queryWhere .= " AND `active` = :p_active ";
                $arrParams[":p_active"] = $iActive;
            }
            //search by level
            if(!empty($iLevel)){
                $queryWhere .= " AND `level` = :p_level ";
                $arrParams[":p_level"] = $iLevel;
            }

            $sql .= $queryWhere;

            //Order by
            if(!empty($sSortField) && !empty($sSortType)){
                $sql .= " ORDER BY :p_sort_field :p_sort_type ";
                $arrParams[":p_sort_field"] = $sSortField;
                $arrParams[":p_sort_type"] = $sSortType;
            }            
            // Prepare store procude
            $stmt = $storage->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

            $stmt->execute($arrParams);

            // Fetch All Result
            $arrResult = $stmt->fetch();
            
            $totals = $arrResult["totals"];
            // Free cursor
            $stmt->closeCursor();            

            //Return data
            $arrResult = array(
                'total' => $iTotal,
                'data' => $arrResult
            );            
            
        } catch (Exception $ex) {
            Core_Common::var_dump($ex);
            // ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),0,$sName);
            $totals = 0;
        }

        // return data
        return $totals;
    }

    public function getAccountInfoList($sAccountID, $sName, $sEmail, $sPhone, $sAddress, $iGender, $iActive, $iLevel, $sSortField, $sSortType, $iOffset = 0, $iLimit = 20) {
       
        $arrResult = array();
        $queryWhere = " WHERE 1=1 ";
        $arrParams = array(); 
        try {          
                        
            // Get Data Master Global
            $storage = Core_Global::getDbGlobalSlave();

            $sql = "SELECT * 
                    FROM `account_info`                    
                    ";

            //search by accountId
            if(!empty($sAccountID)){
                $queryWhere .= " AND `account_id` LIKE :p_sAccountID ";
                $arrParams[":p_sAccountID"] = "%".$sAccountID."%";
            }
            //search by name, username
            if(!empty($sName)){
                $queryWhere .= " AND ( `name` LIKE :p_sName OR `username` LIKE :p_username ) ";
                $arrParams[":p_sName"] = "%".$sName."%";
                $arrParams[":p_username"] = "%".$sName."%";
            }
            //search by email
            if(!empty($sEmail)){
                $queryWhere .= " AND ( `email` LIKE :p_email OR `email1` LIKE :p_email1 ) ";
                $arrParams[":p_email"] = "%".$sEmail."%";
                $arrParams[":p_email1"] = "%".$sEmail."%";
            }
            //search by phone
            if(!empty($sPhone)){
                $queryWhere .= " AND `phone` LIKE :p_phone ";
                $arrParams[":p_phone"] = "%".$sPhone."%";
            }
            //search by address
            if(!empty($sAddress)){
                $queryWhere .= " AND `address` LIKE :p_sAddress ";
                $arrParams[":p_sAddress"] = "%".$sAddress."%";
            }
            //search by gender
            if(!empty($iGender)){
                $queryWhere .= " AND `gender` = :p_gender ";
                $arrParams[":p_gender"] = $iGender;
            }
            //search by active
            if(!empty($iActive)){
                $queryWhere .= " AND `active` = :p_active ";
                $arrParams[":p_active"] = $iActive;
            }
            //search by level
            if(!empty($iLevel)){
                $queryWhere .= " AND `level` = :p_level ";
                $arrParams[":p_level"] = $iLevel;
            }

            $sql .= $queryWhere;

            //Order by
            if(!empty($sSortField) && !empty($sSortType)){
                $sql .= " ORDER BY :p_sort_field :p_sort_type ";
                $arrParams[":p_sort_field"] = $sSortField;
                $arrParams[":p_sort_type"] = $sSortType;
            }
            $sql .= " LIMIT :p_offset, :p_limit ";
            $arrParams[":p_offset"] = $iOffset;
            $arrParams[":p_limit"] = $iLimit;
            
            // Prepare store procude
            $stmt = $storage->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

            $stmt->execute($arrParams);

            // Fetch All Result
            $arrResult = $stmt->fetchAll();
            
            // Free cursor
            $stmt->closeCursor();                                
            
        } catch (Exception $ex) {
            Core_Common::var_dump($ex);
            // ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),0,$sName);
            $arrResult = array();
        }

        // return data
        return $arrResult;
    }  
    
    /*
     * 
     */
     public function getAccountInfoByAccountID($iAccountID) {
     	
     	$arrResult = array();
     	
        try {

            // Get Data Master Global
            $storage = Core_Global::getDbGlobalSlave();

            $sql = "SELECT * 
                    FROM `account_info` 
                    WHERE `account_id` = :p_account_id
                    LIMIT 1";

            // Prepare store procude
            $stmt = $storage->prepare($sql);
            $stmt->bindParam(':p_account_id', $iAccountID, PDO::PARAM_INT);
            $stmt->execute();

            // Fetch All Result
            $arrResult = $stmt->fetch();

            // Free cursor
            $stmt->closeCursor();
        } catch (Exception $ex) {
            // ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),$iAccountID);
            $arrResult = array();
        }

        // return data
        return $arrResult;
    }
    
    /*
     * 
     */
     public function getAccountInfoByEmail($sEmail, $iActive = 1) {
         
         $arrResult = array();
         
        try {

            // Get Data Master Global
            $storage = Core_Global::getDbGlobalSlave();
            $sql = "SELECT * 
                    FROM `account_info` 
                    WHERE `email` = :p_email
                    AND `active` = :p_active
                    LIMIT 1";
            // Prepare store procude
            $stmt = $storage->prepare($sql);
            $stmt->bindParam(':p_email', $sEmail, PDO::PARAM_STR);
            $stmt->bindParam(':p_active', $iActive, PDO::PARAM_INT);
            $stmt->execute();

            // Fetch All Result
            $arrResult = $stmt->fetch();

            // Free cursor
            $stmt->closeCursor();
        } catch (Exception $ex) {
            // ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage());
            $arrResult = array();
        }

        // return data
        return $arrResult;
    }
    
    
    /*
     * 
     */
     public function getAccountInfoByUserName($sUserName, $iActive) {
         
         $arrResult = array();
         
        try {

            // Get Data Master Global
            $storage = Core_Global::getDbGlobalSlave();
            $sql = "SELECT * 
                    FROM `account_info` 
                    WHERE `username` = :p_username
                    AND `active` = :p_active
                    LIMIT 1";
            // Prepare store procude
            $stmt = $storage->prepare($sql);
            $stmt->bindParam(':p_username', $sUserName, PDO::PARAM_STR);
            $stmt->bindParam(':p_active', $iActive, PDO::PARAM_INT);
            $stmt->execute();

            // Fetch All Result
            $arrResult = $stmt->fetch();

            // Free cursor
            $stmt->closeCursor();
        } catch (Exception $ex) {
            Core_Common::var_dump($ex->getMessage());
            // ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),0,$sUserName);
            $arrResult = array();
        }

        // return data
        return $arrResult;
    }
    
    /**
     * @return <int>
     */
    public function updateAvatar($iAccountID, $sAvatar) 
    {
         //init return result
        $result = -1;
        
        try {
            
            
            // Get Data Master Global
            $storage = Core_Global::getDbGlobalMaster();
            $sql = "UPDATE `account_info` SET 
                        `avatar` = :p_avatar,
                        `update_date` = :p_update_date
                    WHERE `account_id` = :p_account_id
                    LIMIT 1    
                    ";        
            // Prepare store procude
            $stmt = $storage->prepare($sql);
            
            $stmt->bindParam(':p_account_id', $iAccountID, PDO::PARAM_INT);
            $stmt->bindParam(':p_avatar', $sAvatar, PDO::PARAM_STR);
            $stmt->bindParam(':p_update_date', time(), PDO::PARAM_INT);
         
            $stmt->execute();

            // Fetch All Result
            $result = $stmt->rowCount();

            // Free cursor
            $stmt->closeCursor();
        } catch (Exception $ex) {
            // ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),$iAccountID);
            $result = -1;
        }

        // return data
        return $result;
    }
    
    
    
     /*
     *  Account Info many AccountIDs
     */
     public function getAccountInfoByAccountIDs($iAccountID) 
     {
         
        $arrResult = array();
        
        try {

            // Get Data Master Global
            $storage = Core_Global::getDbGlobalSlave();
            $sql = "SELECT * 
                    FROM `account_info` 
                    WHERE `account_id` = :p_account_id
                    AND `active` = :p_active
                    LIMIT 1";
            // Prepare store procude
            $stmt = $storage->prepare($sql);
            $stmt->bindParam(':p_account_id', $iAccountID, PDO::PARAM_INT);
            $stmt->bindParam(':p_active', $iActive, PDO::PARAM_INT);
            $stmt->execute();

            // Fetch All Result
            $arrResult = $stmt->fetch();

            // Free cursor
            $stmt->closeCursor();
        } catch (Exception $ex) {
            ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),$sAccountID);
            $arrResult = array();
        }

        // return data
        return $arrResult;
    }    
        

    public function countUserActive() {    	
    	$totals = 0;    	
    	try {
    
    		// Get Data Master Global
    		$storage = Core_Global::getDbGlobalSlave();
            $sql = "SELECT COUNT(`account_id`) as totals 
                    FROM `account_info` ";
    		// Prepare store procude
    		$stmt = $storage->prepare($sql);
       		$stmt->execute();
    
    		// Fetch All Result
    		$arrResult = $stmt->fetch();
    		$totals = $arrResult['totals'];
    		// Free cursor
    		$stmt->closeCursor();
    	} catch (Exception $ex) {
    		$totals = 0;
    	}
    
    	// return data
    	return $totals;
    }   

    /*
     * 
     */
     public function userLogin($sUserName, $sPassword, $arrAccInfo) {
         
         $result = 0;         
        try {

            // Get Data Master Global
            $storage = Core_Global::getDbGlobalSlave();
            $sql = "SELECT * 
                    FROM `account_info` 
                    WHERE ( `username` = :p_username OR `email` = :p_username )
                    AND `password` = :p_password
                    AND `active` = 1
                    LIMIT 1";
            // Prepare store procude
            $stmt = $storage->prepare($sql);
            $stmt->bindParam(':p_username', $sUserName, PDO::PARAM_STR);
            $stmt->bindParam(':p_password', md5($sPassword), PDO::PARAM_STR);
            $stmt->execute();

            // Fetch All Result
            $arrAccInfo = $stmt->fetch();

            // Free cursor
            $stmt->closeCursor();

            $result = 1;
        } catch (Exception $ex) {
            Core_Common::var_dump($ex->getMessage());
            // ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),0,$sUserName);
            $arrAccInfo = array();
            $result = 0;
        }

        // return data
        return $result;
    } 

}