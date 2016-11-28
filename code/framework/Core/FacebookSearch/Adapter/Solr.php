<?php

/**
 * @author      :   Linuxpham
 * @name        :   Core_FacebookSearch_Adapter_Solr
 * @version     :   201008
 * @copyright   :   My company
 * @todo        :   Using to facebook search
 */
class Core_FacebookSearch_Adapter_Solr extends Core_FacebookSearch_Adapter_Abstract {

    /**
     * Constructor
     * @param int   $appid
     * @param int   $secret
     * @param array $options
     */
    public function __construct($appid, $secret, $caching, $options=array()) {

        //Check host
        if (empty($options['solr']['host'])) {
            throw new Core_FacebookSearch_Exception('Input host for Solr.');
        }

        //Check port
        if (empty($options['solr']['port'])) {
            throw new Core_FacebookSearch_Exception('Input port for Solr.');
        }

        //Check fbinfos core
        $fbinfos = $options['solr']['core']['fbinfos'];
        if (empty($fbinfos)) {
            throw new Core_FacebookSearch_Exception('Input facebook information core for Solr.');
        }

        //Check fbfriends core
        $fbfriends = $options['solr']['core']['fbfriends'];
        if (empty($fbfriends)) {
            throw new Core_FacebookSearch_Exception('Input facebook friend core for Solr.');
        }
        unset($options['solr']['core']);

        try {
            // Instance Facebook API
            $instanceFbAPI = new Facebook(array('appId' => $appid, 'secret' => $secret));
            $this->setFacebookAPI($instanceFbAPI);

            $options['solr']['core'] = $fbinfos;
            $instanceFbInfos = Core_Search::getInstance($options);

            $options['solr']['core'] = $fbfriends;
            $instanceFbFriends = Core_Search::getInstance($options);

            //Set FacebookInfos
            $this->setInfoCore($fbinfos);
            $this->setFacebookInfos($instanceFbInfos);

            //Set FacebookInfos
            $this->setFriendCore($fbfriends);
            $this->setFacebookFriends($instanceFbFriends);

            //Set cache core
            $this->setCacheCore($caching);
        } catch (Exception $ex) {
            throw new Core_FacebookSearch_Exception($ex->getMessage());
        }
    }

    /**
     * Set facebook friend Of user
     * @param string $strFbToken
     * @param int    $intFbID
     * @param array  $arrAppIDs
     * @return bool
     */
    public function importFbFriendList($sFacebookToken, $iFacebookID, $iUserID, $arrAppID) {
        try {
            $rs = self::setFbFriendList($sFacebookToken, $iFacebookID);
            self::setFbProfile($sFacebookToken, $iUserID, $arrAppID);
        } catch (Exception $ex) {
            return false;
        }

        return $rs;
    }

    /**
     * Set profile facebook Of user
     * @param string $strFbToken
     * @param int    $iUserID
     * @param array  $arrAppIDs
     * @return array
     */
    public function setFbProfile($sFacebookToken, $iUserID, $arrAppID) {
        // Default data
        $arrResult = array();

        try {
            // Set Facebook token
            $this->FacebookAPI->setAccessToken($sFacebookToken);

            // Get Facebook friend info
            $arrResponse = $this->FacebookAPI->api("/me", array("fields" => "id, picture, first_name, last_name, middle_name"));

            // Check empty
            if (!empty($arrResponse)) {
                // Get data
                $arrData[] = array(
                    'FacebookID' => $arrResponse['id'],
                    'UserID' => $iUserID,
                    'UserName' => isset($arrResponse['middle_name']) ? $arrResponse['first_name'] . " " . $arrResponse['middle_name'] . " " . $arrResponse['last_name'] : $arrResponse['first_name'] . " " . $arrResponse['last_name'],
                    'FirstName' => isset($arrResponse['first_name']) ? $arrResponse['first_name'] : "",
                    'MiddleName' => isset($arrResponse['middle_name']) ? $arrResponse['middle_name'] : "",
                    'LastName' => isset($arrResponse['last_name']) ? $arrResponse['last_name'] : "",
                    'Picture' => isset($arrResponse['picture']) ? $arrResponse['picture']['data']['url'] : "http://pc.mbstatic.com/pc_20120727/images/mobion-music.png",
                    'LastModified' => time()
                );

                // Get facebook profile
                $facebookProfile = self::getFbProfile($arrResponse['id']);

                // Default app
                $arrApp = array();

                // Check empty
                if (!empty($facebookProfile)) {
                    // Get list app
                    $arrApp = $facebookProfile['ArrAppID'];
                }

                // Loop
                foreach ($arrAppID as $sAppID) {
                    $arrApp[] = $sAppID;
                }

                // Unique
                $arrApp = array_filter(array_unique($arrApp));

                // Set data
                $arrData[0]['ArrAppID'] = $arrApp;

                // Insert data
                $this->FacebookInfos->index($arrData);

                // Commit
                $this->FacebookInfos->commit();

                // Set data
                $arrResult = $arrData[0];
            }
        } catch (Exception $ex) {
            $arrResult = array();
        }

        // Set return data
        return $arrResult;
    }

    /**
     * Set Facebook friend list
     * @param string $sFacebookToken
     * @param int $iFacebookID
     * @return type
     */
    public function setFbFriendList($sFacebookToken, $iFacebookID) {
        $arrResponse = array();
        try {
            // Set Facebook token
            $this->FacebookAPI->setAccessToken($sFacebookToken);

            // Get Facebook friend info
            $arrResponse = $this->FacebookAPI->api("/me/friends", array("fields" => "id, picture, first_name, last_name, middle_name"));
        } catch (Exception $ex) {
            return $ex->getMessage();
        }

        //If have data from facebook
        if (isset($arrResponse['data'])) {
            $arrFacebookInfo = $arrResponse['data'];

            // Get length
            $iLength = count($arrFacebookInfo);

            //Check count data
            if($iLength == 0)
            {
                return;
            }

            // Set index
            $iIndex = 0;

            // Get cache
            $cachingInstance = $this->CacheCore;

            // Loop
            while ($iIndex <= $iLength) {
                // Init count
                $iCount = 0;

                // Init array
                $arrData = array();
                $arrMapping = array();

                // Make data
                for ($i = $iIndex; $i < $iLength; $i++) {
                    // Increase count
                    $iCount++;

                    // Check count
                    if ($iCount == 51) {
                        break;
                    }

                    // Check exist fbid
                    if ($arrFacebookInfo[$i]['id'] != "" && !empty($arrFacebookInfo[$i]['id'])) {
                        // Add mapping data
                        $arrMapping[] = array(
                            'ID' => $iFacebookID . "_" . $arrFacebookInfo[$i]['id'],
                            'MyFacebookID' => $iFacebookID,
                            'FriendFacebookID' => $arrFacebookInfo[$i]['id']
                        );

                        // Generate key
//                        $key = $this->KeyPrefix . $arrFacebookInfo[$i]['id'];

                        // Read cache
//                        $isExist = $cachingInstance->read($key);

                        // Check false
//                        if ($isExist === false) {
                        // Facebook info
                        $arrData[] = array(
                            'FacebookID' => $arrFacebookInfo[$i]['id'],
                            'UserName' => isset($arrFacebookInfo[$i]['middle_name']) ? $arrFacebookInfo[$i]['first_name'] . " " . $arrFacebookInfo[$i]['middle_name'] . " " . $arrFacebookInfo[$i]['last_name'] : $arrFacebookInfo[$i]['first_name'] . " " . $arrFacebookInfo[$i]['last_name'],
                            'FirstName' => isset($arrFacebookInfo[$i]['first_name']) ? $arrFacebookInfo[$i]['first_name'] : "",
                            'MiddleName' => isset($arrFacebookInfo[$i]['middle_name']) ? $arrFacebookInfo[$i]['middle_name'] : "",
                            'LastName' => isset($arrFacebookInfo[$i]['last_name']) ? $arrFacebookInfo[$i]['last_name'] : "",
                            'Picture' => isset($arrFacebookInfo[$i]['picture']) ? $arrFacebookInfo[$i]['picture']['data']['url'] : $this->DefaultAvatar,
                            'LastModified' => time()
                        );

                        // Write cache
//                        $cachingInstance->write($key, true, $this->KeyExpired);
//                        }
                    }
                }

                // Index data
                $this->FacebookInfos->update($arrData, "FacebookID");
                $this->FacebookFriends->update($arrMapping, "ID");

                // Increase index
                $iIndex += 50;

                // Commit if < 100
                if ($iIndex <= 50) {
                    $this->FacebookInfos->commit();
                    $this->FacebookFriends->commit();
                }
            }
        }

        return $rs;
    }

    /**
     * Get list facebook friend of current app By FacebookID
     * @param string $intFbID
     * @param array  $intLimit
     * @param int    $intOffset
     * @return array
     */
    public function getFbFriendList($iFacebookID, $iOffset, $iLimit, $sSort="UserName asc") {
        // Check fbid
        if ($iFacebookID == "" || is_null($iFacebookID)) {
            return array(
                'total' => 0,
                'data' => array()
            );
        }

        // Build query
        $sQuery = "*:*";

        // Build order
        $sSort = "UserName asc";

        // Get result
        $arrSearchData = self::join($sQuery, "FacebookID", "FriendFacebookID", $this->FriendCore, "MyFacebookID:$iFacebookID", $iOffset, $iLimit, $sSort);

        // Set return data
        return $arrSearchData;
    }

    /**
     * Get list facebook friend not used of current app By FacebookID
     * @param string $intFbID
     * @param array  $intLimit
     * @param int    $intOffset
     */
    public function getFbFriendNotUsedList($iFacebookID, $sAppID, $iOffset, $iLimit, $sSort="UserName asc") {
        // Check fbid
        if ($iFacebookID == "" || is_null($iFacebookID)) {
            return array(
                'total' => 0,
                'data' => array()
            );
        }

        // Build query
        $sQuery = "*:* NOT ArrAppID:$sAppID";

        // Get result
        $arrSearchData = self::join($sQuery, "FacebookID", "FriendFacebookID", $this->FriendCore, "MyFacebookID:$iFacebookID", $iOffset, $iLimit, $sSort);

        // Set return data
        return $arrSearchData;
    }

    /**
     * Get list facebook friend used of current app By FacebookID
     * @param string $intFbID
     * @param array  $intLimit
     * @param int    $intOffset
     */
    public function getFbFriendUsedAppList($iFacebookID, $sAppID, $iOffset, $iLimit, $sSort="UserName asc") {
        // Check fbid
        if ($iFacebookID == "" || is_null($iFacebookID)) {
            return array(
                'total' => 0,
                'data' => array()
            );
        }

        // Build query
        $sQuery = "*:* AND ArrAppID:$sAppID";

        // Get result
        $arrSearchData = self::join($sQuery, "FacebookID", "FriendFacebookID", $this->FriendCore, "MyFacebookID:$iFacebookID", $iOffset, $iLimit, $sSort);

        // Set return data
        return $arrSearchData;
    }

    /**
     * Search facebook friend of current app By FacebookID and Keyword
     * @param string $intFbID
     * @param string $strKeyword
     * @param array  $intLimit
     * @param int    $intOffset
     */
    public function searchFbFriendList($iFacebookID, $sKeyword, $iOffset, $iLimit, $sSort="UserName asc") {
        // Check fbid
        if ($iFacebookID == "" || is_null($iFacebookID)) {
            return array(
                'total' => 0,
                'data' => array()
            );
        }

        // Set default field
        $fieldList = '';
        $fieldQuery = '';
        $fieldValue = '';

        //Escape query string
        $sKeyword = $this->escapeQueryString($sKeyword);

        // Build query
        $sQuery = "UserName:" . $sKeyword;

        //Put to query value
        $fieldValue = $sKeyword;

        //Put to query field
        $fieldQuery = 'UserName';

        // Get result
        $arrResponse = self::join($sQuery, "FacebookID", "FriendFacebookID", $this->FriendCore, "MyFacebookID:$iFacebookID", $iOffset, $iLimit, $sSort, $fieldList, $fieldQuery, $fieldValue);

        // Set return data
        return $arrResponse;
    }
    
     /**
     * Search facebook friend of current app By FacebookID and Query
     * @param string $intFbID
     * @param string $strQuery
     * @param array  $intLimit
     * @param int    $intOffset
     * @param string sKeyword
     */
    public function searchQueryFbFriendList($iFacebookID, $sQuery, $iOffset, $iLimit, $sKeyword='',$sSort="UserName asc") {
        // Check fbid
        if ($iFacebookID == "" || is_null($iFacebookID)) {
            return array(
                'total' => 0,
                'data' => array()
            );
        }
        
        //check empty 
        if(empty($sQuery))
        {
            $sQuery = '*:*';
        }

        // Set default field
        $fieldList = '';
        $fieldQuery = '';
        $fieldValue = '';

        //Put to query value
        $fieldValue = $sKeyword;

        //Put to query field
        $fieldQuery = 'UserName';

        // Get result
        $arrResponse = self::join($sQuery, "FacebookID", "FriendFacebookID", $this->FriendCore, "MyFacebookID:$iFacebookID", $iOffset, $iLimit, $sSort, $fieldList, $fieldQuery, $fieldValue);

        // Set return data
        return $arrResponse;
    }

    /**
     * Check exist facebook friend of current app By FacebookID
     * @param string $intFbID
     * @param bool
     */
    public function checkFbFriendListExist($iFacebookID) {
        // Make query
        $sQuery = "MyFacebookID:" . $iFacebookID;

        // Get result
        $arrSearchData = self::queryFriend($sQuery, 0, 1);

        // Check total
        if ($arrSearchData['total'] > 0) {
            return true;
        }

        return false;
    }

    /**
     * Get fb profile
     * @param type $iFacebookID
     * @return type
     */
    public function getFbProfile($iFacebookID) {
        // Make query
        $sQuery = "FacebookID:$iFacebookID";

        // Search data
        $arrSearchData = self::queryFacebook($sQuery, 0, 1);

        // Check total
        if ($arrSearchData['total'] > 0) {
            return $arrSearchData['data'][0];
        }

        // Set return data
        return array();
    }

    /**
     * Destructor
     */
    public function __destruct() {
        //Cleanup
        unset($this->FacebookInfos, $this->FacebookFriends, $this->FacebookAPI, $this->InfoCore, $this->FriendCore);
    }

    // ================================ PRIVATE ============================== //
    /**
     * Search news core
     * @param <string> $query
     * @param <int> $offset
     * @param <int> $limit
     * @param <string> $sort
     * @param <string> $fl
     * @return <array>
     */
    private function queryFriend($query, $offset, $limit, $sort='', $fl='', $qlf='', $qvf='', $debug=false) {
        //Try get all data from search server
        try {
            //Check query
            if (empty($query)) {
                return array(
                    'total' => 0,
                    'data' => array()
                );
            }

            //Put data search
            $arrQuery = array();
            $arrQuery['q'] = $query;
            $arrQuery['start'] = $offset;
            $arrQuery['rows'] = $limit;

            //Check sort
            if (!empty($sort)) {
                $arrQuery['sort'] = $sort;
            }

            //Check fl
            if (!empty($fl)) {
                $arrQuery['fl'] = $fl;
            }

            //Check q.lf
            if (!empty($qlf)) {
                $arrQuery['q.lf'] = $qlf;
            }

            //Check q.vf
            if (!empty($qvf)) {
                $arrQuery['q.vf'] = $qvf;
            }

            //Check debug
            if ($debug) {
                $arrQuery['q.vdg'] = 'on';
            }

            //Search data
            $arrData = $this->FacebookFriends->search($arrQuery);

            //Json decode data
            $arrData = unserialize($arrData);

            //Return data
            return array(
                'total' => $arrData['response']['numFound'],
                'data' => $arrData['response']['docs']
            );
        } catch (Exception $ex) {
            //Nothing
        }

        //Return default
        return array(
            'total' => 0,
            'data' => array()
        );
    }

    /**
     * Join core
     * @param type $query
     * @param type $src_field
     * @param type $dst_field
     * @param type $dst_core
     * @param type $dst_query
     * @param type $offset
     * @param type $limit
     * @param type $sort
     * @param type $fl
     * @param type $qlf
     * @param type $qvf
     * @param type $debug
     * @return type
     */
    private function join($query, $src_field, $dst_field, $dst_core, $dst_query, $offset, $limit, $sort='', $fl='', $qlf='', $qvf='', $debug=false) {
        //Try get all data from search server
        try {
            //Check query
            if (empty($query) || empty($src_field) || empty($dst_field) || empty($dst_core) || empty($dst_query)) {
                return array(
                    'total' => 0,
                    'data' => array()
                );
            }

            //Put data search
            $arrQuery = array();
            $arrQuery['q'] = $query;
            $arrQuery['fq'] = "{!join from={$dst_field} to={$src_field} fromIndex={$dst_core}}{$dst_query}";
            $arrQuery['start'] = $offset;
            $arrQuery['rows'] = $limit;

            //Check sort
            if (!empty($sort)) {
                $arrQuery['sort'] = $sort;
            }

            //Check fl
            if (!empty($fl)) {
                $arrQuery['fl'] = $fl;
            }

            //Check q.lf
            if (!empty($qlf)) {
                $arrQuery['q.lf'] = $qlf;
            }

            //Check q.vf
            if (!empty($qvf)) {
                $arrQuery['q.vf'] = $qvf;
            }

            //Check debug
            if ($debug) {
                $arrQuery['q.vdg'] = 'on';
            }
           // var_dump($arrQuery);exit;

            //Search data
            $arrData = $this->FacebookInfos->search($arrQuery);

            //Json decode data
            $arrData = unserialize($arrData);


            //Return data
            return array(
                'total' => $arrData['response']['numFound'],
                'data' => $arrData['response']['docs']
            );
        } catch (Exception $ex) {
            //Nothing
        }

        //Return default
        return array(
            'total' => 0,
            'data' => array()
        );
    }

    /**
     * Search news core
     * @param <string> $query
     * @param <int> $offset
     * @param <int> $limit
     * @param <string> $sort
     * @param <string> $fl
     * @return <array>
     */
    private function queryFacebook($query, $offset, $limit, $sort='', $fl='', $qlf='', $qvf='', $debug=false) {
        //Try get all data from search server
        try {
            //Check query
            if (empty($query)) {
                return array(
                    'total' => 0,
                    'data' => array()
                );
            }

            //Put data search
            $arrQuery = array();
            $arrQuery['q'] = $query;
            $arrQuery['start'] = $offset;
            $arrQuery['rows'] = $limit;

            //Check sort
            if (!empty($sort)) {
                $arrQuery['sort'] = $sort;
            }

            //Check fl
            if (!empty($fl)) {
                $arrQuery['fl'] = $fl;
            }

            //Check q.lf
            if (!empty($qlf)) {
                $arrQuery['q.lf'] = $qlf;
            }

            //Check q.vf
            if (!empty($qvf)) {
                $arrQuery['q.vf'] = $qvf;
            }

            //Check debug
            if ($debug) {
                $arrQuery['q.vdg'] = 'on';
            }

            //Search data
            $arrData = $this->FacebookInfos->search($arrQuery);

            //Json decode data
            $arrData = unserialize($arrData);

            //Return data
            return array(
                'total' => $arrData['response']['numFound'],
                'data' => $arrData['response']['docs']
            );
        } catch (Exception $ex) {
            //Nothing
        }

        //Return default
        return array(
            'total' => 0,
            'data' => array()
        );
    }

}

