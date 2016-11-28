<?php

/**
 * @author      :   HoaiTN
 * @name        :   LoginController
 * @version     :   201010
 * @copyright   :   GNT
 */
class Backend_LoginController extends Core_Controller_Action {

    protected $title = 'Login Page';
    /**
     * init of controller
     */
    public function init()
    {
        parent::init();
        //Disable layout
        $this->_helper->layout()->disableLayout();
    }

    /**
     * Default action
     */
    public function indexAction()
    {
        global $globalConfig;
        
        
        $username ='';
        $message = '';
        //user login
        if($this->_request->isPost())
        {
            //Get params
            $params = $this->_request->getPost();
            
            $username = trim($params["username"]);
            $password = trim($params["password"]);
            $arrAccount = array();
            
            if(!empty($username) && !empty($password))
            {
                
                    $sEmail = $username;
                    $arrUserName = explode('@',$username);
                    
                    if(count($arrUserName)>1)
                    {
                       $username = $arrUserName[0]; 
                       $sEmail = $username.'@'.DOMAIN_NAME_EMAIL;
                    }
                    else
                    {
                        $sEmail= $username.'@'.DOMAIN_NAME_EMAIL;
                    }
                    
                   
                    $accountIns = Ldap::getInstance();
                    $isLoginM = $accountIns->login($sEmail, $password);
                    $iAccountID =0;

                    //Is Login Success
                    if ($isLoginM)
                    {
                         
                        //get AccountInfo
                        $arrAccount= AccountInfo::getInstance()->getAccountInfoByUserName($username);
                           
                         //check empty account
                         if(!empty($arrAccount))
                         {
                             $iAccountID = $arrAccount['account_id'];
                             $sName = $arrAccount['name'];
                             $sEmail = $arrAccount['email'];
                             
                         }
                         else
                         {
                              //Will get data from active directory
                             $arrAccountLdap = $accountIns->getAccountInfo($username,$sEmail,$password);
                             
                             if(!empty($arrAccountLdap))
                             {
                                 $sName = isset($arrAccountLdap['name'])?$arrAccountLdap['name']:'';
                                 $sEmail =  isset($arrAccountLdap['mail'])?$arrAccountLdap['mail']:'';
                                 $sPicture =  isset($arrAccountLdap['picture'])?$arrAccountLdap['picture']:'';
                                 $sUserName =  isset($arrAccountLdap['username'])?$arrAccountLdap['username']:'';
                                 $sTeamName =  isset($arrAccountLdap['team_name'])?$arrAccountLdap['team_name']:'';

                                 if(!empty($sName) && !empty($sEmail) && !empty($sUserName))
                                 {
                                      $iAccountID = AccountInfo::getInstance()->insertAccountInfoBase($sName, $sEmail,$sPicture,$sUserName, $sTeamName);
                                 }
                                 
                             }
                             
                         }
                         
                         
                         //if Account ok
                         if($iAccountID>0)
                         {
                             
                             //Set cookie expired
                            $iExpired = DOMAIN_COOKIE_EXPIRED;

                            $sToken = Core_Guuid::generateNoSpace(Core_Guuid::UUID_TIME, Core_Guuid::FMT_STRING, "InternalProject", Core_Utility::getAltIp());

                            //Set Auth Cookie                
                            Core_Cookie::setCookie(AUTH_LOGIN_TOKEN, $sToken, $iExpired, '/', DOMAIN, true, true);

                            //set session
                            Admin::getInstance()->setLogin($sToken, $iAccountID, $sName, $sEmail);

                             //
                             $this->_redirect('/');
                             exit();
                             
                         }
                         else
                         {
                              $message = 'Pls try again!';
                         }
                           
                    }
                    else
                    {
                        $message = 'Wrong Email Or Password. Pls check your information again!';
                    }
                   

                    /*
                    //Login success
                    if($isLoginM)
                    {
                        
                        //get Permission
                        $arrPermission = Admin::getInstance()->getPermissionAccessMenu($arrProfile['profile']->accountid);

                        if(!empty($arrPermission[0]))
                        {  
                              $this->_redirect('/'.$globalConfig['menu'][$arrPermission[0]]['controller']);

                        }
                        else
                        {
                            //Redirect to mainpage
                            $this->_redirect('/');
                      `  }

                    }
                    else
                    {
                        $message = 'Wrong Email Or Password. Pls check your information again!';
                    }
                   */
            }
            else
            {
                 $message = 'Pls input Email Or Password!'; 
            }
            
            //Check login mobion
        }
        
        $this->view->email = $username;
        $this->view->message  = $message;
    }
}

