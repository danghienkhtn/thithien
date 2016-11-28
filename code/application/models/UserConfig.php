<?php
/**
 * UserConfig model
 *
 * @category  Model
 * @package   UserConfig
 * @author    Tai Le Thanh <tai.lt@vn.gnt-global.com>
 * @copyright 2015 Gianty
 * @license   http://www.gianty.com.vn CIO Team
 * @link      http://svn.sgcharo.com/svn/portal/sourcecode
 */
class UserConfig
{
    /**
    * @var object $_modeParent parent model instance
    */
    private $_modeParent        = null;    

    /**
    * @var object $_modeParent parent model instance
    */
    protected static $_instance = null;

    /**
    * Constructor
    *
    * return none
    * @author Tai Le Thanh <tai.lt@vn.gnt-global.com>
    */
    protected function __construct()
    {
        $this->_modeParent = Core_Business_Api_UserConfig::getInstance();        
    }

    /**
    * Make new one instance for every object
    *
    * @return Calendar Return calendar model
    * @author Tai Le Thanh <tai.lt@vn.gnt-global.com>
    */
    public final static function getInstance()
    {
        // Check Instance
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        // Return Instance
        return self::$_instance;
    }
    
    public function getUserConfigByKey($data)
    {
        return $this->_modeParent->getUserConfigByKey($data);
    }    
    public function addUserConfig($data)
    {
        return $this->_modeParent->addUserConfig($data);
    }    
    public function updateUserConfig($data)
    {
        return $this->_modeParent->updateUserConfig($data);
    } 
    
    /*
     *  $data = array(
            'accountId' => $accountId,
            'key' => userConfigKey, //define key define.php
            'value' => $sLanguage, //new value
        );
     */
    public function writeUserConfig($data)
    {
        $userConfig = $this->_modeParent->getUserConfigByKey($data);

        if (isset($userConfig['user_config_id'])) {
            $updateData = array(
                'userConfigId' => $userConfig['user_config_id'],
                'accountId' => $userConfig['account_id'],
                'key' => $userConfig['key'],
                'value' => $data['value'],                
            );
            
            return $this->_modeParent->updateUserConfig($updateData);
        }

        return $this->_modeParent->addUserConfig($data);        
    }
}