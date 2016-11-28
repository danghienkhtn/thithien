<?php
/**
 * @author      :   Workflow
 * @name        :   Model Workflow
 * @version     :   20130502
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
    public static $create = 1;
    public static $update = 2;
    public static $delete = 3;
    public static $add    = 4;


    // action log type
    public static $news = 1;
    public static $photo = 2;
    public static $profile = 3;
    public static $comment = 4;
    public static $like = 5;
    public static $user = 6;
    public static $project_member = 7;
    public static $project = 8;
    public static $group = 9;

    public static $general = 10;
    public static $leave_application = 11;
    public static $feed = 12;
    public static $album = 13;
    public static $specialDay = 14;
    public static $admin = 15;
    public static $absence = 16;
    public static $attendance = 17;

    public static $group_member = 18;
    public static $mail = 18;
    public static $overtime = 19;

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
    public function insert($iID, $iAction, $iType, $iAccountID, $sAccountName, $sNote = '')
    {
    	$arrData = array(
    			"id" => $iID,
    			"action" => $iAction,
    			"type" => $iType,
    			"account_id" => $iAccountID,
    			"account_name" => $sAccountName,
    			"note" => Validate::encodeValues($sNote,true,false),
    			"created" => new MongoDate()
    			 
    	);
    	
        //Get data
        $iID = $this->_modeParent->insert($arrData);
        //Return result data
        return $iID;
    }
    
    public function select($sAccountName, $sStartDate, $sEndDate, $iType, $iAction, $iStart, $iLimit)
    {
    	return $this->_modeParent->select($sAccountName, $sStartDate, $sEndDate, $iType, $iAction, $iStart, $iLimit);
    }

    public function selectById($sId)
    {
        return $this->_modeParent->selectById($sId);
    }
    
    public function getActionLog($sAccountId, $iType, $iAction, $iStart, $iLimit)
    {
        return $this->_modeParent->getActionLog($sAccountId, $iType, $iAction, $iStart, $iLimit);
    }
   

}
