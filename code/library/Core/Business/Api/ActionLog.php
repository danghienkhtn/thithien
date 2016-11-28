<?php
/**
 * @author      :   HoaiTN
 * @name        :   Core_Business_Api_Album
 * @version     :   20101111
 * @copyright   :   My company
 * @todo        :   Using for account service
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
         
        try {     

        	//connect mongo
        	$connection = Core_Global::getMongoInstance();
        	$collection = Core_Common::getCollection($connection,'actionlog');
        	
        	$collection->insert($arrData);
        	$sID = (string)$arrData['_id'];

        } catch (Exception $ex) {
            ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage());
            print_r($ex->getMessage());
        }

        // return data
        return $sID;
    }
    
    public function select($sAccountName, $sStartDate, $sEndDate, $iType, $iAction, $iStart, $iLimit)
    {

    	$result = array();
    	$cursor = array();
    	 
    	try {
    
    		//connect mongo
    		$connection = Core_Global::getMongoInstance();
    		$collection = Core_Common::getCollection($connection,'actionlog');
    		
    		$query = array();
    		
    		if(!empty($sAccountName)){

//    			$query['account_name'] = new MongoRegex("/$sAccountName/i");
    			$query['note'] = new MongoRegex("/$sAccountName/i");
    		}
    		
    		if($iType > 0){
    			$query['type'] = $iType;
    		}
    		
    		if($iAction > 0){
    			$query['action'] = $iAction;
    		}
    		
    		if(!empty($sStartDate) && !empty($sEndDate)){
    			$query['created'] = array('$gte' => new MongoDate($sStartDate), '$lte' => new MongoDate($sEndDate));
    		}
    		
    		
    		$iTotal = $collection->count($query);
    		$cursor = $collection->find($query)->sort(array('created' => -1))->skip($iStart)->limit($iLimit);
    		
    		$result = array('total' => $iTotal, 'data' => $cursor);
    		
    
    	} catch (Exception $ex) {
            ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),'',$sAccountName);
    		print_r($ex->getMessage());
    	}
    
    	// return data
    	return $result;
    }


    public function selectById($sId)
    {

        $result = array();
        $cursor = array();

        try {

            //connect mongo
            $connection = Core_Global::getMongoInstance();
            $collection = Core_Common::getCollection($connection,'actionlog');

            $result = $collection->findOne(array('_id' => new MongoId($sId)));

        } catch (Exception $ex) {
            print_r($ex->getMessage());
        }

        // return data
        return $result;
    }

    public function getActionLog($sAccountId, $iType, $iAction, $iStart, $iLimit)
    {

        $result = array();
        $cursor = array();
         
        try {
    
            //connect mongo
            $connection = Core_Global::getMongoInstance();
            $collection = Core_Common::getCollection($connection,'actionlog');
            
            $query = array();
            
            if($sAccountId > 0){
                $query['account_id'] = $sAccountId;
            }
            
            if($iType > 0){
                $query['type'] = $iType;
            }
            
            if($iAction > 0){
                $query['action'] = $iAction;
            }
                                    
            
            $iTotal = $collection->count($query);
            $cursor = $collection->find($query)->sort(array('created' => -1))->skip($iStart)->limit($iLimit);
            
            $result = array('total' => $iTotal, 'data' => iterator_to_array($cursor));
            
    
        } catch (Exception $ex) {
            ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),'',$sAccountId);
            print_r($ex->getMessage());
        }
    
        // return data
        return $result;
    }
}