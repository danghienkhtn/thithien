<?php
/**
 * Created by PhpStorm.
 * User: thanh.lh
 * Date: 8/20/2015
 * Time: 10:13 AM
 */

class ApiUser {

    public function login($name,$password){

        if ($_SERVER['REQUEST_METHOD'] != "POST") {
            return Core_Server::setOutputData(true, "post support only", array());
        }

        $name = urldecode($name);
        $name = trim($name);
        $password = urldecode($password);
        $ps = base64_encode($password);
        $isLogin = Ldap::getInstance()->loginWithUserNameOrEmail($name, $password);
        if($isLogin){
            $fields = array('account_id', 'username', 'name', 'email', 'avatar');
            $accountInfo = AccountInfoMapper::getInstance()->selectOneWithField($fields,"username = '$name' OR email = '$name'");

            // 86400 is 1 day
            $sToken = Token::getInstance()->generateToken('chat',$accountInfo['account_id'],$accountInfo['username'],$accountInfo['avatar'],$ps,Core_Utility::getAltIp(), CHAT_SERVER_IP, 86400);
            $accountInfo['token'] = $sToken;
            return Core_Server::setOutputData(false, "Login Success", $accountInfo);

        }
        return Core_Server::setOutputData(true, "Login fail",array());
    }

    public function searchUserTagInput($key, $offset = 0, $limit = ADMIN_PAGE_SIZE, $sortField = 'name', $sortType = 'ASC',$token='')
    {

        $token = trim($token);
        $result = ApiToken::checkToken('chat',$token);
        if($result['error']){
            return Core_Server::setOutputData(true, $result['message'], array());
        }
        $Accounts = array('data'=>array());
        $key = urldecode($key);

        if(trim($key) != '') {
            $fields = array('account_id','username','name','email','avatar');
            $sWhere = "name LIKE'%$key%' OR username LIKE'%$key%' OR email LIKE'%$key%'";
            $Accounts = AccountInfoMapper::getInstance()->selectWithField($fields, $sWhere, $offset, $limit, $sortField, $sortType);
            foreach ($Accounts['data'] as $key=>$accountInfo) {
                $Accounts['data'][$key]['image_tag'] = Core_Common::avatarProcess($accountInfo['avatar']);
            }
        }

        return Core_Server::setOutputData(false, "Success", $Accounts);

    }



    public function getAllUserBasic($offset, $limit, $token = ''){

        $token = trim($token);
        $result = ApiToken::checkToken('chat',$token);
        if($result['error']){
            return Core_Server::setOutputData(true, $result['message'], array());
        }

        $offset = intval($offset);

        $limit = intval($limit);
//        $iOffset = $offset*$limit ;

        $accountInfo = AccountInfoMapper::getInstance()->selectWithField(array('account_id','name','avatar','username','email'),'',$offset,$limit);
        $index = $offset + 1;

        $showMore = ($index + $limit) >= $accountInfo['total'] ? false : true;

        $arrLogin = Admin::getInstance()->getLogin();
//        if($arrLogin['username'] == 'thanh.lh' ){
//            echo ($index + $limit) .' >= '.$accountInfo['total'];
//        }
        return Core_Server::setOutputData(false, "Success", array('show_more'=>$showMore,'pathAvatar'=>PATH_AVATAR_URL,'accounts'=>$accountInfo['data'],'total'=>count($accountInfo['data'])));

    }

    public function currentUser(){
        $arrLogin = Admin::getInstance()->getLogin();
//        echo Zend_Json::encode($_COOKIE['DEV_PREF_PORTAL_MAIN']);
        echo Zend_Json::encode($arrLogin);
        exit();
    }
}