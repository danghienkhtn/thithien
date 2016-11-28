<?php
/**
 * @author      :   Workflow
 * @name        :   Model Workflow
 * @version     :   20130502
 * @copyright   :   My company
 * @todo        :   Product model
 */
class ErrorLog
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
        $this->_modeParent = Core_Business_Api_ErrorLog::getInstance();
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
    public function insert( $sModel, $sName, $sMessage, $iAccountID =0, $sAccountName = '',$sNote = '')
    {
    	$arrData = array(
    			"account_id" => $iAccountID,
    			"account_name" => $sAccountName,
    			"note" => $sNote,
    			"message" => $sMessage,
                "model"=> $sModel,
                "name"=> $sName,
    			"created" => new MongoDate()
    			 
    	);
    	
        //Get data
        $iID = $this->_modeParent->insert($arrData);
        //Return result data
        return $iID;
    }
    
    public function select($sModel, $sName, $sStartDate, $sEndDate, $iStart, $iLimit)
    {
    	return $this->_modeParent->select($sModel, $sName, $sStartDate, $sEndDate, $iStart, $iLimit);
    }
    
    
   

}
