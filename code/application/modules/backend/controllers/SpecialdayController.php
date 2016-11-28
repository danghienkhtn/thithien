<?php
/**
 * Created by PhpStorm.
 * User: thanh.lh
 * Date: 6/2/2015
 * Time: 11:08 AM
 */

class Backend_SpecialdayController extends Core_Controller_ActionBackend{
    private $arrLogin;
    private $globalConfig;
    private $type = 0;
    function init(){
        parent::init();
        global $globalConfig;
        $this->arrLogin = $this->view->arrLogin;
        $this->globalConfig = $globalConfig;
        $this->type = $this->_getParam('type',0);
        $this->view->type   =  $this->type;
    }

    public function indexAction()
    {
        if(!isset($this->globalConfig['special_day_type'][ $this->type]))
            return $this->_redirect(BASE_ADMIN_URL.'/');
    }

    public function addAction()
    {

        $type   =  $this->type;
        if(!isset($this->globalConfig['special_day_type'][$type]))
            return $this->_redirect(BASE_ADMIN_URL.'/');
        $specialDay = array(
            'name'  => '',
            'type'  => $type,
            'type_name'  => '',
            'date_from'  => '',
            'date_to'  => '',
            'no_date' => 0,
            'description'  => '',
        );
        if($this->_request->isPost())
        {
            $arrError   = array();
            $specialDay['name']         = $this->_getParam('name','');
            $specialDay['type']         = $type;
            $specialDay['type_name']    = $this->_getParam('type_name',' ');
            $specialDay['description']  = $this->_getParam('description','');
            $specialDay['no_date']      = $this->_getParam('no_date',0);
            $specialDay['date_to']         = Core_Common::convertStringToYMD($this->_getParam('date_to',''));
            $specialDay['date_from']         = Core_Common::convertStringToYMD($this->_getParam('date_from',''));
            if($specialDay['type'] == 3) { // leave unpaid
                $specialDay['date_from'] = date('Y-m-d');
                $specialDay['date_to'] = date('Y-m-d');

            }
            // validate data
            $arrError = Validate::checkEmpty($specialDay,'This field is require',array('no_date', 'type_name','description'));
            if(empty($specialDay['name']) && empty($arrError))
                $arrError []= array('field' => 'name', 'message' => 'This field is require');
            else{
                $specialDaysTmp  = SpecialDay::getInstance()->select($specialDay['name']);
                if(!empty($specialDaysTmp))
                {
                    foreach($specialDaysTmp['data'] as $specialDayTmp)
                    {
                        if(trim($specialDayTmp['name']) === trim($specialDay['name']) && $specialDayTmp['type'] == $specialDay['type'])
                            $arrError []= array('field' => 'name', 'message' => $specialDay['name'].' is already exits');
                    }
                }

            }


            if( $specialDay['date_from'] == '0000-00-00')
                $specialDay['date_from'] = $specialDay['date_to'];
            else if($specialDay['date_to'] == '0000-00-00')
                $specialDay['date_to'] = $specialDay['date_from'];

            if( $specialDay['date_from'] == '0000-00-00')
                $arrError []= array('field' => 'date_from', 'message' => 'This field is require');

            if(empty($arrError)) {


                if($specialDay['no_date'] == 0)
                    $specialDay['no_date'] = Core_Common::differentTime($specialDay['date_from'], $specialDay['date_to']);

                ActionLog::getInstance()->insert($this->arrLogin['id'],ActionLog::$create,ActionLog::$specialDay,$this->arrLogin['accountID'],$this->arrLogin['nickName'],'"'.$specialDay['name'].'" specialDay');
                SpecialDay::getInstance()->insert($specialDay);
                $this->_redirect(BASE_ADMIN_URL.'/specialday?type='.$specialDay['type']);
            }

            $this->view->arrError   = $arrError;
        }
        $this->view->types       = $this->globalConfig['special_day_type'];
        $this->view->specialDay = $specialDay;
    }

    public function editAction()
    {
        $specialDayId   = $this->_getParam('special_day',0);
        $specialDay     = SpecialDay::getInstance()->selectById($specialDayId);

        if(empty($specialDay))
            return $this->_redirect(ADMIN_BASE_URL.'/');

        if($this->_request->isPost())
        {
            $arrError   = array();
            $specialDay['name']         = $this->_getParam('name','');
            $specialDay['type']         = $this->_getParam('type','');;
            $specialDay['type_name']    = $this->_getParam('type_name','');
            $specialDay['description']  = $this->_getParam('description','');
            $specialDay['no_date']      = $this->_getParam('no_date',0);
            $specialDay['date_to']         = Core_Common::convertStringToYMD($this->_getParam('date_to',''));
            $specialDay['date_from']         = Core_Common::convertStringToYMD($this->_getParam('date_from',''));
            if($specialDay['type'] == 3) { // leave unpaid
                $specialDay['date_from'] = date('Y-m-d');
                $specialDay['date_to'] = date('Y-m-d');

            }

            // validate data
            $arrError = Validate::checkEmpty($specialDay,'This field is require',array('no_date','type_name','description'));

            if(empty($specialDay['name']))
                $arrError []= array('field' => 'name', 'message' => 'This field is require');
            else{
                $specialDaysTmp  = SpecialDay::getInstance()->select($specialDay['name']);
                if(!empty($specialDaysTmp))
                {
                    foreach($specialDaysTmp['data'] as $specialDayTmp)
                    {
                        if(trim($specialDayTmp['name']) === trim($specialDay['name']) && $specialDayTmp['type'] == $specialDay['type'] && $specialDayTmp['id'] != $specialDay['id'])
                            $arrError []= array('field' => 'name', 'message' => $specialDay['name'].' is already exits');
                    }
                }

            }

            if( $specialDay['date_from'] == '0000-00-00')
                $specialDay['date_from'] = $specialDay['date_to'];
            else if($specialDay['date_to'] == '0000-00-00')
                $specialDay['date_to'] = $specialDay['date_from'];


            if( $specialDay['date_from'] == '0000-00-00')
                $arrError []= array('field' => 'date_from', 'message' => 'This field is require');

            if(empty($arrError)) {

                if($specialDay['no_date'] == 0)
                    $specialDay['no_date'] = Core_Common::differentTime($specialDay['date_from'], $specialDay['date_to']);

                SpecialDay::getInstance()->update($specialDay);
                ActionLog::getInstance()->insert($this->arrLogin['id'],ActionLog::$update,ActionLog::$specialDay,$this->arrLogin['accountID'],$this->arrLogin['nickName'],'"'.$specialDay['name'].'" specialDay');

                $this->_redirect(BASE_ADMIN_URL.'/specialday?type='.$specialDay['type']);
            }
            $this->view->arrError   = $arrError;
        }
        $this->view->types       = $this->globalConfig['special_day_type'];
        $this->view->specialDay = $specialDay;
    }

    public function deleteAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $error  = array('error' => false, 'message' => '');
        if($this->getRequest()->isPost()) {
            //get params
            $arrParam = $this->_request->getParams();
            //get params
            $arrSpecialDay = $arrParam['special_ids'];
            if(is_array($arrSpecialDay))
            {
                foreach($arrSpecialDay as $iSpecialDay)
                {
                    $specialDay   = SpecialDay::getInstance()->selectById($iSpecialDay);
                    if($specialDay)
                    {
                        ActionLog::getInstance()->insert($this->arrLogin['id'],ActionLog::$delete,ActionLog::$specialDay,$this->arrLogin['accountID'],$this->arrLogin['nickName'],'"'.$specialDay['name'].'" specialDay');
                        SpecialDay::getInstance()->delete($iSpecialDay);
                    }
                    else{
                        $error  = array('error' => true, 'message' => 'Special Day Not Found');
                    }
                }
            }

        }

        echo Zend_Json::encode($error);
        exit();
    }

    public function lstspecialdayAction(){
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $draw =  $this->_getParam('draw',0);
        $limit  = $this->_getParam('length',ADMIN_PAGE_SIZE);
        $offset = $this->_getParam('start',0);

        $queryString = Core_Common::getQueryString();
        $search =  $queryString['search'];
        $columns = $queryString['columns'];
        $key    = isset($search['value']) ? $search['value'] : '';
        $type   = !empty($columns[0]['search']['value']) ? $columns[0]['search']['value'] : 0;
        $result = array();

        $sSortField = '';
        $sSortType = 'DESC';
        $index = (isset($order[0]['column'])) ? $order[0]['column'] : '';
        if (!empty($index) || $index == 0 ) {
            $sSortField = empty($columns[$index]['name']) ? '' : $columns[$index]['name'];
            $sSortType = empty($order[0]['dir']) ? '' : $order[0]['dir'];
        }

        $sDateFrom = '';
        $sDateTo = '';
        // get special days
//        var_dump($key, $type, $sDateFrom, $sDateTo, $sSortField, $sSortType, $offset, $limit);die;
        $specialDays = SpecialDay::getInstance()->select($key, $type, $sDateFrom, $sDateTo, $sSortField, $sSortType, $offset, $limit);

        // parse account to json
        $arrSpecialDays = array();
        if($specialDays)
        {
            foreach ($specialDays['data'] as $key=>$specialDay) {
                // process project
                $specialDay = Core_Common::specialDayProcess($specialDay);
                $checkbox_delete = '<div class="checkbox-custom checkbox-primary"><input type="checkbox" data-action="check-delete" id="inputUnchecked'.$key.'" value="'.$specialDay['id'].'"/><label for="inputUnchecked'.$key.'"></label></div>';
                $actions    =  ' <a href="'.BASE_ADMIN_URL.'/specialday/edit?special_day='.$specialDay['id'].'" ><i class="fa fa-pencil-square-o"></i></a> ';
                $actions   .=  ' <a href="javascript:void(0);" data-action="special-day-delete" data-value="'.$specialDay['id'].'"><i class="fa fa-trash-o"></i></a> ';

                $name = '<a href="' . BASE_ADMIN_URL . '/specialday/edit?special_day=' . $specialDay['id'] . '">' . $specialDay['name'] . '</a>';

                $arrSpecialDays [] = array('id' => $specialDay['id'], 'name' => $name, 'date_from' => $specialDay['sDateFrom'],
                    'date_to' => $specialDay['sDateTo'], 'no_date' => $specialDay['no_date'],
                     'actions' => $actions, 'checkbox_delete' => $checkbox_delete, 'description' => $specialDay['description']
                );
            }
            $result = array('draw'=>$draw, 'recordsFiltered'=>$specialDays['total'],'recordsTotal'=>$specialDays['total'],'data'=>$arrSpecialDays);
        }
        else
            $result = array('draw'=>$draw, 'recordsFiltered'=>0,'recordsTotal'=>0,'data'=>array());

        echo  Zend_Json::encode($result);
    }
}