<?php
/**
 * @author      :   HoaiTN
 * @name        :   Core_Business_Api_Album
 * @version     :   20101111
 * @copyright   :   My company
 * @todo        :   Using for account service
 */
class Core_Business_Api_ErrorLog
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
        	$collection = Core_Common::getCollection($connection,'errorlog');
        	
        	$collection->insert($arrData);
        	$sID = (string)$arrData['_id'];

        } catch (Exception $ex) {
            print_r($ex->getMessage());
        }

        // return data
        return $sID;
    }
    
    public function select($sModel, $sName, $sStartDate, $sEndDate, $iStart, $iLimit)
    {

    	$result = array();
    	$cursor = array();
    	 
    	try {
    
    		//connect mongo
    		$connection = Core_Global::getMongoInstance();
    		$collection = Core_Common::getCollection($connection,'errorlog');
    		
    		$query = array();


    		if(!empty($sModel)){
    			$query['model'] = new MongoRegex("/$sModel/i");
    		}

            if(!empty($sName)) {
                $query['model'] = new MongoRegex("/$sName/i");
            }
    		
    		if(!empty($sStartDate) && !empty($sEndDate)){
    			$query['created'] = array('$gte' => new MongoDate($sStartDate), '$lte' => new MongoDate($sEndDate));
    		}
    		
    		
    		$iTotal = $collection->count($query);
    		$cursor = $collection->find($query)->sort(array('created' => -1))->skip($iStart)->limit($iLimit);
    		
    		$result = array('total' => $iTotal, 'data' => $cursor);
    		
    
    	} catch (Exception $ex) {
    		print_r($ex->getMessage());
    	}
    
    	// return data
    	return $result;
    }
    

  
}