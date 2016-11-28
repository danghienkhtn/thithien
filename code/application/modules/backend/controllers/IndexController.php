<?php
/**
 * @author      :   HoaiTN
 * @name        :   IndexController
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   controller default 
 */


class Backend_IndexController extends Core_Controller_ActionBackend
{
     public function init() {
         
        parent::init();
        
        global $globalConfig;
        
        //Asign manager Type
        $this->view->arrManagerType = $globalConfig['manager_type']; 
        $this->view->arrCountry = $globalConfig['country'];
        
        
    }

    public function realtimechartAction()
    {

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        // The x value is the current JavaScript time, which is the Unix time multiplied by 1000.
        $x = $this->_getParam('_',0);
//        $x = 0;
        // The y value is a random number
//        echo date('Y').'--'.date('m').'--'.date('d');
        $total = UserActive::getInstance()->count(intval(date('Y')),intval(date('m')),intval(date('d')));
        $y = $total;

        // Create a PHP array and echo it as JSON
        $ret = array($x, $y);
        echo Zend_Json::encode($ret);
        exit();
    }

    /**
     * Default action
     */
    public function indexAction()
    {

        // total and percent photo
        $totalPhoto =  StatisticFile::getInstance()->getDirSize(PATH_IMAGES_UPLOAD_DIR);
        $percentTotalPhoto = ($totalPhoto * 100) / PHOTO_LIMIT;

        // total and percent File
        $totalFile =  StatisticFile::getInstance()->getDirSize(PATH_FILES_UPLOAD_DIR);
        $percentTotalFile = ($totalFile * 100) / FILE_LIMIT;

        // get Action Logs
        $actionLogs = ActionLog::getInstance()->select('','','',0,0,0,ADMIN_PAGE_SIZE);

        // return action logs to view
        $this->view->actionLogs = $actionLogs;

        // return total and percent photo to view
        $this->view->totalPhoto = $totalPhoto;
        $this->view->percentTotalPhoto = $percentTotalPhoto;

        // return total and percent File to view
        $this->view->totalFile          = $totalFile;
        $this->view->percentTotalFile   = $percentTotalFile;
        $this->view->userStatistic      = Statistic::getInstance()->getUserActiveInDay(intval(date('Y')),intval(date('m')),intval(date('d')));
    }
    
    /**
     * Default action
     */
    
    public function newAction()
    {
        //Init data
        $sName ='';
        $iActive =1;
        $iOffset =0;
        $iLimit = 30;
        
        //group
        $arrGroup = Group::getInstance()->getGroupListAll(1,2);
        
        //get Type
        $arrGeneralAtt= General::getInstance()->getGeneralAtt();
        
        //get Attribute
        $arrAttribute = Attribute::getInstance()->getAttributeList($sName, $iActive, $iOffset, $iLimit);
        
        //check empty
        if(!empty($arrAttribute))
        {
            $arrAttribute = $arrAttribute['data'];
        }
        
        //Asign to view
        $this->view->iPage = 1;
        $this->view->arrGeneralAtt = $arrGeneralAtt;
        $this->view->arrAttribute = $arrAttribute;
        $this->view->arrGroup    = $arrGroup;
    }
    
    /**
     * Default action
     */
    
    public function updAction()
    {
      
        $iPage = $this->_request->getParam('page', 1);
        $iAccountID = $this->_request->getParam('id', 0);
        
         //init data
        $arrAccountInfo = array();
        //General Att
        $arrGeneralAtt = array();
        
        //manager account
        $arrAccountID = array();
        
        $arrProfileManager = array();
        
        //extra profile
        $arrAccountExtra = array();
        
        //Init data
        $sName ='';
        $iActive =1;
        $iOffset =0;
        $iLimit = 30;
        
         //group
        $arrGroup = Group::getInstance()->getGroupListAll(1,2);
        

         //check params
         if($iAccountID>0)
         {
              $arrAccountInfo = AccountInfo::getInstance()->getAccountInfoByAccountID($iAccountID);
              
              //Direct manager
              if(!empty($arrAccountInfo['direct_manager']) && $arrAccountInfo['direct_manager']>0 && !in_array($arrAccountInfo['direct_manager'], $arrAccountID))
              {
                   $arrAccountID[] = $arrAccountInfo['direct_manager'];
              }
              
              //Leader
              if(!empty($arrAccountInfo['leader_id']) && $arrAccountInfo['leader_id']>0 && !in_array($arrAccountInfo['leader_id'], $arrAccountID))
              {
                   $arrAccountID[] = $arrAccountInfo['leader_id'];
              }
              
              //Manager
              if(!empty($arrAccountInfo['manager_id']) && $arrAccountInfo['manager_id']>0 && !in_array($arrAccountInfo['manager_id'], $arrAccountID))
              {
                   $arrAccountID[] = $arrAccountInfo['manager_id'];
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
         
         if(!empty($arrAccountID))
         {
             $arrProfileManager = AccountInfo::getInstance()->getAccountListShort($arrAccountID);
         }
         
         
         //set to view
         $this->view->arrAccountInfo = $arrAccountInfo;
         $this->view->iPage = $iPage;
         $this->view->arrGeneralAtt = $arrGeneralAtt;
         $this->view->arrProfileManager = $arrProfileManager;
         $this->view->arrAttribute = $arrAttribute;
         $this->view->arrAccountExtra = $arrAccountExtra;
         $this->view->arrGroup    = $arrGroup;
    }
    
   /**
     * Default action
     */
    public function addAction()
    {

        $this->_helper->layout()->disableLayout();
        //Disable render
        $this->_helper->viewRenderer->setNoRender();

        $error = -1;
        $message = 'Please check your information';
        if ($this->getRequest()->isPost()) {

            //get params
            $arrParam = $this->_request->getParams();

            //get params
            $iAccountID = $arrParam['accountid'];
            $sSkype = $arrParam['skype'];
            $sEmail = trim($arrParam['email']);
            $sName = $arrParam['name'];
            $sBirthday = $arrParam['birthday'];
            $sPhone = $arrParam['phone'];
            $sIdentity = $arrParam['identity'];
            $sPicture = $arrParam['picture'];
            $arrDirectManager = $arrParam['directmanager'];
            $arrManager = $arrParam['manager'];
            $arrLeader = $arrParam['leader'];


            $sSignDate = $arrParam['signday'];
            $sStartDate = $arrParam['startday'];
            $sEndDate = $arrParam['endday'];
            $sTaxCode = $arrParam['taxcode'];
            $sAboutMe = $arrParam['aboutme'];
            $sAddress = $arrParam['address'];
            $sAttribute = $arrParam['attids'];
            $sAvatar = $arrParam['avatar'];

            $iTeamID = intval($arrParam['team']);


            $iDirectManager = 0;
            $iManager = 0;
            $iLeader = 0;
            $iUpdate = 0;

            //check Directmanager
            if (!empty($arrDirectManager) && is_array($arrDirectManager)) {
                $iDirectManager = $arrDirectManager[0];
            }

            //check Manager
            if (!empty($arrManager) && is_array($arrManager)) {
                $iManager = $arrManager[0];
            }

            //check Leader
            if (!empty($arrLeader) && is_array($arrLeader)) {
                $iLeader = $arrLeader[0];
            }

            //check DirectManager
            if ($iDirectManager == 0) {
                $iDirectManager = ($iLeader > 0) ? $iLeader : $iManager;
            }


            //Init data
            $arrData = array(
                'account_id' => $iAccountID,
                'name' => $sName,
                'email' => $sEmail,
                'phone' => $sPhone,
                'birthday' => $sBirthday,
                'picture' => $sPicture,
                'avatar' => $sAvatar,
                'id' => intval($arrParam['id']),
                'identity' => $sIdentity,
                'tax_code' => $sTaxCode,
                'address' => $sAddress,
                'position' => intval($arrParam['position']),
                'department_id' => 0,
                'team_id' => $iTeamID,
                'leader_id' => $iLeader,
                'manager_id' => $iManager,
                'direct_manager' => $iDirectManager,
                'skype_account' => $sSkype,
                'mobion_account' => '',
                'start_date' => $sStartDate,
                'end_date' => $sEndDate,
                'contract_type' => intval($arrParam['contracttype']),
                'contract_sign_date' => $sSignDate,
                'country_id' => intval($arrParam['country']),
                'description' => $sAboutMe,
                'status' => 0,
                'active' => 1,
                'username' => '',
                'team_name' => '',
                'manager_type' => intval($arrParam['managertype']),
                'top_people' => intval($arrParam['toppeople']),
                'create_date' => '',
                'update_date' => ''
            );


            //check params
            if (empty($sName) || empty($sEmail)) {
                echo Zend_Json::encode(array('error' => $error, 'message' => $message));
                exit();
            }


            //check invalid Email
            if (!Core_Valid::isEmail($sEmail)) {
                $message = 'The Email is not valid, Pls check your email again';
                echo Zend_Json::encode(array('error' => $error, 'message' => $message));
                exit();
            }


            //get Instance
            $accountInstance = AccountInfo::getInstance();

            //Update data
            if ($iAccountID > 0) {
                //Update
                $flag = $accountInstance->updateAccountInfo($arrData);


                //Update to solr search
                if ($flag == true) {

                    Search::getInstance()->update($arrData);

                }

                //Return  
                $flag = true;
                $iUpdate = 1;
            } else {
                //check email
                if (!empty($sEmail)) {
                    $arrUserName = explode('@', $sEmail);
                    if (count($arrUserName) == 2) {
                        $sUserName = $arrUserName[0];
                    }
                }


                //check Exist email
                $arrAccountInfo = $accountInstance->getAccountInfoByUserName($sUserName);

                if (!empty($arrAccountInfo)) {
                    $message = 'The Email has existsed in the system. Pls choose other email';
                    echo Zend_Json::encode(array('error' => $error, 'message' => $message));
                    exit();
                }


                //check Ldap
                $arrAccountLDap = Ldap::getInstance()->getAccountInfo($sUserName, $this->view->arrLogin['email'], base64_decode($this->view->arrLogin['ps']));

                if (empty($arrAccountLDap)) {

                    $message = 'The Email is not valid in the system. Pls check again';
                    echo Zend_Json::encode(array('error' => $error, 'message' => $message));
                    exit();
                }

                //check empty avatar
                if (empty($sPicture)) {
                    $sPicture = isset($arrAccountLDap['picture']) ? $arrAccountLDap['picture'] : '';
                }

                //check avatar
                if (empty($sAvatar)) {
                    $sAvatar = $sPicture;
                }

                //get Team IT
                $sTeamName = isset($arrAccountLDap['team_name']) ? $arrAccountLDap['team_name'] : '';

                $sEmail = empty($arrAccountLDap['mail']) ? $sEmail : $arrAccountLDap['mail'];

                //Init data mail
                $arrData['email'] = $sEmail;
                $arrData['team_name'] = $sTeamName;
                $arrData['picture'] = $sPicture;
                $arrData['avatar'] = $sAvatar;
                $arrData['username'] = $sUserName;

                //Add Data
                $iAccountID = $accountInstance->insertAccountInfo($arrData);

                //Insert Solr
                if ($iAccountID > 0) {
                    $arrData['account_id'] = $iAccountID;
                    Search::getInstance()->insert($arrData);

                }


            }

            //check result
            if ($iAccountID == 0) {
                $message = 'The System can not add account. Pls try again';
                echo Zend_Json::encode(array('error' => $error, 'message' => $message));
                exit();
            } else {


                if ($iTeamID > 0) {
                    //init data to group  
                    $arrDataMember = array(
                        'group_id' => $iTeamID,
                        'account_id' => $iAccountID,
                        'level' => GroupMember::$staff
                    );

                    //Update
                    GroupMember::getInstance()->addGroupMember($arrDataMember);

                    //Delete cache
                    Group::getInstance()->deleteCacheUser($iAccountID);

                    //remove cache group
                    Group::getInstance()->removeCache($iTeamID);

                }


                if (!empty($sAttribute)) {
                    //init instance account extra
                    $instanceAccountExtra = AccountExtra::getInstance();

                    //for data
                    $arrAttribute = explode(',', $sAttribute);
                    foreach ($arrAttribute as $value) {

                        if (!empty($arrParam['attribute_' . $value])) {
                            $instanceAccountExtra->updateAccountExtra($iAccountID, $value, $arrParam['attribute_' . $value]);
                        }

                    }

                }
            }

            //error
            $error = 0;

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
         $iAccountID = $this->_request->getParam('id', 0);
         $arrAccountInfo  = array();
         $arrGeneralAttHash = array();
         $arrAccountManager = array();
         $arrAccountExtra = array();
         $arrGroupName = array();
         $arrAccountID = array();
         
          //check params
         if($iAccountID>0)
         {
              //get data
             $arrAccountInfo = AccountInfo::getInstance()->getAccountInfoByAccountID($iAccountID);
             
            //Direct manager
              if(!empty($arrAccountInfo['direct_manager']) && $arrAccountInfo['direct_manager']>0 && !in_array($arrAccountInfo['direct_manager'], $arrAccountID))
              {
                   $arrAccountID[] = $arrAccountInfo['direct_manager'];
              }
              
              //Leader
              if(!empty($arrAccountInfo['leader_id']) && $arrAccountInfo['leader_id']>0 && !in_array($arrAccountInfo['leader_id'], $arrAccountID))
              {
                   $arrAccountID[] = $arrAccountInfo['leader_id'];
              }
              
              //Manager
              if(!empty($arrAccountInfo['manager_id']) && $arrAccountInfo['manager_id']>0 && !in_array($arrAccountInfo['manager_id'], $arrAccountID))
              {
                   $arrAccountID[] = $arrAccountInfo['manager_id'];
              }
              
             
             //get account extra info 
             $arrAccountExtra=  AccountExtra::getInstance()->getAccountExtraDetail($iAccountID);
                   
             //General attribute
             $arrGeneralAttHash = General::getInstance()->getGeneralAttHash();
             
             
             if(!empty($arrAccountInfo))
             {
                 $arrGroupName = Group::getInstance()->getGroupList2(array($arrAccountInfo['team_id'])); 
                 
             }
              
         }
         
         
          
         if(!empty($arrAccountID))
         {
             $arrAccountManager = AccountInfo::getInstance()->getAccountListShort($arrAccountID);
         }
         
         
         //set to view
         $this->view->arrAccountInfo = $arrAccountInfo;
         $this->view->arrGeneralAttHash = $arrGeneralAttHash;
         $this->view->arrAccountExtra = $arrAccountExtra;
         $this->view->arrGroupName  = $arrGroupName;
         $this->view->arrAccountManager = $arrAccountManager;
    }
    
    /**
     * Default action
     */
    public function deleteAction()
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
            $iAccountID  = $arrParam['id'];
            if($iAccountID>0)
            {
                
                $iActive =10;
                $flag= AccountInfo::getInstance()->updateAccountInfoStatus($iAccountID, $iActive);
                if($flag ==true)
                {
                    //Delete Search
                    Search::getInstance()->delete($iAccountID);
                }
                
            }

            $error =0; 
        }
        
        echo Zend_Json::encode(array('error' => $error, 'message' => $message));
        exit();
    }
    
    /*
     *  Upload
     */
    
    public function uploadAction()
    {
        
        $this->_helper->layout()->disableLayout();
        //Disable render
        $this->_helper->viewRenderer->setNoRender();
        
        $iUploadType = $this->_request->getParam('type', 0);
        
        //For Avatar image
        $sPathUpload = PATH_AVATAR_UPLOAD_DIR;
        $sImageUrl = PATH_AVATAR_URL;
        
        //For News Image
        if($iUploadType ==1)
        {
             $sPathUpload = PATH_NEWS_UPLOAD_DIR;
             $sImageUrl = PATH_NEWS_URL;
        }
        
        
        $error =1;
        
        $filename = $_FILES['file_upload']['tmp_name'];
        $valid_formats = array("jpg", "png", "gif", "bmp", "jpeg","GIF","JPG","PNG");
        
        $name = $_FILES['file_upload']['name'];
        list($txt, $ext) = explode(".", $name);
         
        $imageName = str_replace(" ", "_", $txt)."_".time();
        $actual_image_name =  $imageName.".".$ext;
        $thumb_image_name =  'thumb'.$imageName.".".$ext;
        
        //File original
        $sPathOriginalFile = $sPathUpload.'/original/'.$actual_image_name;
        
        // File Thumb
        $sPathFile = $sPathUpload.'/'.$actual_image_name;
        
         if(move_uploaded_file($filename, $sPathOriginalFile))
         {
              Core_Common::generate_image_thumbnail($sPathOriginalFile, $sPathFile);
              $error =0;
         }
         
         //Result
         $arrResult = array(
             'error'        => $error,
             'image_id'     => $imageName,
             'image_format' => $ext,
             'image_url'    => $sImageUrl
         );
         
         echo '<script type="text/javascript">
                    document.domain = "'.DOMAIN.'";
               </script>
               <div class="response">
                 <div class="image_id">'.$arrResult['image_id'].'</div>
                 <div class="image_format">'.$arrResult['image_format'].'</div>
                 <div class="image_url">'.$arrResult['image_url'].'</div>
               </div>';
 
               exit();  
    }
    
    public function autocompleteAction()
    {
        
        $arrResult = array();
        
        $this->_helper->layout()->disableLayout();
        //Disable render
        $this->_helper->viewRenderer->setNoRender();
        
        $sName= $this->_request->getParam('tag', '');
        
        if(!empty($sName))
        {
            $sName = urldecode(trim($sName));

             $iStart =0;
             $iPageSize = 50;
             
             $sSort ='';
             
             //Search Init
             $arrSearch= array(
                            'name'      => $sName,
                            'email'     => '',
                            'id'        => 0,
                            'identity'  => 0,
                            'taxcode'   => '',
                            'position'  => 0,
                            'departmentid'  => 0,
                            'teamid'        => 0  
                 );
             
        
            //get data search
           $arrTmp = Search::getInstance()->getProfileSearch($arrSearch, $iStart, $iPageSize, $sSort);

            if(!empty($arrTmp['data']))
            {
                  //Asign data
                  $arrTmp = $arrTmp['data'];
                  
                  foreach($arrTmp as $value)
                  {

                      $arrResult[] = array('id' => $value['account_id'], 'fullname' => $value['name']);
                  }
            }
            
        }
        
        echo json_encode($arrResult);
        
        exit();
    }
    
    public function avatarAction($username)
    {
        
        $iTotal =0;
        
        $ldap_columns = NULL;
        $ldap_connection = NULL;
        $ldap_password = 'hoaigntvietnam@';
        $ldap_username = 'hoai.tn@gnt-global.com';
        $ldap_host = 'dc.gnt-global.com';
        
        $picture ='';
        $dirPic = '/usr/local/src/www/InternalProject/upload/avatar';

        //------------------------------------------------------------------------------
        // Connect to the LDAP server.
        //------------------------------------------------------------------------------
        $ldap_connection = ldap_connect($ldap_host);
        if (FALSE === $ldap_connection){
            die("<p>Failed to connect to the LDAP server: ". $ldap_host ."</p>");
        }

        ldap_set_option($ldap_connection, LDAP_OPT_PROTOCOL_VERSION, 3) or die('Unable to set LDAP protocol version');
        ldap_set_option($ldap_connection, LDAP_OPT_REFERRALS, 0); // We need this for doing an LDAP search.

        if (TRUE !== ldap_bind($ldap_connection, $ldap_username, $ldap_password)){
            die('<p>Failed to bind to LDAP server.</p>');
        }

                 
        $ldap_base_dn = 'DC=gnt-global,DC=com';
        $search_filter = "(&(objectCategory=person))";
        
        $search_filter ='(&(objectClass=user)(objectCategory=person)(!(userAccountControl:1.2.840.113556.1.4.803:=2)))';
      
        
        $search_filter="(|(mail=".$username."@*))";
        $result = ldap_search($ldap_connection, $ldap_base_dn, $search_filter, array('thumbnailphoto', 'jpegphoto'));
        
        if (FALSE !== $result){
            $entries = ldap_get_entries($ldap_connection, $result);
            
            if ($entries['count'] > 0)
            {
                
                $odd = 0;
                foreach ($entries[0] AS $key => $value){
                    if (0 === $odd%2){
                        $ldap_columns[] = $key;
                    }
                    $odd++;
                }
                
         
                for ($i = 0; $i < $entries['count']; $i++)
                {
                    
                    foreach ($ldap_columns AS $col_name)
                    {
                       
                        if (isset($entries[$i][$col_name]))
                        {
                            
                           
                           if($col_name == 'thumbnailphoto' && !empty($entries[$i][$col_name][0]))
                           {
                               
                               Header("Content-type: image/jpeg");
               
                               $picture = $username.'.jpg';
                                
                                $f = fopen($dirPic."/".$picture,"w");
                                fwrite($f,$entries[$i][$col_name][0]);

                           }
                           
                           
                           if($col_name == 'jpegphoto' && !empty($entries[$i][$col_name][0]))
                           {
                               
                               Header("Content-type: image/jpeg");
                               $picture = $username.'.jpg';
                                
                                $f = fopen($dirPic."/".$picture,"w");
                                fwrite($f,$entries[$i][$col_name][0]);
                           }
                           
                        }
                    }
                    

                }
            }
        }
        
        ldap_unbind($ldap_connection); // Clean up after ourselves.
        
        return $picture;
    }
    
    
}

