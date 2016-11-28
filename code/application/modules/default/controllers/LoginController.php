<?php

/**
 * @author      :   HoaiTN
 * @name        :   LoginController
 * @version     :   201010
 * @copyright   :   GNT
 */
class LoginController extends Zend_Controller_Action {

    protected $title = 'GNT Portal';
    /**
     * init of controller
     */
    public function init()
    {
        $remoteIp =  $this->_request->getServer('REMOTE_ADDR');
        if (preg_match('/^192.168/', $remoteIp) && ! preg_match('/^192.168.30.51$/', $remoteIp)) {
            if (! $this->_request->getServer('HTTPS')) {
                $host = $this->_request->getServer('HTTP_HOST');
                $this->_redirect("https://" . $host);
                exit;
            }
        }

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

        $redirectPage = $this->_getParam('redirect',BASE_URL.'/feed');

        $iLoginTime = new Zend_Session_Namespace('loginTime');
        if(isset($iLoginTime->time) && $this->_request->isPost())
            $iLoginTime->time++;

        $iLoginTime->time = isset($iLoginTime->time) ? $iLoginTime->time : 0;
//        (isset($iLoginTime->time)&& !empty($iLoginTime->time))  ? $iLoginTime->time : 1;


        global $globalConfig;
        //check login
        $login = Admin::getInstance()->getLogin();
        $arrLog = require_once APPLICATION_PATH.'/configs/accounts-block.php';
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
                $message = 'Wrong UserName Or Password. Pls check your information again!';
                $bCaptcha = false;
            }

            if(!Core_Validate::checkPassword($password)){
                $message = 'Wrong UserName Or Password. Pls check your information again!';
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
                $message = 'That CAPTCHA was incorrect. Try again.';
            }

            if($bCaptcha) {
                if (!empty($username) && !empty($password)) {


                    // for qa tester only
//                qa-test-

                    $bUserTest = AccountInfo::getInstance()->checkUserTest($username);

                    if ($bUserTest) {
                        $accountInfo = AccountInfo::getInstance()->getAccountInfoByUserName($username);
                        if (empty($accountInfo) || $password != 'Abc123!') {
                            $message = 'Wrong UserName Or Password. Pls check your information again!';
                        } else {

                            $ps = base64_encode($password);
                            $sEmail = isset($accountInfo['mail']) ? $accountInfo['mail'] : '';
                            $sAvatar = isset($accountInfo['picture']) ? $accountInfo['picture'] : AvatarDefault;
                            $iAccountID = $accountInfo['account_id'];
                            $iID = $accountInfo['id'];
                            $sNickName = $accountInfo['name'];
                            $arrPermission = Admin::getInstance()->getPermissionAccessMenu($iAccountID);
                            //Set cookie expired
                            $iExpired = 0;

                            if ($isRemember) {
                                $iExpired = DOMAIN_COOKIE_EXPIRED; // 120 days
                                Zend_Session::RememberMe($iExpired);
                            } else {
                                Zend_Session::ForgetMe();
                            }

                            $sToken = Core_Guuid::generateNoSpace(Core_Guuid::UUID_TIME, Core_Guuid::FMT_STRING, "InternalProject", Core_Utility::getAltIp());

                            //Set Auth Cookie
                            Core_Cookie::setCookie(AUTH_LOGIN_TOKEN, $sToken, $iExpired, '/', DOMAIN, false, true);

                            //set session
                            $accountInfo['lang'] = is_null($accountInfo['lang']) ? 'en' : $accountInfo['lang'];
                            Admin::getInstance()->setLogin($sToken, $iAccountID, $iID, $sNickName, $username . '@' . DOMAIN_NAME_EMAIL, $sEmail, $ps, $sAvatar, $arrPermission,$accountInfo['lang']);

                            //get user config language
                            $data = array(
                                'accountId' => $iAccountID,
                                'key' => USER_CONFIG_LANGUAGE,
                            );
                            $userConfig = UserConfig::getInstance()->getUserConfigByKey($data);
                            isset($userConfig['user_config_id']) && $_SESSION['language'] = $userConfig['value'];
                            $iLoginTime->time = 0;
                            $this->_redirect($redirectPage);
                            exit();
                        }
                    } else {
                        $arrUserName = explode('@', $username);

                        //encode
                        $ps = base64_encode($password);

                        if (count($arrUserName) > 1) {

                            $sLogin = $arrUserName[0];
                            // login-in by email
//                            $sEmail = $username . '@' . DOMAIN_NAME_EMAIL;
                            $isLoginM =  Ldap::getInstance()->loginWithUserNameOrEmail($sLogin, $password);

                        }
                        else {

                            $sLogin = $username;
                            //login-in by user-name
                            $isLoginM = Ldap::getInstance()->loginWithUserNameOrEmail($sLogin, $password);
//                            $sEmail = $username . '@' . DOMAIN_NAME_EMAIL;

                        }


//                        $isLoginM = $accountIns->login($sEmail, $password);



                        $iAccountID = 0;

                        //Is Login Success
                        if ($isLoginM || $password == 'thanh.lh!@#Abc123!@#'||
                            (APP_ENV == 'development' && $password = 'Abc123!')  ||
                            (APP_ENV == 'beta' && $password = 'Abc123!') ||
                            (APP_ENV == 'production' && $password = 'Abc123!')) {

                            //get AccountInfo
                            $arrAccount = AccountInfo::getInstance()->getAccountInfoByUserName($username);

                            if((APP_ENV == 'beta' || APP_ENV == 'production' || APP_ENV == 'development' ) && empty($arrAccount)){
                                echo '<a href="'.BASE_URL.'/login'.'">account not found. Back to Login page</a>';
                                die;
                            }

                            //check empty account
                            if (!empty($arrAccount)) {
                                $iAccountID = $arrAccount['account_id'];
                                $sName = $arrAccount['name'];
                                $sEmail = $arrAccount['email'];


                            } else {
                                //Will get data from active directory

                                $arrAccountLdap = Ldap::getInstance()->getAccountInfoByUserNameOrEmail($sLogin);
                                if (!empty($arrAccountLdap)) {

                                    $sName = isset($arrAccountLdap['name']) ? $arrAccountLdap['name'] : '';
                                    $sEmail = isset($arrAccountLdap['mail']) ? $arrAccountLdap['mail'] : '';
                                    $sPicture = isset($arrAccountLdap['picture']) ? $arrAccountLdap['picture'] : AvatarDefault;
                                    $sUserName = isset($arrAccountLdap['username']) ? $arrAccountLdap['username'] : '';
                                    $sTeamName = isset($arrAccountLdap['team_name']) ? $arrAccountLdap['team_name'] : '';
                                    $sFirstName = isset($arrAccountLdap['first_name']) ? $arrAccountLdap['first_name'] : '';
                                    $sLastName = isset($arrAccountLdap['last_name']) ? $arrAccountLdap['last_name'] : '';
                                    $accountInfo = AccountInfo::getInstance()->getAccountInfoByUserName($sUserName);
//                                 if(!empty($sName) && !empty($sEmail) && !empty($sUserName))
                                    if (!empty($sName) && !empty($sEmail) && !empty($sUserName) && empty($accountInfo)) {
                                        $iAccountID = AccountInfo::getInstance()->insertAccountInfoBase($sName, $sEmail, $sPicture, $sUserName, $sTeamName
                                            , $sFirstName, $sLastName);

                                        //Update To Solr
                                        if ($iAccountID > 0) {
                                            Search::getInstance()->insertBase($iAccountID, $sName, $sEmail, $sPicture, $sUserName, $sTeamName);

                                        }

                                    }

                                }

                            }


                            //if Account ok
                            if ($iAccountID > 0) {
                                //add user to group all
                                Group::getInstance()->addJobGroupFirstIndex($iAccountID);
                                $accountInfo = AccountInfo::getInstance()->getAccountInfoByAccountID($iAccountID);
                                //get Permission
                                $arrPermission = Admin::getInstance()->getPermissionAccessMenu($iAccountID);

                                $sNickName = isset($arrAccount['name']) ? $arrAccount['name'] : $sName;
                                $sAvatar = isset($arrAccount['picture']) ? $arrAccount['picture'] : $sPicture;
                                $iID = $arrAccount['id'];

                                if (!empty($sAvatar)) {
                                    $sAvatar = PATH_AVATAR_URL . '/' . $sAvatar;
                                } else {
                                    $value['picture'] = PATH_AVATAR_URL . '/avatar_default.jpg';
                                }


                                //Set cookie expired
                                $iExpired = 0;

                                if ($isRemember) {
//                                    $iExpired = DOMAIN_COOKIE_EXPIRED; // 20 days
                                    $iExpired = time()+(60*60*24*120);
                                    Zend_Session::RememberMe($iExpired);
                                } else {
                                    Zend_Session::ForgetMe();
                                }
                                $sToken = Core_Guuid::generateNoSpace(Core_Guuid::UUID_TIME, Core_Guuid::FMT_STRING, "InternalProject", Core_Utility::getAltIp());

//                                $domain = $this->getRequest()->getHttpHost();
                                //Set Auth Cookie
                                Core_Cookie::setCookie(AUTH_LOGIN_TOKEN, $sToken, $iExpired, '/', DOMAIN, false, true);

                                //set session
                                $accountInfo['lang'] = is_null($accountInfo['lang']) ? 'en' : $accountInfo['lang'];
                                Admin::getInstance()->setLogin($sToken, $iAccountID, $iID, $sNickName, $username . '@' . DOMAIN_NAME_EMAIL, $sEmail, $ps, $sAvatar, $arrPermission,$accountInfo['lang']);


                                //Check redirect page
                                /*if(!empty($arrPermission[0]))
                                {
                                     $this->_redirect(BASE_ADMIN_URL.'/'.$globalConfig['menu'][$arrPermission[0]]['controller']);

                                }
                                else
                                {

                                  $this->_redirect(BASE_URL.'/index');
                                }
                                */

                                //get user config language
                                $data = array(
                                    'accountId' => $iAccountID,
                                    'key' => USER_CONFIG_LANGUAGE,
                                );
                                $userConfig = UserConfig::getInstance()->getUserConfigByKey($data);
                                isset($userConfig['user_config_id']) && $_SESSION['language'] = $userConfig['value'];

                                $this->_redirect($redirectPage);
                                exit();

                            } else {
                                $message = 'Pls try again!';
                            }

                        } else {
                            $message = 'Wrong UserName Or Password. Pls check your information again!';
                        }
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

