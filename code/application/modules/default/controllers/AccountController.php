<?php
/**
 * @name        :   IndexController
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   controller default 
 */


class AccountController extends Core_Controller_Action
{
     //var arr login
     private $arrLogin;
     
     public function init() {
        parent::init();
        
        //Asign login
        $this->arrLogin = $this->view->arrLogin;
        
        //Get Controller
      //  $controller = $this->_request->getParam('controller');
      //  $this->view->controller = $controller;
        
       
       
       
    }

    
    /**
     * delete member of group
     */
    public function deleteAction(){
    	
    	if($this->_request->isPost())
    	{
    		global $globalConfig;
    		 
    		$params = $this->_request->getPost();
    		$message = '';
    	
    		$output = array('error' => 0,'message' => '');
    		 
    		$iAccountId = $params['id'];
    		$iGroupId = $params['g_id'];
    		
    		if(is_numeric($iAccountId) && is_numeric($iGroupId)){
    			
    			//get group by group id
    			$arrGroup = Group::getInstance()->getGroupByID($iGroupId);
    			
    			if(!empty($arrGroup)){
    				
    				//get account admin
    				if($this->arrLogin['accountID'] == $arrGroup['admin_id']){
    					$iResult = GroupMember::getInstance()->deleteGroupMember($iAccountId, $iGroupId);
    					
    					if($iResult > 0){
    						$output = array('error' => 0, 'message' => 'Delete successed.');
    					}else{
    						$output = array('error' => 1, 'message' => 'Delete failed.');
    					}
    				}
    			}
    			
    			
    		}
    		
    		echo Zend_Json::encode($output);
    		exit();
    		
    	}
    	
 
    }
    
    public function deletelistAction(){
    
    	if($this->_request->isPost())
    	{
    		$params = $this->_request->getPost();
    
    		if(!empty($params['data'])){
    			 
    			$arrDatas = json_decode($params['data'], true);
    			 
    			$total = 0;
    			$totalDelete = 0;
    
    			if(!empty($arrDatas['account_ids'])){

    				$arrAccountIds = $arrDatas['account_ids'];
    				$iGroupId = $arrDatas['group_id'];
    				
    				foreach ($arrAccountIds as $id){
    						
    					$iResult = GroupMember::getInstance()->deleteGroupMember($id, $iGroupId);
    					$total++;
    						
    					if($iResult > 0){
    						$totalDelete++;
    					}
    				}
    			}
    			 
    			$output = array('total' => $total, 'totalDelete' => $totalDelete);
    			echo Zend_Json::encode($output);
    			exit();
    			 
    		}
    	}
    
    }
    
  
    
}
