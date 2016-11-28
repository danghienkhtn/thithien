<?php
/**
 * @author      :   HoaiTN
 * @name        :   Core_Business_Api_Photo
 * @version     :   20101111
 * @copyright   :   My company
 * @todo        :   Using for account service
 */
class Core_Business_Api_Photo
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
//    public function addPhoto($sImage,$iAlbumID,$iHot, $iActive)
    public function addPhoto($arrData)
    {
         //init return result
        $result = 0;
        
        
        try {
            $arrData = Validate::encodeValues($arrData);
            $iUpdateDate = time();
            
            # Get Data Master Global
            $storage = Core_Global::getDbGlobalMaster();
                    
            # Prepare store procude
            $stmt = $storage->prepare("CALL sp_photo_insert(:p_image_url,:p_album_id,:p_ishot,:p_active,:p_create_date,@p_RowCount)");
            
            $stmt->bindParam('p_image_url', $arrData['image_url'], PDO::PARAM_STR);
            $stmt->bindParam('p_album_id', $arrData['album_id'], PDO::PARAM_INT);
            $stmt->bindParam('p_ishot', $arrData['ishot'], PDO::PARAM_INT);
            $stmt->bindParam('p_active', $arrData['active'], PDO::PARAM_INT);
            $stmt->bindParam('p_create_date', $iUpdateDate, PDO::PARAM_INT);
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
     /**
     * @return <int>
     */
    public function updatePhoto($iPhotoID,$sImage,$iAlbumID,$iHot, $iActive)
    {
         //init return result
        $result = 0;
        
        // return data
        return $result;
    }
    
       /**
     * @return <int>
     */
    public function updatePhotoByAlbumStatus($iAlbumID, $iActive) 
    {
         //init return result
        $result = 0;
        
        
        try {
            
            # Get Data Master Global
            $storage = Core_Global::getDbGlobalMaster();
                    
            # Prepare store procude
            $stmt = $storage->prepare("CALL sp_photo_update_byalbum_status(:p_album_id,:p_active,@p_RowCount)");
            
         
            $stmt->bindParam('p_album_id', $iAlbumID, PDO::PARAM_INT);
            $stmt->bindParam('p_active', $iActive, PDO::PARAM_INT);
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
    
           /**
     * @return <int>
     */
    public function updatePhotoStatus($iPhotoID, $iActive) 
    {
         //init return result
        $result = 0;
        
        
        try {
            
            # Get Data Master Global
            $storage = Core_Global::getDbGlobalMaster();
                    
            # Prepare store procude
            $stmt = $storage->prepare("CALL sp_photo_update_status(:p_photo_id,:p_active,@p_RowCount)");
            
         
            $stmt->bindParam('p_photo_id', $iPhotoID, PDO::PARAM_INT);
            $stmt->bindParam('p_active', $iActive, PDO::PARAM_INT);
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
    
    
    

    /**
     * @todo  Remove event
     * @param <int> $iEventId
     * @return <int>
     */
    public function removePhoto($iID) {
        try {
            # Get Data Master Global
            $storage = Core_Global::getDbGlobalMaster();

            # Prepare store procude
            $stmt = $storage->prepare("CALL sp_photo_delete(:p_photo_id, @p_RowCount)");
            $stmt->bindParam('p_photo_id', $iID, PDO::PARAM_INT);
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
              
             
    public function getPhotoList($iAlbumID,$iActive, $iOffset, $iLimit) {
       
        $arrResult = array();
         
        try {          
                        
            # Get Data Master Global
            $storage = Core_Global::getDbGlobalSlave();

            # Prepare store procude
            $stmt = $storage->prepare("CALL sp_photo_select(:p_album_id,:p_active, :p_offset, :p_limit, @p_RowCount)");
            
            $stmt->bindParam('p_album_id', $iAlbumID, PDO::PARAM_INT);
            $stmt->bindParam('p_active', $iActive, PDO::PARAM_INT);

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
    
    /*
     * Select By ID
     */
    public function getPhotoByID($iID) {
      try {

            # Get Data Master Global
            $storage = Core_Global::getDbGlobalSlave();

            # Prepare store procude
            $stmt = $storage->prepare("CALL sp_photo_select_byid(:p_photo_id, @p_RowCount)");
            $stmt->bindParam('p_photo_id', $iID, PDO::PARAM_INT);
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
    
    public function countPhotoByAlbumId($iAlbumId) {
    	
    	$arrResult = array();
    	try {
    
    		# Get Data Master Global
    		$storage = Core_Global::getDbGlobalSlave();
    
    		# Prepare store procude
    		$stmt = $storage->prepare("CALL sp_photo_count_by_album_id(:p_album_id)");
    		$stmt->bindParam('p_album_id', $iAlbumId, PDO::PARAM_INT);
    		$stmt->execute();
    
    		# Fetch All Result
    		$arrResult = $stmt->fetch();
    
    		# Free cursor
    		$stmt->closeCursor();
    	} catch (Exception $ex) {
    		$arrResult = array();
    	}
    	
    	if(!empty($arrResult)){
            ErrorLog::getInstance()->insert(__CLASS__, __FUNCTION__, $ex->getMessage());
    		return $arrResult['total'];
    	}
    	// return data
    	return 0;
    }
    
    public function getPhotoByAlbumId($iAlbumID, $iOffset, $iLimit) {
    	 
    	$arrResult = array();
    	 
    	try {
    
    		# Get Data Master Global
    			$storage = Core_Global::getDbGlobalSlave();
    
    			# Prepare store procude
    			$stmt = $storage->prepare("CALL sp_photo_select_by_album_id(:p_album_id,:p_offset, :p_limit, @p_RowCount)");
    
            	$stmt->bindParam('p_album_id', $iAlbumID, PDO::PARAM_INT);
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
    
    
    
    
}