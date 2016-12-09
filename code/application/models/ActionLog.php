<?php
/**
 * @author      :   Hiennd
 * @name        :   Model Workflow
 * @version     :   20161207
 * @copyright   :   My company
 * @todo        :   Product model
 */
class ActionLog
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



    // action log action
    public static $create = "create";
    public static $update = "update";
    public static $delete = "delete";
    public static $add    = "add";
    public static $approved    = "approved";


    // action log type
    public static $news = "post_news";
    public static $photo = "";
    public static $profile = "profile";    

    /**
     * Constructor of class
     * we don't permit an explicit call of the constructor! (like $v = new Singleton())
     */
    protected function __construct() {
        // Int Parent Model
        $this->_modeParent = Core_Business_Api_ActionLog::getInstance();        
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
    public function insert($iAction, $iType, $iAccountID, $sNote = '')
    {
        $iCurDate = time();
    	$arrData = array(
    			"log_action" => $iAction,
    			"log_type" => $iType,
    			"log_account_id" => $iAccountID,
    			"log_ip" => $_SERVER['REMOTE_ADDR'],
    			"log_note" => Validate::encodeValues($sNote,true,false),
    			"log_useragent" => $_SERVER['HTTP_USER_AGENT']    			 
    	);
    	
        //Get data
        $iID = $this->_modeParent->insert($arrData);
        //Return result data
        return $iID;
    }
    
    public function count($sAccountID, $sStartDate, $sEndDate, $sType, $sAction)
    {
    	return $this->_modeParent->select($sAccountID, $sStartDate, $sEndDate, $sType, $sAction);
    }

    public function select($sAccountID, $sStartDate, $sEndDate, $sType, $sAction, $iOffset = 0, $iLimit = 20)
    {
        return $this->_modeParent->select($sAccountID, $sStartDate, $sEndDate, $sType, $sAction, $iOffset, $iLimit);
    }

}
