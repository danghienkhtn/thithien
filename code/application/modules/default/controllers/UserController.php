<?php
/**
 * @name        :   UserController
 * @version     :   20161215
 * @copyright   :   Dahi
 * @todo        :   controller default 
 */

class UserController extends Core_Controller_Action
{
     //var arr login
     // private $arrLogin;l
     
     public function init() {
        parent::init();
// error_log("user controller....");        
        //Asign login
        // $this->arrLogin = $this->view->arrLogin;
        
        //Get Controller
        $controller = $this->_request->getParam('controller');
        $this->view->controller = $controller;

        // $this->_helper->layout()->disableLayout();
       
    }

    public function comingSoonAction()
    {

    }
    /**
     * Default action
     */
    public function indexAction()
    {
        error_log("___index user__");
       // $this->_redirect('/feed');
       // exit();   
    }
    
    /*public function fbloginAction()
    {
        $this->_helper->layout()->disableLayout();

        $reCode = $this->_getParam('code', '');
        $redirect_uri = urlencode("http://thithien.com/user/fblogin");
error_log($reCode."___recode_");        
        if(!empty($reCode)){
            $url_send = "https://graph.facebook.com/v2.8/oauth/access_token?client_id=". FB_APP_ID ."&redirect_uri=$redirect_uri&client_secret=". FB_APP_SECRET ."&code=". $reCode;
            $response = Core_Common::sendGetData($url_send);
error_log($url_send);

error_log("))))))___".$response);

            $fbResponse = Zend_Json::decode($response);
            $fbToken = $fbResponse["access_token"];
error_log("fbToken:".$fbToken. "----".$fbResponse["access_token"]);
            
            if(!empty($fbToken)){
                $strUser = Core_Common::sendGetData("https://graph.facebook.com/me?fields=name,email,link,gender,picture&access_token=".$fbToken);
                if(!empty($strUser)){
error_log("strUser=".$strUser);                    
                    $arrUser = Zend_Json::decode($strUser);                    
                    if(isset($arrUser["email"])){
                        $isUsernameExisted = AccountInfo::getInstance()->getAccountInfoByEmail($$arrUser["email"]);
                        if(!isUsernameExisted){
                            $arrAcc = array();
                            $arrAcc["username"]=$arrUser["username"];
                            $arrAcc["password"]=md5($arrUser["username"]);
                            $arrAcc["name"]=$arrUser["name"];
                            $arrAcc["email"]=$arrUser["username"];
                            $arrAcc["phone"]="";
                            $arrAcc["avatar"]="";
                            $arrAcc["picture"]="";
                            $arrAcc["address"]="";
                            $arrAcc["level"]=0;
                            $arrAcc["is_admin"]=0;
                            $arrAcc["active"]=1;
                            $arrAcc["status"]=1;                    
                            $inserted = AccountInfo::getInstance()->insertAccountInfo($arrAcc);                        
                            if($inserted > 0){
                                echo Zend_Json::encode(Core_Server::setOutputData(false, 'Dang ky hoan tat', array("userId" =>"$inserted")));
                                exit;                
                            }
                            else{
                                echo Zend_Json::encode(Core_Server::setOutputData(true, 'Co loi xay ra', array()));
                                exit;   
                            }
                        }
                        else
                        {
                            // login with FB
                        }    
                    }
                }
            }
        }
    }*/         
}

