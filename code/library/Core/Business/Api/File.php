<?php
/**

 * @todo        :   Using for account service
 */
class Core_Business_Api_File
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
        	 $collection = Core_Common::getCollection($connection,'file');

        	$collection->insert($arrData);

        } catch (Exception $ex) {
            ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),'','');
        }

        // return data
        return $arrData['_id'];
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
    		 $collection = Core_Common::getCollection($connection,'file');
    
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
    public function delete($iID){
    	
    	$flag = FALSE;
    	
    	try {
    	
    		//connect mongo
    		$connection = Core_Global::getMongoInstance();
    		 $collection = Core_Common::getCollection($connection,'file');
    	
    		$flag = $collection->remove(array('_id' => $iID));
    	
    	} catch (Exception $ex) {
    		ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),'','');
    	}
    	 
    	return $flag;
    	
    	
    }
    
    public function selectOne($query)
    {
    
    	$arrResult = array();
    
    	try {
    
    		//connect mongo
    		$connection = Core_Global::getMongoInstance();
    		 $collection = Core_Common::getCollection($connection,'file');
    
    		$arrResult = $collection->findOne($query);
    		$arrResult = is_null($arrResult) ? array() : $arrResult;
    
    	} catch (Exception $ex) {
    		ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),'','');
    	}
    

    	// return data
    	return $arrResult;
    }
    
    public function select($iStart, $iLimit, $query = array(), $sort = array())
    {
    
    	$result = array();
    	$cursor = array();
    
    	try {
    
    		//connect mongo
    		$connection = Core_Global::getMongoInstance();
			$collection = Core_Common::getCollection($connection,'file');

    		$iTotal = $collection->count($query);
    		
    		$cursor = $collection->find($query)->sort($sort)->skip($iStart)->limit($iLimit);
    		$result = array('total' => $iTotal, 'data' => iterator_to_array($cursor));
    
    
    	} catch (Exception $ex) {
    		ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),'','');
    	}
    
    	// return data
    	return $result;
    }
    
    public function selectAll($query = array(), $sort = array())
    {
    
        $result = array();
        $cursor = array();
    
        try {
    
            //connect mongo
            $connection = Core_Global::getMongoInstance();
            $collection = Core_Common::getCollection($connection,'file');

            $iTotal = $collection->count($query);
            
            $cursor = $collection->find($query)->sort($sort);
            $result = array('total' => $iTotal, 'data' => iterator_to_array($cursor));
    
    
        } catch (Exception $ex) {
            ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),'','');
        }
    
        // return data
        return $result;
    }    
}