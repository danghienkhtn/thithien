<?php
/**
 * @author      :   HoaiTN
 * @name        :   IndexController
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   controller default 
 */
class PhotoController extends Core_Controller_Action
{
    
	private $_urlImage = '';
	private $arrLogin;
     public function init() 
     {
        parent::init();
        
        global $globalConfig;
         $this->arrLogin = $this->view->arrLogin;
        //Get Controller
        $controller = $this->_request->getParam('controller');
        $this->view->controller = $controller;
        $this->arrLogin = $this->view->arrLogin;
        
        $this->_urlImage = PATH_IMAGES_URL.'/original';
        //$accountIns = Ldap::getInstance();
       
       //Will get data from active directory
       // $arrAccountLdap = $accountIns->getAccountInfo('billsama');
        //var_dump($arrAccountLdap); exit;           
        
     }

     /**
     * Default action
     */
    public function indexAction()
    {
    	global $globalConfig;
    	
        //int param
        $iPage = $this->_request->getParam('page', 1);
        $sKeyword = '';
        $iTypeCompany = $globalConfig['album_type']['company'];
//        $iTypeTeam = $globalConfig['album_type']['team'];
        $iYear = $this->_request->getParam('year',0);
      
        
        //result
        $arrResult = array();
        
        //check 
        $iActive = 1;
        $iTotal = 0;
        
        //check pagesize
       $iPageSize = 50;
       $iStart = 0;
    
       //init instance account
//        $instanceAlbum= Album::getInstance();
                     
        //get abbum company
        $arrAlbumCompany = Album::getInstance()->getAlbumList($sKeyword, $iYear, $iTypeCompany,$iActive, 0, $iStart, 4);
//        $arrAlbumCompany = isset($arrAlbumCompany['data']) ? $arrAlbumCompany['data'] : array();


		$arrResult = GroupMember::getInstance()->getGroupMemberByMemberId($this->arrLogin['accountID'],0,MAX_QUERY_LIMIT);
        $arrMyGroups = $arrGroupMember = $arrResult['data'];

//        echo '<pre>';
//        var_dump($arrMyGroups);
//        echo '</pre>';die;
        $iLimitAlbumGroup = 4;
        $iTypeTeam  = 0; // get all team
        $AlbumGroup = Album::getInstance()->getAlbumGroup($arrMyGroups, $sKeyword, $iYear, $iTypeTeam, $iActive, $iStart, $iLimitAlbumGroup);

        //Assign view

        $this->view->arrGroupMember  = $arrMyGroups;
        $this->view->arrAlbumCompany  = $arrAlbumCompany;
        $this->view->arrTotalAlbum  = $AlbumGroup['arrTotalAlbum'];
        $this->view->arrAlbumTeam  = $AlbumGroup['arrAlbumTeam'];
        $this->view->urlImage = PATH_IMAGES_URL . '/original/';
        $this->view->typeCompany = $iTypeCompany;
        $this->view->typeTeam = $iTypeTeam;
        $this->view->groupTypes = array_flip($globalConfig['group_type']);

    }
    
    public function albumdetailAction()
    {
    	global $globalConfig;
    	
    	$iStart = $this->_request->getParam('offset', 1);
    	$iEnd = $this->_request->getParam('number', ADMIN_PAGE_SIZE);
    	$iAlbumId = $this->_request->getParam('a_id', 0);
    	$iGroupId = $this->_request->getParam('g_id', 0);

    	//check is my group
//    	$isExist = Group::getInstance()->isMyGroup($this->view->arrGroupMember, $iGroupId);

    	
//    	if(!$isExist || $iAlbumId == 0){
//    		$this->_redirect('photo/index');
//    		exit();
//    	}
    	
    	
    	$arrPhotos = array();
    	$iTotal = 0;
    	
    	//get name album
    	$arrAlbum = Album::getInstance()->getAlbumByID($iAlbumId);

//    	if(empty($arrAlbum)){
//    		$this->_redirect('photo/index');
//    		exit();
//    	}
    	
//    	$arrPhotos = PhotoFeed::getInstance()->getPhotoFeedListByGroupIdAndAlbumId($iGroupId, $iAlbumId, $iStart*ADMIN_PAGE_SIZE, $iEnd);

//        $keyCaching = Core_Global::getKeyPrefixCaching(REDIS_GROUP_ALBUM_PHOTO_FEED_KEY) . $iGroupId .':' .$iAlbumId;
//        $iTotal = Core_Business_Nosql_Redis::getInstance()->getListTotal($keyCaching);

//        Core_Common::var_dump($arrPhotos);
    	$arrPhotoFeedIds = PhotoFeed::getInstance()->getPhotoFeedIDByGroupIdAndAlbumId($iGroupId, $iAlbumId, $iStart, $iEnd);
//        Core_Common::var_dump($arrPhotoFeedIds);
    	$this->view->albumName = $arrAlbum['name'];
    	$this->view->album = $arrAlbum;
    	$this->view->albumId = $iAlbumId;
    	$this->view->groupId = $iGroupId;
//    	$this->view->feedId = isset($arrPhotos[0]['feed_id']) ? $arrPhotos[0]['feed_id'] : '';
//    	$this->view->photos = $arrPhotos;
//    	$this->view->totalPhotos = $iTotal;
//    	$this->view->photoIds = join(",",$arrPhotoFeedIds);
//    	$this->view->url = $this->_urlImage;
    	/*
    	if($iType == $globalConfig['album_type']['company']){
	    	$arrAlbum = Album::getInstance()->getAlbumByID($iAlbumId);
	    	$arrResults = Photo::getInstance()->getPhotoByAlbumId($iAlbumId, $iStart, $iEnd);
	      	
	    	if($arrResults['total'] > 0){
	    		$arrPhotos = $arrResults['data'];
	    		$iTotal = $arrResults['total'];
	    	}
	    	
	    	$this->view->photos = $arrPhotos;
	    	$this->view->albumName = $arrAlbum['name'];
	    	$this->view->url = PATH_NEWS_URL;
    	}else{//photo of team
    		
    		$iFeedId = $iAlbumId;
    		//album_id is feed_id
    		$arrPhotos = PhotoFeed::getInstance()->getPhotoFeedListByFeedId($iFeedId, $iStart, $iEnd);
    		$feed = Feed::getInstance()->getFeedById($iFeedId);
    		
    		//get photofeed ids by feed id
    		$arrPhotoFeedIds = PhotoFeed::getInstance()->getPhotoFeedIDByFeedId($iFeedId, $iStart, $iEnd);

    		$this->view->albumName = substr($feed['message'], 0, 100);
    		$this->view->feedId = $iFeedId;
    		$this->view->photos = $arrPhotos;
    		$this->view->photoIds = join(",",$arrPhotoFeedIds);
    		$this->view->type = $iType;
    		$this->view->url = $this->_urlImage;
    		
    	}
    	*/
    	
    }
    
    public function scrollpaginationAction(){
    	 
    	//Disable layout
    	$this->_helper->layout()->disableLayout();
    	 
    	$arrResults = array();
    	 
    	 
    	if($this->_request->isPost())
    	{
    		$params = $this->_request->getPost();
    
    		$iStart = is_numeric($params['offset']) ? $params['offset'] : 0;
    		$iEnd = is_numeric($params['number']) ? $params['number'] : 9;
    		$iAlbumId = !empty($params['albumId']) ? $params['albumId'] : 0;
    		$iType = $this->_request->getParam('type', 0);
    		
    		$arrResults = Photo::getInstance()->getPhotoByAlbumId($iAlbumId, $iStart, $iEnd);
       	}
    	 
    	$output = array('photos'=>$arrResults);
    	echo Zend_Json::encode($output);
    	exit();
    }
    
    
    /**
     * Default action
     */
    public function listAction()
    {
        //int param
        $iPage = $this->_request->getParam('page', 1);
        $iAlbumID = $this->_request->getParam('id',0);
        
         //result
        $arrResult = array();
        $arrAlbumOther = array();
        
        //check 
        $isCheck =0;
        $iActive = 1;
        $iTotal =0;
        $arrAlbum = array();
        $iMore =0;
        
        //check pagesize
        $iPageSize = ADMIN_PAGE_SIZE;
        
        $iStart = ($iPage - 1) * $iPageSize;
       
        
        if($iAlbumID>0)
        {
             //get Instance
             $instanceAlbum = Album::getInstance();
             
             $arrAlbum = $instanceAlbum->getAlbumByID($iAlbumID);
             
             if(!empty($arrAlbum))
             {
                 //get Type
                 $iType = $arrAlbum['type'];
                 
                 //get year
                 $iYear = date('Y', time());
                 
                //init instance account
                 $instancePhoto= Photo::getInstance();


                 //get result
                 $arrResult = $instancePhoto->getPhotoList($iAlbumID, $iActive, $iStart, $iPageSize);
                 $iTotal = isset($arrResult['total'])?$arrResult['total']:0;
                 $arrResult = isset($arrResult['data'])?$arrResult['data']:array();
                 
                 if(count($arrResult)<$iTotal)
                 {
                     $iMore =1;
                 }
                 
                 //Get other Album
                 $arrAlbumOther = $instanceAlbum->getAlbumList('', $iYear, $iType,$iActive, 0, 20);
                 $arrAlbumOther = isset($arrAlbumOther['data'])?$arrAlbumOther['data']: array();

             } 
            
        }
         
        
        //Assign view
        $this->view->paginator     = $arrResult;
        $this->view->iPage         = $iPage;
        $this->view->iTotal        = $iTotal;
        $this->view->arrAlbumOther = $arrAlbumOther;
        $this->view->iType         = $iType;
        $this->view->iMore         = $iMore;
        $this->view->arrAlbum     = $arrAlbum;
        
    }
     
    /**
     * Default action
     */
    public function phototypeAction()
    {
        //int param
        $iType = $this->_request->getParam('type',1);
        $iYear = $this->_request->getParam('year',date('Y', time()));
     
        //check 
        $iActive = 1;
        $sKeyword ='';
        
        //check pagesize
        $iPageSize = 50;
        $iStart = 0;
        
    
       //init instance account
        $instanceAlbum= Album::getInstance();
                     
        //get result
        $arrResult = $instanceAlbum->getAlbumList($sKeyword, $iYear, $iType,$iActive, $iStart, $iPageSize);

        $arrResult = isset($arrResult['data'])?$arrResult['data']:array();


        //Assign view
        $this->view->paginator  = $arrResult;
        $this->view->iType      = $iType;
        
        echo $this->view->render('photo/phototype.phtml');
        
        exit; 
    }

    public function showMoreAction()
    {

        $this->_helper->layout()->disableLayout();
        $iAlbumId = $this->_getParam('a_id',0);
        $iGroupId = $this->_getParam('g_id',0);
        $iOffset = $this->_getParam('offset',0);
        $iLimit = $this->_getParam('limit',ADMIN_PAGE_SIZE);

        $iOffsetTmp = ($iOffset > 0) ? $iOffset*$iLimit+1 : 0;
        $photos = PhotoFeed::getInstance()->getPhotoFeedListByGroupIdAndAlbumId($iGroupId, $iAlbumId,$iOffsetTmp, $iLimit);
        $arrPhotoFeedIds = PhotoFeed::getInstance()->getPhotoFeedIDByGroupIdAndAlbumId($iGroupId, $iAlbumId, $iOffset, $iLimit);

        $this->view->photoIds = join(",",$arrPhotoFeedIds);
        $this->view->photos = $photos;
        $this->view->albumId = $iAlbumId;
        $this->view->groupId = $iGroupId;

    }
    
    /**
     * Photo list
     */
    public function photolistAction()
    {
        
          //Disable and render
        $this->_helper->layout()->disableLayout();
        
        //int param
        $iPage = $this->_request->getParam('page', 1);
        $iAlbumID = $this->_request->getParam('id',0);
        $iPageSize = ADMIN_PAGE_SIZE;
        
        $iStart = ($iPage -1)*$iPageSize;
        $iMore =0;
        
        //check 
        $iActive = 1;


        $arrResult = Photo::getInstance()->getPhotoList($iAlbumID, 1, $iStart, $iPageSize);
        
        $iTotal= isset($arrResult['total'])?$arrResult['total']:0;
        $arrResult = isset($arrResult['data'])?$arrResult['data']:array();
        
        //Count result
        $iCount = count($arrResult);

        //Check more
        if($iStart + $iCount< $iTotal)
        {
            $iMore =1;
        }
        
        //Get html view
        $htmlView = new Zend_View();

         //Set script path
        $htmlView->setScriptPath(APPLICATION_PATH . '/modules/default/views/scripts/photo/');
            
         
        //Assign view
         $htmlView->paginator  = $arrResult;

         //Load default configuration for this view
         Core_Global::addToDefaultView($htmlView, "default");

         $sContent = $htmlView->render('photolist.phtml');

            //Set response html
         $arrRespone = array(
                'data'       => $sContent,
                'page'       => $iPage + 1,
                'more'       => $iMore
          );

         //Send data
         echo Zend_Json::encode($arrRespone);

         //Exit render
         exit();
    }
    
    public function createalbumAction()
    {
    	$this->_helper->layout()->disableLayout();
    	
    	$error = 0;
    	$iFeedId = 0;
    	 
    	if($this->_request->isPost())
    	{
    		$arrFeed = array();
    		$params = $this->_request->getPost();
    		
    		if(!empty($params['data'])){
    			
	    		$arrDatas = json_decode($params['data'], true);
//	    		Core_Common::var_dump($arrDatas);
	    		//Feed
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


	    		//album
	    		$group = Group::getInstance()->getGroupByID($arrDatas['team_id_to']);
	    		$sName = trim($arrDatas['name']);
	    		$sContent = $arrDatas['desc'];
	    		$sImage = $sLocation = '';
	    		$iEventDate = time();
	    		$iAccountID = $this->arrLogin['accountID'];
	    		$iActive = 1;
	    		$iYear = date("Y");
	    		$iType = !empty($group) ? $group['group_type'] : 0;
	    		
	    		//image
	    		if(!empty($arrDatas['images'])){
	    			$arrFeed = $this->setFile($arrFeed, $arrDatas['images'], 'image_url');
	    			$sImage = $arrDatas['images'][0]['name'];
	    		}else{
	    			
	    			$output = array('error'=>1, 'message'=>'photo is empty');
	    			echo Zend_Json::encode($output);
	    			exit();
	    		}

                if($sName == '')
                {
                    $output = array('error'=>1, 'message'=>'album name not empty');
                    echo Zend_Json::encode($output);
                    exit();
                }
	    		//insert Album
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
                    'is_other' => 0
                );

                if($Album['group_id'] == 10) // 10 is group all gianty
                {
                    $output = array('error'=>1, 'message'=>'not allow group all gianty');
                    echo Zend_Json::encode($output);
                    exit();
                }
                $iAlbumId = Album::getInstance()->addAlbum($Album);

//                Core_Common::var_dump($iAlbumId);
	    		if($iAlbumId > 0){
		    		//insert Feed
		    		$iFeedId = Feed::getInstance()->insert($arrFeed);
		    
		    		if($iFeedId > 0){
		    			$i = 0;
		    			//insert photo
		    			foreach ($arrDatas['images'] as $image){
                            $photoFeed = array(
                                'image_url' => $image['name'],
                                'original_name' => $image['original_name'],
                                'feed_id' => $iFeedId,
                                'album_id' => $iAlbumId,
                                'active' => 1,
                                'group_id' => $arrFeed['team_id_to'],
                                'account_id' => $arrFeed['account_id'],
                                'message' => $arrDatas['descImages'][$i]
                            );
		    				PhotoFeed::getInstance()->addPhoto($photoFeed);
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
    
    public function createphotoAction()
    {
    	$this->_helper->layout()->disableLayout();
    	 
    	$error = 0;
    	$iFeedId = 0;
        $message = '';
        
    	if($this->_request->isPost())
    	{
    		$arrFeed = array();
    		$params = $this->_request->getPost();
//            Core_Common::var_dump($params);
    		if(!empty($params['data'])){
    			 
    			$arrDatas = json_decode($params['data'], true);
    	   
    			$group = array();
    			
    			if(isset($arrDatas['team_id_to']) && !empty($arrDatas['team_id_to'])){
    				$group = Group::getInstance()->getGroupByID($arrDatas['team_id_to']);
    				
    				//check exist group
    				if(empty($group)){
    					$output = array('error'=>1, 'message'=>'group is not exist');
    					echo Zend_Json::encode($output);
    					exit();
    				}
    			}

    			//check team id
    			if(!isset($arrDatas['team_id_to']) || empty($arrDatas['team_id_to'])){
    				$arrAccount = AccountInfo::getInstance()->getAccountInfoByAccountID($this->arrLogin['accountID']);
    				$group = Group::getInstance()->getGroupByID($arrAccount['team_id']);
    				$arrDatas['team_id_to'] = $arrAccount['team_id'];
    				$arrDatas['teamName'] = $group['group_name'];
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
    			if(!empty($arrDatas['images'])){
    				$arrFeed = $this->setFile($arrFeed, $arrDatas['images'], 'image_url');
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
//                    die($arrDatas['team_id_to']);
    				if(empty($arrAlbumTmp)){
    					
	    				$isNewAlbum = 1;
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

//                        var_dump($sName,$sContent,$sImage,$sLocation, $iEventDate, $iAccountID, $iActive,$iYear,$iType, $arrDatas['team_id_to'], $iOther);die;
    				}else{
    					$iAlbumId = $arrAlbumTmp['album_id'];
    				}
    				
    			}else if(is_numeric($arrDatas['album_id'])){
    				
    				$iAlbumId = $arrDatas['album_id'];
    				$arrAlbum = Album::getInstance()->getAlbumByID($iAlbumId);

    				if(empty($arrAlbum)){
    					$output = array('error'=>1, 'message' => 'Album not exist.');
    					echo Zend_Json::encode($output);
    					exit();
    				}
    			}
    			
    			//check feed
    			if(!empty($arrDatas['feed_id']) && is_numeric($arrDatas['feed_id'])){
    				
    				$iFeedId = $arrDatas['feed_id'];
//                    Core_Common::var_dump($arrDatas['feed_id']);
    				$arrFeedTmp = Feed::getInstance()->getFeedById($iFeedId);
    				
    				if(empty($arrFeedTmp)){
    					$output = array('error'=>1, 'message' => 'Feed not exist.');
    					echo Zend_Json::encode($output);
    					exit();
    				}
    			}
    			
    			if($iAlbumId > 0){
    				
    				//insert Feed
    				if($iFeedId == 0){
	    				$iFeedId = Feed::getInstance()->insert($arrFeed);
    				}
    				
    				if($iFeedId > 0){
    					$i = 0;
    					//insert photo
    					foreach ($arrDatas['images'] as $image){
                            $photoFeed = array(
                                'image_url' => $image['name'],
                                'original_name' => $image['original_name'],
                                'feed_id' => $iFeedId,
                                'album_id' => $iAlbumId,
                                'active' => 1,
                                'group_id' => $arrFeed['team_id_to'],
                                'account_id' => $arrFeed['account_id'],
                                'message' => $arrDatas['message']
                            );
    						PhotoFeed::getInstance()->addPhoto($photoFeed);
    						$i++;
    					}
    						
    					//update total album
    					Album::getInstance()->updateAlbumTotal($iAlbumId, $i);
    						
    				}else{
    					$error = 1;
    					$message = 'Create Feed Error';
    					
    					//rollback (delete $iAlbumId)
    					if($isNewAlbum == 1){
    						Album::getInstance()->removeAlbum($iAlbumId);
    					}
    				}
    			}else{
    				$error = 1;
    				$message = 'Create Album Error';
    				//rollback (delete $iAlbumId)
    				if($isNewAlbum == 1){
    					Album::getInstance()->removeAlbum($iAlbumId);
    				}
    			}

    		}
    		$output = array('error'=>$error, 'message' => $message);
    		echo Zend_Json::encode($output);
    		exit();
    
    	}
    
    }
    
    public function deletealbumAction()
    {
    	$this->_helper->layout()->disableLayout();
    
    	$error = 0;
    	$iFeedId = 0;
    	$iStart = 0;
    	$iEnd = MAX_QUERY_LIMIT;
    	
    	if($this->_request->isPost())
    	{
    		$arrFeed = array();
    		$params = $this->_request->getPost();
    
    		if(isset($params['album_id']) && isset($params['group_id'])){
    
    			$iAlbumId = empty($params['album_id']) ? 0 : $params['album_id'];
    			$iGroupId = empty($params['group_id']) ? 0 : $params['group_id'];
    			
    			if($iAlbumId == 0 || $iGroupId == 0){
    				$output = array('error'=>1, 'message'=>'group or album is not exist');
    				echo Zend_Json::encode($output);
    				exit();
    			}
    			
    			//get album
    			$arrAlbum = Album::getInstance()->getAlbumByID($iAlbumId);
    			$arrGroup = Group::getInstance()->getGroupByID($iGroupId);
    			
    			//validate album groupid
    			if(empty($arrAlbum) || empty($arrGroup)){
    				$output = array('error'=>1, 'message'=>'group or album is not exist');
    				echo Zend_Json::encode($output);
    				exit();
    			}

    		    //get list photo 
    			$arrPhotoFeeds = PhotoFeed::getInstance()->getPhotoFeedListByGroupIdAndAlbumId($iGroupId, $iAlbumId, $iStart, $iEnd);
    			    
    			if(!empty($arrPhotoFeeds)){
    				
    				$arrFeedIds = array();
    				
    			    foreach ($arrPhotoFeeds as $photo){
    			    	
    			    	$arrFeedIds[] = $photo['feed_id'];
    			    	//delete photo_feed
    			    	PhotoFeed::getInstance()->removePhoto($photo['photo_id'], $photo['group_id'], $photo['album_id'],$photo['feed_id']);    			    		
    			    }
    			    
    			    if(!empty($arrFeedIds)){
    			    	$arrFeedIds = array_unique($arrFeedIds);
    			    }
    			    //delete feed
    			    foreach ($arrFeedIds as $feedId){
    			    	$total = PhotoFeed::getInstance()->countPhotoFeedIDByFeedId($feedId);
    			    	if($total == 0){
    			    		//delete feed
    			    		Feed::getInstance()->delete($feedId, $iGroupId, $this->arrLogin['accountID']);
    			    	}
    			    }
    			    
    			    //delete album
    			    $total = PhotoFeed::getInstance()->countPhotoFeedIDByGroupIdAndAlbumId($iGroupId, $iAlbumId);
    			    if($total == 0){
    			    	Album::getInstance()->removeAlbum($iAlbumId);
    			    }
    			}
    		}
    	
    		$output = array('error'=>$error);
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
    			$arrFeed[$value.$i] = $file['name'];
    			$arrFeed['original_name'.$i] = $file['original_name'];
    		}
    	}
    	 
    	return $arrFeed;
    	 
    }
    
}

