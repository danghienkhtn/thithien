<?php
/**

 * @todo        :   Using for account service
 */
class Core_Business_Api_Token
{
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
     * @return <int>
     */
    public function insert($sKey, $sType, $iAccountID, $sEmail, $sAvatar, $sPs, $sIPOwner, $sIPClient, $iExpired) 
    {
        $result = 0;
        try {
            $iUpdateDate = time();

            # Get Data Master Global
            $storage = Core_Global::getDbGlobalMaster();

            $sql = "INSERT INTO `token`(
                        `key`,
                        `type`,
                        `account_id`,
                        `email`,
                        `avatar`,
                        `ps`,
                        `IPOwner`,
                        `IPClient`,
                        `expired`,
                        `create_date`,
                        `update_date`
                    )
                    VALUES( :p_key, :p_type, :p_account_id, :p_email, :p_avatar, :p_ps, :p_ipOwner, :p_ipClient, :p_expired, :p_create_date, :p_update_date )";                
            # Prepare store procude
            $stmt = $storage->prepare($sql);

            $stmt->bindParam(':p_key', $sKey, PDO::PARAM_STR);
            $stmt->bindParam(':p_type', $sType, PDO::PARAM_STR);
            $stmt->bindParam(':p_account_id', $iAccountID, PDO::PARAM_INT);
            $stmt->bindParam(':p_email', $sEmail, PDO::PARAM_STR);
            $stmt->bindParam(':p_avatar', $sAvatar, PDO::PARAM_STR);
            $stmt->bindParam(':p_ps', $sPs, PDO::PARAM_STR);
            $stmt->bindParam(':p_ipOwner', $sIPOwner, PDO::PARAM_STR);
            $stmt->bindParam(':p_ipClient', $sIPClient, PDO::PARAM_STR);
            $stmt->bindParam(':p_expired', $iExpired, PDO::PARAM_INT);    
            $stmt->bindParam(':p_create_date', $iUpdateDate, PDO::PARAM_INT);
            $stmt->bindParam(':p_update_date', $iUpdateDate, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $storage->lastInsertId();

            # Free cursor
            $stmt->closeCursor();

        } catch (Exception $ex) {
            // ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),'','');
            return $result;
        }
// error_log($sKey."_insert token:".$sql);

        // return data
        return $result;
    }
    
    /**
     * update
     * @param array $query
     * @param array $update
     * @return boolean
     */
    public function update($iAccountID, $sEmail, $sKey)
    {
    	$result = FALSE;
    	try {
    
    		$iUpdateDate = time();

            # Get Data Master Global
            $storage = Core_Global::getDbGlobalMaster();

            $sql = "UPDATE `token` SET                        
                        `update_date` = :p_update_date
                    WHERE `key` = :p_key
                    AND `account_id` = :p_account_id
                    AND `email` = :p_email                    
                    LIMIT 1";                
            # Prepare store procude
            $stmt = $storage->prepare($sql);

            $stmt->bindParam(':p_update_date', $iUpdateDate, PDO::PARAM_INT);
            $stmt->bindParam(':p_key', $sKey, PDO::PARAM_STR);
            $stmt->bindParam(':p_account_id', $iAccountID, PDO::PARAM_INT);
            $stmt->bindParam(':p_email', $sEmail, PDO::PARAM_STR);
            
            $stmt->execute();

            # Fetch All Result        
            $result = $stmt->rowCount();

            # Free cursor
            $stmt->closeCursor();
    
    	} catch (Exception $ex) {
            Core_Common::var_dump($ex);
    		// ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),'','');
            return $result; 
    	}
    	
    	return $result;
    }
    
    /**
     * delete file
     * 
     * @param int $iID
     * @return boolean
     */
    public function delete($iKey){
    	
    	$result = 0;
        try {
            # Get Data Master Global
            $storage = Core_Global::getDbGlobalMaster();

            $sql = "DELETE FROM `token` 
                    WHERE `key` = :p_key
                    LIMIT 1";
            # Prepare store procude
            $stmt = $storage->prepare($sql);
            $stmt->bindParam(':p_key', $iKey, PDO::PARAM_INT);
            $stmt->execute();

            # Fetch All Result
            $result = $stmt->fetchColumn();

            # Free cursor
            $stmt->closeCursor();
        } catch (Exception $ex) {
            // ErrorLog::getInstance()->insert(__CLASS__, __FUNCTION__, $ex->getMessage());
            print_r($ex->getMessage());
        }

        // return data
        return $result;    	    	
    }        
    
    public function select($sType = "", $iAccountID = "", $sEmail = "", $iPs = "", $sIPClient = "", $sKey = "")
    {
        $queryWhere = " WHERE 1=1 ";        
        $arrParamsWhere = array();
        //search type
        if(!empty($sType)){
            $queryWhere .= " AND `type` = :sType";
            $arrParamsWhere[":sType"] = $sType;
        }
        
        //search accountId
        if(!empty($iAccountID)){
            $queryWhere .= " AND `account_id` = :iAccountID";
            $arrParamsWhere[":iAccountID"] = $iAccountID;
        }        

        //search email
        if(!empty($sEmail)){
            $queryWhere .= " AND `email` = :sEmail";
            $arrParamsWhere[":sEmail"] = $sEmail;
        }

        //search ps
        if(!empty($iPs)){
            $queryWhere .= " AND `ps` = :iPs";
            $arrParamsWhere[":iPs"] = $iPs;
        }

        //IP Client
        if(!empty($sIPClient)){
            $queryWhere .= " AND (`IPClient` = :sIPClient OR `IPOwner` = :sIPOwner)";
            $arrParamsWhere[":sIPClient"] = $sIPClient;
            $arrParamsWhere[":sIPOwner"] = $sIPClient;
        }                

        //search skey
        if(!empty($sKey)){
            $queryWhere .= " AND `key` = :sToken";
            $arrParamsWhere[":sToken"] = $sKey;
        }

    	// $arrResult = array();

        try {
            # Get Data Master Global
            $storage = Core_Global::getDbGlobalSlave();
            $sql = "SELECT * 
                    FROM `token`";
            $sql .= $queryWhere . " LIMIT 1 ";        

            # Prepare store procude
            $stmt = $storage->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

            // $stmt->bindParam($sKey, PDO::PARAM_STR);
            $stmt->execute($arrParamsWhere);

            # Fetch All Result
            $arrResult = $stmt->fetch();

            # Free cursor
            $stmt->closeCursor();                      

        } catch (Exception $ex) {
            //ErrorLog::getInstance()->insert(__CLASS__, __FUNCTION__, $ex->getMessage());
            error_log("error here $ex");
            return array();
        }
// error_log("get token : ".$sql);
// error_log(Zend_Json::encode($arrParamsWhere));
// error_log("----return----".Zend_Json::encode($arrResult));
        // return data
        return $arrResult;
    }
    
    public function getToken($iAccountID, $sEmail, $sPs, $sIpClient)
    {
    
        $arrResult = array();

        try {

            # Get Data Master Global
            $storage = Core_Global::getDbGlobalSlave();

            $sql = "SELECT `key`
                    FROM `token`
                    WHERE `account_id` = :p_account_id
                    AND `email` = :p_email
                    AND `ps` = :p_ps
                    AND (`IPClient` = :p_ipClient OR `IPOwner` = :p_ipOwner)
                    AND NOW() <= `update_date` + `expired`
                    LIMIT 1";
            # Prepare store procude
            $stmt = $storage->prepare($sql);

            $stmt->bindParam(':p_account_id', $iAccountID, PDO::PARAM_INT);
            $stmt->bindParam(':p_email', $sEmail, PDO::PARAM_STR);
            $stmt->bindParam(':p_ps', $sPs, PDO::PARAM_STR);
            $stmt->bindParam(':p_ipClient', $sIPClient, PDO::PARAM_STR);
            $stmt->bindParam(':p_ipOwner', $sIPClient, PDO::PARAM_STR);
            $stmt->execute();

            # Fetch All Result
            $arrResult = $stmt->fetchColumn();

            # Free cursor
            $stmt->closeCursor();            

        } catch (Exception $ex) {
            // ErrorLog::getInstance()->insert(__CLASS__, __FUNCTION__, $ex->getMessage());
            return "";
        }

        // return data
        return $arrResult;
    }
    

  
}