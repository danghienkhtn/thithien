<?php
/**
 * @author      :   HienND
 * @name        :   Model AccountInfo
 * @version     :   201611
 * @copyright   :   DAHI
 * @todo        :   AccountInfo model
 */
class AccountInfo
{
    /**
     * Parent instance
     * @var <object>
     */
    private $_modeParent = null;

    /**
     * @var type
     */
    protected static $_instance = null;

    /**
     * Constructor of class
     * we don't permit an explicit call of the constructor! (like $v = new Singleton())
     */
    protected function __construct()
    {
        // Int Parent Model
        $this->_modeParent = Core_Business_Api_AccountInfo::getInstance();
    }

    /**
     * Get singletom instance
     * @return <object>
     */
    public final static function getInstance()
    {        
        // Check Instance
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
// error_log("done");
        // Return Instance
        return self::$_instance;
    }

    public function insertAccountInfo($arrData)
    {
        $arrData['avatar'] = Core_Common::fixAvatarName($arrData['avatar']);
        $arrData['picture'] = Core_Common::fixAvatarName($arrData['picture']);
        //Get data
        $iResult = $this->_modeParent->insertAccountInfo($arrData);
        //Return result data
        return $iResult;
    }

    public function updateAccountInfo($arrData)
    {

        $arrData['avatar'] = Core_Common::fixAvatarName($arrData['avatar']);
        $arrData['picture'] = Core_Common::fixAvatarName($arrData['picture']);
        //Get data
        $iResult = $this->_modeParent->updateAccountInfo($arrData);

        //delete cache 
        // Core_Common::clearCache(CACHE_ACCOUNT_DETAIL_SHORT_KEY,$arrData['account_id']);


        //Return result data
        return $iResult;
    }    

    /**
     * @todo  Update
     * @return <int>
     */
    public function updateActiveStatus($iAccountID, $iActive)
    {
        //Get data
        $iResult = $this->_modeParent->updateActiveStatus($iAccountID, $iActive);


        //delete cache
        // $this->removeCache($iAccountID);

        //Return result data
        return $iResult;
    }

    /**
     * @todo  Update
     * @return <int>
     */
    public function updateLastLogin($iAccountID)
    {
        //Get data
        $iResult = $this->_modeParent->updateLastLogin($iAccountID);


        //delete cache
        // $this->removeCache($iAccountID);

        //Return result data
        return $iResult;
    }

    /**
     * @todo  Update
     * @return <int>
     */
    public function updateUpperTimes($iAccountID, $times)
    {
        //Get data
        $iResult = $this->_modeParent->updateUpperTimes($iAccountID, $times);


        //delete cache
        // $this->removeCache($iAccountID);

        //Return result data
        return $iResult;
    }

    /**
     * @todo  Add new GiveAway
     */
    public function insertAccountInfoBase($sName, $sEmail, $sPicture, $sUserName,$iActive = 1)
    {


        $arrDataInit = array(
            'name' => $sName,
            'email' => $sEmail,
            'picture' => $sPicture,
            'avatar' => $sPicture,
            'phone' => '',
            'birthday' => '0000-00-00',
            'address' => '',
            'skype_account' => '',
            'google_account' => '',
            'facebook_account' => '',
            'yahoo_account' => '',
            'country_id' => 0,
            'description' => '',
            'status' => 0,
            'active' => $iActive,
            'username' => $sUserName,
            'gender' => 0,
            'email1' => '',
            'contact_name' => '',
            'contact_address' => '',
            'contact_phone' => '',
            'contact_relationship' => ''
        );


        //Get data
        $iResult = $this->_modeParent->insertAccountInfo($arrDataInit);


        //Return result data
        return $iResult;
    }

    /*
    **
     * @todo  Remove GiveAway
     * @return <int>
     */
    public function updateAvatar($iAccountID, $sPicture)
    {
        $sPicture['avatar'] = Core_Common::fixAvatarName($sPicture);

        //Get data
        $iResult = $this->_modeParent->updateAvatar($iAccountID, $sPicture);


        //delete cache
        // $this->removeCache($iAccountID);


        //Return result data
        return $iResult;
    }    

    /**
     * @todo  Remove GiveAway
     * @return <int>
     */
    public function removeAccountInfo($iAccountID)
    {
        //Get data
        $iResult = $this->_modeParent->removeAccountInfo($iAccountID);

        //delete cache
        // $this->removeCache($iAccountID);

        //Return result data
        return $iResult;
    }

    /**
     * @todo Get all Give Away
     * @return <array>
     */
    public function getAccountInfoList($sAccountID = '', $sName = '', $sEmail = '', $sPhone = '', $sAddress = '', $iGender = '', $iActive = '', $iLevel = '', $iActive = 0, $sSortField = 'account_id', $sSortType = 'ASC', $iOffset = 0, $iLimit = MAX_QUERY_LIMIT)
    {    
        //Get data
        $arrResult = $this->_modeParent->getAccountInfoList($sAccountID, $sName, $sEmail, $sPhone, $sAddress, $iGender, $iActive, $iLevel, $iActive, $sSortField, $sSortType, $iOffset, $iLimit);

        //Return result data
        return $arrResult;
    }

    /**
     * @todo Get all Give Away
     * @return <array>
     */
    public function getAccountInfoByAccountID($iAccountID)
    {

    
        $arrResult = $this->_modeParent->getAccountInfoByAccountID($iAccountID);

        return $arrResult;

    }

    /**
     * @todo Get all Give Away
     * @return <array>
     */
    public function getAccountInfoByEmail($sEmail)
    {
        //Get data
        $arrResult = $this->_modeParent->getAccountInfoByEmail($sEmail);

        //Return result data
        return $arrResult;
    }

    /**
     * @todo Get all Give Away
     * @return <array>
     */
    public function getAccountInfoByUserName($sUserName, $iActive = 1)
    {
        //Get data
        $arrResult = $this->_modeParent->getAccountInfoByUserName($sUserName, $iActive);

        //Return result data
        return $arrResult;
    }


    /**
     * @todo Get all Give Away
     * @return <array>
     */
    public function getAccountInfoByAccountIDs($sAccountID)
    {
        //Get data
        $arrResult = $this->_modeParent->getAccountInfoByAccountIDs($sAccountID);

        //Return result data
        return $arrResult;
    }

    public function countUserActive()
    {
        return $this->_modeParent->countUserActive();
    }

    public function userLogin($username, $password, $arrAccInfo)
    {
        return $this->_modeParent->userLogin($username, $password, &$arrAccInfo);
    }    

    public function checkUserLogin($sAuthToken = '')
    {
        $sAuthToken = trim($sAuthToken);
          //Check cookie data
        if(empty($sAuthToken)) {
            $sAuthToken = Core_Cookie::getCookie(AUTH_USER_LOGIN_TOKEN);
        }

        //Check token
        if (empty($sAuthToken)) {
            return false;
        }

        /* Session expired*/
        if(!isset($_SESSION[$sAuthToken]))
        {
            return false;
        }
        //Return data
        return true;

    }

    public function getUserLogin($sAuthToken = '')
    {
        $sAuthToken = trim($sAuthToken);
          //Check cookie data
        if(empty($sAuthToken)) {
            $sAuthToken = Core_Cookie::getCookie(AUTH_USER_LOGIN_TOKEN);
        }

        //Check token
        if (empty($sAuthToken)) {
            return array();
        }

        /* Session expired*/
        if(!isset($_SESSION[$sAuthToken]))
        {
            return array();
        }
        //Return data
        return $_SESSION[$sAuthToken];

    }
    
    public function setUserLogin($sAuthToken, $iAccountID)
    {

        $accountInfo = AccountInfo::getInstance()->getAccountInfoByAccountID($iAccountID);
        $_SESSION[$sAuthToken] = array(
            'account_id'      => $iAccountID,
            'name'       => $accountInfo['name'],
            'avatar'         => Core_Common::avatarProcess($accountInfo['avatar']),
            'token'          => $sAuthToken,
            'is_admin'     => $accountInfo['is_admin'],
            'email'          => $accountInfo['email'],
            'email1'   => $accountInfo['email1'],
            'username'   => $accountInfo['username'],
            'last_login_date'   => $accountInfo['last_login_date'],
            'update_date'   => $accountInfo['update_date'],
            'active'   => $accountInfo['active'],
            'ps'             => $accountInfo['password']
        );        
    }
/*
    public function removeCache($iAccountID)
    {
        //Init caching
        $caching = Core_Global::getCacheInstance();

        //Get prefix from configuration
        $keyPrefixCaching = Core_Global::getKeyPrefixCaching('account_detail_short_key') . $iAccountID;
        $caching->delete($keyPrefixCaching);
    }
*/    
}
