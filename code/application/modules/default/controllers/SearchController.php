<?php
/**
 * @name        :   IndexController
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   controller default 
 */


class SearchController extends Core_Controller_Action
{
     //var arr login
     private $arrLogin;
     
     public function init() {
        parent::init();
        
        //Asign login
        $this->arrLogin = $this->view->arrLogin;
       
    }

   
    public function indexAction()
    {

    	$this->_helper->layout()->disableLayout();

    	if($this->_request->isPost())
    	{
    		
    		$iStart = 0;
    		$iPageSize = 10;
    		
    		$params = $this->_request->getPost();
    		$sName = empty($params['name']) ? '' : $params['name'];
            $sName = str_replace('@','',$sName);
//    		echo ' 1 '.$sName.' <br/>';
//    		if(!empty($sName)){
//                $text = str_replace("@","",$params['name']);
//    			if(isset($text)){
//    				$sName = $text[0];//get user name
//    			}
//    		}
//
//            echo ' 2 '.$sName.' <br/>';
    		$arrSearch= array(
    				'name'      => $sName,
    				'email'     => '',
    				'id'        => 0,
    				'identity'  => 0,
    				'taxcode'   => '',
    				'position'  => 0,
    				'departmentid'  => 0,
    				'teamid'        => 0
    		);


    		//get data search
//    		$arrTmp = Search::getInstance()->getMemberSearch($arrSearch, $iStart, $iPageSize, '');
//    		$arrTmp = Search::getInstance()->getMemberSearch($iStart, $iPageSize, $sName);
//    		$arrProfile = array();
//
//    		if(isset($arrTmp) && $arrTmp['total'] > 0){
//	    		foreach ($arrTmp['data'] as $value){
//
//	    			if(empty($value['picture'])){
//	    				$value['picture'] = 'avatar_default.jpg';
//	    			}
//
//	    			$picture = PATH_AVATAR_URL . '/' . $value['picture'];
//	    			$arrProfile[] = array('name' => $value['name']
//	    					, 'picture' => $picture
//	    					, 'team_id' => $value['team_id']
//	    					, 'account_id' => $value['account_id']);
//	    		}
//    		}
//
            $offset = 0;
            $limit = 15;

            $accountsInfo = AccountInfo::getInstance()->getAccountInfoList($sName,'', 0,0, '', 0,0,0, 0,0,'','', $offset, $limit);
            foreach($accountsInfo['data'] as $key=>$accountInfo)
            {
                $accountInfo = Core_Common::accountProcess($accountInfo);
                $accountsInfo['data'][$key] = $accountInfo;
                $accountsInfo['data'][$key]['picture'] = Core_Common::avatarProcess($accountInfo['picture']);
            }

    		$output = array('users' => $accountsInfo['data']);
    		echo Zend_Json::encode($output);
    		exit();
    	}    	
    	   
    }
    
    public function index2Action()
    {
    	 
    	$this->_helper->layout()->disableLayout();
    	 
    	if($this->_request->isPost())
    	{
    
//    		$iStart = 0;
//    		$iPageSize = 10;
//
    		$params = $this->_request->getPost();
//    		$sName = empty($params['name']) ? '' : $params['name'];
//
//
//    		$arrSearch= array(
//    				'name'      => $sName,
//    				'email'     => '',
//    				'id'        => 0,
//    				'identity'  => 0,
//    				'taxcode'   => '',
//    				'position'  => 0,
//    				'departmentid'  => 0,
//    				'teamid'        => 0
//    		);
//
//
//    		//get data search
//    		$arrTmp = Search::getInstance()->getProfileSearch($arrSearch, $iStart, $iPageSize, '');
//
//    		$arrProfile = array();
//
//    		if(isset($arrTmp) && $arrTmp['total'] > 0){
//    			foreach ($arrTmp['data'] as $value){
//
//    				if(empty($value['picture'])){
//    					$value['picture'] = 'avatar_default.jpg';
//    				}
//
//    				$picture = PATH_AVATAR_URL . '/' . $value['picture'];
//    				$arrProfile[] = array('name' => $value['name']
//    						, 'picture' => $picture
//    						, 'team_id' => $value['team_id']
//    						, 'account_id' => $value['account_id']);
//    			}
//    		}
//
//    		$output = array('users' => $arrProfile);
//    		echo Zend_Json::encode($output);
//    		exit();
            $sName = empty($params['name']) ? '' : $params['name'];
            $sName = str_replace('@','',$sName);

            $offset = 0;
            $limit = 15;
            $accountsInfo = AccountInfo::getInstance()->getAccountInfoList($sName,'', 0,0, '', 0,0,0, 0,0,'','', $offset, $limit);
            foreach($accountsInfo['data'] as $key=>$accountInfo)
            {
                $accountsInfo['data'][$key]['picture'] = Core_Common::avatarProcess($accountInfo['picture']);
            }

            $output = array('users' => $accountsInfo['data']);
            echo Zend_Json::encode($output);
            exit();
    	}
    
    }
    
   
  
    
     
}

