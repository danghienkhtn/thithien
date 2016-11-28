<?php

require_once APPLICATION_PATH . '/configs/admin.php';
require_once APPLICATION_PATH . '/configs/global.php';

abstract class Core_Controller_ActionBackend extends Zend_Controller_Action {

  //Configuration information
    protected $_configuration = null;
    
    protected $_admin = array();

    /**
     * Init
     */
    public function init() {
    	global $adminConfig;
    	$arrLogin = array();
    	
    	if ( $this->_request->getControllerName() != "login" && $this->_request->getControllerName() != "logout")
    	{
    		//get Login
    		$arrLogin = Admin::getInstance()->getLogin();
            $arrLogin['avatar'] = !empty($arrLogin['avatar']) ? $arrLogin['avatar'] : PATH_AVATAR_URL.'/avatar_default.png';
    		if($this->_request->getActionName() != 'mail')
    		{
    	
    			if (!isset($arrLogin['accountID']) || empty($arrLogin['accountID']))
    			{
    	
    				$this->_redirect(BASE_URL."/login");
    				exit();
    			}
    		}
    	
    	}
        $adminDetail = Admin::getInstance()->checkLogin();

        $controllerName = $this->_request->getControllerName();
        $actionName = $this->_request->getActionName();

        // check permission
        $myPermission = $this->checkPermission($adminDetail, $controllerName, $actionName);



        $arrListAllowMenu = Admin::getInstance()->getPermissionAccessMenu($myPermission);

//        echo'<pre>';
//        var_dump($arrListAllowMenu);
//        echo'</pre>';die;
        if ($this->_request->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }

        //Set title separator
        $this->view->headTitle()->setSeparator(' - ');
        //Add title
        $title = empty($this->title) ? 'CMS Master' : $this->title;
        $this->view->headTitle()->append($title);
        $this->view->arrMenu = $arrListAllowMenu;
        $this->view->adminDetail = $this->_admin =  $adminDetail;    
        $this->view->arrMenuParent = $adminConfig['menu'];
        $this->view->myPermission = $myPermission;
        $this->view->arrLogin = $arrLogin;
        $this->view->controllerName = $this->_request->getControllerName();

    }

    private function checkPermission($adminDetail = array()) {

        global $adminConfig;

        $myPermission = array();

        /*$allowPermission = array(
            'index' => array('index', 'setlanguage'),
            'app' => array('import', 'clean', 'clear', 'recommend', 'topdownload'),
        );*/

        if (!empty($adminDetail)) {

        	$arrRole = array();

        	if(!empty($adminDetail['role_id'])){
        		$arrRole = explode(',', $adminDetail['role_id']);
        	}

        	if(!empty($arrRole)){

        		foreach ($arrRole as $roleId){
            		$myPermission[] = Role::getInstance()->selectOne($roleId);
        		}
        	}

        }

        $acl = new Zend_Acl();

        //add resource
        $fullPermission = $adminConfig['permission'];
        $arrController = array_keys($fullPermission);

        //array_push($arrController, 'index');

        foreach ($arrController as $controller) {
            $acl->add(new Zend_Acl_Resource($controller));
        }

        //add role
        $acl->addRole(new Zend_Acl_Role('guest'));

        $acl->addRole(new Zend_Acl_Role('member'), 'guest');

        $acl->addRole(new Zend_Acl_Role('root'), 'member');

        //set default permission
        $acl->allow('guest', 'login');
        $acl->allow('member', 'logout');
        $acl->allow('member', 'ajax');
        $acl->allow('root');

        /*foreach ($allowPermission as $controller => $arrAction) {
            foreach ($arrAction as $action) {
                $acl->allow('member', $controller, $action);
            }
        }*/

        //set permission
        if (empty($adminDetail)) {

            $role = "guest";

        } else {
            if ($adminDetail['super_admin'] == SUPER_ADMIN) {

                $role = "root";
                $myPermission = array();

                foreach ($fullPermission as $controller => $permission) {

                    if (!empty($permission['action'])) {

                        $data = isset($data) ? $data : "";

                        $myPermission[$controller] = 0;

                        $keys = array();

                        foreach ($permission['action'] as $action => $per) {

                            $key = intval($per['value']);

                            if (!in_array($key, $keys)) {

                                array_push($keys, $key);

                                $myPermission[$controller] += $key;
                            }
                        }
                    }
                }
            } else {

                $role = "member";
				$permissionTmp = array();

                foreach ($fullPermission as $controller => $permission) {

                	if(!empty($myPermission)){

                		$permissionTmp[$controller] = 0;
                		$keys = array();

	                	foreach ($myPermission as $myPer){

		                    if (isset($myPer['permission'][$controller]) && !empty($permission['action'])) {

		                        foreach ($permission['action'] as $action => $per) {

		                        	$key = intval($per['value']);

		                            if ($myPer['permission'][$controller] & $key) {

		                                $acl->allow($role, $controller, $action);

		                                if (!in_array($key, $keys)) {
		                                	array_push($keys, $key);
		                                	$permissionTmp[$controller] += $key;
		                                }

		                            }
		                        }
		                    }

	                	}

                	}
                }

                $myPermission = $permissionTmp;
            }
        }



        //check permission
        $controller = $this->_request->getControllerName();

        $action = $this->_request->getActionName();

        if (!in_array($controller, $arrController)) {
            $acl->add(new Zend_Acl_Resource($controller));
        }

        if (!$acl->isAllowed($role, $controller, $action)) {
            if ($role == "guest") {
                $this_url = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
                $this->_redirect(BASE_URL . "/login?callback=".  urlencode($this_url));
            } else {
                $_SESSION['msg_error'] = MSG_DENIED;
                $this->_redirect(BASE_ADMIN_URL . "/deny/");
            }
        }

        return $myPermission;
    }

    
    
    protected function _processData($array_keys, $dataDetail = array()) {

        $dataProcess = array();

        $dataPost = $this->_request->getParams();

        $arrayKeys = array_keys($array_keys);

        if (!empty($arrayKeys)) {
            foreach ($arrayKeys as $key) {
                $default_value = isset($array_keys[$key]['default']) ? $array_keys[$key]['default'] : ""; // 0
                $dataPost[$key] = isset($dataPost[$key]) ? $dataPost[$key] : "";
                $dataPost[$key] = $dataPost[$key] != "" ? $dataPost[$key] : $default_value;
                $dataProcess[$key] = $dataPost[$key];
            }
        }
        
        //check valid
        $this->_checkValid($array_keys, $dataProcess);

        if ($dataDetail) {
            if (array_intersect_assoc($dataDetail, $dataProcess) == $dataProcess) {
                $dataProcess = array();
            }
        }

        return $dataProcess;
    }

    private function _checkValid($array_keys, $dataProcess) {

        $valid_empty = new Zend_Validate_NotEmpty();

        foreach ($array_keys as $key => $value) {
            if (!empty($value['is_compulsory'])) {
                if ($dataProcess[$key] == "") {
                    $message = str_replace('[var]', $value['text'], $this->view->locales->global->is_required);
                    break;
                }
            }
            
            if(!empty($value['is_alnum'])) {
                if (!ctype_alnum($dataProcess[$key])) {
                    $message = str_replace('[var]', $value['text'], $this->view->locales->global->is_alnum);
                    break;
                }
            }
            
            if (!empty($value['min_length']) && $valid_empty->isValid($dataProcess[$key])) {
                if (strlen($dataProcess[$key]) < $value['min_length']) {
                    $message = str_replace('[var]', $value['text'], $this->view->locales->global->is_not_valid_min_length);
                    $message = str_replace('[value]', $value['min_length'], $message);
                    break;
                }
            }

            if (!empty($value['max_length']) && $valid_empty->isValid($dataProcess[$key])) {
                if (strlen($dataProcess[$key]) > $value['max_length']) {
                    $message = str_replace('[var]', $value['text'], $this->view->locales->global->is_not_valid_max_length);
                    $message = str_replace('[value]', $value['max_length'], $message);
                    break;
                }
            }

            if (!empty($value['is_email']) && $valid_empty->isValid($dataProcess[$key])) {
                if (!filter_var($dataProcess[$key], FILTER_VALIDATE_EMAIL)) {
                    $message = str_replace('[var]', $value['text'], $this->view->locales->global->is_not_valid_email);
                    break;
                }
            }

            if (!empty($value['is_ip']) && $valid_empty->isValid($dataProcess[$key])) {
                if (!filter_var($dataProcess[$key], FILTER_VALIDATE_IP)) {
                    $message = str_replace('[var]', $value['text'], $this->view->locales->global->is_not_valid_ip);
                    break;
                }
            }

            if (!empty($value['is_url']) && $valid_empty->isValid($dataProcess[$key])) {
                if (!filter_var($dataProcess[$key], FILTER_VALIDATE_URL)) {
                    $message = str_replace('[var]', $value['text'], $this->view->locales->global->is_not_valid_url);
                    break;
                }
            }

            if (!empty($value['is_datetime']) && $valid_empty->isValid($dataProcess[$key])) {

                $preg = '/^[1-2][0-9]{3}-[0-1][0-9]-[0-3][0-9]\s([0-2][0-9](:)[0-6][0-9])$/';
                $preg2 = '/^[1-2][0-9]{3}-[0-1][0-9]-[0-3][0-9]$/';

                if (!preg_match($preg, $dataProcess[$key]) && !preg_match($preg2, $dataProcess[$key])) {
                    $message = str_replace('[var]', $value['text'], $this->view->locales->global->is_not_valid_datetime);
                    break;
                }
            }

            if (!empty($value['is_numeric']) && $valid_empty->isValid($dataProcess[$key])) {
                if (!is_numeric($dataProcess[$key])) {
                    $message = str_replace('[var]', $value['text'], $this->view->locales->global->must_contain_only_numbers);
                    break;
                } else {

                    if (!empty($value['min'])) {
                        if ($dataProcess[$key] < $value['min']) {
                            $message = str_replace('[var]', $value['text'], $this->view->locales->global->is_not_valid_min);
                            $message = str_replace('[value]', $value['min'], $message);
                            break;
                        }
                    }

                    if (!empty($value['max'])) {
                        if ($dataProcess[$key] > $value['max']) {
                            $message = str_replace('[var]', $value['text'], $this->view->locales->global->is_not_valid_max);
                            $message = str_replace('[value]', $value['max'], $message);
                            break;
                        }
                    }
                }
            }
        }

        if (!empty($message)) {
            $return = array();
            $return['message'] = $message;
            $return['key'] = isset($key) ? $key : "";
            $return['code'] = 0; //false

            Core_Global::returnJson($return);
        }
    }

}

