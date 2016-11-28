<?php
/**
 * @author      :   HoaiTN
 * @name        :   ProductController
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Product Controller 
 */
class ProductController extends Core_Controller_Action
{
    
     public function init() 
     {
        parent::init();
        
        global $globalConfig;
        
        
        //get Type
        $arrProductType= General::getInstance()->getGeneralAttHash(6);
        $this->view->arrProductType = $arrProductType;
        
        //Get Controller
        $controller = $this->_request->getParam('controller');
        $this->view->controller = $controller;
        
    }
    
    /**
     * Default action
     */
    public function indexAction()
    {
        //int param
        $iPage = $this->_request->getParam('page', 1);
        $sKeyword = '';
        $iType = $this->_request->getParam('type',0);
        
         //Check ajax request
        $isAjaxRequest = $this->_request->isXmlHttpRequest();
       
        
        //check 
        $isCheck =0;
        $iActive =1;
        $isHot =0;
        $iMore =0;
        
        //check pagesize
        $iPageSize = ADMIN_PAGE_SIZE;
     
        $iStart = ($iPage - 1) * $iPageSize;
       
    
       //init instance account
        $instanceProduct= Product::getInstance();
        
        //get result
        $arrResult = $instanceProduct->getProductList($sKeyword,$iType, $iActive, $iStart, $iPageSize);
        
        $iTotal = isset($arrResult['total'])?$arrResult['total']:0;
         
        $arrResult = isset($arrResult['data'])?$arrResult['data']:array();
        
        //Count result
        $iCount = count($arrResult);

        //Check more
        if($iStart + $iCount< $iTotal)
        {
            $iMore =1;
        }
        
        
         //Check ajax request
        if ($isAjaxRequest)
        {
            
            //Disable and render
            $this->_helper->layout()->disableLayout();
            
            //Get html view
            $htmlView = new Zend_View();

            //Set script path
            $htmlView->setScriptPath(APPLICATION_PATH . '/modules/default/views/scripts/product/');
            
         
            //Assign view
            $htmlView->paginator  = $arrResult;
            $htmlView->iPage      = $iPage;
            

            //Load default configuration for this view
            Core_Global::addToDefaultView($htmlView, "default");

            $sContent = $htmlView->render('more.phtml');

            //Set response html
            $arrRespone = array(
                'data'       => $sContent,
                'page'       => $iPage + 1,
                'more'       => $iMore
            );

            //Send data
            echo Zend_Json::encode($arrRespone);

            //Exit render
            exit();
        }
        
     
        
        //Assign view
        $this->view->paginator  = $arrResult;
        $this->view->iPage      = $iPage;
        $this->view->iMore      = $iMore;
        $this->view->iTotal    = $iTotal;
    }
    
    
     /**
     * Default action
     */
    
    public function detailAction()
    {
        
         //get param
        $iProductID = $this->_request->getParam('id', 0);
        
        // init 
        $arrProduct = array();
        $arrResult = array();
        
        //check newsid
        if($iProductID>0)
        {
             //new Instance
             $instanceProduct = Product::getInstance();
               
             //Get product detail
             $arrProduct = $instanceProduct->getProductByID($iProductID);
             
             $sKeyword ='';
             $iType =0;
             $iActive =1;
             $iStart =0;
             $iPageSize =7;
             
             //Get other product
             $arrResult = $instanceProduct->getProductList($sKeyword,$iType, $iActive, $iStart, $iPageSize);
             $arrResult = isset($arrResult['data'])?$arrResult['data']:array();
        }
        
        $this->view->arrProduct = $arrProduct;
        $this->view->arrResult  = $arrResult;
   
    }
    
}

