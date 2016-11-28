<?php

/**
 * @author      :   HoaiTN
 * @name        :   Model_API
 * @version     :   201011
 * @copyright   :   My company
 * @todo        :   Api model
 */
class ApiUpload{


    private static $sToken = 'FDFRGDdfdhfsjfhsj';

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
                move_uploaded_file($tmp_name, "$sPathUpload/$name");
                $arrFile []= array('name'=>$name,'url' => "$sFileUrl/$name",'size'=>$size, 'type'=>$type);
            }
        }
        echo Zend_Json::encode($arrFile);
        exit();

    }

}
