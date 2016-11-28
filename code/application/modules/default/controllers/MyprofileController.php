<?php
/**
 * @author      :   HoaiTN
 * @name        :   IndexController
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   controller default 
 */

class MyprofileController extends Core_Controller_Action
{
     /* AccountID*/
     private $accountID =0;
     private $globalConfig;
     
     public function init() {
         
        parent::init();
        
        global $globalConfig;
        
        //Asign manager Type
        $this->view->arrManagerType = $globalConfig['manager_type']; 

        //Asign 
        $this->accountID = $this->view->arrLogin['accountID'];
        
        //Get Controller
        $controller = $this->_request->getParam('controller');
        $this->view->controller = $controller;
        $this->globalConfig  = $globalConfig;

    }
    

    
    public function editAction()
    {
      
        $iAccountID = $this->accountID;
        
         //init data
        $arrAccountInfo = array();
        //General Att
        $arrGeneralAtt = array();
        
        //manager account
        $arrAccountManager = array();
        
        //extra profile
        $arrAccountExtra = array();
        
        //Init data
        $sName ='';
        $iActive =1;
        $iOffset =0;
        $iLimit = 30;
        

         //check params
         if($iAccountID>0)
         {
              $arrAccountInfo = AccountInfo::getInstance()->getAccountInfoByAccountID($iAccountID);
              
              if(!empty($arrAccountInfo['direct_manager']) && $arrAccountInfo['direct_manager']>0)
              {
                   $arrAccountManager = AccountInfo::getInstance()->getAccountInfoByAccountID($arrAccountInfo['direct_manager']);
              }
             //get general att
              $arrGeneralAtt= General::getInstance()->getGeneralAtt();
              
              //get Attribute
             $arrAttribute = Attribute::getInstance()->getAttributeList($sName, $iActive, $iOffset, $iLimit);

            //check empty
             if(!empty($arrAttribute))
             {
                $arrAttribute = $arrAttribute['data'];
                $arrAttID = array();
                
                if(!empty($arrAttribute))
                {
                     foreach($arrAttribute as $value)
                     {
                         $arrAttID[] = "'".$value['attribute_id']."'";
                     }
                     
                     //convert from array to string
                     $sarrAttID = implode(',', $arrAttID);

                     $arrAccountExtra = AccountExtra::getInstance()->getAccountExtraList($iAccountID, $sarrAttID);
                    
                }
                  
             }
        
         }
         
         //set to view
         $this->view->arrAccountInfo = $arrAccountInfo;
         $this->view->arrGeneralAtt = $arrGeneralAtt;
         $this->view->managerAccountID = isset($arrAccountManager['account_id'])?$arrAccountManager['account_id']:0;
         $this->view->managerName = isset($arrAccountManager['name'])?$arrAccountManager['name']:0;
         $this->view->arrAttribute = $arrAttribute;
         $this->view->arrAccountExtra = $arrAccountExtra;
    }
    
   /**
     * Default action
     */
    public function updateAction()
    {
        
        $this->_helper->layout()->disableLayout();
        //Disable render
        $this->_helper->viewRenderer->setNoRender();
        
        $error = -1;
        $message ='Please check your information';
        if($this->getRequest()->isPost())
	{
            //get params
            $arrParam  = $this->_request->getParams();
                         
            //get params
            $iAccountID = $this->accountID;
            $iID  = $arrParam['id'];
            $sSkype  = $arrParam['skype'];
            $sMobionAccount  = $arrParam['mobion'];
            
            $sEmail  = trim($arrParam['email']);
            $sName  = $arrParam['name'];
            $sBirthday  = $arrParam['birthday'];
            $sPhone  = $arrParam['phone'];
            $sIdentity  = $arrParam['identity'];
            $sPicture  = $arrParam['picture'];
            $sAboutMe  = $arrParam['aboutme'];
            $iCountryID  = $arrParam['country'];
            $sAddress  = $arrParam['address'];
            $sAttribute  = $arrParam['attids'];
            
         
            
            //Init data
            $arrdata = array(
                'account_id' => $iAccountID,
                'id'         => $iID,
                'name'       => $sName,
                'phone'      => $sPhone,
                'birthday'   => $sBirthday,
                'identity'   => $sIdentity,
                'address'    => $sAddress,
                'skype_account'  => $sSkype,
                'mobion_account' => $sMobionAccount,
                'avatar'         => $sPicture,
                'country_id'     => $iCountryID,
                'description'    => $sAboutMe   
                
            );
            
            //check params
            if(empty($sName) || empty($sEmail))
            {
                 echo Zend_Json::encode(array('error' => $error, 'message' => $message));
                 exit();
            }
            
            
            //check invalid Email
            if(!Core_Valid::isEmail($sEmail))
            {
                $message = 'The Email is not valid, Pls check your email again';
                 echo Zend_Json::encode(array('error' => $error, 'message' => $message));
                 exit();
            }
            
            
            //get Instance
            $accountInstance = AccountInfo::getInstance();
           
            //Update data
            if($iAccountID>0)
            {
                 //Update
               $flag =  $accountInstance->updateMyAccountInfo($arrdata);
                
               //Update to solr search
                if($flag == true)
                {
                    
                    Search::getInstance()->updateMyProfile($iAccountID,$sName,$sPhone,$sBirthday,$sPicture,$iID,$sIdentity, $sAddress,$sSkype,
                         $sMobionAccount, $iCountryID);
                    
                }
                    
                //Return  
                $flag = true; 
                $iUpdate =1;
            }

            
             //check result
            if($iAccountID == 0)
            {
                $message = 'The System can not add account. Pls try again';
                echo Zend_Json::encode(array('error' => $error, 'message' => $message));
                exit();
            }
            else
            {
                if(!empty($sAttribute))
                {
                      //init instance account extra
                      $instanceAccountExtra = AccountExtra::getInstance();
                      
                      //for data 
                      $arrAttribute = explode(',', $sAttribute);
                      foreach($arrAttribute as $value)
                      {
                          
                            if(!empty($arrParam['attribute_'.$value]))
                            {
                                    $instanceAccountExtra->updateAccountExtra($iAccountID,$value, $arrParam['attribute_'.$value]);
                            }
                             
                      }
                    
                }
            }

            //error
            $error =0;
              
        }
        
        echo Zend_Json::encode(array('error' => $error, 'message' => $message));
        exit();
    }
    
    /*
     * Detail  page
     */
    
    public function detailAction()
    {
         //get Param
         $iAccountID = $this->accountID;
         $arrAccountInfo  = array();
         $arrGeneralAttHash = array();
         $arrAccountManager = array();
         $arrAccountExtra = array();
         
          //check params
         if($iAccountID>0)
         {
              //get data
             $arrAccountInfo = AccountInfo::getInstance()->getAccountInfoByAccountID($iAccountID);
             
             if(!empty($arrAccountInfo['direct_manager']) && $arrAccountInfo['direct_manager']>0)
             {
                    //get account manager
                   $arrAccountManager = AccountInfo::getInstance()->getAccountInfoByAccountID($arrAccountInfo['direct_manager']);
             }
             
             //get account extra info 
             //$arrAccountExtra=  AccountExtra::getInstance()->getAccountExtraDetail($iAccountID);
                   
             //General attribute
             $arrGeneralAttHash = General::getInstance()->getGeneralAttHash();
         }
         
         
         //set to view
         $this->view->arrAccountInfo = $arrAccountInfo;
         $this->view->arrGeneralAttHash = $arrGeneralAttHash;
         $this->view->arrAccountManager = $arrAccountManager;
        // $this->view->arrAccountExtra = $arrAccountExtra;
        
    }
    
    
}

