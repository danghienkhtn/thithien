<?php

/**
 * Created by PhpStorm.
 * User: thanh.lh
 * Date: 2016-04-07
 * Time: 10:58
 */
class  Backend_ImportController extends Core_Controller_ActionBackend
{
    private $arrLogin;

    function init()
    {
        parent::init();
        $this->arrLogin = $this->view->arrLogin;
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        if($this->arrLogin['id'] != 1836)
            die('permission is denied');
    }

    public function importContractsAction(){

        ini_set('memory_limit',-1);
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes

        $file = 'static_backend/import/contract-type/contract-type-for-users.xlsx';
        $Reader = PHPExcel_IOFactory::createReaderForFile($file);
        $Reader->setReadDataOnly(true);

        $objXLS = $Reader->load($file);
        $sheet = $objXLS->getSheet(0);

        $highestRow         = $sheet->getHighestRow();
        $highestColumn      = $sheet->getHighestColumn(); // e.g 'F'
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

        $probationContract = General::getInstance()->selectByNameAndType('Probation contract (Thỏa thuận thử việc)', General::$contract);
        $labourContract = General::getInstance()->selectByNameAndType('Labour contract (Hợp đồng lao động)', General::$contract);

        for($row=1; $row < $highestRow; $row++)
        {
            set_time_limit(20);

            $checkCell = $sheet->getCell('B'.$row);
            $iId = is_null($checkCell->getValue()) ? 0 : intval($checkCell->getValue());// userID

            if($iId == 0 || empty($iId)) {
                continue;
            }

            $accountInfo = AccountInfo::getInstance()->getAccountInfoList('','',$iId,0,'',0,0,0,0,0,'','',0,1);
            if(empty($accountInfo['data'])) {
                continue;
            }

            $columnData = array();
            $accountInfo = $accountInfo['data'][0];
            $iAccountId = $accountInfo['account_id'];

            for ($column = 0; $column < $highestColumnIndex; $column++) {

                $cell = $sheet->getCellByColumnAndRow($column, $row);
                $val = $cell->getCalculatedValue();
                $val = trim($val);

                $columnData[] = $val;
            }


            $sStartDate = $columnData[5];
            $sStartDate = trim($sStartDate);
            $sStartDate = Core_Common::convertStringToYMD($sStartDate,'/');

            $sEndProbation = $columnData[6];
            $sEndProbation = trim($sEndProbation);
            $sEndProbation = Core_Common::convertStringToYMD($sEndProbation,'/');



            if($sStartDate == '0000-00-00' || $sEndProbation == '0000-00-00' ){
                continue;
            }

            $dStartDate = new DateTime($sStartDate);
            $dEndProbation = new DateTime($sEndProbation);



            if($dStartDate == $dEndProbation){
                $userContract = array('date'=>$dStartDate->getTimestamp(), 'account_id'=>$iAccountId, 'general_id'=>$labourContract['general_id'], 'general_name'=>$labourContract['name']);
                UserContract::getInstance()->insert($userContract);
            }else{
                $userProbationContract = array('date'=>$dStartDate->getTimestamp(), 'account_id'=>$iAccountId, 'general_id'=>$probationContract['general_id'], 'general_name'=>$probationContract['name']);
                UserContract::getInstance()->insert($userProbationContract);

                $userLabourContract = array('date'=>$dEndProbation->getTimestamp(), 'account_id'=>$iAccountId, 'general_id'=>$labourContract['general_id'], 'general_name'=>$labourContract['name']);
                UserContract::getInstance()->insert($userLabourContract);
            }

        }

        die('!!!');

    }

    public function importAbsenceAction()
    {

        $dDate = new DateTime();
        $users = array();
        $arrData = array();
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes
        $file = 'static_backend/import/absence/TONG HOP CHAM CONG GIANTY 2016 - Copy 1h51pm 31032016_Nhu.xlsx';
        $Reader = PHPExcel_IOFactory::createReaderForFile($file);
        $Reader->setReadDataOnly(true);
        $objXLS = $Reader->load($file);
        $sheet = $objXLS->getSheet(0);
        $highestRow         = $sheet->getHighestRow();
        $highestColumn      = $sheet->getHighestColumn(); // e.g 'F'
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
        for($row=0; $row < $highestRow; $row++)
        {
            set_time_limit(20);
            $columnData = array();
            $checkCell = $sheet->getCell('B'.$row);
            $iId = is_null($checkCell->getValue()) ? 0 : intval($checkCell->getValue());// userID
            if($iId > 1) {
                $accountInfo = AccountInfo::getInstance()->getAccountInfoList('','',$iId,0,'',0,0,0,0,0,'','',0,1);
                if(!empty($accountInfo['data'])) {
                    $users[$iId] = $accountInfo['data'][0];
                    for ($column = 0; $column < $highestColumnIndex; $column++) {

                        $cell = $sheet->getCellByColumnAndRow($column, $row);
                        $val = $cell->getCalculatedValue();
                        $val = trim($val);

                        $columnData[] = $val;
                    }
                    if (!empty($columnData) && $iId > 0)
                        $arrData[$iId] = $columnData;
                }
            }
        }// get row value

        $month = 3;
        $year = 2016;
        foreach($arrData as $iId=>$data)
        {
            $accountInfo = $users[$iId];

            $noDayAbsence = $data[9]; // v?ng c� ph�p
            $noDayAbsence += $data[10]; // v?ng kh�ng  ph�p

            $noSpecialAbsence = $data[11]; // v?ng ??c bi?t
            $ot = $data[16]; // Over time
            $iSoPhepCo = $data[17]; // t?ng s? ng�y ph�p
//            $iSoPhepCo = ($iSoPhepCo > 0) ? $iSoPhepCo :0;
            $iRemain = $data[18]; // s? ng�y ph�p c�n l?i
            $minIndexDay = 19;
            $maxIndexDay = 80;
            $day = 0;

            $specialDayID = 0;
//            $specialDays = SpecialDay::getInstance()->select('ngh? thai s?n');
//            if(empty($specialDays['data']))
//                $specialDayID = SpecialDay::getInstance()->insert(array('name'=>'ngh? thai s?n','type'=>3,'date_from'=>date('Y-m-d'), 'date_to'=>date('Y-m-d'), 'no_date'=>1,'description'=>''));
//            else
//                $specialDayID = $specialDays['data'][0]['id'];
            $absenceAccount = AbsenceAccount::getInstance()->selectByAccountId( $accountInfo['account_id']);
            if(empty($absenceAccount))
                AbsenceAccount::getInstance()->add(array('account_id'=>$accountInfo['account_id'], 'total'=>$iSoPhepCo));

            StatisticAbsenceHistory::getInstance()->insertFromToDate($dDate,$dDate, $accountInfo['account_id']);
            $absenceHistories = StatisticAbsenceHistory::getInstance()->select('',3,2016, $accountInfo['account_id']);
            foreach($absenceHistories['data'] as $absenceHistory){
                $absenceHistory['total'] = $iSoPhepCo;
                $absenceHistory['remain'] = $iRemain;
                $absenceHistory['used'] = 0;
                StatisticAbsenceHistory::getInstance()->update($absenceHistory);
            }


//            else
//                AbsenceAccount::getInstance()->
            $dateType = 1; //full day
            $totalDay = 1;
            $specialDay = 0;
            $lastActionStats = 3;
            $manualAbsence = array(
                'owner' => $this->arrLogin['accountID'],
                'account_name' => $accountInfo['name'],
                'account_id' => $accountInfo['account_id'],
                'date' => '',
                'date_type' => '1', // full day
                'description' => 'admin create',
                'status' => 1
            );

            $apiAbsence = new ApiAbsence();
            for($minIndexDay; $minIndexDay < $maxIndexDay; $minIndexDay++)
            {
                $am = strtolower(trim($data[$minIndexDay]));
                $minIndexDay++;
                $pm = strtolower(trim($data[$minIndexDay]));

                if($minIndexDay%2 == 0)
                {
                    $day++;
                }

                try {
                    $currentDate = new DateTime($year.'-'.$month.'-'.$day);
                    // create absence request by api function
                    if ($am == 'v' && $pm == 'v') {

                        $dateType = Absence::$full; //full day
                        $totalDay = 1;
                        $specialDay = 0;
                        $lastActionStats = 3;
                        $usedDate = Core_Common::checkSpecialDay($currentDate->format('Y-m-d'), $accountInfo['account_id'], $dateType);
                        if (empty($usedDate['message'])) {
                            $apiAbsence->addRequest($accountInfo['account_id'],
                                array($currentDate->format('d-m-Y')),
                                array($currentDate->format('d-m-Y')),
                                array($dateType), $currentDate->format('d-m-Y'), $totalDay, $specialDay, $lastActionStats);
                        }


                    } else {
                        if ($am == 'v') {

                            $dateType = Absence::$am; //AM day
                            $totalDay = 0.5;
                            $specialDay = 0;
                            $lastActionStats = 3;
                            $usedDate = Core_Common::checkSpecialDay($currentDate->format('Y-m-d'), $accountInfo['account_id'], $dateType);
                            if (empty($usedDate['message'])) {
                                $apiAbsence->addRequest($accountInfo['account_id'],
                                    array($currentDate->format('d-m-Y')),
                                    array($currentDate->format('d-m-Y')),
                                    array($dateType), $currentDate->format('d-m-Y'), $totalDay, $specialDay, $lastActionStats, '');
                            }
                        } else if ($pm == 'v') {
                            $dateType = 3; //PM day
                            $totalDay = 0.5;
                            $specialDay = 0;
                            $lastActionStats = 3;
                            $usedDate = Core_Common::checkSpecialDay($currentDate->format('Y-m-d'), $accountInfo['account_id'], $dateType);
                            if (empty($usedDate['message'])) {
                                $apiAbsence->addRequest($accountInfo['account_id'],
                                    array($currentDate->format('d-m-Y')),
                                    array($currentDate->format('d-m-Y')),
                                    array($dateType), $currentDate->format('d-m-Y'), $totalDay, $specialDay, $lastActionStats, '');
                            }
                        }

                    }// end create absence request by api function

                    // vang ko phep
                    if ($am == 'vkp' && $pm == 'vkp') {

                        $dateType = Absence::$full; //full day
                        $totalDay = 1;
                        $specialDay = 0;
                        $lastActionStats = 3;
                        $usedDate = Core_Common::checkSpecialDay($currentDate->format('Y-m-d'), $accountInfo['account_id'], $dateType);
                        if (empty($usedDate['message'])) {
                            $apiAbsence->addRequest($accountInfo['account_id'],
                                array($currentDate->format('d-m-Y')),
                                array($currentDate->format('d-m-Y')),
                                array($dateType), $currentDate->format('d-m-Y'), $totalDay, $specialDay, $lastActionStats);
                        }


                    } else {
                        if ($am == 'vkp') {

                            $dateType = Absence::$am; //AM day
                            $totalDay = 0.5;
                            $specialDay = 0;
                            $lastActionStats = 3;
                            $usedDate = Core_Common::checkSpecialDay($currentDate->format('Y-m-d'), $accountInfo['account_id'], $dateType);
                            if (empty($usedDate['message'])) {
                                $apiAbsence->addRequest($accountInfo['account_id'],
                                    array($currentDate->format('d-m-Y')),
                                    array($currentDate->format('d-m-Y')),
                                    array($dateType), $currentDate->format('d-m-Y'), $totalDay, $specialDay, $lastActionStats, '');
                            }
                        } else if ($pm == 'vkp') {
                            $dateType = 3; //PM day
                            $totalDay = 0.5;
                            $specialDay = 0;
                            $lastActionStats = 3;
                            $usedDate = Core_Common::checkSpecialDay($currentDate->format('Y-m-d'), $accountInfo['account_id'], $dateType);
                            if (empty($usedDate['message'])) {
                                $apiAbsence->addRequest($accountInfo['account_id'],
                                    array($currentDate->format('d-m-Y')),
                                    array($currentDate->format('d-m-Y')),
                                    array($dateType), $currentDate->format('d-m-Y'), $totalDay, $specialDay, $lastActionStats, '');
                            }
                        }

                    }

                    // v?ng kh�ng l??ng
                    if ($am == 'vkl' && $pm == 'vkl') {

                        $dateType = 1; //full day
                        $totalDay = 1;
                        $specialDay = 0;
                        $lastActionStats = 3;
                        $usedDate = Core_Common::checkSpecialDay($currentDate->format('Y-m-d'), $accountInfo['account_id'], $dateType);
                        if (empty($usedDate['message'])) {
                            $apiAbsence->addRequest($accountInfo['account_id'],
                                array($currentDate->format('d-m-Y')),
                                array($currentDate->format('d-m-Y')),
                                array($dateType), $currentDate->format('d-m-Y') . ' v?ng kh�ng l??ng (th? vi?c)', $totalDay, $specialDay, $lastActionStats, '');
                        }


                    } else {
                        if ($am == 'vkl') {

                            $dateType = 2; //AM day
                            $totalDay = 0.5;
                            $specialDay = 0;
                            $lastActionStats = 3;
                            $usedDate = Core_Common::checkSpecialDay($currentDate->format('Y-m-d'), $accountInfo['account_id'], $dateType);
                            if (empty($usedDate['message'])) {
                                $apiAbsence->addRequest($accountInfo['account_id'],
                                    array($currentDate->format('d-m-Y')),
                                    array($currentDate->format('d-m-Y')),
                                    array($dateType), $currentDate->format('d-m-Y') . ' v?ng kh�ng l??ng (th? vi?c)', $totalDay, $specialDay, $lastActionStats, '');
                            }
                        } else if ($pm == 'vkl') {
                            $dateType = 3; //PM day
                            $totalDay = 0.5;
                            $specialDay = 0;
                            $lastActionStats = 3;
                            $usedDate = Core_Common::checkSpecialDay($currentDate->format('Y-m-d'), $accountInfo['account_id'], $dateType);
                            if (empty($usedDate['message'])) {
                                $apiAbsence->addRequest($accountInfo['account_id'],
                                    array($currentDate->format('d-m-Y')),
                                    array($currentDate->format('d-m-Y')),
                                    array($dateType), $currentDate->format('d-m-Y') . ' v?ng kh�ng l??ng (th? vi?c)', $totalDay, $specialDay, $lastActionStats, '');
                            }
                        }

                    }// end v?ng kh�ng l??ng


                    // Over Time
                    if ($am == 'ot' && $pm == 'ot') {
                        $dateType = Absence::$full; //Full day
                        $manualAbsences = ManualAbsence::getInstance()->selectBy('',0,$accountInfo['account_id'],0,0,$currentDate->format('Y-m-d'),$dateType);
                        if(empty($manualAbsences['data'])) {
                            $manualAbsence['date'] = $currentDate->format('Y-m-d');
                            $manualAbsence['date_type'] = $dateType;
                            ManualAbsence::getInstance()->add($manualAbsence);
                        }
                    } else {
                        if ($am == 'ot') {
                            $dateType = Absence::$am; //AM day
                            $manualAbsences = ManualAbsence::getInstance()->selectBy('',0,$accountInfo['account_id'],0,0,$currentDate->format('Y-m-d'),$dateType);
                            if(empty($manualAbsences['data'])) {
                                $manualAbsence['date'] = $currentDate->format('Y-m-d');
                                $manualAbsence['date_type'] = $dateType;
                                ManualAbsence::getInstance()->add($manualAbsence);
                            }
                        } else if ($pm == 'ot') {
                            $dateType = Absence::$pm; //PM day
                            $manualAbsences = ManualAbsence::getInstance()->selectBy('',0,$accountInfo['account_id'],0,0,$currentDate->format('Y-m-d'),$dateType);
                            if(empty($manualAbsences['data'])) {
                                $manualAbsence['date'] = $currentDate->format('Y-m-d');
                                $manualAbsence['date_type'] = $dateType;
                                ManualAbsence::getInstance()->add($manualAbsence);
                            }
                        }
                    }
                }
                catch(Exception $ex)
                {

                }
//                    // End Over Time
//
//                    //special day
////                    if($am == 'db' && $pm == 'db') {
////                        $dateType = 1; //full day
////                        $totalDay = 1;
////                        $specialDay = 0;
////                        $lastActionStats = 3;
////                        $usedDate = Core_Common::checkSpecialDay($currentDate->format('Y-m-d'),$accountInfo['account_id'],$dateType);
////                        if(empty($usedDate['message'])) {
////                            $apiAbsence->addRequest($accountInfo['account_id'],
////                                array($currentDate->format('d-m-Y')),
////                                array($currentDate->format('d-m-Y')),
////                                array($dateType), $currentDate->format('d-m-Y'), $totalDay, $specialDayID, $lastActionStats, '');
////                        }
////                    }else{
////                        if($am == 'ot')
////                        {
////                            $dateType = 2; //AM day
////                            $totalDay = 0.5;
////                            $lastActionStats = 3;
////                            $usedDate = Core_Common::checkSpecialDay($currentDate->format('Y-m-d'),$accountInfo['account_id'],$dateType);
////                            if(empty($usedDate['message'])) {
////                                $apiAbsence->addRequest($accountInfo['account_id'],
////                                    array($currentDate->format('d-m-Y')),
////                                    array($currentDate->format('d-m-Y')),
////                                    array($dateType), $currentDate->format('d-m-Y'), $totalDay, $specialDayID, $lastActionStats, '');
////                            }
////                        }else if($pm == 'ot')
////                        {
////                            $dateType = 3; //PM day
////                            $totalDay = 0.5;
////                            $lastActionStats = 3;
////                            $usedDate = Core_Common::checkSpecialDay($currentDate->format('Y-m-d'),$accountInfo['account_id'],$dateType);
////                            if(empty($usedDate['message'])) {
////                                $apiAbsence->addRequest($accountInfo['account_id'],
////                                    array($currentDate->format('d-m-Y')),
////                                    array($currentDate->format('d-m-Y')),
////                                    array($dateType), $currentDate->format('d-m-Y'), $totalDay, $specialDayID, $lastActionStats, '');
////                            }
////                        }
////                    }
//
//                }

            }
        }

        die('s');

    }


    public function importUserAction()
    {

        ini_set('memory_limit',-1);
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes
        global $globalConfig;
        $level = $globalConfig['level'];
        $levelNameTypes = array_flip($level);

        $file = 'static_backend/import-user/Gianty_List_of_Staff_02_2016-Bill_310316_Fixed.xlsx';
        $Reader = PHPExcel_IOFactory::createReaderForFile($file);

        $objXLS = $Reader->load($file);

        $sheet = $objXLS->getSheet(0);
        $highestRow         = $sheet->getHighestRow();
        $highestColumn      = $sheet->getHighestColumn(); // e.g 'F'

        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

        $generalType = 1; //is position
        $generals = General::getInstance()->getGeneralList('', General::$position, 11, 0, MAX_QUERY_LIMIT);

        foreach($generals['data'] as  $generalDel)
        {
            General::getInstance()->removeGeneral($generalDel['general_id']);
        }

        // get IMAGES
        foreach ($sheet->getDrawingCollection() as $drawing) {
            //for XLSX format
            $string = $drawing->getCoordinates();
            $coordinate = PHPExcel_Cell::coordinateFromString($string);

            if ($drawing instanceof PHPExcel_Worksheet_Drawing || $drawing instanceof  PHPExcel_Worksheet_MemoryDrawing){
                $filename = $drawing->getPath();
                $coordinate = $coordinate[0].''.$coordinate[1];


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

            }
        }

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
            $sLevel = trim($user[5]);
            $iLevel = isset($levelNameTypes[$sLevel]) ? $levelNameTypes[$sLevel] : 0;
            $groupName = trim($user[6]);
            $birthday = trim($user[7]);
            $startDate = trim($user[8]);
            $skype =  trim($user[9]);
            $imageTmp = trim($user[10]);
            $imageTmp = (empty($imageTmp)) ? '' : $imageTmp;
            $sNgayKyHopDongLaoDong = trim($user[11]);
            $contractTypeName = trim($user[12]);
            $contractType = General::getInstance()->selectByNameAndType($contractTypeName,General::$contract);
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

            $aShikishima = AccountInfo::getInstance()->getAccountInfoByUserName('shikishima');
            $aShikishimaId = ($aShikishima) ? $aShikishima['account_id'] : 0;

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
                'stixchat'=>$aLamId,
                'offshore'=>$aShikishimaId
            );


            if($groupName != 'NGH? THAI S?N' && strtolower($groupName) && 'ngh? thai s?n' && strtolower($groupName) != 'nghi thai san')  {
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

                    if($group['manager_id'] > 0) {
                        $groupMember = GroupMember::getInstance()->getGroupMemberByAccountAndGroupId($group['manager_id'], $group['group_id']);

                        if (empty($groupMember)) {
                            // add group member

                            $arrMemberData['account_id'] = $group['manager_id'];
                            $arrMemberData['group_id'] = $group['group_id'];
                            $arrMemberData['level'] = GroupMember::$manager;
                            $arrMemberData['create_date'] = time();
//                            Core_Common::var_dump($arrMemberData,false);
                            GroupMember::getInstance()->addGroupMember($arrMemberData);
                        }
                    }
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

            if(!empty($imageTmp) && file_exists($imageTmp)) {
                Core_Common::deleteFile($avatar,PATH_AVATAR_UPLOAD_DIR);
                copy($imageTmp, PATH_AVATAR_UPLOAD_DIR . '/' . $avatar);
            }else{
                echo ' file not exits name : '.$imageTmp.'<br/>';
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
                $accountInfo['contract_type'] = empty($contractType) ? 0 : $contractType['general_id'];
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
                    $arrMemberData['level'] = $iLevel;
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
                $accountInfo['name'] = $name;
                $accountInfo['email'] = $email;
                $accountInfo['picture'] = $avatar;
                $accountInfo['avatar'] = $avatar;
                $accountInfo['id'] = $id;
                $accountInfo['contract_type'] = empty($contractType) ? 0 : $contractType['general_id'];
                $accountInfo['skype_account'] = $skype;
//                if($accountInfo['username'] == 'phat.lt')
//                    Core_Common::var_dump($accountInfo);


                if(!isset($accountInfo['account_id']))
                    Core_Common::var_dump($accountInfo);

                $accountId = $accountInfo['account_id'];
                AccountInfo::getInstance()->updateAccountInfo($accountInfo);

                if($accountId > 0  && !empty($group)) {

                    $groupMember = GroupMember::getInstance()->getGroupMemberByAccountAndGroupId($accountId, $group['group_id']);
//                    if($accountId == $group['manager_id'])
//                        $level = GroupMember::$manager;
//                    else if($accountId == $group['admin_id'])
//                        $level = GroupMember::$admin;

                    if(empty($groupMember)) {
                        // add group member
                        echo ' add id: '.  $accountInfo['id']. ' -- username: '.$accountInfo['username'].' -- teamName: '.$group['group_name'].' -- position: '.$generalName. ' -- level '.$level.'<br/>';
                        $arrMemberData = array();
                        $arrMemberData['account_id'] = $accountId;
                        $arrMemberData['group_id'] = $group['group_id'];
                        $arrMemberData['level'] = $iLevel;
                        $arrMemberData['create_date'] = time();
                        Core_Common::var_dump($arrMemberData,false);
                        GroupMember::getInstance()->addGroupMember($arrMemberData);
                    }

                }

            }

        }
//        $_SESSION["import-absence"]=$users;
        die('success');
        Core_Common::var_dump($users);
    }


    function LoadJpeg($imgname)
    {
        /* Attempt to open */

        $im = @imagecreatefromjpeg($imgname);

        /* See if it failed */
        if(!$im)
        {
            /* Create a black image */
            $im  = imagecreatetruecolor(150, 30);
            $bgc = imagecolorallocate($im, 255, 255, 255);
            $tc  = imagecolorallocate($im, 0, 0, 0);

            imagefilledrectangle($im, 0, 0, 150, 30, $bgc);

            /* Output an error message */
            imagestring($im, 1, 5, 5, 'Error loading ' . $imgname, $tc);
        }

        return $im;
    }
}