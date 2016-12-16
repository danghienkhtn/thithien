<?php
/**
 * @author      :   Hien.nd
 * @version     :   20161207
 * @copyright   :   Dahi
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
     * insert token
     */
    public function insert($sKey, $iType, $iAccountID, $sEmail, $sAvatar, $iPs, $iIPOwner, $iIPClient, $iExpired = 3600)
    {
        //Get data
        $iResult = $this->_modeParent->insert($sKey, $iType, $iAccountID, $sEmail, $sAvatar, $iPs, $iIPOwner, $iIPClient, $iExpired);
        //Return result data
        return $iResult;  
    }

    public function update($iAccountID,$sEmail,$key)
    {        
        return $this->_modeParent->update($iAccountID,$sEmail,$key);
    }
    
    /**
     * 
     * @param int $iStart
     * @param int $iLimit
     * @param string $sName
     * @param array $arrFileIds
     * return  array('total' => $iTotal, 'data' => $cursor);
     */
    public function getToken($skey = "", $sType = "", $iAccountID = "", $sEmail = "", $sPs = "", $sIpClient = "")
    {
        //Get data
        $iResult = $this->_modeParent->select($sType, $iAccountID, $sEmail, $sPs, $sIpClient, $skey);
// echo Zend_Json::encode($iResult);
        //Return result data
        return $iResult;
    }

    /**
     * 
     * @param string $sName
     * @param array $arrFileIds
     * return  array('total' => $iTotal, 'data' => $cursor);
     */
    public function generateToken($sType="user", $iAccountID, $sEmail="", $sAvatar = "", $iPs="", $sIPOwner="", $sIPClient="", $iExpired = 3600)
    {       
        $arrToken = $this->_modeParent->select($sType, $iAccountID, $sEmail, $iPs, $sIPClient, $sKey = "");
/*if(!is_array($arrToken)){
    error_log("errroooooo_".$arrToken);
}        
else{
    error_log(Zend_Json::encode($arrToken));
}*/
        if(is_array($arrToken)){
            $currTime = time();
            if($currTime < $arrToken['expired'] + $arrToken['update_date'] ){//token is not expired                
// error_log("loi64 ha_");
                $this->update($iAccountID, $sEmail, $arrToken["key"]);                
            }
// error_log("here_".$arrToken["key"]);                            
            return $arrToken["key"];                        
        }
        else{
// error_log("here_1_");            
            $sToken = Core_Guuid::generateNoSpace(Core_Guuid::UUID_TIME, Core_Guuid::FMT_STRING, "user", Core_Utility::getAltIp());
// error_log("gen new token: ". $sToken);            
            $inserted = $this -> insert($sToken, $sType, $iAccountID, $sEmail, $sAvatar, $iPs, $sIPOwner, $sIPClient, $iExpired);                                        
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
