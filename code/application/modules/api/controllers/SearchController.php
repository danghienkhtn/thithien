<?php
/**
 * Created by PhpStorm.
 * User: hien.nd
 * Date: 077/07/2016
 * Time: 14:33 AM
 */

class Api_SearchController extends Zend_Controller_Action{

    /**
     * init of controller
     */
    public function init()
    {
        //Disale layout
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function indexAction()
    {

        /*//Set option for server
        $options = array(
            'adapter' => 'rest'
        );

        //Get server instance
        $serverInstance = Core_Server::getInstance($options);

        //Register class call
        $serverInstance->setClass('ApiDocs');

        //Hanlde instance
        $serverInstance->handle($this->_request);*/
    }    
    

    public function searchUserAndGroupAction()
    {
        $arrLogin = Admin::getInstance()->getLogin();
        if(!isset($arrLogin['accountID']) || empty($arrLogin['accountID'])){
            echo Zend_Json::encode(Core_Server::setOutputData(true, 'You must login!', array()));
            exit;
        }
        $key = $this->_getParam('key', '');        
        $key = urldecode($key);
        $arrResult = array();
        if(trim($key) != ''){
            $ApiSearch = new ApiSearch();
            $accountsInfo = $ApiSearch->searchAccountInfoListByKey($key, 0, ADMIN_PAGE_SIZE);
            $GroupMemberInfo = $ApiSearch->searchGroupMemberByMemberKey($arrLogin['accountID'], $key, 0, ADMIN_PAGE_SIZE);

            if(isset($accountsInfo['total']) && $accountsInfo['total'] > 0){
                foreach ($accountsInfo['data'] as $accInfo) {
                    $arrResultTmp["searchId"] = $accInfo['account_id'];
                    $arrResultTmp["searchName"] = $accInfo['name'];
                    $arrResultTmp["searchAvatar"] = PATH_AVATAR_URL . DIRECTORY_SEPARATOR . $accInfo['avatar'];
                    $arrResultTmp["searchType"] = 'account';
                    $arrResult[] = $arrResultTmp;
                }
            }
            if(isset($GroupMemberInfo['total']) && $GroupMemberInfo['total'] > 0){
                foreach ($GroupMemberInfo['data'] as $groupInfo) {
                    $arrResultTmp["searchId"] = $groupInfo['group_id'];
                    $arrResultTmp["searchName"] = $groupInfo['group_name'];
                    $arrResultTmp["searchAvatar"] = PATH_GROUPS_URL . DIRECTORY_SEPARATOR . "200x200" . DIRECTORY_SEPARATOR . $groupInfo['image_url'];
                    $arrResultTmp["searchType"] = 'group';
                    $arrResult[] = $arrResultTmp;
                }
            }
        }
        echo Zend_Json::encode(Core_Server::setOutputData(false, 'OK', array("data"=>$arrResult)));      
        exit;
    }

    

}

