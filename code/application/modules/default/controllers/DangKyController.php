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
        $email = "";
        $fullname = "";
        $password= "";
        $confirmpassword= "";
        $bCaptcha = true;
        //user login

        if($this->_request->isPost())
        {            
            $params = $this->_request->getPost();

            $username = trim($params["email"]);
            $password = trim($params["password"]);
            $confirmpassword = trim($params["confirmpassword"]);
            $fullname = trim($params["fullname"]);
            $email = trim($params["email"]);
            // $params["isremember"] = isset($params["isremember"]) ? $params["isremember"]: 'off';
            // $isRemember = ($params["isremember"] == 'on') ? true : false;
            $arrAccount = array();
            //validate password and username
            /*if(!Core_Validate::checkUsername($username)){
                $message .= "<br />Nhập lại tên đăng nhập";
                $bCaptcha = false;
            }*/

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

            if(!Core_Validate::checkNormalText($fullname)){
                $message .= " <br />Nhập lại ten";
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
                $arrAcc = AccountInfo::getInstance()->getAccountInfoByEmail($email);
                if($arrAcc || (is_array($arrAcc) && sizeof($arrAcc) > 0)){
// error_log("here_".Zend_Json::encode($arrAcc));                    
                    $message .= "Tai khoan da ton tai, vui long dang nhap voi mat khau.";
                }
                else{
                    $arrAcc = array();
                    $arrAcc["username"]=$email;
                    $arrAcc["password"]=md5($password);
                    $arrAcc["name"]=$fullname;
                    $arrAcc["email"]=$email;
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
                    if($inserted > 0)
                        $this->view->registOK = $inserted;
                }                
            }

        }
// error_log("here_");
        $this->view->abc=$redirectPage;
        $this->view->iSubmitTime = $iSubmitTime->time;
        $this->view->email = $email;
        $this->view->fullname = $fullname;
        $this->view->message  = $message;
        $this->view->redirectPage  = $redirectPage;


        // error_log("3. ". Zend_Json::encode($this->view));
    }

    public function loginAction()
    {

        $this->render();
    }
}

