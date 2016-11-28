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

        // Return Instance
        return self::$_instance;
    }

    public function checkUserTest($username){
        $matches = null;
        $iMatch = preg_match('/^qa-tester-/',$username, $matches);
        return ($iMatch > 0) ? true : false;
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
        Core_Common::clearCache(CACHE_ACCOUNT_DETAIL_SHORT_KEY,$arrData['account_id']);


        //Return result data
        return $iResult;
    }

    /**
     * @todo  Update
     * @return <int>
     */
    public function updateMyAccountInfo($arrData)
    {
        $arrData['avatar'] = Core_Common::fixAvatarName($arrData['avatar']);
        $arrData['picture'] = Core_Common::fixAvatarName($arrData['picture']);
        //Get data
        $iResult = $this->_modeParent->updateMyAccountInfo($arrData);

        //delete cache
        Core_Common::clearCache(CACHE_ACCOUNT_DETAIL_SHORT_KEY,$arrData['account_id']);

        //Return result data
        return $iResult;
    }

    /**
     * @todo  Update
     * @return <int>
     */
    public function updateAccountInfoStatus($iAccountID, $iActive)
    {
        //Get data
        $iResult = $this->_modeParent->updateAccountInfoStatus($iAccountID, $iActive);


        //delete cache
        $this->removeCache($iAccountID);

        //Return result data
        return $iResult;
    }

    /**
     * @todo  Add new GiveAway
     */
    public function insertAccountInfoBase($sName, $sEmail, $sPicture, $sUserName, $sTeamName, $sFirstName = '', $sLastName = '',$iActive = 1)
    {


        $arrDataInit = array(
            'name' => $sName,
            'email' => $sEmail,
            'picture' => $sPicture,
            'avatar' => $sPicture,
            'phone' => '',
            'birthday' => '0000-00-00',
            'id' => 0,
            'identity' => '',
            'tax_code' => '',
            'address' => '',
            'position' => 0,
            'department_id' => 0,
            'team_id' => 0,
            'direct_manager' => 0,
            'skype_account' => '',
            'mobion_account' => '',
            'start_date' => '0000-00-00',
            'end_date' => '0000-00-00',
            'contract_type' => 0,
            'contract_sign_date' => '0000-00-00',
            'country_id' => 0,
            'description' => '',
            'status' => 0,
            'active' => $iActive,
            'username' => $sUserName,
            'team_name' => $sTeamName,
            'manager_type' => 0,
            'gender' => 0,
            'place_of_birth' => 0,
            'home_town' => 0,
            'identity_date' => '0000-00-00',
            'identity_place' => 0,
            'passport' => '',
            'passport_date' => '0000-00-00',
            'passport_place' => 0,
            'social_insurance' => '',
            'bank_account' => '',
            'bank_account_id' => 0,
            'bank_account_branch' => 0,
            'marital_status' => 0,
            'no_of_children' => 0,
            'first_name' => $sFirstName,
            'last_name' => $sLastName,
            'personal_email' => '',
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
        $this->removeCache($iAccountID);


        //Return result data
        return $iResult;
    }

    public function updateGeneral($arrData)
    {
        //delete cache
        Core_Common::clearCache(CACHE_ACCOUNT_DETAIL_SHORT_KEY,$arrData['account_id']);


        $arrData['avatar'] = Core_Common::fixAvatarName($arrData['avatar']);
        $arrData['picture'] = Core_Common::fixAvatarName($arrData['picture']);
        $this->_modeParent->updateGeneral($arrData);
//        $arrLogin = Admin::getInstance()->getLogin();
//        $accountInfo = AccountInfo::getInstance()->getAccountInfoByAccountID($arrData['account_id']);
//
//        $accountInfo = Search::getInstance()->getProfileSearchTmp($accountInfo);
//        Search::getInstance()->update($accountInfo);
        return true;

    }

    public function updatePersonal($arrData)
    {

        //delete cache
        Core_Common::clearCache(CACHE_ACCOUNT_DETAIL_SHORT_KEY,$arrData['account_id']);
        $result = $this->_modeParent->updatePersonal($arrData);
//        $accountInfo = AccountInfo::getInstance()->getAccountInfoByAccountID($arrData['account_id']);
//        $accountInfo = Search::getInstance()->getProfileSearchTmp($accountInfo);
//        Search::getInstance()->update($accountInfo);
        return $result;
    }

    public function updateJob($arrData)
    {
        //delete cache
        Core_Common::clearCache(CACHE_ACCOUNT_DETAIL_SHORT_KEY,$arrData['account_id']);
        $result = $this->_modeParent->updateAccountInfo($arrData);
//        $accountInfo = AccountInfo::getInstance()->getAccountInfoByAccountID($arrData['account_id']);
//        $accountInfo = Search::getInstance()->getProfileSearchTmp($accountInfo);
//        Search::getInstance()->update($accountInfo);

        return $result;
//        return $this->_modeParent->updateJob($arrData);
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
        $this->removeCache($iAccountID);

        //Return result data
        return $iResult;
    }

    /**
     * @todo Get all Give Away
     * @return <array>
     */
    public function getAccountInfoList($sName = '', $sEmail = '', $iID = 0, $iIdentity = 0, $sTaxCode = '', $iPosition = 0, $iDepartmentID = 0, $iTeamID = 0, $iLevel = 0, $iActive = 0, $sSortField = 'account_id', $sSortType = 'ASC', $iOffset = 0, $iLimit = MAX_QUERY_LIMIT)
    {
        //Get data
        $arrResult = $this->_modeParent->getAccountInfoList($sName, $sEmail, $iID, $iIdentity, $sTaxCode, $iPosition, $iDepartmentID, $iTeamID, $iLevel,
           $iActive, $sSortField, $sSortType , $iOffset, $iLimit);

        //Return result data
        return $arrResult;
    }

    /**
     * @todo Get all Give Away
     * @return <array>
     */
    public function getAccountInfoListByLikeEmail($sEmail, $iOffset, $iLimit)
    {
        //Get data
        $arrResult = $this->_modeParent->getAccountInfoListByLikeEmail($sEmail, $iOffset, $iLimit);

        //Return result data
        return $arrResult;
    }

    /**
     * @todo Get all Give Away
     * @return <array>
     */
    public function getAccountInfoListShort($sName, $sEmail, $iID, $iIdentity, $sTaxCode, $iPosition, $iDepartmentID, $iTeamID, $iOffset, $iLimit)
    {
        //Get data
        $arrResult = $this->_modeParent->getAccountInfoListShort($sName, $sEmail, $iID, $iIdentity, $sTaxCode, $iPosition, $iDepartmentID, $iTeamID, $iOffset, $iLimit);

        //Return result data
        return $arrResult;
    }

    /**
     * @todo Get all Give Away
     * @return <array>
     */
    public function getAccountInfoListTop($iOffset, $iLimit)
    {
        //Get data
        $arrResult = $this->_modeParent->getAccountInfoListTop($iOffset, $iLimit);

        //Return result data
        return $arrResult;
    }

    /**
     * @todo Get all Give Away
     * @return <array>
     */
    public function getAccountInfoByAccountID($iAccountID)
    {

        $keyCaching = Core_Global::getKeyPrefixCaching('account_detail_short_key') . $iAccountID;
        $caching = Core_Global::getCacheInstance();

        //Get data from caching
        $arrResult = $caching->read($keyCaching);

        if ($arrResult === false) {

            $arrResult = $this->_modeParent->getAccountInfoByAccountID($iAccountID);

            if (!empty($arrResult)) {

                $time = Core_Global::getKeyPrefixCaching('account_detail_short_expired');
                $caching->write($keyCaching, $arrResult, $time);
            }
        }

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
     * getAccountInfoByLikeName
     *
     * @param string $name
     * @return array return all record match like %$name%
     * @author Tai Le Thanh <tai.lt@vn.gnt-global.com>
     */
    public function getAccountInfoByLikeName($name)
    {
        $arrResult = $this->_modeParent->getAccountInfoByLikeName($name);

        return $arrResult;
    }

    /**
     *
     * @param string $email
     * @return array return all record match like %$email%
     * @author Tai Le Thanh <tai.lt@vn.gnt-global.com>
     */
    public function getAccountInfoByLikeEmail($email)
    {
        $arrResult = $this->_modeParent->getAccountInfoByLikeEmail($email);

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


    public function getAccountInfoBySkype($sSkype)
    {
        //Get data
        $arrResult = $this->_modeParent->getAccountInfoBySkype($sSkype);

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


    /**
     * @todo Get all Give Away
     * @return <array>
     */
    public function getAccountInfoSuggestion($sName, $sPosition, $sEmail, $iOffset, $iLimit)
    {
        //Get data
        $arrResult = $this->_modeParent->getAccountInfoSuggestion($sName, $sPosition, $sEmail, $iOffset, $iLimit);

        //Return result data
        return $arrResult;
    }

    /**
     *
     * @param arra
     * @return array
     */
    public function getAccountListIN($sAccountIDs)
    {


        $arrResult = array();
        if (is_array($sAccountIDs)) {
            $sAccountIDs = implode(",", $sAccountIDs);
        }


        $arrResult = $this->_modeParent->getAccountListIN($sAccountIDs);

        if (!empty($arrResult)) {
            //Init caching
            $caching = Core_Global::getCacheInstance();

            $keyCaching = Core_Global::getKeyPrefixCaching('account_detail_short_key');

            //Get key expired
            $time = Core_Global::getKeyPrefixCaching('account_detail_short_expired');

            foreach ($arrResult as $key => $value) {
                $value['picture'] = Core_Common::avatarProcess($value['picture']);
                $value['image_tag'] = $value['picture'];

                $arrResult[$key] = $value;

                //get Key cache
                $keyCacheAccount = $keyCaching . $value['account_id'];

                //Update in cache
                $caching->write($keyCacheAccount, $value, $time);
            }
        }


        return $arrResult;
    }

    /**
     *
     * @param array $arrAppList
     * @return array
     */
    public function getAccountListShort($arrAccountID)
    {

        $arrGroupID = array();

        //Constructor default array result
        $arrResult = array();

        //Init caching
        $caching = Core_Global::getCacheInstance();

        //Get prefix from configuration
        $keyPrefixCaching = Core_Global::getKeyPrefixCaching('account_detail_short_key');

        //  $arrAccountID[] = 999;

        //Add prefix caching for list key
        array_walk($arrAccountID, 'Core_Global::addKeyPrefix', $keyPrefixCaching);


        //Get array key miss
        $arrMissKey = array();

        $iTotalAccount = count($arrAccountID);

        if ($iTotalAccount > 1) {//get multi cache
            //Get data in cache
            $arrResultCache = $caching->readMulti($arrAccountID);

            if (!empty($arrResultCache)) {
                //Loop to check data missing
                foreach ($arrResultCache as $keyCaching => $arrDetail) {
                    //Get AppID
                    $iAccountID = str_replace($keyPrefixCaching, '', $keyCaching);

                    //Check cache
                    if (empty($arrDetail)) {
                        //Add app_id to list missing cache
                        $arrMissKey[] = $iAccountID;
                    } else {
                        //get In
                        $arrResult[$iAccountID] = $arrDetail;
                        $arrGroupID[] = $arrDetail['team_id'];
                    }
                }
            }
        } else if ($iTotalAccount > 0) {//get single cache
            $iAccountID = str_replace($keyPrefixCaching, '', $arrAccountID[0]);
            $arrMissKey[] = $iAccountID;
        }

        //check miss key
        if (!empty($arrMissKey)) {

            // var_dump($arrMissKey); exit;
            $arrResultMiss = $this->getAccountListIN($arrMissKey);

            if (!empty($arrResultMiss)) {
                foreach ($arrResultMiss as $value) {
                    $arrResult[$value['account_id']] = $value;
                    $arrGroupID[] = $value['team_id'];
                }
            }
        }

        // print_r($arrResult);
        // print_r($arrGroupID);

        //get grouplist
        if (!empty($arrGroupID)) {
            $arrGroupList = Group::getInstance()->getGroupList2($arrGroupID);
        }


        foreach ($arrResult as $key => $value) {
            $value['team_name'] = isset($arrGroupList[$value['team_id']]['group_name']) ? $arrGroupList[$value['team_id']]['group_name'] : '';
            if (empty($value['team_name'])) {
                $value['team_name'] = 'IT Team';
            }

            $arrResult[$value['account_id']] = $value;
        }
        //Return result
        return $arrResult;
    }

    public function countUserActive()
    {
        return $this->_modeParent->countUserActive();
    }

    public function removeCache($iAccountID)
    {
        //Init caching
        $caching = Core_Global::getCacheInstance();

        //Get prefix from configuration
        $keyPrefixCaching = Core_Global::getKeyPrefixCaching('account_detail_short_key') . $iAccountID;
        $caching->delete($keyPrefixCaching);
    }

    public function addNewUserFromLdap($email,$pass)
    {
        $blockAccounts = require_once APPLICATION_PATH . '/configs/accounts-block.php';
        $ldap_columns = NULL;
        $ldap_connection = NULL;

        // Connect to the LDAP server.
        $ldap_connection = ldap_connect(LDAP_SERVER);
        if (FALSE === $ldap_connection)
            die('connect fail');

        ldap_set_option($ldap_connection, LDAP_OPT_PROTOCOL_VERSION, 3) or die('Unable to set LDAP protocol version');
        ldap_set_option($ldap_connection, LDAP_OPT_REFERRALS, 0); // We need this for doing an LDAP search.

        if (TRUE !== ldap_bind($ldap_connection, $email, $pass))
            die('login fail');

        $search_filter = "(&(objectCategory=person))";
        $ldap_base_dn = LDAP_BASEDN;
        $result = ldap_search($ldap_connection, $ldap_base_dn, $search_filter);
        $arrChangeNameTeam = array('Offshore Partner'=> 'OFFSHORE', 'Game Artist'=>'DESIGN');
        if (FALSE !== $result) {
            $entries = ldap_get_entries($ldap_connection, $result);
            if ($entries['count'] > 0) {
                for ($i = 0; $i < $entries['count']; $i++) {
                    if (isset($entries[$i]['samaccountname'])) {

                        $iActive = 1;
                        $username = $entries[$i]['samaccountname'][0];
//                        if($username == 'cookojima.chikara'){
//                            echo 'check-------------cookojima.chikara--------';
//                            Core_Common::var_dump($entries[$i]['samaccountname'],false);
//                            Core_Common::var_dump($entries[$i]['mail'],false);
//
//                        }
//                        if($username == 'huy.tq'){
//                            echo 'check-------cookojima--------------';
////                            Core_Common::var_dump($entries[$i]['samaccountname'],false);
//                            Core_Common::var_dump($entries[$i]);
//
//                        }

//                        if($username == 'ptest'){
//                            echo 'check-------cookojima--------------';
//                            Core_Common::var_dump($entries[$i]['samaccountname'],false);
//                            Core_Common::var_dump($entries[$i]);
//
//                        }
//                        if($username == 'trung.hk')
//                            Core_Common::var_dump($entries[$i]);

                        $arrUserName = explode('.', $username);
                        $dn = $entries[$i]['dn'];
                        $arrDnName = explode(',', $dn);
                        foreach ($arrDnName as $dnName) {
                            $groupName = explode('=', $dnName);

                            $dnName = $groupName[1];
                            if ($dnName == 'disabled_accounts') {
                                $iActive = 10;
                            }
                        }
                        echo '<br/> ------------------------------- <br/>';
//                        if($username == 'portal')
//                            Core_Common::var_dump($entries[$i]['mail'][0]);

                        if (!in_array($entries[$i]['samaccountname'][0], $blockAccounts) && count($arrUserName) > 1) {
                            $hasAccount = AccountInfo::getInstance()->getAccountInfoByUserName($username,0);

                            if (empty($hasAccount)) {
                                echo 'username: '.$username.'<br/>';
                                $newAccount = array();
                                $newAccount['username'] = $username;
                                $newAccount['name'] = isset($entries[$i]['name']) ? $entries[$i]['name'][0] : '';
                                $arrName = explode(' ',$newAccount['name']);
                                $firstName = $newAccount['name'];
                                $lastName = '';
                                if(count($arrName) > 1) {
                                    $firstName = $arrName[0];
                                    $lastName = trim(str_replace($firstName, '', $newAccount['name']));
                                }
                                $newAccount['first_name'] = $firstName;
                                $newAccount['last_name'] = $lastName;
                                $newAccount['mail'] = isset($entries[$i]['mail']) ? $entries[$i]['mail'][0] : '';
                                $newAccount['picture'] = AvatarDefault;
                                $newAccount['teamName'] = '';

                                if (isset($entries[$i]['dn'])) {
                                    $dn = $entries[$i]['dn'];
                                    $arrDnName = explode(',', $dn);
                                    foreach ($arrDnName as $dnName) {
                                        $groupName = explode('=', $dnName);
                                        $dnName = $groupName[1];
                                       $dnName = isset($arrChangeNameTeam[$dnName]) ? $arrChangeNameTeam[$dnName] : $dnName;
                                        $Group = Group::getInstance()->selectByName(strtolower($dnName));
                                        if (!empty($Group)) {

                                            $newAccount['teamName'] = $Group['group_name'];
                                            $iAccountID = AccountInfo::getInstance()->insertAccountInfoBase($newAccount['name'], $newAccount['mail'], $newAccount['picture'], $newAccount['username'], $newAccount['teamName']
                                                , $newAccount['first_name'], $newAccount['last_name'],$iActive);

                                            //Update To Solr
                                            if ($iAccountID > 0) {
                                                $groupMember = array('account_id' => $iAccountID, 'group_id' => $Group['group_id'], 'level' => GroupMember::$staff);
                                                $accountInfo = AccountInfo::getInstance()->getAccountInfoByAccountID($iAccountID);
                                                $accountInfo['team_id'] = $Group['group_id'];
                                                $accountInfo['team_name'] = $Group['group_name'];
                                                AccountInfo::getInstance()->updateGeneral($accountInfo);

                                                GroupMember::getInstance()->addGroupMember($groupMember);
                                                Search::getInstance()->insertBase($iAccountID, $newAccount['name'], $newAccount['mail'], $newAccount['picture'], $newAccount['username'], $newAccount['teamName']);

                                                $allGianty = Group::getInstance()->selectByName(Group::$AllGianty);
                                                $feedBack = Group::getInstance()->selectByName('Feedback');
                                                if(!empty($allGianty))
                                                {
                                                    $groupMember = array('account_id' => $iAccountID, 'group_id' => $allGianty['group_id'], 'level' => GroupMember::$staff);
                                                    GroupMember::getInstance()->addGroupMember($groupMember);
                                                }

                                                if(!empty($feedBack))
                                                {
                                                    $groupMember = array('account_id' => $iAccountID, 'group_id' => $feedBack['group_id'], 'level' => GroupMember::$staff);
                                                    GroupMember::getInstance()->addGroupMember($groupMember);
                                                }

                                            }
                                        }
                                    }
                                } // if(empty($hasAccount))

                            }//  end else (empty($hasAccount))
                            else {
                                //leave work
                                if (isset($entries[$i]['dn'])) {
                                    $dn = $entries[$i]['dn'];
                                    $arrDnName = explode(',', $dn);
//                                    if($username == 'khoa.lnd')
//                                        Core_Common::var_dump($arrDnName);

                                    foreach ($arrDnName as $dnName) {
                                        $groupName = explode('=', $dnName);
                                        $dnName = $groupName[1];
                                        if ($dnName == 'disabled_accounts') {
                                            if($hasAccount['end_date'] == '0000-00-00' && $hasAccount['active'] == 1){
                                                echo ' <br/> --------------------update end_date-----------------------<br/>';
                                                $dEndDate = new DateTime();
                                                $hasAccount['end_date'] = $dEndDate->format('Y-m-d');
                                                AccountInfo::getInstance()->updateAccountInfo($hasAccount);
                                            }else{
                                                $dEndDate = new DateTime($hasAccount['end_date']);
                                                $dEndDate = $dEndDate->modify('+1 month');
                                                $toDate = new DateTime();
                                                if($dEndDate == $toDate){
                                                    echo 'disabled account :'.$hasAccount['username'] . ' -- ' . $dnName . '<br/>';
                                                    AccountInfo::getInstance()->updateAccountInfoStatus($hasAccount['account_id'], 10);
                                                }

                                            }

                                        }
                                        else{
                                            $hasAccount['end_date'] = '0000-00-00';
                                            $hasAccount['active'] = 1;
                                            AccountInfo::getInstance()->updateAccountInfo($hasAccount);
                                        }
//                                        else{
//
//                                            $dnName = isset($arrChangeNameTeam[$dnName]) ? $arrChangeNameTeam[$dnName] : $dnName;
//                                            $Group = Group::getInstance()->selectByName(strtolower($dnName));
//
//                                            if(!empty($Group)) {
////                                                if($Group['group_id'] != $hasAccount['team_id']) {
//                                                    $member = GroupMember::getInstance()->getGroupMemberByAccountAndGroupId($hasAccount['account_id'], $Group['group_id']);
//                                                    if (empty($member)) {
//
//                                                        // delete old group member
//                                                        $oldGroup = Group::getInstance()->getGroupByID($hasAccount['team_id']);
//                                                        if (!empty($oldGroup)) {
//                                                            $oldMember = GroupMember::getInstance()->getGroupMemberByAccountAndGroupId($hasAccount['account_id'], $oldGroup['group_id']);
//                                                            if (!empty($oldMember)) {
//                                                                GroupMember::getInstance()->deleteGroupMember($oldMember['account_id'], $oldGroup['group_id']);
//                                                            }
//
//                                                        }
//
//                                                        echo 'add member: '.$hasAccount['account_id'].' - '.$hasAccount['name'].' - '.$hasAccount['team_name'].'<br/>';
//                                                        // add new group member
//                                                        $hasAccount['team_id'] = $Group['group_id'];
//                                                        AccountInfo::getInstance()->updateAccountInfo($hasAccount);
//                                                        $member = array('account_id' => $hasAccount['account_id'], 'group_id' => $Group['group_id'], 'level' => GroupMember::$staff);
//                                                        GroupMember::getInstance()->addGroupMember($member);
//                                                    }else
//                                                        echo 'member-id: '.$member.'<br/>';
////                                                }
//
//                                            }else{
//                                               echo 'group not exits: '.$dnName.'<br/>';
//                                            }
//                                        }
                                    }

                                }
                            }
                        }//   if (!in_array($entries[$i]['samaccountname'][0], $blockAccounts) && count($arrUserName) > 1)

                    }// for ($i = 0; $i < $entries['count']; $i++)

                }// if ($entries['count'] > 0)

            }// if(FALSE !== $result)

            ldap_unbind($ldap_connection); // Clean up after ourselves.


        }


    }
}
