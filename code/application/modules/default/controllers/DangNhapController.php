<?php

/**
 * @author      :   Hiennd
 * @name        :   LoginController
 * @version     :   20161207
 * @copyright   :   Dahi
 */
class DangNhapController extends Core_Controller_Action {

    protected $title = 'Login Action';
    /**
     * init of controller
     */
    public function init()
    {
        parent::init();
        $remoteIp =  $this->_request->getServer('REMOTE_ADDR'); 
        // echo $remoteIp;       
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
        $redirectPage = $this->_getParam('redirect',BASE_URL);
        if(isset($this->isLogin) && $this->isLogin){
            $this->_redirect($redirectPage);
        }        

        $iLoginTime = new Zend_Session_Namespace('loginTime');        
        $iLoginTime->time = isset($iLoginTime->time) ? $iLoginTime->time : 0;
//        (isset($iLoginTime->time)&& !empty($iLoginTime->time))  ? $iLoginTime->time : 1;
        // global $globalConfig;
        //check login
        // $login = AccountInfo::getInstance()->getUserLogin();
        // $arrLog = require_once APPLICATION_PATH.'/configs/accounts-block.php';
//        Core_Common::var_dump($arrLog);
        /*if(isset($login['accountID']) && isset($login['email']))
            $this->_redirect($redirectPage);*/
        /*$username ='';
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
            
            //Get params
            if($iLoginTime->time >= 4){
                $reCaptcha = $this->_getParam('g-recaptcha-response');

                $data = array(
                    "secret" => GG_RECAPTCHA_SECRET,
                    "response" => $reCaptcha
                );
                $url_send ="https://www.google.com/recaptcha/api/siteverify";
                //secret=6LdYFh0TAAAAAAL_RVrbhr83qFapwIZrQaOE8jC0&response=".$reCaptcha;
                $str_data = json_encode($data);
                $responseCaptcha = Core_Common::sendPostData($url_send, $str_data);
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
        }*/
        if($iLoginTime->time >= 4)
            $this->view->recaptchaDis = "none";
        else
            $this->view->recaptchaDis = "hide";    
        $this->view->ggSiteKey = GG_RECAPTCHA_SITE_KEY;
        // $this->view->message  = $message;
        $this->view->redirectPage  = $redirectPage;
        $this->view->urlGGLogin = $this->chkgglogin();
        $this->view->urlFBLogin = "https://www.facebook.com/dialog/oauth?client_id=".FB_APP_ID."&redirect_uri=".BASE_URL."/login/fblogin&scope=email";
    }
    
    private function chkgglogin()
    {       
        $authUrl = ""; 
        // unset($_SESSION['ggtoken']);
        if (isset($_SESSION['ggtoken'])) 
        { 
            $gClient->setAccessToken($_SESSION['ggtoken']);
            $authUrl = "";
        }
        else{
            require_once 'Google/Google_Client.php';
            require_once 'Google/contrib/Google_Oauth2Service.php';
             
            $gClient = new Google_Client();
            $redirect_url = BASE_URL."/login/gglogin";
            $gClient->setApplicationName(GG_APP_NAME);
            $gClient->setClientId(GG_CREDENTIALS_KEY);
            $gClient->setClientSecret(GG_CREDENTIALS_SECRET);
            $gClient->setRedirectUri($redirect_url);
            $gClient->setDeveloperKey(GG_API_KEY);
            $gClient->setScopes("https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email");            
            // $gClient->setIncludeGrantedScopes(true);
            $gClient->setAccessType("online");
            //For Guest user, get google login url
            $authUrl = $gClient->createAuthUrl();
            // error_log("here gg.".$authUrl."--");
        }
        return $authUrl;
    }

    public function ggloginAction()
    {
        $this->_helper->layout()->disableLayout();

        $reCode = $this->_getParam('code', '');
        $redirect_url = urlencode("http://thithien.com/login/gglogin");

        require_once 'Google/Google_Client.php';
        require_once 'Google/contrib/Google_Oauth2Service.php';
         
        $gClient = new Google_Client();
        $gClient->setApplicationName(GG_APP_NAME);
        $gClient->setClientId(GG_CREDENTIALS_KEY);
        $gClient->setClientSecret(GG_CREDENTIALS_SECRET);
        $gClient->setRedirectUri($redirect_url);
        $gClient->setDeveloperKey(GG_API_KEY);
        $gClient->setScopes("https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email");
        $gClient->setAccessType("online");

        $google_oauthV2 = new Google_Oauth2Service($gClient);
        if(!empty($reCode)){
            $gClient->authenticate($reCode);
            $_SESSION['ggtoken'] = $gClient->getAccessToken();
            $this->_redirect('/login/gglogin');
            exit;
            // header('Location: ' . filter_var($google_redirect_url, FILTER_SANITIZE_URL));
            // return;
        }
        if (isset($_SESSION['ggtoken'])) 
        { 
            $gClient->setAccessToken($_SESSION['ggtoken']);
        }


        if ($gClient->getAccessToken()) 
        {
            //For logged in user, get details from google using access token
            $arrUser                 = $google_oauthV2->userinfo->get();
// error_log("detail existed: ".json_encode($arrUser));            
            /*$user_id              = $userInfo['id'];
            $user_name            = filter_var($userInfo['name'], FILTER_SANITIZE_SPECIAL_CHARS);
            $email                = filter_var($userInfo['email'], FILTER_SANITIZE_EMAIL);
            $profile_url          = filter_var($userInfo['link'], FILTER_VALIDATE_URL);
            $profile_image_url    = filter_var($userInfo['picture'], FILTER_VALIDATE_URL);
            $personMarkup         = "$email<div><img src='$profile_image_url?sz=50'></div>";*/
            $_SESSION['ggtoken']    = $gClient->getAccessToken();

            /////////////////////////////
            if(isset($arrUser["email"])){
                $userDetail = AccountInfo::getInstance()->getAccountInfoByEmail(filter_var($arrUser['email'], FILTER_SANITIZE_EMAIL));
                // add new user
                if(!is_array($userDetail)){ 
                    
                    $arrAcc = array();
                    $arrAcc["username"]=filter_var($arrUser['email'], FILTER_SANITIZE_EMAIL);
                    $arrAcc["password"]=md5(filter_var($arrUser['email'], FILTER_SANITIZE_EMAIL));
                    $arrAcc["name"]=filter_var($arrUser['name'], FILTER_SANITIZE_SPECIAL_CHARS);
                    $arrAcc["email"]=filter_var($arrUser['name'], FILTER_SANITIZE_SPECIAL_CHARS);
                    $arrAcc["phone"]="";
                    $arrAcc["avatar"]=filter_var($arrUser['picture'], FILTER_VALIDATE_URL);
                    $arrAcc["picture"]="";
                    $arrAcc["address"]="";
                    $arrAcc["level"]=0;
                    $arrAcc["is_admin"]=0;
                    $arrAcc["active"]=1;
                    $arrAcc["status"]=1;                    
                    $inserted = AccountInfo::getInstance()->insertAccountInfo($arrAcc);                        
                    if($inserted > 0){
                        $iAccountID = $inserted;
                        $sName = $arrAcc['name'];
                        $sEmail = $arrAcc['email'];
                        $sAvatar = $arrAcc["avatar"];
                        $sPs = $arrAcc["password"];

                        $iExpired = 3600;
                        //Set cookie expired
                        Zend_Session::RememberMe($iExpired);

                        // $sToken = Core_Guuid::generateNoSpace(Core_Guuid::UUID_TIME, Core_Guuid::FMT_STRING, "InternalProject", Core_Utility::getAltIp());
                        $sToken = Token::getInstance()->generateToken($iType="user", $iAccountID, $sEmail, $sAvatar, $sPs, $iIPOwner=$_SERVER["REMOTE_ADDR"], $iIPClient=$_SERVER["REMOTE_ADDR"], $iExpired); 

            //                                $domain = $this->getRequest()->getHttpHost();
                        //Set Auth Cookie
                        Core_Cookie::setCookie(AUTH_USER_LOGIN_TOKEN, $sToken, $iExpired, '/', DOMAIN, false, true);

                        //set session                                
                        AccountInfo::getInstance()->setUserLogin($sToken, $iAccountID);

                        $this->_redirect('/');
                        exit();                
                    }
                    else{
                        // echo Zend_Json::encode(Core_Server::setOutputData(true, 'Co loi xay ra', array()));
                        $this->_redirect('/dang-ky');
                        exit;   
                    }
                }
                else// existed user
                {
            // error_log(Zend_Json::encode($userDetail));   
                    if(isset($userDetail["account_id"])){                         
                        $iAccountID = $userDetail["account_id"];
                        $sName = $userDetail['name'];
                        $sEmail = $userDetail['email'];
                        $sAvatar = $userDetail["avatar"];
                        $sPs = $userDetail["password"];
                        $iExpired = 3600;
                        //Set cookie expired
                        Zend_Session::RememberMe($iExpired);
                        $sToken = Token::getInstance()->generateToken($iType="user", $iAccountID, $sEmail, $sAvatar, $sPs, $iIPOwner=$_SERVER["REMOTE_ADDR"], $iIPClient=$_SERVER["REMOTE_ADDR"], $iExpired); 
            // error_log("login token:".$sToken);
            //                                $domain = $this->getRequest()->getHttpHost();
                        //Set Auth Cookie
                        Core_Cookie::setCookie(AUTH_USER_LOGIN_TOKEN, $sToken, $iExpired, '/', DOMAIN, false, true);

                        //set session                                
                        AccountInfo::getInstance()->setUserLogin($sToken, $iAccountID);

                        $this->_redirect('/index');
                        exit();
                    }
                    else{
                        $this->_redirect('/dang-ky');
                        exit();
                    }    
                }    
            }
            ///////////////////////////////

            $this->_redirect('/');
            exit;
        }
        else {
            //For Guest user, get google login url
            $authUrl = $gClient->createAuthUrl();
            // header("Location: ".$authUrl);
            header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
            exit;
        }
    }

    public function fbloginAction()
    {
        $this->_helper->layout()->disableLayout();

        $reCode = $this->_getParam('code', '');
        $redirect_uri = urlencode("http://thithien.com/login/fblogin");
        if(!empty($reCode)){
            $url_send = "https://graph.facebook.com/v2.8/oauth/access_token?client_id=". FB_APP_ID ."&redirect_uri=$redirect_uri&client_secret=". FB_APP_SECRET ."&code=". $reCode;
            $response = Core_Common::sendGetData($url_send);
// error_log($url_send);
// error_log("))))))___".$response);
            $fbResponse = Zend_Json::decode($response);
            $fbToken = $fbResponse["access_token"];
// error_log("fbToken:".$fbToken);            
            if(!empty($fbToken)){
                $strUser = Core_Common::sendGetData("https://graph.facebook.com/me?fields=name,email,link,gender,picture&access_token=".$fbToken);
                if(!empty($strUser)){
// error_log("strUser=".$strUser);                    
                    $arrUser = Zend_Json::decode($strUser);                    
                    if(isset($arrUser["email"])){
                        $userDetail = AccountInfo::getInstance()->getAccountInfoByEmail($arrUser["email"]);
                        // add new user
                        if(!is_array($userDetail)){ 
                            $arrAcc = array();
                            $arrAcc["username"]=$arrUser["email"];
                            $arrAcc["password"]=md5($arrUser["email"]);
                            $arrAcc["name"]=$arrUser["name"];
                            $arrAcc["email"]=$arrUser["email"];
                            $arrAcc["phone"]="";
                            $arrAcc["avatar"]=$arrUser["picture"]["data"]["url"];
                            $arrAcc["picture"]="";
                            $arrAcc["address"]="";
                            $arrAcc["level"]=0;
                            $arrAcc["is_admin"]=0;
                            $arrAcc["active"]=1;
                            $arrAcc["status"]=1;                    
                            $inserted = AccountInfo::getInstance()->insertAccountInfo($arrAcc);                        
                            if($inserted > 0){
                                $iAccountID = $inserted;
                                $sName = $arrAcc['name'];
                                $sEmail = $arrAcc['email'];
                                $sAvatar = $arrAcc["avatar"];
                                $sPs = $arrAcc["password"];

                                $iExpired = 3600;
                                //Set cookie expired
                                Zend_Session::RememberMe($iExpired);

                                // $sToken = Core_Guuid::generateNoSpace(Core_Guuid::UUID_TIME, Core_Guuid::FMT_STRING, "InternalProject", Core_Utility::getAltIp());
                                $sToken = Token::getInstance()->generateToken($iType="user", $iAccountID, $sEmail, $sAvatar, $sPs, $iIPOwner=$_SERVER["REMOTE_ADDR"], $iIPClient=$_SERVER["REMOTE_ADDR"], $iExpired); 

    //                                $domain = $this->getRequest()->getHttpHost();
                                //Set Auth Cookie
                                Core_Cookie::setCookie(AUTH_USER_LOGIN_TOKEN, $sToken, $iExpired, '/', DOMAIN, false, true);

                                //set session                                
                                AccountInfo::getInstance()->setUserLogin($sToken, $iAccountID);

                                $this->_redirect('/index');
                                exit();                
                            }
                            else{
                                // echo Zend_Json::encode(Core_Server::setOutputData(true, 'Co loi xay ra', array()));
                                $this->_redirect('/dang-ky');
                                exit;   
                            }
                        }
                        else// existed user
                        {
// error_log(Zend_Json::encode($userDetail));   
                            if(isset($userDetail["account_id"])){                         
                                $iAccountID = $userDetail["account_id"];
                                $sName = $userDetail['name'];
                                $sEmail = $userDetail['email'];
                                $sAvatar = $userDetail["avatar"];
                                $sPs = $userDetail["password"];
                                $iExpired = 3600;
                                //Set cookie expired
                                Zend_Session::RememberMe($iExpired);
                                $sToken = Token::getInstance()->generateToken($iType="user", $iAccountID, $sEmail, $sAvatar, $sPs, $iIPOwner=$_SERVER["REMOTE_ADDR"], $iIPClient=$_SERVER["REMOTE_ADDR"], $iExpired); 
// error_log("login token:".$sToken);
    //                                $domain = $this->getRequest()->getHttpHost();
                                //Set Auth Cookie
                                Core_Cookie::setCookie(AUTH_USER_LOGIN_TOKEN, $sToken, $iExpired, '/', DOMAIN, false, true);

                                //set session                                
                                AccountInfo::getInstance()->setUserLogin($sToken, $iAccountID);

                                $this->_redirect('/index');
                                exit();
                            }
                            else{
                                $this->_redirect('/dang-ky');
                                exit();
                            }    
                        }    
                    }
                }
            }
        }
    }

    public function loginAction()
    {

        $this->render();
    }
}

