<?php
/**
 * @author      :   HoaiTN
 * @name        :   NewsController
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   News Controller 
 */
class NewsController extends Core_Controller_Action
{
    
     public function init() 
     {
        parent::init();
        
        global $globalConfig;
        
        
        //define type
        $this->view->arrType = $globalConfig['news_type'];
        
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
        $iTotal =0;
        $iMore =0;
        

        $iPageSize = 21;
        
        
        $iStart = ($iPage - 1) * $iPageSize;

        //from second load limit item = 21
        $iPageSize = ($iStart > 0) ? $iPageSize : 21;
       //init instance account
        $instanceNews= News::getInstance();
        
        //get result
        $arrResult = $instanceNews->getNewsList2($iStart, $iPageSize);
        
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
            $htmlView->setScriptPath(APPLICATION_PATH . '/modules/default/views/scripts/news/');
            
         
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
    }
     
    /**
     * Default action
     */
    
    public function detailAction()
    {
        
         //get param
        $iNewsID = $this->_request->getParam('id', 0);
        
        // init 
        $arrNews = array();
        
        $arrHotNews = array();
        
        //check newsid
        if($iNewsID>0)
        {
             $instanceNews = News::getInstance();
             $arrNews = $instanceNews->getNewsByID($iNewsID);
             if(!$arrNews['active'])
                 $arrNews = array();
             //Get Tin hot 
             $arrHotNews = $instanceNews->getNewsList('',0, 1, 1, 0, 4);
             $arrHotNews = isset($arrHotNews['data'])?$arrHotNews['data']:array();
             
        }
        
        $this->view->arrNews    = $arrNews;
        $this->view->arrHotNews = $arrHotNews;
   
    }
   
    
}

