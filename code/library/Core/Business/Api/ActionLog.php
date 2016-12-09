<?php
/**
 * @author      :   Hiennd
 * @name        :   Core_Business_Api_ActionLog
 * @version     :   20161207
 * @copyright   :   My company
 * @todo        :   Using for all service
 */
class Core_Business_Api_ActionLog
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
    public function insert($arrData) 
    {
    	
        $sID = '';
        $result = 0;
        $arrData = Validate::encodeValues($arrData);

        try {     

        	$iCreateDate = time();
            
            # Get Data Master Global
            $storage = Core_Global::getDbGlobalMaster();
            $sql = "INSERT INTO `action_log` ( 
                `log_account_id`,
                `log_type`,
                `log_action`,
                `log_note`,
                `log_useragent`,
                `log_ip`,                                
                `log_create_date`,
                `log_update_date` ) VALUES (
                :p_account_id ,
                :p_type,
                :p_action,
                :p_note,
                :p_useragent,
                :p_ip,                
                :p_create_date,
                :p_update_date
                )";
                    
            # Prepare store procude
            $stmt = $storage->prepare($sql);
            
            $stmt->bindParam(':p_account_id', $arrData['log_account_id'], PDO::PARAM_INT);
            $stmt->bindParam(':p_action', $arrData['log_action'], PDO::PARAM_STR);
            $stmt->bindParam(':p_type', $arrData['log_type'], PDO::PARAM_STR);
            $stmt->bindParam(':p_note', $arrData['log_note'], PDO::PARAM_STR);
            $stmt->bindParam(':p_useragent', $arrData['log_useragent'], PDO::PARAM_STR);
            $stmt->bindParam(':p_ip', $arrData['log_ip'], PDO::PARAM_STR);
            $stmt->bindParam(':p_create_date', $iCreateDate, PDO::PARAM_INT);
            $stmt->bindParam(':p_update_date', $iCreateDate, PDO::PARAM_INT);
                        
            $stmt->execute();

            # Fetch Result            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            

            # Free cursor
            $stmt->closeCursor();

        } catch (Exception $ex) {
            // ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage());
            print_r($ex->getMessage());
            $result = 0;
        }

        // return data
        return $result;
    }
    
    public function count($sAccountID, $sStartDate, $sEndDate, $sType, $sAction)
    {
        $totals = 0;
        $queryWhere = " WHERE 1=1 ";
        $arrParams = array();
         
        try {
    
            # Get Data Master Global
            $storage = Core_Global::getDbGlobalSlave();

            $sql = "SELECT count(`log_id`) as totals
                    FROM `action_log`                    
                    ";

            //search by accountId
            if(!empty($sAccountID)){
                $queryWhere .= " AND `log_account_id` LIKE :p_sAccountID ";
                $arrParams[":p_sAccountID"] = "%".$sAccountID."%";
            }
            
            if(!empty($sStartDate) && !empty($sEndDate)){

                $queryWhere .= " AND `log_create_date` <= :p_sStartDate AND `log_create_date` <= :p_sEndDate ";
                $arrParams[":p_sStartDate"] = $sStartDate;
                $arrParams[":p_sEndDate"] = $sEndDate;
            }
            //search by action
            if(!empty($sAction)){
                $queryWhere .= " AND `log_action` LIKE :p_sAction ";
                $arrParams[":p_sAction"] = "%".$sAction."%";
            }
            //search by type
            if(!empty($sType)){
                $queryWhere .= " AND `log_type` LIKE :p_sType ";
                $arrParams[":p_sType"] = "%".$sType."%";
            }

            $sql .= $queryWhere;

            # Prepare store procude
            $stmt = $storage->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

            $stmt->execute($arrParams);

            # Fetch All Result
            $arrResult = $stmt->fetch();
            
            $totals = $arrResult["totals"];
            # Free cursor
            $stmt->closeCursor();
    
        } catch (Exception $ex) {
            // ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),'',$sAccountName);
            print_r($ex->getMessage());
            $totals = 0;
        }
    
        // return data
        return $totals;
    }

    public function select($sAccountID, $sStartDate, $sEndDate, $sType, $sAction, $iOffset = 0, $iLimit = 20)
    {

    	$arrResult = array();
        $queryWhere = " WHERE 1=1 ";
        $arrParams = array();
    	 
    	try {
    
    		# Get Data Master Global
            $storage = Core_Global::getDbGlobalSlave();

            $sql = "SELECT * 
                    FROM `action_log`                    
                    ";

            //search by accountId
            if(!empty($sAccountID)){
                $queryWhere .= " AND `log_account_id` LIKE :p_sAccountID ";
                $arrParams[":p_sAccountID"] = "%".$sAccountID."%";
            }
    		
    		if(!empty($sStartDate) && !empty($sEndDate)){

    			$queryWhere .= " AND `log_create_date` <= :p_sStartDate AND `log_create_date` <= :p_sEndDate ";
                $arrParams[":p_sStartDate"] = $sStartDate;
                $arrParams[":p_sEndDate"] = $sEndDate;
    		}
    		//search by action
            if(!empty($sAction)){
                $queryWhere .= " AND `log_action` LIKE :p_sAction ";
                $arrParams[":p_sAction"] = "%".$sAction."%";
            }
            //search by type
            if(!empty($sType)){
                $queryWhere .= " AND `log_type` LIKE :p_sType ";
                $arrParams[":p_sType"] = "%".$sType."%";
            }
    		$sql .= $queryWhere;

            $sql .= " LIMIT :p_offset, :p_limit ";
            $arrParams[":p_offset"] = $iOffset;
            $arrParams[":p_limit"] = $iLimit;

            # Prepare store procude
            $stmt = $storage->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

            $stmt->execute($arrParams);

            # Fetch All Result
            $arrResult = $stmt->fetchAll();
            
            // $totals = $arrResult["totals"];
            # Free cursor
            $stmt->closeCursor();
    
    	} catch (Exception $ex) {
            // ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),'',$sAccountName);
    		print_r($ex->getMessage());
            $arrResult = array();
    	}
    
    	// return data
    	return $arrResult;
    }

}