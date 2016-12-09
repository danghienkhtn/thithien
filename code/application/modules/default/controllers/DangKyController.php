<?php

/**
 * @author      :   Hiennd
 * @name        :   DangKyController
 * @version     :   20161208
 * @copyright   :   Dahi
 */
class DangKyController extends Core_Controller_Action {

    protected $title = 'Regist Action';
    /**
     * init of controller
     */
    public function init()
    {
        parent::init();

        $remoteIp =  $this->_request->getServer('REMOTE_ADDR'); 
        // error_log("----". $remoteIp);       
        //Disable layout
        // $this->_helper->layout()->disableLayout();
    }

    private function sendPostData($url, $post){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        curl_close($ch);  // Seems like good practice
        return $result;
    }


    /**
     * Default action
     */
    public function indexAction()
    {

        $redirectPage = $this->_getParam('returnUrl',BASE_URL);
        // error_log($this->_getParam("returnUrl")); 
        //check login
        // $login = AccountInfo::getInstance()->getUserLogin();
        if(isset($this->isLogin) && $this->isLogin === TRUE)
            $this->_redirect($redirectPage);

        $iSubmitTime = new Zend_Session_Namespace('RegistTime');
        if(isset($iSubmitTime->time) && $this->_request->isPost())
            $iSubmitTime->time++;

        $iSubmitTime->time = isset($iSubmitTime->time) ? $iSubmitTime->time : 0;
        global $globalConfig;

        $username ='';
        $message = '';
        $sName ='';
        $sPicture ='';
        $bCaptcha = true;
        //user login

        if($this->_request->isPost())
        {            
            $params = $this->_request->getPost();

            $username = trim($params["username"]);
            $password = trim($params["password"]);
            $confirmpassword = trim($params["confirmpassword"]);
            $params["isremember"] = isset($params["isremember"]) ? $params["isremember"]: 'off';
            $isRemember = ($params["isremember"] == 'on') ? true : false;
            $arrAccount = array();
            //validate password and username
            if(!Core_Validate::checkUsername($username)){
                $message .= "<br />Nhập lại tên đăng nhập";
                $bCaptcha = false;
            }

            if(!Core_Validate::checkEmail($email)){
                $message .= "<br />Nhập lại email";
                $bCaptcha = false;
            }

            if(!Core_Validate::checkPassword($password)){
                $message .= " <br />Nhập lại mật khẩu";
                $bCaptcha = false;
            }            

            if(!Core_Validate::checkPassword($confirmpassword) || $password !== $confirmpassword){
                $message .= " <br />Nhập lại xác nhận mật khẩu";
                $bCaptcha = false;
            }

            $reCaptcha = $this->_getParam('g-recaptcha-response');
            //Get params
            if($iSubmitTime->time >= 4){

                $data = array(
                    "secret" => "6Lc8Ww4UAAAAAKLadWT-J3Rfwwea-_4vE-CIOorN",
                    "response" => $reCaptcha
                );
                $url_send ="https://www.google.com/recaptcha/api/siteverify?secret=6Lc8Ww4UAAAAAKLadWT-J3Rfwwea-_4vE-CIOorN&response=".$reCaptcha;
                $str_data = json_encode($data);
                $responseCaptcha = $this->sendPostData($url_send, $str_data);
                $responseCaptcha = json_decode($responseCaptcha,true);
                $bCaptcha = $responseCaptcha['success'];
                if(!$bCaptcha){
                    error_log("chung thuc ko dc");
                    $message .= " <br />Sai chứng thức, vui lòng thử lại!";
                }
                else{
                    error_log("Chung thuc ok");
                }
            }

            if($bCaptcha) {
                if (!empty($username) && !empty($password)) {

                    $isLoginM = AccountInfo::getInstance()->checkUserLogin($username, $password, &$arrAccount);

                    $iAccountID = 0;

                    //Is Login Success
                    if ($isLoginM) {

                        //get AccountInfo
                        // $arrAccount = AccountInfo::getInstance()->getAccountInfoByUserName($username);

                        //check empty account
                        if (!empty($arrAccount)) {
                            $iAccountID = $arrAccount['account_id'];
                            $sName = $arrAccount['name'];
                            $sEmail = $arrAccount['email'];

                            //Set cookie expired
                            $iExpired = 0;

                            if ($isRemember) {
//                                    $iExpired = DOMAIN_COOKIE_EXPIRED; // 20 days
                                $iExpired = time()+(60*60*24*120);
                                Zend_Session::RememberMe($iExpired);
                            } else {
                                Zend_Session::ForgetMe();
                            }

                            // $sToken = Core_Guuid::generateNoSpace(Core_Guuid::UUID_TIME, Core_Guuid::FMT_STRING, "InternalProject", Core_Utility::getAltIp());
                            $sToken = Token::getInstance()->generateToken($iType="user", $arrAccount['account_id'], $arrAccount['username'], $arrAccount['avatar'], $arrAccount['password'], $iIPOwner=$_SERVER["REMOTE_ADDR"], $iIPClient=$_SERVER["REMOTE_ADDR"], $iExpired); 

//                                $domain = $this->getRequest()->getHttpHost();
                            //Set Auth Cookie
                            Core_Cookie::setCookie(AUTH_USER_LOGIN_TOKEN, $sToken, $iExpired, '/', DOMAIN, false, true);

                            //set session
                            // $accountInfo['lang'] = is_null($accountInfo['lang']) ? 'en' : $accountInfo['lang'];
                            AccountInfo::getInstance()->setLogin($sToken, $iAccountID);

                            $this->_redirect($redirectPage);
                            exit();

                        }
                        else {
                            $message .= "Vui long thu lai!";
                        } 

                    } else {
                        $message .= 'Wrong UserName Or Password. Pls check your information again!';
                    }

                } else {
                    $message .= 'Pls input UserName Or Password!';
                }
            }

        }
// error_log("here_");
        $this->view->abc=$redirectPage;
        $this->view->iSubmitTime = $iSubmitTime->time;
        $this->view->email = $username;
        $this->view->message  = $message;
        $this->view->redirectPage  = $redirectPage;


        // error_log("3. ". Zend_Json::encode($this->view));
    }

    public function loginAction()
    {

        $this->render();
    }
}

