l<?php
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
    public function insert($sKey, $iType, $iAccountID, $sUsername, $sAvatar, $sPs, $sIPOwner, $sIPClient, $iExpired) 
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
                        `username`,
                        `avatar`,
                        `ps`,
                        `IPOwner`,
                        `IPClient`,
                        `expired`,
                        `create_date`,
                        `update_date`,
                    )
                    VALUES( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )";                
            # Prepare store procude
            $stmt = $storage->prepare($sql);

            $stmt->bindParam($sKey, PDO::PARAM_STR);
            $stmt->bindParam($iType, PDO::PARAM_INT);
            $stmt->bindParam($iAccountID, PDO::PARAM_INT);
            $stmt->bindParam($sUsername, PDO::PARAM_STR);
            $stmt->bindParam($sAvatar, PDO::PARAM_STR);
            $stmt->bindParam($sPs, PDO::PARAM_STR);
            $stmt->bindParam($sIPOwner, PDO::PARAM_STR);
            $stmt->bindParam($sIPClient, PDO::PARAM_STR);
            $stmt->bindParam($iExpired, PDO::PARAM_INT);    
            $stmt->bindParam($iUpdateDate, PDO::PARAM_INT);
            $stmt->bindParam($iUpdateDate, PDO::PARAM_INT);
            $stmt->execute();

            # Fetch All Result        
            $result = $stmt->fetchColumn();

            # Free cursor
            $stmt->closeCursor();

        } catch (Exception $ex) {
            // ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),'','');
            return $result;
        }

        // return data
        return $result;
    }
    
    /**
     * update
     * @param array $query
     * @param array $update
     * @return boolean
     */
    public function update($iAccountID, $sUsername, $sKey)
    {
    	$result = FALSE;
    	try {
    
    		$iUpdateDate = time();

            # Get Data Master Global
            $storage = Core_Global::getDbGlobalMaster();

            $sql = "UPDATE `token` SET                        
                        `update_date` = ?
                    WHERE `key` = ?
                    AND `account_id` = ?
                    AND `username` = ?
                    LIMIT 1;";                
            # Prepare store procude
            $stmt = $storage->prepare($sql);

            $stmt->bindParam($iUpdateDate, PDO::PARAM_INT);
            $stmt->bindParam($sKey, PDO::PARAM_STR);
            $stmt->bindParam($iAccountID, PDO::PARAM_INT);
            $stmt->bindParam($sUsername, PDO::PARAM_STR);
            
            $stmt->execute();

            # Fetch All Result        
            $result = $stmt->fetchColumn();

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
                    WHERE `key` = ?
                    LIMIT 1;";
            # Prepare store procude
            $stmt = $storage->prepare($sql);
            $stmt->bindParam($iKey, PDO::PARAM_INT);
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
    
    public function select($sKey)
    {
    
    	$arrResult = array();

        try {

            # Get Data Master Global
            $storage = Core_Global::getDbGlobalSlave();

            $sql = "SELECT *
                    FROM `token`
                    WHERE `key` = ?
                    LIMIT 1;";
            # Prepare store procude
            $stmt = $storage->prepare($sql);

            $stmt->bindParam($sKey, PDO::PARAM_STR);
            $stmt->execute();

            # Fetch All Result
            $arrResult = $stmt->fetchColumn();

            # Free cursor
            $stmt->closeCursor();

            //Return data
            $arrResult = $arrResult            

        } catch (Exception $ex) {
            //ErrorLog::getInstance()->insert(__CLASS__, __FUNCTION__, $ex->getMessage());
            return array();
        }

        // return data
        return $arrResult;
    }
    
    public function getToken($iAccountID, $sUsername, $sPs, $sIpClient)
    {
    
        $arrResult = array();

        try {

            # Get Data Master Global
            $storage = Core_Global::getDbGlobalSlave();

            $sql = "SELECT `key`
                    FROM `token`
                    WHERE `account_id` = ?
                    AND `username` = ?
                    AND `ps` = ?
                    AND (`IPClient` = ? OR `IPOwner` = ?)
                    AND NOW() >= `update_date` + `expired`
                    LIMIT 1;";
            # Prepare store procude
            $stmt = $storage->prepare($sql);

            $stmt->bindParam($iAccountID, PDO::PARAM_INT);
            $stmt->bindParam($sUsername, PDO::PARAM_STR);
            $stmt->bindParam($sPs, PDO::PARAM_STR);
            $stmt->bindParam($sIPClient, PDO::PARAM_STR);
            $stmt->bindParam($sIPClient, PDO::PARAM_STR);
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