<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_FacebookSearch_Adapter_Abstract
 * @version     :   201008
 * @copyright   :   My company
 * @todo        :   Using to search friend from facebook
 */
abstract class Core_FacebookSearch_Adapter_Abstract
{
    protected $DefaultAvatar = "http://images.mobion.com/music_default_avatar_small.jpg";

    protected $KeyPrefix = "facebook_id:";

    protected $KeyExpired = 86400;

    /**
    * Instance search for facebook informations
    * @var Core_Search
    */
    protected $FacebookInfos = null;

    /**
    * Instance search for facebook friends
    * @var Core_Search
    */
    protected $FacebookFriends = null;

    /**
    * Instance Api for facebook
    * @var Core_Search
    */
    protected $FacebookAPI = null;

    /**
    * Info Core Name
    * @var string
    */
    protected $InfoCore = '';

    /**
    * Friend Core Name
    * @var string
    */
    protected $FriendCore = '';

    protected $CacheCore = '';

    /**
    * Set FacebookApi
    * @param object $FacebookApi
    */
    protected function setFacebookAPI($instanceFbAPI)
    {
        $this->FacebookAPI = $instanceFbAPI;
    }

    /**
    * Set FacebookInfos
    * @param object $FacebookInfos
    */
    protected function setFacebookInfos($instanceFbInfos)
    {
        $this->FacebookInfos = $instanceFbInfos;
    }

    /**
    * Set FacebookFriends
    * @param object $FacebookFriends
    */
    protected function setFacebookFriends($instanceFbFriends)
    {
        $this->FacebookFriends = $instanceFbFriends;
    }

    /**
    * Set InfoCore
    * @param string $strCoreName
    */
    protected function setInfoCore($strCoreName)
    {
        $this->InfoCore = $strCoreName;
    }

    /**
    * Set FriendCore
    * @param string $strCoreName
    */
    protected function setFriendCore($strCoreName)
    {
        $this->FriendCore = $strCoreName;
    }

    protected function setCacheCore($strCoreName)
    {
        $this->CacheCore = $strCoreName;
    }

        /**
     * Escape string when query
     * @param <string> $strQuery
     * @return <string>
     */
    public function escapeQueryString($strQuery)
    {
        $strQuery = iconv("UTF-8", "UTF-8//IGNORE", $strQuery);
        $pattern = '/(\+|-|&&|\|\||!|\(|\)|\{|}|\[|]|\^|"|~|\*|\?|:|\\\)/';
        $replace = '\\\$1';
        $strQuery = preg_replace($pattern, $replace, $strQuery);
        return preg_replace('/[;,:,\\,\[,\],\{,!,^,\}]|OR|AND/', '', $strQuery) ;
    }

    /**
     * Set facebook friend Of user
     * @param string $strFbToken
     * @param int    $intFbID
     * @param array  $arrAppIDs
     * @return bool
     */
    abstract protected function importFbFriendList($strFbToken, $intFbID, $iUserID, $arrAppIDs);

    //Private function

    /**
     * Set profile facebook Of user
     * @param string $strFbToken
     * @param int    $iUserID
     * @param array  $arrAppIDs
     * @return array
     */
    abstract protected function setFbProfile($strFbToken, $iUserID, $arrAppIDs);

    /**
     * Set fb friend list
     */
    abstract protected function setFbFriendList($strFbToken, $intFbID);

    /**
     * Get list facebook friend of current app By FacebookID
     * @param string $intFbID
     * @param array  $intLimit
     * @param int    $intOffset
     * @return array
     */
    abstract protected function getFbFriendList($intFbID, $intOffset, $intLimit, $strSort);

    /**
     * Get list facebook friend not used of current app By FacebookID
     * @param string $intFbID
     * @param array  $intLimit
     * @param int    $intOffset
     */
    abstract protected function getFbFriendNotUsedList($intFbID, $strAppID, $intOffset, $intLimit, $strSort);

    /**
     * Get list facebook friend used of current app By FacebookID
     * @param string $intFbID
     * @param array  $intLimit
     * @param int    $intOffset
     */
    abstract protected function getFbFriendUsedAppList($intFbID, $strAppID, $intOffset, $intLimit, $strSort);

    /**
     * Search facebook friend of current app By FacebookID and Keyword
     * @param string $intFbID
     * @param string $strKeyword
     * @param array  $intLimit
     * @param int    $intOffset
     */
    abstract protected function searchFbFriendList($intFb, $strKeyword, $intOffset, $intLimit, $strSort);

    /**
     * Check exist facebook friend of current app By FacebookID
     * @param string $intFbID
     * @param bool
     */
    abstract protected function checkFbFriendListExist($intFbID);
}

