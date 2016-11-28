<?php
/**
 * @author      :   HoaiTN
 * @name        :   UploadController
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Upload Controller 
 */
class Backend_UploadController extends Core_Controller_Action
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

        $fileName = (strlen($attachment['name'])) > 50 ? time().'.'.$ext : $attachment['name'];
        $fullPath = PATH_MAIL_UPLOAD_DIR.'/'.$fileName;
        file_put_contents($fullPath, $attachment['content']);

        if (file_exists($fullPath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.$fileName.'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($fullPath));
            readfile($fullPath);
            exit;
        }else{
            $this->redirect(PATH_MAIL_URL.'/'.$attachment);
            exit;
        }


    }

    public function deleteFileAction()
    {
        $result = false;
        $this->_helper->layout()->disableLayout();
        //Disable render
        $this->_helper->viewRenderer->setNoRender();
        $fileName = $this->_getParam('name','');
        $folder = $this->_getParam('folder','');
        $path = ROOT_IMAGES_PATH.'/'.$folder;
        if(file_exists($path.'/'.$fileName))
        {
            unlink($path.'/'.$fileName);
            $result = true;
        }
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
     
     public function indexAction()
     {
     
     	$this->_helper->layout()->disableLayout();
     	//Disable render
     	$this->_helper->viewRenderer->setNoRender();
     
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

    //image
    public function upload2Action()
    {

        $this->_helper->layout()->disableLayout();
        //Disable render
        $this->_helper->viewRenderer->setNoRender();

        $sPathUpload = PATH_IMAGES_UPLOAD_DIR;
        $sImageUrl = PATH_IMAGES_URL;


        $error =1;

        $filename = $_FILES['chosenImage']['tmp_name'];
        $valid_formats = array("jpg", "png", "gif", "bmp", "jpeg","GIF","JPG","PNG");

        //check height and width
        list($width, $height) = getimagesize($filename);
        if($width < MIN_WIDTH || $height < MIN_HEIGHT){
//            Core_Common::var_dump($this->view->locales);
            $output = array('error'=>1, 'message' => 'Please upload Image with width and heigh greater than 400px.');
            echo Zend_Json::encode($output);
            exit();
        }

        $name = $_FILES['chosenImage']['name'];

        $processFileName = Core_Common::getExtensionAndFileName($name);
        $imageName = $processFileName['file_not_extent'].'_'.time();
        $imageName = str_replace(' ','_',$imageName);
        $ext = $processFileName['extent'];

        $actual_image_name =  $imageName.".".$ext;

        Core_Common::createFolder($sPathUpload.'/original');
        Core_Common::createFolder($sPathUpload.'/90x90');
        Core_Common::createFolder($sPathUpload.'/500x280');
        Core_Common::createFolder($sPathUpload.'/320x179');
        Core_Common::createFolder($sPathUpload.'/320x179');
        Core_Common::createFolder($sPathUpload.'/200x112');
        Core_Common::createFolder($sPathUpload.'/800x800');

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


        if(move_uploaded_file($filename, $sPathOriginalFile))
        {
            Core_Image::cropImage($sPathOriginalFile, $sPath90x90File, 90, 90);
            Core_Image::cropImage($sPathOriginalFile, $sPath500x280File, 500, 280);
            Core_Image::cropImage($sPathOriginalFile, $sPath320x179File, 320, 179);
            Core_Image::cropImage($sPathOriginalFile, $sPath200x112File, 200, 112);
            Core_Image::cropImage($sPathOriginalFile, $sPath800x800File, 800, 800);

            unlink($sPathOriginalFile);
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
            $output = array('error'=>0, 'filename' => $actual_image_name, 'url' => $sImageUrl .'/90x90/'. $actual_image_name);

        }else{
            $output = array('error'=>1, 'message' => $this->view->locales->validate->upload_error);
        }

        echo Zend_Json::encode($output);
        exit();

    }

    //upload image
    public function upload5Action()
    {

        $this->_helper->layout()->disableLayout();
        //Disable render
        $this->_helper->viewRenderer->setNoRender();

        $sPathUpload = PATH_IMAGES_UPLOAD_DIR;
        $sImageUrl = PATH_IMAGES_URL;


        $error =1;

        $filename = $_FILES['chosenImage']['tmp_name'];
        $valid_formats = array("jpg", "png", "gif", "bmp", "jpeg","GIF","JPG","PNG");

        $name = $_FILES['chosenImage']['name'];

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

    public function uploadGroupAction()
    {

        $sPathUpload = PATH_GROUPS_UPLOAD_DIR;
        $sImageUrl = PATH_GROUPS_URL;


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
            unlink($sPathOriginalFile);

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

