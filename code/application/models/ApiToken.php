<?php

/**
 * @author      :   HoaiTN
 * @name        :   Model_API
 * @version     :   201011
 * @copyright   :   My company
 * @todo        :   Api model
 */
class ApiToken{

   
    private static $sToken = 'FDFRGDdfdhfsjfhsj';


    public function generateToken()
    {
        $sToken = Core_Guuid::generateNoSpace(Core_Guuid::UUID_TIME, Core_Guuid::FMT_STRING, "InternalProject", Core_Utility::getAltIp());
        $iExpired = 0;
        Core_Cookie::setCookie(TOKEN_API, $sToken, $iExpired, '/', DOMAIN, false, true);

        echo $sToken;
        exit();
    }

	public function generateDocsToken()
    {
    	$arrLogin = Admin::getInstance()->getLogin();
        if(!isset($arrLogin['accountID']) || empty($arrLogin['accountID']))
            return Core_Server::setOutputData(true, 'You must login!', array());
        $iType = "docs";
        $iAccountID = $arrLogin['accountID'];
        $iUsername = $arrLogin['username'];
        $sAvatar = $arrLogin['avatar'];
        $iPs = $arrLogin['ps'];
        $iIPOwner = Core_Utility::getAltIp();       
        $iExpired = DOC_TOKEN_EXPIRED;
        // Core_Cookie::setCookie(TOKEN_API, $sToken, $iExpired, '/', DOMAIN, false, true);        
        echo Token::getInstance()->generateToken($iType, $iAccountID, $iUsername, $sAvatar, $iPs, $iIPOwner, DOC_SERV_IP, $iExpired);        
        exit();
    }

    public static function checkDocsToken($token)
    {
    	$arrReturn = array();
    	if(!empty($token))
    	{	    	
	        $iType = "docs";	        
	        $iIPClient = Core_Utility::getAltIp();       
	        $arrReturn = Token::getInstance()->select($token, $iType, $iIPClient);
            if(!is_null($arrReturn))
            {
                $iExpired = DOC_TOKEN_EXPIRED;
                Token::getInstance()->update(array("key"=>$token), array("expired" => new MongoDate(time() + (int)$iExpired)));
            }    
	        return Core_Server::setOutputData(false, 'OK', $arrReturn);
	    }    
	    else
	    	return Core_Server::setOutputData(true, 'Token is null', array());	        
    }

    public static function checkToken($iType, $token)
    {
        $arrReturn = array();
        if(!empty($token))
        {           
            // $iType = "docs";            
            $iIPClient = Core_Utility::getAltIp();       
            $arrReturn = Token::getInstance()->select($token, $iType, $iIPClient);
            if(sizeof($arrReturn) > 0)
            {
                $iExpired = $arrReturn['expired']->sec;
                 if($iExpired < time()){
                     return Core_Server::setOutputData(true, 'Token is expired', $arrReturn);
                 }

                $iExpired = DOC_TOKEN_EXPIRED;
                Token::getInstance()->update(array("key"=>$token), array("expired" => new MongoDate(time() + (int)$iExpired)));
                return Core_Server::setOutputData(false, 'OK', $arrReturn);
            }    
            return Core_Server::setOutputData(true, 'Token not existed', array());
        }    
        else
            return Core_Server::setOutputData(true, 'Token is null', array());          
    }
}
