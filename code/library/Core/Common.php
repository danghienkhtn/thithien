<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Core_Common
{
   /*
    *  User
    */
    public static $users                = null;

    /**
     *
     * Stored permission value of user
     * @var type
     */
    public static $permissionUserList   = null;

    /**
     *
    /**
     *
     * Store list menu display for user
     * @var type
     */
    public static $listMenuAllow        = null;

    /**
     * Buffer content
     * @param type $rawHtml
     * @return type
     */
    public static function getHtmlContentBuffered($rawHtml)
    {
        return Core_Global::getHtmlContent(Core_Filter::stripBuffer($rawHtml));
    }


     /**
     * Get Deny action of current user
     * @param type $username
     * @return type
     */
    public static function getListDeny($username)
    {
        if (empty(self::$permissionUserList[$username]))
        {
            $user = self::getUserConfig();
            foreach ($user as $i => $value)
            {
                if ($value["id"] == $username)
                {
                    self::$permissionUserList[$username] = $value["permission"];
                }
            }
        }

        return self::$permissionUserList[$username];
    }


    /**
     *
     * @param type $username
     * @return type
     */
    public static function getPermissionAccessMenu($username){
        if(!isset(self::$listMenuAllow[$username])){
            global $globalMenu;

            //Result
            $arrMenuAllow = array();

            //Get permission of user
            $perMissionVal = self::getListDeny($username);

            //Get number item of menu
            $iNumberItem = sizeof($globalMenu);

            //Loop and compare value to check
            for($i = 0; $i < $iNumberItem; $i++){
                $tmpVal = (2 << $i) / 2;

                if(($tmpVal & $perMissionVal) > 0){
                    $arrMenuAllow[] = $tmpVal;
                }
            }

            self::$listMenuAllow[$username] = $arrMenuAllow;
        }

        //Return array menu allow
        return self::$listMenuAllow[$username];
    }

    /**
     *
     * @param type $username
     * @return type
     */

    public static function sortArrayTree($input, $fieldID, $fieldParent,$iToplevel, $fieldCheckTop,$iTopID=-1 )
    {
        //Init Result
        $output = array();
        $all = array();
        $dangling = array();
        $arrAllAccountID = array();

        // Initialize arrays
        foreach ($input as $entry) {
            $entry['children'] = array();
            $id = $entry[$fieldID];

            // If this is a top-level node, add it to the output immediately
            if ($entry[$fieldParent] == $iToplevel || $entry[$fieldCheckTop] == $iTopID) {
                $all[$id] = $entry;
                $output[] =& $all[$id];

            // If this isn't a top-level node, we have to process it later
            } else {
                $dangling[$id] = $entry;
            }

            $arrAllAccountID[] = $entry[$fieldID];
        }

        if(empty($output))
        {
            return array();
        }


        // Process all 'dangling' nodes
        while (count($dangling) > 0) {
            foreach($dangling as $entry) {
                $id = $entry[$fieldID];
                $pid = $entry[$fieldParent];

                // If the parent has already been added to the output, it's
                // safe to add this node too
                if(!in_array($pid,$arrAllAccountID))
                {
                    unset($dangling[$id]);
                }
                else
                {
                    if (isset($all[$pid]))
                    {
                        $all[$id] = $entry;
                        $all[$pid]['children'][] =& $all[$id];

                        unset($dangling[$entry[$fieldID]]);
                    }
                }


            }
        }

        //return result
        return $output;
    }
    /**
     *
     * @param type $username
     * @return type
     */

    public static function rederTreeById($arrResult)
    {
        $result='<ul>';
           foreach($arrResult as $value)
           {
                   if(!empty($value['children'])) {
                           $result.= '<li>'.self::renderHTML($value['picture'], $value['name'], $value['position'],$value['link']);
                           $result.=self::rederTreeById($value['children']);
                           $result.= '</li>';
                   } else {
                           $result.= '<li>'.self::renderHTML($value['picture'], $value['name'], $value['position'],$value['link']).'</li>';
                   }
           }
           $result.= '</ul>';

           return $result;
   }
  /**
     *
     * @param type $username
     * @return type
     */
   public static function renderHTML($avatar, $name, $position, $link='#')
   {
         $picture='http://placehold.it/100/EFEFEF/AAAAAA&text=no+image';

         if(!empty($avatar))
         {
             $picture = PATH_AVATAR_URL.'/'.$avatar;
         }

        $result ='<div>
                       <div style="float:left;padding-right:7px">
                          <img src="'.$picture.'" width="50px;"/>
                       </div>
                       <div style="text-align:left">
                           <strong><a href="'.$link.'">'.$name.'</a></strong>
                           <p style="text-align:left;"><strong>'.$position.'</strong></p>
                       </div>
                  </div>';

        return $result;
    }

    /**
     *
     * @param type $username
     * @return type
     */

    public static function generate_image_thumbnail($source_image_path, $thumbnail_image_path, $maxWidth= 720,$maxHeight= 720)
    {
        list($source_image_width, $source_image_height, $source_image_type) = getimagesize($source_image_path);
        switch ($source_image_type) {
            case IMAGETYPE_GIF:
                $source_gd_image = imagecreatefromgif($source_image_path);
                break;
            case IMAGETYPE_JPEG:
                $source_gd_image = imagecreatefromjpeg($source_image_path);
                break;
            case IMAGETYPE_PNG:
                $source_gd_image = imagecreatefrompng($source_image_path);
                break;
        }
        if ($source_gd_image === false) {
            return false;
        }

        $source_aspect_ratio = $source_image_width / $source_image_height;
        $thumbnail_aspect_ratio = $maxWidth / $maxHeight;
        if ($source_image_width <= $maxWidth && $source_image_height <= $maxHeight) {
            $thumbnail_image_width = $source_image_width;
            $thumbnail_image_height = $source_image_height;
        } elseif ($thumbnail_aspect_ratio > $source_aspect_ratio) {
            $thumbnail_image_width = (int) ($maxHeight * $source_aspect_ratio);
            $thumbnail_image_height = $maxHeight;
        } else {
            $thumbnail_image_width = $maxWidth;
            $thumbnail_image_height = (int) ($maxWidth / $source_aspect_ratio);
        }
        $thumbnail_gd_image = imagecreatetruecolor($thumbnail_image_width, $thumbnail_image_height);
        imagecopyresampled($thumbnail_gd_image, $source_gd_image, 0, 0, 0, 0, $thumbnail_image_width, $thumbnail_image_height, $source_image_width, $source_image_height);
        imagejpeg($thumbnail_gd_image, $thumbnail_image_path, 90);
        imagedestroy($source_gd_image);
        imagedestroy($thumbnail_gd_image);
        return true;
    }

     /**
     *
     * @param type $username
     * @return type
     */

    public static function covertDateFromSolr($date)
    {
         $date = str_replace('T', ' ',$date);
         $date = str_replace('00:00:00Z', ' ',$date);
         return $date;
    }

    public static function formatDateTime($sTime,$format = 'd-m-Y')
    {
        $sTime  = trim($sTime);
        if (empty($sTime))
            return '';
        else{
            $sTimeTmp  = explode('-',$sTime);
            if(strlen($sTimeTmp[2]) > 3)
                return $sTime;
        }
        $newDate = new DateTime($sTime);
        return $newDate->format($format);

    }
    /*
     *
     */

    public static function SubFullStrings ($string, $start=0 , $len=20, $charlim = '...')
    {
        //Strip tags html
        $string = strip_tags($string);

        //Check length
        if(Core_String::lengthString($string, true, 'UTF-8') <= $len)
        {
            return $string;
        }

        //Get list chars
        $arrList = explode(' ', $string);

        //If one element
        if(count($arrList) == 1)
        {
            return Core_String::subString($string, $start, $len);
        }

		//Loop to check data
        $idexLoop = 0;
        $lenNumber = 0;
        $bLastWorldTooLong = false;
        foreach($arrList as $spliceString)
        {
            //Add length
            $lenNumber += Core_String::lengthString($spliceString, true, 'UTF-8');

            if (Core_String::lengthString($spliceString, true, 'UTF-8') > $len)
                $bLastWorldTooLong = true;

            //Incement len
            $idexLoop++;

            //Check length
            if($lenNumber >= $len)
            {
                break;
            }
        }

        if ($bLastWorldTooLong)
            $idexLoop--;

        //Get list slice
        $arrNewList = array_slice($arrList, $start, $idexLoop);

        //Get string
        $newString = implode(' ', $arrNewList);

        if ($bLastWorldTooLong)
        {
            $iNewStringLength = Core_String::lengthString($newString, true, 'UTF-8');

            if ($iNewStringLength < $len)
            {
                $sLastWord = $arrList[$idexLoop];
                $sExtraCut = Core_String::subString($sLastWord, 0, ($len - $iNewStringLength));
                $newString .= $sExtraCut;
            }
        }

        //Check string
        if($newString == $string)
        {
            return $string;
        }

        //Return default
        return (strlen($string) > $len) ?  $newString.$charlim : $newString;
    }

    //check is in day
	public static function inDay($dateTime1, $dateTime2)
    {
    	$ts = $dateTime2 - $dateTime1;
    	if($ts > 86400){
    		return date("H:i d-m-Y", $dateTime1);
    	}
    	return '';

    }

    public static function isToDay($dateTime){

    	if (date('Y-m-d') == date('Y-m-d', $dateTime)) {
    		return true;
    	}
    	return false;
    }

    //difference betwen time
    public static function differencetime($dateTime1, $dateTime2=0)
    {
        $val='';
        if($dateTime2 ==0){
            $dateTime2 = time();
        }


       $localObj = Core_Global::getLocalesIni();


    	$ts = $dateTime2 - $dateTime1;

    	if($ts>31536000) $val = round($ts/31536000,0).' '.$localObj->year;
    	else if($ts>2419200) $val = round($ts/2419200,0).' '.$localObj->month;
    	else if($ts>604800) $val = round($ts/604800,0).' '.$localObj->week;
    	else if($ts>86400) $val = round($ts/86400,0).' '.$localObj->day;
    	else if($ts>3600) $val = round($ts/3600,0).' '.$localObj->hour;
    	else if($ts>60) $val = round($ts/60,0).' '.$localObj->minute;
    	else $val = $ts.' '.$localObj->second;

    	return $val;
    }

    public static function colorProcess($percent)
    {
        $class = '';
        $percent = intval($percent);
        if($percent > 50 && $percent < 80)
            $class = 'bg-orange';
        else if($percent >=80)
            $class = ' bg-red';
        return $class;
    }

    public static function iconActionProcess($actionType)
    {
        if(is_null($actionType))
            return array('class'=>'','bg-color'=>'');

        $actionType = intval($actionType);
        switch($actionType)
        {
            case ActionLog::$create: return array('class'=>'fa-plus','bg-color'=>'bg-green');
            case ActionLog::$add: return array('class'=>'fa-plus','bg-color'=>'bg-green');
            case ActionLog::$update: return  array('class'=>'fa-pencil','bg-color'=>'bg-orange');
            case ActionLog::$delete: return array('class'=>'fa-remove','bg-color'=>'bg-red');

        }

    }

    public static function accountProcess($accountInfo)
    {
        if(!isset($accountInfo['account_id'])) {
            return false;
        }

        global $globalConfig;

        //get arr account
        $arrAccounts = AccountInfo::getInstance()->getAccountListShort(array($accountInfo['manager_id'],$accountInfo['leader_id']));

        $arrGeneral = General::getInstance()->getGeneralAtt(0, 1, 0, MAX_QUERY_LIMIT);

        // process avatar
//        $accountInfo['picture'] = trim($accountInfo['picture']);
//        $sAvatar  = PATH_AVATAR_URL.'/avatar_default.png';
//        $sAvatarDIR =   PATH_AVATAR_UPLOAD_DIR.'/avatar_default.png';
//        $accountInfo['picture'] = str_replace(PATH_AVATAR_URL . '/','', $accountInfo['picture']);
//        if(!empty($accountInfo['picture'])) {
//            $sAvatar = PATH_AVATAR_URL . '/' . $accountInfo['picture'];
//            $sAvatarDIR = PATH_AVATAR_UPLOAD_DIR . '/' . $accountInfo['picture'];
//        }

        $accountInfo['image_tag'] =  Core_Common::avatarProcess($accountInfo['picture']);
        $accountInfo['image_dir'] =  Core_Common::avatarDirProcess($accountInfo['picture']);
//        $accountInfo['avatar']  =  $sAvatar;

        // process position
        $general =  General::getInstance()->getGeneralByID($accountInfo['position']);
        $accountInfo['position_name']    = (empty($general)) ? '' : $general['name'];

        $groupMember = GroupMember::getInstance()->getGroupMemberByAccountAndGroupId($accountInfo['account_id'],$accountInfo['team_id']);
        $accountInfo['level'] = empty($groupMember) ? GroupMember::$staff : $groupMember['level'];

        // Process Team Name
        // get All Groups
        $arrGroupAlls = Group::getInstance()->getGroupAll(0, MAX_QUERY_LIMIT);//get group list all
        $arrGroup = array();

        foreach($arrGroupAlls['data'] as $group){
            $arrGroup[$group['group_id']] = $group['group_name'];
        }

        $arrColumnDate = array(array('name'=>'birthday','format'=>'d-m-Y'),array('name'=>'start_date','format'=>'d-m-Y'),
                                array('name'=>'identity_date','format'=>'d-m-Y'),array('name'=>'passport_date','format'=>'d-m-Y'));

        $accountInfo    = Core_Common::convertTimeToStringByArrayObject($arrColumnDate,$accountInfo);
        // process team name
//        $group = Group::getInstance()->getGroupByID($accountInfo['team_id']);
//        $accountInfo['team_name']               = empty($group) ? '' : $group['group_name'];

        // process activity
        $accountInfo['active_name']                  = '';//($accountInfo['active'] == 1) ? 'Deactivate' : 'Active';

        // process place of birthday
        $accountInfo['place_of_birth_name']          = array_key_exists ($accountInfo['place_of_birth'],$globalConfig['province']) ? $globalConfig['province'][$accountInfo['place_of_birth']] : '';

        // process home town
        $accountInfo['home_town_name']               = array_key_exists ($accountInfo['home_town'],$globalConfig['province']) ? $globalConfig['province'][$accountInfo['home_town']] : '';

        // process Identity Place
        $accountInfo['identity_place_name']          = array_key_exists ($accountInfo['identity_place'],$globalConfig['province']) ? $globalConfig['province'][$accountInfo['identity_place']] : '';

        // process Passport Place
        $accountInfo['passport_place_name']          = array_key_exists ($accountInfo['passport_place'],$globalConfig['province']) ? $globalConfig['province'][$accountInfo['passport_place']] : '';

        // process bank's name
        $accountInfo['bank_account_id_name']         = array_key_exists ($accountInfo['bank_account_id'],$globalConfig['bank_account_id']) ? $globalConfig['bank_account_id'][$accountInfo['bank_account_id']] : '';

        // process bank branch's name
        $accountInfo['bank_account_branch_name']     = array_key_exists ($accountInfo['bank_account_branch'],$globalConfig['province']) ? $globalConfig['province'][$accountInfo['bank_account_branch']] : '';

        // process marital status
        $accountInfo['marital_status_name']          = array_key_exists( $accountInfo['marital_status'],$globalConfig['marital']) ? $globalConfig['marital'][$accountInfo['marital_status']] : '';

        // process contract type
        $accountInfo['contract_name']           = isset($arrGeneral[General::$contract][$accountInfo['contract_type']]) ?  $arrGeneral[General::$contract][$accountInfo['contract_type']] : '';

        // process level
        $accountInfo['level_name']                   = isset($arrGeneral[General::$level][$accountInfo['level']]) ?  $arrGeneral[General::$level][$accountInfo['level']] : '';

        // process manager
        $accountInfo['manager_name']             = array_key_exists($accountInfo['manager_id'],$arrAccounts) ? $arrAccounts[$accountInfo['manager_id']]['name'] : '';

        // process leader
        $accountInfo['leader_name']             = array_key_exists($accountInfo['leader_id'],$arrAccounts) ? $arrAccounts[$accountInfo['leader_id']]['name'] : '';

        // process gender
        $accountInfo['gender_name']             = array_key_exists($accountInfo['gender'],$globalConfig['gender']) ? $globalConfig['gender'][$accountInfo['gender']] : '';

        return $accountInfo;
    }

    public static function feedProcess($feed)
    {
        if(!isset($feed['feed_id']))
            return false;
        $keyRedisLike = Core_Global::getKeyPrefixCaching(REDIS_FEED_LIKE).$feed['feed_id'];

        $arrLogin   = Admin::getInstance()->getLogin();
        $feed['differencetime'] = Core_Common::differencetime($feed['create_date'], time());
        $feed['isLike'] = Core_Business_Nosql_Redis::getInstance()->isList($keyRedisLike, $arrLogin['accountID']);
        $feed['isOwner'] = false;

        $feed['image_tag_url1'] = empty($feed['image_url1']) ? '' : PATH_IMAGES_URL.'/'.$feed['image_url1'];
        $feed['image_tag_url2'] = empty($feed['image_url2']) ? '' : PATH_IMAGES_URL.'/'.$feed['image_url2'];
        if(Core_Common::checkOwnerOfObject($feed, $arrLogin['accountID']))
            $feed['isOwner'] = true;

        for($type = 1; $type < 6; $type++)
        {
            $feed['bFeedType'.$type] = ($feed['feed_type'] == $type) ? true : false;
        }


        $feed['photos'] = array();
        $feed['photo_ids'] = array();

        if($feed['feed_type'] == 3){//image
            //get photo feed (limit 5 photo)
            $arrPhotos = PhotoFeed::getInstance()->getPhotoFeedListByFeedId($feed['feed_id'], 0, 5);
            $arrPhotoIds = PhotoFeed::getInstance()->getPhotoFeedIDByFeedId($feed['feed_id'], 0, 30);

            $feed['photos'] = $arrPhotos;
            $feed['photo_ids'] = $arrPhotoIds;
        }

        $feed['sizePhoto'] = '';
        if(count($feed['photos']) == 1)
            $feed['sizePhoto'] = '500x280';
        else if(count($feed['photos']) >1)
            $feed['sizePhoto'] = '800x800';

        $feed['ownerInfo'] = AccountInfo::getInstance()->getAccountInfoByAccountID($feed['account_id']);
        $feed['ownerInfo'] = Core_Common::accountProcess($feed['ownerInfo']);

        $Group = Group::getInstance()->getGroupByID($feed['team_id_to']);
        $feed['group'] = Core_Common::groupProcess($Group);

        $message = $feed['message'];

        $regex = '/^un=[a-zA-Z0-9_.-]+/';
        $matches = preg_match($regex, $message);
//        Core_Common::var_dump($matches);
        $arrUserName = array();
        if($matches) {
            foreach ($matches as $match) {
                $user = explode('un=', $match);
                $userName = $user[1];
                if (!in_array($userName, $arrUserName)) {
                    $arrUserName [] = $userName;
                    $tagUserInfo = AccountInfo::getInstance()->getAccountInfoByUserName($userName);
                    $tagUserInfo = Core_Common::accountProcess($tagUserInfo);
                    if ($tagUserInfo) {
                        $userHtml = '&#8203;<span class="' . $tagUserInfo['username'] . '" data-value="' . $tagUserInfo['username'] . '">' .
                            '<a href="javascript:void(0);">​' .
                            '<img with="30px" class="userThumbnail img-responsive inline imgRichTag" src="' . $tagUserInfo['image_tag'] . '">' . $tagUserInfo['name'] .
                            '</a></span>&#8203;';
                        $message = str_replace('[un="' . $userName . '"]', $userHtml, $message);
                    }
                }
            }
            $feed['message'] = $message;
        }
        return $feed;
    }

    public static function commentProcess($comment)
    {
        if(!isset($comment['comment_id']))
            return false;


        $arrLogin   = Admin::getInstance()->getLogin();
        $comment['account_id'] = intval($comment['account_id']);
        $comment['isOwner'] = ($arrLogin['accountID'] == $comment['account_id']) ? true : false;
        $comment['owner_info'] = AccountInfo::getInstance()->getAccountInfoByAccountID( $comment['account_id']);
        $comment['owner_info'] = Core_Common::accountProcess($comment['owner_info']);
        $comment['differencetime'] = Core_Common::differencetime($comment['create_date'],time());
        $comment['sCreateDate'] = date(Core_Common::getFormatDateByLanguage($_SESSION['language'], $comment['create_date']), $comment['create_date']);
        return $comment;
    }

    public static function commentPhotoProcess($comment)
    {
        if(!isset($comment['id']))
            return false;


        $arrLogin   = Admin::getInstance()->getLogin();
        $comment['account_id'] = intval($comment['account_id']);
        $comment['isOwner'] = ($arrLogin['accountID'] == $comment['account_id']) ? true : false;
        $comment['owner_info'] = AccountInfo::getInstance()->getAccountInfoByAccountID( $comment['account_id']);
        $comment['owner_info'] = Core_Common::accountProcess($comment['owner_info']);
        $comment['differencetime'] = Core_Common::differencetime($comment['create_date'],time());
        $comment['sCreateDate'] = date(Core_Common::getFormatDateByLanguage($_SESSION['language'], $comment['create_date']), $comment['create_date']);
        return $comment;
    }

    public static function photoProcess($photo, $size = '800x800')
    {
        if(!isset($photo['photo_id']))
            return false;

        $arrLogin   = Admin::getInstance()->getLogin();
        $photo['image_tag'] = PATH_IMAGES_URL.'/'.$size.'/'.$photo['image_url'];
        $photo['isOwner'] = Core_Common::checkOwnerOfObject($photo, $arrLogin['accountID']);
        $photo['isLike'] = PhotoFeed::getInstance()->isLike($photo['photo_id'], $arrLogin['accountID']);
        return $photo;
    }

    public static function getFileImage($extension){

        if($extension != ''){
            if($extension == 'doc' || $extension == 'docx'){
                return  "/images/post-word.png";
            }

            if($extension == 'xls' || $extension == 'xlsx'){
                return "/images/post-excel.png";
            }

            if($extension == 'pdf'){
                return "/images//post-pdf.png";
            }

            if($extension == 'ppt' || $extension == 'pptx'){
                return "/images//post-ppt.png";
            }

        }
        return '';
    }


    public static function checkOwnerOfObject($obj, $accountId, $checkAccountId = true)
    {
        $result = false;
        if(isset($obj['owner']))
        {
            if(intval($obj['owner']) === intval($accountId))
                $result = true;
        }

        if(isset($obj['account_id']) && $checkAccountId)
        {
            if(intval($obj['account_id']) === intval($accountId))
                $result = true;
        }

        return $result;

    }

    public static function checkAdminOfObject($obj, $accountId)
    {
        $result = false;
        if(isset($obj['admin_id']))
        {
            if(intval($obj['admin_id']) === intval($accountId))
                $result = true;
        }
        return $result;

    }

    public static function checkManagerOfObject($obj, $accountId)
    {
        $result = false;
        if(isset($obj['manager_id']))
        {
            if(intval($obj['manager_id']) === intval($accountId))
                $result = true;
        }

        return $result;
    }


    public static function avatarProcess($sPicture, $defaultImage = 'avatar_default.png', $path = PATH_AVATAR_URL, $size = '')
    {
        $sPicture = trim($sPicture);
        $sAvatar  = $path.'/'.$defaultImage;
        $sPicture = str_replace($path . '/','', $sPicture);
        if(!empty($sPicture)) {
            $sAvatar = $path .$size. '/' . $sPicture;
        }else{
            $sAvatar = $path .$size. '/' . $defaultImage;
        }
        return $sAvatar;
    }

    public static function fixAvatarName($sAvatar)
    {
        $arrAvatar =    explode('/',$sAvatar);
        return end($arrAvatar);
    }




    public static function avatarDirProcess($sPicture)
    {
        $sPicture = trim($sPicture);
        $sPicture = str_replace(PATH_AVATAR_URL . '/','', $sPicture);
        $sAvatarDIR =   PATH_AVATAR_UPLOAD_DIR.'/avatar_default.png';

        if(!empty($sPicture)) {
            $sAvatarDIR = PATH_AVATAR_UPLOAD_DIR . '/' .$sPicture;
        }
        return $sAvatarDIR;
    }

    public static function  arrAccountProcess($arrAccountInfo)
    {
        if(!is_array($arrAccountInfo))
            return false;
        foreach($arrAccountInfo as $key=>$accountInfo)
            $arrAccountInfo[$accountInfo['account_id']]  = Core_Common::accountProcess($accountInfo);

        return $arrAccountInfo;
    }

    public static function groupProcess($group,$accountId = 0)
    {
        global $globalConfig;
        if(!isset($group['group_id']))
            return false;

        if($accountId == 0) {
            $arrLogin = Admin::getInstance()->getLogin();
            $accountId = $arrLogin['accountID'];
        }


        // process image url
        $group['image_tag'] = Core_Common::avatarProcess($group['image_url'],GroupAvatar,PATH_GROUPS_URL,'/200x200');
        $group['imageUrl'] = '';
        $group['image_name'] = '';
        if(!empty($group['image_url'])) {
            $group['image_name']    =   PATH_GROUPS_URL . '/200x200/' . $group['image_url'];
            $group['imageUrl']      = '<img width="50px" height="50px" class="imgs-sub-circle" src="' .$group['image_tag']. '">';
        }


        // process privacy
        $group['sPrivacy']      = 'private';
        if($group['is_public'])
            $group['sPrivacy']   = 'public';

        // process active checkbox
        $group['active_checkbox']       = '<div class="checkbox-custom checkbox-success"><input type="checkbox" data-action="group-change-active" value="0" disabled/><label></label></div>';
        if($group['active'])
            $group['active_checkbox']   = '<div class="checkbox-custom checkbox-success"><input type="checkbox" data-action="group-change-active" value="1" checked disabled/><label></label></div>';

        // process type name
        $arrGroupTypes  = array_flip($globalConfig['group_type']);
        $group['sType'] = isset($arrGroupTypes[$group['group_type']]) ? $arrGroupTypes[$group['group_type']] : '';

        // process admin name
        $adminInfo  = AccountInfo::getInstance()->getAccountInfoByAccountID($group['admin_id']);
        $group['admin_info'] = Core_Common::accountProcess($adminInfo);
        $group['admin_name']    = $adminInfo['name'];

        $managerInfo  = AccountInfo::getInstance()->getAccountInfoByAccountID($group['manager_id']);
        $group['manager_info'] = Core_Common::accountProcess($managerInfo);
        $group['manager_name']    = $managerInfo['name'];

        $group['is_admin'] = ($group['admin_id'] == $accountId) ? true : false;
        $group['is_manager'] = ($group['manager_id'] == $accountId) ? true : false;

        return $group;
    }

    public static function groupMemberProcess($groupMember)
    {
        global $globalConfig;
        if(!isset($groupMember['group_id'])) {
            return false;
        }
//        if(isset($groupMember['team_name'])) { //is account Info



            $group = Group::getInstance()->getGroupByID($groupMember['group_id']);
            $groupMember['is_admin'] = ($group['admin_id'] == $groupMember['account_id']) ? true : false;
            $groupMember['is_manager'] = ($group['manager_id'] == $groupMember['account_id']) ? true : false;
            $groupMember['group_name'] = $group['group_name'];

//        }else {
            // process image url
            $accountInfo = AccountInfo::getInstance()->getAccountInfoByAccountID($groupMember['account_id']);

            $groupMember['accountInfo'] = Core_Common::accountProcess($accountInfo);
//        }

        if($groupMember['level'] == 0)
            $groupMember['sLevel'] = $globalConfig['level'][GroupMember::$staff];
        else
            $groupMember['sLevel'] = isset($globalConfig['level'][$groupMember['level']]) ? $globalConfig['level'][$groupMember['level']] : $globalConfig['level'][GroupMember::$staff];
        return $groupMember;
    }

    public static function groupInviteProcess($groupInvite)
    {
        if(!isset($groupInvite['request_id']))
            return false;

        $groupInfo = Group::getInstance()->getGroupByID($groupInvite['group_id']);
        $groupInvite['groupInfo'] = $groupInfo;
        $accountFrom = AccountInfo::getInstance()->getAccountInfoByAccountID($groupInvite['account_from']);
        $groupInvite['accountFromInfo'] = Core_Common::accountProcess($accountFrom);

        $accountTo = AccountInfo::getInstance()->getAccountInfoByAccountID($groupInvite['account_to']);
        $groupInvite['accountToInfo'] = Core_Common::accountProcess($accountTo);
        return $groupInvite;
    }

    public static function generalProcess($general)
    {
        global $globalConfig;
        if(!isset($general['general_id']))
            return false;

        // process type name
        $arrGeneralType     = $globalConfig['general_type'];
        $general['sType']   = isset($arrGeneralType[ $general['type']]) ? $arrGeneralType[ $general['type']] : '';

        // process active checkbox
        $general['active_checkbox']       = '<div class="checkbox-custom checkbox-success"><input type="checkbox" data-action="group-change-active" value="0" disabled/><label></label></div>';
        if($general['active'])
            $general['active_checkbox']   = '<div class="checkbox-custom checkbox-success"><input type="checkbox" data-action="group-change-active" value="1" checked disabled/><label></label></div>';

        return $general;
    }

    public static function newsProcess($news)
    {
        global $globalConfig;
        if(!isset($news['news_id']))
            return false;

        // process type name
        $arrType     = $globalConfig['news_type'];
        $news['sType'] = isset($arrType[$news['type']]) ? $arrType[$news['type']] : '';

        // process active checkbox
        $news['active_checkbox']       = '<div class="checkbox-custom checkbox-success"><input type="checkbox" disabled/><label></label></div>';
        if($news['active'])
            $news['active_checkbox']   = '<div class="checkbox-custom checkbox-success"><input type="checkbox" checked disabled/><label></label></div>';

        // hot active checkbox
        $news['hot_checkbox']       = '<div class="checkbox-custom checkbox-danger"><input type="checkbox" disabled/><label></label></div>';
        if($news['ishot'])
            $news['hot_checkbox']   = '<div class="checkbox-custom checkbox-danger"><input type="checkbox" checked disabled/><label></label></div>';

        // process image url
        $imgHtml = '<img width="50px" height="50px" src="'.PATH_NEWS_URL.'/'.$news['image_url'].'"/>';
        $news['image_tag']  =   !empty($news['image_url']) ? $imgHtml : '';
        $news['image_name'] = !empty($news['image_url']) ? PATH_NEWS_URL.'/'.$news['image_url'] : '';

        return $news;
    }

    public static function actionLogsProcess($actionLogs){
        if(!isset($actionLogs['id']))
            return false;

        global $globalConfig;
        // process action type name
        $actionLogs['sType']    = !(empty($globalConfig['action_log_type'][$actionLogs['type']])) ? $globalConfig['action_log_type'][$actionLogs['type']] : '';

        // process action name
        $actionLogs['sActionName']    = !(empty($globalConfig['action_log_action'][$actionLogs['action']])) ? $globalConfig['action_log_action'][$actionLogs['action']] : '';

        return $actionLogs;
    }

    public static function workflowActionProcess($workflowAction){
        if(empty($workflowAction['action_id']))
            return false;

        global $globalConfig;
        $iCountryId = $workflowAction['country_id'];
        // process type name
        $arrType    = $globalConfig['workflow_action'];
        $workflowAction['action_name']    = !empty($arrType[$workflowAction['action_type']]) ? $arrType[$workflowAction['action_type']] : '';

        // process authority name
        $arrAuthority    = $globalConfig['workflow_role'];
        $workflowAction['sRole']    = !empty($arrAuthority[$workflowAction['role_id']]) ? $arrAuthority[$workflowAction['role_id']] : '';

        // process account info name
        $accountId  =   trim($workflowAction['account_other']);
        $workflowAction['account_name'] = '';
        if(!empty($accountId)){
            $accountInfo    = AccountInfo::getInstance()->getAccountInfoByAccountID($accountId);
            $workflowAction['account_name'] = !empty($accountInfo) ? $accountInfo['name'] : '';
        }
        $workflowAction['countryName'] = isset($globalConfig['country'][$iCountryId]) ? $globalConfig['country'][$iCountryId] : '';
        // process active checkbox
        $workflowAction['active_checkbox']       = '<div class="checkbox-custom checkbox-success"><input type="checkbox" disabled/><label></label></div>';
        if($workflowAction['active'])
            $workflowAction['active_checkbox']   = '<div class="checkbox-custom checkbox-success"><input type="checkbox" checked disabled/><label></label></div>';

        return $workflowAction;

    }

    public static function albumProcess($album)
    {
        global $globalConfig;
        $arrLogin = Admin::getInstance()->getLogin();
        if(!isset($album['album_id']))
            return false;

        //process album type
        $album['sType'] = !empty($globalConfig['album_type'][$album['type']]) ? $globalConfig['album_type'][$album['type']] : '';
        // process group name
        $group  = Group::getInstance()->getGroupByID($album['group_id']);
        $album['sGroupName'] = '';
        if(!empty($group))
            $album['sGroupName'] = ($group['group_id'] === $album['group_id']) ? $group['group_name'] : '';

        // process image tag
        $album['image_tag'] = !empty($album['image_url']) ? PATH_IMAGES_URL . '/800x800/'.$album['image_url'] : '';
        $album['image_tag'] = ($album['is_other']) ? PATH_IMAGES_URL.'/'.AlbumOther : $album['image_tag'];
        $album['isOwner'] = Core_Common::checkOwnerOfObject($album, $arrLogin['accountID']);

        $ownerInfo  = AccountInfo::getInstance()->getAccountInfoByAccountID($album['account_id']);
        $album['owner_info'] = Core_Common::accountProcess($ownerInfo);
        return $album;
    }

    public static function projectMemberProcess($projectMember)
    {
        if(!isset($projectMember['id']))
            return false;
        $arrColumnDate = array(array('name'=>'start_date','format'=>'d-m-Y'),array('name'=>'end_date','format'=>'d-m-Y'));

        global $globalConfig;
        foreach($arrColumnDate as $column)
        {
            if($projectMember[$column['name']] == '0000-00-00')
                $projectMember[$column['name']] = '';
            else{
                $date = new DateTime($projectMember[$column['name']]);
                $projectMember[$column['name']] = $date->format($column['format']);
            }
        }

        // process member name
        $memberInfo    = AccountInfo::getInstance()->getAccountInfoByAccountID($projectMember['account_id']);
        $projectMember['member_info'] = Core_Common::accountProcess($memberInfo);
        $projectMember['account_name']  = empty($memberInfo['name']) ? '' : $memberInfo['name'];

        $projectMember['sLevel'] = isset($globalConfig['level'][$projectMember['level']]) ? $globalConfig['level'][$projectMember['level']] : '';
        return $projectMember;
    }

    public static function projectProcess($project)
    {
        if(!isset($project['id']))
            return false;
        // process manager name
        $managerInfo    = AccountInfo::getInstance()->getAccountInfoByAccountID($project['manager_id']);
        $project['manager_info'] = Core_Common::accountProcess($managerInfo);
        $project['manager_name']  = $managerInfo['name'];

        // process admin name
        $adminInfo    = AccountInfo::getInstance()->getAccountInfoByAccountID($project['admin_id']);
        $project['admin_info'] = Core_Common::accountProcess($adminInfo);
        $project['admin_name']  = $adminInfo['name'];

        // process start date and end date
        $arrColumnDate = array(array('name'=>'start_date','format'=>'d-m-Y'),array('name'=>'end_date','format'=>'d-m-Y'));
        $project    = Core_Common::convertTimeToStringByArrayObject($arrColumnDate,$project);

        // process create and update date
        $project['sCreated'] = date('Y-m-d',$project['created']);
        $project['sUpdated'] = date('Y-m-d',$project['updated']);
        return $project;
    }

    public static function specialDayProcess($specialDay)
    {
        if(!isset($specialDay['id']))
            return false;

        $specialDay['sDateFrom'] = Core_Common::formatDateTime($specialDay['date_from']);
        $specialDay['sDateTo'] = Core_Common::formatDateTime($specialDay['date_to']);
        $specialDay['sCreated'] = date('Y-m-d',$specialDay['created']);
        $specialDay['sUpdated'] = date('Y-m-d',$specialDay['updated']);
        return $specialDay;
    }

    public static function statisticAbsenceHistoryProcess($history)
    {
        if(!isset($history['account_id']))
            return false;

        // process used
        $history['used']    = isset($history['used']) ? $history['used'] : 0;

        // process remain
        $history['remain']    = isset($history['remain']) ? $history['remain'] : 0;

        // process total
        $history['total']    = isset($history['total']) ? $history['total'] : 0;

        return $history;
    }

    public static function expenseRequestProcess($expenseRequest)
    {
        if(!isset($expenseRequest['id']))
            return false;
        global $globalConfig;
        $actionStatus = $globalConfig['action_status'];
        $currencies = $globalConfig['currencies'];
        $country = $globalConfig['country'];

        $expenseRequest['sCurrency'] = isset($currencies[$expenseRequest['currency']]) ? $currencies[$expenseRequest['currency']] : '';
        $expenseRequest['sCountry'] = isset($country[$expenseRequest['country_id']]) ? $country[$expenseRequest['country_id']] : '';

        $accountInfo = AccountInfo::getInstance()->getAccountInfoByAccountID($expenseRequest['account_id']);
        $expenseRequest['accountInfo'] = Core_Common::accountProcess($accountInfo);
        $expenseRequest['sStatus'] = (isset($actionStatus[$expenseRequest['status']])) ? $actionStatus[$expenseRequest['status']] : '';

        $expenseRequest['dExpectDate'] = new DateTime( $expenseRequest['expect_date']);
        $expenseRequest['sExpectDate'] = $expenseRequest['dExpectDate']->format('d-m-Y');

        $expenseActions = ExpenseAction::getInstance()->select($expenseRequest['id'],0,0,WorkflowAction::$approveRequest);
        $arrApproveName = array();
        foreach($expenseActions['data'] as $expenseAction)
        {
            $expenseAction = Core_Common::expenseActionProcess($expenseAction);
            $arrApproveName []= $expenseAction['accountInfo']['name'];
        }
        $expenseRequest['sApproveName'] = implode(',',$arrApproveName);
        $expenseRequest['sTotalAmount'] = number_format($expenseRequest['total_amount']).' '.$expenseRequest['sCurrency'];
        return $expenseRequest;

    }

    public static function expenseActionProcess($expenseAction,$locales = '')
    {
        if(!isset($expenseAction['id']))
            return false;
        global $globalConfig;
        $actionStatus = $globalConfig['action_status'];
        $workflowTypes = $globalConfig['workflow_action'];

        $accountInfo = AccountInfo::getInstance()->getAccountInfoByAccountID($expenseAction['account_id']);
        $expenseAction['accountInfo'] = Core_Common::accountProcess($accountInfo);

        $expenseAction['sStatus'] = (isset($actionStatus[$expenseAction['status']])) ? $actionStatus[$expenseAction['status']] : '';

        $workflowTypeName = '';
        $sectionName = '';
        $whoDoneAction = '';
        if($locales!= '') {
            switch ($expenseAction['action_type']) {
                case WorkflowAction::$createRequest :
                    $workflowTypeName = $locales->absence->request;

                    break;

                case WorkflowAction::$reviewRequest :
                    $workflowTypeName = $locales->absence->review;
                    $sectionName = $locales->absence->reviewSection;
                    $whoDoneAction = $locales->absence->reviewer;
                    break;

                case WorkflowAction::$approveRequest :
                    $workflowTypeName = $locales->absence->approve;
                    $sectionName = $locales->absence->approveSection;
                    $whoDoneAction = $locales->absence->approver;
                    break;

                case WorkflowAction::$confirmRequest :
                    $workflowTypeName = $locales->absence->confirm;
                    $sectionName = $locales->absence->confirmSection;
                    $whoDoneAction = $locales->absence->confirm;
                    break;
            }

        }
        $expenseAction['sWorkflowType'] = $workflowTypeName;
        $expenseAction['sSection'] = $sectionName;
        $expenseAction['whoDoneAction'] = $whoDoneAction;
        return $expenseAction;

    }

    public static function absenceRequestProcess($absenceRequest)
    {
        if(!isset($absenceRequest['request_id']))
            return false;
        global $globalConfig;
        $actionStatus = $globalConfig['action_status'];
        $accountInfo = AccountInfo::getInstance()->getAccountInfoByAccountID($absenceRequest['account_id']);
        $absenceRequest['accountInfo'] = Core_Common::accountProcess($accountInfo);
        $absenceRequest['sStatus'] = (isset($actionStatus[$absenceRequest['last_action_status']])) ? $actionStatus[$absenceRequest['last_action_status']] : '';
        $absenceRequest['dStartDate'] = new DateTime($absenceRequest['start_date']);
        $absenceRequest['sStartDate'] = $absenceRequest['dStartDate']->format('d-m-Y');

        $absenceRequest['dEndDate'] = new DateTime($absenceRequest['end_date']);
        $absenceRequest['sEndDate'] = $absenceRequest['dEndDate']->format('d-m-Y');
        return $absenceRequest;

    }

    public static function absenceDetailProcess($absenceDetail)
    {
        if(!isset($absenceDetail['id']))
            return false;
        global $globalConfig;

        $absenceDetail['used_date'] = Core_Common::formatDateTime($absenceDetail['used_date']);
        $absenceDetail['sType']     = isset($globalConfig['absence_date_type'][$absenceDetail['type']]) ? $globalConfig['absence_date_type'][$absenceDetail['type']]: '';
        if(isset($absenceDetail['last_action_status']))
            $absenceDetail['last_action_status'] = isset($globalConfig['action_status'][$absenceDetail['last_action_status']]) ? $globalConfig['action_status'][$absenceDetail['last_action_status']] : '';
        return $absenceDetail;
    }

    public static function absenceActionProcess($absenceAction)
    {
        if(!isset($absenceAction['absence_action_id']))
            return false;
        global $globalConfig;
        $actionStatus = $globalConfig['action_status'];

        if(isset($absenceAction['account_request_id']))
            $accountInfo = AccountInfo::getInstance()->getAccountInfoByAccountID($absenceAction['account_request_id']);
        else
            $accountInfo = AccountInfo::getInstance()->getAccountInfoByAccountID($absenceAction['account_id']);

        $absenceAction['accountInfo'] = Core_Common::accountProcess($accountInfo);

        $absenceAction['sStatus'] = (isset($actionStatus[$absenceAction['status']])) ? $actionStatus[$absenceAction['status']] : '';
        return $absenceAction;

    }

    public static function manualAbsenceProcess($manualAbsence)
    {
        if(!isset($manualAbsence['owner']))
            return false;
        global $globalConfig;
        $arrStatus = $globalConfig['group_public'];
        $dateTypes = $globalConfig['absence_date_type'];
        $manualAbsence['dDate'] = new DateTime($manualAbsence['date']);
        $manualAbsence['sDate'] = $manualAbsence['dDate']->format('d-m-Y');
        $ownerInfo  = AccountInfo::getInstance()->getAccountInfoByAccountID($manualAbsence['owner']);
        $manualAbsence['ownerInfo'] = Core_Common::accountProcess($ownerInfo);
        $accountInfo  = AccountInfo::getInstance()->getAccountInfoByAccountID($manualAbsence['account_id']);
        $manualAbsence['accountInfo'] = Core_Common::accountProcess($accountInfo);

        $manualAbsence['account_name'] =  $manualAbsence['accountInfo']['name'];
        $manualAbsence['sStatus'] = isset($arrStatus[$manualAbsence['status']]) ? $arrStatus[$manualAbsence['status']] : $arrStatus[0];
        $manualAbsence['sDateType'] = isset($dateTypes[$manualAbsence['date_type']]) ? $dateTypes[$manualAbsence['date_type']] : '';
        return $manualAbsence;
    }

    public static function mailProcess($mail, $backend = true)
    {
        $baseUrl = BASE_ADMIN_URL;
        if(!$backend)
            $baseUrl = BASE_URL;
        if(!isset($mail['ItemId']))
            return false;
        if(!$mail['IsRead'])
        {
            $mail['sSender']     = '<b>'.$mail['Sender'].'</b>';
            $mail['sSubject']    = '<b>'.$mail['Subject'].'</b>';
            $mail['sDateSent']   = '<b>'.$mail['DateSent'].'</b>';

        }else{
            $mail['sSender']      = $mail['Sender'];
            $mail['sSubject']     = $mail['Subject'];
            $mail['sDateSent']    = $mail['DateSent'];
        }

        $mail['iconAttachments'] = ($mail['HasAttachments']) ?  '<i class="fa fa-paperclip"></i>' : '';
        $mail['iconImportance']  = (trim($mail['Importance']) == 'High') ?  '<i class="fa fa-flag red"></i>' : '<i class="fa fa-flag"></i>';
        $mail['checkbox_delete'] = '<div class="checkbox-custom checkbox-primary"><input type="checkbox" data-action="check-delete" data-id="'.$mail['ItemId'].'" id="inputUnchecked" data-change-key="'.$mail['ChangeKey'].'" /><label for="inputUnchecked"></label></div>' ;
        $mail['aSubjects']       =   '<a href="'.$baseUrl.'/mail/detail?id='.urlencode($mail['ItemId']).'">'. $mail['sSubject'].'</a>';
        return $mail;
    }

    public static function attendanceStatistic($attendanceStatistic,$notAccountInfo = false)
    {
        if(!isset($attendanceStatistic['id']))
            return false;

        if(!$notAccountInfo) {
            $accountInfo = AccountInfo::getInstance()->getAccountInfoByAccountID($attendanceStatistic['account_id']);
            $attendanceStatistic['accountInfo'] = Core_Common::accountProcess($accountInfo);
        }

        $attendanceStatistic['dDate'] = new DateTime(date('Y-m-d',$attendanceStatistic['date']));
        $attendanceStatistic['sDate'] = (empty($attendanceStatistic['date'])) ? '' : date('d-m-Y',$attendanceStatistic['date']);

        $dCheckIn = new DateTime();
        $attendanceStatistic['dCheckIn'] = $dCheckIn->setTimestamp($attendanceStatistic['check_in']);
        empty($attendanceStatistic['check_in']) ? $attendanceStatistic['dCheckIn']->setTime(0,0,0): '';
        $attendanceStatistic['sCheckIn'] = (empty($attendanceStatistic['check_in'])) ? '' : date('H:i:s',$attendanceStatistic['check_in']);

        $dCheckOut = new DateTime();
        $attendanceStatistic['dCheckOut'] = $dCheckOut->setTimestamp($attendanceStatistic['check_out']);
        empty($attendanceStatistic['check_out']) ? $attendanceStatistic['dCheckOut']->setTime(0,0,0): '';
        $attendanceStatistic['sCheckOut'] = (empty($attendanceStatistic['check_out'])) ? '' : date('H:i:s',$attendanceStatistic['check_out']);

        $attendanceStatistic['s2hLess'] = (empty($attendanceStatistic['2h_less'])) ? '' : date('H:i:s',$attendanceStatistic['2h_less']);
        $attendanceStatistic['s2hEarly'] = (empty($attendanceStatistic['2h_early'])) ? '' : date('H:i:s',$attendanceStatistic['2h_early']);
        return $attendanceStatistic;
    }

    public static function attendanceConfirm($attendanceConfirm)
    {
        if(!isset($attendanceConfirm['id']))
            return false;

        $accountInfo = AccountInfo::getInstance()->getAccountInfoByAccountID($attendanceConfirm['account_id']);
        $attendanceConfirm['accountInfo'] = Core_Common::accountProcess($accountInfo);

        $ownerInfo = AccountInfo::getInstance()->getAccountInfoByAccountID($attendanceConfirm['owner']);
        $attendanceConfirm['ownerInfo'] = Core_Common::accountProcess($ownerInfo);

        $date = new DateTime();
        $date->setTimestamp($attendanceConfirm['date']);
        $attendanceConfirm['dDate'] = $date;

        $created = new DateTime();
        $created->setTimestamp($attendanceConfirm['created']);
        $attendanceConfirm['dCreated'] = $created;

        return $attendanceConfirm;
    }

	public static function convertStringToYMD($string, $delimiter = '-'){
		if(empty($string)){
			return '0000-00-00';
		}
        $explode = explode($delimiter, $string);
        if(count($explode) < 3)
            return '0000-00-00';
		list($d, $m, $y) = explode($delimiter, $string);

		if(!is_numeric($d) || !is_numeric($m) || !is_numeric($y)){
			return '0000-00-00';
		}

		if(checkdate($m,$d,$y)){
			return $y .'-'. $m .'-'. $d;
		}

		return '0000-00-00';
	}


    public static function checkSpecialDay($sDate,$account_id,$dataDateType = 1, &$arrDateType = array())
    {
        global $globalConfig;
        $arrDateType[$sDate] []= intval($dataDateType);

        $dateType = isset($globalConfig['absence_date_type'][$dataDateType]) ?  $globalConfig['absence_date_type'][$dataDateType] : '';

        $newDate    = new DateTime($sDate);

        $arrError   =   array('message'=>'','color' =>true, 'date_from' => $newDate->format('Y-m-d'), 'date_to' => $newDate->format('Y-m-d'), 'data_type' => $dateType );
        $iDayOfWeek = $newDate->format('w');

        // validate weekend day
        if($iDayOfWeek == 0 || $iDayOfWeek == 6)
             $arrError = array('message' => $newDate->format('d-m-Y').' is week-end. Please exclude this day.', 'color' =>false , 'date_from' => $newDate->format('Y-m-d'), 'date_to' => $newDate->format('Y-m-d'),  'data_type' => $dateType);

        // validate special day
        $holidays    = SpecialDay::getInstance()->select('',SpecialDay::$holiday,$sDate,$sDate);
        $compensations    = SpecialDay::getInstance()->select('',SpecialDay::$compensation,$sDate,$sDate);

        if(!empty($holidays['data'])) {
            $holiday = $holidays['data'][0];
            $arrError = array('message' => $newDate->format('d-m-Y') . ' is ' . $holiday['name'].'. Please exclude this day.', 'color' => false, 'date_from' => $newDate->format('Y-m-d'), 'date_to' => $newDate->format('Y-m-d'), 'data_type' => $dateType);
        }

        if(!empty($compensations['data']))
        {
            $arrError = array('message' => '', 'color' => true, 'date_from' => $newDate->format('Y-m-d'), 'date_to' => $newDate->format('Y-m-d'), 'data_type' => $dateType);
        }



        $dayAlreadyFull = AbsenceDetail::getInstance()->select($account_id, 0, 0, $newDate->format('Y-m-d'), 0, Absence::$full);

        if(!empty($dayAlreadyFull['data'])){
            $arrError = array('message' => $newDate->format('d-m-Y') . ' used day. Please exclude this day. ', 'color' => false, 'date_from' => $newDate->format('Y-m-d'), 'date_to' => $newDate->format('Y-m-d'), 'data_type' => $dateType);
        }else{
//            echo $dataDateType.'<br/>';
            $dataDateType = ($dataDateType == Absence::$full) ? 4 : $dataDateType;
//            echo $dataDateType.'<br/>';
            $dayAlready = AbsenceDetail::getInstance()->select($account_id, 0, 0, $newDate->format('Y-m-d'), 0, $dataDateType);
//            echo  $newDate->format('Y-m-d');
//            Core_Common::var_dump($dayAlready);
            if(!empty($dayAlready['data'])) {
                $arrError = array('message' => $newDate->format('d-m-Y') . ' used day. Please exclude this day. ', 'color' => false, 'date_from' => $newDate->format('Y-m-d'), 'date_to' => $newDate->format('Y-m-d'), 'data_type' => $dateType);
            }
        }

        // check duplicate ex when chose 5-10-2016 am and 5-10-2016 full
        if(count($arrDateType[$sDate]) > 1)
        {
            $iNo = 0;
            foreach($arrDateType[$sDate] as $iDateType)
            {
                if($iDateType == $dataDateType)
                    $iNo++;

                if($iDateType == Absence::$full || $iNo > 1)
                {
                    $arrError = array('message' => $newDate->format('d-m-Y') . ' is duplicated. Please exclude this day. ', 'color' => false, 'date_from' => $newDate->format('Y-m-d'), 'date_to' => $newDate->format('Y-m-d'), 'data_type' => $dateType);
                }

            }
        }
//        if(in_array(Absence::$full,$arrDateType[$sDate])) {
//            Core_Common::var_dump($arrDateType,false);
////            echo $sDate . ' - ' . $dataDateType . '<br/>';
//        }

        return $arrError;
    }

    public static  function getDateString(){
        global $globalConfig;

        $str = '<select class="dateType" title="#index#" name="dateType#index#">';

        foreach ($globalConfig['absence_date_type'] as $key => $value){
            $str .= '<option value="' . $key . '">' . $value . '</option>';
        }

        $str .= '</select>';
        return $str;
    }
	/**
	 *
	 * @param unknown $string  = d-m-Y
	 * @return multitype:|multitype:unknown multitype: |Ambigous <multitype:, multitype:unknown multitype: >
	 */
	public static function getDate($string){

		$arr = array();

		if(empty($string)){
			return $arr;
		}

		list($d, $m, $y) = explode('-', $string);

		if(!is_numeric($d) || !is_numeric($m) || !is_numeric($y)){
			return $arr;
		}

		if(checkdate($m,$d,$y)){

			return $arr = array('y'=>$y, 'm' => $m, 'd' => $d);
		}

		return $arr;
	}

	public static function convertStringEmptyToZero($input){
		if(empty($input)){
			return 0;
		}
		return $input;
	}

	public static function convertTimeToStringByArrayObject($arrColumnDate,$arrObject)
    {
        foreach($arrColumnDate as $column)
        {
            if($arrObject[$column['name']] == '0000-00-00')
                $arrObject[$column['name']] = '';
            else{
                $date = new DateTime($arrObject[$column['name']]);
                $arrObject[$column['name']] = $date->format($column['format']);
            }
        }
        return $arrObject;
    }

    public static function var_dump($value,$die = true)
    {
        echo '<pre>';
        var_dump($value);
        echo '</pre>';
        if($die)
            die;
    }

    public static function getAccountIDByRoleId($arrAccountInfo, $iRoleId, $iAccountIDOther)
    {
        if (!empty($iAccountIDOther) && $iAccountIDOther > 0) {
            return $iAccountIDOther;
        } else if ($iRoleId == 2) {//sub/leader
            return $arrAccountInfo['leader_id'];
        }
        return $arrAccountInfo['manager_id'];//manager 3
    }


    public static function checkPermission($adminDetail = array(), $controllerName, $actionName) {

        global $adminConfig;

        $myPermission = array();

        /*$allowPermission = array(
            'index' => array('index', 'setlanguage'),
            'app' => array('import', 'clean', 'clear', 'recommend', 'topdownload'),
        );*/

        if (!empty($adminDetail)) {

            $arrRole = array();

            if(!empty($adminDetail['role_id'])){
                $arrRole = explode(',', $adminDetail['role_id']);
            }

            if(!empty($arrRole)){

                foreach ($arrRole as $roleId){
                    $myPermission[] = Role::getInstance()->selectOne($roleId);
                }
            }

        }
//        Core_Common::var_dump($adminDetail);
        $acl = new Zend_Acl();

        //add resource
        $fullPermission = $adminConfig['permission'];
//        Core_Common::var_dump($fullPermission);
        $arrController = array_keys($fullPermission);

        //array_push($arrController, 'index');

        foreach ($arrController as $controller) {
            $acl->add(new Zend_Acl_Resource($controller));
        }

        //add role
        $acl->addRole(new Zend_Acl_Role('guest'));

        $acl->addRole(new Zend_Acl_Role('member'), 'guest');

        $acl->addRole(new Zend_Acl_Role('root'), 'member');

        //set default permission
        $acl->allow('guest', 'login');
        $acl->allow('member', 'logout');
        $acl->allow('member', 'ajax');
        $acl->allow('root');

        /*foreach ($allowPermission as $controller => $arrAction) {
            foreach ($arrAction as $action) {
                $acl->allow('member', $controller, $action);
            }
        }*/

        //set permission
        if (empty($adminDetail)) {

            $role = "guest";

        } else {
            if ($adminDetail['super_admin'] == SUPER_ADMIN) {

                $role = "root";
                $myPermission = array();

                foreach ($fullPermission as $controller => $permission) {

                    if (!empty($permission['action'])) {

                        $data = isset($data) ? $data : "";

                        $myPermission[$controller] = 0;

                        $keys = array();

                        foreach ($permission['action'] as $action => $per) {

                            $key = intval($per['value']);

                            if (!in_array($key, $keys)) {

                                array_push($keys, $key);

                                $myPermission[$controller] += $key;
                            }
                        }
                    }
                }
            } else {

                $role = "member";
                $permissionTmp = array();

                foreach ($fullPermission as $controller => $permission) {

                    if(!empty($myPermission)){

                        $permissionTmp[$controller] = 0;
                        $keys = array();

                        foreach ($myPermission as $myPer){

                            if (isset($myPer['permission'][$controller]) && !empty($permission['action'])) {

                                foreach ($permission['action'] as $action => $per) {

                                    $key = intval($per['value']);

                                    if ($myPer['permission'][$controller] & $key) {

                                        $acl->allow($role, $controller, $action);

                                        if (!in_array($key, $keys)) {
                                            array_push($keys, $key);
                                            $permissionTmp[$controller] += $key;
                                        }

                                    }
                                }
                            }

                        }

                    }
                }

                $myPermission = $permissionTmp;
            }
        }



        //check permission

        if (!in_array($controllerName, $arrController)) {
            $acl->add(new Zend_Acl_Resource($controllerName));
        }

        if (!$acl->isAllowed($role, $controllerName, $actionName)) {
            if ($role == "guest") {
                $this_url = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
               $myPermission = BASE_URL . "/login?callback=".  urlencode($this_url);
            } else {
                $_SESSION['msg_error'] = MSG_DENIED;
                $myPermission = BASE_ADMIN_URL . "/deny/";
//                $this->_redirect(BASE_ADMIN_URL . "/deny/");
            }
        }

        return $myPermission;
    }

    /**
     * get FormatDate by Language
     * return date
     */
    public static function getFormatDateByLanguage($sLang, $iDate){
    	if($sLang == 'en'){
    		return date("m-d-Y H:i ", $iDate);
    	}

    	if($sLang == 'ja'){
    		return date("Y-m-d H:i ", $iDate);
    	}

    	return date("d-m-Y H:i ", $iDate);
    }


   /**
    * insert redis
    * @param string $sKey
    * @param string $tailKey
    * @param int $value
    * return int -1 0 1
    */
    public static function setRedis($sKey, $tailKey, $value){


    	$keyRedis = Core_Global::getKeyPrefixCaching($sKey) . $tailKey;
    	return Core_Business_Nosql_Redis::getInstance()->setList($keyRedis, $value, time());
    }


    public static function setKeyRedis($sKey, $tailKey, $value){
        $keyRedis = Core_Global::getKeyPrefixCaching($sKey) . $tailKey;
        return Core_Business_Nosql_Redis::getInstance()->setKey($keyRedis, $value);
    }

    public static function isListRedis($sKey, $tailKey, $value)
    {
        $keyRedisLike = Core_Global::getKeyPrefixCaching($sKey) . $tailKey;
        return  Core_Business_Nosql_Redis::getInstance()->isList($keyRedisLike, $value) == 1 ? true : false;
    }
    /**
     * delete redis
     * @param string $sKey
     * @param string $tailKey
     * return int -1 0 1
     */
    public static function deleteRedis($sKey, $tailKey){
    	$keyRedis = Core_Global::getKeyPrefixCaching($sKey) . $tailKey;
    	$iResult = Core_Business_Nosql_Redis::getInstance()->deleteKey($keyRedis);
    }

    /**
     * delete item in list redis
     * @param string $sKey
     * @param string $tailKey
     * return int -1 0 1
     */
    public static function deleteItemInListRedis($sKey, $tailKey, $value){
    	$keyRedis = Core_Global::getKeyPrefixCaching($sKey) . $tailKey;
    	Core_Business_Nosql_Redis::getInstance()->deleteItemList($keyRedis, $value);
    }

    /**
     * count redis
     * @param string $sKey
     * @param string $tailKey
     * return int total
     */
    public static function countRedis($sKey, $tailKey)
    {
    	$keyRedis = Core_Global::getKeyPrefixCaching($sKey) . $tailKey;
    	$iResult = Core_Business_Nosql_Redis::getInstance()->getListTotal($keyRedis);

    	return $iResult;
    }

    /**
     * get list redis
     * @param int $iStart
     * @param int $iLimit
     * @param string $sKey
     * @param string $tailKey
     * @param bool $isOrderBy
     * return array key
     */
    public static function selectRedis($iStart, $iLimit, $sKey, $tailKey, $isOrderBy = FALSE){

    	$keyRedis = Core_Global::getKeyPrefixCaching($sKey) . $tailKey;
    	if($isOrderBy){
    		return Core_Business_Nosql_Redis::getInstance()->getListByScore($keyRedis, $iStart, $iLimit);
    	}

    	return Core_Business_Nosql_Redis::getInstance()->getList($keyRedis, $iStart, $iLimit);
    }


    public static function sortRedis($iStart, $iLimit, $sKey, $tailKey, $sortType = 'asc'){

        $keyRedis = Core_Global::getKeyPrefixCaching($sKey) . $tailKey;

        return Core_Business_Nosql_Redis::getInstance()->sort($keyRedis,$sortType, $iStart, $iLimit);
    }

    /**
     * insert cache
     * @param string $sKey
     * @param string $tailKey
     * @param int $value
     * return no
     */

    public static function readCache($key,$id)
    {
        $keyCaching = Core_Global::getKeyPrefixCaching($key) . $id;
        $caching = Core_Global::getCacheInstance();
        return $caching->read($keyCaching);
    }


    public static function writeCache($key, $key_expired, $id, $obj)
    {
        $keyCaching = Core_Global::getKeyPrefixCaching($key) . $id;
        $caching = Core_Global::getCacheInstance();
        $time = Core_Global::getKeyPrefixCaching($key_expired);
        $caching->write($keyCaching, $obj, $time);
    }

    public static function clearCache($key, $iID)
    {
        $keyCaching = Core_Global::getKeyPrefixCaching($key) . $iID;

        //Init caching
        $caching = Core_Global::getCacheInstance();
        $caching->delete($keyCaching);
    }

    /**
     * create folder if not exist
     */

    public static function createFolder($sPath){
    	if (!file_exists($sPath)) {
    		mkdir($sPath, 0777, true);
    	}
    }

    public static function createFolders($baseUrl,array $paths){

        $sPath = '';
        foreach($paths as $path) {
            $sPath .= "/$path";
            $fullPath = $baseUrl.$sPath;
            if (!file_exists($fullPath)) {
                mkdir($fullPath, 0777, true);
            }
        }
    }

    public static function deleteFolder($dir){
        if (file_exists($dir)) {

            $it = new RecursiveDirectoryIterator($dir);
            $it = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
            foreach($it as $file) {
                if ('.' === $file->getBasename() || '..' ===  $file->getBasename()) continue;
                if ($file->isDir()) rmdir($file->getPathname());
                else unlink($file->getPathname());
            }
            rmdir($dir);

        }
    }

    /**
     * endswith words
     * @param unknown $haystack
     * @param unknown $needle
     * @return boolean
     */
    public static function endsWith($haystack, $needle) {
    	$haystack = strtolower($haystack);
    	$needle = strtolower($needle);
    	// search forward starting from end minus needle length characters
    	return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
    }

    /**
     * convert Array string to array int
     * @param unknown $array
     * @return array int
     */
    public static function convertArrStringToInt($array){

    	$result = array();
    	foreach ($array as $value){
    		$result[] = (int)$value;
    	}
    	return $result;

	}

    public static function checkShowGroupCompany($group)
    {
        $arrLogin = Admin::getInstance()->getLogin();
        if($group['group_type'] == 1)
        {
            if(!Admin::getInstance()->isAdmin( $arrLogin['accountID']))
            {
                return false;
            }
        }
        return true;
    }

    public static function deleteFile($fileName, $folder)
    {
        $result = false;
        $path = ROOT_IMAGES_PATH.'/'.$folder;
        if(file_exists($path.'/'.$fileName))
        {
            unlink($path.'/'.$fileName);
            $result = true;
        }
        return $result;
    }

    public static function getExtensionAndFileName($fileName)
    {
        $str = explode('.',$fileName);
        if(count($str) == 1)
            return array();
        $extent = end($str);
        $fileNotExtent = str_replace('.'.$extent,'',$fileName);

        return array('extent'=>$extent,'file_not_extent'=>$fileNotExtent);


    }

    /**
     * @param $extension
     * @param string $type
     * @return bool
     */
    public static function checkExtension($extension, $type='all')
    {
        global $globalConfig;
        $allowExtensions = array();
        switch ($type) {
            case 'all': {
                $allowExtensions = $globalConfig['allow_file'];
                break;
            }
            case 'image': {
                $allowExtensions = $globalConfig['allow_image'];
                break;
            }
            case 'document': {
                $allowExtensions = $globalConfig['allow_document'];
                break;
            }
        }

        return in_array($extension, $allowExtensions) ? true : false;

    }


    public static function removeSpecialCharAction($string,$sReplace = ' ',$hyphens = true, $allSpace = true)
    {
        if(!$allSpace)
            $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^A-Za-z0-9_-]/', $sReplace, $string); // Removes special chars.
        if($hyphens)
            return  preg_replace('/-+/', $sReplace, $string); // Replaces multiple hyphens with single one.
        else
            return $string;
    }
    public static function matchLeaderAndSubLeader($position)
    {
        $result = $position;
        $position = strtolower($position);
        $position = Core_Common::removeSpecialCharAction($position,'-',true,false);
        $position = explode('-',$position);
        $patternLeader = '/^leader/';
        $patternSubLeader = '/^sub/';

        $sLeader = '';
        $sSub = '';

        foreach($position as $str )
        {
            preg_match($patternLeader, strtolower($str), $leader, PREG_OFFSET_CAPTURE);
            preg_match($patternSubLeader, strtolower($str), $subLeader, PREG_OFFSET_CAPTURE);
            if(count($leader)) {
                $sLeader = 'Leader';
            }

            if(count($subLeader)) {
                $sSub = 'Sub';
            }
        }

        if(!empty($sSub) && !empty($sLeader))
            $result = $sSub.' '.$sLeader;
        else if(!empty($sLeader))
            $result = $sLeader;

        return trim($result);
    }
    public static function getQueryString()
    {
        $url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $parts = parse_url($url);
        parse_str($parts['query'], $query);
        return $query;
    }

    public static function unique_multidim_array($array, $key) {
        $temp_array = array();
        $i = 0;
        $key_array = array();

        foreach($array as $val) {
            if (!in_array($val[$key], $key_array)) {
                $key_array[$i] = $val[$key];
                $temp_array[$i] = $val;
            }
            $i++;
        }
        return $temp_array;
    }

    public static function array_sort($array, $on, $order=SORT_ASC, $bReindex = false)
    {
        $new_array = array();
        $sortable_array = array();

        if (count($array) > 0) {
            foreach ($array as $k => $v) {

                if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $on) {

                            $sortable_array[$k] = $v2;
                        }
                    }
                } else {
                    $sortable_array[$k] = $v;
                }
            }

            switch ($order) {
                case SORT_ASC:
                    asort($sortable_array);
                    break;
                case SORT_DESC:
                    arsort($sortable_array);
                    break;
            }

            foreach ($sortable_array as $k => $v) {
                if($bReindex) {
                    $new_array  []= $array[$k];
                }else{
                    $new_array[$k] = $array[$k];
                }
            }
        }
        return $new_array;
    }

    public static function getLanguage($language)
    {
        $language = strtolower($language);
        $result = '';

        switch($language) {
            case LANGUAGE_EN:
                $result = LANGUAGE_EN;
                break;
            case LANGUAGE_JP:
                $result = LANGUAGE_JP;
                break;
            case LANGUAGE_VI:
                $result = LANGUAGE_VI;
                break;
            default:
                $result = DEFAULT_LANGUAGE;
                break;
        }
        return $result;
    }
    public static function fillterTimezone($timezone)
    {
        $timezone = strtolower($timezone);
        $result = '';

        switch($timezone) {
            case strtolower(TIMEZONE_EN):
                $result = TIMEZONE_EN;
                break;
            case strtolower(TIMEZONE_JA):
                $result = TIMEZONE_JA;
                break;
            case strtolower(TIMEZONE_VI):
                $result = TIMEZONE_VI;
                break;
            default:
                $result = DEFAULT_TIMEZONE;
                break;
        }
        return $result;
    }

    /**
     * isValidExtendsion
     *
     * @param string $fileName
     * @author Tai Le Thanh <tai.lt@vn.gnt-global.com>
     */
    public static function isValidExtendsion($fileName)
    {
        $fileInfo = pathinfo($fileName);

        $extendsion = isset($fileInfo['extension']) ? $fileInfo['extension'] : NULL;

        $strEnableExtList = ENABLE_EXT_LIST;
        $arrExtList = explode(',', $strEnableExtList);

        $pattern = array();
        foreach($arrExtList as $tempExtendsion) {
            $pattern[] = trim($tempExtendsion);
        }

        $strPattern = implode('|', $pattern);
        return preg_match('/^(' . $strPattern . ')$/i', $extendsion);
    }

    public static function getLevelUser($levelName)
    {
        switch(strtolower($levelName)){
            case 'manager': $level = GroupMember::$manager;
                break;
            case 'deputy manager': $level = GroupMember::$deputyManager;
                break;
            case 'leader': $level = GroupMember::$leader;
                break;
            case 'sub leader': $level = GroupMember::$subLeader;
                break;
            default :  $level = GroupMember::$staff;
        };
        return $level;
    }

    public static function convertNumberDayToInt($dateType)
    {
        switch($dateType){
            case 1: return 1;
                break;
            default : return 0.5;
        }
    }

    public static function differentTime($sDateFrom, $sDateTo, $flush = true)
    {
        $dateFrom = new DateTime($sDateFrom);
        $dateTo = new DateTime($sDateTo);
        $d = $dateFrom->diff($dateTo);
        return ($flush) ? $d->days +1 : $d->days;
    }

    public static function parseContractTypeToTotalAbsence($contractType, $remain = 0, $month = 0)
    {
        if($month == 0)
            $month = date('m');
        $total = 0;
        if ($contractType > 0) {
            $contract = General::getInstance()->getGeneralByID($contractType);
            if (!empty($contract)) {
                if (strtolower($contract['name']) == 'indefinite contract') {
                    if ($month == 4) //chỉ cộng 2 ngày cho hợp đồng ko thời hạn khi tháng hiện tại là 4
                        $total = ($remain > 0) ? $remain + 2 : 2;
                    else
                        $total = ($remain > 0) ? $remain + 1 : 1;
                }else if(strtolower($contract['name']) == 'one-year contract' ||  strtolower($contract['name']) == 'three-year contract')
                {
                    $total = ($remain > 0) ? $remain + 1 : 1;
                }// end  if (strtolower($contract['name']) == 'indefinite contract')
            }// end if (!empty($contract))
        }// end if ($accountInfo['contract_type'] > 0)
        return $total;
    }

    public static function checkAllowFileType($fileName, $allowFileInConfig = 'allow_image')
    {
        global $globalConfig;
        $allowTypes = $globalConfig[$allowFileInConfig];
        $processFileName = Core_Common::getExtensionAndFileName($fileName);

        $ext = $processFileName['extent'];
        if(in_array($ext,$allowTypes))
            return array('error'=>false, 'message'=> implode(',',$allowTypes));

        return array('error'=>true, 'message'=> implode(',',$allowTypes));

    }

    public static function addJob($params, $sClass = 'JobNotification', $sFunction = 'pushNotification', $sJobFunction = 'notification')
    {

        // push to job
        $jobParams = array();
        $jobParams['class'] = $sClass;
        $jobParams['function'] = $sFunction;
        $jobParams['args'] = $params;

        //Get job name
        $jobName = Core_Global::getJobFunction($sJobFunction);

        //Create job client
        $jobClient = Core_Global::getJobClient();

        //Register job
        $jobClient->doBackgroundTask($jobName, $jobParams);
    }

    public static function parseDateTimeToTimeSpan($dateTime,$time = true)
    {

        if(! $dateTime instanceof DateTime)
            return 0;

        $currentTimeZone = date_default_timezone_get();
        date_default_timezone_set('Asia/Ho_Chi_Minh');

        //parse time to time-span
        $hour = intval($dateTime->format('H'));
        $minute = intval($dateTime->format('i'));
        $second = intval($dateTime->format('s'));
        $day = intval($dateTime->format('d'));
        $month = intval($dateTime->format('m'));
        $year = intval($dateTime->format('Y'));
        if($time)
            return mktime($hour, $minute, $second, $month, $day, $year);
        else
            return mktime(0, 0, 0, $month, $day, $year);
    }

    public static function setTimeZoneDefault(DateTime $dateTime)
    {
//        $timeZone =  new DateTimeZone('Asia/Ho_Chi_Minh');
        $timeZone =  new DateTimeZone('UTC');
        return $dateTime->setTimezone($timeZone);
    }


    public static function bolderFontBackGround($bgColor = '0000ff', $fontColor = 'ffffff', $borderColor = 'c0c0c0')
    {
        $default_border = array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('rgb'=>$borderColor)
        );

        return array(
            'borders' => array(
                'bottom' => $default_border,
                'left' => $default_border,
                'top' => $default_border,
                'right' => $default_border,
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => $bgColor)
            ),
            'font'  =>  array(
                'bold' => true,
                'color' => array('rgb' => $fontColor)
            )
        );
    }

    public static function isWeekend($sDate)
    {
        $weekDay = date('w', strtotime($sDate));
        return ($weekDay == 0 || $weekDay == 6) ? true : false;
    }

    public static function getCollection($db,$collectionName){

        if(!$db->connect()){
            return null;
        }
        $configuration = Core_Global::getApplicationIni();
        $options = $configuration->system->nosql->mongo->toArray();
        return $db->$options['dbname']->$collectionName;
    }


    public static function convert_vi_to_en($str) {
        $str = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $str);
        $str = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $str);
        $str = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $str);
        $str = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $str);
        $str = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $str);
        $str = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $str);
        $str = preg_replace('/(đ)/', 'd', $str);
        $str = preg_replace('/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/', 'A', $str);
        $str = preg_replace('/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/', 'E', $str);
        $str = preg_replace('/(Ì|Í|Ị|Ỉ|Ĩ)/', 'I', $str);
        $str = preg_replace('/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/', 'O', $str);
        $str = preg_replace('/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/', 'U', $str);
        $str = preg_replace('/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/', 'Y', $str);
        $str = preg_replace('/(Đ)/', 'D', $str);
        return $str;

    }

    public static function removeElementEmptyInArray($array){
        return array_filter($array, create_function('$value', 'return $value !== "";'));
    }

    public static function diffDate($sDate,$sDateTo)
    {
        $diff = abs(strtotime($sDate) - strtotime($sDateTo));

        $years = floor($diff / (365*60*60*24));
        $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
        $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

       return array('days'=>$days, 'months'=>$months, 'years'=>$years);
    }

    public static function diffMonth($sDate,$sDateTo)
    {
        return (int)abs((strtotime($sDate) - strtotime($sDateTo))/(60*60*24*30)); //
    }

    public static function diffYear($sDate,$sDateTo)
    {
        return (int)abs((strtotime($sDate) - strtotime($sDateTo))/(365*60*60*24)); //
    }

    public static function array_diff_key_with_value($array1, $array2, $key, $valDiff) {
        $array = array_merge($array1,$array2);
        $temp_array = array();
        $i = 0;
        foreach($array as $val) {
            if ($val[$key] !=  $valDiff) {
                $temp_array[$i] = $val;
            }
            $i++;
        }
        return $temp_array;
    }

    public static function getMetaTags($str)
    {
        $pattern = '
  ~<\s*meta\s

  # using lookahead to capture type to $1
    (?=[^>]*?
    \b(?:name|property|http-equiv)\s*=\s*
    (?|"\s*([^"]*?)\s*"|\'\s*([^\']*?)\s*\'|
    ([^"\'>]*?)(?=\s*/?\s*>|\s\w+\s*=))
  )

  # capture content to $2
  [^>]*?\bcontent\s*=\s*
    (?|"\s*([^"]*?)\s*"|\'\s*([^\']*?)\s*\'|
    ([^"\'>]*?)(?=\s*/?\s*>|\s\w+\s*=))
  [^>]*>

  ~ix';

        if(preg_match_all($pattern, $str, $out))
            return array_combine($out[1], $out[2]);
        return array();
    }


    public static function parseToList($accounts, $field){
        $arr = array();
        foreach($accounts as $account){
            $arr []= $account[$field];
        }

        return $arr;
    }

    public static function removeAllVariables(){
        $vars = array_keys(get_defined_vars());
        for ($i = 0; $i < sizeOf($vars); $i++) {
            unset($vars[$i]);
        }
        unset($vars,$i);

    }

}


