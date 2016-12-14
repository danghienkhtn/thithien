<?php
/**
 * @author      :   Hiennd
 * @name        :   ApiController
 * @version     :   20161214
 * @copyright   :   Dahi
 * @todo        :   controller API 
 */
class Api_UserController extends Zend_Controller_Action
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
    
    public function checkUserExistedAction()
    {        
     
        // $token = $this->_getParam('token', '');
        $sEmail = $this->_getParam('sEmail', '');
        $arrReturn = array();
        if(!empty($sEmail))
        {           
            $arrAcc = AccountInfo::getInstance()->getAccountInfoByEmail($sEmail);
            if($arrAcc || (is_array($arrAcc) && sizeof($arrAcc) > 0)){
                echo Zend_Json::encode(Core_Server::setOutputData(true, 'Tai khoan da ton tai, vui long dang nhap voi mat khau.', array()));
                exit;
            }
                        
            echo Zend_Json::encode(Core_Server::setOutputData(false, 'OK', array()));
            exit;    
        }    
        else{
            echo Zend_Json::encode(Core_Server::setOutputData(true, 'Email is null', array()));
            exit;            
        }
    }
    
    public function registAction()
    {             
        $sFullname = $this->_getParam('sFullname', '');
        $sEmail = $this->_getParam('sEmail', '');
        $sPassword = $this->_getParam('sPassword', '');
        $sConfirmpassword = $this->_getParam('sConfirmpassword', '');
        $sRecaptcha = $this->_getParam('sRecaptcha', '');
        $arrReturn = array();
        $bCaptcha = true;        
        if($this->_request->isXmlHttpRequest()){
            error_log("ajax here_".Zend_Json::encode($this->_request->getParam('sEmail')));
        }
        else if($this->_request->isPost()){
            error_log("post is here ". $this->_getParam('sEmail'));
            error_log($this->_request->getParam('sEmail'));
        }    
        else{
            error_log("submit other....");
        }
        if(!Core_Validate::checkEmail($sEmail)){
            echo Zend_Json::encode(Core_Server::setOutputData(true, 'Nhập lại email', array()));
            exit;
        }

        if(!Core_Validate::checkPassword($sPassword)){
            echo Zend_Json::encode(Core_Server::setOutputData(true, 'Nhap lai mat khau', array()));
            exit;
        }            

        if(!Core_Validate::checkPassword($sConfirmpassword) || $sPassword !== $sConfirmpassword){
            echo Zend_Json::encode(Core_Server::setOutputData(true, 'Nhap lai xac nhan mat khau', array()));
            exit;
        }

        if(!Core_Validate::checkNormalText($sFullname)){
            echo Zend_Json::encode(Core_Server::setOutputData(true, 'Nhập lại ten', array()));
            exit;
        }

        $iSubmitTime = new Zend_Session_Namespace('RegistTime');
        if(isset($iSubmitTime->time) && $this->_request->isPost())
            $iSubmitTime->time++;

        $iSubmitTime->time = isset($iSubmitTime->time) ? $iSubmitTime->time : 0;

        if($iSubmitTime->time >= 4){
            $data = array(
                "secret" => "6Lc8Ww4UAAAAAKLadWT-J3Rfwwea-_4vE-CIOorN",
                "response" => $sRecaptcha
            );
            $url_send ="https://www.google.com/recaptcha/api/siteverify?secret=6Lc8Ww4UAAAAAKLadWT-J3Rfwwea-_4vE-CIOorN&response=".$reCaptcha;
            $str_data = Zend_Json::encode($data);
            $responseCaptcha = Core_Common::sendPostData($url_send, $str_data);
            $responseCaptcha = Zend_Json::decode($responseCaptcha);
            $bCaptcha = $responseCaptcha['success'];
            if(!$bCaptcha){
                error_log("Sai chứng thức, vui lòng thử lại!");
                echo Zend_Json::encode(Core_Server::setOutputData(true, 'Sai chứng thức, vui lòng thử lại!', array()));
                exit;
            }
            else{
                error_log("Chung thuc ok");
            }
        }
        $arrAcc = AccountInfo::getInstance()->getAccountInfoByEmail($sEmail);
        if($arrAcc || (is_array($arrAcc) && sizeof($arrAcc) > 0)){
// error_log("here_".Zend_Json::encode($arrAcc));                    
            echo Zend_Json::encode(Core_Server::setOutputData(true, 'Tai khoan da ton tai, vui long dang nhap voi mat khau.', array()));
            exit;   
        }
        else{
            $arrAcc = array();
            $arrAcc["username"]=$sEmail;
            $arrAcc["password"]=md5($sPassword);
            $arrAcc["name"]=$sFullname;
            $arrAcc["email"]=$sEmail;
            $arrAcc["phone"]="";
            $arrAcc["avatar"]="";
            $arrAcc["picture"]="";
            $arrAcc["address"]="";
            $arrAcc["level"]=0;
            $arrAcc["is_admin"]=0;
            $arrAcc["active"]=1;
            $arrAcc["status"]=1;                    
            $inserted = AccountInfo::getInstance()->insertAccountInfo($arrAcc);
            error_log("new user:".$inserted);
            if($inserted > 0){
                echo Zend_Json::encode(Core_Server::setOutputData(false, 'Dang ky hoan tat', array("userId" =>"$inserted")));
                exit;                
            }
            else{
                echo Zend_Json::encode(Core_Server::setOutputData(true, 'Co loi xay ra', array()));
                exit;   
            }
        }
    }   
    
}

