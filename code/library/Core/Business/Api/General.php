<?php
/**
 * @author      :   HoaiTN
 * @name        :   Core_Business_Api_General
 * @version     :   20101111
 * @copyright   :   My company
 * @todo        :   Using for account service
 */
class Core_Business_Api_General
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
    public function insertGeneral($sName, $iType, $iSortOrder, $iActive) 
    {
         //init return result
        $result = 0;
        $sName = Validate::encodeValues($sName);
        try {
            
            $iUpdateDate = time();
            
            # Get Data Master Global
            $storage = Core_Global::getDbGlobalMaster();
                    
            # Prepare store procude
            $stmt = $storage->prepare("CALL sp_general_insert(:p_name,:p_type,:p_sort_order,
             :p_active,:p_update_date,@p_RowCount)");
            
            $stmt->bindParam('p_name', $sName, PDO::PARAM_STR);
            $stmt->bindParam('p_type', $iType, PDO::PARAM_INT);
            $stmt->bindParam('p_sort_order', $iSortOrder, PDO::PARAM_INT);
            $stmt->bindParam('p_active', $iActive, PDO::PARAM_INT);
            $stmt->bindParam('p_update_date', $iUpdateDate, PDO::PARAM_INT);
            $stmt->execute();

            # Fetch All Result
            $stmt = $storage->query("SELECT @p_RowCount");
            $result = $stmt->fetchColumn();

            # Free cursor
            $stmt->closeCursor();
        } catch (Exception $ex) {
            ErrorLog::getInstance()->insert(__CLASS__, __FUNCTION__, $ex->getMessage());
            $result = 0;
        }

        // return data
        return $result;
    }
    
    /*
     *  Update Account Info
     */
    
    /**
     * @return <int>
     */
    public function updateGeneral($iID, $sName, $iType, $iSortOrder, $iActive) 
    {
         //init return result
        $result = 0;
        $sName = Validate::encodeValues($sName);
        try {
            
            $iUpdateDate = time();
            
            # Get Data Master Global
            $storage = Core_Global::getDbGlobalMaster();
                    
            # Prepare store procude
            $stmt = $storage->prepare("CALL sp_general_update(:p_general_id, :p_name,:p_type,:p_sort_order,:p_active
                 ,:p_update_date,@p_RowCount)");
            
            $stmt->bindParam('p_general_id', $iID, PDO::PARAM_INT);
            $stmt->bindParam('p_name', $sName, PDO::PARAM_STR);
            $stmt->bindParam('p_type', $iType, PDO::PARAM_INT);
            $stmt->bindParam('p_sort_order', $iSortOrder, PDO::PARAM_INT);
            $stmt->bindParam('p_active', $iActive, PDO::PARAM_INT);
            $stmt->bindParam('p_update_date', $iUpdateDate, PDO::PARAM_INT);
            $stmt->execute();

            # Fetch All Result
            $stmt = $storage->query("SELECT @p_RowCount");
            $result = $stmt->fetchColumn();

            # Free cursor
            $stmt->closeCursor();
        } catch (Exception $ex) {
            ErrorLog::getInstance()->insert(__CLASS__, __FUNCTION__, $ex->getMessage());
            $result = 0;
            //var_dump($ex->getMessage());
        }
        //echo  $result; exit;

        // return data
        return $result;
    }


    /**
     * @todo  Remove event
     * @param <int> $iEventId
     * @return <int>
     */
    public function removeGeneral($iID) {
        try {
            # Get Data Master Global
            $storage = Core_Global::getDbGlobalMaster();

            # Prepare store procude
            $stmt = $storage->prepare("CALL sp_general_delete(:p_general_id, @p_RowCount)");
            $stmt->bindParam('p_general_id', $iID, PDO::PARAM_INT);
            $stmt->execute();

            # Fetch All Result
            $stmt = $storage->query("SELECT @p_RowCount");
            $result = $stmt->fetchColumn();

            # Free cursor
            $stmt->closeCursor();
        } catch (Exception $ex) {
            ErrorLog::getInstance()->insert(__CLASS__, __FUNCTION__, $ex->getMessage());
            $result = -1;
        }

        // return data
        return $result;
    }

    /**
     * @return <array>
     */
                 
    public function getGeneralList($sName,$iType, $iActive, $iOffset, $iLimit, $iSort=0) {
       
        $arrResult = array();
        $iSort = intval($iSort); 
         
        try {          
                        
            # Get Data Master Global
            $storage = Core_Global::getDbGlobalSlave();

            # Prepare store procude
            $stmt = $storage->prepare("CALL sp_general_select(:p_name,:p_type,:p_active, :p_is_sort, :p_offset, :p_limit, @p_RowCount)");
            
            $stmt->bindParam('p_name', $sName, PDO::PARAM_STR);
            $stmt->bindParam('p_type', $iType, PDO::PARAM_INT);
            $stmt->bindParam('p_active', $iActive, PDO::PARAM_INT);

            $stmt->bindParam('p_offset', $iOffset, PDO::PARAM_INT);
            $stmt->bindParam('p_limit', $iLimit, PDO::PARAM_INT);
            $stmt->bindParam('p_is_sort', $iSort, PDO::PARAM_INT);
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
            
            
        } catch (Exception $ex) {
            ErrorLog::getInstance()->insert(__CLASS__, __FUNCTION__, $ex->getMessage());
           
        }

        // return data
        return $arrResult;
    } 
    
    public function selectByNameAndType($sName,$iType) {
    	 
    	$arrResult = array();
 	 
    	try {
    
    		# Get Data Master Global
    			$storage = Core_Global::getDbGlobalSlave();
    
    			# Prepare store procude
    			$stmt = $storage->prepare("CALL sp_general_select_by_name_type(:p_name,:p_type)");
    
            	$stmt->bindParam('p_name', $sName, PDO::PARAM_STR);
    			$stmt->bindParam('p_type', $iType, PDO::PARAM_INT);
    			
    			$stmt->execute();
    			$arrResult = $stmt->fetch();

    			$stmt->closeCursor();
    
    
    
    	} catch (Exception $ex) {
            ErrorLog::getInstance()->insert(__CLASS__, __FUNCTION__, $ex->getMessage());
    		print_r($ex->getMessage());
    		
    	}
    
    // return data
    return $arrResult;
    }
    
    /*
     * Select By ID
     */
    public function getGeneralByID($iID) {
        try {

            # Get Data Master Global
            $storage = Core_Global::getDbGlobalSlave();

            # Prepare store procude
            $stmt = $storage->prepare("CALL sp_general_select_byid(:p_general_id, @p_RowCount)");
            $stmt->bindParam('p_general_id', $iID, PDO::PARAM_INT);
            $stmt->execute();

            # Fetch All Result
            $arrResult = $stmt->fetch();

            # Free cursor
            $stmt->closeCursor();
        } catch (Exception $ex) {
            ErrorLog::getInstance()->insert(__CLASS__, __FUNCTION__, $ex->getMessage());
            $arrResult = array();
        }

        // return data
        return $arrResult;
    }
    
    
}