<?php
/**
 * @author      :   Linuxpham
 * @name        :   IndexController
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   controller default 
 */
class Backend_FixController extends Core_Controller_Action
{

	private $arrLogin;
	 
	public function init() {
	
		parent::init();
	
		//Asign login
		$this->arrLogin = $this->view->arrLogin;

	}
	

    /**
     * fix data group member for redis
     */
    public function indexAction()
    {

    	echo "index all group id into redis\n";
    	
    		$iGroupID = isset($params['group_id']) ? $params['group_id'] : 0;
    		$iOffset = 0;
    		$iLimit = MAX_QUERY_LIMIT;
    	
    		//index all group id into redis
    		if($iGroupID == 0){
    			//get all group
    			$groupAlls = Core_Business_Api_Group::getInstance()->getGroupAll($iOffset, $iLimit);
    			 
    			if(!empty($groupAlls) && $groupAlls['total'] > 0){
    				 
    				//remove all group in redis
    				Core_Common::deleteRedis(REDIS_GROUP_ALL_LIST, '');
    				 
    				//index all group
    				foreach ($groupAlls['data'] as $group){
    					//add redis group all[group ids]
    					Core_Common::setRedis(REDIS_GROUP_ALL_LIST, '', $group['group_id']);
    	
    					//clear group [member ids]
    					Core_Common::deleteRedis(REDIS_GROUPMEMBER_GROUP_ID, $group['group_id']);
    				}
    			}
    	
    			$groupAlls = array();
    	
    		}
    		echo "end >index all group id into redis\n";
    		//update total member of group
    		Group::getInstance()->updateGroupAll($iGroupID);
    	
    	
    		 
    	
    		$total = 0;
    		$arrAccountIds = array();
    		//get account
    		$accountsInfo = AccountInfo::getInstance()->getAccountInfoList('', '', 0, 0, '', 0, '', 0, 0, $iOffset, $iLimit);
    	
    	
    		if(!empty($accountsInfo) && $accountsInfo['total'] > 0){
    	
    			$total = $accountsInfo['total'];
    	
    			foreach ($accountsInfo['data'] as $account){
    				$arrAccountIds[] = $account['account_id'];
    			}
    	
    			//clear
    			$accountsInfo = array();
    	
    	
    			//index my group for user
    			//group[member ids]
    			//member[invite group ids]
    			//member[requested group ids]
    			//member[suggestion group ids]
    	
    			//clear redis member group
    			foreach ($arrAccountIds as $uID){
    				Core_Common::deleteRedis(REDIS_GROUPMEMBER_MEMBER_ID, $uID);
    				//Core_Common::deleteRedis(REDIS_GROUP_INVITE_MEMBER_LIST, $uID);
    				//Core_Common::deleteRedis(REDIS_GROUP_REQUEST_MEMBER_LIST, $uID);
    				Core_Common::deleteRedis(REDIS_GROUP_SUGGESTION_MEMBER_LIST, $uID);
    			}
    	
    			$groupMembers = GroupMember::getInstance()->getGroupMembeAll(0, MAX_QUERY_LIMIT);
    			 
    			
    			if(!empty($groupMembers)){
    				foreach ($groupMembers['data'] as $arrData){
    						
    					//insert member[group ids]
    					$iResult = Core_Common::setRedis(REDIS_GROUPMEMBER_MEMBER_ID, $arrData['account_id'], $arrData['group_id']);
    						
    					//insert group [member ids]
    					Core_Common::setRedis(REDIS_GROUPMEMBER_GROUP_ID, $arrData['group_id'], $arrData['account_id']);
    						
    					//delete item invite group member id [key invite group ids ]
    					Core_Common::deleteItemInListRedis(REDIS_GROUP_INVITE_MEMBER_LIST, $arrData['account_id'], $arrData['group_id']);
    						
    					//delete item request group
    					Core_Common::deleteItemInListRedis(REDIS_GROUP_REQUEST_MEMBER_LIST, $arrData['account_id'], $arrData['group_id']);
    						
    					//delete suggestion group member id[key suggestion group ids]
    					Core_Common::deleteItemInListRedis(REDIS_GROUP_SUGGESTION_MEMBER_LIST, $arrData['account_id'], $arrData['group_id']);
    				}
    			}
    	
    			//get all group
    			$arrGroupAllIds = Core_Common::selectRedis($iOffset, $iLimit, REDIS_GROUP_ALL_LIST, '');

    			echo "begin >index all Suggestion group\n";
    	
    			foreach ($arrAccountIds as $accountId){
    				$arrGroupIds = GroupMember::getInstance()->getGroupMemberByMemberIdRedis($iOffset, $iLimit, $accountId);

    				foreach ($arrGroupAllIds as $id){
    					if(!empty($arrGroupIds)){
    	
    						if(!in_array($id, $arrGroupIds)){
    							//index all Suggestion group
    							Core_Common::setRedis(REDIS_GROUP_SUGGESTION_MEMBER_LIST, $accountId, $id);
    						}
    					}else{
    	
    						//index all Suggestion group
    						Core_Common::setRedis(REDIS_GROUP_SUGGESTION_MEMBER_LIST, $accountId, $id);
    					}
    				}
    			}
    			
    			echo "end >index all Suggestion group\n";
    		}
    		//end index Suggestion group
    	
    		echo "end";
		exit();
      
    
    }

    public function testAction(){
    	$arrGroupIds = GroupMember::getInstance()->getGroupMemberByMemberIdRedis(0, 1000, 3238);
    	print_r($arrGroupIds);
    	echo "<br/>";
    	$arrGroupIds = Core_Common::selectRedis(0, 100, REDIS_GROUP_SUGGESTION_MEMBER_LIST, 3238);
    	print_r($arrGroupIds);
    	echo "<br/>";
    	$arrGroupIds = Core_Common::selectRedis(0, 100, REDIS_GROUP_ALL_LIST, '');
    	print_r($arrGroupIds);
    	exit();
    	
    }
 
}

