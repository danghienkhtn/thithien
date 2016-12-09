<?php
/**
 * @name        :   IndexController
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   controller default 
 */

class IndexController extends Core_Controller_Action
{
     //var arr login
     private $arrLogin;
     
     public function init() {
        parent::init();
        
        //Asign login
        // $this->arrLogin = $this->view->arrLogin;
        
        //Get Controller
        $controller = $this->_request->getParam('controller');
        $this->view->controller = $controller;

        // $this->_helper->layout()->disableLayout();
       
    }

    public function comingSoonAction()
    {

    }
    /**
     * Default action
     */
    public function indexAction()
    {
       // $this->_redirect('/feed');
       // exit();   
    }
    

    /**
     * Calendar
     */
    public function calendarAction()
    {
         
        $this->_helper->layout()->disableLayout();
        //Disable render
        $this->_helper->viewRenderer->setNoRender();
        
        $iYear = $this->_request->getParam('year', 0);
        $iMonth = $this->_request->getParam('month', 0);
        $iDate = $this->_request->getParam('day', 0);
        
        
        
        if(!empty($iMonth) && !empty($iYear))
        {
            
            $iDay =1;
            $startDate = $iMonth.'/'.$iDay.'/'.$iYear ;
            
            $iDay =31;
            $endDate  = $iMonth .'/'.$iDay.'/'.$iYear;
            
        }
        else
        {
            $time = time();
            $iDay = 1;
            $iMonth = date('m',$time);
            $iYear = date('Y',$time);
            
            $startDate = $iMonth.'/'.$iDay.'/'.$iYear ;
            
            $iDay =31;
            $endDate  = $iMonth .'/'.$iDay.'/'.$iYear;
            
        }
       
        //Get calendar data
        $arrCalendar = Exchange::getInstance()->getCalendarList($this->arrLogin['email'], base64_decode($this->arrLogin['ps']), $startDate, $endDate);
        
        
        echo Zend_Json::encode(array('error' => 0, 'data' => $arrCalendar));
        exit();
    }
    
    
        /**
     * Calendar
     */
    public function calendardetailAction()
    {
          
        global $globalConfig;
        
        $this->_helper->layout()->disableLayout();
        //Disable render
        $this->_helper->viewRenderer->setNoRender();
        
        $iYear = $this->_request->getParam('year', 0);
        $iMonth = $this->_request->getParam('month', 0);
        $iDate = $this->_request->getParam('day', 0);
        $iFrom = $this->_request->getParam('from', 0);
        
        $arrCalendar = array();
        $sTextDay ='';
        
        if(!empty($iMonth) && !empty($iYear) && !empty($iDate))
        {

            $startDate = $iMonth.'/'.$iDate.'/'.$iYear;
            $today = date('m/d/Y', time());
            
            if($startDate== $today){
                 $sTextDay = 'Today '.date('d/m',time());
            }else{
                $time = strtotime($startDate);
                $sTextDay = date('l', $time).' '.date('d/m',$time); 
            }
                       
         
            $iDate2 = $iDate + 1;
            $iMonth2 = $iMonth;
            
            $endDate  = $iMonth .'/'.$iDate2.'/'.$iYear;
            
            if(strlen($iMonth) ==1)
            {
                $iMonth = '0'.$iMonth;
            }
            
            if(strlen($iDate) ==1)
            {
                $iDate = '0'.$iDate;
            }
            
            $date = $iYear.'-'.$iMonth.'-'.$iDate;
            
             //Get calendar data
            $arrCalendar = Exchange::getInstance()->getCalendarDetail($this->arrLogin['email'], base64_decode($this->arrLogin['ps']), $startDate, $endDate,$date);
        
        }
        
        
         //render
        $this->view->arrCalendar = $arrCalendar;
        $this->view->sTextDay = $sTextDay;
       
             
        echo $this->view->render('index/calendardetail.phtml');
       
    }
    
    /**
     * Get Total mail unread
     */
    public function totalunreadAction()
    {
          
        $this->_helper->layout()->disableLayout();
        //Disable render
        $this->_helper->viewRenderer->setNoRender();
        
        
       $iTotal=  Exchange::getInstance()->getTotalMailUnread($this->arrLogin['email'], base64_decode($this->arrLogin['ps']));
       
      //Respond
        $arrRespone = array(
                'total' => $iTotal,
                'error' => 0
        );

        //Send data
        echo Zend_Json::encode($arrRespone);
       
        exit();
       
    }
    
    /*
     * Get Calendar list
     */
    public function calendarlistAction()
    {  
        $this->_helper->layout()->disableLayout();
        //Disable render
        $this->_helper->viewRenderer->setNoRender();
        

        $time = time();
        $time = mktime(0,0,1, date('m', $time),date('d', $time), date('Y', $time));

        $startDate =date('m/d/Y',$time);
        $endDate =date('m/d/Y',$time + 7*86400);
                   
        //Get calendar data
        $arrCalendar = Exchange::getInstance()->getCalendarList2($this->arrLogin['email'], base64_decode($this->arrLogin['ps']), $startDate, $endDate);
       // var_dump($arrCalendar); exit;
        
        $this->view->arrCalendar = $arrCalendar;
          //Render to  View
        echo $this->view->render('index/calendarlist.phtml');  
    }
    
    
    public function autocompleteAction()
    {
        
        $arrResult = array();
        
        $this->_helper->layout()->disableLayout();
        //Disable render
        $this->_helper->viewRenderer->setNoRender();
        
        $sName= $this->_request->getParam('tag', '');
        
        if(!empty($sName))
        {
            $sName = urldecode(trim($sName));

             $iStart =0;
             $iPageSize = 50;
             
             $sSort ='';
             
             //Search Init
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
           $arrTmp = Search::getInstance()->getProfileSearch($arrSearch, $iStart, $iPageSize, $sSort);

            if(!empty($arrTmp['data']))
            {
                  //Asign data
                  $arrTmp = $arrTmp['data'];
                  
                  foreach($arrTmp as $value)
                  {

                      $arrResult[] = array('value' => $value['account_id'], 'caption' => $value['name']);
                  }
            }
            
        }
        
        echo json_encode($arrResult);
        
        exit();
    }
     
}

