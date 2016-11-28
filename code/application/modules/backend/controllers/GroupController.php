<?php

/**
 * @author      :   HoaiTN
 * @name        :   IndexController
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   controller default
 */
class Backend_GroupController extends Core_Controller_ActionBackend
{

    private $globalConfig;
    private $arrLogin;

    public function init()
    {
        parent::init();

        global $globalConfig;

        $this->globalConfig = $globalConfig;
        $this->view->arrCountry = $globalConfig['country'];
        $this->view->arrPublicStatus = $globalConfig['group_public'];
        $this->arrLogin = $this->view->arrLogin;
        $bomGroup = Group::getInstance()->selectByIsBom(IS_BOM);
        if (!empty($bomGroup))
            $this->view->bomMembers = GroupMember::getInstance()->getGroupMemberID($bomGroup['group_id'], 0, MAX_QUERY_LIMIT);
    }

    public function installIsBomGroupAction()
    {
        global $globalConfig;
        $Boom = Group::getInstance()->selectByIsBom(IS_BOM);
        if (empty($Boom)) {
            $iGroupType = $globalConfig['group_type']['team'];
            $arrData = array(
                'group_name' => 'Bom',
                'account_id' => $this->view->arrLogin['accountID'],
                'active' => 1,
                'group_type' => $iGroupType,
                'country_id' => 0,
                'is_public' => 1,
                'admin_id' => 0,
                'manager_id' => 0,
                'is_bom' => 1,
                'image_url' => '',
                'image_name' => '',
                'sort_order' => 1

            );
            $iGroupId = Group::getInstance()->addGroup($arrData);
            if ($iGroupId > 0) {
                echo 'Create Bom group success.';
                exit;
            }
        }
        echo 'Create Bom group fail. Please try again!';
        exit;
    }

    /**
     * Default action
     */
    public function indexAction()
    {

        $this->view->GroupType = $this->globalConfig['group_type'];
    }




    /**
     * Default action
     */
    public function addAction()
    {
        $arrError = array();
        $arrData = array(
            'group_name' => '',
            'account_id' => '',
            'active' => 1,
            'group_type' => '',
            'country_id' => '',
            'is_public' => 1,
            'admin_id' => '',
            'admin_name' => '',
            'manager_id' => '',
            'manager_name' => '',
            'is_bom' => 0,
            'image_url' => '',
            'image_name' => '',
            'sort_order' => ''

        );
        if ($this->getRequest()->isPost()) {

            $sGroupName = $this->_getParam('group_name', '');
            $sImageUrl = $this->_getParam('image_url', '');
            $sImageName = $this->_getParam('image_name', '');
            $iGroupType = $this->_getParam('group_type', 0);
            $iCountryId = $this->_getParam('country_id', 0);
            $iIsPublic = $this->_getParam('is_public', 0);
            $iAdminId = $this->_getParam('admin_id', 0);
            $sAdminName = $this->_getParam('admin_name', '');
            $iManagerId = $this->_getParam('manager_id', 0);
            $sManagerName = $this->_getParam('manager_name', '');
            $iIsBom = $this->_getParam('is_bom', 0);
            $iActive = $this->_getParam('active', 1);
            $iSortOrder = $this->_getParam('sort_order', 0);

            $arrData = array(
                'group_name' => $sGroupName,
                'account_id' => $this->view->arrLogin['accountID'],
                'active' => $iActive,
                'group_type' => $iGroupType,
                'country_id' => $iCountryId,
                'is_public' => $iIsPublic,
                'admin_id' => $iAdminId,
                'admin_name' => $sAdminName,
                'manager_id' => $iManagerId,
                'manager_name' => $sManagerName,
                'is_bom' => $iIsBom,
                'image_url' => $sImageUrl,
                'image_name' => $sImageName,
                'sort_order' => $iSortOrder

            );

            if ($sGroupName == '')
                $arrError [] = array('field' => 'group_name', 'message' => 'this field is required');
            else {
                $checkName = Group::getInstance()->selectByName(trim($sGroupName));
                if (!empty($checkName))
                    $arrError [] = array('field' => 'group_name', 'message' => 'Name is already exits !');
            }

            if ($iSortOrder != 0) {
                if (!Core_Validate::sanityCheck($iSortOrder, 'numeric', 3))
                    $arrError [] = array('field' => 'sort_order', 'message' => 'this field must numeric and (< 999)');
            }

            if ($iGroupType == 0)
                $arrError [] = array('field' => 'group_type', 'message' => 'this field is required');
            if ($iCountryId == 0)
                $arrError [] = array('field' => 'country_id', 'message' => 'this field is required');
//            if ($iAdminId == 0)
//                $arrError [] = array('field' => 'admin_id', 'message' => 'this field is required');
            if ($iManagerId == 0)
                $arrError [] = array('field' => 'manager_id', 'message' => 'this field is required');


            if (!empty($arrError)) {
                $this->view->arrError = $arrError;
            } else {
                if (Group::getInstance()->addGroup($arrData)) {
                    ActionLog::getInstance()->insert($this->arrLogin['id'], ActionLog::$create, ActionLog::$group, $this->arrLogin['accountID'], $this->arrLogin['nickName'], $arrData['group_name'] . ' group');
                    $this->_redirect(BASE_ADMIN_URL . '/group');
                    exit();
                }

            }


        }
        
        //get group company
        $groups = Group::getInstance()->selectByGroupType($this->globalConfig['group_type']['company']);
//        Core_Common::var_dump($groups);
        $this->view->isExcept = FALSE;
        if(!empty($groups) && $groups['total'] > 0){
        	$this->view->isExcept = TRUE;
        }
        
        $this->view->groupTypes = array_flip($this->globalConfig['group_type']);
        $this->view->countries = $this->globalConfig['country'];
        $this->view->arrData = $arrData;
    }

    /**
     * Default action
     */
    public function deleteAction()
    {

        $this->_helper->layout()->disableLayout();
        $error = array('error' => false, 'message' => '');
        if ($this->getRequest()->isPost()) {
            //get params
            $arrParam = $this->_request->getParams();
            //get params
            $arrGroupID = $arrParam['group_id'];
            if (is_array($arrGroupID)) {
                foreach ($arrGroupID as $iGroupID) {
                    $group = Group::getInstance()->getGroupByID($iGroupID);
                    $project = Project::getInstance()->selectById($group['project_id']);
                    if(!empty($project))
                        Project::getInstance()->delete($project['id']);

                    if ($group) {
                        ActionLog::getInstance()->insert($this->arrLogin['id'], ActionLog::$delete, ActionLog::$group, $this->arrLogin['accountID'], $this->arrLogin['nickName'], $group['group_name'] . ' group');
                        $nameFile = trim($group['image_url']);
                        Group::getInstance()->deleteGroup($iGroupID);

                        if (file_exists($nameFile))
                            Core_Image::delete(PATH_AVATAR_UPLOAD_DIR . '/' . $nameFile);
                    } else {
                        $error = array('error' => true, 'message' => 'Group Member Not Found');
                    }
                }
            }

        }

        echo Zend_Json::encode($error);
        exit();
    }

    public function lstgroupAction()
    {
        $this->_helper->layout()->disableLayout();

        // process params Get request of DataTable
        $draw = $this->_getParam('draw', 0);
        $iLimit = $this->_getParam('length', ADMIN_PAGE_SIZE);
        $iOffset = $this->_getParam('start', 0);

        $queryString = Core_Common::getQueryString();
        $search =  $queryString['search'];
        $columns = $queryString['columns'];
        $key = isset($search['value']) ? $search['value'] : '';


        $type = empty($columns[0]['search']['value']) ? 0 :  $columns[0]['search']['value'] ;


        $order = $this->_getParam('order', array());

        $sSortField = '';
        $sSortType = 'DESC';
        $index = (isset($order[0]['column'])) ? $order[0]['column'] : '';
        if (!empty($index)) {
            $sSortField = empty($columns[$index]['name']) ? '' : $columns[$index]['name'];
            $sSortType = empty($order[0]['dir']) ? '' : $order[0]['dir'];
        }

        // get all without action mode
        $iActive = 11;

        $groups = Group::getInstance()->getGroupAlls($iActive, $type, $iOffset, $iLimit, $key, $sSortField, $sSortType);
        $data = array();

        // process data response
        if (!empty($groups)) {
            foreach ($groups['data'] as $key => $group) {
                $group = Core_Common::groupProcess($group);
                $actions = ' <a href="' . BASE_ADMIN_URL . '/group/edit?group_id=' . $group['group_id'] . '" data-action="group-edit" data-value="' . $key . '"><i class="fa fa-pencil-square-o"></i></a> ';
                $actions .= ' <a href="javascript:void(0);" data-action="group-delete" data-value="' . $group['group_id'] . '"><i class="fa fa-trash-o"></i></a> ';
                $order = '<a href="javascript:void(0);" ><i class="fa fa-ellipsis-v"></i></a>';
                $groupName = ' <a href="' . BASE_ADMIN_URL . '/groupmember?group_id=' . $group['group_id'] . '" data-action="group-edit" data-value="' . $key . '">' . Core_Common::SubFullStrings($group['group_name'],0,40) . '</a> ';
                $checkbox_delete = '<div class="checkbox-custom checkbox-primary"><input type="checkbox" data-action="check-delete" id="inputUnchecked' . $key . '" value="' . $group['group_id'] . '"/><label for="inputUnchecked' . $key . '"></label></div>';
                // add data response
                $data[] = array('checkbox_delete' => $checkbox_delete, 'id' => $group['group_id'], 'name' => $groupName, 'group_privacy' => $group['sPrivacy'],
                    'group_active_checkbox' => $group['active_checkbox'], 'group_type_name' => $group['sType'], 'image_url' => $group['imageUrl'],
                    'type' => $group['group_type'], 'actions' => $actions, 'isBom' => $group['is_bom'], 'order' => $order
                );
            }
            $result = array('draw' => $draw, 'recordsFiltered' => $groups['total'], 'recordsTotal' => $groups['total'], 'data' => $data);
        } else
            $result = array('draw' => $draw, 'recordsFiltered' => 0, 'recordsTotal' => 0, 'data' => array());

        echo Zend_Json::encode($result);
        exit();
    }

    public function editAction()
    {
        global $globalConfig;
        $level = $globalConfig['level'];
        $level = array_flip($level);
        $arrError = array();
        $iGroupId = $this->_getParam('group_id', 0);
        $group = Group::getInstance()->getGroupByID($iGroupId);
        if (!$group)
            $this->_redirect(BASE_ADMIN_URL . '/group');
        $group['image_name'] = '';
        $group['admin_name'] = '';
        $group['manager_name'] = '';
        $group = Core_Common::groupProcess($group);
        if ($this->getRequest()->isPost()) {

            $sGroupName = $this->_getParam('group_name', '');
            $sImageUrl = $this->_getParam('image_url', '');
            $sImageName = $this->_getParam('image_name', '');

            $iCountryId = $this->_getParam('country_id', 0);
            $iIsPublic = $this->_getParam('is_public', 0);
            $iAdminId = $this->_getParam('admin_id', 0);
            $sAdminName = $this->_getParam('admin_name', '');
            $iManagerId = $this->_getParam('manager_id', 0);
            $sManagerName = $this->_getParam('manager_name', '');
            $iIsBom = $this->_getParam('is_bom', 0);
            $iActive = $this->_getParam('active', 1);
            $iSortOrder = $this->_getParam('sort_order', 0);


            $group['group_name'] = $sGroupName;
            $group['account_id'] = $this->view->arrLogin['accountID'];
            $group['active'] = $iActive;
//            $group['group_type'] = $iGroupType;
            $group['country_id'] = $iCountryId;
            $group['is_public'] = $iIsPublic;
            $group['admin_id'] = $iAdminId;
            $group['admin_name'] = $sAdminName;
            $group['manager_id'] = $iManagerId;
            $group['manager_name'] = $sManagerName;
            $group['is_bom'] = $iIsBom;
            $group['image_url'] = $sImageUrl;
            $group['image_name'] = $sImageName;
            $group['sort_order'] = $iSortOrder;


            if ($sGroupName == '')
                $arrError [] = array('field' => 'group_name', 'message' => 'this field is required');
            if ($iSortOrder != 0) {
                if (!Core_Validate::sanityCheck($iSortOrder, 'numeric', 3))
                    $arrError [] = array('field' => 'sort_order', 'message' => 'this field must numeric and (< 999)');
            }

//            if ($iGroupType == 0)
//                $arrError [] = array('field' => 'group_type', 'message' => 'this field is required');
//            if ($iCountryId == 0)
//                $arrError [] = array('field' => 'country_id', 'message' => 'this field is required');
//
//            if ($iGroupType > 0 && $iGroupType < 4) {
//                if ($iAdminId == 0)
//                    $arrError [] = array('field' => 'admin_id', 'message' => 'this field is required');
                if ($iManagerId == 0)
                    $arrError [] = array('field' => 'manager_id', 'message' => 'this field is required');
//            }
            if (!empty($arrError))
                $this->view->arrError = $arrError;
            else {

                $project = Project::getInstance()->selectById($group['project_id']);
//                Core_Common::var_dump($project);
                if(!empty($project))
                {
                    $projectMember = array(
                        'project_id' => $project['id'],
                        'account_id' => 0,
                        'account_name' => '',
                        'percent' => 0,
                        'start_date' => $project['start_date'],
                        'end_date' => $project['end_date'],
                        'level' => 0,
                    );
                    if($project['manager_id'] != $group['manager_id'] && $group['manager_id'] > 0) {

                        if($project['manager_id'] > 0) {
                            ProjectMember::getInstance()->deleteByAccountIdAndProjectId($project['manager_id'], $project['id']);
                            GroupMember::getInstance()->deleteGroupMember($project['manager_id'], $project['group_id']);
                        }
                        $project['manager_id'] = $group['manager_id'];
                        $projectMember['account_id'] = $group['manager_id'];
                        $projectMember['level'] = GroupMember::$manager; // manager
                        ProjectMember::getInstance()->insert($projectMember);

                        //add Group Member
                        $arrGroupMember = array('account_id'=>$projectMember['account_id'],'group_id'=>$project['group_id'], 'level'=> GroupMember::$manager);
                        GroupMember::getInstance()->addGroupMember($arrGroupMember);
                    }



                    if($project['admin_id'] != $group['admin_id'])
                    {
                        $project['admin_id'] = $group['admin_id'];
//                        echo $project['admin_id'];die;
                        if($project['admin_id'] > 0) {
                            ProjectMember::getInstance()->deleteByAccountIdAndProjectId($project['admin_id'], $project['id']);
                            GroupMember::getInstance()->deleteGroupMember($project['admin_id'], $project['group_id']);
                        }
                        if($group['admin_id'] > 0) {

                            $projectMember['level'] = $level['staff']; // staff
                            $projectMember['account_id'] = GroupMember::$admin;
                            ProjectMember::getInstance()->insert($projectMember);

                            //add Group Member
                            $arrGroupMember = array('account_id'=>$projectMember['account_id'],'group_id'=>$project['group_id']);
                            GroupMember::getInstance()->addGroupMember($arrGroupMember);
                        }
                    }

                    if($project['name'] != $group['group_name'])
                    {
                        $project['name'] = $group['group_name'];
                    }

                    Project::getInstance()->update($project);

                }

                if (Group::getInstance()->updateGroup($group)) {
                    ActionLog::getInstance()->insert($this->arrLogin['id'], ActionLog::$update, ActionLog::$group, $this->arrLogin['accountID'], $this->arrLogin['nickName'], $group['group_name'] . ' group');
                    $this->_redirect(BASE_ADMIN_URL . '/group');
                    exit();
                }

            }


        }
        $this->view->groupTypes = array_flip($this->globalConfig['group_type']);
        $this->view->countries = $this->globalConfig['country'];
        $this->view->group = $group;
    }
}

