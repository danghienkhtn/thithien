<?php
/**
 * @author      :   Photo
 * @name        :   Model Product
 * @version     :   20130502
 * @copyright   :   My company
 * @todo        :   Product model
 */
class Photo
{
    /**
     * Parent instance
     * @var <object>
     */
    private $_modeParent        = null;    

    /**
     * @var type 
     */
    protected static $_instance = null;
    
    
    
    /**
     * Constructor of class
     * we don't permit an explicit call of the constructor! (like $v = new Singleton())
     */
    protected function __construct() {
        // Int Parent Model
        $this->_modeParent = Core_Business_Api_Photo::getInstance();        
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
     * @todo  Add new GiveAway
     */
//    public function addPhoto($sImage,$iAlbumID,$iHot, $iActive)
    public function addPhoto($arrData)
    {
        //Get data
        $iResult = $this->_modeParent->addPhoto($arrData);
        //Return result data
        return $iResult;
    }
    
     /**
     * @todo  Add new GiveAway
     */
    public function  updatePhotoByAlbumStatus($iAlbumID, $iActive)
    {
        //Get data
        $iResult = $this->_modeParent->updatePhotoByAlbumStatus($iAlbumID, $iActive);
        //Return result data
        return $iResult;
    }
    
    
         /**
     * @todo  Add new GiveAway
     */
    public function  updatePhotoStatus($iPhotoID, $iActive)
    {
        //Get data
        $iResult = $this->_modeParent->updatePhotoStatus($iPhotoID, $iActive);
        //Return result data
        return $iResult;
    }
    
    
    /**
     * @todo  Remove GiveAway
     * @return <int>
     */
    public function removePhoto($iID)
    {
        //Get data
        $iResult = $this->_modeParent->removePhoto($iID);
       
        //Return result data
        return $iResult;
    }
    
    /**
     * @todo Get all Give Away
     * @return <array>
     */
    public function getPhotoList($iAlbumID,$iActive, $iOffset, $iLimit)
    {
        //Get data
        $arrResult = $this->_modeParent->getPhotoList($iAlbumID,$iActive, $iOffset, $iLimit);
       
        //Return result data
        return $arrResult;
    }
    
    
    /**
     * @todo  Remove GiveAway
     * @return <int>
     */
    public function getPhotoByID($iID)
    {
        //Get data
        $iResult = $this->_modeParent->getPhotoByID($iID);
       
        //Return result data
        return $iResult;
    }
    
    public function countPhotoByAlbumId($iAlbumId)
    {
    	return $this->_modeParent->countPhotoByAlbumId($iAlbumId);
    }
   
    public function getPhotoByAlbumId($iAlbumID, $iOffset, $iLimit){
    	return $this->_modeParent->getPhotoByAlbumId($iAlbumID, $iOffset, $iLimit);
    }
}
