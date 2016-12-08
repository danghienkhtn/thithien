<?php

/**
 * @author      :   Hiennd
 * @name        :   LoginController
 * @version     :   20161207
 * @copyright   :   Dahi
 */
class LoginController extends Core_Controller_Action {

    protected $title = 'Login Action';
    /**
     * init of controller
     */
    public function init()
    {
        $remoteIp =  $this->_request->getServer('REMOTE_ADDR'); 
        // echo $remoteIp;       
        //Disable layout
        $this->_helper->layout()->disableLayout();
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

        $redirectPage = $this->_getParam('redirect',BASE_URL.'/trang-chu');

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
        if(isset($login['accountID']) && isset($login['email']))
            $this->_redirect($redirectPage);
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
            $params["isremember"] = isset($params["isremember"]) ? $params["isremember"]: 'off';
            $isRemember = ($params["isremember"] == 'on') ? true : false;
            $arrAccount = array();
            //validate password and username
            if(!Core_Validate::checkUsername($username)){
                $message = 'Tên đăng nhập không chính xác!!';
                $bCaptcha = false;
            }

            if(!Core_Validate::checkPassword($password)){
                $message = 'Mật khẩu không chính xác!';
                $bCaptcha = false;
            }            

            $reCaptcha = $this->_getParam('g-recaptcha-response');
            //Get params
            if($iLoginTime->time >= 4){

                $data = array(
                    "secret" => "6LdYFh0TAAAAAAL_RVrbhr83qFapwIZrQaOE8jC0",
                    "response" => $reCaptcha
                );
                $url_send ="https://www.google.com/recaptcha/api/siteverify?secret=6LdYFh0TAAAAAAL_RVrbhr83qFapwIZrQaOE8jC0&response=".$reCaptcha;
                $str_data = json_encode($data);
                $responseCaptcha = $this->sendPostData($url_send, $str_data);
                $responseCaptcha = json_decode($responseCaptcha,true);
                $bCaptcha = $responseCaptcha['success'];
                if(!$bCaptcha);
                $message = 'Sai chứng thức, vui lòng thử lại!';
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
                            $message = "Vui long thu lai!";
                        } 

                    } else {
                        $message = 'Wrong UserName Or Password. Pls check your information again!';
                    }

                } else {
                    $message = 'Pls input UserName Or Password!';
                }
            }

            //Check login mobion
        }

        $this->view->iLoginTime = $iLoginTime->time;
        $this->view->email = $username;
        $this->view->message  = $message;
        $this->view->redirectPage  = $redirectPage;
    }

    public function loginAction()
    {

        $this->render();
    }
}

