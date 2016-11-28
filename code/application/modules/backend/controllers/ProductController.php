<?php
/**
 * @author      :   HoaiTN
 * @name        :   ProductController
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Product Controller 
 */
class Backend_ProductController extends Core_Controller_Action
{
     private $globalConfig;
     
     public function init() 
     {
        parent::init();
        
        global $globalConfig;
        
        
        //get Type
        $arrProductType= General::getInstance()->getGeneralAttHash(6);
        $this->view->arrProductType = $arrProductType;
        $this->globalConfig = $globalConfig;
        
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
        $iType = $this->_request->getParam('type',0);
        
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
        $isHot =0;
        
        //check pagesize
        if(intval($iPageSize) ==0)
        {
              $iPageSize = ADMIN_PAGE_SIZE;
        }
        
        $iStart = ($iPage - 1) * $iPageSize;
        
        //Init data search
         $arrSearch = array(
            'keyword'   => $sKeyword,
            'type'      => $iType
        );
         
    
       //init instance account
        $instanceProduct= Product::getInstance();
        
        
        //get result
        $arrResult = $instanceProduct->getProductList($sKeyword,$iType, $iActive, $iStart, $iPageSize);

       
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
        
        $iType = $this->_request->getParam('type', 0);
        //get Type
        $this->view->iPage = 1;
        $this->view->iType = $iType;
        $this->view->arrPlatForm = $this->globalConfig['platform_type'];
        $this->view->arrDeviceType = $this->globalConfig['device_type'];
    }
    
    
    public function screenshotsAction()
    {
        
        $iProductID = $this->_request->getParam('id', 0);
        $iPage = $this->_request->getParam('page', 1);
        
        $arrProduct =  Product::getInstance()->getProductByID($iProductID);
        
        
        $this->view->iProductID = $iProductID;
        $this->view->iPage      = $iPage;
        $this->view->arrProduct = $arrProduct;
    }
    
    
    public function updscreenshotsAction()
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
           
            $iProductID  = $arrParam['id'];
            
            $screenshots1  = $arrParam['image1'];
            $screenshots2  = $arrParam['image2'];
            $screenshots3  = $arrParam['image3'];
            $screenshots4  = $arrParam['image4'];
            $screenshots5  = $arrParam['image5'];
            
 
           
            //get Instance
            $productInstance = Product::getInstance();
            
            //Update data
            if($iProductID>0)
            {
                 //Update
                $productInstance->updateScreenshotsProduct($iProductID, $screenshots1, $screenshots2, $screenshots3, $screenshots4,$screenshots5);
                $flag = true;
                
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
    
    public function updAction()
    {
      
         $iPage = $this->_request->getParam('page', 1);
         $iID = $this->_request->getParam('id', 0);
         
         //init data
         $arrProduct = array();

         
         //check params
         if($iID>0)
         {
             $arrProduct = Product::getInstance()->getProductByID($iID);
           
         }
         
         //set to view
         $this->view->arrProduct = $arrProduct;
         $this->view->iPage = $iPage;
         $this->view->arrPlatForm = $this->globalConfig['platform_type'];
         $this->view->arrDeviceType = $this->globalConfig['device_type'];
        
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
            $sTitle  = $arrParam['title'];
            $sContent  = $arrParam['content'];
            $sImage  = $arrParam['image'];
            
            
            $iProductID  = $arrParam['id'];
            $iSortOrder   = intval($arrParam['sort']);
            
            $iPlatformType = intval($arrParam['platform']);
            $iDevice = intval($arrParam['device']);
            
            $sIos ='';
            if(($iDevice&1)>0)
            {
                $sIos  = $arrParam['ioslink'];
            }
            
            $sAndroid ='';
            if(($iDevice&2)>0)
            {
                $sAndroid  = $arrParam['androidlink'];
            }
            
            $sWindow ='';
            if(($iDevice&4)>0)
            {
                $sWindow  = $arrParam['windowlink'];
            }
            
            $sLink ='';
            if(($iDevice&8)>0)
            {
                 $sLink  = $arrParam['link'];
            }
            
            
            $screenshots1  = '';//$arrParam['screenshots1'];
            $screenshots2  = '';//$arrParam['screenshots2'];
            $screenshots3  = '';//$arrParam['screenshots3'];
            $screenshots4  = '';//$arrParam['screenshots4'];
            $screenshots5 = '';//$arrParam['screenshots5'];
            
 
            
            //check params
            if(empty($sTitle) || empty($sContent))
            {
                 echo Zend_Json::encode(array('error' => $error, 'message' => $message));
                 exit();
            }
            
           $sContent = str_replace('width="1"',' ', $sContent);
            
            
            //get Instance
            $productInstance = Product::getInstance();
            
            //Update data
            if($iProductID>0)
            {
                
                 //Update
                $productInstance->updateProduct($iProductID,$sTitle,$sImage,$sContent, $sLink,$iType, $iSortOrder, $iActive,$iPlatformType, $iDevice,
            $sWindow, $sIos, $sAndroid);
                $flag = true;
                
            }
            else
            {
                     //Add Data
                  $flag= $productInstance->insertProduct($sTitle,$sImage,$sContent,$sLink, $iType, $iSortOrder, $iActive,$iPlatformType, $iDevice,
            $sWindow, $sIos, $sAndroid, $screenshots1, $screenshots2, $screenshots3, $screenshots4,$screenshots5);
            }
            
             //check result
            if($flag == false)
            {
                $message = 'The System can not add product. Pls try again';
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
            $iProductID  = $arrParam['id'];
            if($iProductID>0)
            {
                 $productInstance = Product::getInstance();
                
                 $arrProduct = $productInstance->getProductByID($iProductID);
                 
                 //Check News
                 if(!empty($arrProduct))
                 {
                      $flag = $productInstance->removeProduct($iProductID);
                      if($flag ==true)
                      {
                          if(file_exists(PATH_NEWS_UPLOAD_DIR.'/'.$arrProduct['image_url']))
                          {
                              @unlink(PATH_NEWS_UPLOAD_DIR.'/'.$arrProduct['image_url']);
                          }
                          
                          if(file_exists(PATH_NEWS_UPLOAD_DIR.'/original/'.$arrProduct['image_url']))
                          {
                              @unlink(PATH_NEWS_UPLOAD_DIR.'/original/'.$arrProduct['image_url']);
                          }
                      }
                 }
                 
                
            }

            $error =0; 
        }
        
        echo Zend_Json::encode(array('error' => $error, 'message' => $message));
        exit();
    }
    
    
}

