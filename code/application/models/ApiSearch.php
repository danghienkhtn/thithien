<?php

/**
 * @author      :   Hien.nd
 * @name        :   Model_API
 * @version     :   201607
 * @copyright   :   Gianty
 * @todo        :   Api model
 */
class ApiSearch{
    
    public function searchGroupMemberByMemberKey($iMemberId, $sKey, $iOffset, $iLimit) {
         
        $arrResult = array();
         
        try {
    
            # Get Data Master Global
                $storage = Core_Global::getDbGlobalSlave();
    
                # Prepare store procude
                $stmt = $storage->prepare("CALL sp_group_member_select_by_account_id_and_key(:p_account_id, :p_key, :p_offset, :p_limit, @p_RowCount)");
    
                $stmt->bindParam('p_account_id', $iMemberId, PDO::PARAM_INT);
                $stmt->bindParam('p_key', $sKey, PDO::PARAM_STR);
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
    
    
        } catch (Exception $ex) {
            ErrorLog::getInstance()->insert(__CLASS__, __FUNCTION__, $ex->getMessage());
        }
    
        // return data
        return $arrResult;
    }
    public function searchAccountInfoListByKey($sKey, $iOffset, $iLimit) {

        $arrResult = array('data'=>array(),'total'=>0);

        try {

            # Get Data Master Global
            $storage = Core_Global::getDbGlobalSlave();

            # Prepare store procude
            $stmt = $storage->prepare("CALL sp_account_info_search_key(:p_key, :p_offset, :p_limit, @p_RowCount)");
            $stmt->bindParam('p_key',$sKey, PDO::PARAM_STR);
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


        } catch (Exception $ex) {
            ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),0,$sName);
        }

        // return data
        return $arrResult;
    }    

}
