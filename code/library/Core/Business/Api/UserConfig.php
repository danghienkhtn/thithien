<?php

/**
 * UserConfig API
 *
 * @category  API
 * @package   UserConfig
 * @author    Tai Le Thanh <tai.lt@vn.gnt-global.com>
 * @copyright 2015 Gianty
 * @license   http://www.gianty.com.vn CIO Team
 * @link      http://svn.sgcharo.com/svn/portal/sourcecode
 */
class Core_Business_Api_UserConfig
{

    /**
     *
     * @var $_instance singleton instance
     */
    protected static $_instance = null;

    /**
     * Contructor
     * 
     * @return void
     */
    protected function __construct()
    {
        
    }

    /**
     * Get singleton instance
     *
     * @return UserConfig Return UserConfig object
     * @author Tai Le Thanh <tai.lt@vn.gnt-global.com>
     */
    public final static function getInstance()
    {
        // check instance
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        // return instance
        return self::$_instance;
    }


    /**
     * addUserConfigEvent
     * 
     * @param array $data
     * @return int
     */
    public function addUserConfig($data)
    {
        $result = 0;
        $params = array(
                'accountId' => $data['accountId'],
                'key' => $data['key'],
                'value' => $data['value'],
        );
        
        try {

            $storage = Core_Global::getDbGlobalMaster();

            $sql = "call sp_user_config_insert(:accountId, :key, :value, @last_insert_id)";
            $stmt = $storage->prepare($sql);
            $stmt->execute($params);

            $stmt   = $storage->query("SELECT @last_insert_id");
            $result = $stmt->fetchColumn();

            $stmt->closeCursor();
        } catch (Exception $ex) {
            // ErrorLog::getInstance()->insert(__CLASS__, __FUNCTION__, $ex->getMessage(), $data['accountId'], '');
        }

        return $result;
    }
    
    public function updateUserConfig($data)
    {
        $result = 0;
        $params = array(
                'userConfigId' => $data['userConfigId'],
                'accountId' => $data['accountId'],
                'key' => $data['key'],
                'value' => $data['value'],
        );
        try {
            $storage = Core_Global::getDbGlobalMaster();
            $sql = "call sp_user_config_update(:userConfigId, :accountId, :key, :value, @p_row_count)";
            $stmt = $storage->prepare($sql);
            $stmt->execute($params);

            $stmt = $storage->query("SELECT @p_row_count");
            $result = $stmt->fetchColumn();

            $stmt->closeCursor();
        } catch (Exception $ex) {
           
            // ErrorLog::getInstance()->insert(__CLASS__, __FUNCTION__, $ex->getMessage(), $data['accountId'], '');
        }

        return $result;
    }
    
    public function getUserConfigByKey($data)
    {
        $result = array();
        $params = array(
            'accountId' => $data['accountId'],
            'key' => $data['key']
        );
        
        try {

            $storage = Core_Global::getDbGlobalSlave();

            $sql = "call sp_user_config_select_by_key(:accountId, :key)";
            $stmt = $storage->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();
            
            ($result === FALSE) && $result = array();
            
            $stmt->closeCursor();
        } catch (Exception $ex) {
            
            // ErrorLog::getInstance()->insert(__CLASS__, __FUNCTION__, $ex->getMessage(), $data['accountId'], '');
        }

        return $result;        
    }
    
}
