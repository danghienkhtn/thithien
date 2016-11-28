<?php

/**
 * @author      :   Hien.nd
 * @name        :   Model_API
 * @version     :   201607
 * @copyright   :   Gianty
 * @todo        :   Api model
 */
class ApiDocs{

    public static $actionlogDocsType = 20;//defined in ActionLog models
    
    public function createFolder($group_id = -1, $parent_id = 0, $folderName="Untitled")
    {
        $arrLogin = Admin::getInstance()->getLogin();
        if(!isset($arrLogin['accountID']) || empty($arrLogin['accountID']))
            return Core_Server::setOutputData(true, 'You must login!', array());
        $partPath = "";
        $groupName = "";
        $parentName = "";
        if($group_id > 0)//document tạo trong 1 group            
        {            
            //get group
            $arrGroup = Group::getInstance()->getGroupByID($group_id);

            if (empty($arrGroup)) {
                error_log("error= Group is not exists.");
                return Core_Server::setOutputData(true, 'Group is not exists.', array());                
            }

            //check permision user in group
            $groupMember = GroupMember::getInstance()->getGroupMemberByAccountAndGroupId($arrLogin['accountID'], $group_id);

            if (empty($groupMember) && $arrGroup['admin_id'] != $arrLogin['accountID']) {
                error_log("error= Permission deny.");
                return Core_Server::setOutputData(true, 'Permission deny...', array());              
            }
            $groupName = $arrGroup['group_name'];
            if($parent_id ==  0){                                
                $partPath = DIRECTORY_SEPARATOR . $group_id;
                $parentName = $groupName;
                // return Core_Server::setOutputData(true, 'Select a group to create the folder', array());              
            }
            else{
                $arrFile = File::getInstance()->selectOne($parent_id);
                if(empty($arrFile)){
                    return Core_Server::setOutputData(true, 'folder parent is not exited!', array());
                }

                //check owner
                //20160812 cho phep ko phai owner cung co the tao folder con
                /*if($arrFile['owner'] != $arrLogin['accountID']){
                    return Core_Server::setOutputData(true, 'Permission denied!', array());
                }*/
                $partPath = $arrFile['path'].DIRECTORY_SEPARATOR.$arrFile['name'];
                $parentName = $arrFile['name'];
            }
            // $arr['URL'] = "/docs/editor/id/".$file["_id"];
            $fullPath = PATH_FILES_UPLOAD_DIR . (!empty($partPath)? $partPath . DIRECTORY_SEPARATOR:"") . $folderName;
        }
        else{//My document onlyoffice
            $group_id = -1;
            //Create Home Document Folder
            Core_Helper::createNewFolder(DOC_ROOT_PATH . DIRECTORY_SEPARATOR . "users". DIRECTORY_SEPARATOR . $arrLogin['accountID']);
            if($parent_id > 0){
                $arrFile = File::getInstance()->selectOne($parent_id);

                if(empty($arrFile)){
                    return Core_Server::setOutputData(true, 'folder parent is not exited!', array());
                }

                //check owner
                if($arrFile['owner'] != $arrLogin['accountID']){
                    return Core_Server::setOutputData(true, 'Permission denied!', array());
                }
                $partPath = (!empty($arrFile['path']))?$arrFile['path'].DIRECTORY_SEPARATOR.$arrFile['name']:DIRECTORY_SEPARATOR.$arrFile['name'];
                $parentName = $arrFile['name'];
            }            
            $fullPath = DOC_ROOT_PATH . DIRECTORY_SEPARATOR . "users". DIRECTORY_SEPARATOR . $arrLogin['accountID'] . (!empty($partPath)? $partPath:"") . DIRECTORY_SEPARATOR .$folderName;            
        }
        if (!empty($fullPath) && $fullPath != "" && !file_exists($fullPath)) {            
            $fileId = File::getInstance()->insert($folderName, $partPath, FOLDER, $parent_id, $arrLogin['accountID'], $group_id, 0, $folderName, 1);
            if($fileId > 0)
            {
                if(Core_Helper::createNewFolder($fullPath))
                {                    
                    $this->addActionlog("createfolder", $iType = 0, $iAccountID = $arrLogin['accountID'], $sAccountName = $arrLogin['nickName'], $sAccountAvatar = $arrLogin['avatar'], $groupId = $group_id, $groupName, $parent_id, $parentName, $fileId, $fileName = $folderName, $fileType=1, $filePath=$fullPath, $fileUrl="", $detail = array(), $sNote = '');                    
                    $arrReturn = array("fileId"=> $fileId, "folderPath" => $fullPath);
                    return Core_Server::setOutputData(false, 'OK', array("data" => Zend_Json::encode($arrReturn)));
                }
                else{
                    File::getInstance()->delete($fileId);
                    return Core_Server::setOutputData(true, "create folder error, check folder permission again!" , array());
                }    
            }            
        }
        return Core_Server::setOutputData(true, 'Create folder error.', array());      
    }
    public function getDocuments($page, $limit, $group_id, $parent_id)
    {
        $arrLogin = Admin::getInstance()->getLogin();
        if(!isset($arrLogin['accountID']) || empty($arrLogin['accountID']))
            return Core_Server::setOutputData(true, 'You must login!', array());
        // $totalSize = 0;
        $arrFiles = array();
        $arrTmps = array();
        $total = 0;
        if($group_id > 0) {

            //get group
            $arrGroup = Group::getInstance()->getGroupByID($group_id);

            if (empty($arrGroup)) {
                error_log("error= Group is not exists.");
                return Core_Server::setOutputData(true, 'Group is not exists.', array());                
            }

            //check permision user in group
            $groupMember = GroupMember::getInstance()->getGroupMemberByAccountAndGroupId($arrLogin['accountID'], $group_id);

            if (empty($groupMember) && $arrGroup['admin_id'] != $arrLogin['accountID']) {
                error_log("error= Permission deny.");
                return Core_Server::setOutputData(true, 'Permission deny..', array());              
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
                        $arr['downloadURL'] = BASE_URL. DIRECTORY_SEPARATOR . "api" . DIRECTORY_SEPARATOR . "docs" . DIRECTORY_SEPARATOR . "download-docs?fileId=".$file['_id'];
                        $fullPath = DOC_ROOT_PATH . DIRECTORY_SEPARATOR . "users". DIRECTORY_SEPARATOR . $file['owner'] . (!empty($file['path'])?DIRECTORY_SEPARATOR . $file['path']:"") . DIRECTORY_SEPARATOR .$file['name'];
                        if(Core_Helper::checkIsDocs($file['name'])){
                            $arr['URL'] = "/docs/editor/id/".$file["_id"];
                        }elseif (Core_Helper::checkIsImage($file['name'])) {
                            $sFileName = $file['name'];
                            $sUrlFile = ROOT_DOC_URL . DIRECTORY_SEPARATOR . "users". DIRECTORY_SEPARATOR . $file['owner'] . (!empty($file['path'])?DIRECTORY_SEPARATOR . $file['path']:"") . DIRECTORY_SEPARATOR . $file['name'];
                            $sUrl = ROOT_DOC_URL . DIRECTORY_SEPARATOR . "users". DIRECTORY_SEPARATOR . $file['owner'] . (!empty($file['path'])?DIRECTORY_SEPARATOR . $file['path']:"") . DIRECTORY_SEPARATOR;

                            if(file_exists($sUrl.THUMBNAIL.DIRECTORY_SEPARATOR.$sFileName)){
                                $sUrlFile = $sUrl.THUMBNAIL.DIRECTORY_SEPARATOR.$sFileName;
                            }
                            $arr['URL'] = $sUrlFile;
                        }
                    }
                    else{//document tạo trong 1 group
                        $arr['downloadURL'] = BASE_URL. DIRECTORY_SEPARATOR . "api" . DIRECTORY_SEPARATOR . "docs" . DIRECTORY_SEPARATOR . "download-docs?fileId=".$file['_id'];
                        //PATH_FILES_URL.$file['path'].DIRECTORY_SEPARATOR.$file['name'];                        
                        $fullPath = PATH_FILES_UPLOAD_DIR.$file['path'].'/'.$file['name'];
                        if(Core_Helper::checkIsDocs($file['name'])){
                            $arr['URL'] = "/docs/editor/id/".$file["_id"];
                        }elseif (Core_Helper::checkIsImage($file['name'])) {
                            $arr['URL'] =  PATH_FILES_URL.$file['path'].DIRECTORY_SEPARATOR.$file['name'];

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
        //Assign view
        return Core_Server::setOutputData(false, 'OK', array("total"=>$total, "data" => Zend_Json::encode($arrFiles)));
    }

    /**
        Create anew docs
        input:
            type = spreadsheet, presentation, document
            g[roup] = 0
            p[arentId] = 0
    **/
    public function createDocs($groupId=-1, $parentId=0, $type="document", $name="")
    {
        global $actionDocs;
        $arrLogin = Admin::getInstance()->getLogin();
        if(!isset($arrLogin['accountID']) || empty($arrLogin['accountID']))
            return Core_Server::setOutputData(true, 'You must login!', array());       
        $newName = !empty($name)?$name:"Untitled";        
        $fullPath = "";
        $partPath = "";
        $folderPath = "";
        $error = 0;
        $groupName = "";
        $parentName = "";
        if(!empty($type)){
            if($groupId == -1)
            {
                $folderPath = DOC_ROOT_PATH . DIRECTORY_SEPARATOR . "users" . DIRECTORY_SEPARATOR . $arrLogin["accountID"];
                if($parentId > 0){
                    $arrFile = File::getInstance()->selectOne($parentId);

                    if(empty($arrFile)){
                        return Core_Server::setOutputData(true, 'folder parent is not exited!', array());
                    }

                    //check owner
                    if($arrFile['owner'] != $arrLogin['accountID']){
                        return Core_Server::setOutputData(true, 'Permission denied!', array());
                    }
                    $partPath = (!empty($arrFile['path']))?$arrFile['path'].DIRECTORY_SEPARATOR.$arrFile['name']:DIRECTORY_SEPARATOR.$arrFile['name'];
                    $parentName = $arrFile['name'];
                }            
                $folderPath .= (!empty($partPath)?$partPath:"");
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
                $groupName = $group['group_name'];
                if($parentId > 0){
                    $arrFile = File::getInstance()->selectOne($parentId);

                    if(empty($arrFile)){
                        return Core_Server::setOutputData(true, 'Folder parent not existed!', array());
                    }

                    //check owner
                    //20160812 cho phep tao file trong folder ko phai owner
                    /*if($arrFile['owner'] != $arrLogin['accountID']){
                        return Core_Server::setOutputData(true, 'Folder parent permission denied ', array());                        
                    }*/
                    $partPath = $arrFile['path'].DIRECTORY_SEPARATOR.$arrFile['name'];
                    $folderPath = PATH_FILES_UPLOAD_DIR . $partPath;
                    $parentName = $arrFile['name'];
                }
                else { //file not found in folder
                    $partPath = DIRECTORY_SEPARATOR.$groupId;
                    $folderPath = PATH_FILES_UPLOAD_DIR . $partPath;
                    $parentName = "";  
                    $parentId = 0; 
                    // return Core_Server::setOutputData(true, 'Select a group to create document!', array());
                }    
            }                
            if(!empty($folderPath))
            {    
                Core_Helper::createNewFolder($folderPath);                    
                $fullPath = $this->createNewDocsByType($type, $folderPath, $newName);            
            }    
        }                       
        if (!empty($fullPath) && $fullPath != "" && file_exists($fullPath)) {
            $fileId = File::getInstance()->insert($newName, $partPath, FILE, $parentId, $arrLogin['accountID'], $groupId, 0, $newName, 1);            
            if($fileId > 0)
            {
                //send notification
                ShareDocs::getInstance()->sendNotification($iAction="createfile", $fileId, $folderPath, $newName, $groupId, $arrLogin['accountID'], $arrLogin['nickName'], $arrLogin['avatar'], $actionDocs, $shareType="", $share_account_id = "", $share_account_name="");
                //add action log    
                $this->addActionlog("createfile", $iType = 0, $iAccountID = $arrLogin['accountID'], $sAccountName = $arrLogin['nickName'], $sAccountAvatar = $arrLogin['avatar'], $groupId, $groupName, $parentId, $parentName, $fileId, $newName, $fileType=0, $filePath=$fullPath, $fileUrl="/docs/editor/id/".$fileId, $detail = array(), $sNote = '');
                return Core_Server::setOutputData(false, 'OK', array("fileId"=>$fileId, "URL"=>"/docs/editor/id/".$fileId));
            }
            else{
                unlink($fullPath);
                return Core_Server::setOutputData(true, 'Create new file error', array());                              
            }            
        }
        return Core_Server::setOutputData(true, 'Create new docs error', array());
    }    
    
    /**
        Upload file
        input:            
            groupId = -1
            parentId = 0
    **/
    public function UploadFileDocs($groupId=-1, $parentId=0)
    {
        global $actionDocs;
        $arrLogin = Admin::getInstance()->getLogin();
        if(!isset($arrLogin['accountID']) || empty($arrLogin['accountID']))
            return Core_Server::setOutputData(true, 'You must login!', array());       
        // $params = $_GET['abc'];

        if (!isset($_FILES['files']) || $_FILES['files']['error'] > 0) {
            return Core_Server::setOutputData(true, 'Loading file error ', array()); 
        }

        if(!isset($_FILES['files']['tmp_name']) || empty($_FILES['files']['tmp_name'])) {
            return Core_Server::setOutputData(true, 'No file sent!', array());
        }

        $tmp = $_FILES['files']['tmp_name'];
        $partPath = "";
        $groupName = "";
        $parentName = "";
        if (is_uploaded_file($tmp))
        {

            $filesize = $_FILES['files']['size'];
            // $ext = strtolower('.' . pathinfo($_FILES['files']['name'], PATHINFO_EXTENSION));

            if ($filesize <= 0 || $filesize > FILE_SIZE_MAX) {
                return Core_Server::setOutputData(true, 'File size is incorrect', array());
            }
            
            if(!$this->checkStorageSpace($filesize, $arrLogin['accountID'], $groupId)){
                return Core_Server::setOutputData(true, 'not enough storage to upload this file!', array());
            }

            $filename = $_FILES['files']['name'];

            if(!Core_Helper::checkUploadDocsExtension($filename)) {
                return Core_Server::setOutputData(true, 'File type is not supported', array());
            }

            if($groupId == -1)
            {
                $folderPath = DOC_ROOT_PATH . DIRECTORY_SEPARATOR . "users" . DIRECTORY_SEPARATOR . $arrLogin["accountID"];
                if($parentId > 0){
                    $arrFile = File::getInstance()->selectOne($parentId);

                    if(empty($arrFile)){
                        return Core_Server::setOutputData(true, 'folder parent is not exited!', array());
                    }

                    //check owner
                    if($arrFile['owner'] != $arrLogin['accountID']){
                        return Core_Server::setOutputData(true, 'Permission denied!', array());
                    }
                    $partPath = (!empty($arrFile['path']))?$arrFile['path'].DIRECTORY_SEPARATOR.$arrFile['name']:DIRECTORY_SEPARATOR.$arrFile['name'];
                    $parentName = $arrFile['name'];
                }            
                $folderPath .= (!empty($partPath)?$partPath:"");
                // $folderPath = DOC_ROOT_PATH . DIRECTORY_SEPARATOR . "users" . DIRECTORY_SEPARATOR . $arrLogin["accountID"];            
            }
            else
            {
                // check group existed
                $group = Group::getInstance()->getGroupByID($groupId);
                $arrError = array();
                if(empty($group)) {
                   return Core_Server::setOutputData(true, 'Group is not existed!', array());
                }
                $groupName = $group['group_name'];
                //check permision user in group
                $groupMember = GroupMember::getInstance()->getGroupMemberByAccountAndGroupId($arrLogin['accountID'], $groupId);
                // $level = GroupMember::getInstance()->getMemberGroupLevelById($groupId, $arrLogin['accountID']);
                if(empty($groupMember)){// && !in_array((int)$level, $globalConfig['ManagerDocsLevel'])
                    return Core_Server::setOutputData(true, 'permission denied', array());
                }

                if($parentId > 0){
                    $arrFile = File::getInstance()->selectOne($parentId);

                    if(empty($arrFile)){
                        return Core_Server::setOutputData(true, 'Folder parent not existed!', array());
                    }

                    //check owner
                    /*if($arrFile['owner'] != $arrLogin['accountID']){
                        return Core_Server::setOutputData(true, 'Folder parent permission denied ', array());                        
                    }*/
                    $partPath = $arrFile['path'].DIRECTORY_SEPARATOR.$arrFile['name'];
                    $folderPath = PATH_FILES_UPLOAD_DIR . $partPath;
                    $parentName = $arrFile['name'];
                }
                else { //file not found in folder
                    $partPath = DIRECTORY_SEPARATOR.$groupId;
                    $folderPath = PATH_FILES_UPLOAD_DIR . $partPath;
                    $parentName = "";  
                    $parentId = 0;
                    // return Core_Server::setOutputData(true, 'Select a group to upload file!', array());
                }    
            }                
            if(!empty($folderPath))
            {    
                Core_Helper::createNewFolder($folderPath);                    
                // $fullPath = $this->createNewDocsByType($type, $folderPath, $newName);            
            }                    
            $filename = Core_Helper::GetCorrectName($folderPath.DIRECTORY_SEPARATOR.str_replace(' ', '_', $filename));
            if(move_uploaded_file($tmp, $folderPath.DIRECTORY_SEPARATOR.$filename))
            {
                $sUploadThumbnailDir = '';
                list($org_width, $org_height) = getimagesize($folderPath.DIRECTORY_SEPARATOR.$filename);
                if ($org_width && $org_height) {
                    $sFileDir = $folderPath.DIRECTORY_SEPARATOR.$filename;
                    Core_Common::createFolder($folderPath.DIRECTORY_SEPARATOR.'detail');
                    Core_Common::createFolder($folderPath.DIRECTORY_SEPARATOR.'thumbnail');
                    $sUploadDir = $folderPath.DIRECTORY_SEPARATOR.'detail'.DIRECTORY_SEPARATOR.$filename;
                    $sUploadThumbnailDir = $folderPath.DIRECTORY_SEPARATOR.'thumbnail'.DIRECTORY_SEPARATOR.$filename;

                    list($org_width, $org_height) = getimagesize($sFileDir);
                    Core_Image::resizeImage($sFileDir,$sUploadThumbnailDir,200,200);
                    if($org_height >=914){
                        FeedMongo::getInstance()->resizeImageFeed($sFileDir,$sUploadDir,0,914);
                    }elseif($org_width >= 1368){
                        FeedMongo::getInstance()->resizeImageFeed($sFileDir,$sUploadDir,1368,0);
                    }else{
                        FeedMongo::getInstance()->resizeImageFeed($sFileDir,$sUploadDir,$org_width,$org_height);
                    }
                }



                $fileId = File::getInstance()->insert($filename, $partPath, FILE, $parentId, $arrLogin['accountID'], $groupId, 0, $filename, 1);
                if($fileId > 0)
                {
                    //send notification
                    ShareDocs::getInstance()->sendNotification($iAction="uploadfile", $fileId, $partPath, $filename, $groupId, $arrLogin['accountID'], $arrLogin['nickName'], $arrLogin['avatar'], $actionDocs, $shareType="", $share_account_id = "", $share_account_name="");
                    //add action log
                    $this->addActionlog("uploadfile", $iType = 0, $iAccountID = $arrLogin['accountID'], $sAccountName = $arrLogin['nickName'], $sAccountAvatar = $arrLogin['avatar'], $groupId, $groupName, $parentId, $parentName, $fileId, $filename, $fileType=0, $filePath=$folderPath.DIRECTORY_SEPARATOR.$filename, $fileUrl=$this->getURLDocsFile($fileId, $folderPath, $filename, $arrLogin['accountID'], $groupId), $detail = array(), $sNote = '');
                    $arrRes = array();
                    $arrRes["_id"] = $fileId;
                    $arrRes["downloadURL"] = BASE_URL. DIRECTORY_SEPARATOR . "api" . DIRECTORY_SEPARATOR . "docs" . DIRECTORY_SEPARATOR . "download-docs?fileId=".$fileId;
                    //$folderPath.DIRECTORY_SEPARATOR.$filename;                    
                    $arrRes["path"] = $partPath;                    
                    $arrRes["group_id"] = $groupId;
                    $arrRes["name"] = $filename;
                    $arrRes["original_name"] = $filename;
                    $arrRes["owner"] = $arrLogin['accountID'];
                    $arrRes["parent"] = $parentId;
                    $arrRes["size"] = Core_Helper::GetFolderSize($folderPath.DIRECTORY_SEPARATOR.$filename);
                    $arrRes["type"] = 0;
                    $arrRes["updated"] = $fileId;
                    $arrRes["is_docs"] = 1;
                    $arrRes["ext"] = strtolower(pathinfo($folderPath.DIRECTORY_SEPARATOR.$filename, PATHINFO_EXTENSION));
                    if(Core_Helper::checkIsDocs($filename)){
                        $arrRes['URL'] = "/docs/editor/id/".$fileId;
                    }elseif (Core_Helper::checkIsImage($filename)) {
                        if($groupId == -1){
                            if(empty($sUploadThumbnailDir)) {
                                $arrRes['URL'] = ROOT_DOC_URL . DIRECTORY_SEPARATOR . "users" . DIRECTORY_SEPARATOR . $arrLogin['accountID'] . (!empty($partPath) ? $partPath : "") . DIRECTORY_SEPARATOR . $filename;
                            }else{
                                $arrRes['URL'] = ROOT_DOC_URL . DIRECTORY_SEPARATOR . "users" . DIRECTORY_SEPARATOR . $arrLogin['accountID'] . (!empty($partPath) ? $partPath : "") . DIRECTORY_SEPARATOR .'thumbnail'.DIRECTORY_SEPARATOR. $filename;
                            }
                        }
                        else{
                            $arrRes['URL'] = empty($sUploadThumbnailDir) ? PATH_FILES_URL.$partPath.DIRECTORY_SEPARATOR.$filename : PATH_FILES_URL.$partPath.DIRECTORY_SEPARATOR.'thumbnail'.DIRECTORY_SEPARATOR.$filename;
                        }                        
                    }
                    else{
                        $arrRes['URL'] = "";
                    }
                    $time = Date('d-M-Y H:i:s', time());
                    $arrRes['created'] = $time;
                    $arrRes['updated'] = $time;                                        
                    return Core_Server::setOutputData(false, 'Upload successfull', array(Zend_Json::encode($arrRes)));              
                }
                else{
                    unlink($folderPath.DIRECTORY_SEPARATOR.$filename);
                    return Core_Server::setOutputData(true, 'Upload failed', array());                              
                }
                
            }
        }    
        return Core_Server::setOutputData(true, 'Upload failed', array());
    }

    public static function addShareDocs($fileId, $accountID, $share_account, $share_type="account", $iPermission="view")    
    {        
        $fileInfo = array();
        $accInfo = array();
        $groupInfo = array();
        $parentInfo = array();
        $groupName = "";
        $parentName = "";
        global $globalConfig;
        global $actionDocs;
        $accOwnerInfo = AccountInfo::getInstance()->getAccountInfoByAccountID($accountID);                                    
        if(!$accOwnerInfo){
            return Core_Server::setOutputData(true, 'Account is not existed', array());            
        }           

        if($fileId > 0){            
            $fileInfo = File::getInstance()->selectOne($fileId);
            if($fileInfo){
                if($fileInfo['group_id'] > 0){
                    $groupInfo = Group::getInstance()->getGroupByID($fileInfo['group_id']);
                    $groupName = $groupInfo['group_name'];
                    $level = GroupMember::getInstance()->getMemberGroupLevelById($fileInfo['group_id'], $accountID);
                }
                if($fileInfo['owner'] == $accountID || in_array((int)$level, $globalConfig['ManagerDocsLevel'])){
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
                               return Core_Server::setOutputData(true, 'Group is not existed!', array());                               
                            }
                            //check permision user in group
                            /*$groupMember = GroupMember::getInstance()->getGroupMemberByAccountAndGroupId($accountID, $share_group_id);
                            if(empty($groupMember) && $group['admin_id'] != $accountID){
                                return Core_Server::setOutputData(true, 'Group permission denied', array());
                            }*/
                            //check permission existed
                            $arrShare = ShareDocs::getInstance()->checkDocsShareExisted($fileId, 'group', $share_group_id);
                            if(sizeof($arrShare)){
                                return Core_Server::setOutputData(true, 'This group had been shared', array("data"=>Zend_Json::encode($arrShare)));
                            }
                            $checkGroup = GroupMember::getInstance()->getGroupMemberByAccountAndGroupId($accountID, $share_group_id);
                            if(sizeof($checkGroup)){
                                $groupInfo = Group::getInstance()->getGroupByID($share_group_id);
                                $share_group_name = $groupInfo['group_name'];
                                $share_group_avatar = $groupInfo['image_url'];
                                // $fileUrl = "/docs/editor/id/".$fileId;
                                if($fileInfo['type'] == 0){
                                    $downloadURL = BASE_URL. DIRECTORY_SEPARATOR . "api" . DIRECTORY_SEPARATOR . "docs" . DIRECTORY_SEPARATOR . "download-docs?fileId=".$fileId;
                                    //self::getDownloadDocsPath($fileId);
                                    $fileUrl = self::getURLDocsFile($fileId, $fileInfo['path'], $fileInfo['name'], $accountID, $fileInfo['group_id']);
                                }else{
                                    $fileUrl = "";
                                    $downloadURL = "";
                                }
                                // new group share
                                $ishare = ShareDocs::getInstance()->insert($fileId, $fileInfo['name'], $fileInfo['type'], $fileInfo['path'], $fileUrl, $downloadURL, $iShareType='group', $iPermission, $fileInfo['group_id'], $groupName, $fileInfo['parent'], $parentName, $accountID, $accOwnerInfo['name'], $accOwnerInfo['avatar'], $accOwnerInfo['email'], $share_account_id = 0, $share_account_name = "", $share_account_avatar ="", $share_account_email = "", $sNote = 'share group', $iShareGroupID = $share_group_id, $iShareGroupName = $share_group_name, $iShareGroupAvatar = $share_group_avatar, $sStatus="allow");
                                if(!empty($ishare)){
                                    //send notification
                                    if($fileInfo['type'] == 0){//share file
                                        ShareDocs::getInstance()->sendNotification($iAction="sharefile", $fileId, $fileInfo['path'], $fileInfo['name'], $fileInfo['group_id'], $accountID, $accOwnerInfo['name'], $accOwnerInfo['avatar'], $actionDocs, $shareType="group", $share_account_id = $share_group_id, $share_account_name=$share_group_name);
                                    }    
                                    //add action log
                                    ShareDocs::getInstance()->addActionlog($iAction="sharefile", $accountID, $accOwnerInfo['name'], $accOwnerInfo['avatar'], $fileInfo['group_id'], $groupName, $fileInfo['parent'], $parentName, $fileId, $fileInfo['name'], $fileInfo['type'], $fileInfo['path'], $fileUrl, $detail = array(), $sNote = '', $share_account_id = 0, $share_account_name = "", $share_account_avatar = "",  $share_group_id, $share_group_name, $share_group_avatar);
                                    //return 
                                    $arrResult = array("share_id" => $ishare,"share_type" => "group", "share_group_id" => $share_group_id, "share_group_name" => $groupName, "share_group_avatar" => $share_group_avatar, "permission" => $iPermission);
                                    return Core_Server::setOutputData(false, 'OK', array("data" => Zend_Json::encode( $arrResult )));
                                }                            
                            }
                            else{
                                return Core_Server::setOutputData(true, 'Group permission denied', array());
                            }

                        }
                    }
                    else{
                        $share_account_id = $share_account;
                        //share to a user
                        if($share_account_id > 0){                                                                        
                            $accInfo = AccountInfo::getInstance()->getAccountInfoByAccountID($share_account_id);
                            if($accInfo){
                                //check permission existed
                                $arrShare = ShareDocs::getInstance()->checkDocsShareExisted($fileId, 'account', $share_account_id);                              
                                if(sizeof($arrShare)){
                                    return Core_Server::setOutputData(true, 'This user had been shared', array("data"=>Zend_Json::encode($arrShare)));
                                }
                                // $fileUrl = "/docs/editor/id/".$fileId;
                                if($fileInfo['type'] == 0){
                                    $downloadURL = BASE_URL. DIRECTORY_SEPARATOR . "api" . DIRECTORY_SEPARATOR . "docs" . DIRECTORY_SEPARATOR . "download-docs?fileId=".$fileId;
                                    //self::getDownloadDocsPath($fileId);
                                    $fileUrl = self::getURLDocsFile($fileId, $fileInfo['path'], $fileInfo['name'], $accountID, $fileInfo['group_id']);
                                }else{
                                    $fileUrl = "";
                                    $downloadURL = "";
                                }
                                $ishare = ShareDocs::getInstance()->insert($fileId, $fileInfo['name'], $fileInfo['type'], $fileInfo['path'], $fileUrl, $downloadURL, $iShareType='user', $iPermission, $fileInfo['group_id'], $groupName, $fileInfo['parent'], $parentName, $accountID, $accOwnerInfo['name'], $accOwnerInfo['avatar'], $accOwnerInfo['email'], $share_account_id, $accInfo['name'], $accInfo['avatar'], $accInfo['email'], $sNote = 'share user', $iShareGroupID = 0, $iShareGroupName = "", $iShareGroupAvatar = "", $sStatus="allow");
                                if(!empty($ishare)){
                                    //send notification
                                    if($fileInfo['type'] == 0){//share file
                                        ShareDocs::getInstance()->sendNotification($iAction="sharefile", $fileId, $fileInfo['path'], $fileInfo['name'], $fileInfo['group_id'], $accountID, $accOwnerInfo['name'], $accOwnerInfo['avatar'], $actionDocs, $shareType="user", $share_account_id = $share_account_id, $share_account_name=$accInfo['name']);
                                    }
                                    //add a action Docs log
                                    ShareDocs::getInstance()->addActionlog($iAction="sharefile", $accountID, $accOwnerInfo['name'], $accOwnerInfo['avatar'], $fileInfo['group_id'], $groupName, $fileInfo['parent'], $parentName, $fileId, $fileInfo['name'], $fileInfo['type'], $fileInfo['path'], $fileUrl, $detail = array(), $sNote = '', $share_account_id, $accInfo['name'], $accInfo['avatar'],  $share_group_id = 0, $share_group_name = "", $share_group_avatar = "");
                                    $arrResult = array("share_id" => $ishare, "share_account_id" => $share_account_id, "share_account_name" => $accInfo['name'], "share_account_email" => $accInfo['email'], "share_account_avatar" => $accInfo['avatar'], "permission" => $iPermission);
                                    return Core_Server::setOutputData(false, 'OK', array("data" => Zend_Json::encode( $arrResult )));
                                }
                            }
                            else{
                                return Core_Server::setOutputData(true, 'Account does not existed', array());
                            }    
                        }
                    }                    
                                        
                }
            }
        }
        return Core_Server::setOutputData(true, 'Pemission denied!', array());     
    }    
    
    public static function addActionlog($iAction, $iType, $iAccountID, $sAccountName, $sAccountAvatar, $groupId, $groupName, $parentId, $parentName, $fileId, $fileName, $fileType, $filePath, $fileUrl, $detail = array(), $sNote = '')
    {        
        if($groupId > 0)//action in a group
        {
            $actLogId = ActionDocsLog::getInstance()->insert($iAction, $iType = 0, $iAccountID, $sAccountName, $sAccountAvatar, $groupId, $groupName, $parentId, $parentName, $fileId, $fileName, $fileType, $filePath, $fileUrl, $detail, $sNote);
            $arrMembers = GroupMember::getInstance()->getGroupMemberID($groupId, 0, 1000);
            if(sizeof($arrMembers))
            {
                $detail["account_id"] =  $iAccountID;
                $detail["account_name"] = $sAccountName;                        
                $detail["account_avatar"] = $sAccountAvatar;
                foreach ($arrMembers['data'] as $member)
                {                    
                    if($member['account_id'] != $iAccountID)
                    {    
                        // action tac dong boi nguoi khac
                        ActionDocsLog::getInstance()->insert($iAction, $iType = 1, $member['account_id'], $member['name'], $member['avatar'], $groupId, $groupName, $parentId, $parentName, $fileId, $fileName, $fileType, $filePath, $fileUrl, $detail, $sNote);
                    }                        
                }    
            }    
        }
        else{
            $actLogId = ActionDocsLog::getInstance()->insert($iAction, $iType = 0, $iAccountID, $sAccountName, $sAccountAvatar, $groupId, $groupName, $parentId, $parentName, $fileId, $fileName, $fileType, $filePath, $fileUrl, $detail, $sNote);
        }            
        if(!empty($actLogId)) return true;
         return false;
    }                

    public static function getStorageGroup($accountID, $group_id = -1, &$limitStorage){
        $folderPath = "";
        if($group_id == -1){// my documents
            $folderPath = DOC_ROOT_PATH.DIRECTORY_SEPARATOR."users".DIRECTORY_SEPARATOR.$accountID;
            $limitStorage = Core_Helper::FileSizeConvert(FOLDER_USER_DOCS_SIZE_MAX);
        }elseif ($group_id > 0) {
            $folderPath = PATH_FILES_UPLOAD_DIR.DIRECTORY_SEPARATOR.$group_id;
            $limitStorage = Core_Helper::FileSizeConvert(FOLDER_USER_DOCS_SIZE_MAX);
        }else{
            $limitStorage = 'undefined';
            return 0;
        }
        return Core_Helper::GetFolderSize($folderPath, false);        
    }

    private function createNewDocsByType($type, $path, &$name) {
        $ext;
        switch ($type)
        {            
            case "spreadsheet":
                $ext = ".xlsx";
                $sampleName = "demo";                 
                $sampleFile = DOC_ROOT_PATH . DIRECTORY_SEPARATOR. "samples" . DIRECTORY_SEPARATOR . $sampleName . $ext;
                break;
            case "presentation":
                $ext = ".pptx";
                $sampleName = "demo";                 
                $sampleFile = DOC_ROOT_PATH . DIRECTORY_SEPARATOR. "samples" . DIRECTORY_SEPARATOR . $sampleName . $ext;
                break;
            case "document":
                $ext = ".docx";
                $sampleName = "demo";                 
                $sampleFile = DOC_ROOT_PATH . DIRECTORY_SEPARATOR. "samples" . DIRECTORY_SEPARATOR . $sampleName . $ext;
                break;
            case "note":
                $ext = ".txt";
                $sampleName = "demo";                 
                $sampleFile = DOC_ROOT_PATH . DIRECTORY_SEPARATOR. "samples" . DIRECTORY_SEPARATOR . $sampleName . $ext;                        
                break;
            default:
                $ext = ".docx";
                $sampleName = "demo";                 
                $sampleFile = DOC_ROOT_PATH . DIRECTORY_SEPARATOR. "samples" . DIRECTORY_SEPARATOR . $sampleName . $ext;
                // return "";
        }

        $newName = ($name == "")?"Untitled" . $ext : $name.$ext;        
        $name = Core_Helper::GetCorrectName($path . DIRECTORY_SEPARATOR . $newName);
        $newFile = $path . DIRECTORY_SEPARATOR . $name;        

        if(!@copy($sampleFile, $newFile))
        {
            // sendlog("Copy file error to ". getStoragePath($demoFilename), "logs/common.log");
            //Copy error!!!
            error_log("Copy file to new document error!");
            return "";
        }

        return $newFile;
    }
    private static function getURLDocsFile($fileId, $filePath, $fileName, $accountID, $groupId)
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
    }

    private static function getDownloadDocsPath($fileId){
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
}
