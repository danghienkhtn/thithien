<?php
/**
 * @author      :   Workflow
 * @name        :   Model Workflow
 * @version     :   20130502
 * @copyright   :   My company
 * @todo        :   Product model
 */
class File
{

	const FILE_DETAIL_KEY = 'file_detail_key';
	const FILE_DETAIL_EXPIRED = 'file_detail_expired';

	const FILE_LIST_KEY = 'file_list_key';
	
	const REDIS_FEED_FILE_LIST = 'redis_filefeed_list_key';
    /**
     * @var type 
     */
    protected static $_instance = null;
    
    private $_modeParent        = null;    

    
  
    /**
     * Constructor of class
     * we don't permit an explicit call of the constructor! (like $v = new Singleton())
     */
    protected function __construct() {
        // Int Parent Model
        $this->_modeParent = Core_Business_Api_File::getInstance();        
    }
 

    /**
     * Get singletom instance
     * @return <object>
     */
    public final static function getInstance()
    {        
        // Check Instance
        if(is_null(self::$_instance))
        {
            self::$_instance = new self();
        }

        // Return Instance
        return self::$_instance;
    }
    
    /**
     * insert file
     * @param string $sName
     * @param string $sPath
     * @param int $iType '0: file, 1: folder'
     * @param int $iParent '0: is parent'
     * @param int $iOwner 'creater'
     * @param int $iCreated
     * @param int $iUpdated
     * @return int id
     */
    public function insert($sName, $sPath, $iType, $iParent, $iOwner, $iGroupId, $iFeedId = 0, $sOriginalName = '', $isDocs = 0)
    {
    	$arrData = array(
    			"_id" => Core_Business_Api_NextId::getInstance()->nextValue("userid"),
                "original_name" => $sOriginalName,
    			"name" => $sName,
    			"path" => $sPath,
    			'type' => (int)$iType,
    			"parent" => (int)$iParent,
    			"owner" => (int)$iOwner,
    			"group_id" => (int)$iGroupId,
                "is_docs" => (int)$isDocs,
                "is_public" => 0,
    			"updated" => new MongoDate(),
    			"created" => new MongoDate()
    	
    	);

    	$iID = $this->_modeParent->insert($arrData);
    	
    	if($iID > 0){

    		$this->flushAllCache();
    		
    		$isInsert = 1;
    		//insert redis feed[list file id]
    		if($iFeedId > 0){
    			$keyRedis = Core_Global::getKeyPrefixCaching(self::REDIS_FEED_FILE_LIST) . $iFeedId;
    			$isInsert = Core_Business_Nosql_Redis::getInstance()->setList($keyRedis, $iID, time());
    		}
    		
    		//rollback
    		if($isInsert < 1){
    			$this->_modeParent->delete($iID);

    			if($iFeedId > 0){
    				Core_Common::deleteItemInListRedis(self::REDIS_FEED_FILE_LIST, $iFeedId, $iID);
    			}
    			
    			$iID = 0;
    		}
    	}
    	return $iID;
    }

    public function renameFile($fileId, $accountID, $name){       
        $query["_id"] = (int)$fileId;
        $query["owner"] = (int)$accountID;
        $query["type"] = 0;
        $update = array("name" => $name, "original_name" => $name, "updated" => new MongoDate());
        $this->flushAllCache();
        return $this->_modeParent->update($query, $update);
    }

    public function updatePublicFileStatus($fileId, $accountID, $iPublic = 0){       
        if($iPublic != 1) $iPublic = 0;

        $query["_id"] = (int)$fileId;
        $query["owner"] = (int)$accountID;        
        $update = array("is_public" => $iPublic);        
        return $this->_modeParent->update($query, $update);
    }

    public function renameFolder($folderId, $name){       
        $query["_id"] = (int)$folderId;
        /*$query["owner"] = (int)$accountID;*/
        $query["type"] = 1;
        $update = array("name" => $name, "original_name" => $name, "updated" => new MongoDate());
        $this->flushAllCache();
        return $this->_modeParent->update($query, $update);
    }

    public function updatePath($folderId, $iGroupId, $path){       
        $listChild = $this->selectAllByParent($folderId, $iGroupId);
        if(isset($listChild['total']) && $listChild['total'] > 0){
            foreach ($listChild['data'] as $File) {
                $query["_id"] = (int)$File['_id'];
                // $query["owner"] = (int)$iAccountId;        
                $update = array("path" => $path);                
                $this->_modeParent->update($query, $update);
                if($File['type'] == 1)//folder
                {
                    $this->updatePath($File['_id'], $iGroupId, $path.DIRECTORY_SEPARATOR.$File['name']);
                }                
            }    
        }        
        
    }

    public function updateWrongPath($folderRoot, $folderId, $iGroupId, $path){       
        $listChild = $this->selectAllByParent($folderId, $iGroupId);
        if(isset($listChild['total']) && $listChild['total'] > 0){
            foreach ($listChild['data'] as $File) {
                $query["_id"] = (int)$File['_id'];                
                $update = array("path" => $path);                
                $this->_modeParent->update($query, $update);
error_log('fileId='.$File['_id'].'&oldpath='.$File['path'].'&newpath='.$path);                
                if($File['type'] == 1)//folder
                {
                    if(!file_exists($folderRoot.$path.DIRECTORY_SEPARATOR.$File['name']))
                        Core_Helper::createNewFolder($folderRoot.$path.DIRECTORY_SEPARATOR.$File['name']);
                    $this->updateWrongPath($folderRoot, $File['_id'], $iGroupId, $path.DIRECTORY_SEPARATOR.$File['name']);
                }
                else{
                    $sourceFile = $folderRoot.$File['path'].DIRECTORY_SEPARATOR.$File['name'];
                    $targetFile = $folderRoot.$path.DIRECTORY_SEPARATOR.$File['name'];
error_log('source:'.$sourceFile.'&target:'.$targetFile);                    
                    if(file_exists($sourceFile) && !file_exists($targetFile))
                        @copy($sampleFile, $newFile);        
                }                
            }    
        }        
        
    }

    public function update($query,$file)
    {
        $this->flushAllCache();
        return $this->_modeParent->update($query,$file);
    }
    /**
     * add file to feed
     * int iFeedId
     * array FileIds
     */
    public function  addFileToFeed($iFeedId, $arrFileIds){
    	
    	//insert redis feed[list file id]
    	if($iFeedId > 0){
    		
    		$keyRedis = Core_Global::getKeyPrefixCaching(self::REDIS_FEED_FILE_LIST) . $iFeedId;
    		
    		foreach ($arrFileIds as $id){
    			Core_Business_Nosql_Redis::getInstance()->setList($keyRedis, $id, time());
    		}

    	}
    	
    }
    

    
    /**
     * select file by id
     * @param int $iID
     * @return array
     */
    public function selectOne($iID)
    {
        $query = array();
        $query['_id'] = (int)$iID;
        $arrResult = $this->_modeParent->selectOne($query);
        if(!empty($arrResult)) {
            $keyCaching = Core_Global::getKeyPrefixCaching(self::FILE_DETAIL_KEY) . $iID;
            $caching = Core_Global::getCacheInstance();
            $time = Core_Global::getKeyPrefixCaching(self::FILE_DETAIL_EXPIRED);
            $caching->write($keyCaching, $arrResult, $time);
        }

//     	//Get data from caching
//    	$arrResult = $caching->read($keyCaching);
//
//    	if (empty($arrResult)) {
//
//    		$query = array();
//    		$query['_id'] = (int)$iID;
//    		$arrResult = $this->_modeParent->selectOne($query);
//
//    		if (!empty($arrResult)) {
//
//    			$time = Core_Global::getKeyPrefixCaching(self::FILE_DETAIL_EXPIRED);
//    			$caching->write($keyCaching, $arrResult, $time);
//    		}
//    	}
//
    	return $arrResult;
    }
    
    /**
     * select file by Name, Type and groupid
     * @param int $iID
     * @return array
     */
    public function selectOneByNameAndTypeAndGroup($sName, $iType, $iGroupId, $iParent = 0)
    {
    	$query = array();
    	$query['name'] = $sName;
    	$query['type'] = (int)$iType;
    	$query['group_id'] = (int)$iGroupId;
    	
    	if($iParent > 0){
    		$query['parent'] = (int)$iParent;
    	}
    	return $this->_modeParent->selectOne($query);
    }
    
    /**
     * get file by group id
     * @param int $iStart
     * @param int $iLimit
     * @param int $iGroupId
     * @return array('total' => $iTotal, 'data' => $arrResult);
     */
    public function selectByGroupId($iStart, $iLimit, $iGroupId) {
    
    	$arrResult = array();
    	
    	$keyCaching = Core_Global::getKeyPrefixCaching(self::FILE_LIST_KEY) . $iGroupId . ':' . $iStart . ':' .$iLimit;
    	$caching = Core_Global::getCacheInstance();
    	
    	//Get data from caching
    	$arrResult = $caching->read($keyCaching);
    	
    	if (empty($arrResult)) {
    	
    		$arrResult = $this->select($iStart, $iLimit, '', array(), 0, $iGroupId);
    	
    		if (!empty($arrResult)) {
    	
    			$time = Core_Global::getKeyPrefixCaching(self::FILE_DETAIL_EXPIRED);
    			$caching->write($keyCaching, $arrResult, $time);
    		}
    	}

    	//Return result
    	return $arrResult;
    }
    
    /**
     * get file by owner
     * @param int $iStart
     * @param int $iLimit
     * @param int $iGroupId
     * @return array('total' => $iTotal, 'data' => $arrResult);
     */
    public function selectByOwner($iStart, $iLimit, $iAccountId) {
    
    	$arrResult = array();
    	 
    	$keyCaching = Core_Global::getKeyPrefixCaching(self::FILE_LIST_KEY) . 'account:' . $iAccountId . ':' . $iStart . ':' .$iLimit;
    	$caching = Core_Global::getCacheInstance();
    	 
    	//Get data from caching
    	$arrResult = $caching->read($keyCaching);
    	 
    	if (empty($arrResult)) {
    		 
    		$arrResult = $this->select($iStart, $iLimit, '', array(), 0, 0, $iAccountId);
    		 
    		if (!empty($arrResult)) {
    			 
    			$time = Core_Global::getKeyPrefixCaching(self::FILE_DETAIL_EXPIRED);
    			$caching->write($keyCaching, $arrResult, $time);
    		}
    	}
    
    	//Return result
    	return $arrResult;
    }
    
    /**
     * get file by owner and parent
     * @param int $iStart
     * @param int $iLimit
     * @param int $iGroupId
     * @return array('total' => $iTotal, 'data' => $arrResult);
     */
    public function selectByOwnerAndParent($iStart, $iLimit, $iAccountId, $iParent) {
    
    	$arrResult = array();
    
    	$keyCaching = Core_Global::getKeyPrefixCaching(self::FILE_LIST_KEY) . 'account:parent:' . $iAccountId . ':' . $iParent . ':' . $iStart . ':' .$iLimit;
    	$caching = Core_Global::getCacheInstance();
    
    	//Get data from caching
    	$arrResult = $caching->read($keyCaching);
    
    	if (empty($arrResult)) {
    		 
    		$arrResult = $this->select($iStart, $iLimit, '', array(), $iParent, 0, $iAccountId);
    		 
    		if (!empty($arrResult)) {
    
    			$time = Core_Global::getKeyPrefixCaching(self::FILE_DETAIL_EXPIRED);
    			$caching->write($keyCaching, $arrResult, $time);
    		}
    	}
    
    	//Return result
    	return $arrResult;
    }
    
    /**
     * get file by parent
     * @param int $iStart
     * @param int $iLimit
     * @param int $iGroupId
     * @return array('total' => $iTotal, 'data' => $arrResult);
     */
    public function selectByParent($iStart, $iLimit, $iParent) {
    
    	$arrResult = array();
    	 
    	$keyCaching = Core_Global::getKeyPrefixCaching(self::FILE_LIST_KEY) .'parent:' . $iParent . ':' . $iStart . ':' .$iLimit;
    	$caching = Core_Global::getCacheInstance();
    	 
    	//Get data from caching
    	$arrResult = $caching->read($keyCaching);
    	 
    	if (empty($arrResult)) {
    		 
    		$arrResult = $this->select($iStart, $iLimit, '', array(), $iParent, 0, 0);
    		 
    		if (!empty($arrResult)) {
    			 
    			$time = Core_Global::getKeyPrefixCaching(self::FILE_DETAIL_EXPIRED);
    			$caching->write($keyCaching, $arrResult, $time);
    		}
    	}
    
    	//Return result
    	return $arrResult;
    }

    public function getAllParent($fileId, &$arrParent)    
    {                
        // $arrRes = array();
        $arrChild = array();        
        $fileInfo = $this->selectOne($fileId);
        if($fileInfo){
            $arrChild['file_id'] = $fileInfo['_id'];
            $arrChild['file_name'] = $fileInfo['name'];
            $arrChild['group_id'] = $fileInfo['group_id'];
            $arrChild['parent_id'] = $fileInfo['parent'];
            array_unshift($arrParent, $arrChild);
            if($fileInfo['parent'] > 0)
                $this->getAllParent($fileInfo['parent'], $arrParent);
        }    
    }
            
    public function getFileBelongToFolder($fileId, $iGroupId, $iAccountId)    
    {                
        $arrRes = array();
        $arrChild = array();        
        $fileInfo = $this->selectOne($fileId);
        if($fileInfo){
            if($fileInfo['owner'] != $iAccountId){
                $arrRes["error"] = true;
                $arrRes["message"] = "Permission denied!";       
                $arrRes["body"] = $arrChild;         
            }            
            $this->getAllChild($fileId, $fileInfo['group_id'], $arrLogin['accountID'], $arrChild);
            if(sizeof($arrChild)>0){
                $arrRes["error"] = false;
                $arrRes["message"] = "OK";       
                $arrRes["body"]["data"] = $arrChild;
                $arrRes["body"]["total"] = sizeof($arrChild);                    
            }
            else{
                $arrRes["error"] = false;
                $arrRes["message"] = "have no child";       
                $arrRes["body"]["data"] = $arrChild;
                $arrRes["body"]["total"] = 0;
            }
        }    
        else{
            $arrRes["error"] = true;
            $arrRes["message"] = "File does not existed";       
            $arrRes["body"] = $arrChild;
        }        
        return $arrRes;
    }

    public function renameFolderDocs($folderId, $iGroupId, $name, $path)
    {
        $update = $this->renameFolder($folderId, $name);
        if($update){
            $this->updatePath($folderId, $iGroupId, $path);
            return true;                
        }
        return false;
    }

    public function getAllChild($iParent, $iGroupId, &$arrFileChild)
    {
        $arrFileChildTmp = $this->selectAllByParent($iParent, $iGroupId);
        if($arrFileChildTmp["total"] > 0){
            $arrFileChild = array_merge($arrFileChild, $arrFileChildTmp["data"]);
            foreach ($arrFileChildTmp["data"] as $fileInfo) {
                if($fileInfo["type"] == 1){//folder
                    $this->getAllChild($fileInfo["_id"], $iGroupId, $arrFileChild);
                }                    
            }
        }        
    }

    /**
     * get child file     
     * @param int $iParent
     * @return array('total' => $iTotal, 'data' => $arrResult);
     */
    public function selectAllByParent($iParentId, $iGroupId) {
    
        $arrResult = array();
         
        $arrResult = $this->selectAll($sName = '', $arrFileIds = array(), $iParentId, $iGroupId, $iAccountId = 0);         
    
        //Return result
        return $arrResult;
    }

    /**
     * get file by FeedId
     * @param int $iStart
     * @param int $iEnd
     * @param int $iGroupId
     * @return array('total' => $iTotal, 'data' => $arrResult);
     */
    public function selectByFeedId($iStart, $iEnd, $iFeedId) {

		$arrResult = array('total' => 0, 'data' => array());
    	$keyCaching = Core_Global::getKeyPrefixCaching(self::REDIS_FEED_FILE_LIST) . $iFeedId;
    
    	$iTotal = Core_Business_Nosql_Redis::getInstance()->getListTotal($keyCaching);
    
    	if ($iTotal > 0) {
    		$arrFileIds = Core_Business_Nosql_Redis::getInstance()->getListByScore($keyCaching, $iStart, $iEnd);

    		$arrFeed = $this->getFileDetailList($arrFileIds);
			$data = array();

    		if (!empty($arrFeed) && !empty($arrFileIds)) {

    			foreach ($arrFileIds as $value) {

    				if (!empty($arrFeed[$value])) {

    					$data[] = $arrFeed[$value];
    				}
    			}
    		}

    		$arrResult = array('total' => $iTotal, 'data' => $data);
    	}

    
    	//Return result
    	return $arrResult;
    }
    
    /**
     * get file list by array file ids
     * @param unknown $arrFeedIds
     * @return multitype:unknown
     */
    public function getFileDetailList($arrFileIds)
    {
    	//Constructor default array result
    	$arrResult = array();
    
    	//Init caching
    	$caching = Core_Global::getCacheInstance();
    
    	//Get prefix from configuration
    	$keyPrefixCaching = Core_Global::getKeyPrefixCaching(self::FILE_DETAIL_KEY);
    
    	//Get array key miss
    	$arrMissKey = array();
    	$iTotalFileId = count($arrFileIds);
    
    	if($iTotalFileId > 1){
    		//Add prefix caching for list key
    		array_walk($arrFileIds, 'Core_Global::addKeyPrefix', $keyPrefixCaching);
    	  
    	  
    		//Get data in cache
    		$arrResultCache = $caching->readMulti($arrFileIds);
    
    		if(!empty($arrResultCache))
    		{
    			//Loop to check data missing
    			foreach ($arrResultCache as $keyCaching => $arrDetail)
    			{
    				//Get AppID
    				$iFileID = str_replace($keyPrefixCaching, '', $keyCaching);
    				 
    				//Check cache
    				if (empty($arrDetail)){
    					//Add app_id to list missing cache
    					$arrMissKey[] = $iFileID;
    				}
    				else
    				{
    					$arrResult[$iFileID] = $arrDetail;
    				}
    				 
    			}
    		}
    	}else if($iTotalFileId > 0){
    		$arrMissKey[] = $arrFileIds[0];
    	}
    
    	//check miss key
    	if(!empty($arrMissKey))
    	{
    		//no search parent
    		$arrResultMiss = $this->select(0, MAX_QUERY_LIMIT, '', $arrMissKey, -1);
    		 
    		if($arrResultMiss['total'] > 0 && !empty($arrResultMiss['data']))
    		{
    			foreach($arrResultMiss['data'] as $value)
    			{
    
    				$arrResult[$value['_id']] = $value;
    			}
    		}
    	}
    
    	//Return result
    	return $arrResult;
    }
    
    /**
     * 
     * @param int $iStart
     * @param int $iLimit
     * @param string $sName
     * @param array $arrFileIds
     * return  array('total' => $iTotal, 'data' => $cursor);
     */
    public function selectAll($sName = '', $arrFileIds = array(), $iParentId = 0, $iGroupId = 0, $iAccountId = 0)
    {
        $query = array();
        $sort = array('type' => -1, 'created' => -1);
        
        //search name
        if(!empty($sName)){
            $query['name'] = new MongoRegex("/$sName/i");
        }
        

        //search ids
        if(!empty($arrFileIds)){
            $arrFileIds = Core_Common::convertArrStringToInt($arrFileIds);
            $query['_id'] = array('$in' => $arrFileIds);
        }
        
        //search parent id
        if($iParentId >= 0){
            $query['parent'] = (int)$iParentId;
        }
        
        //group id
        if($iGroupId != 0){
            $query['group_id'] = (int)$iGroupId;
        }
        
        //owner
        if($iAccountId > 0){
            $query['owner'] = (int)$iAccountId;
        }
                
        return $this->_modeParent->selectAll($query, $sort);
    }

    /**
     * 
     * @param int $iStart
     * @param int $iLimit
     * @param string $sName
     * @param array $arrFileIds
     * return  array('total' => $iTotal, 'data' => $cursor);
     */
    public function select($iStart, $iLimit, $sName = '', $arrFileIds = array(), $iParentId = 0, $iGroupId = 0, $iAccountId = 0)
    {
    	$query = array();
    	$sort = array('type' => -1, 'created' => -1);
    	
    	//search name
    	if(!empty($sName)){
    		$query['name'] = new MongoRegex("/$sName/i");
    	}
    	

    	//search ids
    	if(!empty($arrFileIds)){
    		$arrFileIds = Core_Common::convertArrStringToInt($arrFileIds);
    		$query['_id'] = array('$in' => $arrFileIds);
    	}
    	
    	//search parent id
    	if($iParentId >= 0){
    		$query['parent'] = (int)$iParentId;
    	}
    	
    	//group id
    	if($iGroupId != 0){
    		$query['group_id'] = (int)$iGroupId;
    	}
    	
    	//owner
    	if($iAccountId > 0){
    		$query['owner'] = (int)$iAccountId;
    	}
    	

    	
    	return $this->_modeParent->select($iStart, $iLimit, $query, $sort);
    }
    

    /**
     * 
     * @param int $iStart
     * @param int $iLimit
     * @param string $sName
     * @param array $arrFileIds
     * return  array('total' => $iTotal, 'data' => $cursor);
     */
    public function selectMyDocuments($iStart, $iLimit, $sName = '', $arrFileIds = array(), $iParentId = 0, $iGroupId = -1, $iAccountId = 0)
    {
        $query = array();
        $sort = array('type' => -1, 'created' => -1);
        
        //search name
        if(!empty($sName)){
            $query['name'] = new MongoRegex("/$sName/i");
        }
        

        //search ids
        if(!empty($arrFileIds)){
            $arrFileIds = Core_Common::convertArrStringToInt($arrFileIds);
            $query['_id'] = array('$in' => $arrFileIds);
        }
        
        //search parent id
        if($iParentId >= 0){
            $query['parent'] = (int)$iParentId;
        }
        
        //group id
        if($iGroupId != 0){
            $query['group_id'] = (int)$iGroupId;
        }
        
        //owner
        if($iAccountId > 0){
            $query['owner'] = (int)$iAccountId;
        }
        

        
        return $this->_modeParent->select($iStart, $iLimit, $query, $sort);
    }

    /**
     * delete file
     *
     * @param int $iID
     * @return boolean
     */
    public function delete($iID){
    	
    	$result = $this->_modeParent->delete((int)$iID);
    	$this->flushAllCache();
    	return $result;
    }
    
    /**
     * delete all note
     */
    public function deleteRecursive($iID) {
    	
    	$arrResult = $this->select(0, MAX_QUERY_LIMIT, '', array(), (int)$iID, 0, 0);
    	
    	if (!empty($arrResult) && $arrResult['total']>0) {
//            return arr
//    		foreach ($arrResult as $file){
//    			$this->deleteRecursive($file['_id']);
//    		}
    	}
    	$this->delete($iID);
    }
    
    public function flushAllCache()
    {
    
    	$Config = Core_Global::getApplicationIni();
    
    	$m = new Memcached();
    
    	$host = $Config->caching->statistic->server->memcachedv1->host;
    	$post = $Config->caching->statistic->server->memcachedv1->post;
 
    	$m->addServer($host, $post);
 
    	$m->flush();
    }

    /**
     * get file by parent
     * @param int $iStart
     * @param int $iLimit
     * @param int $iOwner
     * @param int $iParent
     * @return array('total' => $iTotal, 'data' => $arrResult);
     */
    public function selectOwnerByParent($iStart, $iLimit, $iOwner, $iParent, $iGroup = -1) {
    
        $arrResult = array();
         
        $keyCaching = Core_Global::getKeyPrefixCaching(self::FILE_LIST_KEY) .'MyDocument:' . $iOwner . ":" . $iParent . ':' . $iStart . ':' .$iLimit;
        $caching = Core_Global::getCacheInstance();
         
        //Get data from caching
        $arrResult = $caching->read($keyCaching);
         
        if (empty($arrResult)) {
             
            $arrResult = $this->select($iStart, $iLimit, '', array(), $iParent, $iGroup, $iOwner);
             
            if (!empty($arrResult)) {
                 
                $time = Core_Global::getKeyPrefixCaching(self::FILE_DETAIL_EXPIRED);
                $caching->write($keyCaching, $arrResult, $time);
            }
        }
    
        //Return result
        return $arrResult;
    }

    /**
     * get file by parent
     * @param int $iStart
     * @param int $iLimit
     * @param int $iOwner
     * @param int $iParent
     * @return array('total' => $iTotal, 'data' => $arrResult);
     */
    public function selectMyDocumentsByParent($iStart, $iLimit, $iOwner, $iParent, $iGroup = -1) {
    
        $arrResult = array();
         
        $keyCaching = Core_Global::getKeyPrefixCaching(self::FILE_LIST_KEY) .'MyDocumentByParent:' . $iOwner . ":" . $iParent . ':' . $iStart . ':' .$iLimit;
        $caching = Core_Global::getCacheInstance();
         
        //Get data from caching
        $arrResult = $caching->read($keyCaching);

        if (empty($arrResult)) {
             
            $arrResult = $this->selectMyDocuments($iStart, $iLimit, '', array(), $iParent, $iGroup, $iOwner);
             
            if (!empty($arrResult)) {
                 
                $time = Core_Global::getKeyPrefixCaching(self::FILE_DETAIL_EXPIRED);
                $caching->write($keyCaching, $arrResult, $time);
            }
        }
    
        //Return result
        return $arrResult;
    }

}
