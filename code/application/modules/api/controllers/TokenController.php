<?php
/**
 * @author      :   Hoaitn
 * @name        :   ApiController
 * @version     :   20110214
 * @copyright   :   My company
 * @todo        :   controller API 
 */
class Api_TokenController extends Zend_Controller_Action
{
    
    /**
     * init of controller
     */
    public function init()
    {
        //Disale layout
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
    }
    
    public function indexAction()
    {

    	//Set option for server
    	$options = array(
    			'adapter' => 'rest'
    	);
    
    	//Get server instance
    	$serverInstance = Core_Server::getInstance($options);
    
    	//Register class call
    	$serverInstance->setClass('ApiToken');
    
    	//Hanlde instance
    	$serverInstance->handle($this->_request);
    }
    public function generateAction()
    {
        $sToken = Core_Guuid::generateNoSpace(Core_Guuid::UUID_TIME, Core_Guuid::FMT_STRING, "InternalProject", Core_Utility::getAltIp());
        $iExpired = 3000;
        Core_Cookie::setCookie(TOKEN_API, $sToken, $iExpired, '/', DOMAIN, false, true);

        echo $sToken;
        exit();
    }
    
    /*public function generateDocsToken()
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
    }*/

    /*public static function checkDocsToken($token)
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
    }*/

    public function checkTokenAction()
    {        
     
        $token = $this->_getParam('token', '');
        $iType = $this->_getParam('type', '');
        $arrReturn = array();
        if(!empty($token))
        {           
            // $iType = "docs";            
            $iIPClient = Core_Utility::getAltIp(); 
            $arrReturn = Token::getInstance()->getToken($token, $iType, $iAccountID = "", $sUsername = "", $sPs = "", $iIPClient);
            if(sizeof($arrReturn) > 0 && $arrReturn !== false)
            {
                $iExpired = $arrReturn['update_date'] + $arrReturn['expired'];
                 if($iExpired < time()){
                    // return Core_Server::setOutputData(true, 'Token is expired', $arrReturn);
                    echo Zend_Json::encode(Core_Server::setOutputData(true, 'Token is expired', array()));
                    exit;
                 }
                // $iExpired = DOC_TOKEN_EXPIRED;
                Token::getInstance()->update($arrReturn['account_id'], $arrReturn['username'], $token);
                echo Zend_Json::encode(Core_Server::setOutputData(false, 'OK', array("token" => $arrReturn["key"], "account_id" => $arrReturn["account_id"], "username" => $arrReturn["username"])));
                exit;
                // return Core_Server::setOutputData(false, 'OK', $arrReturn);
            }
            echo Zend_Json::encode(Core_Server::setOutputData(true, 'Token not existed', array()));
            exit;    
            // return Core_Server::setOutputData(true, 'Token not existed', array());
        }    
        else{
            echo Zend_Json::encode(Core_Server::setOutputData(true, 'Token is null', array()));
            exit;
            // return Core_Server::setOutputData(true, 'Token is null', array());          
        }
    }
   
    
}

