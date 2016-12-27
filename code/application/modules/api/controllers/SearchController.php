<?php
/**
 * @author      :   Hiennd
 * @name        :   ApiController
 * @version     :   20161214
 * @copyright   :   Dahi
 * @todo        :   controller API 
 */
class Api_SearchController extends Zend_Controller_Action
{
    
    /**
     * init of controller
     */
    public function init()
    {
        //Disale layout
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
    }
    
    public function indexAction()
    {
        $cityId = $this->_getParam('cityId', '');
        $catId = $this->_getParam('catId', '');
        $txtSearch = $this->_getParam('txtSearch', '');
        // $sRecaptcha = $this->_getParam('sRecaptcha', '');
// exit($sRecaptcha);

        $arrReturn = array();
        // $bCaptcha = true;        

        if(!empty($cityId) && !Core_Validate::checkNumber($cityId)){
            echo Zend_Json::encode(Core_Server::setOutputData(true, 'Mã tỉnh thành sai', array()));
            exit;
        }

        if(!empty($catId) && !Core_Validate::checkNumber($catId)){
            echo Zend_Json::encode(Core_Server::setOutputData(true, 'Mã danh mục sai', array()));
            exit;
        }                    

        $totals = News::getInstance()->countNewsList($catId, $subCatId="", $cityId, $districtId="", $priceFrom="", $priceTo="", $active="", $txtSearch, $accountId="");
        if($totals == 0){
// error_log("here_".Zend_Json::encode($arrAcc));                    
            echo Zend_Json::encode(Core_Server::setOutputData(false, 'Không tìm thấy', array("totals"=>0, "data"=>array())));
            exit;   
        }
        else{

            $arrNews = News::getInstance()->getNewsList($catId, $subCatId="", $cityId, $districtId="", $priceFrom="", $priceTo="", $active="", $txtSearch, $accountId="",  $sSortField="news_is_vip, news_update_date", $sSortType="DESC", $iLimit=0, $iOffset=MAX_QUERY_LIMIT);
            foreach ($arrNews as $news) {
                $arrReturnTmp = array();
                $arrReturnTmp["news_id"] = $news["news_id"];
                $arrReturnTmp["news_type"] = $news["news_type"];
                $arrReturnTmp["news_account_id"] = $news["news_account_id"];
                $arrReturnTmp["news_cat_id"] = $news["news_cat_id"];
                $arrReturnTmp["news_sub_cat_id"] = $news["news_sub_cat_id"];
                $arrReturnTmp["news_city_id"] = $news["news_city_id"];
                $arrReturnTmp["news_district_id"] = $news["news_district_id"];
                $arrReturnTmp["news_ward_id"] = $news["news_ward_id"];
                $arrReturnTmp["news_price"] = $news["news_price"];
                $arrReturnTmp["news_tensp"] = $news["news_tensp"];
                $arrReturnTmp["news_tittle"] = $news["news_tittle"];
                $arrReturnTmp["news_contact_name"] = $news["news_contact_name"];
                $arrReturnTmp["news_mobilel"] = $news["news_mobilel"];
                $arrReturnTmp["news_email"] = $news["news_email"];
                $arrReturnTmp["news_address"] = $news["news_address"];
                $arrReturnTmp["news_detail"] = $news["news_detail"];
                $arrReturnTmp["news_update_date"] = $news["news_update_date"];
                $arrReturnTmp["news_create_date"] = $news["news_create_date"];
                $arrReturnTmp["upper_today"] = $news["news_is_upper_today"];
                switch($news["news_type"]){                    
                    case 1://properties
                    {
                        $arrReturnTmp["proper_type_id"] = $news["proper_type_id"];
                        $arrReturnTmp["proper_address"] = $news["proper_address"];
                        $arrReturnTmp["proper_ward_id"] = $news["proper_ward_id"];
                        $arrReturnTmp["proper_project"] = $news["proper_project"];
                        $arrReturnTmp["proper_CT1"] = $news["proper_CT1"];
                        $arrReturnTmp["proper_CT2"] = $news["proper_CT2"];
                        $arrReturnTmp["proper_CT3_id"] = $news["proper_CT3_id"];
                        $arrReturnTmp["proper_CT4_id"] = $news["proper_CT4_id"];
                        $arrReturnTmp["proper_CT5"] = $news["proper_CT5"];
                        $arrReturnTmp["proper_CT6"] = $news["proper_CT6"];
                        $arrReturnTmp["proper_CT7"] = $news["proper_CT7"];                        
                    }
                    case 2://job
                    {
                        $arrReturnTmp["job_gender_id"] = $news["job_gender_id"];
                        $arrReturnTmp["job_birth_year"] = $news["job_birth_year"];
                        $arrReturnTmp["job_type_id"] = $news["job_type_id"];
                        $arrReturnTmp["job_cat_id"] = $news["job_cat_id"];
                        $arrReturnTmp["job_birth_year_from"] = $news["job_birth_year_from"];
                        $arrReturnTmp["job_birth_year_to"] = $news["job_birth_year_to"];
                        $arrReturnTmp["job_experience"] = $news["job_experience"];
                        $arrReturnTmp["job_salary_from"] = $news["job_salary_from"];
                        $arrReturnTmp["job_salary_to"] = $news["job_salary_to"];
                    }
                    case 3://car
                    {
                        $arrReturnTmp["vehicle_model"] = $news["vehicle_model"];
                        $arrReturnTmp["vehicle_made_by_id"] = $news["vehicle_made_by_id"];
                        $arrReturnTmp["vehicle_body_style_id"] = $news["vehicle_body_style_id"];
                        $arrReturnTmp["vehicle_made_year_id"] = $news["vehicle_made_year_id"];
                        $arrReturnTmp["vehicle_origin_id"] = $news["vehicle_origin_id"];
                        $arrReturnTmp["vehicle_new_percen"] = $news["vehicle_new_percen"];
                        $arrReturnTmp["vehicle_transmission_id"] = $news["vehicle_transmission_id"];
                        $arrReturnTmp["vehicle_driver_type_id"] = $news["vehicle_driver_type_id"];
                        $arrReturnTmp["vehicle_fuel_type_id"] = $news["vehicle_fuel_type_id"];
                        $arrReturnTmp["vehicle_color_id"] = $news["vehicle_color_id"];
                        $arrReturnTmp["vehicle_safety"] = $news["vehicle_safety"];
                        $arrReturnTmp["vehicle_feature"] = $news["vehicle_feature"];
                    }
                    case 4://bike
                    {
                        $arrReturnTmp["vehicle_made_by_id"] = $news["vehicle_made_by_id"];
                        $arrReturnTmp["vehicle_made_year_id"] = $news["vehicle_made_year_id"];
                        $arrReturnTmp["vehicle_origin_id"] = $news["vehicle_origin_id"];
                        $arrReturnTmp["vehicle_new_percen"] = $news["vehicle_new_percen"];
                        $arrReturnTmp["vehicle_color_id"] = $news["vehicle_color_id"];
                    }
                    default: //case 0://normal
                    {

                    }
                }                                
                $arrReturn[]=$arrReturnTmp;
            }                    
            echo Zend_Json::encode(Core_Server::setOutputData(false, 'OK', array("totals"=>$totals, "data"=>$arrReturn)));
            exit;               
        }
    }           

    public function getNewsAction()
    {
        $isVip = $this->_getParam('isVip', '');
        $iOffset = $this->_getParam('iOffset', 0);
        $iLimit = $this->_getParam('iLimit', MAX_PAGE_LIMIT);
        // $sRecaptcha = $this->_getParam('sRecaptcha', '');
// exit($sRecaptcha);

        $arrReturn = array();
        // $bCaptcha = true;        

        if(!empty($isVip) && $isVip != 0 && $isVip != 1){
            echo Zend_Json::encode(Core_Server::setOutputData(true, 'Loai tin sai dinh dang', array()));
            exit;
        }

        if(!Core_Validate::checkNumber($iOffset)){
            echo Zend_Json::encode(Core_Server::setOutputData(true, 'Phan doan sai', array()));
            exit;
        }

        if(!Core_Validate::checkNumber($iLimit)){
            echo Zend_Json::encode(Core_Server::setOutputData(true, 'Phan trang sai', array()));
            exit;
        }           

        $totals = News::getInstance()->countNewsList($isVip, $catId="", $subCatId="", $cityId="", $districtId="", $priceFrom="", $priceTo="", $active="", $txtSearch="", $accountId="");
        if($totals == 0){
// error_log("here_".Zend_Json::encode($arrAcc));                    
            echo Zend_Json::encode(Core_Server::setOutputData(false, 'Không tìm thấy', array("totals"=>0, "data"=>array())));
            exit;   
        }
        else{

            $arrNews = News::getInstance()->getNewsList($isVip, $catId="", $subCatId="", $cityId="", $districtId="", $priceFrom="", $priceTo="", $active="", $txtSearch="", $accountId="",  $sSortField="news_update_date", $sSortType="DESC", $iOffset, $iLimit);
            foreach ($arrNews as $news) {
                $arrReturnTmp = array();
                $arrReturnTmp["news_id"] = $news["news_id"];
                $arrReturnTmp["news_type"] = $news["news_type"];
                $arrReturnTmp["news_account_id"] = $news["news_account_id"];
                $arrReturnTmp["news_cat_id"] = $news["news_cat_id"];
                $arrReturnTmp["news_sub_cat_id"] = $news["news_sub_cat_id"];
                $arrReturnTmp["news_city_id"] = $news["news_city_id"];
                $arrReturnTmp["news_district_id"] = $news["news_district_id"];
                $arrReturnTmp["news_ward_id"] = $news["news_ward_id"];
                $arrReturnTmp["news_price"] = $news["news_price"];
                $arrReturnTmp["news_tensp"] = $news["news_tensp"];
                $arrReturnTmp["news_tittle"] = $news["news_tittle"];
                $arrReturnTmp["news_contact_name"] = $news["news_contact_name"];
                $arrReturnTmp["news_mobilel"] = $news["news_mobilel"];
                $arrReturnTmp["news_email"] = $news["news_email"];
                $arrReturnTmp["news_address"] = $news["news_address"];
                $arrReturnTmp["news_detail"] = $news["news_detail"];
                $arrReturnTmp["news_update_date"] = $news["news_update_date"];
                $arrReturnTmp["news_create_date"] = $news["news_create_date"];
                $arrReturnTmp["upper_today"] = $news["news_is_upper_today"];
                $arrReturnTmp["news_detailURL"] = "/xem-tin/".Core_Helper::convertStrToURL($news["news_tittle"])."-".$news["news_id"];
                switch($news["news_type"]){                    
                    case 1://properties
                    {
                        $arrReturnTmp["proper_type_id"] = $news["proper_type_id"];
                        $arrReturnTmp["proper_address"] = $news["proper_address"];
                        $arrReturnTmp["proper_ward_id"] = $news["proper_ward_id"];
                        $arrReturnTmp["proper_project"] = $news["proper_project"];
                        $arrReturnTmp["proper_CT1"] = $news["proper_CT1"];
                        $arrReturnTmp["proper_CT2"] = $news["proper_CT2"];
                        $arrReturnTmp["proper_CT3_id"] = $news["proper_CT3_id"];
                        $arrReturnTmp["proper_CT4_id"] = $news["proper_CT4_id"];
                        $arrReturnTmp["proper_CT5"] = $news["proper_CT5"];
                        $arrReturnTmp["proper_CT6"] = $news["proper_CT6"];
                        $arrReturnTmp["proper_CT7"] = $news["proper_CT7"];                        
                    }
                    case 2://job
                    {
                        $arrReturnTmp["job_gender_id"] = $news["job_gender_id"];
                        $arrReturnTmp["job_birth_year"] = $news["job_birth_year"];
                        $arrReturnTmp["job_type_id"] = $news["job_type_id"];
                        $arrReturnTmp["job_cat_id"] = $news["job_cat_id"];
                        $arrReturnTmp["job_birth_year_from"] = $news["job_birth_year_from"];
                        $arrReturnTmp["job_birth_year_to"] = $news["job_birth_year_to"];
                        $arrReturnTmp["job_experience"] = $news["job_experience"];
                        $arrReturnTmp["job_salary_from"] = $news["job_salary_from"];
                        $arrReturnTmp["job_salary_to"] = $news["job_salary_to"];
                    }
                    case 3://car
                    {
                        $arrReturnTmp["vehicle_model"] = $news["vehicle_model"];
                        $arrReturnTmp["vehicle_made_by_id"] = $news["vehicle_made_by_id"];
                        $arrReturnTmp["vehicle_body_style_id"] = $news["vehicle_body_style_id"];
                        $arrReturnTmp["vehicle_made_year_id"] = $news["vehicle_made_year_id"];
                        $arrReturnTmp["vehicle_origin_id"] = $news["vehicle_origin_id"];
                        $arrReturnTmp["vehicle_new_percen"] = $news["vehicle_new_percen"];
                        $arrReturnTmp["vehicle_transmission_id"] = $news["vehicle_transmission_id"];
                        $arrReturnTmp["vehicle_driver_type_id"] = $news["vehicle_driver_type_id"];
                        $arrReturnTmp["vehicle_fuel_type_id"] = $news["vehicle_fuel_type_id"];
                        $arrReturnTmp["vehicle_color_id"] = $news["vehicle_color_id"];
                        $arrReturnTmp["vehicle_safety"] = $news["vehicle_safety"];
                        $arrReturnTmp["vehicle_feature"] = $news["vehicle_feature"];
                    }
                    case 4://bike
                    {
                        $arrReturnTmp["vehicle_made_by_id"] = $news["vehicle_made_by_id"];
                        $arrReturnTmp["vehicle_made_year_id"] = $news["vehicle_made_year_id"];
                        $arrReturnTmp["vehicle_origin_id"] = $news["vehicle_origin_id"];
                        $arrReturnTmp["vehicle_new_percen"] = $news["vehicle_new_percen"];
                        $arrReturnTmp["vehicle_color_id"] = $news["vehicle_color_id"];
                    }
                    default: //case 0://normal
                    {

                    }
                }                                
                $arrReturn[]=$arrReturnTmp;
            }                    
            echo Zend_Json::encode(Core_Server::setOutputData(false, 'OK', array("totals"=>$totals, "data"=>$arrReturn)));
            exit;               
        }
    }

    public function getDetailNewsAction()
    {
        $newsId = $this->_getParam('nid', '');        

        $arrReturn = array();
        // $bCaptcha = true;        

        if(empty($newsId) || !Core_Validate::checkNumber($newsId)){
            echo Zend_Json::encode(Core_Server::setOutputData(true, 'ma tin khong hop le - '.$newsId, array()));
            exit;
        }              

        $arrNewsDetail = News::getInstance()->getNewsByID($newsId);
error_log(Zend_Json::encode($arrNewsDetail));        
        if(is_array($arrNewsDetail) && sizeof($arrNewsDetail)>0){
            $arrReturnTmp = array();
            $arrReturnTmp["news_id"] = $arrNewsDetail["news_id"];
            $arrReturnTmp["news_type"] = $arrNewsDetail["news_type"];
            $arrReturnTmp["news_account_id"] = $arrNewsDetail["news_account_id"];
            $arrReturnTmp["news_cat_id"] = $arrNewsDetail["news_cat_id"];
            $arrReturnTmp["news_sub_cat_id"] = $arrNewsDetail["news_sub_cat_id"];
            $arrReturnTmp["news_city_id"] = $arrNewsDetail["news_city_id"];
            $arrReturnTmp["news_district_id"] = $arrNewsDetail["news_district_id"];
            $arrReturnTmp["news_ward_id"] = $arrNewsDetail["news_ward_id"];
            $arrReturnTmp["news_price"] = $arrNewsDetail["news_price"];
            $arrReturnTmp["news_tensp"] = $arrNewsDetail["news_tensp"];
            $arrReturnTmp["news_tittle"] = $arrNewsDetail["news_tittle"];
            $arrReturnTmp["news_contact_name"] = $arrNewsDetail["news_contact_name"];
            $arrReturnTmp["news_mobilel"] = $arrNewsDetail["news_mobilel"];
            $arrReturnTmp["news_email"] = $arrNewsDetail["news_email"];
            $arrReturnTmp["news_address"] = $arrNewsDetail["news_address"];
            $arrReturnTmp["news_detail"] = $arrNewsDetail["news_detail"];
            $arrReturnTmp["news_update_date"] = $arrNewsDetail["news_update_date"];
            $arrReturnTmp["news_create_date"] = $arrNewsDetail["news_create_date"];
            $arrReturnTmp["upper_today"] = $arrNewsDetail["news_is_upper_today"];
            // $arrReturnTmp["news_detailURL"] = "/xem-tin/".Core_Helper::convertStrToURL($arrNewsDetail["news_tittle"])."-".$arrNewsDetail["news_id"];
            switch($arrNewsDetail["news_type"]){                    
                case 1://properties
                {
                    $arrReturnTmp["proper_type_id"] = $arrNewsDetail["proper_type_id"];
                    $arrReturnTmp["proper_address"] = $arrNewsDetail["proper_address"];
                    $arrReturnTmp["proper_ward_id"] = $arrNewsDetail["proper_ward_id"];
                    $arrReturnTmp["proper_project"] = $arrNewsDetail["proper_project"];
                    $arrReturnTmp["proper_CT1"] = $arrNewsDetail["proper_CT1"];
                    $arrReturnTmp["proper_CT2"] = $arrNewsDetail["proper_CT2"];
                    $arrReturnTmp["proper_CT3_id"] = $arrNewsDetail["proper_CT3_id"];
                    $arrReturnTmp["proper_CT4_id"] = $arrNewsDetail["proper_CT4_id"];
                    $arrReturnTmp["proper_CT5"] = $arrNewsDetail["proper_CT5"];
                    $arrReturnTmp["proper_CT6"] = $arrNewsDetail["proper_CT6"];
                    $arrReturnTmp["proper_CT7"] = $arrNewsDetail["proper_CT7"];                        
                }
                case 2://job
                {
                    $arrReturnTmp["job_gender_id"] = $arrNewsDetail["job_gender_id"];
                    $arrReturnTmp["job_birth_year"] = $arrNewsDetail["job_birth_year"];
                    $arrReturnTmp["job_type_id"] = $arrNewsDetail["job_type_id"];
                    $arrReturnTmp["job_cat_id"] = $arrNewsDetail["job_cat_id"];
                    $arrReturnTmp["job_birth_year_from"] = $arrNewsDetail["job_birth_year_from"];
                    $arrReturnTmp["job_birth_year_to"] = $arrNewsDetail["job_birth_year_to"];
                    $arrReturnTmp["job_experience"] = $arrNewsDetail["job_experience"];
                    $arrReturnTmp["job_salary_from"] = $arrNewsDetail["job_salary_from"];
                    $arrReturnTmp["job_salary_to"] = $arrNewsDetail["job_salary_to"];
                }
                case 3://car
                {
                    $arrReturnTmp["vehicle_model"] = $arrNewsDetail["vehicle_model"];
                    $arrReturnTmp["vehicle_made_by_id"] = $arrNewsDetail["vehicle_made_by_id"];
                    $arrReturnTmp["vehicle_body_style_id"] = $arrNewsDetail["vehicle_body_style_id"];
                    $arrReturnTmp["vehicle_made_year_id"] = $arrNewsDetail["vehicle_made_year_id"];
                    $arrReturnTmp["vehicle_origin_id"] = $arrNewsDetail["vehicle_origin_id"];
                    $arrReturnTmp["vehicle_new_percen"] = $arrNewsDetail["vehicle_new_percen"];
                    $arrReturnTmp["vehicle_transmission_id"] = $arrNewsDetail["vehicle_transmission_id"];
                    $arrReturnTmp["vehicle_driver_type_id"] = $arrNewsDetail["vehicle_driver_type_id"];
                    $arrReturnTmp["vehicle_fuel_type_id"] = $arrNewsDetail["vehicle_fuel_type_id"];
                    $arrReturnTmp["vehicle_color_id"] = $arrNewsDetail["vehicle_color_id"];
                    $arrReturnTmp["vehicle_safety"] = $arrNewsDetail["vehicle_safety"];
                    $arrReturnTmp["vehicle_feature"] = $arrNewsDetail["vehicle_feature"];
                }
                case 4://bike
                {
                    $arrReturnTmp["vehicle_made_by_id"] = $arrNewsDetail["vehicle_made_by_id"];
                    $arrReturnTmp["vehicle_made_year_id"] = $arrNewsDetail["vehicle_made_year_id"];
                    $arrReturnTmp["vehicle_origin_id"] = $arrNewsDetail["vehicle_origin_id"];
                    $arrReturnTmp["vehicle_new_percen"] = $arrNewsDetail["vehicle_new_percen"];
                    $arrReturnTmp["vehicle_color_id"] = $arrNewsDetail["vehicle_color_id"];
                }
                default: //case 0://normal
                {

                }
            }                                
            echo Zend_Json::encode(Core_Server::setOutputData(false, 'OK', array("detail"=>$arrReturnTmp)));
            exit;
        }                    
        echo Zend_Json::encode(Core_Server::setOutputData(true, 'Ma tin khong ton tai', array()));
        exit; 
    }
}

