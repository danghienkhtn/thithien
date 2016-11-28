<?php
/**
 * @author      :   HoaiTN
 * @name        :   IndexController
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   controller default
 */
class Backend_PhotoController extends Core_Controller_ActionBackend
{
    private $arrLogin;
     public function init()
     {
        parent::init();

        global $globalConfig;
         $this->arrLogin    = $this->view->arrLogin;
    }

    /**
     * Default action
     */
    public function indexAction()
    {


    }

    public function addAction()
    {
        $this->_helper->layout()->disableLayout();

        $error = 0;
        $iFeedId = 0;
        $arrPhoto = array();
        if($this->_request->isPost())
        {
            $arrFeed = array();
            $params = $this->_request->getPost();

            if(!empty($params['data'])){

                $arrDatas = json_decode($params['data'], true);

                $group = array();

                if(isset($arrDatas['team_id_to']) && !empty($arrDatas['team_id_to'])){
                    $group = Group::getInstance()->getGroupByID($arrDatas['team_id_to']);
                    $arrDatas['team_id_to'] = $group['group_id'];
                    $arrDatas['teamName'] = $group['group_name'];
                    //check exist group
                    if(empty($group)){
                        $output = array('error'=>1, 'message'=>'group is not exist');
                        echo Zend_Json::encode($output);
                        exit();
                    }
                }

                $arrFeed['message'] = '';
                $arrFeed['image_url1'] = $arrFeed['image_url2'] = $arrFeed['image_url3'] = $arrFeed['image_url4'] = '';
                $arrFeed['file_url1'] = $arrFeed['file_url2'] = $arrFeed['file_url3'] = $arrFeed['file_url4'] = '';
                $arrFeed['video_url1'] = $arrFeed['video_url2'] = $arrFeed['video_url3'] = $arrFeed['video_url4'] = '';
                $arrFeed['link_url'] = '';
                $arrFeed['feed_type'] = 3;//'1:normal, 2:link, 3: image, 4:file, 5: video'
                $arrFeed['title'] = '';
                $arrFeed['description'] = '';
                $arrFeed['account_id'] = $this->arrLogin['accountID'];
                $arrFeed['team_id_to'] = $arrDatas['team_id_to'];
                $arrFeed['team_name'] =  empty($arrDatas['teamName']) ? '' : $arrDatas['teamName'];
                $arrFeed['account_list_to'] = '';
                $arrFeed['comment1'] = '';
                $arrFeed['comment2'] = '';
                $arrFeed['total_like'] = 0;
                $arrFeed['total_comment'] = 0;
                $arrFeed['create_date'] = time();
                $arrFeed['status'] = 0;

                //image
                if(!empty($arrDatas['imageNames'])){
                    $arrFeed = $this->setFile($arrFeed, $arrDatas['imageNames'], 'image_url');
                }else{

                    $output = array('error'=>1, 'message'=>'photo is empty');
                    echo Zend_Json::encode($output);
                    exit();
                }


                $iFeedId = 0;
                $iAlbumId = 0;
                $isNewAlbum = 0;
                //check exist album id if exist then update else insert
                if(empty($arrDatas['album_id'])){//insert

                    $sName = 'Others';
                    $sContent = $sImage = $sLocation = '';
                    $iEventDate = time();
                    $iAccountID = $this->arrLogin['accountID'];
                    $iActive = 1;
                    $iYear = date("Y");
                    $iType = !empty($group) ? $group['group_type'] : 0;
                    $iOther = 1;//album Others

                    $arrAlbumTmp = Album::getInstance()->getAlbumByGroupOther($arrDatas['team_id_to']);

                    if(empty($arrAlbumTmp)){
                        $isNewAlbum = 1;
                        //insert album
                        $Album = array(
                            'name' => $sName,
                            'content' => $sContent,
                            'image_url' => $sImage,
                            'location' => $sLocation,
                            'event_date' => $iEventDate,
                            'account_id' => $iAccountID,
                            'active' => $iActive,
                            'year' => $iYear,
                            'type' => $iType,
                            'group_id' => $arrDatas['team_id_to'],
                            'is_other' => $iOther
                        );
                        $iAlbumId = Album::getInstance()->addAlbum($Album);
                    }else{
                        $iAlbumId = $arrAlbumTmp['album_id'];
                    }

                }else if(is_numeric($arrDatas['album_id'])){
                    //check feed id
                    $iAlbumId = $arrDatas['album_id'];
                    $iFeedId = $arrDatas['feed_id'];

                    $arrAlbum = Album::getInstance()->getAlbumByID($iAlbumId);
                    $arrFeedTmp = Feed::getInstance()->getFeedById($iFeedId);

                    if(!empty($arrAlbum))
                    {
                        if($arrAlbum['is_other'])
                        {
                            $isNewAlbum = 0;
                        }
                    }
                    else if(empty($arrFeedTmp)){
                        $output = array('error'=>1, 'message' => 'Album not exist.');
                        echo Zend_Json::encode($output);
                        exit();
                    }
                }

                if($iAlbumId > 0){
                    if($isNewAlbum == 0) {
                        //insert Feed
                        $iFeedId = Feed::getInstance()->insert($arrFeed);
                    }
                    if($iFeedId > 0){
                        $i = 0;
                        //insert photo
                        foreach ($arrDatas['imageNames'] as $imageUrl){
                            $photoFeed = array(
                                'image_url' => $imageUrl,
                                'feed_id' => $iFeedId,
                                'album_id' => $iAlbumId,
                                'active' => 1,
                                'group_id' => $arrFeed['team_id_to'],
                                'account_id' => $arrFeed['account_id'],
                                'message' => $arrDatas['message']
                            );
                            $iPhotoId = PhotoFeed::getInstance()->addPhoto($photoFeed);
                            $photo  = PhotoFeed::getInstance()->getPhotoFeedById($iPhotoId);
                            $photo  = Core_Common::photoProcess($photo);
                            $arrPhoto   []= array('id'=>$photo['photo_id'],'image_tag'=>$photo['image_tag']);

                            ActionLog::getInstance()->insert($this->arrLogin['id'],ActionLog::$add,ActionLog::$photo,$this->arrLogin['accountID'],$this->arrLogin['nickName'],' a new photo To "'.$arrAlbum['name'].'" album' );
                            $i++;
                        }

                        //update total album
                        Album::getInstance()->updateAlbumTotal($iAlbumId, $i);

                    }else{
                        $error = 1;
                        //rollback (delete $iAlbumId)
                        Album::getInstance()->removeAlbum($iAlbumId);
                    }
                }else{
                    $error = 1;
                    //rollback (delete $iAlbumId)
                    Album::getInstance()->removeAlbum($iAlbumId);
                }

            }
            $output = array('error'=>$error,'arrPhoto'=>$arrPhoto);
            echo Zend_Json::encode($output);
            exit();

        }

    }

    private function setFile($arrFeed, $arrFiles, $value){

        $i = 0;
        foreach ($arrFiles as $file){

            if($i == 4){
                break;
            }

            if(!empty($file)){
                $i = $i + 1;
                $arrFeed[$value.$i] = $file;
            }
        }

        return $arrFeed;

    }

    
    /**
     * Default action
     */
    public function deleteAction()
    {

        $this->_helper->layout()->disableLayout();
        $error  = array('error' => false, 'message' => '');
        if($this->getRequest()->isPost()) {

            //get params
            $arr_photoId = $this->_getParam('arr_photoId','');
            $iAlbumId = $this->_getParam('albumId',0);
            $iGroupId = $this->_getParam('groupId',0);
            $arrFeedIds    = array();
            $album  = Album::getInstance()->getAlbumByID($iAlbumId);

            if(is_array($arr_photoId) && !empty($album))
            {
                $arr_photo  = PhotoFeed::getInstance()->getPhotoFeedByIds($arr_photoId);

                foreach($arr_photo['data'] as $photo)
                {

                    if(!empty($photo))
                    {
                        // create log
                        ActionLog::getInstance()->insert($this->arrLogin['id'],ActionLog::$delete,ActionLog::$photo,$this->arrLogin['accountID'],$this->arrLogin['nickName'],' a photo Of "'.$album['name'].'" album' );
                        // process photo and name
                        $photo  = Core_Common::photoProcess($photo);
                        $fileName   = trim($photo['image_tag']);
                        // delete file
                        if(!empty($fileName))
                            Core_Image::delete($fileName);

                        $arrFeedIds[] = $photo['feed_id'];
                            //delete photo_feed
                         PhotoFeed::getInstance()->removePhoto($photo['photo_id'], $photo['group_id'], $photo['album_id'],$photo['feed_id']);
//                        Core_Common::var_dump($photo);

                    }
                    else{
                        $error  = array('error' => true, 'message' => 'Photo Not Found');
                    }
                }

                if(!empty($arrFeedIds)){
                    $arrFeedIds = array_unique($arrFeedIds);
                }


                //delete feed
                foreach ($arrFeedIds as $feedId){
                    $total = PhotoFeed::getInstance()->countPhotoFeedIDByFeedId($feedId);
                    if($total == 0){
                        // create log
                        ActionLog::getInstance()->insert($this->arrLogin['id'],ActionLog::$delete,ActionLog::$feed,$this->arrLogin['accountID'],$this->arrLogin['nickName'],' a feed '.$feedId );
                        //delete feed
                        Feed::getInstance()->delete($feedId, $iGroupId);
                    }
                }

                //delete album
                $total = PhotoFeed::getInstance()->countPhotoFeedIDByGroupIdAndAlbumId($iGroupId, $iAlbumId);

                if($total == 0){
                    // create log
                    ActionLog::getInstance()->insert($this->arrLogin['id'],ActionLog::$delete,ActionLog::$album,$this->arrLogin['accountID'],$this->arrLogin['nickName'],' "' . $album['name'].'" album' );

                    // process album and get path file album
                    $album  = Core_Common::albumProcess($album);
                    $fileName   = $album['image_tag'];
                    // delete file album
                    if(!empty($fileName))
                        Core_Image::delete($fileName);

                    // delete a album
                    Album::getInstance()->removeAlbum($iAlbumId);
                    $error  = array('error' => false,'album'=>true,'message' =>'');
                    echo Zend_Json::encode($error);
                    exit();
                }

                Album::getInstance()->updateAlbumTotal($iAlbumId,-$arr_photo['total']);
                $error  = array('error' => false,'album'=>false,'message' =>'');
            }
            else{
                $error  = array('error' => true,'album'=>false, 'message' => 'Album Not Found');
            }

        }

        echo Zend_Json::encode($error);
        exit();
    }
    
    
        /**
     * Form new product
     */
    public function addphotosAction() 
    {
        
         $this->_helper->layout()->disableLayout();
        //Disable render
        $this->_helper->viewRenderer->setNoRender();
        
        # Get albumID
        $iAlbumID = $this->_request->getParam('albumid', 0);
        $arrImages = $this->_request->getParam('data');
        
        $arrResult = array(
                    'error' => 1,
                    'message' => 'AlbumID is not found!',
                );
        
        //check empty photo
        if(empty($arrImages))
        {
             $arrResult = array(
                'error' => 1,
                'message' => 'Pls choose at least a photo!',
            );
             
            echo json_encode($arrResult);
            exit;
        }
        
         //Check AlbumID  
        if (!empty($iAlbumID)) 
        {
            $arrResult = array(
                'error' => 1,
                'message' => 'AlbumID is not found!',
            );

            $arrAlbum = Album::getInstance()->getAlbumByID($iAlbumID);
            
            //check empty album
            if (!empty($arrAlbum))
            {
                //check empty
                 $photoInstance = Photo::getInstance();
                 $iActive =1;
                 $iHot=0;
                 
                 foreach($arrImages as $image_url)
                 {
                     $photo = array(
                         'image_url' => $image_url,
                         'album_id' => $iAlbumID,
                         'ishot' => $iHot,
                         'active' => $iActive
                     );
                    $photoInstance->addPhoto($photo);
                     $msg = " add photo for ".$image_url.' to album '.$arrAlbum['name'];
                     ActionLog::getInstance()->insert($this->arrLogin['id'],ActionLog::$add,ActionLog::$album,$this->arrLogin['accountID'],$this->arrLogin['nickName'],$msg);
                 }
              
                 //
                $arrResult = array(
                    'error' => 0,
                    'message' => 'Photo has been successfully added!',
                );
            }
        }

       
        echo json_encode($arrResult);
        exit;
    }
    
    
}

