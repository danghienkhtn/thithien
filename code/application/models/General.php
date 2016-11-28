<?php
/**
 * @author      :   HoaiTN
 * @name        :   Model General
 * @version     :   20130502
 * @copyright   :   My company
 * @todo        :   General model
 */
class General
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
    
    /*
     * Assign att when select DB
     */
    private static  $_generalAtt = array();
    
    
    public static $position = 1;
    public static $level = 2;
    public static $contract = 4;
    
    /**
     * Constructor of class
     * we don't permit an explicit call of the constructor! (like $v = new Singleton())
     */
    protected function __construct() {
        // Int Parent Model
        $this->_modeParent = Core_Business_Api_General::getInstance();        
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
    public function insertGeneral($sName, $iType, $iSortOrder, $iActive)
    {
        //Get data
        $iResult = $this->_modeParent->insertGeneral($sName, $iType, $iSortOrder, $iActive);
        //Return result data
        return $iResult;
    }
    
    /**
     * @todo  Update 
     
     * @return <int>
     */
    public function updateGeneral($iID, $sName, $iType, $iSortOrder, $iActive)
    {
        //Get data
        $iResult = $this->_modeParent->updateGeneral($iID, $sName, $iType, $iSortOrder, $iActive);
       
        //Return result data
        return $iResult;
    }
   
    
    /**
     * @todo  Remove GiveAway
     * @return <int>
     */
    public function removeGeneral($iID)
    {
        //Get data
        $iResult = $this->_modeParent->removeGeneral($iID);
       
        //Return result data
        return $iResult;
    }
    
    /**
     * @todo Get all Give Away
     * @return <array>
     */
    public function getGeneralList($sName,$iType, $iActive, $iOffset, $iLimit)
    {
        //Get data
        $arrResult = $this->_modeParent->getGeneralList($sName,$iType, $iActive, $iOffset, $iLimit);
       
        //Return result data
        return $arrResult;
    }
    
    
    /**
     * @todo Get all Give Away
     * @return <array>
     */
    public function getGeneralByID($iID) 
    {
        //Get data
        $arrResult = $this->_modeParent->getGeneralByID($iID);
       
        //Return result data
        return $arrResult;
    }
    
    
    /**
     * @todo Get all Give Away
     * @return <array>
     */
    public function getGeneralAtt($iType=0, $iActive=1, $iOffset=0, $iLimit=500) 
    {
        
        if(!empty(self::$_generalAtt))
        {
             return self::$_generalAtt;
        }
        
        //Get data
        $sName ='';
        $hashResult = array();
        
        
        //Get data
        $arrResult = $this->_modeParent->getGeneralList($sName,$iType, $iActive, $iOffset, $iLimit, 1);

        $hashResult[self::$position][0] = '';
        $hashResult[self::$contract][0] = '';
        $hashResult[self::$level][0]    = '';
        
        if(!empty($arrResult['data']))
        {
             foreach($arrResult['data'] as $value)
             {
                   switch ($value['type'])
                    {
                        	case self::$position:	
                                $hashResult[self::$position][$value['general_id']]= $value['name'];
                            	break;
                        
                            case self::$contract:
                            	$hashResult[self::$contract][$value['general_id']]= $value['name'];
                            	break;
                        
                        	case self::$level:
                            	$hashResult[self::$level][$value['general_id']]= $value['name'];
                            	break;
                       
                        default:
                          break;
                    }
                
             }
             
             self::$_generalAtt = $hashResult;
        }        
       
        //Return result data
        return $hashResult;
    }
    
    
    /**
     * @todo Get all Give Away
     * @return <array>
     */
    public function getGeneralAttHash($iType=0, $iActive=1, $iOffset=0, $iLimit=500) 
    {
        
        $sName ='';
        //Get data
        $hashResult = array();
        $hashResult[0] =  '';
        
        //Get data
        $arrResult = $this->_modeParent->getGeneralList($sName,$iType, $iActive, $iOffset, $iLimit);

        if(!empty($arrResult['data']))
        {
             foreach($arrResult['data'] as $value)
             {
                   $hashResult[$value['general_id']] =  $value['name'];     
             }
        }        
       
        //Return result data
        return $hashResult;
    }
    
    public function selectByNameAndType($sName,$iType) {
    	return $this->_modeParent->selectByNameAndType($sName,$iType);
    }
      
}
