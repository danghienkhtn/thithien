<?php
/**
 * @author      :   Hien.nd
 * @version     :   20160712
 * @copyright   :   Gianty
 * @todo        :   Token model
 */
class Token
{

	/*const FILE_DETAIL_KEY = 'file_detail_key';
	const FILE_DETAIL_EXPIRED = 'file_detail_expired';

	const FILE_LIST_KEY = 'file_list_key';
	
	const REDIS_FEED_FILE_LIST = 'redis_filefeed_list_key';*/
    /**
     * @var type 
     */
    protected static $_instance = null;
    
    private $_modeParent        = null;    

    
  
    /**
     * Constructor of class
     * we don't permit an explicit call of the constructor! (like $v = new Singleton())
     */
    protected function __construct() {
        // Int Parent Model
        $this->_modeParent = Core_Business_Api_Token::getInstance();        
    }
 

    /**
     * Get singletom instance
     * @return <object>
     */
    public final static function getInstance()
    {        
        // Check Instance
        if(is_null(self::$_instance))
        {
            self::$_instance = new self();
        }

        // Return Instance
        return self::$_instance;
    }
    
    /**
     * insert file
     * @param string $sName
     * @param string $sPath
     * @param int $iType '0: file, 1: folder'
     * @param int $iParent '0: is parent'
     * @param int $iOwner 'creater'
     * @param int $iCreated
     * @param int $iUpdated
     * @return int id
     */
    public function insert($sKey, $iType, $iAccountID, $iUsername, $sAvatar, $iPs, $iIPOwner, $iIPClient, $iExpired = 3600)
    {
        $time = time();
        $mongoDate = new MongoDate($time);        
        $Expired = new MongoDate($time + (int)$iExpired);
    	$arrData = array(    			
    			"key" => $sKey,    			
    			'type' => $iType,// for docs=> type = docs    			
                "account_id" => $iAccountID,
    			"username" => $iUsername,
                "avatar" => $sAvatar,
                "ps" => $iPs,
    			"IPOwner" => $iIPOwner, //Ip người tao
                "IPClient" => $iIPClient,// IP người có thể dùng: *=>all
                "expired" => $Expired,
    			"updated" => $mongoDate,
    			"created" => $mongoDate
    	
    	);

    	$iID = $this->_modeParent->insert($arrData);    	    	
    	return $iID;
    }

    public function update($query,$key)
    {        
        return $this->_modeParent->update($query,$key);
    }
    
    /**
     * 
     * @param int $iStart
     * @param int $iLimit
     * @param string $sName
     * @param array $arrFileIds
     * return  array('total' => $iTotal, 'data' => $cursor);
     */
    public function select($sKey = '', $iType = "", $iIPClient = "", $iIPOwner = "")
    {
        $query = array();        
                
        //search key
        if(!empty($sKey)){
            $query['key'] = $sKey;
        }

        //search type
        if(!empty($iType)){
            $query['type'] = $iType;
        }
        
        //IP Client
        if(!empty($iIPClient)){
            $query['$or'] = array( array('IPClient' => $iIPClient),  array('IPOwner' => $iIPClient));
        }
        
        //IP owner
        if(!empty($iIPOwner)){
            $query['IPOwner'] = $iIPOwner;
        }
        
        $query['expired'] = array('$gte'=> new MongoDate());

        return $this->_modeParent->select($query);
    }

    /**
     * 
     * @param string $sName
     * @param array $arrFileIds
     * return  array('total' => $iTotal, 'data' => $cursor);
     */
    public function generateToken($iType="docs", $iAccountID, $iUsername="", $sAvatar = "", $iPs="", $iIPOwner="", $iIPClient="", $iExpired = 3600)
    {
        $query = array();                                        
        //search type
        if(!empty($iType)){
            $query['type'] = $iType;
        }
        
        //search accountId
        if(!empty($iAccountID)){
            $query['account_id'] = $iAccountID;
        }

        //search username
        if(!empty($iUsername)){
            $query['username'] = $iUsername;
        }

        //search ps
        if(!empty($iPs)){
            $query['ps'] = $iPs;
        }

        //IP Client
        if(!empty($iIPClient)){
            $query['IPClient'] = $iIPClient;
        }
        
        //IP owner
        if(!empty($iIPOwner)){
            $query['IPOwner'] = $iIPOwner;
        }
                
        $arrToken = $this->_modeParent->select($query);
        if(sizeof($arrToken) > 0){
            $currTime = time();
            if($currTime < $arrToken['expired']->sec){//token is not expired                
                $this->update(array("key"=>$arrToken["key"]), array("expired" => new MongoDate($currTime + (int)$iExpired)));
                return $arrToken["key"];
            }
            else{
                $this->delete($arrToken['key']);
            }                
        }
        else{
            $sToken = Core_Guuid::generateNoSpace(Core_Guuid::UUID_TIME, Core_Guuid::FMT_STRING, "Documents", Core_Utility::getAltIp());
            $inserted = $this -> insert($sToken, $iType, $iAccountID, $iUsername, $sAvatar, $iPs, $iIPOwner, $iIPClient, $iExpired);
            if($inserted)
                return $sToken;
            else
                return "";            
        }
    }

    /**
     * delete token
     *
     * @param string $key
     * @return boolean
     */
    public function delete($ikey){    	
    	$result = $this->_modeParent->delete($ikey);    	    	
    }        
}
