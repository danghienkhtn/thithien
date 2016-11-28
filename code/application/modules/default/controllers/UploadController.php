<?php
/**
 * @author      :   HoaiTN
 * @name        :   UploadController
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Upload Controller 
 */
class UploadController extends Core_Controller_Action
{

    private $arrLogin;
    private $allowFiles;
    public function init()
    {
        parent::init();
        $this->arrLogin = $this->view->arrLogin;
        global $globalConfig;
        $this->allowFiles = $globalConfig['allow_file'];
    }

    public function uploadFileAbsenceAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        if(!isset($_FILES['file'])) {
            $arrError = array('error' => true, 'message' => 'file not found');
            echo Zend_Json::encode($arrError);
            exit;
        }

        $filename = $_FILES['file']['name'];
        $processFileName = Core_Common::getExtensionAndFileName($filename);
        $ext = $processFileName['extent'];

        if(in_array($ext,$this->allowFiles))
        {
            Absence::uploadFileAbsence($_FILES['file'],$originalName,$fileAttach);
            $arrError = array('error'=>false,'message'=>'success', 'file_name' => $fileAttach, 'original_name' => $originalName);
        }
        else
            $arrError = array('error'=>true,'message'=>'support Only '.implode(',',$this->allowFiles));

        echo Zend_Json::encode($arrError);
        exit;
    }

    public function checkFileAction()
    {

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        if(!isset($_FILES['file'])) {
            $arrError = array('error' => true, 'message' => 'file not found');
            echo Zend_Json::encode($arrError);
            exit;
        }

        $filename = $_FILES['file']['name'];
        $processFileName = Core_Common::getExtensionAndFileName($filename);
        $ext = $processFileName['extent'];

        if(in_array($ext,$this->allowFiles))
            $arrError = array('error'=>false,'message'=>'file\'s type is allowed');
        else
            $arrError = array('error'=>true,'message'=>'support Only '.implode(',',$this->allowFiles));

        echo Zend_Json::encode($arrError);
        exit;
    }

    public function downloadAttachmentAction()
    {

        $attachment = $this->_getParam('attachment','');
        $id = $this->_getParam('id','');
        $id = urldecode($id);
        $attachment = trim(urldecode($attachment));

        $email = Exchange::getInstance()->getMaillDetail($id,$this->arrLogin['email'],base64_decode($this->arrLogin['ps']));

        if(!isset($email['arrAttachment'][$attachment]))
            $this->redirect(BASE_URL);

        $attachment = $email['arrAttachment'][$attachment];

        $processFileName = Core_Common::getExtensionAndFileName( $attachment['name']);
        $ext = $processFileName['extent'];
        $fileName = (strlen($attachment['name'])) > 200 ? Core_Common::SubFullStrings($attachment['name'],0,200) : $attachment['name'];
        $path = PATH_MAIL_UPLOAD_DIR.'/'.$attachment['name'];
        if(file_exists($path))
            unlink($path);

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.$fileName.'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        $numberBytes = strlen($attachment['content']);
        header('Content-Length: ' . $numberBytes);
        echo ($attachment['content']);
        exit;

    }

    public function downloadFeedPhotoAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $photoFeedId = $this->_getParam('photoId');
        $photoFeedId = intval($photoFeedId);
        $size = $this->_getParam('size');
        $photoFeed = PhotoFeed::getInstance()->getPhotoFeedById($photoFeedId);
        if(empty($photoFeed))
            $arrError = array('error'=>true, 'message'=>'photo not found');
        else
        {
            $originalName = empty($photoFeed['original_name']) ? $photoFeed['image_url'] : $photoFeed['original_name'];
            $fullURL = PATH_IMAGES_URL.'/'.$size.'/'.$photoFeed['image_url'];
            $fullPath = PATH_IMAGES_UPLOAD_DIR.'/'.$size.'/'.$photoFeed['image_url'];

            if (file_exists($fullPath)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="'.$originalName.'"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($fullPath));
                readfile($fullPath);
                exit;
                $arrError = array('error'=>false, 'message'=>'success');
            }
        }


        echo Zend_Json::encode($arrError);
        exit;
    }

    public function downloadFeedDocumentAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $fileId = $this->_getParam('id');
        $fileId = intval($fileId);
        $File = File::getInstance()->selectOne($fileId);


        $arrError = array('error'=>true, 'message'=>'file not found');
        if(empty($File))
            $arrError = array('error'=>true, 'message'=>'file not found');
        else
        {
            $originalName = empty($File['original_name']) ? $File['name'] : $File['original_name'];
            if(empty($File['path'])) {
                $fullURL = self::getDownloadDocsPath($fileId);
                $fullPath = self::getDownloadDocsPath($fileId);
            }else{
                $fullURL = PATH_FILES_URL . $File['path'] . '/' . $File['name'];
                $fullPath = PATH_FILES_UPLOAD_DIR . $File['path'] . '/' . $File['name'];

            }
//            Core_Common::var_dump($fullPath);

            if (file_exists($fullPath)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="'.$originalName.'"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($fullPath));
                readfile($fullPath);
                exit;
                $arrError = array('error'=>false, 'message'=>'success');
            }

        }


        echo Zend_Json::encode($arrError);
        exit;
    }

    public static function getDownloadDocsPath($fileId){
        $fileInfo = File::getInstance()->selectOne($fileId);
        $downloadPath = "";
        if($fileInfo)
        {
            if($fileInfo['type'] == 0){
                if($fileInfo['group_id'] > 0){
                    $downloadPath = PATH_FILES_UPLOAD_DIR . (!empty($fileInfo['path'])? $fileInfo['path']:"") . $fileInfo['name'];
                }
                else{
                    $downloadPath = DOC_ROOT_PATH . DIRECTORY_SEPARATOR . "users". DIRECTORY_SEPARATOR . $fileInfo['owner'] . (!empty($fileInfo['path'])? $fileInfo['path']:"") . DIRECTORY_SEPARATOR .$fileInfo['name'];
                }
            }
        }
        return $downloadPath;
    }

    public function downloadAbsenceFileAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $absenceRequestId = $this->_getParam('id');
        $absenceRequest = AbsenceRequest::getInstance()->getAbsenceRequestByID($absenceRequestId);
        if(empty($absenceRequest))
            $arrError = array('error'=>true, 'message'=>'request not found');
        else
        {
            $originalName = empty($absenceRequest['original_name']) ? $absenceRequest['file_attach'] : $absenceRequest['original_name'];
            $fullURL = PATH_ABSENCES_URL.'/'.$absenceRequest['file_attach'];
            $fullPath = PATH_ABSENCES_UPLOAD_DIR.'/'.$absenceRequest['file_attach'];
            file_put_contents($fullPath,file_get_contents($fullURL));

            if (file_exists($fullPath)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="'.$originalName.'"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($fullPath));
                readfile($fullPath);
                $arrError = array('error'=>false, 'message'=>'success');
            }
        }


        echo Zend_Json::encode($arrError);
        exit;
    }

    public function deleteFileAction()
    {
        $result = false;
        $this->_helper->layout()->disableLayout();
        //Disable render
        $this->_helper->viewRenderer->setNoRender();
        $fileName = $this->_getParam('name','');
        $folder = $this->_getParam('folder','');
        $result = Core_Common::deleteFile($fileName,$folder);
        echo Zend_Json::encode($result);
        exit();
    }

    public function uploadAttachmentsAction()
    {

        $this->_helper->layout()->disableLayout();
        //Disable render
        $this->_helper->viewRenderer->setNoRender();

        $sPathUpload = PATH_MAIL_UPLOAD_DIR;
        $sFileUrl = PATH_MAIL_URL;

        $arrFile = array();
        foreach ($_FILES["file"]["error"] as $key => $error) {
            if ($error == UPLOAD_ERR_OK) {

                $filename = $_FILES['file']['name'][$key];
                $processFileName = Core_Common::getExtensionAndFileName($filename);
                $ext = $processFileName['extent'];

                if(!in_array($ext,$this->allowFiles)) {
                    $arrError = array('error' => true, 'message' => 'support Only ' . implode(',', $this->allowFiles));
                    echo Zend_Json::encode($arrError);
                    exit();
                }


                $size = $_FILES["file"]['size'][$key];
                $type = $_FILES["file"]['type'][$key];
                $tmp_name = $_FILES["file"]["tmp_name"][$key];
                $name = uniqid('', true).'_'.$_FILES["file"]["name"][$key];
                $originalName = $_FILES["file"]["name"][$key];
                $name = str_replace(' ','',$name);
                move_uploaded_file($tmp_name, "$sPathUpload/$name");
                $arrFile []= array('original_name'=>$originalName, 'file_name'=>$name,'url' => "$sFileUrl/$name",'size'=>$size, 'type'=>$type);
            }
        }
        echo Zend_Json::encode($arrFile);
        exit();

    }
    
    public function emptyAction()
    {
        die();
    }

    // upload attachment for mail
    public function uploadAttachments2Action()
    {

        $this->_helper->layout()->disableLayout();
        //Disable render
        $this->_helper->viewRenderer->setNoRender();

        $uploads_dir = PATH_MAIL_UPLOAD_DIR;
        $filenames = array();
        //var_dump($_FILES);
        //die();
        if (isset($_FILES["file"]) && is_array($_FILES["file"]["error"])) {
            foreach ($_FILES["file"]["error"] as $key => $error) {
                if ($error == UPLOAD_ERR_OK) {
                    $tmp_name = $_FILES["file"]["tmp_name"][$key];
                    // basename() may prevent filesystem traversal attacks;
                    // further validation/sanitation of the filename may be appropriate
                    $name = basename($_FILES["file"]["name"][$key]);

                    // unique file path
                    $unique_name = sha1(uniqid('', true) . $name);
                    $file_path = $uploads_dir . '/' . $unique_name; 
                    if (move_uploaded_file($tmp_name, $file_path)) {
                        $filenames[] = array(
                            'path' => $file_path,
                            'original_name' => $_FILES["file"]["name"][$key],
                            'type' => $_FILES["file"]["type"][$key],
                            'unique_name' => $unique_name,
                            'size' => $_FILES["file"]["size"][$key]
                        );
                    }
                }
            }
        } else {
            echo 'not array';
        }

        echo json_encode($filenames, JSON_HEX_APOS);
        exit();

    }

    public function removeAttachments2Action()
    {
        $this->_helper->layout()->disableLayout();
        //Disable render
        $this->_helper->viewRenderer->setNoRender();

        $unique_name = $this->_request->getParam('path');
        if (empty($path)) {
            $params = json_decode(file_get_contents('php://input'),true);
            $unique_name = isset($params['unique_name']) ? $params['unique_name'] : null;
        }
        Core_Common::deleteFile($unique_name, FOLDER_MAIL);
        echo Zend_Json::encode(array($unique_name));
    }

    public function indexAction()
    {

        $this->_helper->layout()->disableLayout();
        //Disable render
        $this->_helper->viewRenderer->setNoRender();

        $filename = $_FILES['chosenImage']['name'];
        $processFileName = Core_Common::getExtensionAndFileName($filename);
        $ext = $processFileName['extent'];

        if(!in_array($ext,$this->allowFiles)) {
            $arrError = array('error' => true, 'message' => 'support Only ' . implode(',', $this->allowFiles));
            echo Zend_Json::encode($arrError);
            exit();
        }

        //get old image
        $sOldImage = $this->_request->getParam('img', '');
        $f = $this->_request->getParam('f', '');
        $w = $this->_request->getParam('w', 500);
        $h = $this->_request->getParam('h', 500);

        $sPathUpload = ROOT_IMAGES_PATH . '/' . $f;
        $sImageUrl = ROOT_IMAGES_URL . '/' . $f;

        $org_file = $_FILES['chosenImage']['tmp_name'];
        $valid_formats = array("jpg", "png", "gif", "bmp", "jpeg","GIF","JPG","PNG");



        $name = $_FILES['chosenImage']['name'];

        $processFileName = Core_Common::getExtensionAndFileName($name);
        $imageName = $processFileName['file_not_extent'].'_'.time();
        $imageName = str_replace(' ','_',$imageName);
        $ext = $processFileName['extent'];

        $actual_image_name =  $imageName.".".$ext;


        //if (!file_exists($sPathUpload.'/200x200')) {
        //	mkdir($sPathUpload.'/200x200', 0777, true);
        //}

        $des_file = $sPathUpload .'/'.$actual_image_name;
        $arrReuslt = Core_Image::resizeImage($org_file, $des_file, $w, $h);


        if($arrReuslt['error'] == 0){

            $arrReuslt['url'] = $sImageUrl .'/' . $actual_image_name;
            $arrReuslt['file_name'] = $actual_image_name;

            unlink($org_file);
        }

        echo Zend_Json::encode($arrReuslt);
        exit();

    }

    /*
     * Upload Action
     */  
    public function uploadAction()
    {
        
        $this->_helper->layout()->disableLayout();
        //Disable render
        $this->_helper->viewRenderer->setNoRender();

        $filename = $_FILES['file_upload']['name'];
        $processFileName = Core_Common::getExtensionAndFileName($filename);
        $ext = $processFileName['extent'];

        if(!in_array($ext,$this->allowFiles)) {
            $arrError = array('error' => true, 'message' => 'support Only ' . implode(',', $this->allowFiles));
            echo Zend_Json::encode($arrError);
            exit();
        }

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

        $processFileName = Core_Common::getExtensionAndFileName($name);
        $imageName = $processFileName['file_not_extent'].'_'.time();
        $imageName = str_replace(' ','_',$imageName);
        $ext = $processFileName['extent'];


        $actual_image_name =  $imageName.".".$ext;
        $thumb_image_name =  'thumb_'.$imageName.".".$ext;
        
        //File original
        $sPathOriginalFile = $sPathUpload.'/original/'.$actual_image_name;
        
        // File medium
        $sPathFile = $sPathUpload.'/'.$actual_image_name;
        
        //File thumb
        $sPathFileThumb = $sPathUpload.'/'.$thumb_image_name;
        
         if(move_uploaded_file($filename, $sPathOriginalFile))
         {
              Core_Common::generate_image_thumbnail($sPathOriginalFile, $sPathFile);
              //Core_Common::generate_image_thumbnail($sPathOriginalFile, $sPathFileThumb, 349, 349);
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
     
     //image
	public function upload2Action()
    {
        
        $this->_helper->layout()->disableLayout();
        //Disable render
        $this->_helper->viewRenderer->setNoRender();

        if(!isset($_FILES['chosenImage'])){
            echo Zend_Json::encode(array());
            exit();
        }
        $file = $_FILES['chosenImage'];

        $filename = $file['name'];
        $processFileName = Core_Common::getExtensionAndFileName($filename);
        $ext = $processFileName['extent'];

        if(!in_array($ext,$this->allowFiles)) {
            $arrError = array('error' => true, 'message' => 'support Only ' . implode(',', $this->allowFiles));
            echo Zend_Json::encode($arrError);
            exit();
        }

        $sPathUpload = PATH_IMAGES_UPLOAD_DIR;
        $sImageUrl = PATH_IMAGES_URL;


        $error =1;


        $filename = $_FILES['chosenImage']['tmp_name'];
//        $valid_formats = array("jpg", "png", "gif", "bmp", "jpeg","GIF","JPG","PNG");
        //check height and width
        list($width, $height) = getimagesize($filename);



//        if($width < MIN_WIDTH || $height < MIN_HEIGHT){
//        	$output = array('error'=>1, 'message' => $this->view->locales->validate->invalid_width_height);
//    		echo Zend_Json::encode($output);
//    		exit();
//        }
        
        $name = $_FILES['chosenImage']['name'];

        $processFileName = Core_Common::getExtensionAndFileName($name);
        $imageName = $processFileName['file_not_extent'].'_'.time();
        $imageName = str_replace(' ','_',$imageName);
        $ext = $processFileName['extent'];

        $actual_image_name =  $imageName.".".$ext;

        if(filesize($filename) > MAX_UPLOAD_PHOTO_SIZE){
            $output = array('error'=>1,'message' =>$this->view->locales->validate->upload->max_size);
            echo Zend_Json::encode($output);
            exit();
        }
        if(!Core_Common::checkExtension($ext,'image')){
            $output = array('error'=>1,'message' =>$this->view->locales->validate->invalid_file_extens);
            echo Zend_Json::encode($output);
            exit();
        }

        Core_Common::createFolder($sPathUpload.'/original');
//        Core_Common::createFolder($sPathUpload.'/90x90');
//        Core_Common::createFolder($sPathUpload.'/500x280');
//        Core_Common::createFolder($sPathUpload.'/320x179');
//        Core_Common::createFolder($sPathUpload.'/320x179');
//        Core_Common::createFolder($sPathUpload.'/200x112');
//        Core_Common::createFolder($sPathUpload.'/800x800');
        
//        //File original
        $sPathOriginalFile = $sPathUpload.'/original/'.$actual_image_name;
        $sUrlOriginalFile = $sImageUrl.'/original/'.$actual_image_name;
//
//        //Image 90x90
//        $sPath90x90File = $sPathUpload.'/90x90/'.$actual_image_name;
//
//        //Image 500x280
//        $sPath500x280File = $sPathUpload.'/500x280/'.$actual_image_name;
//
//        //Image 320x179
//        $sPath320x179File = $sPathUpload.'/320x179/'.$actual_image_name;
//
//        //Image 200x112
//        $sPath200x112File = $sPathUpload.'/200x112/'.$actual_image_name;
//
//        //Image 800x800
//        $sPath800x800File = $sPathUpload.'/800x800/'.$actual_image_name;
        

         if(move_uploaded_file($filename, $sPathOriginalFile))
         {

//              Core_Image::cropImage($sPathOriginalFile, $sPath90x90File, 90, 90);
//              Core_Image::cropImage($sPathOriginalFile, $sPath500x280File, 500, 280);
//              Core_Image::cropImage($sPathOriginalFile, $sPath320x179File, 320, 179);
//              Core_Image::cropImage($sPathOriginalFile, $sPath200x112File, 200, 112);
//              Core_Image::cropImage($sPathOriginalFile, $sPath800x800File, 800, 800);
              
//              unlink($sPathOriginalFile); keep original
              $error =0;
         }
         
         //Result
        // $arrResult = array(
       //      'error'        => $error,
        //    'image_id'     => $imageName,
         //    'image_format' => $ext,
        //     'image_url'    => $sImageUrl
       //  );
         
      //   echo '<script type="text/javascript">
      //              document.domain = "'.DOMAIN.'";
     //          </script>
     //          <div class="response">
      //           <div class="image_id">'.$arrResult['image_id'].'</div>
    //             <div class="image_format">'.$arrResult['image_format'].'</div>
     //            <div class="image_url">'.$arrResult['image_url'].'</div>
     //         </div>';
 
         $output = array();
         
         if($error == 0){
         	$output = array('error'=>0,'original_name' =>$name, 'filename' => $actual_image_name,'url'=>$sUrlOriginalFile);
         	
         }else{
         	$output = array('error'=>1, 'message' => $this->view->locales->validate->upload_error);
         }
         
         echo Zend_Json::encode($output);
         exit();
               
     }

     //File
     public function upload3Action()
     {
     
     	$this->_helper->layout()->disableLayout();
     	//Disable render
     	$this->_helper->viewRenderer->setNoRender();


         $filename = $_FILES['chosenFile']['name'];
         $processFileName = Core_Common::getExtensionAndFileName($filename);
         $ext = $processFileName['extent'];

         if(!in_array($ext,$this->allowFiles)) {
             $arrError = array('error' => true, 'message' => 'support Only ' . implode(',', $this->allowFiles));
             echo Zend_Json::encode($arrError);
             exit();
         }

     	$sPathUpload = PATH_FILES_UPLOAD_DIR;
     	$sImageUrl = PATH_FILES_URL;


     	$error =1;
     
     	$filename = $_FILES['chosenFile']['tmp_name'];
     	$valid_formats = array("pdf","doc","docx","xls","xlsx");
     
     	$name = $_FILES['chosenFile']['name'];

         $processFileName = Core_Common::getExtensionAndFileName($name);
         $imageName = $processFileName['file_not_extent'].'_'.time();
         $imageName = str_replace(' ','_',$imageName);
         $ext = $processFileName['extent'];

     	$actual_image_name =  $imageName.".".$ext;
     	
     	// File medium
     	$sPathFile = $sPathUpload.'/'.$actual_image_name;

     
     	if(move_uploaded_file($filename, $sPathFile))
     	{
     		$error =0;
     	}
     	 
     	if($error == 0){
     		echo $actual_image_name;
     	}else{
     		echo '';
     	}
     
     	exit();
     }
     
     public function uploadFileAction()
     {
     	 
     	$this->_helper->layout()->disableLayout();
     	//Disable render
     	$this->_helper->viewRenderer->setNoRender();

         $filename = $_FILES['chosenFile']['name'];
         $processFileName = Core_Common::getExtensionAndFileName($filename);
         $ext = $processFileName['extent'];

         if(!in_array($ext,$this->allowFiles)) {
             $arrError = array('error' => true, 'message' => 'support Only ' . implode(',', $this->allowFiles));
             echo Zend_Json::encode($arrError);
             exit();
         }

     	$sPathUpload = PATH_FILES_UPLOAD_DIR;
     	$sImageUrl = PATH_FILES_URL;

     	 
     	$filename = $_FILES['chosenFile']['tmp_name'];
        $addNew = $this->_getParam('add-new',false);
        $sReName = $this->_getParam('re-name','');
         $name = (empty($sReName)) ?  $_FILES['chosenFile']['name'] : $sReName;


         $processFileName = Core_Common::getExtensionAndFileName($name);
         $ext = $processFileName['extent'];

         $addNew = filter_var($addNew, FILTER_VALIDATE_BOOLEAN);
         $index = 1;
         while($addNew == true)
         {
             $name = $processFileName['file_not_extent'].'('.$index.').'.$ext;
             $sPathFile = $sPathUpload.'/'.$name;

             if(!file_exists($sPathFile)) {
                 $addNew = false;
                 break;
             }
             else
                $index++;
         }
         // File medium
         $sPathFile = $sPathUpload.'/'.$name;

         $allowFiles = Core_Common::checkAllowFileType($name,'allow_document');
        if(file_exists($sPathFile))
        {
            $output = array('error'=>1, 'filename' => $name, 'message' => 'file already exit.', 'file_already'=>true,  'url' => $sImageUrl .'/'. $name, 'path' => $sPathFile);
        }else if($allowFiles['error']) {// check allow file
             $output = array('error' => 1, 'message' => sprintf($this->view->locales->validate->invalid_file_extens,$allowFiles['message']));
         }else if(move_uploaded_file($filename, $sPathFile))
         {
             $output = array('error'=>0, 'original_name'=>$name, 'filename' => $name, 'url' => $sImageUrl .'/'. $name, 'path' => $sPathFile);
         }else{
             $output = array('error'=>1, 'message' => $this->view->locales->validate->upload_error);
         }

         
         echo Zend_Json::encode($output);
         exit();
     }

    public function createFolder($filename,$groupId,$parentId,$folderName, $type)
    {
        // Create folder
        $group = Group::getInstance()->getGroupByID($groupId);
        $arrError = array();
        if(empty($group)) {
            $arrError = array('error' => true, 'message' => 'group not found');
            return $arrError;
        }

        if(empty($folderName)){
            $arrError = array('error' => true, 'message' => 'Please enter the name.');
            return $arrError;
        }

        //check permision user in group
        $groupMember = GroupMember::getInstance()->getGroupMemberByAccountAndGroupId($this->arrLogin['accountID'], $groupId);
        if(empty($groupMember) && $group['admin_id'] != $this->arrLogin['accountID']){
            $arrError = array('error' => true, 'message' => 'Permission deny.');
            return $arrError;
        }

        //check exist folder in db
//        $arrFile = File::getInstance()->selectOneByNameAndTypeAndGroup($filename, FILE, $groupId, $parentId);
//        if(!empty($arrFile)){
//            $arrError = array('error' => true, 'message' => 'You cannot save the folder. This node does not accept two nodes with the same name.');
//            return $arrError;
//        }

//check parent
        if($parentId > 0){
            $arrFile = File::getInstance()->selectOne($parentId);

            if(empty($arrFile)){
                $arrError = array('error' => true, 'message' => 'Folder is not exists.');
                return $arrError;
            }

            //check owner
            if($arrFile['owner'] != $this->arrLogin['accountID']){
                $arrError = array('error' => true, 'message' => "Permission deny. Please choose your folder.");
                return $arrError;
            }

            //set path
            $sPathFolder = $arrFile['path'];

            //create folder in server
            Core_Common::createFolder(PATH_FILES_UPLOAD_DIR .'/'.$group['group_id']);

            $arrError = array('error' => false, 'message' => 'Success!','path'=>PATH_FILES_UPLOAD_DIR .$sPathFolder.'/'.$folderName,'url'=>$sPathFolder.'/'.$folderName);
            return $arrError;

        }else{
            $arrError = array('error' => true, 'message' => 'Folder is not exists.');
            return $arrError;
        }
//        else{
//            $sPathFolder = '/'.$parentId['group_id'];
//        }




    }

    public function uploadDocumentAction()
    {

        $this->_helper->layout()->disableLayout();
        //Disable render
        $this->_helper->viewRenderer->setNoRender();

        $filename = $_FILES['chosenFile']['name'];
        $processFileName = Core_Common::getExtensionAndFileName($filename);
        $ext = $processFileName['extent'];

        if(!in_array($ext,$this->allowFiles)) {
            $arrError = array('error' => true, 'message' => 'support Only ' . implode(',', $this->allowFiles));
            echo Zend_Json::encode($arrError);
            exit();
        }

        $sPathUpload = PATH_FILES_UPLOAD_DIR;
        $sImageUrl = PATH_FILES_URL;
        $sessionOldName = new Zend_Session_Namespace('old-name');

        $filename = $_FILES['chosenFile']['tmp_name'];
        $addNew = $this->_getParam('add-new',false);
        $replace = $this->_getParam('replace',false);
        $sReName = $this->_getParam('re-name','');

        $groupId = $this->_getParam('g',0);
        $parentId = $this->_getParam('p',0);
        $folderName = $this->_getParam('f','');
        $name = (empty($sReName)) ?  $_FILES['chosenFile']['name'] : $sReName;
        $name = trim($name);
        $addNew = filter_var($addNew, FILTER_VALIDATE_BOOLEAN);
        $replace = filter_var($replace, FILTER_VALIDATE_BOOLEAN);
        $allowFiles = Core_Common::checkAllowFileType($name,'allow_document');
        $createFolder = $this->createFolder($name,$groupId,$parentId,$folderName, FILE);
        if($createFolder['error'])
        {
            echo Zend_Json::encode($createFolder);
            exit();
        }

//        split file (name and extent)
        $processFileName = Core_Common::getExtensionAndFileName($name);
        $ext = $processFileName['extent'];

        $sPathFile = $createFolder['path'] . '/' . $name;

        if(file_exists($createFolder['path'].'/'.$name) && !$addNew && !$replace)
        {
//            $sessionOldName->name = $name;

            $output = array('error'=>1, 'filename' => $name, 'message' => 'file already exit.', 'file_already'=>true,  'url' => $sImageUrl . '/' . $groupId . '/' . $folderName . '/' . $name, 'path' => $sPathFile);
        }else if($allowFiles['error']) {// check allow file
            $output = array('error' => 1, 'message' => sprintf($this->view->locales->validate->invalid_file_extens,$allowFiles['message']));
        }else {

//            $index = 1;
//            while ($addNew == true) { // create new file name with (number) ex: a(1).jpg
//                $sessionOldName->unsetAll();
//                $name = $processFileName['file_not_extent'] . '(' . $index . ').' . $ext;
//
//                $sPathFile = $createFolder['path'] . '/' . $name;
//
//                if (!file_exists($sPathFile)) {
//                    $addNew = false;
//                    break;
//                } else
//                    $index++;
//            }

//            $name = 'đơn xin nghỉ phép(3).docx';

            if($replace) {
                if (file_exists($createFolder['path'] . '/' . $name))
                    unlink($createFolder['path'] . '/' . $name);
            }

            if (move_uploaded_file($filename, $sPathFile)) {
                $output = array('error' => 0, 'original_name' => $name, 'filename' => $name, 'url' => $sImageUrl . '/' . $groupId . '/' . $folderName . '/' . $name, 'path' => $sPathFile);

                if(trim($sessionOldName->name) != '')
                {

                    $alreadyFile = File::getInstance()->selectOneByNameAndTypeAndGroup($sessionOldName->name, FILE, $groupId, $parentId);
//                    Core_Common::var_dump($alreadyFile);
                    if(!empty($alreadyFile)){
                        unlink($createFolder['path'].'/'.$sessionOldName->name);
                        $newFile = $alreadyFile;
                        $newFile['name'] = $name;
                        $newFile['original_name'] = $name;
                        File::getInstance()->update($alreadyFile,$newFile);
                    }
                    $sessionOldName->unsetAll();
                }else if(!$replace)
                    $iFileId = File::getInstance()->insert($name, $createFolder['url'], FILE, $parentId, $this->arrLogin['accountID'], $groupId, 0, $name);
//                Core_Common::var_dump($iFileId);
            } else {
                $output = array('error' => 1, 'message' => $this->view->locales->validate->upload_error);
            }
        }
//        else{
//            $output = array('error'=>1, 'message' => $this->view->locales->validate->upload_error);
//        }

//insert file

        echo Zend_Json::encode($output);
        exit();
    }

     //Video
     public function upload4Action()
     {
     	 
     	$this->_helper->layout()->disableLayout();
     	//Disable render
     	$this->_helper->viewRenderer->setNoRender();


         $filename = $_FILES['chosenVideo']['name'];
         $processFileName = Core_Common::getExtensionAndFileName($filename);
         $ext = $processFileName['extent'];

         if(!in_array($ext,$this->allowFiles)) {
             $arrError = array('error' => true, 'message' => 'support Only ' . implode(',', $this->allowFiles));
             echo Zend_Json::encode($arrError);
             exit();
         }

     	$sPathUpload = PATH_VIDEOS_UPLOAD_DIR;
     	$sImageUrl = PATH_VIDEOS_URL;

     	$error =1;
     	 
     	$filename = $_FILES['chosenVideo']['tmp_name'];
     	$valid_formats = array("mp4");
     	 
     	$name = $_FILES['chosenVideo']['name'];

         $processFileName = Core_Common::getExtensionAndFileName($name);
         $imageName = $processFileName['file_not_extent'].'_'.time();
         $imageName = str_replace(' ','_',$imageName);
         $ext = $processFileName['extent'];

     	$actual_image_name =  $imageName.".".$ext;
     
     	// File medium
     	$sPathFile = $sPathUpload.'/'.$actual_image_name;
     
     	 
     	if(move_uploaded_file($filename, $sPathFile))
     	{
     		$error =0;
     	}
     	 
     	if($error == 0){
     		echo $actual_image_name;
     	}else{
     		echo '';
     	}
     	 
     	exit();
     }
     
     //upload image
     public function upload5Action()
     {
     
     	$this->_helper->layout()->disableLayout();
     	//Disable render
     	$this->_helper->viewRenderer->setNoRender();


         $filename = $_FILES['upl']['name'];
         $processFileName = Core_Common::getExtensionAndFileName($filename);
         $ext = $processFileName['extent'];

         if(!in_array($ext,$this->allowFiles)) {
             $arrError = array('error' => true, 'message' => 'support Only ' . implode(',', $this->allowFiles));
             echo Zend_Json::encode($arrError);
             exit();
         }

     	$sPathUpload = PATH_IMAGES_UPLOAD_DIR;
     	$sImageUrl = PATH_IMAGES_URL;


     	$error =1;
     
     	$filename = $_FILES['upl']['tmp_name'];
     	$valid_formats = array("jpg", "png", "gif", "bmp", "jpeg","GIF","JPG","PNG");
     
     	$name = $_FILES['upl']['name'];

         $processFileName = Core_Common::getExtensionAndFileName($name);
         $imageName = $processFileName['file_not_extent'].'_'.time();
         $imageName = str_replace(' ','_',$imageName);
         $ext = $processFileName['extent'];

        $actual_image_name =  $imageName.".".$ext;

        //File original
        $sPathOriginalFile = $sPathUpload.'/original/'.$actual_image_name;
        
        //Image 90x90
        $sPath90x90File = $sPathUpload.'/90x90/'.$actual_image_name;
        
        //Image 500x280
        $sPath500x280File = $sPathUpload.'/500x280/'.$actual_image_name;
        
        //Image 320x179
        $sPath320x179File = $sPathUpload.'/320x179/'.$actual_image_name;
        
        //Image 200x112
        $sPath200x112File = $sPathUpload.'/200x112/'.$actual_image_name;
        
        //Image 800x800
        $sPath800x800File = $sPathUpload.'/800x800/'.$actual_image_name;
        
        // File medium
        $sPathFile = $sPathUpload.'/'.$actual_image_name;

         if(move_uploaded_file($filename, $sPathOriginalFile))
         {
              Core_Image::cropImage($sPathOriginalFile, $sPath90x90File, 90, 90);
              Core_Image::cropImage($sPathOriginalFile, $sPath500x280File, 500, 280);
              Core_Image::cropImage($sPathOriginalFile, $sPath320x179File, 320, 179);
              Core_Image::cropImage($sPathOriginalFile, $sPath200x112File, 200, 112);
              Core_Image::cropImage($sPathOriginalFile, $sPath800x800File, 800, 800);
              
              $error =0;
         }

         if($error == 0){
             $output = array('error'=>0,'original_name' =>$name, 'filename' => $actual_image_name);

         }else{
             $output = array('error'=>1, 'message' => $this->view->locales->validate->upload_error);
         }

         echo Zend_Json::encode($output);
         exit();
     	exit();
     	 
     }
     
/**
     * upload avatar group
     */
	public function uploadGroupAction()
     {

     	$sPathUpload = PATH_GROUPS_UPLOAD_DIR;
     	$sImageUrl = PATH_GROUPS_URL;

         $filename = $_FILES['chosenImage']['name'];
         $processFileName = Core_Common::getExtensionAndFileName($filename);
         $ext = $processFileName['extent'];

         if(!in_array($ext,$this->allowFiles)) {
             $arrError = array('error' => true, 'message' => 'support Only ' . implode(',', $this->allowFiles));
             echo Zend_Json::encode($arrError);
             exit();
         }

     
     	$error = 1;
     	$message = '';
     	$output = array();
     	
     	$filename = $_FILES['chosenImage']['tmp_name'];
     	$valid_formats = array("jpg", "png", "gif", "bmp", "jpeg","GIF","JPG","PNG");

     	$name = $_FILES['chosenImage']['name'];

         $processFileName = Core_Common::getExtensionAndFileName($name);
         $imageName = $processFileName['file_not_extent'].'_'.time();
         $imageName = str_replace(' ','_',$imageName);
         $ext = $processFileName['extent'];

     	$actual_image_name =  $imageName.".".$ext;
     
     	Core_Common::createFolder($sPathUpload);
     	Core_Common::createFolder($sPathUpload.'/200x200');
     	
     	//File original
     	$sPathOriginalFile = $sPathUpload.'/'.$actual_image_name;
     
     	//Image 90x90
     	$sPath100x100File = $sPathUpload.'/200x200/'.$actual_image_name;
     
     	if (!file_exists($sPathUpload.'/200x200')) {
     		mkdir($sPathUpload.'/200x200', 0777, true);
     	}
     	
     	if(move_uploaded_file($filename, $sPathOriginalFile))
     	{

     		Core_Image::cropImage($sPathOriginalFile, $sPath100x100File,200, 200);
     		$error =0;
     	}
     	 
     	
     	if($error == 0){
     		//delete file
//     		unlink($sPathOriginalFile); keep original
     		
     		$message = 'Success!';
     		$output['error'] = 0;
     		$output['filename'] = $actual_image_name;
     		$output['url'] = $sImageUrl .'/200x200/'. $actual_image_name;
     		
     	}else{
     		$output['error'] = 1;
     		$output['message'] = 'Error!';
     		$message = 'Error!';
     	}  
     	
     	echo Zend_Json::encode($output);
     	exit();
     	
     }
}

