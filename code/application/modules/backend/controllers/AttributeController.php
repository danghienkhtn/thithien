<?php
/**
 * @author      :   HoaiTN
 * @name        :   IndexController
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   controller default 
 */
class Backend_AttributeController extends Core_Controller_ActionBackend
{
    
     public function init() 
     {
        parent::init();
        
        global $globalConfig;
        
        //define type
        $this->view->arrAttributeType = $globalConfig['attribute_type'];
    }
    
    /**
     * Default action
     */
    public function indexAction()
    {
        //int param
        $iPage = $this->_request->getParam('page', 1);
        $iPageSize = $this->_request->getParam('pagesize',0);
        $sKeyword = $this->_request->getParam('keyword','');
        
        //
        if(!empty($sKeyword))
        {
            $sKeyword = urldecode($sKeyword);
        }
        
        //result
        $arrResult = array();
        
        //check 
        $isCheck =0;
        $iActive =0;
        $iTotal =0;
        
        //check pagesize
        if(intval($iPageSize) ==0)
        {
              $iPageSize = ADMIN_PAGE_SIZE;
        }
        
        $iStart = ($iPage - 1) * $iPageSize;
        
        //Init data search
         $arrSearch = array(
            'keyword'   => $sKeyword
        );
         
    
       //init instance account
        $instanceAttribute= Attribute::getInstance();
        
        //get result
        $arrResult = $instanceAttribute->getAttributeList($sKeyword,$iActive, $iStart, $iPageSize);

       
        //check empty
        if(!empty($arrResult))
        {
            $isCheck =1; 
            
            //asign data
            $iTotal = $arrResult['total'];
            $arrResult = $arrResult['data'];
        }
        
        
        // paging
        $paginator = new Zend_Paginator(new Page($arrResult, $iTotal));
        $paginator->setCurrentPageNumber($iPage);
        $paginator->setItemCountPerPage($iPageSize);
        Zend_Paginator :: setDefaultScrollingStyle('Sliding');
        Zend_View_Helper_PaginationControl :: setDefaultViewPartial('/partials/my_pagination_control.phtml');
        
        //Assign view
        $this->view->paginator  = $paginator;
        $this->view->iPage      = $iPage;
        $this->view->iTotal     = $iTotal;
        $this->view->pagesize   = $iPageSize;
        $this->view->arrSearch  = $arrSearch;
        $this->view->isCheck    = $isCheck;
         
    }
    
    /**
     * Default action
     */
    
    public function newAction()
    {
        //get Type
        $this->view->iPage = 1;
    }
    
    /**
     * Default action
     */
    
    public function updAction()
    {
      
         $iPage = $this->_request->getParam('page', 1);
         $iID = $this->_request->getParam('id', 0);
         
         //init data
         $arrAttribute = array();

         
         //check params
         if($iID>0)
         {
             $arrAttribute = Attribute::getInstance()->getAttributeByID($iID);
           
         }
         
         //set to view
         $this->view->arrAttribute = $arrAttribute;
         $this->view->iPage = $iPage;
        
    }
    
   /**
     * Default action
     */
    public function addAction()
    {
        
        $this->_helper->layout()->disableLayout();
        //Disable render
        $this->_helper->viewRenderer->setNoRender();
        
        $error = -1;
        $message ='Please check your information';
        if($this->getRequest()->isPost())
	{
            //get params
            $arrParam  = $this->_request->getParams();
             
     
            //get params
            $iActive = intval($arrParam['active']);
            $iType  = $arrParam['type'];
            $sName  = $arrParam['name'];
            $iID  = $arrParam['id'];
            $iSort   = intval($arrParam['sort']);
            
            //check params
            if(empty($sName) || $iType ==0)
            {
                 echo Zend_Json::encode(array('error' => $error, 'message' => $message));
                 exit();
            }
            
            
            //get Instance
            $attributeInstance = Attribute::getInstance();
            
            //Update data
            if($iID>0)
            {
                
                 //Update
                $attributeInstance->updateAttribute($iID, $sName, $iType, $iSort, $iActive);
                $flag = true;
                
            }
            else
            {
                     //Add Data
                  $flag= $attributeInstance->insertAttribute($sName, $iType, $iSort, $iActive);
            }
            
             //check result
            if($flag == false)
            {
                $message = 'The System can not add attribute. Pls try again';
                echo Zend_Json::encode(array('error' => $error, 'message' => $message));
                exit();
            }

            //error
            $error =0;
              
        }
        
        echo Zend_Json::encode(array('error' => $error, 'message' => $message));
        exit();
    }
    
    /**
     * Default action
     */
    public function deleteAction()
    {
        
        $this->_helper->layout()->disableLayout();
        //Disable render
        $this->_helper->viewRenderer->setNoRender();
        
        $error = -1;
        $message ='Please check your information';
        if($this->getRequest()->isPost())
	{
            //get params
            $arrParam  = $this->_request->getParams();
             
            //get params
            $iID  = $arrParam['id'];
            if($iID>0)
            {
                 Attribute::getInstance()->removeAttribute($iID);
            }

            $error =0; 
        }
        
        echo Zend_Json::encode(array('error' => $error, 'message' => $message));
        exit();
    }
    
    
}

