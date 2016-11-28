<?php
/**
 * @author      :   HoaiTN
 * @name        :   IndexController
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   controller default 
 */
class Backend_AlBumController extends Core_Controller_ActionBackend
{
    
     private $globalConfig;
     private $arrLogin;
     public function init() 
     {
        parent::init();
        
        global $globalConfig;
        
        $this->globalConfig = $globalConfig;
        $this->arrLogin     = $this->view->arrLogin;
    }
    
    /**
     * Default action
     */
    public function indexAction()
    {

        $arrType    = $this->globalConfig['album_type'];
        $arrAlbums  = array();
        $iOffset = $this->_getParam('offset',0);
        $iLimit  = $this->_getParam('limit',4);
        $sName   = $this->_getParam('name','');
        $iYear   = $this->_getParam('year',0);
        $iType   = $this->_getParam('type',0);
        $iActive = $this->_getParam('active',11);
        $iTypeCompany = $this->globalConfig['album_type']['company'];

        if($iType == 0)
        {
            foreach($arrType as $key=>$type)
            {
                $arrAlbums[$type]  = Album::getInstance()->getAlbumList($sName,$iYear,0,$iActive,0,$iOffset,$iLimit);
            }
        }
        else{
            $arrAlbums  = Album::getInstance()->getAlbumList($sName,$iYear,$iType,$iActive,0,$iOffset,$iLimit);
        }

        // process get album with group by key of album  is group_id
        $iActive = 11;
        $groupType = 0;
        $iOffset = 0;
        $iLimit  = ADMIN_PAGE_SIZE;

        //get abbum company
        $arrAlbumCompany = Album::getInstance()->getAlbumList($sName, $iYear, $iTypeCompany,$iActive, 0, $iOffset, $iLimit);
//        $arrAlbumCompany = isset($arrAlbumCompany['data']) ? $arrAlbumCompany['data'] : array();


        $arrResult = GroupMember::getInstance()->getGroupMemberByMemberId($this->arrLogin['accountID'],0,MAX_QUERY_LIMIT);
        $arrMyGroups = $arrGroupMember = $arrResult['data'];

//        echo '<pre>';
//        var_dump($arrMyGroups);
//        echo '</pre>';die;
        $iLimitAlbumGroup = 4;
        $iTypeTeam  = 0; // get all team
        $AlbumGroup = Album::getInstance()->getAlbumGroup($arrMyGroups, $sName, $iYear, $iTypeTeam, $iActive, $iOffset, $iLimitAlbumGroup);

        //Assign view
//
//        $this->view->arrGroupMember  = $arrMyGroups;
//        $this->view->arrAlbumCompany  = $arrAlbumCompany;
//        $this->view->arrTotalAlbum  = $AlbumGroup['arrTotalAlbum'];
//        $this->view->arrAlbumTeam  = $AlbumGroup['arrAlbumTeam'];
//        $this->view->urlImage = PATH_IMAGES_URL . '/original/';
//        $this->view->typeCompany = $iTypeCompany;
//        $this->view->typeTeam = $iTypeTeam;
//        $this->view->groupTypes = array_flip($this->globalConfig['group_type']);


        $groups = Group::getInstance()->getGroupAlls($iActive,$groupType, $iOffset, $iLimit);
        $arrGroupMember =    Group::getInstance()->getGroupAll2(0,MAX_QUERY_LIMIT);
        $this->view->arrGroupMember =   $arrGroupMember;
        $this->view->groups = $groups;
        $this->view->albums = $arrAlbums;
        $this->view->albumTypes  = $arrType;

    }

   /**
     * Default action
     */
    public function addAction()
    {

        $this->_helper->layout()->disableLayout();

        $error = 0;
        $iFeedId = 0;
        $arrGroupMember =    Group::getInstance()->getGroupAll2(0,MAX_QUERY_LIMIT);
        $this->view->arrGroupMember =   $arrGroupMember;

        if($this->_request->isPost())
        {
            $arrFeed = array();
            $params = $this->_request->getPost();

            if(!empty($params['data'])){

                $arrDatas = json_decode($params['data'], true);

                //Feed
                $arrFeed['message'] = '';
                $arrFeed['image_url1'] = $arrFeed['image_url2'] = $arrFeed['image_url3'] = $arrFeed['image_url4'] = '';
                $arrFeed['original_name1'] = $arrFeed['original_name2'] = $arrFeed['original_name3'] = $arrFeed['original_name4'] = '';
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

                //album
                $group = Group::getInstance()->getGroupByID($arrDatas['team_id_to']);
                $sName = $arrDatas['name'];
                $sContent = $arrDatas['desc'];
                $sImage = $sLocation = '';
                $iEventDate = time();
                $iAccountID = $this->arrLogin['accountID'];
                $iActive = 1;
                $iYear = date("Y");
                $iType = !empty($group) ? $group['group_type'] : 0;


                //image
                if(!empty($arrDatas['imageNames'])){
                    $arrFeed = $this->setFile($arrFeed, $arrDatas['imageNames'], 'image_url');
                    $sImage = $arrDatas['imageNames'][0];
                }else{

                    $output = array('error'=>1, 'message'=>'photo is empty');
                    echo Zend_Json::encode($output);
                    exit();
                }

                //insert Album
                $Album = array(
                    'name' => $sName,
                    'content' => $sContent,
                    'image_url' => $sImage,
//                    'original_name' => $sOriginalName,
                    'location' => $sLocation,
                    'event_date' => $iEventDate,
                    'account_id' => $iAccountID,
                    'active' => $iActive,
                    'year' => $iYear,
                    'type' => $iType,
                    'group_id' => $arrDatas['team_id_to'],
                    'is_other' => 0
                );
                $iAlbumId = Album::getInstance()->addAlbum($Album);

                // create log
                ActionLog::getInstance()->insert($this->arrLogin['id'],ActionLog::$create,ActionLog::$album,$this->arrLogin['accountID'],$this->arrLogin['nickName'],' "' . $sName.'" album');
                if($iAlbumId > 0){
                    //insert Feed
                    $iFeedId = Feed::getInstance()->insert($arrFeed);

                    // create log
                    ActionLog::getInstance()->insert($this->arrLogin['id'],ActionLog::$create,ActionLog::$feed,$this->arrLogin['accountID'],$this->arrLogin['nickName'],' a feed "'.$iFeedId.'"' );
                    if($iFeedId > 0){
                        $i = 0;
                        //insert photo
                        foreach ($arrDatas['imageNames'] as $imageUrl){
                            $photoFeed = array(
                                'image_url' => $imageUrl,
//                                'original_name' => $imageUrl,
                                'feed_id' => $iFeedId,
                                'album_id' => $iAlbumId,
                                'active' => 1,
                                'group_id' => $arrFeed['team_id_to'],
                                'account_id' => $arrFeed['account_id'],
                                'message' => $arrDatas['descImages'][$i]
                            );
                            PhotoFeed::getInstance()->addPhoto($photoFeed);
                            // create log
                            ActionLog::getInstance()->insert($this->arrLogin['id'],ActionLog::$add,ActionLog::$photo,$this->arrLogin['accountID'],$this->arrLogin['nickName'],' a new photo To "'.$sName.'" album' );
                            $i++;
                        }

                        //update total album
                        Album::getInstance()->updateAlbumTotal($iAlbumId, $i);

                    }else{
                        $error = 1;
                        //rollback (delete $iAlbumId)
                        Album::getInstance()->removeAlbum($iAlbumId);
                    }
                }

            }
            $output = array('error'=>$error);
            echo Zend_Json::encode($output);
            exit();

        }
    }
    
    /**
     * Default action
     */
    public function deleteAction()
    {

        $this->_helper->layout()->disableLayout();

        $error = 0;
        $iFeedId = 0;
        $iStart = 0;
        $iEnd = MAX_QUERY_LIMIT;

        if($this->_request->isPost()) {
            $arrFeed = array();

            $iAlbumId = $this->_getParam('album_id', 0);
            $iGroupId = $this->_getParam('group_id', 0);
//                var_dump($params['data']);
            if ($iAlbumId == 0 || $iGroupId == 0) {
                $output = array('error' => 1, 'message' => 'group or album is not exist');
                echo Zend_Json::encode($output);
                exit();
            }

            //get album
            $arrAlbum = Album::getInstance()->getAlbumByID($iAlbumId);
            $arrGroup = Group::getInstance()->getGroupByID($iGroupId);

            //validate album groupid
            if (empty($arrAlbum) || empty($arrGroup)) {
                $output = array('error' => 1, 'message' => 'group or album is not exist');
                echo Zend_Json::encode($output);
                exit();
            }
            //get list photo
            $arrPhotoFeeds = PhotoFeed::getInstance()->getPhotoFeedListByGroupIdAndAlbumId($iGroupId, $iAlbumId, $iStart, $iEnd);

            if (!empty($arrPhotoFeeds)) {

                $arrFeedIds = array();

                foreach ($arrPhotoFeeds as $photo) {

                    // create log
                    ActionLog::getInstance()->insert($this->arrLogin['id'], ActionLog::$delete, ActionLog::$photo, $this->arrLogin['accountID'], $this->arrLogin['nickName'], ' a photo Of "' . $arrAlbum['name'] . '" album');
                    // process photo and name
                    $photo = Core_Common::photoProcess($photo);
                    $fileName = trim($photo['image_tag']);
                    // delete file
                    if (!empty($fileName))
                        Core_Image::delete($fileName);

                    $arrFeedIds[] = $photo['feed_id'];
                    //delete photo_feed
                    PhotoFeed::getInstance()->removePhoto($photo['photo_id'], $photo['group_id'], $photo['album_id'], $photo['feed_id']);
                }

                if (!empty($arrFeedIds)) {
                    $arrFeedIds = array_unique($arrFeedIds);
                }
                //delete feed
                foreach ($arrFeedIds as $feedId) {
                    $total = PhotoFeed::getInstance()->countPhotoFeedIDByFeedId($feedId);
                    if ($total == 0) {
// create log
                        ActionLog::getInstance()->insert($this->arrLogin['id'], ActionLog::$delete, ActionLog::$feed, $this->arrLogin['accountID'], $this->arrLogin['nickName'], ' a feed ' . $feedId);
                        //delete feed
                        Feed::getInstance()->delete($feedId, $iGroupId);
                    }
                }

                //delete album
                $total = PhotoFeed::getInstance()->countPhotoFeedIDByGroupIdAndAlbumId($iGroupId, $iAlbumId);
                if ($total == 0) {
                    // create log
                    ActionLog::getInstance()->insert($this->arrLogin['id'], ActionLog::$delete, ActionLog::$album, $this->arrLogin['accountID'], $this->arrLogin['nickName'], ' "' . $arrAlbum['name'].'" album');

                    // process album and get path file album
                    $album = Core_Common::albumProcess($arrAlbum);
                    $fileName = $album['image_tag'];
                    // delete file album
                    if (!empty($fileName))
                        Core_Image::delete($fileName);

                    Album::getInstance()->removeAlbum($iAlbumId);
                }
            }

        }
        $output = array('error'=>$error);
        echo Zend_Json::encode($output);
        exit();


    }

    public function updatedetailAction()
    {

        $this->_helper->layout()->disableLayout();

        $error  = array('validate'=>false,'error'=>true,'message'=>'album not found.');
        $iAlbumId   = $this->_getParam('album_id',0);
        $album  = Album::getInstance()->getAlbumByID($iAlbumId);
        if(empty($album))
        {
            echo Zend_Json::encode($error);
            exit();
        }

        // update detail
        if($this->_request->isPost())
        {
            $name      = trim($this->_getParam('name',''));
            $content   = trim($this->_getParam('content',''));
            $error     = array('validate'=>true,'error'=>false,'validate_data'=>array(array('field'=>'name','message'=>'this field is required')));
            //validate data
            if(empty($name))
            {
                echo Zend_Json::encode($error);
                exit();
            }
            // create log
            ActionLog::getInstance()->insert($this->arrLogin['id'], ActionLog::$update, ActionLog::$album, $this->arrLogin['accountID'], $this->arrLogin['nickName'],' "' . $album['name'].'" album');

            $album['name'] = $name;
            $album['content'] = $content;
            Album::getInstance()->updateAlbum($album);
            echo Zend_Json::encode(array('validate'=>false,'error'=>false));
            exit();

        }


    }

    public function detailAction()
    {
        $iAlbumId   = $this->_getParam('album_id',0);
        $iGroupId   = $this->_getParam('group_id',0);
        $album  = Album::getInstance()->getAlbumByID($iAlbumId);

        if(empty($album))
            $this->_redirect(BASE_ADMIN_URL.'/album');


        $photos = PhotoFeed::getInstance()->getPhotoFeedListByGroupIdAndAlbumId($iGroupId,$iAlbumId,0,MAX_QUERY_LIMIT);

        $this->view->feedId = isset($photos[0]['feed_id']) ? $photos[0]['feed_id'] : '';
        $this->view->album  = $album;
        $this->view->photos = $photos;
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
}

