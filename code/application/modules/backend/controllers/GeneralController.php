<?php
/**
 * @author      :   HoaiTN
 * @name        :   IndexController
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   controller default 
 */
class Backend_GeneralController extends Core_Controller_ActionBackend
{
    private $arrLogin;

    public function init()
    {
        parent::init();

        global $globalConfig;

        //define type
        $this->view->arrType = $globalConfig['general_type'];
        $this->arrLogin = $this->view->arrLogin;
    }
    
    /**
     * Default action
     */
    public function indexAction()
    {

    }
    
    /**
     * Default action
     */
    
    public function editAction()
    {
        global $globalConfig;
        $iGeneralId = $this->_getParam('general_id',0);
        $general    = General::getInstance()->getGeneralByID($iGeneralId);
        if(empty($general))
        {
            $this->_redirect(BASE_ADMIN_URL.'/general');
            exit();
        }
        if($this->_request->isPost())
        {
            $general['name']    = trim($this->_getParam('name',''));
            $general['type']    = $this->_getParam('type',0);
            $general['sort_order']    = $this->_getParam('sort_order',0);
            $general['active']    = $this->_getParam('active',1);
            // validate data
            if(empty($general['name']))
                $arrError[] = array('field'=>'name','message'=>'this field is required');
            if($general['type'] == 0)
                $arrError[] = array('field'=>'type','message'=>'this field is required');

            // check name and type is already ?
            if(!empty($general['name']) && $general['type'] > 0)
            {
                $generalTmp = General::getInstance()->selectByNameAndType($general['name'],$general['type']);
                $flag = true;

                if(!empty($generalTmp))
                {
                    if($generalTmp['general_id'] != $iGeneralId)
                        $flag = false;
                }

                if($flag)
                {
                    ActionLog::getInstance()->insert($this->arrLogin['id'],ActionLog::$update,ActionLog::$general,$this->arrLogin['accountID'],$this->arrLogin['nickName'],$general['name'].' general');
                    General::getInstance()->updateGeneral($iGeneralId, $general['name'],$general['type'],$general['sort_order'],$general['active']);
                    $this->_redirect(BASE_ADMIN_URL.'/general');
                    exit();
                }else{
                    $arrError[0] = array('field'=>'name','message'=>'Name and Type is already exits');
                }


            }
            $this->view->arrError        = $arrError;
        }

        $this->view->generalType    = $globalConfig['general_type'];
        $this->view->general        = $general;

    }
    
    /**
     * Default action
     */

    
   /**
     * Default action
     */
    public function addAction()
    {
        global $globalConfig;

        $arrData    = array('name'=>'','type'=>'','sort_order'=>0,'active'=>1);
        if($this->_request->isPost())
        {
            $arrError   = array();
            // process params
            $arrData['name']    = trim($this->_getParam('name',''));
            $arrData['type']    = $this->_getParam('type',0);
            $arrData['sort_order']    = $this->_getParam('sort_order',0);
            $arrData['active']    = $this->_getParam('active',1);
            // validate data
            if(empty($arrData['name']))
                $arrError[] = array('field'=>'name','message'=>'this field is required');
            if($arrData['type'] == 0)
                $arrError[] = array('field'=>'type','message'=>'this field is required');

            if(!empty($arrData['name']) && $arrData['type'] > 0)
            {
                if(General::getInstance()->selectByNameAndType($arrData['name'],$arrData['type']))
                {
                    $arrError[0] = array('field'=>'name','message'=>'Name and Type is already exits');
                }else{
                    ActionLog::getInstance()->insert($this->arrLogin['id'],ActionLog::$create,ActionLog::$general,$this->arrLogin['accountID'],$this->arrLogin['nickName'],$arrData['name'].' general');
                    General::getInstance()->insertGeneral($arrData['name'],$arrData['type'],$arrData['sort_order'],$arrData['active']);
                    $this->_redirect(BASE_ADMIN_URL.'/general');
                    exit();
                }
            }
            $this->view->arrError        = $arrError;
        }
        $this->view->arrData        = $arrData;
        $this->view->generalType    = $globalConfig['general_type'];
    }
    
    /**
     * Default action
     */
    public function deleteAction()
    {
        $this->_helper->layout()->disableLayout();
        $error  = array('error' => true, 'message' => 'function is disable by admin');
//        if($this->getRequest()->isPost()) {
//            //get params
//            $arrParam = $this->_request->getParams();
//            //get params
//            $arrGeneralId = $arrParam['general_id'];
//            if(is_array($arrGeneralId))
//            {
//                foreach($arrGeneralId as $iGeneralId)
//                {
//                    $general    = General::getInstance()->getGeneralByID($iGeneralId);
//                    if(!empty($general))
//                    {
//                        ActionLog::getInstance()->insert($this->arrLogin['id'],ActionLog::$delete,ActionLog::$general,$this->arrLogin['accountID'],$this->arrLogin['nickName'],$general['name'].' general');
//                        General::getInstance()->removeGeneral($iGeneralId);
//                    }
//                    else {
//                        $error  = array('error' => true, 'message' => 'Group Member Not Found');
//                    }
//                }
//            }
//
//        }
        echo Zend_Json::encode($error);
        exit();
    }

    public function lstgeneralAction()
    {
        $this->_helper->layout()->disableLayout();

        // process params Get request of DataTable
        $draw =  $this->_getParam('draw',0);
        $limit  = $this->_getParam('length',ADMIN_PAGE_SIZE);
        $offset = $this->_getParam('start',0);

        $queryString = Core_Common::getQueryString();
        $search =  $queryString['search'];
        $columns = $queryString['columns'];
        $key    = isset($search['value']) ? $search['value'] : '';


        // get all without action mode
        $generals = General::getInstance()->getGeneralList($key, 0, 11, $offset, $limit);

        $data   = array();
        // process data response
        if(!empty($generals)){
            foreach($generals['data'] as $key=>$general)
            {
                $general      = Core_Common::generalProcess($general);
                $actions    =  ' <a href="'.BASE_ADMIN_URL.'/general/edit?general_id='.$general['general_id'].'" data-action="general-edit"><i class="fa fa-pencil-square-o"></i></a> ';
                $actions   .=  ' <a href="javascript:void(0);" data-action="general-delete" data-value="'.$general['general_id'].'"><i class="fa fa-trash-o"></i></a> ';
                $order      =  ' <a href="javascript:void(0);" ><i class="fa fa-ellipsis-v"></i></a>';
                $generalName       = ' <a href="'.BASE_ADMIN_URL.'/general/edit?general_id='.$general['general_id'].'" >'.$general['name'].'</a> ';
                $checkbox_delete = '<div class="checkbox-custom checkbox-primary"><input type="checkbox" id="inputUnchecked'.$key.'" data-action="check-delete" value="'.$general['general_id'].'"/><label for="inputUnchecked'.$key.'"></label></div>';
                // add data response
                $data[]= array('checkbox_delete'=>$checkbox_delete,'id'=>$general['general_id'],'name'=>$generalName,
                    'active_checkbox'=>$general['active_checkbox'], 'type_name'=>$general['sType'], 'actions'=>$actions,'order'=>$order
                );
            }
            $result = array('draw'=>$draw, 'recordsFiltered'=>$generals['total'],'recordsTotal'=>$generals['total'],'data'=>$data);
        }
        else
            $result = array('draw'=>$draw, 'recordsFiltered'=>0,'recordsTotal'=>0,'data'=>array());

        echo Zend_Json::encode($result);
        exit();
    }
}

