<?php
/**
 * Created by PhpStorm.
 * User: thanh.lh
 * Date: 5/12/2015
 * Time: 2:19 PM
 */

class Backend_UserController extends Core_Controller_ActionBackend{

    private $arrLogin;
    function init()
    {
        parent::init();
        $this->arrLogin = $this->view->arrLogin;
    }
    public function indexAction()
    {

    }

    public function saveUserContractTypeAction(){
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $token = $this->_getParam('token','');
        if($token != Core_Cookie::getCookie(TOKEN_API)){
            $arrError = array('error'=>true,'message'=>'Token is expire!!!');
            echo Zend_Json::encode($arrError);
            exit();
        }

        if($this->_request->isPost()){
            $iOldContractId = $this->_getParam('old-contract',0);
            $iOldDate = $this->_getParam('old-date',0);

            $iContractTypeId = $this->_getParam('contract-type',0);
            $sDate = $this->_getParam('date','');
            $sDate = Core_Common::convertStringToYMD($sDate);

            $iAccountId = $this->_getParam('account-id',0);


            try{
                $dDate = new DateTime($sDate);
//                $dOldDate = new DateTime($sOldDate);
            }catch(Exception $ex){
                $arrError = array('error'=>true,'message'=>$ex->getMessage());
                echo Zend_Json::encode($arrError);
                exit();
            }

            $iDate = $dDate->getTimestamp();
            $iMonth = $dDate->format('m');
            $iYear = $dDate->format('Y');
            $dPoint = new DateTime('2016-04-01');
            $contractType = General::getInstance()->getGeneralByID($iContractTypeId);


            $errorContract = array('account_id'=>$iAccountId,'general_id'=>$iOldContractId, "date"=> date('d-m-Y',$iOldDate));
            if($dDate < $dPoint){
                $errorContract['date'] = '__-__-____';
                $arrError = array('error'=>true,'message'=>'Date-time support from 01-04-2016','contract'=>$errorContract);

            }elseif(empty($contractType)){
                $arrError = array('error'=>true,'message'=>'contract-type not found','contract'=>$errorContract);

            }
            else {
                $userContracts = UserContract::getInstance()->select($iAccountId,$iOldContractId, $iOldDate);


                // not allow 2 contract has same date
                $userContractByDate = UserContract::getInstance()->select($iAccountId, 0, $iDate);

                if ($iOldContractId == 0 && $iOldDate == 0) {
                    $bInsert = true;

                    // check $iDate already exits
                    if (!empty($userContractByDate['data'])) {
                        $errorContract['date'] = '__-__-____';
                        $arrError = array('error' => true, 'message' => 'Contract date is duplicate. Change date. Please!!!','contract'=>$errorContract);
                    }

                } elseif (!empty($userContracts['data'])) {
                    $bInsert = false;
                    if (!empty($userContractByDate['data'])) {
                        $arrDiff = Core_Common::array_diff_key_with_value(array(), $userContractByDate['data'],'general_id',$iOldContractId);
                        if(!empty($arrDiff)){
                            $arrError = array('error' => true, 'message' => 'Contract date is duplicate. Change date. Please!!!','contract'=>$errorContract);
                        }
                    }

                }
            }

            if(!empty($arrError)){
                echo Zend_Json::encode($arrError);
                exit;
            }

            $userContractData = array('account_id'=>$iAccountId,'general_id'=>$contractType['general_id'],'general_name'=>$contractType['name'], "date"=>$iDate);

            if($bInsert){
                UserContract::getInstance()->insert($userContractData);

            }else{
                $where = array("account_id = $iAccountId","general_id = $iOldContractId","date = $iOldDate");
                UserContract::getInstance()->update($userContractData,$where);

            }

            // update starting-date
            Core_Common::addJob(array('account_id'=>$iAccountId),'JobAccount','updateStartingDate','absence');

            $params = array('account_id'=>$iAccountId,'month'=> $iMonth, 'year'=>$iYear);
            Core_Common::addJob($params,'JobStatisticAbsenceHistory','reUpdateForManualAbsence','statistic-absence-history');
            $arrError = array('error'=>false, 'message'=> 'Success !!!','contract'=>$userContractData);

        }else{
            $arrError = array('error'=>true, 'message'=> 'Post support only !!!');
        }
        echo Zend_Json::encode($arrError);
    }

    public function deleteUserContractAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $token = $this->_getParam('token','');
        if($token != Core_Cookie::getCookie(TOKEN_API)){
            $arrError = array('error'=>true,'message'=>'Token is expire!!!');
            echo Zend_Json::encode($arrError);
            exit();
        }

        if($this->_request->isPost()){
            $iAccountId = $this->_getParam('account-id',0);
            $iContractTypeId = $this->_getParam('contract-type',0);
            $sDate = $this->_getParam('date','');
            $sDate = Core_Common::convertStringToYMD($sDate);
            $iDate = strtotime($sDate);


            $userContracts = UserContract::getInstance()->select($iAccountId,$iContractTypeId,$iDate);
            if(empty($userContracts['data'])){
                $arrError = array('error'=>true,'message'=>'Data not found!!!');
            }else{
                $dDate = new DateTime($sDate);
                $iMonth = $dDate->format('m');
                $iYear = $dDate->format('Y');

                $where = array("account_id = $iAccountId","general_id = $iContractTypeId","date = $iDate");
                UserContract::getInstance()->delete($where);

                $params = array('account_id'=>$iAccountId,'month'=> $iMonth, 'year'=>$iYear);
                Core_Common::addJob($params,'JobStatisticAbsenceHistory','reUpdateForManualAbsence','statistic-absence-history');

                $arrError = array('error'=>false, 'message'=> 'Success !!!');

            }


        }else{
            $arrError = array('error'=>true, 'message'=> 'Post support only !!!');
        }
        echo Zend_Json::encode($arrError);

    }

    public function qaLoginAction()
    {
        $matches = null;
        $msg = $this->_getParam('u','');
        $arrValue = preg_match('/^qa-tester-/',$msg, $matches);

        echo $arrValue;die;
        return "<strong contenteditable='false' data-mention=\"".$matches[2]."\">".$matches[1]."</strong> ";
    }

    public function addUsersTestAction()
    {

        $group = Group::getInstance()->selectByName('QA');
        $groupAllGianty = Group::getInstance()->selectByName('All Gianty');

        $dStartDate = new DateTime();
        $sStartDate = $dStartDate->format('Y-m-d');
        $position = General::getInstance()->selectByNameAndType('tester',General::$position);
        for($i=1; $i <= 100; $i++) {
            $sName = 'QA tester '.$i;
            $sEmail = 'qa-test-'.$i.'@gmail.com'; // ko test duoc chuc nang mail
            $sPicture = AvatarDefault;
            $sUserName = 'qa-tester-'.$i;
            $sTeamName = 'QA';
            $sFirstName = 'QA';
            $sLastName = 'tester '.$i;
            $accountInfo = AccountInfo::getInstance()->getAccountInfoByUserName($sUserName);
            if(empty($accountInfo)) {


                $iAccountId = AccountInfo::getInstance()->insertAccountInfoBase($sName, $sEmail, $sPicture, $sUserName, $sTeamName
                    , $sFirstName, $sLastName);
                echo $iAccountId.'<br/>';
                $accountInfo = AccountInfo::getInstance()->getAccountInfoByUserName($sUserName);
                if ($iAccountId) {
                    $accountInfo['team_id'] = $group['group_id'];
                    $accountInfo['team_name'] = $group['group_name'];
                    $accountInfo['position'] = $position['general_id'];
                    $accountInfo['start_date'] = $sStartDate;
                    AccountInfo::getInstance()->updateAccountInfo($accountInfo);
                    $groupMember = array('account_id' => $iAccountId, 'group_id' => $group['group_id'], 'level' => GroupMember::$staff);
                    GroupMember::getInstance()->addGroupMember($groupMember);

                    //add group all gianty
                    $groupMember = array('account_id' => $iAccountId, 'group_id' => $groupAllGianty['group_id'], 'level' => GroupMember::$staff);
                    GroupMember::getInstance()->addGroupMember($groupMember);

                }
            }

        }

        die;
    }

    public function delUserTestAction()
    {
//        AccountInfo::getInstance()->get
    }

    public function searchUserTagInputAction()
    {
        $this->_helper->layout()->disableLayout();
        $arrAccount = array();
        if ($this->_request->isPost()) {

            // parse params to Json
            $key = $this->_getParam('key','');
            if(trim($key) != '') {
                $accountsInfoEmail = AccountInfo::getInstance()->getAccountInfoListByLikeEmail($key, 0, ADMIN_PAGE_SIZE);
                $accountsInfoName = AccountInfo::getInstance()->getAccountInfoByLikeName($key, 0, ADMIN_PAGE_SIZE);
                $arrId = array();

                // set data for Account Info
                foreach ($accountsInfoEmail['data'] as $accountInfo) {
                    $arrId []= $accountInfo['account_id'];
                    $img = Core_Common::avatarProcess($accountInfo['picture']);
                    $arrAccount []= array('account_id' => $accountInfo['account_id'], 'name' =>$accountInfo['name'], 'email' => $accountInfo['email'], 'image_tag' =>$img);
                }

                foreach ($accountsInfoName as $accountInfo) {
                    if(!in_array($accountInfo['account_id'],$arrId)) {

                        $arrId []= $accountInfo['account_id'];
                        $img = Core_Common::avatarProcess($accountInfo['picture']);
                        $arrAccount [] = array('account_id' => $accountInfo['account_id'], 'name' => $accountInfo['name'], 'email' => $accountInfo['email'], 'image_tag' => $img);
                    }
                }
            }

        }

        // return to view with Json type
        echo Zend_Json::encode($arrAccount);
        exit();
    }

    public function addSpecialUserAction()
    {
//        AccountInfo::getInstance()->insertAccountInfoBase('Hồ Tùng Lâm', 'lam@gnt.co.jp' , '', 'lam', '');
//        AccountInfo::getInstance()->insertAccountInfoBase('Kondo Nobuyasu', 'kondo@gnt-global.com' , 'default-avatar.png', 'nkondo', 'BOM Global Group', 'Nobuyasu','Kondo');
//        AccountInfo::getInstance()->insertAccountInfoBase('Shingo Honda', '' , 'default-avatar.png', 'shonda', 'BOM Global Group', 'Honda', 'Shingo');
//        AccountInfo::getInstance()->insertAccountInfoBase('Shimojima Gosuke', 'shimojima@gnt-global.com' , 'default-avatar.png', 'gshimojima', 'BOM Global Group', 'Gosuke', 'Shimojima');
//        AccountInfo::getInstance()->insertAccountInfoBase('honda', 'honda@gnt-global.com' , 'default-avatar.png', 'honda', 'BOM Global Group', 'honda', '');
        AccountInfo::getInstance()->insertAccountInfoBase('Kimura Guen', 'kimura.guen@gnt-global.com' , 'default-avatar.png', 'gkimura', '', 'Kimura', 'Guen');
    }

    public function exporttoexcelAction()
    {
        $data = AccountInfo::getInstance()->getAccountInfoList();
        $file = 'static_backend/file/user.xls';
        $Reader = PHPExcel_IOFactory::createReaderForFile($file);
        $objXLS = $Reader->load($file);

        $sheet = $objXLS->getSheet(0);

        $range = array();
        $letter = 'A';
        while ($letter !== 'AA') {
            $range[] = $letter++;
        }

        $row = 2;


        $column = reset($range);

        foreach($data['data'] as $item) {
            $item = Core_Common::accountProcess($item);

            $sheet->setCellValue("{$column}{$row}", $item['id']);
            $column = next($range);

            $sheet->setCellValue("{$column}{$row}", $item['first_name']);

            $column = next($range);

            $sheet->setCellValue("{$column}{$row}", $item['last_name']);
            $column = next($range);

            $sheet->setCellValue("{$column}{$row}", $item['email']);
            $objXLS->getActiveSheet()->getColumnDimensionByColumn(3)->setWidth(50);
            $column = next($range);


            $objDrawing = new PHPExcel_Worksheet_Drawing();
            $objDrawing->setName('Sample image');
            $objDrawing->setDescription('Sample image');

            $objDrawing->setPath($item['image_dir']);

            $objDrawing->setHeight(50);
            $objDrawing->setWidth(50);
            $objDrawing->setResizeProportional(true);
            $objDrawing->setCoordinates("{$column}{$row}");
            $objDrawing->setWorksheet($objXLS->getActiveSheet());

            $objDrawing->setOffsetX(2);
            $objDrawing->setOffsetY(2);
//            $sheet->setCellValue("{$column}{$row}",  $item['image_dir']);
            $column = next($range);

            $sheet->setCellValue("{$column}{$row}", $item['team_name']);
            $column = next($range);

            $sheet->setCellValue("{$column}{$row}", $item['position_name']);
            $column = next($range);

            $sheet->setCellValue("{$column}{$row}", $item['address']);
            ///////////////////////////

            // auto-size

            $objXLS->getActiveSheet()->getRowDimension($row)->setRowHeight(40);

//            $objXLS->getActiveSheet()->getRowDimension($row)->setRowWidth(-1);
            $row++;
            $column = reset($range);

        }

        ////////////////////
        // Download file

        $output_file_name = "list-user.xlsx";

        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename='$output_file_name'");

        $objWriter = PHPExcel_IOFactory::createWriter($objXLS, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }


    public function importUserAction()
    {

        global $globalConfig;
        $level = $globalConfig['level'];
        $level = array_flip($level);

//        $accountsInfo = AccountInfo::getInstance()->getAccountInfoList('','',1002,0,'',0,0,0,0,0,1);
//        $accountsInfo = $accountsInfo['data'][0];
//        $accountsInfo['email'] = 'test';
//        AccountInfo::getInstance()->updateAccountInfo($accountsInfo);
//        $accountsInfo = AccountInfo::getInstance()->getAccountInfoList('','',1002,0,'',0,0,0,0,0,1);
//        Core_Common::var_dump($accountsInfo);
//        $groups = Core_Common::selectRedis(0,MAX_QUERY_LIMIT,REDIS_GROUP_ALL_LIST,'');
//        foreach($groups as $group)
//        {
//            if(Group::getInstance()->getGroupByID($group))
//                echo $group.'<br/>';
//            else
//                Core_Common::deleteRedis(REDIS_GROUP_ALL_LIST,'');
//        }
//        Core_Common::var_dump($groups);
        $file = 'static_backend/import-user/Gianty_List_of_Staff_01_2016(1).xlsx';
        $Reader = PHPExcel_IOFactory::createReaderForFile($file);

        $objXLS = $Reader->load($file);

        $sheet = $objXLS->getSheet(0);
        $highestRow         = $sheet->getHighestRow();
        $highestColumn      = $sheet->getHighestColumn(); // e.g 'F'

        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

        $generalType = 1; //is position
        $generals = General::getInstance()->getGeneralList('', $generalType, 11, 0, MAX_QUERY_LIMIT);

        foreach($generals['data'] as  $generalDel)
        {
            General::getInstance()->removeGeneral($generalDel['general_id']);
        }
//        $this->deleteDesignations();
//        General::getInstance()->

        // get IMAGES
        foreach ($sheet->getDrawingCollection() as $drawing) {
            //for XLSX format
            $string = $drawing->getCoordinates();
            $coordinate = PHPExcel_Cell::coordinateFromString($string);
//

            if ($drawing instanceof PHPExcel_Worksheet_Drawing || $drawing instanceof  PHPExcel_Worksheet_MemoryDrawing){
//                Core_Common::var_dump( $drawing->getWorksheet()->get($coordinate[0].''.$coordinate[1]));
                $filename = $drawing->getPath();
                $coordinate = $coordinate[0].''.$coordinate[1];

//                if(intval($coordinate[1]) == 177)
//                {
//                    die('s');
////                    Core_Common::var_dump($drawing);
//                }
//                $drawing->setRotation($drawing->getRotation());
                $avatar = uniqid('_').'.'.$drawing->getExtension();
                copy($filename, PATH_AVATAR_UPLOAD_DIR . '/' . $avatar);

                $source = $this->LoadJpeg(PATH_AVATAR_URL. '/' . $avatar);

                // delete tmp avatar
                Core_Common::deleteFile($avatar,FOLDER_AVATARS);

                // set rotate for image
                $iRotate = $drawing->getRotation();
                $iRotate = ($iRotate > 0) ? -(9 * $iRotate) : 0;
                $rotate = imagerotate($source, $iRotate, 0);

                imagejpeg($rotate, PATH_AVATAR_UPLOAD_DIR . '/' . $avatar);
                imagedestroy($rotate);
                $drawing->getWorksheet()->setCellValue($coordinate, PATH_AVATAR_UPLOAD_DIR . '/' . $avatar);
//                echo $coordinate.' - '.PATH_AVATAR_UPLOAD_DIR . '/' . $avatar.'<br/>';

            }
        }
//        die;

        $users = array();
        for($row=0; $row <= $highestRow; $row++)
        {
            $columnData = array();
            for($column=0; $column < $highestColumnIndex; $column++)
            {
                $checkCell = $sheet->getCellByColumnAndRow(0, $row);
                $checkCellVal = $checkCell->getCalculatedValue();
                if(!is_numeric($checkCellVal))
                    break;

                $cell = $sheet->getCellByColumnAndRow($column, $row);
                $val = $cell->getCalculatedValue();
                $val = trim($val);

                $columnData[]= $val;

            }
            if(!empty($columnData))
                $users[]= $columnData;
        }


        foreach($users as $user)
        {


            $id = trim($user[0]);
            $userName = trim($user[1]);
            $name = trim($user[2]);
            $email = trim($user[3]);

            $generalName = trim($user[4]);
            $groupName = trim($user[5]);
            $birthday = trim($user[6]);
            $startDate = trim($user[7]);
            $skype =  trim($user[8]);
            $imageTmp = trim($user[9]);
            $imageTmp = (empty($imageTmp)) ? '' : $imageTmp;

            $level = Core_Common::getLevelUser($generalName);

            $nameTmp = explode(' ',$name);
            $firstName = trim($nameTmp[0]);
            $lastName = str_replace($firstName,'',$name);
            $lastName = trim($lastName);


            // check account
            if($id > 0)
            {
                $accountsInfo = AccountInfo::getInstance()->getAccountInfoList('','',$id,0,'',0,0,0,0,0,'','',0,1);
                $accountInfo = (empty($accountsInfo['data'])) ? array() : $accountsInfo['data'][0];
                if(empty($accountsInfo['data'])) {
                    $accountInfo = AccountInfo::getInstance()->getAccountInfoByEmail($email);
                    echo ' <br/>' . $id . '<br/>';
                }
//                if($id == 1898)
//                    Core_Common::var_dump($accountInfo);
            }
            else {
                if(!empty($skype))
                    $accountInfo = AccountInfo::getInstance()->getAccountInfoBySkype($skype);
                else if(!empty($email))
                    $accountInfo = AccountInfo::getInstance()->getAccountInfoByEmail($email);


            }

            $accountInfo = ($accountInfo) ? $accountInfo : array();


            // check group
            $groupName = strtolower($groupName);
            $group = Group::getInstance()->selectByName($groupName);

            $aLam = AccountInfo::getInstance()->getAccountInfoByUserName('lam');
            $aLamId = ($aLam) ? $aLam['account_id'] : 0;

            $pbNguyen = AccountInfo::getInstance()->getAccountInfoByUserName('nguyen');
            $pbNguyenId = ($pbNguyen) ? $pbNguyen['account_id'] : 0;

            $naBang = AccountInfo::getInstance()->getAccountInfoByUserName('bang.na');
            $naBangId = ($naBang) ? $naBang['account_id'] : 0;

            $thNam = AccountInfo::getInstance()->getAccountInfoByUserName('nam.th');
            $thNamId = ($thNam) ? $thNam['account_id'] : 0;

            $nqtBill = AccountInfo::getInstance()->getAccountInfoByUserName('bill');
            $nqtBillId = ($nqtBill) ? $nqtBill['account_id'] : 0;

            $tttThao = AccountInfo::getInstance()->getAccountInfoByUserName('thao.ttt');
            $tttThaoId = ($tttThao) ? $tttThao['account_id'] : 0;

            $ltnChau = AccountInfo::getInstance()->getAccountInfoByUserName('chau.ln');
            $ltnChauId = ($ltnChau) ? $ltnChau['account_id'] : 0;

            $arrManager = array(
                'bod' => $aLamId,
                'manager' => $aLamId,
                'operation' => $aLamId,
                'it' => $nqtBillId,
                'cio office' => $nqtBillId,
                'monitoring' => $nqtBillId,
                'qc' => $naBangId,
                'qa' => $nqtBillId,
                'hr' => $tttThaoId,
                'fn' => $aLamId,
                'design' => $thNamId,
                'game' =>$naBangId,
                'madzone'=>$pbNguyenId,
                'stixchat'=>$aLam
            );


            if($groupName != 'NGHỉ THAI SảN' && strtolower($groupName) && 'nghỉ thai sản' && strtolower($groupName) != 'nghi thai san')  {
                if (empty($group)) {
                    echo $groupName.'<br/>';
                    $group['group_name'] = $groupName;
                    $group['account_id'] = (isset($arrManager[$groupName])) ? $arrManager[$groupName] : 0;
                    $group['active'] = 1;
                    $group['group_type'] = 2;
                    $group['sort_order'] = 0;
                    $group['country_id'] = 1;
                    $group['is_public'] = 1;
                    $group['admin_id'] = (isset($arrManager[$groupName])) ? $arrManager[$groupName] : 0;
                    $group['manager_id'] = (isset($arrManager[$groupName])) ? $arrManager[$groupName] : 0;
                    $group['is_bom'] = 0;
                    $group['image_url'] = GroupAvatar;
                    $group['content'] = '';

                    $groupID = Group::getInstance()->addGroup($group);
                    $group = Group::getInstance()->getGroupByID($groupID);
                } else {
                    if($groupName == 'MANAGER')
                        die('s');
                    $group['admin_id'] = (isset($arrManager[$groupName])) ? $arrManager[$groupName] : 0;
                    $group['manager_id'] = (isset($arrManager[$groupName])) ? $arrManager[$groupName] : 0;
                    Group::getInstance()->updateGroup($group);
                }
            }
            // check general
            $general = array();
            if(!empty($generalName)) {
                $generalName = Core_Common::matchLeaderAndSubLeader($generalName);
                $general = General::getInstance()->selectByNameAndType($generalName, 0);
                if (empty($general)) {
                    $generalID = General::getInstance()->insertGeneral($generalName, 1, 0, 1);
                    $general = General::getInstance()->getGeneralByID($generalID);
                }
            }


            $avatar = (empty($imageTmp)) ? AvatarDefault : $id.'_'.$userName . '.jpeg';

            if(!empty($imageTmp)) {
                Core_Common::deleteFile($avatar,PATH_AVATAR_UPLOAD_DIR);
                copy($imageTmp, PATH_AVATAR_UPLOAD_DIR . '/' . $avatar);
            }

//            if($userName ==  'tien.ns')
//            {
//                echo 'user name: '.$accountInfo['username'].'<br/>';
//                Core_Common::var_dump($group);
//            }

            if(empty($accountInfo)) {

                $accountInfo['id'] = $id;
                $accountInfo['name'] = $name;
                $accountInfo['email'] = $email;
                $accountInfo['phone'] = '';
                $accountInfo['birthday'] = Core_Common::convertStringToYMD($birthday, '/');

                $accountInfo['picture'] = $avatar;
                $accountInfo['avatar'] = $avatar;
                $accountInfo['identity'] = '';
                $accountInfo['tax_code'] = '';
                $accountInfo['address'] = '';
                $accountInfo['position'] = (isset($general['general_id'])) ? $general['general_id'] : 0;
                $accountInfo['department_id'] = (isset($group['group_id'])) ? $group['group_id'] : 0;
                $accountInfo['team_id'] = (isset($group['group_id'])) ? $group['group_id'] : 0;
                $accountInfo['leader_id'] = 0;
                $accountInfo['manager_id'] = (isset($group['manager_id'])) ? $group['manager_id'] : 0;
                $accountInfo['direct_manager'] = 0;
                $accountInfo['skype_account'] = $skype;
                $accountInfo['mobion_account'] = "";
                $accountInfo['start_date'] = Core_Common::convertStringToYMD($startDate, '/');
                $accountInfo['end_date'] = '0000-00-00';
                $accountInfo['contract_type'] = 0;
                $accountInfo['contract_sign_date'] = '';
                $accountInfo['country_id'] = 0;
                $accountInfo['description'] = '';
                $accountInfo['status'] = 1;
                $accountInfo['active'] = 1;
                $accountInfo['username'] = $userName;
                $accountInfo['team_name'] = $groupName;
                $accountInfo['manager_type'] = 0;
                $accountInfo['first_name'] = $firstName;
                $accountInfo['last_name'] = $lastName;

                $accountId = AccountInfo::getInstance()->insertAccountInfo($accountInfo);

                if($accountId > 0 && !empty($group)) {
                    $arrMemberData['account_id'] = $accountId;
                    $arrMemberData['group_id'] = $group['group_id'];
                    $arrMemberData['level'] = $level;
                    $arrMemberData['create_date'] = time();
                    GroupMember::getInstance()->addGroupMember($arrMemberData);
                }
//                Search::getInstance()->get()

            }
            else if(isset($accountInfo['account_id']))
            {
                $accountInfo['position'] = (isset($general['general_id'])) ? $general['general_id'] : 0;
                $accountInfo['department_id'] = (isset($group['group_id'])) ? $group['group_id'] : 0;
                $accountInfo['team_id'] = (isset($group['group_id'])) ? $group['group_id'] : 0;
                $accountInfo['team_name'] = (isset($group['group_name'])) ? $group['group_name'] : '';
                $accountInfo['manager_id'] = (isset($group['manager_id'])) ? $group['manager_id'] : 0;
                $accountInfo['start_date'] = Core_Common::convertStringToYMD($startDate, '/');
                $accountInfo['username'] = $userName;
                $accountInfo['first_name'] = $firstName;
                $accountInfo['last_name'] = $lastName;
                $accountInfo['email'] = $email;
                $accountInfo['picture'] = $avatar;
                $accountInfo['avatar'] = $avatar;
                $accountInfo['id'] = $id;



                if(!isset($accountInfo['account_id']))
                    Core_Common::var_dump($accountInfo);

                $accountId = $accountInfo['account_id'];
                AccountInfo::getInstance()->updateAccountInfo($accountInfo);

                if($accountId > 0  && !empty($group)) {

                    $groupMember = GroupMember::getInstance()->getGroupMemberByAccountAndGroupId($accountId, $group['group_id']);
                    if($accountId == $group['manager_id'])
                        $level = GroupMember::$manager;
                    else if($accountId == $group['admin_id'])
                        $level = GroupMember::$admin;

                    if(empty($groupMember)) {
                        // add group member
                        echo ' add id: '.  $accountInfo['id']. ' -- username: '.$accountInfo['username'].' -- teamName: '.$group['group_name'].' -- position: '.$generalName. ' -- level '.$level.'<br/>';
                        $arrMemberData['account_id'] = $accountId;
                        $arrMemberData['group_id'] = $group['group_id'];
                        $arrMemberData['level'] = $level;
                        $arrMemberData['create_date'] = time();
                        GroupMember::getInstance()->addGroupMember($arrMemberData);
                    }

                }

            }
        }

        die('success');
        Core_Common::var_dump($users);
    }

    /**
     * get list user
     * return users json
     */
    public function lstuserAction()
    {
        $this->_helper->layout()->disableLayout();

        $draw =  $this->_getParam('draw',0);
        $limit  = $this->_getParam('length',ADMIN_PAGE_SIZE);
        $offset = $this->_getParam('start',0);

        $queryString = Core_Common::getQueryString();
        $search =  $queryString['search'];

        $key    = isset($search['value']) ? $search['value'] : '';

        $result = array();

        // get accounts from DB
        $accountsInfo = AccountInfo::getInstance()->getAccountInfoList($key,'', 0,0, '', 0,0,0, 0,0,'create_date','DESC', $offset, $limit);

        // parse account to json
        $arrAccount = array();
        if($accountsInfo)
        {
//            $accountsInfo['data'] = Core_Common::array_sort($accountsInfo['data'],'id');
            foreach ($accountsInfo['data'] as $accountInfo) {
                // process account
                $account = Core_Common::accountProcess($accountInfo);
                $avatar = '<img class="img-thumbnail img-responsive" src="' . $account['image_tag'] . '" width="45"/>';
                $actions   =  ' <a href="javascript:void(0);" data-action="account-delete" data-value="'.$accountInfo['account_id'].'"><i class="fa fa-trash-o"></i></a> ';
                $sActive = ($accountInfo['active'] == 1 ) ? 'YES' : 'NO';
                $accountName = '<a href="' . BASE_ADMIN_URL . '/user/summary?account_id=' . $account['account_id'] . '">' . $account['name'] . '</a>';
                $account['first_name'] = '<a href="' . BASE_ADMIN_URL . '/user/summary?account_id=' . $account['account_id'] . '">' . $account['first_name'] . '</a>';
                $createAt = date('d-m-Y H:i:s',$accountInfo['create_date']);
                $arrAccount [] = array('id' => $account['id'], 'firstName' => $account['first_name'], 'lastName' => $account['last_name'], 'name' => $accountName,
                    'email' => $account['email'], 'avatar' => $avatar, 'team' => $account['team_name'],
                    'position' => $account['position_name'], 'actions' => $actions, 'sActive' => $sActive,'createAt'=>$createAt);
            }
            $result = array('draw'=>$draw, 'recordsFiltered'=>$accountsInfo['total'],'recordsTotal'=>$accountsInfo['total'],'data'=>$arrAccount);
        }
        else
            $result = array('draw'=>$draw, 'recordsFiltered'=>0,'recordsTotal'=>0,'data'=>array());

        echo  Zend_Json::encode($result);
        exit();
    }

    public function summaryAction()
    {

        $accountId = $this->_getParam('account_id',0);
//        //get account info
        $accountInfo = AccountInfo::getInstance()->getAccountInfoByAccountID($accountId);
        if(!$accountInfo)
        {
            $this->_redirect(BASE_ADMIN_URL.'/user');
            exit();
        }

        // get achievements
        $achievements = Achievement::getInstance()->select('',$accountId,0,MAX_QUERY_LIMIT);
//
//        // get Project Memeber
        $projectMember = ProjectMember::getInstance()->getProjectMemberByAccountID($accountId);

        // get group team
        // 2 : team's Gianty
        $groupsTeam = Group::getInstance()->getGroupListAll(1,2);

        // return must have accounts
        $accountsInfo = AccountInfo::getInstance()->getAccountInfoList('','',0,0,'',0,0,0, 0,0,'','', rand(1,200),10);
        while(empty($accountsInfo['data']))
        {
            $accountsInfo = AccountInfo::getInstance()->getAccountInfoList('','',0,0,'',0,0,0, 0,0,'','', rand(1,100),10);
        }

        // return account info
        $this->view->accountInfo    = Core_Common::accountProcess($accountInfo);
//        // return achievements
        $this->view->achievements   = $achievements;
//        // return project member
        $this->view->projectMember  = $projectMember;
        //return groups team
        $this->view->groupsTeam     = $groupsTeam;
        //return accounts search
        $this->view->accountsSearch = $accountsInfo;
    }

    public function generalAction()
    {
        global $globalConfig;
        $accountId = $this->_getParam('account_id',0);
        $levelConfig = $globalConfig['level'];
//        //get account info
        $accountInfo = AccountInfo::getInstance()->getAccountInfoByAccountID($accountId);
        if(!$accountInfo)
        {
            $this->_redirect(BASE_ADMIN_URL.'/user');
            exit();
        }
        
        $sMessage = '';
        if($this->_request->isPost()) {
            $arrFeed = array();
            $params = $this->_request->getPost();
           
            //Init data
            $accountInfo['last_name'] = !empty($params['last_name']) ? $params['last_name'] : '';
            $accountInfo['first_name'] = !empty($params['first_name']) ? $params['first_name'] : ' ';
            $accountInfo['name'] =  $accountInfo['first_name'].' '.$accountInfo['last_name'];
            $accountInfo['id'] = !empty($params['id']) ? $params['id'] : 0;
            $accountInfo['team_id'] = !empty($params['team_id']) ? $params['team_id'] : 0;
            $accountInfo['position'] = !empty($params['position']) ? $params['position'] : 0;
            $accountInfo['email'] = !empty($params['email']) ? $params['email'] : ' ';
            $accountInfo['skype_account'] = !empty($params['skype_account']) ? $params['skype_account'] : ' ';
            $accountInfo['birthday'] = Core_Common::convertStringToYMD($params['birthday']);
            $accountInfo['phone'] = !empty($params['phone']) ? $params['phone'] : ' ';
            $accountInfo['contact_address'] = !empty($params['contact_address']) ? $params['contact_address'] : ' ';
            $accountInfo['personal_email'] = !empty($params['personal_email']) ? $params['personal_email'] : ' ';
            $accountInfo['picture'] = !empty($params['picture']) ? $params['picture'] : '';
            $iLevel = isset($levelConfig[$params['level']]) ? $params['level'] : GroupMember::$staff;
            $accountInfo['avatar'] = $accountInfo['picture'];
            //validate
            if(!Core_Validate::sanityCheck($params['last_name'], 'string', 100)){
            	$sMessage = 'Family name invalid (empty or length > 100 characters)';
            }
            
            if(!Core_Validate::sanityCheck($params['last_name'], 'string', 100)){
            	$sMessage = 'Name invalid (empty or length > 100 characters)';
            }
            
            if(empty($sMessage)){

                $groupMember = GroupMember::getInstance()->getGroupMemberByAccountAndGroupId($accountId, $accountInfo['team_id']);
                if(!empty($groupMember)){
                    $groupMember['level'] = $iLevel;
                    GroupMember::getInstance()->updateGroupMember($groupMember);
                }else{
                    $groupMember = array(
                        'group_id' => $accountInfo['team_id'],
                        'level' =>  $iLevel,
                        'account_id' =>  $accountId,
                    );
                    GroupMember::getInstance()->addGroupMember($groupMember);
                }

                $group = Group::getInstance()->getGroupByID($accountInfo['team_id']);
                if(!empty($group)){
                    $accountInfo['team_name'] = $group['group_name'];

                }

	            AccountInfo::getInstance()->updateAccountInfo($accountInfo);
                ActionLog::getInstance()->insert($this->arrLogin['id'],ActionLog::$update,ActionLog::$user,$this->arrLogin['accountID'],$this->arrLogin['nickName'],$accountInfo['name']."'s  general");
	            $this->_redirect(BASE_ADMIN_URL.'/user/summary?account_id='.$accountId);
	            exit();
            }
        }
            // get group team
        // 2 : team's Gianty
        $groupsTeam = Group::getInstance()->getGroupListAll(1,2);

        // return must have accounts
        $accountsInfo = AccountInfo::getInstance()->getAccountInfoList('','',0,0,'',0,0,0, 0,0,'','', rand(1,200),10);
        while(empty($accountsInfo['data']))
        {
            $accountsInfo = AccountInfo::getInstance()->getAccountInfoList('','',0,0,'',0,0,0, 0,0,'','', rand(1,50),10);
        }

        $positions = General::getInstance()->getGeneralList('',General::$position,1,0,MAX_QUERY_LIMIT);
        $positions['data'] = Core_Common::array_sort($positions['data'],'name');

        $this->view->levels = $levelConfig;
        $this->view->message = $sMessage;
        // return account info
        $this->view->accountInfo  = Core_Common::accountProcess($accountInfo);
        //return groups team
        $this->view->groupsTeam     = $groupsTeam;
        //return accounts search
        $this->view->accountsSearch = $accountsInfo;
        // return teams
//        $this->view->positions      = General::getInstance()->getGeneralAttHash(General::$position, 1, 0, MAX_QUERY_LIMIT);
        $this->view->positions      = $positions;

    }

    // return Json
    public function searchuserAction()
    {
        $this->_helper->layout()->disableLayout();
        $arrAccount = array('total' => 0, 'data' => '');
        if ($this->_request->isPost()) {
            $params = $this->_request->getPost();
            if (!empty($params['data'])) {

                // parse params to Json
                $arrData = json_decode($params['data'], true);

                // set param value
//                $key = $arrData['key'];
                $teamId = isset($arrData['team']) ? $arrData['team'] : 0;
                $sEmail = isset($arrData['email']) ? $arrData['email'] : '';
                $key = isset($arrData['key']) ? $arrData['key'] : '';

                // search accounts info
                if(empty($sEmail)) {
                    $accountsInfo = AccountInfo::getInstance()->getAccountInfoList($key, '', 0, 0, '', 0, 0, intval($teamId), 0, 0,'','',0, ADMIN_PAGE_SIZE);
//                    var_dump($key, '', 0, 0, '', 0, '', intval($teamId), 0, 0, ADMIN_PAGE_SIZE);
                }
                else
                    $accountsInfo = AccountInfo::getInstance()->getAccountInfoListByLikeEmail($sEmail, 0, ADMIN_PAGE_SIZE);

//                var_dump($accountsInfo);
                // set total for arrAccount
//                Core_Common::var_dump($accountsInfo);
                $arrAccount['total'] = $accountsInfo['total'];
                if(empty($accountsInfo['data'])){
                    $arrAccount['data'] = array();
                }else {
                    // set data for Account Info
                    foreach ($accountsInfo['data'] as $accountInfo) {
                        $arrAccount['data'] [] = Core_Common::accountProcess($accountInfo);
                    }
                }
            }
        }

        // return to view with Json type
        echo Zend_Json::encode($arrAccount);
        exit();
    }
    
    public function personalAction()
    {
    	global $globalConfig;
    	$accountId = $this->_getParam('account_id',0);
    	
    	$accountInfo = AccountInfo::getInstance()->getAccountInfoByAccountID($accountId);

    	if(!$accountInfo){
    		$this->_redirect(BASE_ADMIN_URL.'/user');
    		exit(); 
    	}
    	
    	//post
    	if($this->_request->isPost()) {
    		$params = $this->_request->getPost(); 
    		    		
    		$accountInfo['account_id'] = $params['account_id'];
    		$accountInfo['gender'] = Core_Common::convertStringEmptyToZero($params['gender']);
    		$accountInfo['place_of_birth'] =Core_Common::convertStringEmptyToZero($params['place_of_birth']);
    		$accountInfo['home_town'] = Core_Common::convertStringEmptyToZero($params['home_town']);
    		$accountInfo['address'] = $params['address'];
    		$accountInfo['identity'] = $params['identity'];
    		$accountInfo['identity_date'] = Core_Common::convertStringToYMD($params['identity_date']);
    		$accountInfo['identity_place'] = Core_Common::convertStringEmptyToZero($params['identity_place']);
    		$accountInfo['social_insurance'] = $params['social_insurance'];
    		$accountInfo['tax_code'] = $params['tax_code'];
    		$accountInfo['bank_account'] = $params['bank_account'];
    		$accountInfo['bank_account_id'] = Core_Common::convertStringEmptyToZero($params['bank_account_id']);
    		$accountInfo['bank_account_branch'] = Core_Common::convertStringEmptyToZero($params['bank_account_branch']);
    		$accountInfo['marital_status'] = Core_Common::convertStringEmptyToZero($params['marital_status']);
    		$accountInfo['no_of_children'] = Core_Common::convertStringEmptyToZero($params['no_of_children']);
    		$accountInfo['contact_name'] = $params['contact_name'];
    		$accountInfo['contact_relationship'] = $params['contact_relationship'];
    		$accountInfo['contact_address'] = $params['contact_address'];
    		
    		$accountInfo['passport'] = $params['passport'];
    		$accountInfo['passport_date'] = Core_Common::convertStringToYMD($params['passport_date']);
    		$accountInfo['passport_place'] = Core_Common::convertStringEmptyToZero($params['passport_place']);

            ActionLog::getInstance()->insert($this->arrLogin['id'],ActionLog::$update,ActionLog::$user,$this->arrLogin['accountID'],$this->arrLogin['nickName'],$accountInfo['name']."'s  personal");
    		AccountInfo::getInstance()->updateAccountInfo($accountInfo);
    		
    		$this->_redirect(BASE_ADMIN_URL.'/user/summary?account_id='.$params['account_id']);
    		exit();
    	}
    	
    	// return must have accounts
    	$accountsInfo = AccountInfo::getInstance()->getAccountInfoList('','',0,0,'',0,0,0, 0, 0,'','',rand(1, 200),10);
    	while(empty($accountsInfo['data']))
    	{
    		$accountsInfo = AccountInfo::getInstance()->getAccountInfoList('','',0,0,'',0,0,0, 0,0,'','', rand(1,100),10);
    	}
    
    	
    	
    	// return account info
    	$this->view->arrAccountInfo  = Core_Common::accountProcess($accountInfo);
    	
    	//return accounts search
    	$this->view->accountsSearch = $accountsInfo;
    	
    	$this->view->arrProvince = $globalConfig['province'];
    	$this->view->arrBank = $globalConfig['bank_account_id'];
    	$this->view->arrGender = $globalConfig['gender'];
    	$this->view->arrMarital = $globalConfig['marital'];
    }

    public function jobuserAction()
    {
        global $globalConfig;
        $accountId = $this->_getParam('account_id',0);

        $accountInfo = AccountInfo::getInstance()->getAccountInfoByAccountID($accountId);
         $bHopDongDangThuViec =  Absence::getInstance()->checkHopDongDangThuViec($accountInfo);

//        var_dump($accountInfo);die;
        if(empty($accountInfo)){
            $this->_redirect(BASE_ADMIN_URL.'/user');
            exit();
        }

        $oldContractTypeId = $accountInfo['contract_type'];
//        echo   $accountInfo['contract_type'];die;
        // post
//        if($this->_request->isPost()) {
//            $params = $this->_request->getPost();
//            $contractType = $this->_getParam('contract_type', 0);
//            $checkAccountInfo = $accountInfo; // Dung cho phan kiem tra $checkHopDongChinhThuc
//            $checkAccountInfo['contract_type'] = $contractType;
//
//            $checkHopDongMoiLaChinhThuc = Absence::getInstance()->checkUpdateHopDongChinhThuc($checkAccountInfo,'now',true,true);
//            // add 2 day absence for user from probation to one year
//
//            $staringDate = $this->_getParam('start_date', '');
//            $accountInfo['start_date'] = empty($staringDate) ? $accountInfo['start_date'] : Core_Common::convertStringToYMD($staringDate);
//            $iEndProbation = $this->_getParam('end_probation', 0);
//
//
//            $accountInfo['start_date']      =  Core_Common::convertStringToYMD($params['start_date']);
//            $accountInfo['end_probation']  = $iEndProbation;
//
//
//            $accountInfo['contract_type']   = $contractType;
//            AccountInfo::getInstance()->updateAccountInfo($accountInfo);
//            if ($bHopDongDangThuViec  && $checkHopDongMoiLaChinhThuc) {
//
////                echo $accountInfo['contract_type'];die;
//                Absence::getInstance()->addAbsenceDay($accountInfo);
//
//            }else {//end  if(!empty($oldContractType))
//
//                if(Absence::getInstance()->checkHopDongPartTime($accountInfo['contract_type']) && $accountInfo['start_date'] != '0000-00-00' && $accountInfo['contract_type'] > 0){
//                    Absence::getInstance()->addAbsenceDay($accountInfo);
////                    $dDate = new DateTime($accountInfo['start_date']);
////                    $dDateTo = new DateTime($accountInfo['start_date']);
////                    if($accountInfo['contract_type'] == 0){
////                        $accountInfo['contract_type'] = $contractType;
////                    }
////                    if($iEndProbation > 0){
////                        $dDateTo->modify('+'.$iEndProbation.' month');
////                    }
////                    StatisticAbsenceHistory::getInstance()->insertFromToDate($dDate, $dDateTo, $accountInfo['account_id']);
////                    $contract = General::getInstance()->getGeneralByID($accountInfo['contract_type']);
////                    while($dDate <= $dDateTo){
////                        if($dDate == $dDateTo){
////                            $contract = General::getInstance()->getGeneralByID($contractType);
////                        }
////                        $statisticAbsences = StatisticAbsenceHistory::getInstance()->select('',$dDate->format('m'), $dDate->format('Y'), $accountInfo['account_id']);
////                        $statisticAbsence = $statisticAbsences['data'][0];
////                        $statisticAbsence['reset'] = true;
////                        $statisticAbsence['contract_type_name'] = $contract['name'];
////                        StatisticAbsenceHistory::getInstance()->update($statisticAbsence);
////                        $dDate->modify('+1 month');
////                    }
//                }
//                $accountInfo['contract_type']   = $contractType;
//                AccountInfo::getInstance()->updateAccountInfo($accountInfo);
//
//            }
//
//            ActionLog::getInstance()->insert($this->arrLogin['id'],ActionLog::$update,ActionLog::$user,$this->arrLogin['accountID'],$this->arrLogin['nickName'],$accountInfo['name']."'s  job");
//
//
//
//
//            $this->_redirect(BASE_ADMIN_URL.'/user/summary?account_id='.$accountInfo['account_id']);
//            exit();
//        }
            // return must have accounts
        $accountsInfo = AccountInfo::getInstance()->getAccountInfoList('','',0,0,'',0,0,0, 0, 0,'','',rand(1, 200),10);
        while(empty($accountsInfo['data']))
        {
            $accountsInfo = AccountInfo::getInstance()->getAccountInfoList('','',0,0,'',0,0,0, 0, 0,'','',rand(1,100),10);
        }

        // get Projects Member
        $projectsMember   = ProjectMember::getInstance()->getProjectMemberByAccountID($accountInfo['account_id']);
        // update is done
        ProjectMember::getInstance()->UpdateIsDone($projectsMember);
        //get Projects
        $projects         = Project::getInstance()->select('',0,MAX_QUERY_LIMIT);
        $arrProject       = array();

        $myGroupMembers = GroupMember::getInstance()->getGroupMemberByMemberId($accountId);

        foreach($projects['data'] as $project){
            $arrProject[$project['id']]   = $project;
        }
        // return account info
        $this->view->accountInfo     = Core_Common::accountProcess($accountInfo);

        //return accounts search
        $this->view->accountsSearch     = $accountsInfo;
        
        //get general 
        $arrGeneral = General::getInstance()->getGeneralAtt(0, 1, 0, MAX_QUERY_LIMIT);


        // return teams
        $this->view->positions          = $arrGeneral[General::$position];
        //return contract type
        $this->view->contractTypes      = $arrGeneral[General::$contract];

        $this->view->myContractTypes      = UserContract::getInstance()->select($accountId);

        // return levels
//        $this->view->levels             = $arrGeneral[General::$level];
        $this->view->levels             = $globalConfig['level'];
        // return projects
        $this->view->projects           = $projects;
        // return arrProject
        $this->view->arrProject         = $arrProject;
        // return projects member
        $this->view->projectsMember     = $projectsMember;

        $this->view->myGroupMembers             = $myGroupMembers;
        $this->view->bHopDongDangThuViec    = $bHopDongDangThuViec;

    }

    public function achievementAction()
    {
        $accountId = $this->_getParam('account_id',0);

        $accountInfo = AccountInfo::getInstance()->getAccountInfoByAccountID($accountId);

        if(!$accountInfo){
            $this->_redirect(BASE_ADMIN_URL.'/user');
            exit();
        }

        // return must have accounts
        $accountsInfo = AccountInfo::getInstance()->getAccountInfoList('','',0,0,'',0,0,0, 0, 0,'','',rand(1, 200),10);
        while(empty($accountsInfo['data']))
        {
            $accountsInfo = AccountInfo::getInstance()->getAccountInfoList('','',0,0,'',0,0,0, 0,0,'','', rand(1,100),10);
        }
        // get achievements
        $achievements = Achievement::getInstance()->select('',$accountId,0,MAX_QUERY_LIMIT);
        // return account info
        $this->view->accountInfo     = Core_Common::accountProcess($accountInfo);
        //return accounts search
        $this->view->accountsSearch     = $accountsInfo;
        //return achievements
        $this->view->achievements       = $achievements;
    }

    public function addachievementAction()
    {
        $this->_helper->layout()->disableLayout();
        if($this->_request->isPost()) {
            $params = $this->_request->getPost();
            $data   = json_decode($params['data'],true);
            $accountId = $data['account_id'];
            $accountInfo = AccountInfo::getInstance()->getAccountInfoByAccountID($accountId);

            if(!$accountInfo){
                echo Zend_Json::encode(array('result'=>false,'message'=>'User not found !'));
                exit();
            }

            $arrAchievement = array();
            $arrAchievement['account_id']   = $accountId;
            $arrAchievement['name'] = !empty($data['name']) ? $data['name'] : '';
            $dateTmp = Core_Common::getDate($data['date']);
            if($arrAchievement['name'] == '')
            {
                echo Zend_Json::encode(array('result'=>false,'message'=>'Achievement name is required !'));
                exit();
            }

            if(empty($dateTmp))
            {
                echo Zend_Json::encode(array('result'=>false,'message'=>'Date Time is invalid !'));
                exit();
            }
            else{
                $arrAchievement['year']    = $dateTmp['y'];
                $arrAchievement['month']    = $dateTmp['m'];
                $arrAchievement['day']    = $dateTmp['d'];

                $invalid    = Achievement::getInstance()->invalid($arrAchievement['year'],$arrAchievement['month'],$arrAchievement['day'],$accountId);
                if($invalid)
                {
                    echo Zend_Json::encode(array('result'=>false,'message'=>'Achievement already exits.'));
                }
                else{
                    ActionLog::getInstance()->insert($this->arrLogin['id'],ActionLog::$create,ActionLog::$user,$this->arrLogin['accountID'],$this->arrLogin['nickName'],$accountInfo['name']."'s  achievement");

                    $result = Achievement::getInstance()->insert($arrAchievement);
                    echo Zend_Json::encode(array('result'=>$result,'message'=>''));
                }
                exit();
            }
        }
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
            $accountIds = $arrParam['account_ids'];
            if(is_array($accountIds))
            {
                foreach($accountIds as $accountId)
                {
                    $accountInfo   = AccountInfo::getInstance()->getAccountInfoByAccountID($accountId);
                    if($accountInfo)
                    {
                        ActionLog::getInstance()->insert($this->arrLogin['id'],ActionLog::$delete,ActionLog::$user,$this->arrLogin['accountID'],$this->arrLogin['nickName'],'"'.$accountInfo['name'].'" user');
                        AccountInfo::getInstance()->removeAccountInfo($accountId);
                        AccountInfo::getInstance()->removeCache($accountId);
                        Search::getInstance()->delete($accountId);

                    }
                    else{
                        $error  = array('error' => true, 'message' => 'Account Not Found');
                    }
                }
            }

        }

        echo Zend_Json::encode($error);
        exit();
    }

    public function deleteachievementAction()
    {
        $this->_helper->layout()->disableLayout();
        if($this->_request->isPost())
        {
            $params = $this->_request->getPost();
            $AchievementId  = !empty($params['id']) ? $params['id'] : 0;
            $achievement    = Achievement::getInstance()->selectById($AchievementId);
            if(!empty($achievement))
            {
                $iAccountId     = $achievement['account_id'];
                // get account info
                $accountInfo    = AccountInfo::getInstance()->getAccountInfoByAccountID($iAccountId);

                ActionLog::getInstance()->insert($accountInfo['id'],ActionLog::$delete,ActionLog::$user,$accountInfo['account_id'],$accountInfo['name'],'user achievement');
                Achievement::getInstance()->deleteByID($AchievementId);
                echo Zend_Json::encode(array('result'=>true,'message'=>''));
                exit();
            }

        }
        echo Zend_Json::encode(array('result'=>false,'message'=>'Achievement not found'));
        exit();
    }
}