<?php
/**
 * Created by PhpStorm.
 * User: thanh.lh
 * Date: 5/25/2015
 * Time: 8:19 AM
 */

class Backend_ActionlogsController extends Core_Controller_ActionBackend{

    public function indexAction()
    {

    }
    public function detailAction()
    {
        $actionId   =   $this->_getParam('id','');
        $actionLog = ActionLog::getInstance()->selectById($actionId);
        $this->view->actionLog = $actionLog;
    }

    public function lstactionlogsAction()
    {
        $draw =  $this->_getParam('draw',0);
        $limit  = $this->_getParam('length',ADMIN_PAGE_SIZE);
        $offset = $this->_getParam('start',0);

        $queryString = Core_Common::getQueryString();
        $search =  $queryString['search'];
        $columns = $queryString['columns'];
        $key    = isset($search['value']) ? $search['value'] : '';



        $arrActionLog = ActionLog::getInstance()->select($key,'','',0,0,$offset,$limit);
        $data     = array();
        if(!empty($arrActionLog))
        {
            foreach($arrActionLog['data'] as $key=>$actionLog)
            {
//                $created    =    empty($actionLog['created']->sec) ? '' : date('m-d-Y H:i:s',$actionLog['created']->sec);
                $created    =  date('m-d-Y H:i:s',$actionLog['created']->sec);
//                var_dump($actionLog['created']);die;
                // process account
                $actionLog = Core_Common::actionLogsProcess($actionLog);
                $id = '<a href="'.BASE_ADMIN_URL.'/actionlogs/detail?id='.$actionLog['_id'].'">'.$actionLog['id'].'</a>';

                $note = $actionLog['account_name'].' '.$actionLog['sActionName'].' '. Core_Common::SubFullStrings($actionLog['note'],0,40);
                $accountName    = '<a href="'.BASE_ADMIN_URL.'/user/summary?account_id='.$actionLog['account_id'].'">'.$actionLog['account_name'].'</a>';
                $data [] = array('id' => $id, 'time' => $created, 'actionName' => $actionLog['sActionName'],
                    'typeName' => $actionLog['sType'],'accountName'=>$accountName, 'note' => $note);
            }
            $result = array('draw'=>$draw, 'recordsFiltered'=>$arrActionLog['total'],'recordsTotal'=>$arrActionLog['total'],'data'=>$data);
        }
        else
            $result = array('draw'=>$draw, 'recordsFiltered'=>0,'recordsTotal'=>0,'data'=>array());

        echo  Zend_Json::encode($result);
        exit();
    }
}