<?php
/**
 * @author      :   HienND
 * @name        :   Model Attribute
 * @version     :   201611
 * @copyright   :   DAHI
 * @todo        :   General model
 */
class Attribute
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
        $this->_modeParent = Core_Business_Api_Attribute::getInstance();        
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
    public function insertAttribute($sName, $iType, $iSortOrder, $iActive)
    {
        //Get data
        $iResult = $this->_modeParent->insertAttribute($sName, $iType, $iSortOrder, $iActive);
        //Return result data
        return $iResult;
    }
    
    /**
     * @todo  Update 
     
     * @return <int>
     */
    public function updateAttribute($iID, $sName, $iType, $iSortOrder, $iActive)
    {
        //Get data
        $iResult = $this->_modeParent->updateAttribute($iID, $sName, $iType, $iSortOrder, $iActive);
       
        //Return result data
        return $iResult;
    }
   
    
    /**
     * @todo  Remove GiveAway
     * @return <int>
     */
    public function removeAttribute($iID)
    {
        //Get data
        $iResult = $this->_modeParent->removeAttribute($iID);
       
        //Return result data
        return $iResult;
    }
    
    /**
     * @todo Get all Give Away
     * @return <array>
     */
    public function getAttributeList($sName, $iActive, $iOffset, $iLimit)
    {
        //Get data
        $arrResult = $this->_modeParent->getAttributeList($sName, $iActive, $iOffset, $iLimit);
       
        //Return result data
        return $arrResult;
    }
    
    
     /**
     * @todo Get all Give Away
     * @return <array>
     */
    public function getAttributeByID($iAttributeID)
    {
        //Get data
        $arrResult = $this->_modeParent->getAttributeByID($iAttributeID);
       
        //Return result data
        return $arrResult;
    }
     
      
}
