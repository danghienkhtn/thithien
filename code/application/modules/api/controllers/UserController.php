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
// exit($sRecaptcha);

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

        /*if($iSubmitTime->time >= 4){
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
        }*/
        /*$data = array(
            "secret" => "6Lc8Ww4UAAAAAKLadWT-J3Rfwwea-_4vE-CIOorN",
            "response" => $sRecaptcha
        );*/

        //?secret=6Lc8Ww4UAAAAAKLadWT-J3Rfwwea-_4vE-CIOorN&response=".$reCaptcha;
        $url_send ="https://www.google.com/recaptcha/api/siteverify";
        // $str_data = Zend_Json::encode($data);
        $str_data = "secret=6Lc8Ww4UAAAAAKLadWT-J3Rfwwea-_4vE-CIOorN&response=".$sRecaptcha;       
// error_log("here_         ".$str_data);
        $responseCaptcha = Core_Common::sendPostDataNew($url_send, $str_data);
// error_log("response_capcha: ".$responseCaptcha);        
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

    /**
     * Default action
     */
    public function loginAction()
    {
        $redirectPage = $this->_getParam('redirect',BASE_URL);

        $iLoginTime = new Zend_Session_Namespace('loginTime');
        if(isset($iLoginTime->time) && $this->_request->isPost())
            $iLoginTime->time++;
        $iLoginTime->time = isset($iLoginTime->time) ? $iLoginTime->time : 0;
//        (isset($iLoginTime->time)&& !empty($iLoginTime->time))  ? $iLoginTime->time : 1;
        global $globalConfig;
        //check login
        $login = AccountInfo::getInstance()->getUserLogin();
        // echo "dddd";
        // $arrLog = require_once APPLICATION_PATH.'/configs/accounts-block.php';
//        Core_Common::var_dump($arrLog);
        if(isset($login['accountID']) && isset($login['sEmail'])){            
            echo Zend_Json::encode(Core_Server::setOutputData(true, 'User already logged in', array()));
            exit;
        }
        $email ='';
        $message = '';
        $sName ='';
        $sPicture ='';
        $bCaptcha = true;
        //user login

        if($this->_request->isPost())
        {
            error_log("vao subgggmit".$this->_getParam("sEmail"));
            // $params = $this->_request->getPost();
            $email = trim($this->_getParam("sEmail",""));
            $password = trim($this->_getParam("sPassword",""));
            // $this->_getParam("isremember") = isset($this->_getParam("isremember")) ? $this->_getParam("isremember"): 'off';
            $isRemember = $this->_getParam("isremember","on");
            $isRemember = ($isRemember == 'on') ? true : false;
            $arrAccount = array();

            error_log("00".$email);
            //validate password and email
            if(!Core_Validate::checkemail($email)){
                echo Zend_Json::encode(Core_Server::setOutputData(true, $message = 'Tên đăng nhập không chính xác!!', array()));
                exit;                                
            }

            if(!Core_Validate::checkPassword($password)){
                echo Zend_Json::encode(Core_Server::setOutputData(true, $message = 'Mật khẩu không chính xác!', array()));
                exit;
            }            
            
            
            //Get params
            if($iLoginTime->time >= 4){
                $sRecaptcha = $this->_getParam('sRecaptcha');                
                $url_send ="https://www.google.com/recaptcha/api/siteverify";                
                $str_data = "secret=".GG_RECAPTCHA_SECRET."&response=".$sRecaptcha;       
// error_log("here_         ".$str_data);exit;
                $responseCaptcha = Core_Common::sendPostDataNew($url_send, $str_data);
                $responseCaptcha = Zend_Json::decode($responseCaptcha);
                error_log($responseCaptcha);
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
            
           
            if (!empty($email) && !empty($password)) {

                $isLoginM = AccountInfo::getInstance()->userLogin($email, $password, $arrAccount);

                $iAccountID = 0;

                //Is Login Success
                if ($isLoginM) {
error_log("login ok");
                    //get AccountInfo
                    // $arrAccount = AccountInfo::getInstance()->getAccountInfoByemail($email);

                    //check empty account
                    if (is_array($arrAccount)) {
                        $iAccountID = $arrAccount['account_id'];
                        $sName = $arrAccount['name'];
                        $sEmail = $arrAccount['email'];

                        //Set cookie expired
                        $iExpired = 0;

                        if ($isRemember) {
//                                    $iExpired = DOMAIN_COOKIE_EXPIRED; // 20 days
// error_log("ex");                                    
                            $iExpired = time()+(60*60*24*120);
                            Zend_Session::RememberMe($iExpired);
                        } else {
// error_log("exx");                            
                            Zend_Session::ForgetMe();
                        }

                        $sToken = Token::getInstance()->generateToken($iType="user", $arrAccount['account_id'], $arrAccount['email'], $arrAccount['avatar'], $arrAccount['password'], $iIPOwner=$_SERVER["REMOTE_ADDR"], $iIPClient=$_SERVER["REMOTE_ADDR"], $iExpired); 
                        //Set Auth Cookie
                        Core_Cookie::setCookie(AUTH_USER_LOGIN_TOKEN, $sToken, $iExpired, '/', DOMAIN, false, true);
error_log("exxx".$sToken."__".$arrAccount['account_id']);
                        //set session
                        // $accountInfo['lang'] = is_null($accountInfo['lang']) ? 'en' : $accountInfo['lang'];
                        AccountInfo::getInstance()->setUserLogin($sToken, $arrAccount['account_id']);
error_log("exx77x");                        
                        AccountInfo::getInstance()->updateLastLogin($arrAccount['account_id']);
error_log("exxx33333333333");                        
                        $arrReturn = array();
                        $arrReturn["account_id"] = $arrAccount['account_id'];
                        $arrReturn["name"] = $arrAccount['name'];
                        $arrReturn["email"] = $arrAccount['email'];
                        $arrReturn["last_login_date"] = $arrAccount['last_login_date'];
                        echo Zend_Json::encode(Core_Server::setOutputData(false, "Đăng nhập thành công!", array("data"=>$arrReturn)));
                        exit;
                    }
                    else {
                        echo Zend_Json::encode(Core_Server::setOutputData(true, "Vui long thu lai!", array()));
                        exit;                            
                    } 

                } else {
                    echo Zend_Json::encode(Core_Server::setOutputData(true, 'Email không tồn tại hoặc mật khẩu không đúng!', array()));
                    exit;
                    
                }

            } else {
                echo Zend_Json::encode(Core_Server::setOutputData(true, 'Nhập email và mật khẩu để đăng nhập!', array()));
                exit;
            }            
        }
        echo Zend_Json::encode(Core_Server::setOutputData(true, 'Phương thức không hỗ trợ!', array()));
        exit;      
    }



}

