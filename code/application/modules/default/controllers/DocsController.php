<?php
/**
 * @author      :   Hien.nd
 * @name        :   DocsController
 * @version     :   20160704
 * @copyright   :   My company
 * @todo        :   News Controller 
 */

use Sabre\DAV\Client;
// include_once 'Sabre/DAV/autoload.php';
require 'vendor/autoload.php';


class DocsController extends Core_Controller_Action
{
    private $arrLogin;

    public function init() 
    {
        parent::init();
        
        //Get Controller
        $controller = $this->_request->getParam('controller');
        $this->view->controller = $controller;
        $this->arrLogin = $this->view->arrLogin;

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
    
    /**
     * Default action
     */
    public function indexAction()
    {
        
        /*$type = $this->_getParam('type');
        if(!empty($type)){

        }*/

        $iStart = $this->_request->getParam('page', 0);
        $iLimit = $this->_request->getParam('limit', 10);
        
        
        $iGroupId = $this->_request->getParam('g', 0);
        $iParent = $this->_request->getParam('p', 0);

        $arrFiles = array();
        $arrTmps = array();
        if($iGroupId > 0) {

            //get group
            $arrGroup = Group::getInstance()->getGroupByID($iGroupId);

            if (empty($arrGroup)) {
                error_log("error= Group is not exists.");
                $this->_redirect("/feed");
                exit;                
            }

            //check permision user in group
            $groupMember = GroupMember::getInstance()->getGroupMemberByAccountAndGroupId($this->arrLogin['accountID'], $iGroupId);

            if (empty($groupMember) && $arrGroup['admin_id'] != $this->arrLogin['accountID']) {
                error_log("error= Permission deny.");
                $this->_redirect("/feed");
                exit;                
            }
            if($iParent ==  0){
                $arrTmps = File::getInstance()->selectByGroupId($iStart, $iLimit, $iGroupId);                
            }
            else{
                $arrTmps = File::getInstance()->selectByParent($iStart, $iLimit, $iParent);
            }    

        }else{// get files, folders from My Documents 
            $arrTmps = File::getInstance()->selectOwnerByParent($iStart, $iLimit, $this->arrLogin['accountID'], $iParent);
        }


        if($arrTmps['total'] > 0){
            foreach ($arrTmps['data'] as $file){
                $arr = $file;
                if($file['type'] == FILE){
                    $processFileName = Core_Common::getExtensionAndFileName( $file['name']);
                    $ext = $processFileName['extent'];
                    $arr['ext'] = strtolower($ext);
                }
                $arrFiles[] = $arr;
            }
        }
        //Assign view
        $this->view->listFiles  = $arrFiles;
        
    }

    /**
        Create anew docs
        input:
            type = spreadsheet, presentation, document
            g[roup] = 0
            p[arentId] = 0
    **/
    public function createDocsAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);        

        $groupId = $this->_getParam('g',0);
        $parentId = $this->_getParam('p',0);
        $type = $this->_getParam('type');
        $newName = $this->_getParam('name', '');
        $fullPath = "";
        $partPath = "";
        $folderPath = "";
        $error = 0;
        if(!empty($type)){
            if($groupId == -1)
            {
                $folderPath = DOC_ROOT_PATH . DIRECTORY_SEPARATOR . "users" . DIRECTORY_SEPARATOR . $this->arrLogin["accountID"];
                // $this->createNewFolder($folderpath);
            }
            else
            {
                // check group existed
                $group = Group::getInstance()->getGroupByID($groupId);
                $arrError = array();
                if(empty($group)) {
                   $error = 1;
                }

                //check permision user in group
                $groupMember = GroupMember::getInstance()->getGroupMemberByAccountAndGroupId($this->arrLogin['accountID'], $groupId);
                if(empty($groupMember) && $group['admin_id'] != $this->arrLogin['accountID']){
                    $error = 2;
                }

                if($parentId > 0){
                    $arrFile = File::getInstance()->selectOne($parentId);

                    if(empty($arrFile)){
                        $error = 3;
                    }

                    //check owner
                    if($arrFile['owner'] != $this->arrLogin['accountID']){
                        $error = 4;
                    }
                    $partPath = $arrFile['path'];
                    $folderPath = PATH_FILES_UPLOAD_DIR . $arrFile['path'];
                    // $this->createNewFolder($folderpath);    
                    //create folder in server
                    // Core_Common::createFolder(PATH_FILES_UPLOAD_DIR .'/'.$group['group_id']);
                }
                else { //file not found in folder
                    $error = 5;
                }    
            }    
            if($error > 0)
            {
                error_log("error=".$error);
                $this->_redirect("/feed");
                exit;
            }
            if(!empty($folderPath))
            {    
                $this->createNewFolder($folderPath);                    
                $fullPath = $this->createNewDocsByType($type, $folderPath, $newName);            
            }    
        }
        /*if(empty($fullPath) || $fullPath == "")
        {
            $this->_redirect("/feed");
            exit();
        }*/                 
        if (!empty($fullPath) && $fullPath != "" && file_exists($fullPath)) {
            $fileId = File::getInstance()->insert($newName, $partPath, FILE, $parentId, $this->arrLogin['accountID'], $groupId, 0, $newName, 1);
            
            $this->_redirect("/docs/editor/id/".$fileId);
            exit();            
        }

        $this->_redirect("/feed");
        exit;
    }    
    public function permissiondeniedAction()
    {
    } 
    /**
     * view action
     */
    
    public function editorAction()
    {
        // $this->_helper->layout->disableLayout();
        // $this->_helper->viewRenderer->setNoRender(TRUE);            
        $fileId = $this->_getParam('id');
        $File = File::getInstance()->selectOne($fileId);
        global $globalConfig;

        if(empty($File))
        {    
            // $arrError = array('error'=>true, 'message'=>'file not found');
            //redirect
            $this->_redirect("/feed");
            exit();
        }    
        else
        {
            if(!isset($File['is_public']) || $File['is_public'] == 0){
                $checked = array();
                $levelMemberGroup = GroupMember::getInstance()->getMemberGroupLevelById($File['group_id'], $this->arrLogin["accountID"]);
    // error_log("level:".$levelMemberGroup);            
                //check permission
                if($File['owner'] != $this->arrLogin["accountID"] && !in_array((int)$levelMemberGroup, $globalConfig['ManagerDocsLevel'])){                
                    $ApiGroup = new ApiGroup();
                    $arrayRes = $ApiGroup->getListGroupIdByMemberId();
                    $arrGroupId = array();
                    if($arrayRes["error"] == false) $arrGroupId = $arrayRes["body"]["data"];
                    if($arrGroupId){
                        if($File['group_id'] > 0 && in_array($File['group_id'], $arrGroupId))
                            $checked = true;
                        else{
                            $checked = ShareDocs::getInstance()->getDocsPermissionByAccountId($fileId, $this->arrLogin["accountID"], $arrGroupId);        
                        }
                    }

                    if(!$checked){
                        $this->view->fileId = $fileId;
                        $this->_helper->viewRenderer('permissiondenied');
                        return;
                    }
                    else{
                        $this->_helper->layout->setLayout('editor');        
                    }                    
                }else{
                    $this->_helper->layout->setLayout('editor');
                }
            }
            else $this->_helper->layout->setLayout('editor');

            $originalName = empty($File['original_name']) ? $File['name'] : $File['original_name'];
            if($File['group_id'] == -1)//My document onlyoffice
            {
                $fullURL = ROOT_DOC_URL . DIRECTORY_SEPARATOR . "users". DIRECTORY_SEPARATOR . $File['owner'] . (!empty($File['path'])?DIRECTORY_SEPARATOR . $File['path']:"") . DIRECTORY_SEPARATOR . $File['name'];
                $fullPath = DOC_ROOT_PATH . DIRECTORY_SEPARATOR . "users". DIRECTORY_SEPARATOR . $File['owner'] . (!empty($File['path'])?DIRECTORY_SEPARATOR . $File['path']:"") . DIRECTORY_SEPARATOR .$File['name'];
            }
            else{//document tạo trong 1 group
                $fullURL = PATH_FILES_URL.$File['path'].DIRECTORY_SEPARATOR.$File['name'];
                $fullPath = PATH_FILES_UPLOAD_DIR.$File['path'].'/'.$File['name'];
            }
            
            if (file_exists($fullPath)) {
                $accOwnerInfo = AccountInfo::getInstance()->getAccountInfoByAccountID($File['owner']);
                if($accOwnerInfo){
                    $arrShareDocs = ShareDocs::getInstance()->select($fileId, 0, 100);
                    if($arrShareDocs['total'] > 0){
                        foreach ($arrShareDocs['data'] as $share) {
                            if($share['share_type'] == 'user'){
                                $arrtmp['user'] = $share['share_account_name'];                                    
                            }else{
                                $arrtmp['user'] = $share['share_group_name'];
                            }
                            if($share['permission'] == 'canedit'){
                                $arrtmp['permissions'] = 'Full Access';        
                            }else{
                                $arrtmp['permissions'] = 'Read Only';
                            }
                            $arrSharingSettings[] = $arrtmp;
                        }
                        $this->view->sharingSettings = Zend_Json::encode($arrSharingSettings);
                    }
                    else $this->view->sharingSettings = '[]';                    
                    $this->view->author = $accOwnerInfo['name'];
// error_log($File['created']);                    
                    $this->view->created = date('Y-m-d - H:i:s', $File['created']->sec);
                    if($File['parent'] > 0){
                        $parentInfo = File::getInstance()->selectOne($File['parent']);
                        $this->view->folder = $parentInfo['name'];
                    }
                    elseif($File['group_id'] > 0){
                        $groupInfo = Group::getInstance()->getGroupByID($File['group_id']);
                        $this->view->folder = $groupInfo['group_name'];
                    }
                    else $this->view->folder = 'Drive Document'; 
                }
                $token = Token::getInstance()->generateToken("docs", $this->arrLogin["accountID"], $this->arrLogin["username"], $this->arrLogin["avatar"], $this->arrLogin['ps'], Core_Utility::getAltIp(), DOC_SERV_IP, 3600);    
                $this->view->fileId = $fileId;
                $this->view->filename = $fullPath;
                $this->view->originalName = $originalName;
                $this->view->viewType = "desktop"; //embedded
                $this->view->documentType = Core_Helper::getDocumentType($fullPath);
                // $this->view->fullURL = $fullURL;
                $this->view->fullURL = BASE_URL. "/api/docs/download-docs?fileId=".$fileId."&token=".$token;
                //check key existed, if not generate a new key
                if(!isset($File['key']) || empty($File['key'])){
                    $key = $this->generateDocEditorKey($fileId, $fullURL);
                    //update new key
                    // File::getInstance->update(array('_id' => int($fileId)),array('key'=>$key));
                }else{
                    $key = $File['key'];
                }
                $this->view->key = $key;
                $this->view->nickName = $this->arrLogin['nickName'];
                $this->view->accountID = $this->arrLogin['accountID'];
                // $this->view->vkey = Core_Helper::getDocEditorValidateKey($fullURL, $this->servConvGetKey(), $this->servConvGetSKey());
                $this->view->callbackUrl = $this->getCallbackUrl($fileId, $this->arrLogin["accountID"], $this->arrLogin["username"], $this->arrLogin["avatar"], $this->arrLogin['ps']);
                $this->view->onBack = $this->_getParam('action')=="embeded"?"undefined":"onBack";
                if($this->_getParam('action')!="view" && MODE != "view" && in_array(strtolower('.' . pathinfo($fullPath, PATHINFO_EXTENSION)), explode(',' , DOC_SERV_EDITED)))
                {
                    if($File['owner'] == $this->arrLogin["accountID"] || in_array((int)$levelMemberGroup, $globalConfig['ManagerDocsLevel'])){
                        $this->view->mode = "edit";
                        $this->view->permissionedit = "true";
                        $this->view->permissiondownload = "true";
                    }
                    else{
                        if(isset($checked['permission']) && $checked['permission'] == 'canedit'){
                            $this->view->mode = "edit";
                            $this->view->permissionedit = "true";
                            $this->view->permissiondownload = "true";
                        }
                        else{
                            $this->view->mode = "view";        
                            $this->view->permissionedit = "false";
                            $this->view->permissiondownload = "true";   
                        }
                    }
                }
                else
                {                    
                    $this->view->mode = "view";
                    $this->view->permissionedit = "false";
                    $this->view->permissiondownload = "true";
                }                
            }
            else
            {
                $this->_redirect("/docs");
                exit();
            }                

        }
        echo $this->_helper->layout->render();
        exit;
    }

    /**
     * view action
     */
    
    public function editorCacheAction()
    {
        $fileId = $this->_getParam('id');
        $sKey = $this->_getParam('skey');
        $File = File::getInstance()->selectOne($fileId);
        global $globalConfig;
        //check sKey is existed
        $historyDocs = HistoryDocs::getInstance()->selectOneByKey($sKey);

        if(empty($File) || empty($sKey) || empty($historyDocs))
        {    
            $this->_redirect("/feed");
            exit();
        }    
        else
        {
            if(!isset($File['is_public']) || $File['is_public'] == 0){
                $levelMemberGroup = GroupMember::getInstance()->getMemberGroupLevelById($File['group_id'], $this->arrLogin["accountID"]);
                //check permission
                if($File['owner'] != $this->arrLogin["accountID"] || !in_array((int)$levelMemberGroup, $globalConfig['ManagerDocsLevel'])){
                    $checked = false;
                    $ApiGroup = new ApiGroup();
                    $arrayRes = $ApiGroup->getListGroupIdByMemberId();
                    $arrGroupId = array();
                    if($arrayRes["error"] == false) $arrGroupId = $arrayRes["body"]["data"];
                    if($arrGroupId){
                        if($File['group_id'] > 0 && in_array($File['group_id'], $arrGroupId))
                            $checked = true;
                        else{
                            $checked = ShareDocs::getInstance()->getDocsPermissionByAccountId($fileId, $this->arrLogin["accountID"], $arrGroupId);        
                        }
                    }

                    if(!$checked){
                        $this->_helper->viewRenderer('permissiondenied');
                        return;
                    }
                    else{
                        $this->_helper->layout->setLayout('editor-cache');        
                    }                    
                }else
                    $this->_helper->layout->setLayout('editor-cache');
            }else
                $this->_helper->layout->setLayout('editor-cache');

            $originalName = empty($File['original_name']) ? $File['name'] : $File['original_name'];
            if($File['group_id'] == -1)//My document onlyoffice
            {
                $fullURL = ROOT_DOC_URL . DIRECTORY_SEPARATOR . "users". DIRECTORY_SEPARATOR . $File['owner'] . (!empty($File['path'])?DIRECTORY_SEPARATOR . $File['path']:"") . DIRECTORY_SEPARATOR . $File['name'];
                $fullPath = DOC_ROOT_PATH . DIRECTORY_SEPARATOR . "users". DIRECTORY_SEPARATOR . $File['owner'] . (!empty($File['path'])?DIRECTORY_SEPARATOR . $File['path']:"") . DIRECTORY_SEPARATOR .$File['name'];
            }
            else{//document tạo trong 1 group
                $fullURL = PATH_FILES_URL.$File['path'].DIRECTORY_SEPARATOR.$File['name'];
                $fullPath = PATH_FILES_UPLOAD_DIR.$File['path'].'/'.$File['name'];
            }
            
            if (file_exists($fullPath)) {
                $this->view->fileId = $fileId;
                $this->view->filename = $fullPath;
                $this->view->originalName = $originalName;
                $this->view->viewType = "desktop"; //embedded
                $this->view->documentType = Core_Helper::getDocumentType($fullPath);
                $this->view->fullURL = $fullURL;                                
                $this->view->key = $sKey;
                $this->view->nickName = $this->arrLogin['nickName'];
                $this->view->accountID = $this->arrLogin['accountID'];
                // $this->view->vkey = Core_Helper::getDocEditorValidateKey($fullURL, $this->servConvGetKey(), $this->servConvGetSKey());
                $this->view->callbackUrl = BASE_URL."/api/docs/editor-cache-ajax";
                $this->view->onBack = $this->_getParam('action')=="embeded"?"undefined":"onBack";
                $this->view->mode = "view";               
            }
            else
            {
                $this->_redirect("/docs");
                exit();
            }                

        }
        echo $this->_helper->layout->render();
        exit;
    }


    /**
     * view action
     */
    
    public function editorWebdavAction()
    {        
        $this->_helper->layout->setLayout('editor-webdav');        
        // $fileName = $this->_getParam('filename');
        $fileURL = $this->_getParam('fileURL');


        if(!empty($fileURL))
        {    
            $res = ApiWebdav::getWebdavContentFile($fileURL, $this->arrLogin["username"], base64_decode($this->arrLogin['ps']));            
            if($res['statusCode'] != 200)//not existed
            {
                //redirect
                $this->_redirect("/docs/webdav");
                exit();
            }            
                        
            $fileName = basename($fileURL);

            if(!Core_Helper::checkIsDocs($fileName))
            {
                $this->_redirect("/docs/webdav");
                exit();        
            }
            else{
                $token = Token::getInstance()->generateToken("webdav", $this->arrLogin["accountID"], $this->arrLogin["username"], $this->arrLogin["avatar"], $this->arrLogin['ps'], Core_Utility::getAltIp(), DOC_SERV_IP, 3600);    
                $this->view->originalName = $fileName;
                $this->view->fileName = $fileName;
                $this->view->viewType = "desktop"; //embedded
                $this->view->documentType = Core_Helper::getDocumentType($fileName);
                $this->view->fullURL = BASE_URL. "/api/webdav/index?method=downloadWebdav&fileURL=".$fileURL."&token=".$token;
                $this->view->key = Core_Helper::getDocEditorKey($fileURL);
                $this->view->vkey = Core_Helper::getDocEditorValidateKey($fileURL, $this->servConvGetKey(), $this->servConvGetSKey());
                $this->view->callbackUrl = $this->getAjaxURrlWebdav('track', $fileURL, $token);
                $this->view->saveUrl = $this->getAjaxURrlWebdav('save', $fileURL, $token);
                $this->view->embedUrl = $this->getAjaxURrlWebdav('embed', $fileURL, $token);
                $this->view->shareUrl = $this->getAjaxURrlWebdav('share', $fileURL, $token);
                $this->view->onBack = $this->_getParam('action')=="embeded"?"undefined":"onBack";
                $this->view->nickName = $this->arrLogin['nickName'];
                $this->view->accountID = $this->arrLogin['accountID'];
                if($this->_getParam('action')!="view" && MODE != "view" && in_array(strtolower('.' . pathinfo($fileName, PATHINFO_EXTENSION)), explode(',' , DOC_SERV_EDITED)))
                {
                    /*if($File['owner'] == $this->arrLogin["accountID"]){
                        $this->view->mode = "edit";
                        $this->view->permissionedit = "true";
                        $this->view->permissiondownload = "true";
                    }
                    else{
                        if(isset($checked['permission']) && $checked['permission'] == 'canedit'){
                            $this->view->mode = "edit";
                            $this->view->permissionedit = "true";
                            $this->view->permissiondownload = "true";
                        }
                        else{
                            $this->view->mode = "view";        
                            $this->view->permissionedit = "false";
                            $this->view->permissiondownload = "true";   
                        }
                    }*/
                    $this->view->mode = "view";
                    $this->view->permissionedit = "false";
                    $this->view->permissiondownload = "true";

                }
                else
                {                    
                    $this->view->mode = "view";
                    $this->view->permissionedit = "false";
                    $this->view->permissiondownload = "true";
                }

                if($this->_getParam('action')!="view" && MODE != "view" && in_array(strtolower('.' . pathinfo($fileName, PATHINFO_EXTENSION)), explode(',' , DOC_SERV_EDITED)))
                {
                    $this->view->mode = "edit";
                }
                else
                {
                    $this->view->mode = "view";
                }    

            }  

        }    
        else
        {
            //redirect
                $this->_redirect("/docs/webdav");
                exit();
        }            
        echo $this->_helper->layout->render();
        exit;
    }


    public function uploadAjaxAction()
    {          
        $response_array;
        @header( 'Content-Type: application/json; charset==utf-8');
        @header( 'X-Robots-Tag: noindex' );
        @header( 'X-Content-Type-Options: nosniff' );

        Core_Helper::nocache_headers();                         
        $response_array = $this->upload();
        $response_array['status'] = $response_array['error'] != NULL ? 'error' : 'success';        
        die(Zend_Json::encode($response_array));        
    }

    /*public function downloadWebdavAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $fileContent = "";
        $numberBytes = 0;
        $fileURL = $this->_getParam('fileURL');        
        $token = $this->_getParam('token');
        $fileName = "";
        if(!empty($fileURL) && !empty($token))
        {                
            $arrRes = ApiToken::checkDocsToken($token);
            if(sizeof($arrRes['body']) > 0)
            {
                $res = ApiWebdav::getWebdavContentFile($fileURL, $arrRes["body"]["username"], $arrRes["body"]["ps"]);            
                if($res['statusCode'] == 200 && isset($res["headers"]["content-length"][0])  && $res["headers"]["content-length"][0] > 0 )//not existed
                {                                                        
                    $fileName = basename($fileURL);
                    if(Core_Helper::checkIsDocs($fileName))
                    {
                        $numberBytes = $res["headers"]["content-length"][0];
                        $fileContent = $res['body'];                            
                    }                                
                }
            }                                
        }
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.$fileName.'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        // $numberBytes = strlen($attachment['content']);
        header('Content-Length: ' . $numberBytes);
        echo ($fileContent);
        exit;            
    }   */ 

    public function getWebdavFileAction()
    {
        $response_array;
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        // The user credentials I will use to login to the WebDav host
        $credentials = array(
                $this->arrLogin["username"],
                base64_decode($this->arrLogin['ps'])
        );
        $remoteUrl = 'http://soft.gnt-global.com/';
        // $subfolder = 'SHARE/CIO_OFFICE/New%20folder%20(2)/';
        $subfolder = "";
        // $curl_response_res = $this -> getWebdavContent1($remoteUrl.$subfolder, $this->arrLogin["username"], base64_decode($this->arrLogin['ps']));
        $curl_response_res = $this -> getWebdavContent($remoteUrl.$subfolder, $credentials);
        $curl_response_res = str_replace(array("\n", "\r", "\t"), '', $curl_response_res);
        $curl_response_res = trim(str_replace('"', "'", $curl_response_res));
        $simpleXml = simplexml_load_string($curl_response_res);
        $response_array['status'] = Zend_Json::encode($curl_response_res);
        $response_array['error'] = 'ok';        
        die(Zend_Json::encode($response_array));
    }

    public function uploadWebdavAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        // The user credentials I will use to login to the WebDav host
        $credentials = array(
                $this->arrLogin["username"],
                base64_decode($this->arrLogin['ps'])
        );
         
        // Prepare the file we are going to upload
        $filename = 'dahi.txt';
        $filepath = '/tmp/'.$filename;
        $filesize = filesize($filepath);
        $fh = fopen($filepath, 'r');
         
        // The URL where we will upload to, this should be the exact path where the file
        // is going to be placed
        $remoteUrl = 'http://webfile.gnt-global.com/fileserver1/';
        $subfolder = 'SHARE/CIO_OFFICE/New%20folder%20(2)/'; 
        // Initialize cURL and set the options required for the upload. We use the remote
        // path we specified together with the filename. This will be the result of the
        // upload.
        $ch = curl_init($remoteUrl . $subfolder . $filename);
         
        // I'm setting each option individually so it's easier to debug them when
        // something goes wrong. When your configuration is done and working well
        // you can choose to use curl_setopt_array() instead.
         
        // Set the authentication mode and login credentials
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_USERPWD, implode(':', $credentials));        

        // Define that we are going to upload a file, by setting CURLOPT_PUT we are
        // forced to set CURLOPT_INFILE and CURLOPT_INFILESIZE as well.
        curl_setopt($ch, CURLOPT_PUT, true);
        curl_setopt($ch, CURLOPT_INFILE, $fh);
        curl_setopt($ch, CURLOPT_INFILESIZE, $filesize);
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, TRUE); // --data-binary

        // Execute the request, upload the file
        $curl_response_res = curl_exec($ch);
         
        // Close the file handle
        fclose($fh);
        $response_array['status'] = $curl_response_res;
        $response_array['error'] = 'ok';        
        die(Zend_Json::encode($response_array));

    }

    private function servConvGetKey() {
        if (defined('ServiceConverterTenantId'))
            return ServiceConverterTenantId;
        return "OnlyOfficePortal";
    }    
    
    private function servConvGetSKey() {
        if (defined('ServiceConverterKey'))
            return ServiceConverterKey;
        return "ONLYOFFICEPortalDocs";
    }

    private function getCallbackUrl($fileId, $accountID, $username, $avatar, $password) {
        $clientIp = Core_Helper::getClientIp();
        return rtrim(URL, '/'). "/api/docs/editor-ajax"
                    . "?type=track&userAddress=" . $clientIp
                    . "&id=" . urlencode($fileId)
                    . "&token=".Token::getInstance()->generateToken("docs", $accountID, $username, $avatar, $password, $clientIp, DOC_SERV_IP, 3600);
    }

    private function getAjaxURrlWebdav($actionType='track', $fileURL, $token) {
        return rtrim(URL, '/'). '/api/docs/' 
                    . "editor-ajax-webdav"
                    . "?type=".$actionType."&userAddress=" . Core_Helper::getClientIp()
                    . "&fileURL=" . urlencode($fileURL)
                    . "&token=". $token;
    }

    private static function getFileExts() {
        return array_merge(explode(',', DOC_SERV_VIEWD), explode(',', DOC_SERV_EDITED), explode(',', DOC_SERV_CONVERT));
    }

    private static function getWebdavContent1($url, $user, $pass)
    {
        $settings = array(
            'baseUri' => $url,
            'userName' => $user,
            'password' => $pass,    
            'depth'     => 1        
        );
        $client = new Client($settings);

        $collection = $client->propfind($url, array(
            '{DAV:}displayname',
            '{DAV:}getcontentlength',
            '{DAV:}iscollection',
            '{DAV:}creationdate',
        ), 1);

        return $collection;
    }

    private static function getWebdavContent($url, $credentials)
    {
        $headers = array(
            'Content-Type: application/xml; charset=utf-8',
            'Depth: 0',
            'Prefer: return-minimal'
        );
        $body = '<?xml version="1.0" encoding="UTF-8"?>
                <d:propfind xmlns:d="DAV:">
                  <d:prop>
                    <d:displayname/>
                    <d:getcontentlength/>
                    <d:iscollection/>
                    <d:creationdate/>
                  </d:prop>
                </d:propfind>';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_USERPWD, implode(':', $credentials));
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PROPFIND');
        // curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        $curl_response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ($httpcode>=200 && $httpcode<300) ? $curl_response : false;
    }

    private static function createNewDocsByType($type, $path, &$name) {
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

    private static function createNewFolder($pathFolder = DOC_ROOT_PATH)
    {
        if(!empty($pathFolder))
        {    
            if (!file_exists($pathFolder)) {
                    mkdir($pathFolder, 0777, true);                
            }
            return true;            
        }
        return false;    
    }

    public function generateDocEditorKey($fileId, $fileUrl)
    {
        $sKey = "";
        $firstHistory = array();
        $HistoryDocs = HistoryDocs::getInstance()->selectAll($fileId);
        if(!isset($HistoryDocs['total']) || $HistoryDocs['total'] == 0)
            $sKey = Core_Helper::getDocEditorKey($fileId."_".APP_ENV . "_" . $this->replaceSpecialCharDocsKey(basename($fileUrl)));
        else{
            foreach ($HistoryDocs['data'] as $hid => $history) {
                $firstHistory = $history;
                break;    
            }
// error_log('array='.Zend_Json::encode($firstHistory));     
            $sKey = Core_Helper::getDocEditorKey($firstHistory['_id'] . "_" . $fileId . "_" . APP_ENV . "_" . $this->replaceSpecialCharDocsKey(basename($fileUrl)));
        }    
        // update new key for file
        $query = array();
        $query['_id'] = (int)$fileId;
        $update = array();
        $update['key'] = $sKey;
        File::getInstance()->update($query, $update);
        unset($quey, $update);
        // end update
        return $sKey;
    }

    private static function replaceSpecialCharDocsKey($key)
    {
        $arrFind = array(' ','+', '(', ')');
        $arrReplace = array('_','-', '.', '');
        return str_replace($arrFind, $arrReplace, $key);
    }            
}

