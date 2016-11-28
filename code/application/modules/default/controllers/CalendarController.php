<?php

/**
 * Listing jobs to do in day, week, month
 *
 * @category  Controller
 * @package   CalendarController
 * @author    Tai Le Thanh <tai.lt@vn.gnt-global.com>
 * @copyright 2016 Gianty
 * @license   http://www.gianty.com.vn CIO Team
 * @link      http://svn.sgcharo.com/svn/portal/sourcecode
 */

class CalendarController extends Core_Controller_Action
{

    //var arr login
    private $arrLogin;

    /**
     * Init some params to startup app
     *
     * @return none
     * @author Tai Le Thanh <tai.lt@vn.gnt-global.com>
     */
    public function init()
    {
        parent::init();

        //Asign login
        $this->arrLogin = $this->view->arrLogin;

        //Get Controller
        $controller             = $this->getRequest()->getParam('controller');
        $this->view->controller = $controller;
    }
    public function deleteNumberNotify($iNotifyID,$iAccountID)
    {

        //Key Notify
        $keyRedisNotify = Core_Global::getKeyPrefixCaching('redis_new_notify_list_key').$iAccountID;
        $iResult = Core_Business_Nosql_Redis::getInstance()->deleteItemList($keyRedisNotify, $iNotifyID, time());

        return $iResult;
    } 
    
    public function deleteItemNotify($iNotifyID,$iAccountID)
    {

        //Key Notify
        $keyRedisNotify = Core_Global::getKeyPrefixCaching('redis_my_notify_list_key').$iAccountID;
        $iResult = Core_Business_Nosql_Redis::getInstance()->deleteItemList($keyRedisNotify, $iNotifyID, time());

        return $iResult;
    } 
    
    public function addNotificationIDToUser($iNotifyID,$iAccountID)
    {

        //Key Notify
        $keyRedisNotify = Core_Global::getKeyPrefixCaching('redis_my_notify_list_key').$iAccountID;
        $iResult = Core_Business_Nosql_Redis::getInstance()->setList($keyRedisNotify, $iNotifyID, time());

        return $iResult;
    }
 
    public function addNewNotification($iNotifyID,$iAccountID)
    {
        return Core_Common::setRedis('redis_new_notify_list_key', $iAccountID, $iNotifyID);
    }     /**
     * Display default calendar
     *
     * @return none
     * @author Tai Le Thanh <tai.lt@vn.gnt-global.com>
     */
    public function indexAction()
    {

        
        $eventId = $this->_request->getParam('event-id');
        
        $commonPostData = CalendarGateway::getCommonPost($this->getRequest());        
        $commonPostData = CalendarGateway::filterCommonPost($commonPostData);
        
        $day   = $commonPostData['day'];
        $month   = $commonPostData['month'];
        $year   = $commonPostData['year'];

        $arrLogin       = Admin::getInstance()->getLogin();        
        $allGroup = CalendarGateway::getAllGroup($arrLogin['accountID']);
        
        $this->view->defaultGroups  = $allGroup[DEFAULT_GROUP];
        $this->view->otherGroups    = $allGroup[OTHER_GROUP];
        $this->view->projects       = $allGroup[PROJECT_GROUP];
        $this->view->teams          = $allGroup[TEAM_GROUP];
        
        $strProjectId = implode(',', $commonPostData['projectIdList']);
        $strTeamIdList = implode(',', $commonPostData['teamIdList']);
        $strDefaultGroupIdList = implode(',', $commonPostData['defaultGroupIdList']);
        $strOtherGroupIdList = implode(',', $commonPostData['otherGroupIdList']);
        $personal = $commonPostData['personal'];
                
        $this->assignCommonPostToView($commonPostData);
        $this->view->eventId = $eventId;
    }
    
    /**
     * getTeamListAction
     * 
     * @return none
     * @author Tai Le Thanh <tai.lt@vn.gnt-global.com>
     */
    public function getTeamListAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $arrLogin       = Admin::getInstance()->getLogin();
        $arrGroupMember = CalendarGateway::getTeamList($arrLogin['accountID']);       

        $result = array(
            'total' => count($arrGroupMember),
            'data'  => $arrGroupMember
        );

        echo json_encode($result);
        exit();
    }
    /**
     * getDefaultGroupListAction
     * 
     * @return none
     * @author Tai Le Thanh <tai.lt@vn.gnt-global.com>
     */
    public function getDefaultGroupListAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $arrLogin       = Admin::getInstance()->getLogin();
        $defaultGroupList = CalendarGateway::getDefaultGroup($arrLogin['accountID']);       

        $result = array(
            'total' => count($defaultGroupList),
            'data'  => $defaultGroupList
        );

        echo json_encode($result);
        exit();
    }
    /**
     * getOtherGroupListAction
     * 
     * @return none
     * @author Tai Le Thanh <tai.lt@vn.gnt-global.com>
     */
    public function getOtherGroupListAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $arrLogin       = Admin::getInstance()->getLogin();
        $defaultGroupList = CalendarGateway::getOtherGroup($arrLogin['accountID']);       

        $result = array(
            'total' => count($defaultGroupList),
            'data'  => $defaultGroupList
        );

        echo json_encode($result);
        exit();
    }
    
    /**
     * getProjectListAction
     * 
     * @return none
     * @author Tai Le Thanh <tai.lt@vn.gnt-global.com>
     */
    public function getProjectListAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $arrLogin = Admin::getInstance()->getLogin();

        $projectList = CalendarGateway::getProjectList($arrLogin['accountID']);

        $result = array(
            'total' => count($projectList),
            'data'  => $projectList
        );

        echo json_encode($result);
        exit();
    }
    
    /**
     * getCalendarByMonthAction
     * 
     * @return none
     * @author Tai Le Thanh <tai.lt@vn.gnt-global.com>
     */
    public function getCalendarByMonthAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        if (!$this->getRequest()->isPost()) {
            return;
        }

        $commonPostData = CalendarGateway::getCommonPost($this->getRequest());
        $commonPostData = CalendarGateway::filterCommonPost($commonPostData);

        $day   = $commonPostData['day'];
        $month   = $commonPostData['month'];
        $year   = $commonPostData['year'];

        $unixTime              = mktime(0, 0, 0, $month, $tmpDay = 1, $year);
        $nextMonthFromUnixTime = strtotime('+1 month', $unixTime);
        $prevMonthFromUnixTime = strtotime('-1 month', $unixTime);

        $monthName = date('F', $unixTime);
        $nextMonth = date('m', $nextMonthFromUnixTime);
        $nextYear  = date('Y', $nextMonthFromUnixTime);

        $prevMonth = date('m', $prevMonthFromUnixTime);
        $prevYear  = date('Y', $prevMonthFromUnixTime);


        $arrMonth = CalendarGateway::fillMonth($month, $year);
        
        $arrLogin = Admin::getInstance()->getLogin();
        $accountId = $arrLogin['accountID'];
        $startDate = CalendarGateway::getFirstDayOfMonth($arrMonth);
        $endDate   = CalendarGateway::getLastDayOfMonth($arrMonth);

        $from      = mktime(0, 0, 0, $startDate['month'], $startDate['day'], $startDate['year']);
        $to        = mktime(23, 59, 0, $endDate['month'], $endDate['day'], $endDate['year']);
        
        $strProjectId = implode(',', $commonPostData['projectIdList']);
        $strTeamIdList = implode(',', $commonPostData['teamIdList']);
        $strDefaultGroupIdList = implode(',', $commonPostData['defaultGroupIdList']);
        $strOtherGroupIdList = implode(',', $commonPostData['otherGroupIdList']);
        $personal = $commonPostData['personal'];
                
        $options = array(
            'defaultGroupId' => $strDefaultGroupIdList,
            'catDefaultGroup' => EVENT_DEFAULT_GROUP,
            
            'otherGroupId' => $strOtherGroupIdList,
            'catOtherGroup' => EVENT_OTHER_GROUP,
            
            'teamId' => $strTeamIdList,
            'catTeam' => EVENT_TEAM,
            
            'projectId' => $strProjectId,
            'catProject' => EVENT_PROJECT,
            
            'personal' => $personal,
            'arrMonth' => $arrMonth
        );

        $eventsList = CalendarGateway::getCalendarEventByMonth($from, $to, $accountId, $options);
        
        $this->view->isTodayMonth  = CalendarGateway::isTodayMonth($commonPostData);      

        $this->view->nextMonth = $nextMonth;
        $this->view->nextYear  = $nextYear;

        $this->view->prevMonth = $prevMonth;
        $this->view->prevYear  = $prevYear;


        $this->view->eventsList     = $eventsList;
        $this->view->monthName      = $monthName;
        
        
        
        $allGroup = CalendarGateway::getAllGroup($arrLogin['accountID']);
        
        $this->view->defaultGroups  = $allGroup[DEFAULT_GROUP];
        $this->view->otherGroups    = $allGroup[OTHER_GROUP];
        $this->view->projects       = $allGroup[PROJECT_GROUP];
        $this->view->teams          = $allGroup[TEAM_GROUP];
        
        $this->assignCommonPostToView($commonPostData);

        $result = array(
            'html' => $this->view->render('get-calendar-by-month.phtml'),
        );


        echo json_encode($result);
        exit();
    }

    /**
     * getCalendarByWeekAction
     * 
     * @return none
     * @author Tai Le Thanh <tai.lt@vn.gnt-global.com>
     */    
    public function getCalendarByWeekAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if (!$this->getRequest()->isPost()) {
            return;
        }
 
        $result = $this->getCalendarByWeek('get-calendar-by-week.phtml');
        
        echo json_encode($result);
        exit();
    }
    
    /**
     * getCalendarByWorkWeekAction
     * 
     * @return none
     * @author Tai Le Thanh <tai.lt@vn.gnt-global.com>
     */
    public function getCalendarByWorkWeekAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if (!$this->getRequest()->isPost()) {
            return;
        }

        $result = $this->getCalendarByWeek('get-calendar-by-work-week.phtml');
        echo json_encode($result);
        exit();
    }

    /**
     * getCalendarByDayAction
     * 
     * @return none
     * @author Tai Le Thanh <tai.lt@vn.gnt-global.com>
     */
    public function getCalendarByDayAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if (!$this->getRequest()->isPost()) {
            return;
        }

        $commonPostData = CalendarGateway::getCommonPost($this->getRequest());
        
        $commonPostData = CalendarGateway::filterCommonPost($commonPostData);
        
        
        $day   = $commonPostData['day'];
        $month   = $commonPostData['month'];
        $year   = $commonPostData['year'];
                
        
        $date     = mktime(0, 0, 0, $month, $day, $year);
        $nextDate = strtotime('+1 day', $date);
        $prevDate = strtotime('-1 day', $date);

        $dayName       = date('l', $date);
        $monthName     = date('F', $date);
        $weekNumber    = date('W', $date);

        
        $arrLogin = Admin::getInstance()->getLogin();
        $accountId = $arrLogin['accountID'];
        
        
        
        $this->view->isTodayDay       = CalendarGateway::isTodayDay($commonPostData);
        $this->view->dayName       = $dayName;
        $this->view->monthName     = $monthName;

        $this->view->weekNumber    = $weekNumber;

        $this->view->nextDay   = date('j', $nextDate);
        $this->view->nextMonth = date('m', $nextDate);
        $this->view->nextYear  = date('Y', $nextDate);

        $this->view->prevDay   = date('j', $prevDate);
        $this->view->prevMonth = date('m', $prevDate);
        $this->view->prevYear  = date('Y', $prevDate);
        
        $allGroup = CalendarGateway::getAllGroup($arrLogin['accountID']);
        
        $this->view->defaultGroups  = $allGroup[DEFAULT_GROUP];
        $this->view->otherGroups    = $allGroup[OTHER_GROUP];
        $this->view->projects       = $allGroup[PROJECT_GROUP];
        $this->view->teams          = $allGroup[TEAM_GROUP];
        
        $strProjectId = implode(',', $commonPostData['projectIdList']);
        $strTeamIdList = implode(',', $commonPostData['teamIdList']);
        $strDefaultGroupIdList = implode(',', $commonPostData['defaultGroupIdList']);
        $strOtherGroupIdList = implode(',', $commonPostData['otherGroupIdList']);
        $personal = $commonPostData['personal'];
                
        $options = array(
            'defaultGroupId' => $strDefaultGroupIdList,
            'catDefaultGroup' => EVENT_DEFAULT_GROUP,
            
            'otherGroupId' => $strOtherGroupIdList,
            'catOtherGroup' => EVENT_OTHER_GROUP,
            
            'teamId' => $strTeamIdList,
            'catTeam' => EVENT_TEAM,
            
            'projectId' => $strProjectId,
            'catProject' => EVENT_PROJECT,
            
            'personal' => $personal
        );
        

        
        $from = mktime(0 ,0, 0, $month, $day, $year); 
        $to = mktime(23 ,59, 0, $month, $day, $year); 
        
        $eventsList = CalendarGateway::getCalendarEventByDay($from, $to, $accountId, $options);
        
        $this->view->alldayEvents     = $eventsList['allday'];
        $this->view->normalEvents     = $eventsList['normal'];
        
        
        
        $this->assignCommonPostToView($commonPostData);
        
        $result = array(
            'html'  => $this->view->render('get-calendar-by-day.phtml'),
        );

        echo json_encode($result);
        exit();
    }
    
    /**
     * 
     * @param array $commonPostData
     * @return none
     * @author Tai Le Thanh <tai.lt@vn.gnt-global.com>
     */
    private function assignCommonPostToView($commonPostData) 
    {
        foreach ($commonPostData as $name=>$value) {
            $this->view->$name = $value;
        }
    }
    
    /**
     * 
     * @param type $template
     * @return array
     * @author Tai Le Thanh <tai.lt@vn.gnt-global.com>
     */
    private function getCalendarByWeek($template)
    {
        $commonPostData = CalendarGateway::getCommonPost($this->getRequest());
        $commonPostData = CalendarGateway::filterCommonPost($commonPostData);
        $day   = $commonPostData['day'];
        
        $day   = $commonPostData['day'];
        $month   = $commonPostData['month'];
        $year   = $commonPostData['year'];
                
        
        $weekNumber    = date('W', mktime(0, 0, 0, $month, $day, $year));
        $monthName     = date('F', mktime(0, 0, 0, $month, $day, $year));
        $nextWeek      = strtotime('+1 week', mktime(0, 0, 0, $month, $day, $year));
        $prevWeek      = strtotime('-1 week', mktime(0, 0, 0, $month, $day, $year));

        $arrLogin = Admin::getInstance()->getLogin();
        $accountId = $arrLogin['accountID'];

        $arrWeek               = CalendarGateway::fillWeek($day, $month, $year);
        $monday = CalendarGateway::getMondayOfWeek($arrWeek);
        $sunday = CalendarGateway::getSundayOfWeek($arrWeek);
        
        $this->view->arrWeek   = $arrWeek;
        $this->view->monthName = $monthName;

        $this->view->dayNextWeek   = date('j', $nextWeek);
        $this->view->monthNextWeek = date('m', $nextWeek);
        $this->view->yearNextWeek  = date('Y', $nextWeek);

        $this->view->dayPrevWeek   = date('j', $prevWeek);
        $this->view->monthPrevWeek = date('m', $prevWeek);
        $this->view->yearPrevWeek  = date('Y', $prevWeek);
        
        $allGroup = CalendarGateway::getAllGroup($arrLogin['accountID']);
        
        $this->view->defaultGroups  = $allGroup[DEFAULT_GROUP];
        $this->view->otherGroups    = $allGroup[OTHER_GROUP];
        
        $allGroup = CalendarGateway::getAllGroup($arrLogin['accountID']);
        
        $this->view->defaultGroups  = $allGroup[DEFAULT_GROUP];
        $this->view->otherGroups    = $allGroup[OTHER_GROUP];
        $this->view->projects       = $allGroup[PROJECT_GROUP];
        $this->view->teams          = $allGroup[TEAM_GROUP];
        
        $this->view->isTodayDay       = CalendarGateway::isTodayDay($commonPostData);
        $this->view->weekNumber       = $weekNumber;
        $this->assignCommonPostToView($commonPostData);
        
        $strProjectId = implode(',', $commonPostData['projectIdList']);
        $strTeamIdList = implode(',', $commonPostData['teamIdList']);
        $strDefaultGroupIdList = implode(',', $commonPostData['defaultGroupIdList']);
        $strOtherGroupIdList = implode(',', $commonPostData['otherGroupIdList']);
        $personal = $commonPostData['personal'];
                
        $options = array(
            'defaultGroupId' => $strDefaultGroupIdList,
            'catDefaultGroup' => EVENT_DEFAULT_GROUP,
            
            'otherGroupId' => $strOtherGroupIdList,
            'catOtherGroup' => EVENT_OTHER_GROUP,
            
            'teamId' => $strTeamIdList,
            'catTeam' => EVENT_TEAM,
            
            'projectId' => $strProjectId,
            'catProject' => EVENT_PROJECT,
            
            'personal' => $personal
        );
        


        $from = mktime(0 ,0, 0, $monday['month'], $monday['day'], $monday['year']); 
        $to = mktime(23 ,59, 0, $sunday['month'], $sunday['day'], $sunday['year']);         
        $events = CalendarGateway::getCalendarEventByWeek($from, $to, $accountId, $options);

        $this->view->alldayEvents     = $events['allday'];
        $this->view->normalEvents     = $events['normal'];
        
        $result = array(
            'html' => $this->view->render($template),
        );
        return $result;
    }
    
    private function getCalendarEventPost() 
    {
        //get calendar event post by gateway
        return CalendarGateway::getCalendarEventPost($this->getRequest());
    }
    
    public function addCalendarEventAction()
    {
        if (!$this->getRequest()->isPost()) {
            return;
        }
        
        $posts = $this->getCalendarEventPost();
        $data = CalendarGateway::buildCalendarEventParams($posts);
        
        //set created account
        $arrLogin  = Admin::getInstance()->getLogin();
        $accountId = $arrLogin['accountID'];        
        $data['createdAccountId'] = $accountId;
        
        //set created date
        $data['createdDate'] = time();
        
        $calendarEventId = $this->addCalendarEvent($data);
        
        if ($calendarEventId && (isset($data['eventCate']) && $data['eventCate'] == EVENT_PERSONAL)) {
            $arrAccountId = array();
            trim($data['accountIdList']) && $arrAccountId = explode(',', $data['accountIdList']);
            $this->addAttendeeUser($arrAccountId, $calendarEventId);
        }
        
        $error = FALSE;
        $message = '';
        if (!$calendarEventId) {
            $error = TRUE;
            $message = $this->view->locales->calendarEvent->errorAddEvent;
        }
        
        $result = array(
            'error' =>  $error,
            'message' =>  $message,
            'calendarEventId' => $calendarEventId
        );
        
        echo json_encode($result);
        exit;
    }
    
    private function addAttendeeUser($arrAccountId, $calendarEventId)
    {
        foreach ($arrAccountId as $accountId) {
            $data = array(
                'accountId' => $accountId
            );
            CalendarGateway::addAttendeeUser($data, $calendarEventId);
        }
    }
    private function addCalendarEvent($data)
    {
        return CalendarGateway::addCalendarEvent($data);
    }
    
    public function searchUserAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        if (!$this->getRequest()->isPost()) {
            return;
        }
        
        $users = array();
        
        $searchText = trim($this->getRequest()->getPost('searchText'));
        $searchType = $this->getRequest()->getPost('searchType');
        
        //escape special char
        $searchText = Core_MysqlUtility::escapeSpecialChar($searchText);
        
        switch($searchType) {
            case EVENT_SEARCH_USER_BY_EMAIL:
                $searchText && $users = CalendarGateway::getAccountInfoByLikeEmail($searchText);
                break;
            case EVENT_SEARCH_USER_BY_NAME:
                $searchText && $users = CalendarGateway::getAccountInfoByLikeName($searchText);
                break;
            default:
                break;
        }
        $this->view->users = $users;
        $result = array(
            'error'   => '',
            'message' => '',
            'html'    => $this->view->render('user-list.phtml')
        );
        echo json_encode($result);
        
        exit();
    }
    
    public function getCalendarEventByIdAction()
    {

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        if (!$this->getRequest()->isPost()) {
            return;
        }
        
        $arrLogin       = Admin::getInstance()->getLogin();
        $createdAccountId = $arrLogin['accountID'];
        $calendarEventId = (int)$this->getRequest()->getPost('calendarEventId');
        
        $data = array(
            'calendarEventId' => $calendarEventId,
            'createdAccountId' => $createdAccountId
        );
        
        $calendarEvent = CalendarGateway::getCalendarEventByEventId($data);
        
	//convert datetime to userconfig's timezone 
        if($calendarEvent){
            $calendarEvent['from_date'] = date('Y/m/d', $calendarEvent['from_day']);
            $calendarEvent['to_date'] = date('Y/m/d', $calendarEvent['to_day']);
            $calendarEvent['from_time'] = date('H:i', $calendarEvent['from_day']);
            $calendarEvent['to_time'] = date('H:i', $calendarEvent['to_day']);
        }
        
        $result = array(
            'total'  => count($calendarEvent),
            'data' => $calendarEvent
        );
        
        echo json_encode($result);
        
        exit;
    }
    public function deleteCalendarEventByIdAction()
    {

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        if (!$this->getRequest()->isPost()) {
            return;
        }
        
        $error = FALSE;
        $message = '';
        $arrLogin       = Admin::getInstance()->getLogin();
        $createdAccountId = $arrLogin['accountID'];
        $calendarEventId = (int)$this->getRequest()->getPost('eventId');
        
        $data = array(
            'calendarEventId' => $calendarEventId,
            'createdAccountId' => $createdAccountId
        );
        $calendarEvent = CalendarGateway::getCalendarEventByEventId($data);        
        
        // not found event
        if (!$calendarEvent) {
            $result = array(
                'error' => TRUE,
                'message' => $this->view->locales->calendarEvent->errorEditEvent,
                'calendarEventId' => NULL
            );
            echo json_encode($result);
            exit;
        }
        
        //the event was created by other user
        if ($calendarEvent['created_account_id'] != $createdAccountId) {
            $result = array(
                'error' => TRUE,
                'message' => $this->view->locales->calendarEvent->notOwnerDelete,
                'calendarEventId' => NULL
            );
            echo json_encode($result);
            exit;
        }
        
        $delete = $this->deleteCalendarEventById($data);
        
        if(!$delete) {
            $error = TRUE;
            $message = $this->view->locales->calendarEvent->errorDeleteEvent;
        }
        
        $result = array(
            'error'  => $error,
            'message' => $message
        );
        
        echo json_encode($result);
        
        exit;
    }
    
    public function editCalendarEventAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $error = FALSE;
        $message = '';
        
        $posts = $this->getRequest()->getPost();
        $data = CalendarGateway::buildCalendarEventParams($posts);
        
        //set created account
        $arrLogin  = Admin::getInstance()->getLogin();
        $accountId = $arrLogin['accountID'];        
        $data['createdAccountId'] = $accountId;
        
        //set updated date
        $data['updatedDate'] = time();

        $data['calendarEventId'] = (int)$data['calendarEventId'];
        
        
        $arrLogin       = Admin::getInstance()->getLogin();
        $createdAccountId = $arrLogin['accountID'];
        
        
        $searchEventData = array(
            'calendarEventId' => $data['calendarEventId'],
            'createdAccountId' => $createdAccountId
        );
        
        $calendarEvent = CalendarGateway::getCalendarEventByEventId($searchEventData);        
        
        // not found event
        if (!$calendarEvent) {
            $result = array(
                'error' => TRUE,
                'message' => $this->view->locales->calendarEvent->errorEditEvent,
                'calendarEventId' => NULL
            );
            echo json_encode($result);
            exit;
        }
        
        //the event was created by other user
        if ($calendarEvent['created_account_id'] != $createdAccountId) {
            $result = array(
                'error' => TRUE,
                'message' => $this->view->locales->calendarEvent->notOwnerSave,
                'calendarEventId' => NULL
            );
            echo json_encode($result);
            exit;
        }
        
        $data['createdDate'] = $calendarEvent['created_date'];
        
        $edit = $this->editCalendarEvent($data);
        if (!$edit) {
            $error = TRUE;
            $message = $this->view->locales->calendarEvent->errorEditEvent;
        }
        
        switch ($data['eventCate']) {
            case EVENT_PERSONAL:
                $dataAttendeeUser = array(
                    'calendarEventId' => $data['calendarEventId']
                );
                //delete old user
                CalendarGateway::deleteAttendeeUser($dataAttendeeUser);
                
                //add new user
                $arrAccountId = array();
                trim($data['accountIdList']) && $arrAccountId = explode(',', $data['accountIdList']);
                $this->addAttendeeUser($arrAccountId, $data['calendarEventId']);
                break;
            default:
                break;
        }
        
        $result = array(
            'error' => $error,
            'message' => $message,
            'calendarEventId' => $calendarEvent['calendar_event_id']
        );
        
        echo json_encode($result);
        
        exit;
    }
    
    private function editCalendarEvent($data)
    {
        return CalendarGateway::editCalendarEvent($data);
    }
    
    private function deleteCalendarEventById($data)
    {
        return CalendarGateway::deleteCalendarEventById($data);
    }
    
    public function getAttendeeUserAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        if (!$this->getRequest()->isPost()) {
            return;
        }
        
        $eventId = (int)$this->getRequest()->getPost('eventId');
        $data = array(
            'eventId' => $eventId
        );
        $attendeeUser = CalendarGateway::getAttendeeUser($data);
        
        $result = array (
            'total' => count($attendeeUser),
            'data' => $attendeeUser
        );
        
        echo json_encode($result);
        
        exit;
    }
    
    public function uploadAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        if (!$this->getRequest()->isPost()) {
            return;
        }
        
        $calendarEventId = $this->getRequest()->getParam('id');
        $arrLogin       = Admin::getInstance()->getLogin();
        $createdAccountId = $arrLogin['accountID'];
        
        
        $searchEventData = array(
            'calendarEventId' => $calendarEventId,
            'createdAccountId' => $createdAccountId
        );
        
        $calendarEvent = CalendarGateway::getCalendarEventById($searchEventData);        

        if (!$calendarEvent) {
            $result = array(
                'error' => TRUE,
                'message' => $this->view->locales->calendarEvent->notFoundData
            );
            echo json_encode($result);
            exit;
        } 
        
        $updloadFileName = isset($_FILES['attach-file']['name']) ? $_FILES['attach-file']['name'] : NULL;
        $updloadTempFileName = isset($_FILES['attach-file']['tmp_name']) ? $_FILES['attach-file']['tmp_name'] : NULL;
        $fileSize = isset($_FILES['attach-file']['size']) ? $_FILES['attach-file']['size'] : 0;
        
        $fileInfo = pathinfo($updloadFileName);
        
        $fileInfo = pathinfo($updloadFileName);
        $name = isset($fileInfo['filename']) ? $fileInfo['filename'] : NULL;
        $ext = isset($fileInfo['extension']) ? $fileInfo['extension'] : NULL;
        $saveFileName = $name . '-' . md5(time()) . '.' . $ext;
        
        $pattern = '/^(php)|(sh)$/i';
        if (preg_match($pattern, $ext)) {
            $result = array(
                'error' => TRUE,
                'message' => $this->view->locales->calendarEvent->notSupportFileType . ' ' . $ext
            );
            echo json_encode($result);
            exit;            
        }
        
        if ($fileSize > 0) {
                    
            CalendarGateway::moveUploadFileTo($updloadTempFileName, PATH_FILES_UPLOAD_DIR . '/' . FOLDER_CALENDAR_FILES . '/' . $saveFileName);
        
            $data = array(
                'calendarEventId' => $calendarEventId,
                'uploadFileName' => $updloadFileName,
                'saveUploadFileName' => $saveFileName,
            );
            
            CalendarGateway::updateUploadFileName($data);
            
            //remove old upload file
            $saveUploadFileName = $calendarEvent['save_upload_file_name'];
            $currentUploadFilename = PATH_FILES_UPLOAD_DIR . '/' . FOLDER_CALENDAR_FILES . '/' . $saveUploadFileName;
            $saveUploadFileName && CalendarGateway::deleteFile($currentUploadFilename);       
        }
        
        $result = array(
            'error' => FALSE,
            'message' => ''
        );
        echo json_encode($result);
        exit;
    }
    
    public function removeEventUploadFileAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        if (!$this->getRequest()->isPost()) {
            return;
        }
        $calendarEventId = $this->getRequest()->getParam('id');
        $arrLogin       = Admin::getInstance()->getLogin();
        $createdAccountId = $arrLogin['accountID'];
        
        
        $searchEventData = array(
            'calendarEventId' => $calendarEventId,
            'createdAccountId' => $createdAccountId
        );
        
        $calendarEvent = CalendarGateway::getCalendarEventById($searchEventData);        

        if (!$calendarEvent) {
            $result = array(
                'error' => TRUE,
                'message' => $this->view->locales->calendarEvent->notFoundData
            );
            echo json_encode($result);
            exit;
        } 
        
        //remove old upload file
        $saveUploadFileName = $calendarEvent['save_upload_file_name'];
        $currentUploadFilename = PATH_FILES_UPLOAD_DIR . '/' . FOLDER_CALENDAR_FILES . '/' . $saveUploadFileName;
        CalendarGateway::deleteFile($currentUploadFilename);
        
        $data = array(
            'calendarEventId' => $calendarEventId,
            'uploadFileName' => NULL,
            'saveUploadFileName' => NULL,
        );

        CalendarGateway::updateUploadFileName($data);
        
        $result = array(
            'error' => FALSE,
            'message' => ''
        );
        echo json_encode($result);
        exit;
    }
    
    public function getRequest()
    {
        return $this->_request;
    }
	public function eventsAction()
	{
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $startDateHttp = $this->_request->getPost('start');
        $endDateHttp = $this->_request->getPost('end');
        
        $commonPostData = CalendarGateway::getCommonPost($this->getRequest());
        $commonPostData = CalendarGateway::filterCommonPost($commonPostData);

        $day   = $commonPostData['day'];
        $month   = $commonPostData['month'];
        $year   = $commonPostData['year'];

        $unixTime              = mktime(0, 0, 0, $month, $tmpDay = 1, $year);
        $nextMonthFromUnixTime = strtotime('+1 month', $unixTime);
        $prevMonthFromUnixTime = strtotime('-1 month', $unixTime);

        $monthName = date('F', $unixTime);
        $nextMonth = date('m', $nextMonthFromUnixTime);
        $nextYear  = date('Y', $nextMonthFromUnixTime);

        $prevMonth = date('m', $prevMonthFromUnixTime);
        $prevYear  = date('Y', $prevMonthFromUnixTime);


        $arrMonth = CalendarGateway::fillMonth($month, $year);
        
        $arrLogin = Admin::getInstance()->getLogin();
        $accountId = $arrLogin['accountID'];
        $startDate = CalendarGateway::getFirstDayOfMonth($arrMonth);
        $endDate   = CalendarGateway::getLastDayOfMonth($arrMonth);

        //$from      = mktime(0, 0, 0, $startDate['month'], $startDate['day'], $startDate['year']);
        //$to        = mktime(23, 59, 0, $endDate['month'], $endDate['day'], $endDate['year']);
        $from = strtotime($startDateHttp);
        $to = strtotime($endDateHttp);
        
        $strProjectId = implode(',', $commonPostData['projectIdList']);
        $strTeamIdList = implode(',', $commonPostData['teamIdList']);
        $strDefaultGroupIdList = implode(',', $commonPostData['defaultGroupIdList']);
        $strOtherGroupIdList = implode(',', $commonPostData['otherGroupIdList']);
        $personal = $commonPostData['personal'];
        
        
        $options = array(
            'defaultGroupId' => $strDefaultGroupIdList,
            'catDefaultGroup' => EVENT_DEFAULT_GROUP,
            
            'otherGroupId' => $strOtherGroupIdList,
            'catOtherGroup' => EVENT_OTHER_GROUP,
            
            'teamId' => $strTeamIdList,
            'catTeam' => EVENT_TEAM,
            
            'projectId' => $strProjectId,
            'catProject' => EVENT_PROJECT,
            
            'personal' => $personal,
            'arrMonth' => $arrMonth
        );
        
        $eventsList = array();
        $searchEvent = TRUE;
        if (! $strDefaultGroupIdList 
                && ! $strOtherGroupIdList 
                && ! $strTeamIdList 
                && ! $strProjectId 
                && ! $personal) {
            $searchEvent = FALSE;
        } 

        $searchEvent && $eventsList = CalendarGateway::getCalendarEventByDate($from, $to, $accountId, $options);

        echo json_encode($eventsList);
        exit;        
    
	}
	public function getGroupEventsAction()
	{
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $startDateHttp = $this->_request->getPost('start');
        $endDateHttp = $this->_request->getPost('end');
        
        $commonPostData = CalendarGateway::getCommonPost($this->getRequest());
        $commonPostData = CalendarGateway::filterCommonPost($commonPostData);

        $day   = $commonPostData['day'];
        $month   = $commonPostData['month'];
        $year   = $commonPostData['year'];

        $unixTime              = mktime(0, 0, 0, $month, $tmpDay = 1, $year);
        $nextMonthFromUnixTime = strtotime('+1 month', $unixTime);
        $prevMonthFromUnixTime = strtotime('-1 month', $unixTime);

        $monthName = date('F', $unixTime);
        $nextMonth = date('m', $nextMonthFromUnixTime);
        $nextYear  = date('Y', $nextMonthFromUnixTime);

        $prevMonth = date('m', $prevMonthFromUnixTime);
        $prevYear  = date('Y', $prevMonthFromUnixTime);


        $arrMonth = CalendarGateway::fillMonth($month, $year);
        
        $arrLogin = Admin::getInstance()->getLogin();
        $accountId = $arrLogin['accountID'];
        $startDate = CalendarGateway::getFirstDayOfMonth($arrMonth);
        $endDate   = CalendarGateway::getLastDayOfMonth($arrMonth);

        //$from      = mktime(0, 0, 0, $startDate['month'], $startDate['day'], $startDate['year']);
        //$to        = mktime(23, 59, 0, $endDate['month'], $endDate['day'], $endDate['year']);
        $from = strtotime($startDateHttp);
        $to = strtotime($endDateHttp);
        
        $strProjectId = implode(',', $commonPostData['projectIdList']);
        $strTeamIdList = implode(',', $commonPostData['teamIdList']);
        $strDefaultGroupIdList = implode(',', $commonPostData['defaultGroupIdList']);
        $strOtherGroupIdList = implode(',', $commonPostData['otherGroupIdList']);
        $personal = $commonPostData['personal'];
        
        
        $groupList = array_merge(array(), $commonPostData['projectIdList']);
        $groupList = array_merge($groupList, $commonPostData['teamIdList']);
        $groupList = array_merge($groupList, $commonPostData['defaultGroupIdList']);
        $groupList = array_merge($groupList, $commonPostData['otherGroupIdList']);
  
          
        $options = array('groupIdList' => implode(',', $groupList));

        
        $eventsList = array();
        $searchEvent = TRUE;
        if (! $strDefaultGroupIdList 
                && ! $strOtherGroupIdList 
                && ! $strTeamIdList 
                && ! $strProjectId 
                && ! $personal) {
            $searchEvent = FALSE;
        } 
        
        $searchEvent && $eventsList = CalendarGateway::getCalendarEvent4Group($from, $to, $accountId, $options);
        
     
        
        echo json_encode($eventsList);
        exit;        
    
	}
}
