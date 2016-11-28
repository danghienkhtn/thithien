<?php
/**
 * Created by PhpStorm.
 * User: hien.nd
 * Date: 077/07/2016
 * Time: 14:33 AM
 */

class Api_DocsController extends Zend_Controller_Action{

    // public $actionDocs = array();    
    /**
     * init of controller
     */
    public function init()
    {
        //Disale layout
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        global $globalConfig;
        global $actionDocs;
        $actionDocs = array(
            'createfile' => $this->view->locales->actiondocslog->createfile,
            'createfolder' => $this->view->locales->actiondocslog->createfolder, 
            'updatefile' => $this->view->locales->actiondocslog->updatefile, 
            'deletefile' => $this->view->locales->actiondocslog->deletefile, 
            'deletefolder' => $this->view->locales->actiondocslog->deletefolder, 
            'renamefile' => $this->view->locales->actiondocslog->renamefile, 
            'renamefolder' => $this->view->locales->actiondocslog->renamefolder, 
            'copyfile' => $this->view->locales->actiondocslog->copyfile, 
            'copyfolder' => $this->view->locales->actiondocslog->copyfolder, 
            'movefile' => $this->view->locales->actiondocslog->movefile, 
            'movefolder' => $this->view->locales->actiondocslog->movefolder, 
            'uploadfile' => $this->view->locales->actiondocslog->uploadfile,
            'uploadfolder' => $this->view->locales->actiondocslog->uploadfolder,
            'downloadfile' => $this->view->locales->actiondocslog->downloadfile,
            'downloadfolder' => $this->view->locales->actiondocslog->downloadfolder,
            'editfile' => $this->view->locales->actiondocslog->editfile,
            'sharefile' => $this->view->locales->actiondocslog->sharefile,
            'sharefolder' => $this->view->locales->actiondocslog->sharefolder,
            'resquestsharefile' => $this->view->locales->actiondocslog->requestsharefile
        );


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
        $serverInstance->setClass('ApiDocs');

        //Hanlde instance
        $serverInstance->handle($this->_request);
    }

    public function editFileAction()
    {        
        $this->_helper->layout->setLayout('edit_file');
        echo $this->_helper->layout->render();
        exit;
    }

    public function detailFileAction()
    {
        $this->_helper->layout->setLayout('detail_file');
        echo $this->_helper->layout->render();
        exit;
    }

    public function imgCarouselAction()
    {
        $this->_helper->layout->setLayout('img_carousel');
        echo $this->_helper->layout->render();
        exit;
    }

    public function requestShareDocsAction()    
    {
        $fileId = $this->_getParam('fileId', 0);        
        $share_type = "account";
        $iPermission = "";
        $fileInfo = array();
        $accInfo = array();
        $groupInfo = array();
        $parentInfo = array();
        $groupName = "";
        $parentName = "";
        $arrLogin = Admin::getInstance()->getLogin();
        if(!isset($arrLogin['accountID']) || empty($arrLogin['accountID'])){
            echo Zend_Json::encode(Core_Server::setOutputData(true, 'You must login!', array()));
            exit;
        }           

        if($fileId > 0){            
            $fileInfo = File::getInstance()->selectOne($fileId);
            if($fileInfo){
                if($fileInfo['parent'] > 0){
                    $parentInfo = File::getInstance()->selectOne($fileInfo['parent']);
                    $parentName = $parentInfo['name'];
                }
                if($fileInfo['group_id'] > 0){
                    $groupInfo = Group::getInstance()->getGroupByID($fileInfo['group_id']);
                    $groupName = $groupInfo['group_name'];
                }
                $share_account_id = $arrLogin['accountID'];
                $accOwnerInfo = AccountInfo::getInstance()->getAccountInfoByAccountID($fileInfo['owner']);
                if($accOwnerInfo){
                    //check permission existed
                    $arrShare = ShareDocs::getInstance()->checkDocsShareExisted($fileId, 'account', $share_account_id);                              
                    if(sizeof($arrShare)){
                        echo Zend_Json::encode(Core_Server::setOutputData(true, 'This user had been shared', array("data"=>Zend_Json::encode($arrShare))));
                        exit;
                    }
                    // $fileUrl = "/docs/editor/id/".$fileId;
                    if($fileInfo['type'] == 0){
                        // $downloadURL = $this->getDownloadDocsPath($fileId);
                        $downloadURL = BASE_URL. DIRECTORY_SEPARATOR . "api" . DIRECTORY_SEPARATOR . "docs" . DIRECTORY_SEPARATOR . "download-docs?fileId=".$fileId;
                        $fileUrl = $this->getURLDocsFile($fileId, $fileInfo['path'], $fileInfo['name'], $arrLogin['accountID'], $fileInfo['group_id']);   
                    }else{
                        $fileUrl = "";
                        $downloadURL = "";
                    }
                    $ishare = ShareDocs::getInstance()->insert($fileId, $fileInfo['name'], $fileInfo['type'], $fileInfo['path'], $fileUrl, $downloadURL, $iShareType='user', $iPermission, $fileInfo['group_id'], $groupName, $fileInfo['parent'], $parentName, $fileInfo['owner'], $accOwnerInfo['name'], $accOwnerInfo['avatar'], $accOwnerInfo['email'], $arrLogin['accountID'], $arrLogin['nickName'], $arrLogin['avatar'], $arrLogin['email'], $sNote = 'request share user', $iShareGroupID = 0, $iShareGroupName = "", $iShareGroupAvatar = "", $sStatus="pending");
                    if(!empty($ishare)){
                        //add a action Docs log
                        ShareDocs::getInstance()->addActionlog($iAction="resquestsharefile", $fileInfo['owner'], $accOwnerInfo['name'], $accOwnerInfo['avatar'], $fileInfo['group_id'], $groupName, $fileInfo['parent'], $parentName, $fileId, $fileInfo['name'], $fileInfo['type'], $fileInfo['path'], $fileUrl, $detail = array(), $sNote = 'request to share file', $arrLogin['accountID'], $arrLogin['nickName'], $arrLogin['avatar'],  $share_group_id = 0, $share_group_name = "", $share_group_avatar = "");
                        $arrResult = array("share_id" => $ishare, "owner_account_id" => $fileInfo['owner'], "owner_account_name" => $accOwnerInfo['name'], "owner_account_email" => $accOwnerInfo['email'], "owner_account_avatar" => $accOwnerInfo['avatar']);
                        echo Zend_Json::encode(Core_Server::setOutputData(false, 'OK', array("data" => Zend_Json::encode( $arrResult ))));
                        exit;
                    }
                }
                else{
                    echo Zend_Json::encode(Core_Server::setOutputData(true, 'Account does not existed', array()));
                    exit;   
                }
            }
        }
        echo Zend_Json::encode(Core_Server::setOutputData(true, 'Pemission denied!', array()));     
        exit;
    }

    public function addShareDocsAction()    
    {
        $fileId = $this->_getParam('fileId', 0);
        $share_account = $this->_getParam('shareAccount', 0);
        $share_type = $this->_getParam('shareType', "account");
        $iPermission = $this->_getParam('permission', "view");
        $fileInfo = array();
        $accInfo = array();
        $groupInfo = array();
        $parentInfo = array();
        $groupName = "";
        $parentName = "";
        global $globalConfig;
        global $actionDocs;
        $arrLogin = Admin::getInstance()->getLogin();
        if(!isset($arrLogin['accountID']) || empty($arrLogin['accountID'])){
            echo Zend_Json::encode(Core_Server::setOutputData(true, 'You must login!', array()));
            exit;
        }           

        if($fileId > 0){            
            $fileInfo = File::getInstance()->selectOne($fileId);
            if($fileInfo){
                if($fileInfo['group_id'] > 0){
                    $groupInfo = Group::getInstance()->getGroupByID($fileInfo['group_id']);
                    $groupName = $groupInfo['group_name'];
                    $levelMemberGroup = GroupMember::getInstance()->getMemberGroupLevelById($fileInfo['group_id'], $arrLogin["accountID"]);
                }
                else{
                    $groupName = "";
                    $levelMemberGroup = 0;
                }
                if($fileInfo['owner'] == $arrLogin['accountID'] || in_array((int)$levelMemberGroup, $globalConfig['ManagerDocsLevel']))
                {                                                            
                    if($fileInfo['parent'] > 0){
                        $parentInfo = File::getInstance()->selectOne($fileInfo['parent']);
                        $parentName = $parentInfo['name'];
                    }                    
                    if($share_type == "group"){
                        $share_group_id = $share_account;
                        //share to a group
                        if($share_group_id > 0){
                            // check group existed
                            $group = Group::getInstance()->getGroupByID($share_group_id);            
                            if(empty($group)) {
                               echo  Zend_Json::encode(Core_Server::setOutputData(true, 'Group is not existed!', array()));
                               exit;
                            }
                            //check permision user in group
                            /*$groupMember = GroupMember::getInstance()->getGroupMemberByAccountAndGroupId($arrLogin['accountID'], $share_group_id);
                            if(empty($groupMember) && $group['admin_id'] != $arrLogin['accountID']){
                                echo Zend_Json::encode(Core_Server::setOutputData(true, 'Group permission denied', array()));
                                exit;
                            }*/
                            //check permission existed
                            $arrShare = ShareDocs::getInstance()->checkDocsShareExisted($fileId, 'group', $share_group_id);
                            if(sizeof($arrShare)){
                                echo Zend_Json::encode(Core_Server::setOutputData(true, 'This group had been shared', array("data"=>Zend_Json::encode($arrShare))));
                                exit;
                            }
                            $checkGroup = GroupMember::getInstance()->getGroupMemberByAccountAndGroupId($arrLogin['accountID'], $share_group_id);
                            if(sizeof($checkGroup)){
                                $groupInfo = Group::getInstance()->getGroupByID($share_group_id);
                                $share_group_name = $groupInfo['group_name'];
                                $share_group_avatar = $groupInfo['image_url'];
                                // $fileUrl = "/docs/editor/id/".$fileId;
                                if($fileInfo['type'] == 0){
                                    // $downloadURL = $this->getDownloadDocsPath($fileId);
                                    $downloadURL = BASE_URL. DIRECTORY_SEPARATOR . "api" . DIRECTORY_SEPARATOR . "docs" . DIRECTORY_SEPARATOR . "download-docs?fileId=".$fileId;
                                    $fileUrl = $this->getURLDocsFile($fileId, $fileInfo['path'], $fileInfo['name'], $arrLogin['accountID'], $fileInfo['group_id']);    
                                }else{
                                    $fileUrl = "";
                                    $downloadURL = "";
                                }
                                // new group share
                                $ishare = ShareDocs::getInstance()->insert($fileId, $fileInfo['name'], $fileInfo['type'], $fileInfo['path'], $fileUrl, $downloadURL, $iShareType='group', $iPermission, $fileInfo['group_id'], $groupName, $fileInfo['parent'], $parentName, $arrLogin['accountID'], $arrLogin['nickName'], $arrLogin['avatar'], $arrLogin['email'], $share_account_id = 0, $share_account_name = "", $share_account_avatar ="", $share_account_email = "", $sNote = 'share group', $iShareGroupID = $share_group_id, $iShareGroupName = $share_group_name, $iShareGroupAvatar = $share_group_avatar, $sStatus="allow");
                                if(!empty($ishare)){
                                    //add action log
                                    ShareDocs::getInstance()->addActionlog($iAction="sharefile", $arrLogin['accountID'], $arrLogin['nickName'], $arrLogin['avatar'], $fileInfo['group_id'], $groupName, $fileInfo['parent'], $parentName, $fileId, $fileInfo['name'], $fileInfo['type'], $fileInfo['path'], $fileUrl, $detail = array(), $sNote = '', $share_account_id = 0, $share_account_name = "", $share_account_avatar = "",  $share_group_id, $share_group_name, $share_group_avatar);
                                    ShareDocs::getInstance()->sendNotification($iAction="sharefile", $fileId, $fileInfo['path'], $fileInfo['name'], $fileInfo['group_id'], $arrLogin['accountID'], $arrLogin['nickName'], $arrLogin['avatar'], $actionDocs);
                                    //return
                                    $arrResult = array("_id" => $ishare, 
                                        "file_id"=>$fileId,
                                        "file_name"=>$fileInfo['name'], 
                                        "file_type"=>$fileInfo['type'],
                                        "file_path"=>$fileInfo['path'],
                                        "file_url"=>$fileUrl,                                        
                                        "share_type"=>"group",
                                        "permission"=>$iPermission,
                                        "group_id"=>$fileInfo['group_id'],
                                        "group_name"=>$groupName,
                                        "parent_id"=>$fileInfo['parent'],
                                        "parent_name"=>$parentName,
                                        "owner_account_id"=>$arrLogin['accountID'],
                                        "owner_account_name"=>$arrLogin['nickName'],
                                        "owner_account_avatar"=>$arrLogin['avatar'],
                                        "owner_account_email"=>$arrLogin['email'],
                                        "share_group_id"=>$share_group_id,
                                        "share_group_name"=>$share_group_name,
                                        "share_group_avatar"=>$share_group_avatar,
                                        "share_account_id"=>0,
                                        "share_account_name"=>"",
                                        "share_account_avatar"=>"",
                                        "share_account_email"=>"",
                                        "note"=>$sNote,
                                        "status"=>$sStatus
                                        ); 
                                    // $arrResult = array("share_id" => $ishare,"share_type" => "group", "share_group_id" => $share_group_id, "share_group_name" => $groupName, "share_group_avatar" => $share_group_avatar, "permission" => $iPermission);
                                    echo Zend_Json::encode(Core_Server::setOutputData(false, 'OK', array("data" => Zend_Json::encode( $arrResult ))));
                                    exit;
                                }                            
                            }
                            else{
                                echo Zend_Json::encode(Core_Server::setOutputData(true, 'Group permission denied', array()));
                                exit;
                            }

                        }
                    }
                    else{
                        $share_account_id = $share_account;
                        if($share_account_id == $arrLogin['accountID'])
                        {
                            echo Zend_Json::encode(Core_Server::setOutputData(true, 'You can not share yourself!', array()));
                            exit;
                        }    
                        //share to a user
                        if($share_account_id > 0){                                                                        
                            $accInfo = AccountInfo::getInstance()->getAccountInfoByAccountID($share_account_id);
                            if($accInfo){
                                //check permission existed
                                $arrShare = ShareDocs::getInstance()->checkDocsShareExisted($fileId, 'account', $share_account_id);                              
                                if(sizeof($arrShare)){
                                    echo Zend_Json::encode(Core_Server::setOutputData(true, 'This user had been shared', array("data"=>Zend_Json::encode($arrShare))));
                                    exit;
                                }
                                // $fileUrl = "/docs/editor/id/".$fileId;
                                if($fileInfo['type'] == 0){
                                    // $downloadURL = $this->getDownloadDocsPath($fileId);
                                    $downloadURL = BASE_URL. DIRECTORY_SEPARATOR . "api" . DIRECTORY_SEPARATOR . "docs" . DIRECTORY_SEPARATOR . "download-docs?fileId=".$fileId;
                                    $fileUrl = $this->getURLDocsFile($fileId, $fileInfo['path'], $fileInfo['name'], $arrLogin['accountID'], $fileInfo['group_id']);    
                                }else{
                                    $fileUrl = "";
                                    $downloadURL = "";
                                }
                                $ishare = ShareDocs::getInstance()->insert($fileId, $fileInfo['name'], $fileInfo['type'], $fileInfo['path'], $fileUrl, $downloadURL, $iShareType='user', $iPermission, $fileInfo['group_id'], $groupName, $fileInfo['parent'], $parentName, $arrLogin['accountID'], $arrLogin['nickName'], $arrLogin['avatar'], $arrLogin['email'], $share_account_id, $accInfo['name'], $accInfo['avatar'], $accInfo['email'], $sNote = 'share user', $iShareGroupID = 0, $iShareGroupName = "", $iShareGroupAvatar = "", $sStatus="allow");
                                if(!empty($ishare)){
                                    //add a action Docs log
                                    ShareDocs::getInstance()->addActionlog($iAction="sharefile", $arrLogin['accountID'], $arrLogin['nickName'], $arrLogin['avatar'], $fileInfo['group_id'], $groupName, $fileInfo['parent'], $parentName, $fileId, $fileInfo['name'], $fileInfo['type'], $fileInfo['path'], $fileUrl, $detail = array(), $sNote = '', $share_account_id, $accInfo['name'], $accInfo['avatar'],  $share_group_id = 0, $share_group_name = "", $share_group_avatar = "");
                                    // send notification
                                    ShareDocs::getInstance()->sendNotification($iAction="sharefile", $fileId, $fileInfo['path'], $fileInfo['name'], $fileInfo['group_id'], $arrLogin['accountID'], $arrLogin['nickName'], $arrLogin['avatar'], $actionDocs);
                                    $arrResult = array("_id" => $ishare, 
                                        "file_id"=>$fileId,
                                        "file_name"=>$fileInfo['name'], 
                                        "file_type"=>$fileInfo['type'],
                                        "file_path"=>$fileInfo['path'],
                                        "file_url"=>$fileUrl,                                        
                                        "share_type"=>"user",
                                        "permission"=>$iPermission,
                                        "group_id"=>$fileInfo['group_id'],
                                        "group_name"=>$groupName,
                                        "parent_id"=>$fileInfo['parent'],
                                        "parent_name"=>$parentName,
                                        "owner_account_id"=>$arrLogin['accountID'],
                                        "owner_account_name"=>$arrLogin['nickName'],
                                        "owner_account_avatar"=>$arrLogin['avatar'],
                                        "owner_account_email"=>$arrLogin['email'],
                                        "share_group_id"=>$iShareGroupID,
                                        "share_group_name"=>$iShareGroupName,
                                        "share_group_avatar"=>$iShareGroupAvatar,
                                        "share_account_id"=>$share_account_id,
                                        "share_account_name"=>$accInfo['name'],
                                        "share_account_avatar"=>$accInfo['avatar'],
                                        "share_account_email"=>$accInfo['email'],
                                        "note"=>$sNote,
                                        "status"=>$sStatus
                                        );
                                    // $arrResult = array("share_id" => $ishare, "share_account_id" => $share_account_id, "share_account_name" => $accInfo['name'], "share_account_email" => $accInfo['email'], "share_account_avatar" => $accInfo['avatar'], "permission" => $iPermission);
                                    echo Zend_Json::encode(Core_Server::setOutputData(false, 'OK', array("data" => Zend_Json::encode( $arrResult ))));
                                    exit;
                                }
                            }
                            else{
                                echo Zend_Json::encode(Core_Server::setOutputData(true, 'Account does not existed', array()));
                                exit;   
                            }    
                        }
                    }
                    
                                        
                }
            }
        }
        echo Zend_Json::encode(Core_Server::setOutputData(true, 'Pemission denied!', array()));     
        exit;
    }        
    
     public function notifyShareDocsAction()    
    {
        $fileId = $this->_getParam('fileId', 0);
        $share_account = $this->_getParam('shareAccount', 0);
        $share_type = $this->_getParam('shareType', "account");
        global $actionDocs;
        
        $arrLogin = Admin::getInstance()->getLogin();
        if(!isset($arrLogin['accountID']) || empty($arrLogin['accountID'])){
            echo Zend_Json::encode(Core_Server::setOutputData(true, 'You must login!', array()));
            exit;
        }           

        if($fileId > 0) {            
            $fileInfo = File::getInstance()->selectOne($fileId);
            if($fileInfo){
                if($share_type == "group"){
                    $share_group_id = $share_account;
                    
                    //share to a group
                    if($share_group_id > 0){
                        // check group existed
                        $group = Group::getInstance()->getGroupByID($share_group_id);            
                        if(empty($group)) {
                           echo  Zend_Json::encode(Core_Server::setOutputData(true, 'Group is not existed!', array()));
                           exit;
                        }
                        
                        ShareDocs::getInstance()->sendNotificationNew($iAction="sharefile", $fileId, $fileInfo['path'], $fileInfo['name'], $share_group_id, $arrLogin['accountID'], $arrLogin['nickName'], $arrLogin['avatar'], $actionDocs);
                        
                        echo Zend_Json::encode(Core_Server::setOutputData(false, 'OK', array()));
                        exit;
                    }
                }
                else{
                    $share_account_id = $share_account;
                    if($share_account_id == $arrLogin['accountID'])
                    {
                        echo Zend_Json::encode(Core_Server::setOutputData(true, 'You can not share yourself!', array()));
                        exit;
                    }    
                    //share to a user
                    if($share_account_id > 0){                                                                        
                        $accInfo = AccountInfo::getInstance()->getAccountInfoByAccountID($share_account_id);
                        if($accInfo){
                            // send notification
                            ShareDocs::getInstance()->sendNotificationNew($iAction="sharefile", $fileId, $fileInfo['path'], $fileInfo['name'], null, $arrLogin['accountID'], $arrLogin['nickName'], $arrLogin['avatar'], $actionDocs, null, $share_account_id);

                            echo Zend_Json::encode(Core_Server::setOutputData(false, 'OK', array()));
                            exit;
                        }
                        else{
                            echo Zend_Json::encode(Core_Server::setOutputData(true, 'Account does not existed', array()));
                            exit;   
                        }    
                    }
                }
            }
        }
        echo Zend_Json::encode(Core_Server::setOutputData(true, 'Pemission denied!', array()));     
        exit;
    }        

    public function getFileChildAction()    
    {        
        $fileId = $this->_getParam('fileId', 0);        
        $arrChild = array();
        $arrLogin = Admin::getInstance()->getLogin();
        if(!isset($arrLogin['accountID']) || empty($arrLogin['accountID'])){
            echo Zend_Json::encode(Core_Server::setOutputData(true, 'You must login!', array()));
            exit;
        }
        $fileInfo = File::getInstance()->selectOne($fileId);
        if($fileInfo){
            File::getInstance()->getAllChild($fileId, $fileInfo['group_id'], $arrChild);
            if(sizeof($arrChild)>0){
                echo Zend_Json::encode(Core_Server::setOutputData(false, 'OK', array("data"=>Zend_Json::encode($arrChild))));
                exit;    
            }
            else{
                echo Zend_Json::encode(Core_Server::setOutputData(false, 'have no child', array("data"=>"{}")));
                exit;   
            }
        }    
        else{
            echo Zend_Json::encode(Core_Server::setOutputData(true, 'File does not existed', array()));
            exit;
        }
        echo Zend_Json::encode(Core_Server::setOutputData(true, 'Permission denied!', array()));      
        exit;
    }

    public function removeShareDocsAction()    
    {        
        $shareId = $this->_getParam('shareId', 0);        
        $deleted = false;
        $arrLogin = Admin::getInstance()->getLogin();
        if(!isset($arrLogin['accountID']) || empty($arrLogin['accountID'])){
            echo Zend_Json::encode(Core_Server::setOutputData(true, 'You must login!', array()));
            exit;
        }
        $deleted = ShareDocs::getInstance()->delete($shareId, $arrLogin['accountID']);
        if($deleted["ok"] == 1 && $deleted["n"] == 1){
            echo Zend_Json::encode(Core_Server::setOutputData(false, 'remove share OK', array()));
            exit;
        }
        echo Zend_Json::encode(Core_Server::setOutputData(true, 'Permission denied!', array()));      
        exit;
    }

    public function getShareDocsAction()    
    {
        $fileId = $this->_getParam('fileId', 0);
        $iOffset = $this->_getParam('iOffset', 0);
        $iLimit = $this->_getParam('iLimit', 10);

        $arrLogin = Admin::getInstance()->getLogin();
        if(!isset($arrLogin['accountID']) || empty($arrLogin['accountID'])){
            echo Zend_Json::encode(Core_Server::setOutputData(true, 'You must login!', array()));
            exit;
        }
        $arrResult = ShareDocs::getInstance()->select($fileId, $iOffset, $iLimit);
        if(sizeof($arrResult)>0){
            echo Zend_Json::encode(Core_Server::setOutputData(false, 'OK', array('data' => Zend_Json::encode($arrResult))));
            exit;
        }
        echo Zend_Json::encode(Core_Server::setOutputData(true, 'Permission denied!', array()));      
        exit;
    }    

    public function getShareDocsByAccountAction()    
    {        
        $iOffset = $this->_getParam('iOffset', 0);
        $iLimit = $this->_getParam('iLimit', 10);

        $arrLogin = Admin::getInstance()->getLogin();
        if(!isset($arrLogin['accountID']) || empty($arrLogin['accountID'])){
            echo Zend_Json::encode(Core_Server::setOutputData(true, 'You must login!', array()));
            exit;
        }        
        $arrResult = ShareDocs::getInstance()->selectMyShare($arrLogin['accountID'], $iOffset, $iLimit);
        if(sizeof($arrResult)>0){
            echo Zend_Json::encode(Core_Server::setOutputData(false, 'OK', array('data' => Zend_Json::encode($arrResult))));
            exit;
        }
        echo Zend_Json::encode(Core_Server::setOutputData(false, 'You have no shared documents', array()));      
        exit;
    }

    public function updateSharePermissionAction()
    {
        $arrLogin = Admin::getInstance()->getLogin();
        if(!isset($arrLogin['accountID']) || empty($arrLogin['accountID'])){
            echo Zend_Json::encode(Core_Server::setOutputData(true, 'You must login!', array()));
            exit;
        }
        $shareId = $this->_getParam('shareId', 0);
        $sPermission = $this->_getParam('permission', "view");

        $updated = ShareDocs::getInstance()->updatePermission($shareId, $arrLogin['accountID'], $sPermission);
        if($updated){
            echo Zend_Json::encode(Core_Server::setOutputData(false, 'OK', array()));
            exit;
        }
        echo Zend_Json::encode(Core_Server::setOutputData(true, 'Permission denied!', array()));      
        exit;
    }

    public function updatePublicDocsAction()
    {
        $arrLogin = Admin::getInstance()->getLogin();
        if(!isset($arrLogin['accountID']) || empty($arrLogin['accountID'])){
            echo Zend_Json::encode(Core_Server::setOutputData(true, 'You must login!', array()));
            exit;
        }
        $fileId = $this->_getParam('fileId', 0);
        $iPublic = $this->_getParam('is_public', 0);

        $updated = File::getInstance()->updatePublicFileStatus($fileId, $arrLogin['accountID'], $iPublic);
        if($updated){
            echo Zend_Json::encode(Core_Server::setOutputData(false, 'OK', array()));
            exit;
        }
        echo Zend_Json::encode(Core_Server::setOutputData(true, 'Permission denied!', array()));      
        exit;
    }

    public function getStorageAction()
    {   
        $arrLogin = Admin::getInstance()->getLogin();
        if(!isset($arrLogin['accountID']) || empty($arrLogin['accountID'])){
            echo Zend_Json::encode(Core_Server::setOutputData(true, 'You must login!', array()));
            exit;
        }     
        $groupId = (int)$this->_getParam('groupId', -1);
                                    
        $curStorage = $this -> getStorageGroup($arrLogin['accountID'], $groupId, $limitStorage, $percentStorage);
        
        echo Zend_Json::encode(Core_Server::setOutputData(false, 'OK', array('curStorage'=>$curStorage, 'limitStorage'=>$limitStorage, 'percentStorage' => $percentStorage)));      
        exit;
    }

    public function deleteDocsAction()
    {

        $arrLogin = Admin::getInstance()->getLogin();
        if(!isset($arrLogin['accountID']) || empty($arrLogin['accountID'])){
            echo Zend_Json::encode(Core_Server::setOutputData(true, 'You must login!', array()));
            exit;
        }
        global $globalConfig;
        global $actionDocs;
        $fileId = $this->_getParam('fileId', 0);
        if($fileId > 0)
        {
            $fileInfo = File::getInstance()->selectOne($fileId);            
            if($fileInfo){
                if($fileInfo['group_id'] > 0){
                    $groupInfo = Group::getInstance()->getGroupByID($fileInfo['group_id']);
                    $groupName = $groupInfo['group_name'];
                    $level = GroupMember::getInstance()->getMemberGroupLevelById($fileInfo['group_id'], $arrLogin['accountID']);
                }                
                else{
                    $level = 0;
                    $groupName = "";    
                } 
                if($fileInfo['owner'] == $arrLogin['accountID'] || in_array((int)$level, $globalConfig['ManagerDocsLevel'])){
                    $backupFolder = DOC_BACKUP_PATH.DIRECTORY_SEPARATOR.date('Ymd');
                    if (!file_exists($backupFolder)) {
                        mkdir($backupFolder, 0777, true);
                    }
                    $fullPath = $this->getDocsPath($fileId);
                    $sPathDetail = $this->getDocsPath($fileId,DETAIL);
                    $sPathThumbnail = $this->getDocsPath($fileId,THUMBNAIL);
                    $parentName = "";
                    $groupName = "";
                    if($fileInfo['parent'] > 0){
                            $parentInfo = File::getInstance()->selectOne($fileInfo['parent']);
                            $parentName = $parentInfo['name'];
                    }                    
                    // tao folder backup
                    mkdir($backupFolder.DIRECTORY_SEPARATOR.$fileId);
    //error_log('fullPath='.$fullPath);                
                    if($fileInfo['type'] == 0){//delete file
                        //xoa docs

                        $deleted = File::getInstance()->delete($fileId);
                        if($deleted){                        
                            //move file to backup folder                        
                            if(file_exists($fullPath)) {
                                rename($fullPath, $backupFolder.DIRECTORY_SEPARATOR.$fileId.DIRECTORY_SEPARATOR.$fileInfo['name']);
                                rename($sPathDetail, $backupFolder.DIRECTORY_SEPARATOR.$fileId.DIRECTORY_SEPARATOR.$fileInfo['name']);
                                rename($sPathThumbnail, $backupFolder.DIRECTORY_SEPARATOR.$fileId.DIRECTORY_SEPARATOR.$fileInfo['name']);
                                //unlink($fullPath);
                                // $backupFolder = DOC_BACKUP_PATH.time
                                // HistoryDocs::getInstance()->insertHistoryDelete($fileId, $fileInfo['name'], $backupFolder.DIRECTORY_SEPARATOR.$fileId, $arrLogin['accountID'], $arrLogin['nickName'], $arrLogin['avatar']);
error_log('source='.$fullPath. '&target=' . $backupFolder.DIRECTORY_SEPARATOR.$fileId);                                
                                HistoryDeleteDocs::getInstance()->insert($fileId, $fileInfo['name'], $fileInfo['path'], $backupFolder.DIRECTORY_SEPARATOR.$fileId, $fileInfo['type'], $fileInfo['group_id'], $fileInfo['parent'], $arrLogin['accountID'], $arrLogin['nickName'], $arrLogin['avatar']);
                                //send notification
                                // ShareDocs::getInstance()->sendNotification($iAction="deletefile", $fileId, $fileInfo['path'], $fileInfo['name'], $fileInfo['group_id'], $arrLogin['accountID'], $arrLogin['nickName']);
                            }
                            //send notification
                            ShareDocs::getInstance()->sendNotification($iAction="deletefile", $fileId, $fileInfo['path'], $fileInfo['name'], $fileInfo['group_id'], $arrLogin['accountID'], $arrLogin['nickName'], $arrLogin['avatar'], $actionDocs, $shareType="", $share_account_id = "", $share_account_name="");    
                            //xoa tat ca share tren docs nay
                            ShareDocs::getInstance()->deleteAll($fileId);
                            //xoa favorite
                            FavoriteDocs::getInstance()->deleteByFileId($fileId);                                                    
                            //ghi log action docs
                            ApiDocs::addActionlog("deletefile", $iType = 0, $iAccountID = $arrLogin['accountID'], $sAccountName = $arrLogin['nickName'], $sAccountAvatar = $arrLogin['avatar'], $groupId = $fileInfo['group_id'], $groupName, $fileInfo['parent'], $parentName, $fileId, $fileName = $fileInfo['name'], $fileInfo['type'], $filePath=$fileInfo['path'], $fileUrl="", $detail = array(), $sNote = 'delete file');
                            //xoa history
                            //can nhac xem co nen xoa hay ko?!
                            //HistoryDocs::getInstance()->deleteByFileId($fileId);

                        }

                        echo Zend_Json::encode(Core_Server::setOutputData(false, 'Remove docs OK', array()));
                        exit;
                    }
                    else{//delete folder
                        //move file to backup folder                        
                        if(file_exists($fullPath)) {

                            Core_Helper::copyDir($fullPath, $backupFolder.DIRECTORY_SEPARATOR.$fileId.DIRECTORY_SEPARATOR.$fileInfo['name']);
                            Core_Helper::removeDir($fullPath);
                            // have bug with rename() funtion in PHP 5.4.45 when copy between 2 driver
                            //rename($fullPath, $backupFolder.DIRECTORY_SEPARATOR.$fileId.DIRECTORY_SEPARATOR.$fileInfo['name']);
                            //ghi log xoa file
                            // HistoryDocs::getInstance()->insertHistoryDelete($fileId, $fileInfo['name'], $backupFolder.DIRECTORY_SEPARATOR.$fileId, $arrLogin['accountID'], $arrLogin['nickName'], $arrLogin['avatar']);
error_log('source='.$fullPath. '&target=' . $backupFolder.DIRECTORY_SEPARATOR.$fileId);                            
                            HistoryDeleteDocs::getInstance()->insert($fileId, $fileInfo['name'], $fileInfo['path'], $backupFolder.DIRECTORY_SEPARATOR.$fileId, $fileInfo['type'], $fileInfo['group_id'], $fileInfo['parent'], $arrLogin['accountID'], $arrLogin['nickName'], $arrLogin['avatar']);
                        }
                        //send notification
                        ShareDocs::getInstance()->sendNotification($iAction="deletefolder", $fileId, $fileInfo['path'], $fileInfo['name'], $fileInfo['group_id'], $arrLogin['accountID'], $arrLogin['nickName'], $arrLogin['avatar'], $actionDocs, $shareType="", $share_account_id = "", $share_account_name="");                    
                        //xoa tat ca cac file con cua folder
                        $arrChild = array();
                        File::getInstance()->getAllChild($fileId, $fileInfo['group_id'], $arrChild);
                        if(sizeof($arrChild) > 0){
                            foreach ($arrChild as $File) {                                                        
                                $deleted = File::getInstance()->delete($File['_id']);
                                if($deleted){                                                                
                                    //xoa tat ca share tren docs nay
                                    ShareDocs::getInstance()->deleteAll($File['_id']);
                                    //xoa favorite
                                    FavoriteDocs::getInstance()->deleteByFileId($File['_id']);
                                    //xoa history
                                    //can nhac xem co nen xoa hay ko?!
                                    //HistoryDocs::getInstance()->deleteByFileId($fileId);
                                }                                                                                   
                            }
                        }                        
                        //xoa folder 
                        $deleted = File::getInstance()->delete($fileId);
                        if($deleted){
                            //xoa tat ca share tren docs nay
                            ShareDocs::getInstance()->deleteAll($fileId);
                            FavoriteDocs::getInstance()->deleteByFileId($fileId);
                            //ghi log action docs
                            ApiDocs::addActionlog("deletefolder", $iType = 0, $iAccountID = $arrLogin['accountID'], $sAccountName = $arrLogin['nickName'], $sAccountAvatar = $arrLogin['avatar'], $groupId = $fileInfo['group_id'], $groupName, $fileInfo['parent'], $parentName, $fileId, $fileName = $fileInfo['name'], $fileInfo['type'], $filePath=$fileInfo['path'], $fileUrl="", $detail = array(), $sNote = 'delete folder');                                    
                        }
                        echo Zend_Json::encode(Core_Server::setOutputData(false, 'Remove folder OK', array()));      
                        exit;
                    }
                }                                
            }                            
        }            
        echo Zend_Json::encode(Core_Server::setOutputData(true, 'Permission denied!', array()));      
        exit;
    }    

    public function renameDocsAction()
    {
        $arrLogin = Admin::getInstance()->getLogin();
        if(!isset($arrLogin['accountID']) || empty($arrLogin['accountID'])){
            echo Zend_Json::encode(Core_Server::setOutputData(true, 'You must login!', array()));
            exit;
        }
        $fileId = $this->_getParam('fileId', 0);
        $snewName = $this->_getParam('name', "");
        $parentName = "";
        $groupName = "";
        global $globalConfig;
        global $actionDocs;
        if($fileId > 0 && !empty($snewName))
        {
            $fileInfo = File::getInstance()->selectOne($fileId);
            if($fileInfo){
                if($fileInfo['group_id'] > 0){
                    $groupInfo = Group::getInstance()->getGroupByID($fileInfo['group_id']);
                    $groupName = $groupInfo['group_name'];
                    $level = GroupMember::getInstance()->getMemberGroupLevelById($fileInfo['group_id'], $arrLogin['accountID']);
                }                
                else{
                    $level = 0;
                    $groupName = "";    
                }
                if( $fileInfo['owner'] == $arrLogin['accountID'] || in_array((int)$level, $globalConfig['ManagerDocsLevel'])){
                    if($fileInfo['parent'] > 0){
                            $parentInfo = File::getInstance()->selectOne($fileInfo['parent']);
                            $parentName = $parentInfo['name'];
                    }                    
                    $ext = strtolower(pathinfo($fileInfo['name'], PATHINFO_EXTENSION));
                    if($fileInfo["type"] == 0){// rename file
                        $snewName .= (!empty($ext)? ".".$ext:"");
                        $updated = File::getInstance()->renameFile($fileId, $fileInfo['owner'], $snewName);
                        if($updated){
                            if($fileInfo['group_id'] == -1)//My document onlyoffice
                            {                            
                                $path = DOC_ROOT_PATH . DIRECTORY_SEPARATOR . "users". DIRECTORY_SEPARATOR . $fileInfo['owner'] . (!empty($fileInfo['path'])?$fileInfo['path']:"");                            
                            }
                            else{//document tạo trong 1 group                            
                                $path = PATH_FILES_UPLOAD_DIR.$fileInfo['path'];                            
                            }                        
                            if(file_exists($path.DIRECTORY_SEPARATOR.$fileInfo['name']))
                                rename($path.DIRECTORY_SEPARATOR.$fileInfo['name'], $path.DIRECTORY_SEPARATOR.$snewName);
                            //send notification
                            ShareDocs::getInstance()->sendNotification($iAction="renamefile", $fileId, $fileInfo['path'], $snewName, $fileInfo['group_id'], $arrLogin['accountID'], $arrLogin['nickName'], $arrLogin['avatar'], $actionDocs, $shareType="", $share_account_id = "", $share_account_name="");
                            //ghi log action docs
                            ApiDocs::addActionlog("renamefile", $iType = 0, $iAccountID = $arrLogin['accountID'], $sAccountName = $arrLogin['nickName'], $sAccountAvatar = $arrLogin['avatar'], $groupId = $fileInfo['group_id'], $groupName, $fileInfo['parent'], $parentName, $fileId, $fileName = $snewName, $fileInfo['type'], $filePath=$fileInfo['path'], $fileUrl="", $detail = array("oldName"=>$fileInfo['name'], "newName" => $snewName), $sNote = '');
                            //send notification
                            /*ShareDocs::getInstance()->sendNotification($iAction="renamefile", $fileId, $fileInfo['path'], $fileInfo['name'], $fileInfo['group_id'], $arrLogin['accountID'], $arrLogin['nickName'], $actionDocs);*/
                            //update name ins sharedocs
                            ShareDocs::getInstance()->renameFile($fileId, $snewName);
                            echo Zend_Json::encode(Core_Server::setOutputData(false, 'OK', array()));
                            exit;
                        }
                    }
                    else{//rename folder
                        if($fileInfo['group_id'] == -1)//My document onlyoffice
                        {                            
                            $path = (!empty($fileInfo['path'])?$fileInfo['path']:"");                            
                        }
                        else{//document tạo trong 1 group                            
                            $path = $fileInfo['path'];                            
                        }

                        $updated = File::getInstance()->renameFolderDocs($fileId, $fileInfo['group_id'], $snewName, $path.DIRECTORY_SEPARATOR.$snewName);
                        if($updated){
                            //send notification
                            ShareDocs::getInstance()->sendNotification($iAction="renamefolder", $fileId, $fileInfo['path'], $snewName, $fileInfo['group_id'], $arrLogin['accountID'], $arrLogin['nickName'], $arrLogin['avatar'], $actionDocs, $shareType="", $share_account_id = "", $share_account_name="");
                            //add action log
                            ApiDocs::addActionlog("renamefolder", $iType = 0, $iAccountID = $arrLogin['accountID'], $sAccountName = $arrLogin['nickName'], $sAccountAvatar = $arrLogin['avatar'], $groupId = $fileInfo['group_id'], $groupName, $fileInfo['parent'], $parentName, $fileId, $fileName = $snewName, $fileInfo['type'], $filePath=$fileInfo['path'], $fileUrl="", $detail = array("oldName"=>$fileInfo['name'], "newName" => $snewName), $sNote = '');
                            echo Zend_Json::encode(Core_Server::setOutputData(false, 'OK', array()));      
                            exit;
                        }                        
                    }
                }
            }                            
        }            
        echo Zend_Json::encode(Core_Server::setOutputData(true, 'Permission denied!', array()));      
        exit;
    }

    public function getActionDocsLogAction()
    {
        global $actionDocs;
        $iOffset = $this->_getParam('iOffset', 0);
        $iLimit = $this->_getParam('iLimit', 10);
        $groupId = $this->_getParam('groupId', 0);
        $arrLogin = Admin::getInstance()->getLogin();
        if(!isset($arrLogin['accountID']) || empty($arrLogin['accountID']))
        {    
            echo Zend_Json::encode(Core_Server::setOutputData(true, 'You must login!', array()));
            exit;
        }
        $total = 0;    
        $arrResult = ActionDocsLog::getInstance()->select($arrLogin['accountID'], $iType=-1, $sStartDate = "", $sEndDate = "", $iAction = "", $groupId, $parentId = 0, $fileId = 0, $iOffset, $iLimit);
        if(sizeof($arrResult))
        {
            $arrAccDocs = array();
            $arrAccDocsTmp = array();
            foreach ($arrResult['data'] as $accLog) {
                $action = $accLog["action"];
                if($accLog["type"] == 1)//dc share
                {                                                   
                    $arrAccDocsTmp["account_id"] = $accLog['detail']["account_id"];                    
                    $arrAccDocsTmp["account_name"] = $accLog['detail']["account_name"];                                        
                    $arrAccDocsTmp["account_avatar"] = isset($accLog['detail']["account_avatar"])?PATH_AVATAR_URL . DIRECTORY_SEPARATOR . $accLog['detail']["account_avatar"]:"";  
                }
                else{                    
                    $arrAccDocsTmp["account_id"] = $accLog["account_id"];                    
                    $arrAccDocsTmp["account_name"] = $accLog["account_name"];
                    $arrAccDocsTmp["account_avatar"] = PATH_AVATAR_URL . DIRECTORY_SEPARATOR . $accLog["account_avatar"];
                }
                $arrAccDocsTmp["action_name"] = $actionDocs[$action];
                $arrAccDocsTmp["group_id"] = $accLog["group_id"];
                $arrAccDocsTmp["group_name"] = $accLog["group_name"];
                $arrAccDocsTmp["parent_id"] = $accLog["parent_id"];
                $arrAccDocsTmp["parent_name"] = $accLog["parent_name"];
                $arrAccDocsTmp["file_id"] = $accLog["file_id"];
                $arrAccDocsTmp["file_name"] = $accLog["file_name"];
                $arrAccDocsTmp["file_path"] = $accLog["file_path"];
                $arrAccDocsTmp["file_url"] = $accLog["file_url"];                    
                $arrAccDocsTmp["note"] = $accLog["note"];
                $arrAccDocsTmp["created"] = $accLog["created"]->sec;
                $arrAccDocsTmp["type_action"] = $accLog["type"];
                $arrAccDocsTmp["action"] = $accLog["action"];
                if(isset($accLog["file_type"])){
                    if($accLog["file_type"] == 0){
                        $arrAccDocsTmp["docs_type"] = Core_Helper::getDocsExtension($accLog["file_name"]);    
                    }
                    else $arrAccDocsTmp["docs_type"] = "";
                    $arrAccDocsTmp["file_type"] = $accLog["file_type"];
                }
                else $arrAccDocsTmp["file_type"] = "";
                $arrAccDocs[] = $arrAccDocsTmp;
            }
            $total = $arrResult['total'];
        }
        echo Zend_Json::encode(Core_Server::setOutputData(false, 'OK', array('total'=>$total, 'data'=>Zend_Json::encode($arrAccDocs))));
        exit;    
    }

    public function getHistoryDocsAction()
    {
        $iOffset = $this->_getParam('iOffset', 0);
        $iLimit = $this->_getParam('iLimit', 10);
        $fileId = $this->_getParam('fileId', 0);
        $arrLogin = Admin::getInstance()->getLogin();
        if(!isset($arrLogin['accountID']) || empty($arrLogin['accountID']))
        {    
            echo Zend_Json::encode(Core_Server::setOutputData(true, 'You must login!', array()));
            exit;
        }
        $arrResult = array();
        $fileInfo = File::getInstance()->selectOne($fileId);
        $filename = "";
        $owner_id = "";
        $owner_name="";
        $owner_avatar="";
        if($fileInfo){
            $filename = $fileInfo['name'];
            $accInfo = AccountInfo::getInstance()->getAccountInfoByAccountID($fileInfo['owner']);
            $owner_id = $fileInfo['owner'];
            $owner_avatar = isset($accInfo['avatar'])?$accInfo['avatar']:"";
            $owner_name = isset($accInfo['name'])?$accInfo['name']:"";
            $arrResult = HistoryDocs::getInstance()->select($fileId, $iOffset, $iLimit);
            /*echo Zend_Json::encode(Core_Server::setOutputData(false, 'OK', array('data'=>Zend_Json::encode($arrResult))));
            exit;*/
        }
        
        echo Zend_Json::encode(Core_Server::setOutputData(false, 'OK', array(
                'data'=>Zend_Json::encode($arrResult),
                'file_id'=>$fileId,
                'file_name'=>$filename,
                'owner_id'=>$owner_id,
                'owner_name'=>$owner_name,
                'owner_avatar'=>$owner_avatar
                )));
        exit;    
    }

    public function getFavoriteDocsAction()
    {
        $iOffset = $this->_getParam('iOffset', 0);
        $iLimit = $this->_getParam('iLimit', 10);        
        $arrLogin = Admin::getInstance()->getLogin();
        if(!isset($arrLogin['accountID']) || empty($arrLogin['accountID']))
        {    
            echo Zend_Json::encode(Core_Server::setOutputData(true, 'You must login!', array()));
            exit;
        }
        $arrResult = array();        
        $arrResult = FavoriteDocs::getInstance()->select($arrLogin['accountID'], $iOffset, $iLimit);
        
        echo Zend_Json::encode(Core_Server::setOutputData(false, 'OK', array('data'=>Zend_Json::encode($arrResult))));
        exit;    
    }

    public function getTrashedAction()
    {
        $iOffset = $this->_getParam('iOffset', 0);
        $iLimit = $this->_getParam('iLimit', 10);        
        $arrLogin = Admin::getInstance()->getLogin();
        if(!isset($arrLogin['accountID']) || empty($arrLogin['accountID']))
        {    
            echo Zend_Json::encode(Core_Server::setOutputData(true, 'You must login!', array()));
            exit;
        }
        $arrResult = array();        
        $arrResult = HistoryDeleteDocs::getInstance()->select($arrLogin['accountID'], $iOffset, $iLimit);
        
        echo Zend_Json::encode(Core_Server::setOutputData(false, 'OK', array('data'=>Zend_Json::encode($arrResult))));
        exit;    
    }

    public function removeTrashedAction()
    {
        $tid = $this->_getParam('id', '');
        $arrLogin = Admin::getInstance()->getLogin();
        if(!isset($arrLogin['accountID']) || empty($arrLogin['accountID']))
        {    
            echo Zend_Json::encode(Core_Server::setOutputData(true, 'You must login!', array()));
            exit;
        }
        if(!empty($tid)){
            $deleted = false;
            if($tid == 'all')
                $deleted = HistoryDeleteDocs::getInstance()->deleteByAccountId($arrLogin['accountID']);
            else{
                $arrResult = HistoryDeleteDocs::getInstance()->selectOne($tid);
                if($arrResult){
                    if($arrResult['account_id'] == $arrLogin['accountID'])
                    {
                        $deleted = HistoryDeleteDocs::getInstance()->delete($tid);        
                    }else{
                        echo Zend_Json::encode(Core_Server::setOutputData(true, 'Permision denied!', array()));
                        exit;        
                    }
                }
                else{
                    echo Zend_Json::encode(Core_Server::setOutputData(true, 'id does not existed', array()));
                    exit;
                }
            }
            if($deleted){
                echo Zend_Json::encode(Core_Server::setOutputData(false, 'OK', array()));
                exit;
            }
            else{
                echo Zend_Json::encode(Core_Server::setOutputData(true, 'something wrong was happened', array()));
                exit;
            }
        }    
        
        echo Zend_Json::encode(Core_Server::setOutputData(true, 'id param must be not empty', array()));
        exit;    
    }

    public function restoreTrashedAction()
    {
        $tid = $this->_getParam('id', "");
        $arrLogin = Admin::getInstance()->getLogin();
        if(!isset($arrLogin['accountID']) || empty($arrLogin['accountID']))
        {    
            echo Zend_Json::encode(Core_Server::setOutputData(true, 'You must login!', array()));
            exit;
        }
        if(empty($tid)){
            echo Zend_Json::encode(Core_Server::setOutputData(true, 'the id must be not empty!', array()));
            exit;   
        }
        $arrResult = array();        
        $arrResult = HistoryDeleteDocs::getInstance()->selectOne($tid);
        if($arrResult){
            if($arrResult["account_id"] == $arrLogin['accountID']){
                if($arrResult['group_id'] > 0){
                    $filePath = PATH_FILES_UPLOAD_DIR . (!empty($arrResult['file_path'])? $arrResult['file_path']:"");
                }
                else{
                    $filePath = DOC_ROOT_PATH . DIRECTORY_SEPARATOR . "users". DIRECTORY_SEPARATOR . $arrResult['account_id'] . (!empty($arrResult['file_path'])? $arrResult['file_path']:"");
                }
                if($arrResult["type"] === 1){ //restore delete folder
                    $folderRestorePath = $filePath . DIRECTORY_SEPARATOR . $arrResult['file_name'];
                    $fullBkPath = $arrResult["backup_path"] . DIRECTORY_SEPARATOR . $arrResult["file_name"];
                    if(file_exists($fullBkPath)){                        
                        $arrRestore = self::restoreFolder($arrResult['file_name'], $filePath, $arrResult["file_path"], $arrResult["group_id"], $arrResult["parent"], $arrResult["account_id"], $fullBkPath );
                        if(is_array($arrRestore) && sizeof($arrRestore) > 0){
                            Core_Helper::copyDir($fullBkPath, $filePath.DIRECTORY_SEPARATOR.$arrRestore['name']);
                            // cân nhắc việc xóa file vat ly
                            //Core_Helper::removeDir($fullBkPath);

                            // have bug with rename() funtion in PHP 5.4.45 when copy between 2 driver
                            // rename($fullBkPath, $filePath.DIRECTORY_SEPARATOR.$arrRestore['name']);                            
error_log("fileId:".$arrRestore['fileId']."+"."&bkpath:".$fullBkPath."&restorePath:".$filePath.DIRECTORY_SEPARATOR.$arrRestore['name']);
                            //xóa record delete docs
                            HistoryDeleteDocs::getInstance()->delete($tid);
                            echo Zend_Json::encode(Core_Server::setOutputData(false, 'OK', array("fileId"=>$arrRestore['fileId'], "file_name"=>$arrRestore['name'])));
                            exit;
                        }
                    }
                    else{
                        echo Zend_Json::encode(Core_Server::setOutputData(true, 'Backup folder has gone away!', array()));
                        exit;        
                    }
                        
                }
                else{//restore delete file
                    $fullBkPath = $arrResult["backup_path"].DIRECTORY_SEPARATOR.$arrResult["file_name"];
                    if(file_exists($fullBkPath)){
                        //create file path if not existed
                        Core_Helper::createNewFolder($filePath);
                        $newname = Core_Helper::GetCorrectName($filePath . DIRECTORY_SEPARATOR . $arrResult["file_name"]);
                        $fileId = File::getInstance()->insert($newname, $arrResult["file_path"], FILE, $arrResult["parent"], $arrResult['account_id'], $arrResult["group_id"], 0, $newname, 1);
                        if($fileId > 0){
                            rename($fullBkPath, $filePath.DIRECTORY_SEPARATOR.$arrResult['file_name']);
error_log("fileId:".$fileId."+"."&bkpath:".$fullBkPath."&restorePath:".$filePath.DIRECTORY_SEPARATOR.$arrResult['file_name']);
                            //xóa record delete docs
                            HistoryDeleteDocs::getInstance()->delete($tid);
                            echo Zend_Json::encode(Core_Server::setOutputData(false, 'OK', array("fileId"=>$fileId, "file_name"=>$newname)));
                            exit;    
                        }else{
                            echo Zend_Json::encode(Core_Server::setOutputData(true, 'Restore file error!', array()));
                            exit;            
                        }
                    }else{
                        echo Zend_Json::encode(Core_Server::setOutputData(true, 'Backup file has gone away!', array()));
                        exit;        
                    }    
                }
                echo Zend_Json::encode(Core_Server::setOutputData(false, 'OK', array('data'=>Zend_Json::encode($arrResult))));
                exit;
            }else{
                echo Zend_Json::encode(Core_Server::setOutputData(true, 'The id not existed!', array()));
                exit;
            }
        }else{
            echo Zend_Json::encode(Core_Server::setOutputData(true, 'The id not existed!', array()));
            exit;
        }                    
    }

    public function addFavoriteDocsAction()    
    {
        $fileId = $this->_getParam('fileId', 0);        
        $fileInfo = array();
        $accInfo = array();
        $groupInfo = array();
        $parentInfo = array();
        $groupName = "";
        $parentName = "";
        $arrLogin = Admin::getInstance()->getLogin();
        if(!isset($arrLogin['accountID']) || empty($arrLogin['accountID'])){
            echo Zend_Json::encode(Core_Server::setOutputData(true, 'You must login!', array()));
            exit;
        }           

        if($fileId > 0){            
            $fileInfo = File::getInstance()->selectOne($fileId);
            if($fileInfo){
                if($fileInfo['parent'] > 0){
                    $parentInfo = File::getInstance()->selectOne($fileInfo['parent']);
                    $parentName = $parentInfo['name'];
                }
                if($fileInfo['group_id'] > 0){
                    $groupInfo = Group::getInstance()->getGroupByID($fileInfo['group_id']);
                    $groupName = $groupInfo['group_name'];
                }
                if($fileInfo['type'] == 0){
                    // $downloadURL = $this->getDownloadDocsPath($fileId);
                    $downloadURL = BASE_URL. DIRECTORY_SEPARATOR . "api" . DIRECTORY_SEPARATOR . "docs" . DIRECTORY_SEPARATOR . "download-docs?fileId=".$fileId;
                    if(Core_Helper::checkIsDocs($fileInfo['name'])){                            
                        $fileUrl = "/docs/editor/id/".$fileId;
                    }    
                    else{
                        if(Core_Helper::checkIsImage($fileInfo['name'])){
                            $fileURL= $fileInfo['path'].DIRECTORY_SEPARATOR.$fileInfo['name'];
                        }
                        else
                            $fileUrl = "";    
                    }    
                }else{
                    $fileUrl = "";
                    $downloadURL = "";
                }
                $isfavorited = FavoriteDocs::getInstance()->selectByFileId($fileId);
                if($isfavorited){
                    echo  Zend_Json::encode(Core_Server::setOutputData(true, 'File favorite!', array()));
                    exit;    
                }else{
                    $fid = FavoriteDocs::getInstance()->insert($fileId, $fileInfo['name'], $fileInfo['type'], $fileInfo['group_id'], $groupName, $fileInfo['parent'], $parentName, $fileUrl, $downloadURL, $arrLogin['accountID'], $arrLogin['nickName'], $arrLogin['avatar'], $is_docs = 1);
                    if($fid){
                        echo  Zend_Json::encode(Core_Server::setOutputData(false, 'OK', array('fid'=>$fid)));
                        exit;
                    }
                }    
            }
        }
        echo Zend_Json::encode(Core_Server::setOutputData(true, 'Pemission denied!', array()));     
        exit;
    }

    public function removeFavoriteDocsAction()    
    {        
        $favoriteId = $this->_getParam('fId', 0);        
        $deleted = false;
        $arrLogin = Admin::getInstance()->getLogin();
        if(!isset($arrLogin['accountID']) || empty($arrLogin['accountID'])){
            echo Zend_Json::encode(Core_Server::setOutputData(true, 'You must login!', array()));
            exit;
        }
        $deleted = FavoriteDocs::getInstance()->delete($favoriteId, $arrLogin['accountID']);
        if($deleted){
            echo Zend_Json::encode(Core_Server::setOutputData(false, 'remove favorite OK', array()));
            exit;
        }
        echo Zend_Json::encode(Core_Server::setOutputData(true, 'Permission denied!', array()));      
        exit;
    }

    public function getAllParentDocsAction()    
    {        
        $fileId = $this->_getParam('fileId', 0);                
        $arrLogin = Admin::getInstance()->getLogin();
        if(!isset($arrLogin['accountID']) || empty($arrLogin['accountID'])){
            echo Zend_Json::encode(Core_Server::setOutputData(true, 'You must login!', array()));
            exit;
        }
        $arrParent = array();
        File::getInstance()->getAllParent($fileId, $arrParent);
        $level = GroupMember::getInstance()->getMemberGroupLevelById(46, $arrLogin['accountID']);
error_log("level:".$level);        
        if(sizeof($arrParent) > 0){
            echo Zend_Json::encode(Core_Server::setOutputData(false, 'OK', array('data'=>Zend_Json::encode($arrParent))));
            exit;
        }
        echo Zend_Json::encode(Core_Server::setOutputData(false, 'OK', array()));      
        exit;
    }

    public function editorCacheAjaxAction()
    {
        $response_array['status'] = 'OK';
        $response_array['error'] = 0;
        echo Zend_Json::encode($response_array);
        exit;
    }

    public function editorAjaxAction()
    {
                
        $fileId = $this->_getParam('id');        
        $File = File::getInstance()->selectOne($fileId);

        global $globalConfig;
        global $actionDocs;
        if(empty($File))
        {    
            $response_array['status'] = 'error';
            $response_array['error'] = '404 File not found';
            echo Zend_Json::encode($response_array);
        }    
        else
        {
            $pass = false;
            $token = $this->_getParam('token', '');
            $type = $this->_getParam('type', '');
            if(!empty($token))
            {    
                $arrRes = ApiToken::checkDocsToken($token);
                if(sizeof($arrRes['body']) > 0)
                {
                    $accountID = $arrRes["body"]["account_id"];
                    $username = $arrRes["body"]["username"];
                    $avatar = $arrRes["body"]["avatar"];
                    $ps = base64_decode($arrRes["body"]["ps"]);
                    $pass = true;                    
                }
            }
            else
            {
                $arrLogin = Admin::getInstance()->getLogin();
                if(!isset($arrLogin['accountID']) || empty($arrLogin['accountID']))
                    $pass = false;
                else{
                    $accountID = $arrLogin["accountID"]; 
                    $username = $arrLogin["username"];
                    $avatar = $arrLogin['avatar'];
                    $ps = base64_decode($arrLogin["ps"]);
                    $pass = true;
                }
            }
            if($pass){
                $originalName = empty($File['original_name']) ? $File['name'] : $File['original_name'];
                if($File['group_id'] == -1)//My document onlyoffice
                {
                    $fullURL = ROOT_DOC_URL . DIRECTORY_SEPARATOR . "users". DIRECTORY_SEPARATOR . $File['owner'] . (!empty($File['path'])?DIRECTORY_SEPARATOR . $File['path']:"") . DIRECTORY_SEPARATOR . $File['name'];
                    $fullPath = DOC_ROOT_PATH . DIRECTORY_SEPARATOR . "users". DIRECTORY_SEPARATOR . $File['owner'] . (!empty($File['path'])?DIRECTORY_SEPARATOR . $File['path']:"") . DIRECTORY_SEPARATOR .$File['name'];
                }
                else{//document tạo trong 1 group
                    $fullURL = PATH_FILES_URL.$File['path'].DIRECTORY_SEPARATOR.$File['name'];
                    $fullPath = PATH_FILES_UPLOAD_DIR.DIRECTORY_SEPARATOR.$File['path'].DIRECTORY_SEPARATOR.$File['name'];                    
                    //check permission to save file
                    if(!isset($File['is_public']) || $File['is_public'] == 0){
                        $level = GroupMember::getInstance()->getMemberGroupLevelById($File['group_id'], $accountID);                    
                        if($File['owner'] != $accountID && !in_array($level, $globalConfig['ManagerDocsLevel'])){
                            $ApiGroup = new ApiGroup();
                            $arrayRes = $ApiGroup->getListGroupIdByMemberId($accountID);
                            $arrGroupId = array();
                            $checked = false;
                            if($arrayRes["error"] == false) $arrGroupId = $arrayRes["body"]["data"];
                            if($arrGroupId){
                                if(in_array($File['group_id'], $arrGroupId))
                                    $checked = true;
                                else{
                                    $checked = ShareDocs::getInstance()->getDocsPermissionByAccountId($fileId, $accountID, $arrGroupId);        
                                }
                            }
                            if(!$checked)
                                return Core_Server::setOutputData(true, 'Permission denied', array());
                        }
                    }
                }            

                if (!empty($type)) { //Checks if type value exists
                    $response_array;
                    @header( 'Content-Type: application/json; charset==utf-8');
                    @header( 'X-Robots-Tag: noindex' );
                    @header( 'X-Content-Type-Options: nosniff' );

                    Core_Helper::nocache_headers();

                    // sendlog(serialize($_GET),"logs/webedior-ajax.log");                    

                    switch($type) { //Switch case for value of type
                        case "upload":
                            $response_array = $this->upload($accountID);
                            $response_array['status'] = $response_array['error'] != NULL ? 'error' : 'success';
                            // die (json_encode($response_array));
                            die(Zend_Json::encode($response_array));
                        case "convert":
                            $response_array = $this->convert($fullPath, $fullURL);
                            $response_array['status'] = 'success';
                            die(Zend_Json::encode($response_array));
                        case "save":
                            $response_array = $this->save($fullPath, $fullURL);
                            $response_array['status'] = 'success';
                            die(Zend_Json::encode($response_array));
                        case "track":
                            $response_array = $this->track($File, $fullPath);
                            if(isset($response_array["c"]) && $response_array["c"] == "saved"){
                                //get acc info
                                $accInfo = AccountInfo::getInstance()->getAccountInfoByAccountID($accountID);                                
                                //insert history docs cache version
                                $fileUrl = 'docs/editor-cache/id/' . $fileId . '?skey='.$File['key'];
                                HistoryDocs::getInstance()->insert($fileId, $File['name'], $File['key'], $fileUrl, $accountID, $accInfo['name'], $accInfo['avatar']);
                                //update key isnull for file 
                                File::getInstance()->update(array('_id'=>(int)$fileId), array('key'=>'', 'updated' => new MongoDate()));
                                //send notification
                                ShareDocs::getInstance()->sendNotification($iAction="editfile", $fileId, $File['path'], $File['name'], $File['group_id'], $accountID, $username, $avatar, $actionDocs);
                            }
                            // $response_array['status'] = 'success';
                            die(Zend_Json::encode($response_array));
                        default:
                            $response_array['status'] = 'error';
                            $response_array['error'] = '404 Method not found';
                            die(Zend_Json::encode($response_array));
                    }
                }
            }  
            else{
                return Core_Server::setOutputData(true, 'Permission denied', array());
            }  
        }    
    }

    public function editorAjaxWebdavAction()
    {
                
        $fileURL = $this->_getParam('fileURL');        
        // $File = File::getInstance()->selectOne($fileId);


        if(empty($fileURL))
        {                
            $response_array['status'] = 'error';
            $response_array['error'] = '404 File not found';
            echo Zend_Json::encode($response_array);
            exit;
        }    
        else
        {
            $pass = false;
            $token = $this->_getParam('token', '');
            $type = $this->_getParam('type', '');
            if(!empty($token))
            {    
                $arrRes = ApiToken::checkToken("webdav",$token);
                if(sizeof($arrRes['body']) > 0)
                {
                    $accountID = $arrRes["body"]["account_id"];
                    $username = $arrRes["body"]["username"];
                    $ps = base64_decode($arrRes["body"]["ps"]);
                    $pass = true;                    
                }
            }
            else
            {
                $arrLogin = Admin::getInstance()->getLogin();
                if(!isset($arrLogin['accountID']) || empty($arrLogin['accountID']))
                    $pass = false;
                else{
                    $accountID = $arrLogin["accountID"]; 
                    $username = $arrLogin["username"];
                    $ps = base64_decode($arrLogin["ps"]);
                    $pass = true;
                }
            }
            if($pass){
                $response = ApiWebdav::getWebdavRequestFile($fileURL, $username, $ps);
                if(!isset($response['statusCode']) || $response['statusCode'] != 200){
                    $response_array['status'] = 'error';
                    $response_array['error'] = '404 File not found';
                    $response_array['statusCode'] = $response['statusCode'];
                    echo Zend_Json::encode($response_array);
                    exit;
                }
                /*$originalName = empty($File['original_name']) ? $File['name'] : $File['original_name'];
                if($File['group_id'] == -1)//My document onlyoffice
                {
                    $fullURL = ROOT_DOC_URL . DIRECTORY_SEPARATOR . "users". DIRECTORY_SEPARATOR . $File['owner'] . (!empty($File['path'])?DIRECTORY_SEPARATOR . $File['path']:"") . DIRECTORY_SEPARATOR . $File['name'];
                    $fullPath = DOC_ROOT_PATH . DIRECTORY_SEPARATOR . "users". DIRECTORY_SEPARATOR . $File['owner'] . (!empty($File['path'])?DIRECTORY_SEPARATOR . $File['path']:"") . DIRECTORY_SEPARATOR .$File['name'];
                }
                else{//document tạo trong 1 group
                    $fullURL = PATH_FILES_URL.$File['path'].DIRECTORY_SEPARATOR.$File['name'];
                    $fullPath = PATH_FILES_UPLOAD_DIR.DIRECTORY_SEPARATOR.$File['path'].DIRECTORY_SEPARATOR.$File['name'];
                }*/            

                if (!empty($type)) { //Checks if type value exists
                    $response_array;
                    @header( 'Content-Type: application/json; charset==utf-8');
                    @header( 'X-Robots-Tag: noindex' );
                    @header( 'X-Content-Type-Options: nosniff' );

                    Core_Helper::nocache_headers();

                    // sendlog(serialize($_GET),"logs/webedior-ajax.log");                    

                    switch($type) { //Switch case for value of type
                        case "upload":
                            // $response_array = $this->upload($accountID);
                            $response_array['status'] = $response_array['error'] != NULL ? 'error' : 'success';
                            $response_array['error'] = 0;
                            // die (json_encode($response_array));
                            die(Zend_Json::encode($response_array));
                        case "convert":
                            // $response_array = $this->convert($fullPath, $fullURL);
                            $response_array['status'] = 'success';
                            $response_array['error'] = 0;
                            die(Zend_Json::encode($response_array));
                        case "save":
                            // $response_array = $this->save($fullPath, $fullURL);
                            $response_array['status'] = 'success';
                            $response_array['error'] = 0;
                            die(Zend_Json::encode($response_array));
                        case "track":
                            // $response_array = $this->track($fullPath);
                            $response_array['status'] = 'success';
                            $response_array['error'] = 0;
                            die(Zend_Json::encode($response_array));
                        default:
                            $response_array['status'] = 'error';
                            $response_array['error'] = '404 Method not found';
                            die(Zend_Json::encode($response_array));
                    }
                }
            }  
            else{
                return Core_Server::setOutputData(true, 'Permission denied', array());
            }  
        }    
    }

    public function fixWrongPathDocsAction()
    {        
        $fileId = $this->_getParam('fileId', 0);
        $arrLogin = Admin::getInstance()->getLogin();
        if(!isset($arrLogin['accountID']) || empty($arrLogin['accountID']))
        {    
            echo Zend_Json::encode(Core_Server::setOutputData(true, 'You must login!', array()));
            exit;
        }
        $arrResult = array();
        $fileInfo = File::getInstance()->selectOne($fileId);        
        if($fileInfo){
            if($fileInfo['type'] == 1){
                if($fileInfo['group_id'] == -1)
                    $folderPath = DOC_ROOT_PATH . DIRECTORY_SEPARATOR . "users" . DIRECTORY_SEPARATOR . $arrLogin["accountID"];
                else
                    $folderPath = PATH_FILES_UPLOAD_DIR;
                File::getInstance()->updateWrongPath($folderPath, $fileId, $fileInfo['group_id'], $fileInfo['path'].DIRECTORY_SEPARATOR.$fileInfo['name']);
            }    
            else{
                echo Zend_Json::encode(Core_Server::setOutputData(true, 'this is file!', array()));
                exit;
            }     
        }
        
        echo Zend_Json::encode(Core_Server::setOutputData(false, 'Fix all sub folder path successfull', array()));
        exit;    
    }

    public function downloadDocsAction()
    {
        global $globalConfig;
        $fileId = $this->_getParam('fileId', 0);
        $token = $this->_getParam('token', '');

        $fileContent = "";
        $numberBytes = 0;
        $fileName = "";
        $pass = false;
        $localDownload = true;
        if ($fileId > 0) {
            if (!empty($token)) {
                $localDownload = false;
                $arrRes = ApiToken::checkDocsToken($token);
                if (sizeof($arrRes['body']) > 0) {
                    $accountID = $arrRes["body"]["account_id"];
                    $username = $arrRes["body"]["username"];
                    $avatar = $arrRes["body"]["avatar"];
                    $ps = $arrRes["body"]["ps"];
                    $pass = true;
                }
                unset($arrRes);
            } else {
                $arrLogin = Admin::getInstance()->getLogin();
                if (!isset($arrLogin['accountID']) || empty($arrLogin['accountID']))
                    $pass = false;
                else {
                    $accountID = $arrLogin["accountID"];
                    $username = $arrLogin["username"];
                    $avatar = $arrLogin['avatar'];
                    $ps = $arrLogin["ps"];
                    $pass = true;
                }
            }
            $fileInfo = File::getInstance()->selectOne($fileId);
            if ($fileInfo && $pass) {
                if (!isset($fileInfo['is_public']) || $fileInfo['is_public'] == 0) {
                    $checked = array();
                    $levelMemberGroup = GroupMember::getInstance()->getMemberGroupLevelById($fileInfo['group_id'], $accountID);
                    // error_log("level:".$levelMemberGroup);
                    //check permission
                    if ($fileInfo['owner'] != $accountID && !in_array((int)$levelMemberGroup, $globalConfig['ManagerDocsLevel'])) {
                        $ApiGroup = new ApiGroup();
                        $arrRes = $ApiGroup->getListGroupIdByMemberId($accountID);
                        $arrGroupId = array();
                        if ($arrRes["error"] == false) $arrGroupId = $arrRes["body"]["data"];
                        if ($arrGroupId) {
                            if ($fileInfo['group_id'] > 0 && in_array($fileInfo['group_id'], $arrGroupId))
                                $checked = true;
                            else {
                                $checked = ShareDocs::getInstance()->getDocsPermissionByAccountId($fileId, $accountID, $arrGroupId);
                            }
                        }
                        if (!$checked) {
                            $pass = false;
                        }
                    } else
                        $pass = true;
                }
            }
            if ($pass)//allow to download
            {
                $fullPath = $this->getDocsPath($fileId);
// error_log($fullPath);                
                if (!empty($fullPath) && file_exists($fullPath)) {
                    $fileExt = "";
                    $fileName = $fileInfo['name'];
                    $numberBytes = filesize("$fullPath");
                    $fh = fopen("$fullPath", 'r');
                    $fileContent = fread($fh, $numberBytes);
// error_log($fileContent);
                    fclose($fh);
                    /*//check if OpenOffice file
                    if(($localDownload == true) && Core_Helper::checkIsOpenDocs($fullPath, $fileExt)){
                        $fullURL = $this->getDownloadDocsPath($fileId);
                        $revisionKey = Core_Helper::getDocEditorKey("conv_".time()."_".$fileId."_".md5($fullURL).(isset($fileInfo['key'])?"_".$fileInfo['key']:""));
                        $arrRes = $this->convertToOpenDocs($fullURL, $fileExt, $fileInfo['name'], $revisionKey, $is_async = false);
                        if(empty($arrRes['error'])){
error_log('fullURL='.$fullURL.'\n downloadURL='.$arrRes['downloadUri']);                            
                            // $fileContent = file_get_contents($arrRes['downloadUri']);
                            if (($fileContent = file_get_contents(str_replace(" ","%20", $arrRes['downloadUri'])))===FALSE){
                                $fileContent = "";
                                error_log('get converted file error: \n');
                            }
                        }
                        else {
                            error_log('Convert error: \n'. $arrRes['error']);
                            $fileContent = "";
                        }    
error_log('convert & download ok');                        
                    }else{                                                
// error_log($numberBytes);                    
                        $fh = fopen("$fullPath", 'r');
                        $fileContent = fread($fh, $numberBytes);
// error_log($fileContent);                    
                        fclose($fh);
error_log('download ok');
                    }*/
                } else {
                    echo Zend_Json::encode(Core_Server::setOutputData(true, 'file not found!', array()));
                    exit;
                }

            } else {
                echo Zend_Json::encode(Core_Server::setOutputData(true, 'Permission denied!', array()));
                exit;
            }
        }


        list($width, $height) = getimagesize($fullPath);
        if ($width && $height) {
//            Core_Image::
            $imagick = new Imagick($fullPath);
            $imagick->writeImage($fileName);
            header('Content-Description: File Transfer');
            header('Content-Type: image/'.$imagick->getImageFormat());
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . $numberBytes);
           echo $imagick;

        } else {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . $numberBytes);
            echo($fileContent);
        }

        exit;
    }

    /**
        Upload file
        input:            
            groupId = -1
            parentId = 0
    **/
    public function uploadFileDocsAction()
    {
        global $actionDocs;
        $groupId = $this->_getParam('groupId', -1);
        $parentId = $this->_getParam('parentId', 0);

        $arrLogin = Admin::getInstance()->getLogin();
        if(!isset($arrLogin['accountID']) || empty($arrLogin['accountID']))
            return Core_Server::setOutputData(true, 'You must login!', array());               

        if (!isset($_FILES['files']['error']) || $_FILES['files']['error'] > 0) {
            return Core_Server::setOutputData(true, 'Error', array("data"=>Zend_Json::encode($_FILES['files']['error']))); 
        }

        if(!isset($_FILES['files']['tmp_name']) || empty($_FILES['files']['tmp_name'])) {
            return Core_Server::setOutputData(true, 'No file sent!', array());
        }

        $tmp = $_FILES['files']['tmp_name'];

        if (is_uploaded_file($tmp))
        {

            $filesize = $_FILES['files']['size'];
            // $ext = strtolower('.' . pathinfo($_FILES['files']['name'], PATHINFO_EXTENSION));

            if ($filesize <= 0 || $filesize > FILE_SIZE_MAX) {
                return Core_Server::setOutputData(true, 'File size is incorrect', array());
            }
            
            if(!$this->checkStorageSpace($filesize, $arrLogin['accountID'], $groupId)){
                return Core_Server::setOutputData(true, 'not enough storage to upload this file', array());
            }

            $filename = $_FILES['files']['name'];

            if(!Core_Helper::checkUploadDocsExtension($filename)) {
                return Core_Server::setOutputData(true, 'File type is not supported', array());
            }
            $partPath = "";
            if($groupId == -1)
            {
                $folderPath = DOC_ROOT_PATH . DIRECTORY_SEPARATOR . "users" . DIRECTORY_SEPARATOR . $arrLogin["accountID"];            
            }
            else
            {
                // check group existed
                $group = Group::getInstance()->getGroupByID($groupId);
                $arrError = array();
                if(empty($group)) {
                   return Core_Server::setOutputData(true, 'Group is not existed!', array());
                }

                //check permision user in group
                $groupMember = GroupMember::getInstance()->getGroupMemberByAccountAndGroupId($arrLogin['accountID'], $groupId);

                if(empty($groupMember) && $group['admin_id'] != $arrLogin['accountID']){
                    return Core_Server::setOutputData(true, 'permission denied', array());
                }

                if($parentId > 0){
                    $arrFile = File::getInstance()->selectOne($parentId);

                    if(empty($arrFile)){
                        return Core_Server::setOutputData(true, 'Folder parent not existed!', array());
                    }

                    //check owner
                    if($arrFile['owner'] != $arrLogin['accountID']){
                        return Core_Server::setOutputData(true, 'Folder parent permission denied ', array());                        
                    }
                    $partPath = $arrFile['path'];
                    $folderPath = PATH_FILES_UPLOAD_DIR . $arrFile['path'];
                }
                else { //file not found in folder
                    return Core_Server::setOutputData(true, 'Select a group to upload file!', array());
                }    
            }                
            if(!empty($folderPath))
            {    
                Core_Helper::createNewFolder($folderPath);                    
                // $fullPath = $this->createNewDocsByType($type, $folderPath, $newName);            
            }        
            $filename = Core_Helper::GetCorrectName(str_replace(' ', '_', $filename));
            if(move_uploaded_file($tmp, $folderPath.DIRECTORY_SEPARATOR.$filename))
            {
                $fileId = File::getInstance()->insert($filename, $partPath, FILE, $parentId, $arrLogin['accountID'], $groupId, 0, $filename, 1);
                if($fileId > 0)
                {
                    // $this->addActionlog($arrLogin['accountID'], $arrLogin['username'], $groupId, $parentId, $fileId, $type = 0, $iAction = 'upload', $moreInfo = "{'filename'=>".$filename."}");
                    ShareDocs::getInstance()->sendNotification($iAction="uploadfile", $fileId, $partPath, $filename, $groupId, $arrLogin['accountID'], $arrLogin['nickName'], $arrLogin['avatar'], $actionDocs);
                    return Core_Server::setOutputData(false, 'Upload successfull', array("fileId"=>$fileId, "URL"=>$folderPath.DIRECTORY_SEPARATOR.$filename));              
                }
                else{
                    unlink($folderPath.DIRECTORY_SEPARATOR.$filename);
                    return Core_Server::setOutputData(true, 'Upload failed', array());                              
                }
                
            }
        }    
        return Core_Server::setOutputData(true, 'Upload failed', array());
    }

    public function getDocumentsAction()
    {
        $arrLogin = Admin::getInstance()->getLogin();
        if(!isset($arrLogin['accountID']) || empty($arrLogin['accountID'])){
            echo Zend_Json::encode(Core_Server::setOutputData(true, 'You must login!', array()));
            exit;            
        }
        $group_id = $this->_getParam('group_id', -1);
        $parent_id = $this->_getParam('parent_id', 0);
        $page = $this->_getParam('page', 0);
        $limit = $this->_getParam('limit', 10);        
        // $totalSize = 0;
        $arrFiles = array();
        $arrTmps = array();
        $total = 0;
        if($group_id > 0) {

            //get group
            $arrGroup = Group::getInstance()->getGroupByID($group_id);

            if (empty($arrGroup)) {
                error_log("error= Group is not exists.");
                echo Zend_Json::encode(Core_Server::setOutputData(true, 'Group is not exists.', array()));
                exit;
            }

            //check permision user in group
            $groupMember = GroupMember::getInstance()->getGroupMemberByAccountAndGroupId($arrLogin['accountID'], $group_id);

            if (empty($groupMember) && $arrGroup['admin_id'] != $arrLogin['accountID']) {
                error_log("error= Permission deny.");

                echo Zend_Json::encode(Core_Server::setOutputData(true, 'Permission deny..', array()));
                exit;              
            }
            if($parent_id ==  0){
                $arrTmps = File::getInstance()->selectByGroupId($page, $limit, $group_id);                                
            }
            else{
                $arrTmps = File::getInstance()->selectByParent($page, $limit, $parent_id);
            }    

        }else{// get files, folders from My Documents 
            $arrTmps = File::getInstance()->selectMyDocumentsByParent($page, $limit, $arrLogin['accountID'], $parent_id, $group_id = -1);
        }        

        if($arrTmps['total'] > 0){
            $total = $arrTmps['total'];
            foreach ($arrTmps['data'] as $file){
                $arr = $file;
                if($file['type'] == FILE){
                    $processFileName = Core_Common::getExtensionAndFileName( $file['name']);
                    $ext = $processFileName['extent'];
                    $arr['ext'] = strtolower($ext);                    
                    if($file['group_id'] == -1)//My document onlyoffice
                    {
                        // $arr['downloadURL'] = ROOT_DOC_URL . DIRECTORY_SEPARATOR . "users". DIRECTORY_SEPARATOR . $file['owner'] . (!empty($file['path'])?DIRECTORY_SEPARATOR . $file['path']:"") . DIRECTORY_SEPARATOR . $file['name'];
                        $arr['downloadURL'] = BASE_URL. DIRECTORY_SEPARATOR . "api" . DIRECTORY_SEPARATOR . "docs" . DIRECTORY_SEPARATOR . "download-docs?fileId=".$file["_id"];
                        $fullPath = DOC_ROOT_PATH . DIRECTORY_SEPARATOR . "users". DIRECTORY_SEPARATOR . $file['owner'] . (!empty($file['path'])?DIRECTORY_SEPARATOR . $file['path']:"") . DIRECTORY_SEPARATOR .$file['name'];
                        if(Core_Helper::checkIsDocs($file['name'])){
                            $arr['URL'] = "/docs/editor/id/".$file["_id"];
                        }elseif (Core_Helper::checkIsImage($file['name'])) {
                            $arr['URL'] = ROOT_DOC_URL . DIRECTORY_SEPARATOR . "users". DIRECTORY_SEPARATOR . $file['owner'] . (!empty($file['path'])?DIRECTORY_SEPARATOR . $file['path']:"") . DIRECTORY_SEPARATOR . $file['name'];

                            $sUrlFile = ROOT_DOC_URL . DIRECTORY_SEPARATOR . "users". DIRECTORY_SEPARATOR . $file['owner'] . (!empty($file['path'])?DIRECTORY_SEPARATOR . $file['path']:"") . DIRECTORY_SEPARATOR . $file['name'];
                            $sPathFile = DOC_ROOT_PATH. DIRECTORY_SEPARATOR . "users". DIRECTORY_SEPARATOR . $file['owner'] . (!empty($file['path'])?DIRECTORY_SEPARATOR . $file['path']:"") . DIRECTORY_SEPARATOR . $file['name'];

                            list($width, $height) = getimagesize($sPathFile);
                            if ($width && $height) {

                                $sUrlThumbnail =  DOC_ROOT_PATH. DIRECTORY_SEPARATOR . "users". DIRECTORY_SEPARATOR . $file['owner'] . (!empty($file['path'])?DIRECTORY_SEPARATOR . $file['path']:"") . DIRECTORY_SEPARATOR ."thumbnail".DIRECTORY_SEPARATOR .  $file['name'];
                                if (file_exists($sUrlThumbnail)) {
                                    $sUrlFile = ROOT_DOC_URL. DIRECTORY_SEPARATOR . "users". DIRECTORY_SEPARATOR . $file['owner'] . (!empty($file['path'])?DIRECTORY_SEPARATOR . $file['path']:"") . DIRECTORY_SEPARATOR ."thumbnail".DIRECTORY_SEPARATOR .  $file['name'];
                                }

                            }
                            $arr['URL'] = $sUrlFile;

                        }
                    }
                    else {//document tạo trong 1 group
                        // $arr['downloadURL'] = PATH_FILES_URL.$file['path'].DIRECTORY_SEPARATOR.$file['name'];                        
                        $arr['downloadURL'] = BASE_URL . DIRECTORY_SEPARATOR . "api" . DIRECTORY_SEPARATOR . "docs" . DIRECTORY_SEPARATOR . "download-docs?fileId=" . $file["_id"];
                        $fullPath = PATH_FILES_UPLOAD_DIR . $file['path'] . '/' . $file['name'];
                        if (Core_Helper::checkIsDocs($file['name'])) {
                            $arr['URL'] = "/docs/editor/id/" . $file["_id"];
                        } elseif (Core_Helper::checkIsImage($file['name'])) {

                            $sUrlFile = PATH_FILES_URL.$file['path'].DIRECTORY_SEPARATOR.$file['name'];
                            $sPathFile = PATH_FILES_UPLOAD_DIR.$file['path'].DIRECTORY_SEPARATOR.$file['name'];

                            list($width, $height) = getimagesize($sPathFile);
                            if ($width && $height) {

                                $sUrlThumbnail =  PATH_FILES_UPLOAD_DIR.$file['path'].DIRECTORY_SEPARATOR."thumbnail".DIRECTORY_SEPARATOR.$file['name'];
                                if (file_exists($sUrlThumbnail)) {
                                    $sUrlFile = PATH_FILES_URL.$file['path'].DIRECTORY_SEPARATOR."thumbnail".DIRECTORY_SEPARATOR.$file['name'];
                                }
                            }
                            $arr['URL'] = $sUrlFile;
                        }
                    }
                    if(file_exists($fullPath)){
                        $arr['size'] = Core_Helper::FileSizeConvert(filesize($fullPath));
                    }
                    else{
                        $arr['size'] = 0;
                    }                    
                }
                else{//folder
                    if($file['group_id'] == 0)//My document onlyoffice
                    {
                        $fullPath = DOC_ROOT_PATH . DIRECTORY_SEPARATOR . "users". DIRECTORY_SEPARATOR . $file['owner'] . (!empty($file['path'])?DIRECTORY_SEPARATOR . $file['path']:"") . DIRECTORY_SEPARATOR .$file['name'];
                    }
                    else{
                        $fullPath = PATH_FILES_UPLOAD_DIR.$file['path'].'/'.$file['name'];

                    }
                    if(file_exists($fullPath)){
                        $arr['size'] = Core_Helper::GetFolderSize($fullPath);
                    }
                    else{
                        $arr['size'] = 0;
                    }                        
                }
                $arr['created'] = Date('d-M-Y H:i:s', $file['created']->sec);
                $arr['updated'] = Date('d-M-Y H:i:s', $file['updated']->sec);
                $arrFiles[] = $arr;
            }
        }        
        echo Zend_Json::encode(Core_Server::setOutputData(false, 'OK', array("total"=>$total, "data" => Zend_Json::encode($arrFiles))));
        exit;
    }

    private function upload($accountID="") {
        $result; $filename;

        if ($_FILES['files']['error'] > 0) {
            $result["error"] = 'Error ' . Zend_Json::encode($_FILES['files']['error']);
            return $result;
        }

        if(empty($_FILES['files']['tmp_name'])) {
            $result["error"] = 'No file sent';
            return $result;
        }

        $tmp = $_FILES['files']['tmp_name'];

        if (is_uploaded_file($tmp))
        {
            $filesize = $_FILES['files']['size'];
            $ext = strtolower('.' . pathinfo($_FILES['files']['name'], PATHINFO_EXTENSION));

            if ($filesize <= 0 || $filesize > FILE_SIZE_MAX) {
                $result["error"] = 'File size is incorrect';
                return $result;
            }

            if (!in_array($ext, $this->getFileExts())) {
                $result["error"] = 'File type is not supported';
                return $result;
            }

            $filename = Core_Helper::GetCorrectName($_FILES['files']['name']);
            $folderUpload = !empty($accountID) ? DOC_ROOT_PATH . "/" . $accountID : DOC_ROOT_PATH;
            if (!file_exists($folderUpload)) {
                mkdir($folderUpload, 0777, true);
            }
            if( !move_uploaded_file($tmp,  $folderUpload . "/" . $filename)) {
                $result["error"] = 'Upload failed';
                return $result;
            }

        } else {
            $result["error"] = 'Upload failed';
            return $result;
        }
        $result["error"] = 0;
        $result["filename"] = $filename;
        return $result;
    }

    private function track($fileInfo, $fullFilePath) {
        $_trackerStatus = array(
            0 => 'NotFound',
            1 => 'Editing',
            2 => 'MustSave',
            3 => 'Corrupted',
            4 => 'Closed',
            6 => 'Edited',
            7 => 'SaveError'
        );
        $data;
        $result = "";
        if (($body_stream = file_get_contents('php://input'))===FALSE){
            $result["error"] = "Bad Request";
            return $result;
        }

        $data = Zend_Json::decode($body_stream); //json_decode - PHP 5 >= 5.2.0

        if ($data === NULL){
            $result["error"] = "Bad Response";
            return $result;
        }

        $status = $_trackerStatus[$data["status"]];

        switch ($status){
            case "MustSave":
            case "Corrupted":                

                $downloadUri = $data["url"];
                $saved = 1;

                if(Core_Helper::checkIsOpenDocs($fullFilePath, $fileExt)){
                    // $fullURL = $this->getDownloadDocsPath($fileInfo['_id']);
                    $revisionKey = Core_Helper::getDocEditorKey("conv_".time()."_".md5($downloadUri).(isset($fileInfo['key'])?"_".$fileInfo['key']:""));
                    $arrRes = $this->convertToOpenDocs($downloadUri, $fileExt, $fileInfo['name'], $revisionKey, $is_async = false);
                    if(empty($arrRes['error'])){
error_log('fullURL='.$fullFilePath.'\n downloadURL='.$arrRes['downloadUri']);
                        $downloadUri = $arrRes['downloadUri']; 
                        // $fileContent = file_get_contents($arrRes['downloadUri']);
                        /*if (($downloadUri = file_get_contents(str_replace(" ","%20", $arrRes['downloadUri'])))===FALSE){
                            $fileContent = "";
                            $saved = 0;
                            error_log('get converted file error: \n');
                        }*/
error_log('convert & download ok');                                                
                    }
                    else {
                        error_log('Convert error: \n'. $arrRes['error']);
                        $downloadUri = "";
                    }    
                }
                
                if (($new_data = file_get_contents(str_replace(" ","%20", $downloadUri)))===FALSE){
                    $saved = 0;
error_log("error to save file:".$downloadUri);                    
                } else {
error_log("save file:".$downloadUri);                    
                    file_put_contents($fullFilePath, $new_data, LOCK_EX);
error_log("to :".$fullFilePath);
                }

                $result["c"] = "saved";
                $result["status"] = $saved;
                break;
        }        
        $result["error"] = 0;
        return $result;
    }

    private function convert($fullPath, $fileUri) {        
        $path_parts = pathinfo($fullPath);
        $dir = $path_parts['dirname'];
        $extension = $path_parts['extension'];
        $fileName = $path_parts['basename'];        
        $internalExtension = trim(Core_Helper::getInternalExtension($fullPath),'.');

        if (strpos(DOC_SERV_CONVERT, "." + $extension) >= 0 && $internalExtension != "") {            
            if ($fileUri == "") {
                $result["error"] = "error: fileUri is null";
                return $result;
            }
            $key = Core_Helper::GenerateRevisionId($fileUri);

            $newFileUri;
            $result;
            $percent;

            try {
                $percent = Core_Helper::GetConvertedUri($fileUri, $extension, $internalExtension, $key, TRUE, $newFileUri);
            }
            catch (Exception $e) {
                $result["error"] = "error: " . $e->getMessage();
                return $result;
            }

            if ($percent != 100)
            {
                $result["step"] = $percent;
                $result["filename"] = $fileName;
                $result["fileUri"] = $fileUri;
                return $result;
            }

            $baseNameWithoutExt = substr($fileName, 0, strlen($fileName) - strlen($extension) - 1);

            $newFileName = Core_Helper::GetCorrectName($fullPath);

            if (($data = file_get_contents(str_replace(" ","%20",$newFileUri)))===FALSE){
                $result["error"] = 'Bad Request';
                return $result;
            } else {
                file_put_contents($dir . "/" . $newFileName, $data, LOCK_EX);
            }

            unlink($dir . "/" . $fileName);

            $fileName = $newFileName;
        }
        $result["error"] = 0;
        $result["filename"] = $fileName;
        return $result;
    }

    private function save($fullPath, $downloadUri) {
        $contentType = "text/plain";        
        $path_parts = pathinfo($fullPath);
        $dir = $path_parts['dirname'];
        $extension = $path_parts['extension'];
        $fileName = $path_parts['basename'];        

        if (empty($downloadUri) || empty($fileName))
        {
            $result["error"] = 'Error request';
            return $result;
        }

        $newType =  trim(pathinfo($downloadUri, PATHINFO_EXTENSION),'.');
        $currentType = trim(!empty($_GET["filetype"]) ? $_GET["filetype"] : pathinfo($downloadUri, PATHINFO_EXTENSION),'.');

        if (strtolower($newType) != strtolower($currentType))
        {
            $key = Core_Helper::GenerateRevisionId($downloadUri);

            $newFileUri;

            try {
                $percent = Core_Helper::GetConvertedUri($downloadUri, $newType, $currentType, $key, FALSE, $newFileUri);
                if ($percent != 100){
                    $result["error"] = "error: Can't convert file";
                    return $result;
                }
            }
            catch (Exception $e) {
                $result["error"] = "error: " . $e->getMessage();
                return $result;
            }
           
            $downloadUri = $newFileUri;
            $newType = $currentType;
        }
        $baseNameWithoutExt = substr($fileName, 0, strlen($fileName) - strlen($extension) - 1);
        $fileName = $baseNameWithoutExt . "." . $newType;
        if (($data = file_get_contents(str_replace(" ","%20", $downloadUri)))===FALSE){
            $result["error"] = 'Bad Request';
            return $result;
        } else {
            file_put_contents($dir . "/" . $fileName, $data, LOCK_EX);
        }
        $result["error"] = 0;
        $result["success"] = 'success';
        return $result;
    }

    //user for all files and folders
    private function getDocsPath($fileId,$folder=''){
        $fileInfo = File::getInstance()->selectOne($fileId);
        $fullPath = "";
        if($fileInfo)
        {
            if($fileInfo['group_id'] > 0){
                if(empty($folder)) {
                    $fullPath = PATH_FILES_UPLOAD_DIR . (!empty($fileInfo['path']) ? $fileInfo['path'] : "") . DIRECTORY_SEPARATOR . $fileInfo['name'];
                }else{
                    $fullPath = PATH_FILES_UPLOAD_DIR . (!empty($fileInfo['path']) ? $fileInfo['path'] : "") . DIRECTORY_SEPARATOR .$folder.DIRECTORY_SEPARATOR. $fileInfo['name'];
                }
            }
            else{
                if(empty($folder)) {
                    $fullPath = DOC_ROOT_PATH . DIRECTORY_SEPARATOR . "users" . DIRECTORY_SEPARATOR . $fileInfo['owner'] . (!empty($fileInfo['path']) ? $fileInfo['path'] : "") . DIRECTORY_SEPARATOR . $fileInfo['name'];
                }else {
                    $fullPath = DOC_ROOT_PATH . DIRECTORY_SEPARATOR . "users" . DIRECTORY_SEPARATOR . $fileInfo['owner'] . (!empty($fileInfo['path']) ? $fileInfo['path'] : "") . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $fileInfo['name'];
                }
            }
        }
// error_log('fullPath:'.$fullpath);        
        return $fullPath;    
    }

    private function getURLDocsFile($fileId, $filePath, $fileName, $accountID, $groupId)
    {
        if(Core_Helper::checkIsDocs($fileName))
            return "/docs/editor/id/".$fileId;
        elseif(Core_Helper::checkIsImage($fileName))
        {    
            if($groupId == -1)
                return ROOT_DOC_URL . DIRECTORY_SEPARATOR . "users". DIRECTORY_SEPARATOR . $accountID . (!empty($filePath)?DIRECTORY_SEPARATOR . $filePath:"") . DIRECTORY_SEPARATOR .$fileName;
            else                
                return PATH_FILES_URL.$filePath.DIRECTORY_SEPARATOR.$fileName;            
        }
        else{
            return "";
        }    
    }

    //user for all files
    private function getDownloadDocsPath($fileId){
        $fileInfo = File::getInstance()->selectOne($fileId);
        $downloadPath = "";
        if($fileInfo)
        {
            if($fileInfo['type'] == 0){
                if($fileInfo['group_id'] > 0){                    
                    $downloadPath = PATH_FILES_URL . (!empty($fileInfo['path'])? $fileInfo['path']:"") . $fileInfo['name'];
                }
                else{                    
                    $downloadPath = ROOT_DOC_URL . DIRECTORY_SEPARATOR . "users". DIRECTORY_SEPARATOR . $fileInfo['owner'] . (!empty($fileInfo['path'])? $fileInfo['path']:"") . DIRECTORY_SEPARATOR .$fileInfo['name'];
                }
            }
        }
        return $downloadPath;    
    }

    private function getStorageGroup($accountID, $group_id = -1, &$limitStorage, &$percentStorage){
        $folderPath = "";
        $storaged = 0;
        if($group_id == -1){// my documents
            $folderPath = DOC_ROOT_PATH.DIRECTORY_SEPARATOR."users".DIRECTORY_SEPARATOR.$accountID;
            $limitStorage = Core_Helper::FileSizeConvert(FOLDER_USER_DOCS_SIZE_MAX);
            $storaged = Core_Helper::GetFolderSize($folderPath, false);
            if($storaged > 0)
                $percentStorage = round(($storaged * 100 / FOLDER_USER_DOCS_SIZE_MAX), 0);
            else{
                $percentStorage = 0;
                return $storaged;
            } 
                

        }elseif ($group_id > 0) {
            $folderPath = PATH_FILES_UPLOAD_DIR.DIRECTORY_SEPARATOR.$group_id;
            $limitStorage = Core_Helper::FileSizeConvert(FOLDER_GROUP_DOCS_SIZE_MAX);
            $storaged = Core_Helper::GetFolderSize($folderPath, false);
            if($storaged > 0)
                $percentStorage = round(($storaged * 100 / FOLDER_GROUP_DOCS_SIZE_MAX), 0);
            else{
                $percentStorage = 0;
                return $storaged;
            } 
                
        }else{
            $limitStorage = 'undefined';
            $percentStorage = 'undefined';
            return 0;
        }        
        return Core_Helper::FileSizeConvert($storaged);
    }

    private static function checkStorageSpace($fileSize, $accountID, $group_id = -1){
        $folderPath = "";
        $storaged = 0;
        if($group_id == -1){// my documents
            $folderPath = DOC_ROOT_PATH.DIRECTORY_SEPARATOR."users".DIRECTORY_SEPARATOR.$accountID;
            $storaged = Core_Helper::GetFolderSize($folderPath, false);
            $newStorage = $storaged + (int)$fileSize;
            if($newStorage <= FOLDER_USER_DOCS_SIZE_MAX)
                return true;            
        }elseif ($group_id > 0) {
            $folderPath = PATH_FILES_UPLOAD_DIR.DIRECTORY_SEPARATOR.$group_id;
            $storaged = Core_Helper::GetFolderSize($folderPath, false);
            $newStorage = $storaged + (int)$fileSize;
            if($newStorage <= FOLDER_GROUP_DOCS_SIZE_MAX)                
                return true;            
        }
        return false;
    }

    private function convertToOpenDocs($url, $outPutType, $title, $revisionKey, $is_async = true)
    {
        $fileType = '';
        switch ($outPutType) {
            case 'ods':
                $fileType = 'xlsx';
                break;
            case 'odt':
                $fileType = 'docx';
                break;
            case 'odp':
                $fileType = 'pptx';
                break;    

            default:
                # code...
                break;
        }
        if(!empty($fileType)){
            $urlConvert = $this->generateUrlToConverter($url, $fileType, $outPutType, $title, $revisionKey, $is_async);
error_log("urlToConvert_".$urlConvert);            
            $newFileUri;
            try {
                $percent = $this->GetConvertedUri($urlConvert, $newFileUri);
                if ($percent != 100){
                    $result["error"] = "error: Can't convert file";
                    $result["downloadUri"] = "";
                    return $result;
                }
            }
            catch (Exception $e) {
                $result["error"] = "error: " . $e->getMessage();
                $result["downloadUri"] = "";
                return $result;
            }
            $result["error"] = "";
            $result["downloadUri"] = $newFileUri;    
            return $result;
        }
        else{
            return array("error"=>"Unknown error!", "downloadUri" => "");
        }
    }


    private function generateUrlToConverter($document_uri, $from_extension, $to_extension, $title, $document_revision_id, $is_async) {
        $urlToConverterParams = array(
                                    "url" => $document_uri,
                                    "outputtype" => trim($to_extension,'.'),
                                    "filetype" => trim($from_extension, '.'),
                                    "title" => $title,
                                    "key" => $document_revision_id);

        $urlToConverter = DOC_SERV_CONVERTER_URL . "?" . http_build_query($urlToConverterParams);

        if ($is_async)
            $urlToConverter = $urlToConverter . "&async=true";

        return $urlToConverter;
    }

    private function GetConvertedUri($urlToConverter, &$converted_document_uri) {
        $converted_document_uri = "";
        $responceFromConvertService = $this->SendRequestToConvertService($urlToConverter);               
        $errorElement = $responceFromConvertService->Error;
        if ($errorElement != NULL && $errorElement != "") $this->ProcessConvServResponceError($errorElement);

        $isEndConvert = $responceFromConvertService->EndConvert;
        $percent = $responceFromConvertService->Percent . "";
error_log($errorElement."__");
        if ($isEndConvert != NULL && strtolower($isEndConvert) == "true")
        {
error_log("percent:".$percent);            
            $converted_document_uri = $responceFromConvertService->FileUrl;
            $percent = 100;
        }
        else if ($percent >= 100)
            $percent = 99;

        return $percent;
    }
    private function SendRequestToConvertService($urlToConverter) {
        $response_xml_data;
        $countTry = 0;

        $opts = array('http' => array(
                'method'  => 'GET',
                'timeout' => DOC_SERV_TIMEOUT 
            )
        );

        if (substr($urlToConverter, 0, strlen("https")) === "https") {
            $opts['ssl'] = array( 'verify_peer'   => FALSE );
        }
     
        $context  = stream_context_create($opts);
        while ($countTry < ServiceConverterMaxTry)
        {
            $countTry = $countTry + 1;
            $response_xml_data = file_get_contents($urlToConverter, FALSE, $context);
            if ($response_xml_data !== false){ break; }
        }

        if ($countTry == ServiceConverterMaxTry)
        {
            throw new Exception ("Bad Request or timeout error");
        }

        libxml_use_internal_errors(true);
        $data = simplexml_load_string($response_xml_data);
        if (!$data) {
            $exc = "Bad Response. Errors: ";
            foreach(libxml_get_errors() as $error) {
                $exc = $exc . "\t" . $error->message;
            }
            throw new Exception ($exc);
        }

        return $data;
    }

    private function ProcessConvServResponceError($errorCode) {
        $errorMessageTemplate = "Error occurred in the document service: ";
        $errorMessage = '';

        switch ($errorCode)
        {
            case -8:
                $errorMessage = $errorMessageTemplate . "Error document VKey(From new version VKey has disabled)";
                break;
            case -7:
                $errorMessage = $errorMessageTemplate . "Error document request";
                break;
            case -6:
                $errorMessage = $errorMessageTemplate . "Error while accessing the conversion result database";
                break;
            case -5:
                $errorMessage = $errorMessageTemplate . "Error unexpected guid";
                break;
            case -4:
                $errorMessage = $errorMessageTemplate . "Error while downloading the document file to be converted";
                break;
            case -3:
                $errorMessage = $errorMessageTemplate . "Error convertation error";
                break;
            case -2:
                $errorMessage = $errorMessageTemplate . "Error convertation timeout";
                break;
            case -1:
                $errorMessage = $errorMessageTemplate . "Error convertation unknown error";
                break;
            case 0:
                break;
            default:
                $errorMessage = $errorMessageTemplate . "ErrorCode = " . $errorCode;
                break;
        }

        throw new Exception($errorMessage);
    }

    private function restoreFolder($folderName, $folderPath, $partPath, $group_id, $parentId, $account_id, $backupPath)
    {        
        if(file_exists($backupPath) && is_dir($backupPath)){            
            $newname = Core_Helper::GetCorrectFolderName($folderPath, $folderName);
            $fileId = File::getInstance()->insert($newname, $partPath, FOLDER, $parentId, $account_id, $group_id, 0, $newname, 1);
            if($fileId > 0){
                $arrFiles = Core_Helper::listAllEntriesFolder($backupPath);
                if(sizeof($arrFiles)){
error_log(Zend_Json::encode($arrFiles));                    
                    foreach ($arrFiles as $file) {
                        if($file["type"]){//is folder
                            self::restoreFolder($file['name'], $folderPath . DIRECTORY_SEPARATOR . $folderName, $partPath. DIRECTORY_SEPARATOR . $folderName, $group_id, $fileId, $account_id, $backupPath . DIRECTORY_SEPARATOR . $file["name"]);
                        }else{//is file                            
                            File::getInstance()->insert($file['name'], $partPath. DIRECTORY_SEPARATOR . $folderName, FILE, $fileId, $account_id, $group_id, 0, $file['name'], 1);
                        }
                    }
                }
                return array('fileId' => $fileId, 'name' => $newname);
            }else{
                return array();            
            }
        }    
        else{
            return array();
        }
    }
}
