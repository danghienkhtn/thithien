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
    public function insert($arrData) 
    {
        try {
            $arrData = Validate::encodeValues($arrData);
        	//connect mongo
        	$connection = Core_Global::getMongoInstance();
        	$collection = Core_Common::getCollection($connection,'Token');

        	$collection->insert($arrData);

        } catch (Exception $ex) {
            ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),'','');
            return false;
        }

        // return data
        return true;
    }
    
    /**
     * update
     * @param array $query
     * @param array $update
     * @return boolean
     */
    public function update($query, $update)
    {
    	$flag = FALSE;
    	try {
    
    		//connect mongo
    		$connection = Core_Global::getMongoInstance();
    		$collection = Core_Common::getCollection($connection,'Token');
    
    		$result = $collection->update(
    				$query,
    				array('$set' => $update)
    		);
    		
    		if(empty($result['err'])){
    			$flag = TRUE;
    		}
    
    	} catch (Exception $ex) {
            Core_Common::var_dump($ex);
    		ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),'','');
    	}
    	
    	return $flag;
    }
    
    /**
     * delete file
     * 
     * @param int $iID
     * @return boolean
     */
    public function delete($iKey){
    	
    	$flag = FALSE;
    	
    	try {
    	
    		//connect mongo
    		$connection = Core_Global::getMongoInstance();
    		$collection = Core_Common::getCollection($connection,'Token');
    	
    		$flag = $collection->remove(array('key' => $iKey));
    	
    	} catch (Exception $ex) {
    		ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),'','');
    	}
    	 
    	return $flag;
    	
    	
    }        
    
    public function select($query = array())
    {
    
    	$arrResult = array();    	
    
    	try {
    
    		//connect mongo
    		$connection = Core_Global::getMongoInstance();
			$collection = Core_Common::getCollection($connection,'Token');
    		    		
    		$arrResult = $collection->findOne($query);
            $arrResult = is_null($arrResult) ? array() : $arrResult;
    
    
    	} catch (Exception $ex) {
    		ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),'','');
    	}
    
    	// return data
    	return $arrResult;
    }
    
    
    

  
}