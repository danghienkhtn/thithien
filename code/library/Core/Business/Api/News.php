<?php
/**
 * @author      :   Hiennd
 * @name        :   Core_Business_Api_News
 * @version     :   20161221
 * @copyright   :   Dahi
 * @todo        :   Using for post news service
 */
class Core_Business_Api_News
{
   /**
     *
     * 
     */
    protected static $_instance = null;


        /**
     * Constructor of class
     * we don't permit an explicit call of the constructor! (like $v = new Singleton())
     */
    protected function __construct() {
        //Nothing
    }

    /**
     * Get singletom instance
     * 
     */
    public final static function getInstance() {
        // check instance
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        // return instance
        return self::$_instance;
    }
            
          
    
    /**
     * 
     */
    public function insertJobNews($arrData)
    {
         //init return result
        $result = 0;
        $arrData = Validate::encodeValues($arrData);
        try {
            
            $iCreateDate = time();
            // Get Data Master Global
            $storage = Core_Global::getDbGlobalMaster();
            $sql = "INSERT INTO `post_news` ( 
                `news_account_id`,
                `news_type`, 
                `news_cat_id`, 
                `news_sub_cat_id`, 
                `news_city_id`, 
                `news_district_id`, 
                `news_price`, 
                `news_tittle`, 
                `news_contact_name`,
                `news_mobile`,
                `news_email`,
                `news_address`,
                `news_detail`,
                `job_gender_id`,
                `job_birth_year`,
                `job_type_id`,
                `job_cat_id`,
                `job_birth_year_from`,
                `job_birth_year_to`,
                `job_experience`,
                `job_salary_from`,
                `job_salary_to`,
                `news_is_upper_today`,
                `news_create_date`,
                `news_update_date`
                ) VALUES (
                :p_account_id ,
                :p_news_type ,
                :p_cat_id,
                :p_sub_cat_id,
                :p_city_id,
                :p_district_id,
                :p_price,
                :p_tittle,
                :p_contact_name,
                :p_mobile,
                :p_email,
                :p_address,
                :p_detail ,
                :p_job_gender_id ,
                :p_job_birth_year,
                :p_job_type_id,
                :p_job_cat_id,
                :p_job_birth_year_from,
                :p_job_birth_year_to,
                :p_job_experience,
                :p_job_salary_from,
                :p_job_salary_to,
                :p_is_upper_today,
                :p_create_date,
                :p_update_date
                )";
// error_log($sql);                    
// error_log(Zend_Json::encode($arrData));
            // Prepare store procude
            $stmt = $storage->prepare($sql);

            $stmt->bindParam(':p_account_id', $arrData['account_id'], PDO::PARAM_INT);
            $stmt->bindParam(':p_news_type', $arrData['news_type'], PDO::PARAM_INT);
            $stmt->bindParam(':p_cat_id', $arrData['catId'], PDO::PARAM_INT);
            $stmt->bindParam(':p_sub_cat_id', $arrData['subCatId'], PDO::PARAM_INT);
            $stmt->bindParam(':p_city_id', $arrData['cityId'], PDO::PARAM_INT);
            $stmt->bindParam(':p_district_id', $arrData['districtId'], PDO::PARAM_INT);
            $stmt->bindParam(':p_price', $arrData['price'], PDO::PARAM_INT);
            $stmt->bindParam(':p_tittle', $arrData['tittle'], PDO::PARAM_STR);
            $stmt->bindParam(':p_contact_name', $arrData['contact_name'], PDO::PARAM_STR);
            $stmt->bindParam(':p_mobile', $arrData['mobile'], PDO::PARAM_STR);
            $stmt->bindParam(':p_email', $arrData['email'], PDO::PARAM_STR);
            $stmt->bindParam(':p_address', $arrData['address'], PDO::PARAM_STR);
            $stmt->bindParam(':p_detail ', $arrData['detail'], PDO::PARAM_STR);
            $stmt->bindParam(':p_job_gender_id ', $arrData['job_gender_id'], PDO::PARAM_INT);
            $stmt->bindParam(':p_job_birth_year', $arrData['job_birth_year'], PDO::PARAM_INT);
            $stmt->bindParam(':p_job_type_id', $arrData['job_type_id'], PDO::PARAM_INT);
            $stmt->bindParam(':p_job_cat_id', $arrData['job_cat_id'], PDO::PARAM_INT);
            $stmt->bindParam(':p_job_birth_year_from', $arrData['job_birth_year_from'], PDO::PARAM_INT);
            $stmt->bindParam(':p_job_birth_year_to', $arrData['job_birth_year_to'], PDO::PARAM_INT);
            $stmt->bindParam(':p_job_experience', $arrData['job_experience'], PDO::PARAM_STR);
            $stmt->bindParam(':p_job_salary_from', $arrData['job_salary_from'], PDO::PARAM_STR);
            $stmt->bindParam(':p_job_salary_to', $arrData['job_salary_to'], PDO::PARAM_STR);
            $stmt->bindParam(':p_is_upper_today', $arrData['isUpperToday'], PDO::PARAM_INT);
            $stmt->bindParam(':p_create_date', $iCreateDate, PDO::PARAM_INT);
            $stmt->bindParam(':p_update_date', $iCreateDate, PDO::PARAM_INT);
                        
            $stmt->execute();

            // Fetch Result            
            $result = $storage->lastInsertId(); 
            // Free cursor
            $stmt->closeCursor();
        } catch (Exception $ex) {
            Core_Common::var_dump($ex);
            // ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),'',$arrData['name']);
            error_log("error_exception");            
            $result = 0;
        }

        // return data
        return $result;
    }
    
    public function insertPropertiesNews($arrData)
    {
         //init return result
        $result = 0;
        $arrData = Validate::encodeValues($arrData);
        try {
            
            $iCreateDate = time();
            // Get Data Master Global
            $storage = Core_Global::getDbGlobalMaster();
            $sql = "INSERT INTO `post_news` ( 
                `news_account_id`,
                `news_type`, 
                `news_cat_id`, 
                `news_sub_cat_id`, 
                `news_city_id`, 
                `news_district_id`,
                `news_ward_id`, 
                `news_price`, 
                `news_tittle`, 
                `news_contact_name`,
                `news_mobile`,
                `news_email`,
                `news_address`,
                `news_detail`,
                `proper_type_id`,
                `proper_address`,
                `proper_ward_id`,  
                `proper_project`,
                `proper_CT1`,
                `proper_CT2`,
                `proper_CT3_id`,
                `proper_CT4_id`,
                `proper_CT5`,
                `proper_CT6`,
                `proper_CT7`,
                `news_is_upper_today`,
                `news_create_date`,
                `news_update_date`
                ) VALUES (
                :p_account_id ,
                :p_news_type ,
                :p_cat_id,
                :p_sub_cat_id,
                :p_city_id,
                :p_district_id,
                :p_price,
                :p_tittle,
                :p_contact_name,
                :p_mobile,
                :p_email,
                :p_address,
                :p_detail ,
                :p_proper_type_id ,
                :p_proper_address,
                :p_proper_ward_id,
                :p_proper_project,
                :p_proper_CT1,
                :p_proper_CT2,
                :p_proper_CT3,
                :p_proper_CT4,
                :p_proper_CT5,
                :p_proper_CT6,
                :p_proper_CT7,
                :p_is_upper_today,
                :p_create_date,
                :p_update_date
                )";
// error_log($sql);                    
// error_log(Zend_Json::encode($arrData));
            // Prepare store procude
            $stmt = $storage->prepare($sql);

            $stmt->bindParam(':p_account_id', $arrData['account_id'], PDO::PARAM_INT);
            $stmt->bindParam(':p_news_type', $arrData['news_type'], PDO::PARAM_INT);
            $stmt->bindParam(':p_cat_id', $arrData['catId'], PDO::PARAM_INT);
            $stmt->bindParam(':p_sub_cat_id', $arrData['subCatId'], PDO::PARAM_INT);
            $stmt->bindParam(':p_city_id', $arrData['cityId'], PDO::PARAM_INT);
            $stmt->bindParam(':p_district_id', $arrData['districtId'], PDO::PARAM_INT);
            $stmt->bindParam(':p_price', $arrData['price'], PDO::PARAM_INT);
            $stmt->bindParam(':p_tittle', $arrData['tittle'], PDO::PARAM_STR);
            $stmt->bindParam(':p_contact_name', $arrData['contact_name'], PDO::PARAM_STR);
            $stmt->bindParam(':p_mobile', $arrData['mobile'], PDO::PARAM_STR);
            $stmt->bindParam(':p_email', $arrData['email'], PDO::PARAM_STR);
            $stmt->bindParam(':p_address', $arrData['address'], PDO::PARAM_STR);
            $stmt->bindParam(':p_detail ', $arrData['detail'], PDO::PARAM_STR);
            $stmt->bindParam(':p_proper_type_id', $arrData['proper_type_id'], PDO::PARAM_INT);
            $stmt->bindParam(':p_proper_address', $arrData['proper_address'], PDO::PARAM_STR);
            $stmt->bindParam(':p_proper_ward_id', $arrData['proper_ward_id'], PDO::PARAM_INT);
            $stmt->bindParam(':p_proper_project', $arrData['proper_project'], PDO::PARAM_STR);
            $stmt->bindParam(':p_proper_CT1', $arrData['proper_CT1'], PDO::PARAM_STR);
            $stmt->bindParam(':p_proper_CT2', $arrData['proper_CT2'], PDO::PARAM_STR);
            $stmt->bindParam(':p_proper_CT3', $arrData['proper_CT3_id'], PDO::PARAM_INT);
            $stmt->bindParam(':p_proper_CT4', $arrData['proper_CT4_id'], PDO::PARAM_INT);
            $stmt->bindParam(':p_proper_CT5', $arrData['proper_CT5'], PDO::PARAM_STR);
            $stmt->bindParam(':p_proper_CT6', $arrData['proper_CT6'], PDO::PARAM_STR);
            $stmt->bindParam(':p_proper_CT7', $arrData['proper_CT7'], PDO::PARAM_STR);
            $stmt->bindParam(':p_is_upper_today', $arrData['isUpperToday'], PDO::PARAM_INT);
            $stmt->bindParam(':p_create_date', $iCreateDate, PDO::PARAM_INT);
            $stmt->bindParam(':p_update_date', $iCreateDate, PDO::PARAM_INT);
                        
            $stmt->execute();

            // Fetch Result            
            $result = $storage->lastInsertId(); 
            // Free cursor
            $stmt->closeCursor();
        } catch (Exception $ex) {
            Core_Common::var_dump($ex);
            // ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),'',$arrData['name']);
            error_log("error_exception");            
            $result = 0;
        }

        // return data
        return $result;
    }
    
    public function insertCarNews($arrData)
    {
         //init return result
        $result = 0;
        $arrData = Validate::encodeValues($arrData);
        try {
            
            $iCreateDate = time();
            // Get Data Master Global
            $storage = Core_Global::getDbGlobalMaster();
            $sql = "INSERT INTO `post_news` ( 
                `news_account_id`,
                `news_type`, 
                `news_cat_id`, 
                `news_sub_cat_id`, 
                `news_city_id`, 
                `news_district_id`,
                `news_ward_id`, 
                `news_price`, 
                `news_tittle`, 
                `news_contact_name`,
                `news_mobile`,
                `news_email`,
                `news_address`,
                `news_detail`,
                `vehicle_model`,
                `vehicle_made_by_id`,
                `vehicle_body_style_id`,
                `vehicle_made_year_id`,
                `vehicle_origin_id`,
                `vehicle_new_percen`,
                `vehicle_transmission_id`,
                `vehicle_driver_type_id`,
                `vehicle_fuel_type_id`,
                `vehicle_color_id`,
                `vehicle_safety`,
                `vehicle_feature`,
                `news_is_upper_today`,
                `news_create_date`,
                `news_update_date`
                ) VALUES (
                :p_account_id ,
                :p_news_type ,
                :p_cat_id,
                :p_sub_cat_id,
                :p_city_id,
                :p_district_id,
                :p_ward_id,
                :p_price,
                :p_tittle,
                :p_contact_name,
                :p_mobile,
                :p_email,
                :p_address,
                :p_detail ,
                :p_vehicle_model,
                :p_vehicle_made_by_id,
                :p_vehicle_body_style_id,
                :p_vehicle_made_year_id,
                :p_vehicle_origin_id,
                :p_vehicle_new_percen,
                :p_vehicle_transmission_id,
                :p_vehicle_driver_type_id,
                :p_vehicle_fuel_type_id,
                :p_vehicle_color_id,
                :p_vehicle_safety,
                :p_vehicle_feature,
                :p_is_upper_today,
                :p_create_date,
                :p_update_date
                )";
// error_log($sql);                    
// error_log(Zend_Json::encode($arrData));
            // Prepare store procude
            $stmt = $storage->prepare($sql);

            $stmt->bindParam(':p_account_id', $arrData['account_id'], PDO::PARAM_INT);
            $stmt->bindParam(':p_news_type', $arrData['news_type'], PDO::PARAM_INT);
            $stmt->bindParam(':p_cat_id', $arrData['catId'], PDO::PARAM_INT);
            $stmt->bindParam(':p_sub_cat_id', $arrData['subCatId'], PDO::PARAM_INT);
            $stmt->bindParam(':p_city_id', $arrData['cityId'], PDO::PARAM_INT);
            $stmt->bindParam(':p_district_id', $arrData['districtId'], PDO::PARAM_INT);
            $stmt->bindParam(':p_ward_id', $arrData['wardId'], PDO::PARAM_INT);
            $stmt->bindParam(':p_price', $arrData['price'], PDO::PARAM_INT);
            $stmt->bindParam(':p_tittle', $arrData['tittle'], PDO::PARAM_STR);
            $stmt->bindParam(':p_contact_name', $arrData['contact_name'], PDO::PARAM_STR);
            $stmt->bindParam(':p_mobile', $arrData['mobile'], PDO::PARAM_STR);
            $stmt->bindParam(':p_email', $arrData['email'], PDO::PARAM_STR);
            $stmt->bindParam(':p_address', $arrData['address'], PDO::PARAM_STR);
            $stmt->bindParam(':p_detail', $arrData['detail'], PDO::PARAM_STR);
            $stmt->bindParam(':p_vehicle_model', $arrData['vehicle_model'], PDO::PARAM_STR);
            $stmt->bindParam(':p_vehicle_made_by_id', $arrData['vehicle_made_by_id'], PDO::PARAM_INT);
            $stmt->bindParam(':p_vehicle_body_style_id', $arrData['vehicle_body_style_id'], PDO::PARAM_INT);
            $stmt->bindParam(':p_vehicle_made_year_id', $arrData['vehicle_made_year_id'], PDO::PARAM_INT);
            $stmt->bindParam(':p_vehicle_origin_id', $arrData['vehicle_origin_id'], PDO::PARAM_INT);
            $stmt->bindParam(':p_vehicle_new_percen', $arrData['vehicle_new_percen'], PDO::PARAM_STR);
            $stmt->bindParam(':p_vehicle_transmission_id', $arrData['vehicle_transmission_id'], PDO::PARAM_INT);
            $stmt->bindParam(':p_vehicle_driver_type_id', $arrData['vehicle_driver_type_id'], PDO::PARAM_INT);
            $stmt->bindParam(':p_vehicle_fuel_type_id', $arrData['vehicle_fuel_type_id'], PDO::PARAM_INT);
            $stmt->bindParam(':p_vehicle_color_id', $arrData['vehicle_color_id'], PDO::PARAM_INT);
            $stmt->bindParam(':p_vehicle_safety', $arrData['vehicle_safety'], PDO::PARAM_STR);
            $stmt->bindParam(':p_vehicle_feature', $arrData['vehicle_feature'], PDO::PARAM_STR);
            $stmt->bindParam(':p_is_upper_today', $arrData['isUpperToday'], PDO::PARAM_INT);
            $stmt->bindParam(':p_create_date', $iCreateDate, PDO::PARAM_INT);
            $stmt->bindParam(':p_update_date', $iCreateDate, PDO::PARAM_INT);
                        
            $stmt->execute();

            // Fetch Result            
            $result = $storage->lastInsertId(); 
            // Free cursor
            $stmt->closeCursor();
        } catch (Exception $ex) {
            Core_Common::var_dump($ex);
            // ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),'',$arrData['name']);
            error_log("error_exception");            
            $result = 0;
        }

        // return data
        return $result;
    }

    public function insertBikeNews($arrData)
    {
         //init return result
        $result = 0;
        $arrData = Validate::encodeValues($arrData);
        try {
            
            $iCreateDate = time();
            // Get Data Master Global
            $storage = Core_Global::getDbGlobalMaster();
            $sql = "INSERT INTO `post_news` ( 
                `news_account_id`,
                `news_type`, 
                `news_cat_id`, 
                `news_sub_cat_id`, 
                `news_city_id`, 
                `news_district_id`,
                `news_price`, 
                `news_tittle`, 
                `news_contact_name`,
                `news_mobile`,
                `news_email`,
                `news_address`,
                `news_detail`,
                `vehicle_made_by_id`,
                `vehicle_made_year_id`,
                `vehicle_origin_id`,
                `vehicle_new_percen`,
                `vehicle_color_id`,
                `news_is_upper_today`,
                `news_create_date`,
                `news_update_date`
                ) VALUES (
                :p_account_id ,
                :p_news_type ,
                :p_cat_id,
                :p_sub_cat_id,
                :p_city_id,
                :p_district_id,
                :p_price,
                :p_tittle,
                :p_contact_name,
                :p_mobile,
                :p_email,
                :p_address,
                :p_detail ,
                :p_vehicle_made_by_id,
                :p_vehicle_made_year_id,
                :p_vehicle_origin_id,
                :p_vehicle_new_percen,                
                :p_vehicle_color_id,                
                :p_is_upper_today,
                :p_create_date,
                :p_update_date
                )";
// error_log($sql);                    
// error_log(Zend_Json::encode($arrData));
            // Prepare store procude
            $stmt = $storage->prepare($sql);

            $stmt->bindParam(':p_account_id', $arrData['account_id'], PDO::PARAM_INT);
            $stmt->bindParam(':p_news_type', $arrData['news_type'], PDO::PARAM_INT);
            $stmt->bindParam(':p_cat_id', $arrData['catId'], PDO::PARAM_INT);
            $stmt->bindParam(':p_sub_cat_id', $arrData['subCatId'], PDO::PARAM_INT);
            $stmt->bindParam(':p_city_id', $arrData['cityId'], PDO::PARAM_INT);
            $stmt->bindParam(':p_district_id', $arrData['districtId'], PDO::PARAM_INT);
            $stmt->bindParam(':p_price', $arrData['price'], PDO::PARAM_INT);
            $stmt->bindParam(':p_tittle', $arrData['tittle'], PDO::PARAM_STR);
            $stmt->bindParam(':p_contact_name', $arrData['contact_name'], PDO::PARAM_STR);
            $stmt->bindParam(':p_mobile', $arrData['mobile'], PDO::PARAM_STR);
            $stmt->bindParam(':p_email', $arrData['email'], PDO::PARAM_STR);
            $stmt->bindParam(':p_address', $arrData['address'], PDO::PARAM_STR);
            $stmt->bindParam(':p_detail', $arrData['detail'], PDO::PARAM_STR);
            $stmt->bindParam(':p_vehicle_made_by_id', $arrData['vehicle_made_by_id'], PDO::PARAM_INT);
            $stmt->bindParam(':p_vehicle_made_year_id', $arrData['vehicle_made_year_id'], PDO::PARAM_INT);
            $stmt->bindParam(':p_vehicle_origin_id', $arrData['vehicle_origin_id'], PDO::PARAM_INT);
            $stmt->bindParam(':p_vehicle_new_percen', $arrData['vehicle_new_percen'], PDO::PARAM_STR);
            $stmt->bindParam(':p_vehicle_color_id', $arrData['vehicle_color_id'], PDO::PARAM_INT);
            $stmt->bindParam(':p_is_upper_today', $arrData['isUpperToday'], PDO::PARAM_INT);
            $stmt->bindParam(':p_create_date', $iCreateDate, PDO::PARAM_INT);
            $stmt->bindParam(':p_update_date', $iCreateDate, PDO::PARAM_INT);
                        
            $stmt->execute();

            // Fetch Result            
            $result = $storage->lastInsertId(); 
            // Free cursor
            $stmt->closeCursor();
        } catch (Exception $ex) {
            Core_Common::var_dump($ex);
            // ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),'',$arrData['name']);
            error_log("error_exception");            
            $result = 0;
        }

        // return data
        return $result;
    }
      
    public function updateJobNews($arrData, $accountId=0) 
    {
         //init return result
        $result = -1;
        $arrData = Validate::encodeValues($arrData);
        try {
            
            $iUpdateDate = time();
            $storage = Core_Global::getDbGlobalMaster();
            $sql = "UPDATE `post_news` SET 
                    `news_cat_id`       = :p_cat_id,
                    `news_sub_cat_id`   = :p_sub_cat_id,
                    `news_city_id`      = :p_city_id,
                    `news_district_id`  = :p_district_id,
                    `news_price`        = :p_price,
                    `news_tittle`       = :p_tittle,
                    `news_contact_name` = :p_contact_name,
                    `news_mobile`       = :p_mobile,
                    `news_email`        = :p_email,
                    `news_address`      = :p_address,
                    `news_detail`       = :p_detail,
                    `job_gender_id`     = :p_job_gender_id,
                    `job_birth_year`    = :p_job_birth_year,
                    `job_type_id`       = :p_job_type_id,
                    `job_cat_id`        = :p_job_cat_id,
                    `job_birth_year_from` = :p_job_birth_year_from,
                    `job_birth_year_to` = :p_job_birth_year_to,
                    `job_experience`    = :p_job_experience,
                    `job_salary_from`   = :p_job_salary_from,
                    `job_salary_to`     = :p_job_salary_to,                    
                    `news_update_date`  = :p_update_date
                    WHERE news_id = :p_news_id ";
            if($accountId > 0){        
                $sql .= " AND `news_account_id` = :p_account_id ";
                $stmt->bindParam(':p_account_id', $accountId, PDO::PARAM_INT);
            }                
            $sql .= " LIMIT 1;";        
            // Prepare store procude
            $stmt = $storage->prepare($sql);

            
            $stmt->bindParam(':p_cat_id', $arrData['catId'], PDO::PARAM_INT);
            $stmt->bindParam(':p_sub_cat_id', $arrData['subCatId'], PDO::PARAM_INT);
            $stmt->bindParam(':p_city_id', $arrData['phone'], PDO::PARAM_INT);
            $stmt->bindParam(':p_district_id', $arrData['districtId'], PDO::PARAM_INT);
            $stmt->bindParam(':p_price', $arrData['price'], PDO::PARAM_INT);
            $stmt->bindParam(':p_tittle', $arrData['tittle'], PDO::PARAM_STR);            
            $stmt->bindParam(':p_contact_name', $arrData['contact_name'], PDO::PARAM_STR);
            $stmt->bindParam(':p_mobile', $arrData['mobile'], PDO::PARAM_STR);
            $stmt->bindParam(':p_email', $arrData['email'], PDO::PARAM_STR);
            $stmt->bindParam(':p_address', $arrData['address'], PDO::PARAM_STR);
            $stmt->bindParam(':p_detail', $arrData['detail'], PDO::PARAM_STR); 

            $stmt->bindParam(':p_job_gender_id', $arrData['job_gender_id'], PDO::PARAM_INT);
            $stmt->bindParam(':p_job_birth_year', $arrData['job_birth_year'], PDO::PARAM_INT);
            $stmt->bindParam(':p_job_type_id', $arrData['job_type_id'], PDO::PARAM_INT);            
            $stmt->bindParam(':p_job_cat_id', $arrData['job_cat_id'], PDO::PARAM_INT);
            $stmt->bindParam(':p_job_birth_year_from', $arrData['job_birth_year_from'], PDO::PARAM_INT);
            $stmt->bindParam(':p_job_birth_year_to', $arrData['job_birth_year_to'], PDO::PARAM_INT);
            $stmt->bindParam(':p_job_experience', $arrData['job_experience'], PDO::PARAM_STR);
            $stmt->bindParam(':p_job_salary_from', $arrData['job_salary_from'], PDO::PARAM_STR);
            $stmt->bindParam(':p_job_salary_to', $arrData['job_salary_to'], PDO::PARAM_STR);

            $stmt->bindParam(':p_update_date', $iUpdateDate, PDO::PARAM_INT);
            $stmt->bindParam(':p_news_id', $arrData['newsId'], PDO::PARAM_INT);
            
            $stmt->execute();

            // Fetch All Result
            $result = $stmt->rowCount();

            // Free cursor
            $stmt->closeCursor();
        } catch (Exception $ex) {
            Core_Common::var_dump($ex);
            // ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),$arrData['account_id'],$arrData['name']);
        	// print_r($ex->getMessage());
            $result = -1;
            // exit();
        }

        // return data
        return $result;
    }
    
    public function updatePropertiesNews($arrData, $accountId=0) 
    {
         //init return result
        $result = -1;
        $arrData = Validate::encodeValues($arrData);
        try {
            
            $iUpdateDate = time();
            $storage = Core_Global::getDbGlobalMaster();
            $sql = "UPDATE `post_news` SET 
                    `news_cat_id`       = :p_cat_id,
                    `news_sub_cat_id`   = :p_sub_cat_id,
                    `news_city_id`      = :p_city_id,
                    `news_district_id`  = :p_district_id,
                    `news_ward_id`      = :p_ward_id,
                    `news_price`        = :p_price,
                    `news_tittle`       = :p_tittle,
                    `news_contact_name` = :p_contact_name,
                    `news_mobile`       = :p_mobile,
                    `news_email`        = :p_email,
                    `news_address`      = :p_address,
                    `news_detail`       = :p_detail,
                    `proper_type_id`    = :p_proper_type_id,
                    `proper_address`    = :p_proper_address,
                    `proper_ward_id`    = :p_proper_ward_id,  
                    `proper_project`    = :p_proper_project,
                    `proper_CT1`        = :p_proper_CT1,
                    `proper_CT2`        = :p_proper_CT2,
                    `proper_CT3_id`     = :p_proper_CT3_id,
                    `proper_CT4_id`     = :p_proper_CT4_id,
                    `proper_CT5`        = :p_proper_CT5,
                    `proper_CT6`        = :p_proper_CT6,
                    `proper_CT7`        = :p_proper_CT7,                    
                    `news_update_date`  = :p_update_date
                    WHERE news_id = :p_news_id ";
            if($accountId > 0){        
                $sql .= " AND `news_account_id` = :p_account_id ";
                $stmt->bindParam(':p_account_id', $accountId, PDO::PARAM_INT);
            }                
            $sql .= " LIMIT 1;";        
            // Prepare store procude
            $stmt = $storage->prepare($sql);

            
            $stmt->bindParam(':p_cat_id', $arrData['catId'], PDO::PARAM_INT);
            $stmt->bindParam(':p_sub_cat_id', $arrData['subCatId'], PDO::PARAM_INT);
            $stmt->bindParam(':p_city_id', $arrData['phone'], PDO::PARAM_INT);
            $stmt->bindParam(':p_district_id', $arrData['districtId'], PDO::PARAM_INT);
            $stmt->bindParam(':p_ward_id', $arrData['wardId'], PDO::PARAM_INT);
            $stmt->bindParam(':p_price', $arrData['price'], PDO::PARAM_INT);
            $stmt->bindParam(':p_tittle', $arrData['tittle'], PDO::PARAM_STR);            
            $stmt->bindParam(':p_contact_name', $arrData['contact_name'], PDO::PARAM_STR);
            $stmt->bindParam(':p_mobile', $arrData['mobile'], PDO::PARAM_STR);
            $stmt->bindParam(':p_email', $arrData['email'], PDO::PARAM_STR);
            $stmt->bindParam(':p_address', $arrData['address'], PDO::PARAM_STR);
            $stmt->bindParam(':p_detail', $arrData['detail'], PDO::PARAM_STR); 

            $stmt->bindParam(':p_proper_type_id', $arrData['proper_type_id'], PDO::PARAM_INT);
            $stmt->bindParam(':p_proper_address', $arrData['proper_address'], PDO::PARAM_STR);
            $stmt->bindParam(':p_proper_ward_id', $arrData['proper_ward_id'], PDO::PARAM_INT);            
            $stmt->bindParam(':p_proper_project', $arrData['proper_project'], PDO::PARAM_STR);
            $stmt->bindParam(':p_proper_CT1', $arrData['proper_CT1'], PDO::PARAM_STR);
            $stmt->bindParam(':p_proper_CT2', $arrData['proper_CT2'], PDO::PARAM_STR);
            $stmt->bindParam(':p_proper_CT3_id', $arrData['proper_CT3_id'], PDO::PARAM_INT);
            $stmt->bindParam(':p_proper_CT4_id', $arrData['proper_CT4_id'], PDO::PARAM_INT);
            $stmt->bindParam(':p_proper_CT5', $arrData['proper_CT5'], PDO::PARAM_STR);
            $stmt->bindParam(':p_proper_CT6', $arrData['proper_CT6'], PDO::PARAM_STR);
            $stmt->bindParam(':p_proper_CT7', $arrData['proper_CT7'], PDO::PARAM_STR);

            $stmt->bindParam(':p_update_date', $iUpdateDate, PDO::PARAM_INT);
            $stmt->bindParam(':p_news_id', $arrData['newsId'], PDO::PARAM_INT);
            
            $stmt->execute();

            // Fetch All Result
            $result = $stmt->rowCount();

            // Free cursor
            $stmt->closeCursor();
        } catch (Exception $ex) {
            Core_Common::var_dump($ex);
            // ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),$arrData['account_id'],$arrData['name']);
            // print_r($ex->getMessage());
            $result = -1;
            // exit();
        }

        // return data
        return $result;
    }

    public function updateCarNews($arrData, $accountId=0) 
    {
         //init return result
        $result = -1;
        $arrData = Validate::encodeValues($arrData);
        try {
            
            $iUpdateDate = time();
            $storage = Core_Global::getDbGlobalMaster();
            $sql = "UPDATE `post_news` SET 
                    `news_cat_id`       = :p_cat_id,
                    `news_sub_cat_id`   = :p_sub_cat_id,
                    `news_city_id`      = :p_city_id,
                    `news_district_id`  = :p_district_id,
                    `news_ward_id`      = :p_ward_id,
                    `news_price`        = :p_price,
                    `news_tittle`       = :p_tittle,
                    `news_contact_name` = :p_contact_name,
                    `news_mobile`       = :p_mobile,
                    `news_email`        = :p_email,
                    `news_address`      = :p_address,
                    `news_detail`       = :p_detail,
                    `vehicle_model`     = :p_vehicle_model,
                    `vehicle_made_by_id`= :p_vehicle_made_by_id,
                    `vehicle_body_style_id` = :p_vehicle_body_style_id,
                    `vehicle_made_year_id`  = :p_vehicle_made_year_id,
                    `vehicle_origin_id`     = :p_vehicle_origin_id,
                    `vehicle_new_percen`    = :p_vehicle_new_percen,
                    `vehicle_transmission_id`= :p_vehicle_transmission_id,
                    `vehicle_driver_type_id`= :p_vehicle_driver_type_id,
                    `vehicle_fuel_type_id`  = :p_vehicle_fuel_type_id,
                    `vehicle_color_id`      = :p_vehicle_color_id,
                    `vehicle_safety`        = :p_vehicle_safety,
                    `vehicle_feature`       = :p_vehicle_feature,                    
                    `news_update_date`  = :p_update_date
                    WHERE news_id = :p_news_id ";
            if($accountId > 0){        
                $sql .= " AND `news_account_id` = :p_account_id ";
                $stmt->bindParam(':p_account_id', $accountId, PDO::PARAM_INT);
            }                
            $sql .= " LIMIT 1;";        
            // Prepare store procude
            $stmt = $storage->prepare($sql);

            
            $stmt->bindParam(':p_cat_id', $arrData['catId'], PDO::PARAM_INT);
            $stmt->bindParam(':p_sub_cat_id', $arrData['subCatId'], PDO::PARAM_INT);
            $stmt->bindParam(':p_city_id', $arrData['phone'], PDO::PARAM_INT);
            $stmt->bindParam(':p_district_id', $arrData['districtId'], PDO::PARAM_INT);
            $stmt->bindParam(':p_ward_id', $arrData['wardId'], PDO::PARAM_INT);
            $stmt->bindParam(':p_price', $arrData['price'], PDO::PARAM_INT);
            $stmt->bindParam(':p_tittle', $arrData['tittle'], PDO::PARAM_STR);            
            $stmt->bindParam(':p_contact_name', $arrData['contact_name'], PDO::PARAM_STR);
            $stmt->bindParam(':p_mobile', $arrData['mobile'], PDO::PARAM_STR);
            $stmt->bindParam(':p_email', $arrData['email'], PDO::PARAM_STR);
            $stmt->bindParam(':p_address', $arrData['address'], PDO::PARAM_STR);
            $stmt->bindParam(':p_detail', $arrData['detail'], PDO::PARAM_STR); 
            
            $stmt->bindParam(':p_vehicle_model', $arrData['vehicle_model'], PDO::PARAM_STR);
            $stmt->bindParam(':p_vehicle_made_by_id', $arrData['vehicle_made_by_id'], PDO::PARAM_INT);
            $stmt->bindParam(':p_vehicle_body_style_id', $arrData['vehicle_body_style_id'], PDO::PARAM_INT);            
            $stmt->bindParam(':p_vehicle_made_year_id', $arrData['vehicle_made_year_id'], PDO::PARAM_INT);
            $stmt->bindParam(':p_vehicle_origin_id', $arrData['vehicle_origin_id'], PDO::PARAM_INT);
            $stmt->bindParam(':p_vehicle_new_percen', $arrData['vehicle_new_percen'], PDO::PARAM_STR);
            $stmt->bindParam(':p_vehicle_transmission_id', $arrData['vehicle_transmission_id'], PDO::PARAM_INT);
            $stmt->bindParam(':p_vehicle_driver_type_id', $arrData['vehicle_driver_type_id'], PDO::PARAM_INT);
            $stmt->bindParam(':p_vehicle_fuel_type_id', $arrData['vehicle_fuel_type_id'], PDO::PARAM_INT);
            $stmt->bindParam(':p_vehicle_color_id', $arrData['vehicle_color_id'], PDO::PARAM_INT);
            $stmt->bindParam(':p_vehicle_safety', $arrData['vehicle_safety'], PDO::PARAM_STR);
            $stmt->bindParam(':p_vehicle_feature', $arrData['vehicle_feature'], PDO::PARAM_STR);

            $stmt->bindParam(':p_update_date', $iUpdateDate, PDO::PARAM_INT);
            $stmt->bindParam(':p_news_id', $arrData['newsId'], PDO::PARAM_INT);
            
            $stmt->execute();

            // Fetch All Result
            $result = $stmt->rowCount();

            // Free cursor
            $stmt->closeCursor();
        } catch (Exception $ex) {
            Core_Common::var_dump($ex);
            // ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),$arrData['account_id'],$arrData['name']);
            // print_r($ex->getMessage());
            $result = -1;
            // exit();
        }

        // return data
        return $result;
    }

    public function updateBikeNews($arrData, $accountId=0) 
    {
         //init return result
        $result = -1;
        $arrData = Validate::encodeValues($arrData);
        try {
            
            $iUpdateDate = time();
            $storage = Core_Global::getDbGlobalMaster();
            $sql = "UPDATE `post_news` SET 
                    `news_cat_id`       = :p_cat_id,
                    `news_sub_cat_id`   = :p_sub_cat_id,
                    `news_city_id`      = :p_city_id,
                    `news_district_id`  = :p_district_id,
                    `news_ward_id`      = :p_ward_id,
                    `news_price`        = :p_price,
                    `news_tittle`       = :p_tittle,
                    `news_contact_name` = :p_contact_name,
                    `news_mobile`       = :p_mobile,
                    `news_email`        = :p_email,
                    `news_address`      = :p_address,
                    `news_detail`       = :p_detail,
                    `vehicle_made_by_id`= :p_vehicle_made_by_id,
                    `vehicle_made_year_id`  = :p_vehicle_made_year_id,
                    `vehicle_origin_id`     = :p_vehicle_origin_id,
                    `vehicle_new_percen`    = :p_vehicle_new_percen,
                    `vehicle_color_id`      = :p_vehicle_color_id,
                    `news_update_date`  = :p_update_date
                    WHERE news_id = :p_news_id ";
            if($accountId > 0){        
                $sql .= " AND `news_account_id` = :p_account_id ";
                $stmt->bindParam(':p_account_id', $accountId, PDO::PARAM_INT);
            }                
            $sql .= " LIMIT 1;";        
            // Prepare store procude
            $stmt = $storage->prepare($sql);

            
            $stmt->bindParam(':p_cat_id', $arrData['catId'], PDO::PARAM_INT);
            $stmt->bindParam(':p_sub_cat_id', $arrData['subCatId'], PDO::PARAM_INT);
            $stmt->bindParam(':p_city_id', $arrData['phone'], PDO::PARAM_INT);
            $stmt->bindParam(':p_district_id', $arrData['districtId'], PDO::PARAM_INT);
            $stmt->bindParam(':p_ward_id', $arrData['wardId'], PDO::PARAM_INT);
            $stmt->bindParam(':p_price', $arrData['price'], PDO::PARAM_INT);
            $stmt->bindParam(':p_tittle', $arrData['tittle'], PDO::PARAM_STR);            
            $stmt->bindParam(':p_contact_name', $arrData['contact_name'], PDO::PARAM_STR);
            $stmt->bindParam(':p_mobile', $arrData['mobile'], PDO::PARAM_STR);
            $stmt->bindParam(':p_email', $arrData['email'], PDO::PARAM_STR);
            $stmt->bindParam(':p_address', $arrData['address'], PDO::PARAM_STR);
            $stmt->bindParam(':p_detail', $arrData['detail'], PDO::PARAM_STR); 
            
            $stmt->bindParam(':p_vehicle_made_by_id', $arrData['vehicle_made_by_id'], PDO::PARAM_INT);
            $stmt->bindParam(':p_vehicle_made_year_id', $arrData['vehicle_made_year_id'], PDO::PARAM_INT);
            $stmt->bindParam(':p_vehicle_origin_id', $arrData['vehicle_origin_id'], PDO::PARAM_INT);
            $stmt->bindParam(':p_vehicle_new_percen', $arrData['vehicle_new_percen'], PDO::PARAM_STR);
            $stmt->bindParam(':p_vehicle_color_id', $arrData['vehicle_color_id'], PDO::PARAM_INT);

            $stmt->bindParam(':p_update_date', $iUpdateDate, PDO::PARAM_INT);
            $stmt->bindParam(':p_news_id', $arrData['newsId'], PDO::PARAM_INT);
            
            $stmt->execute();

            // Fetch All Result
            $result = $stmt->rowCount();

            // Free cursor
            $stmt->closeCursor();
        } catch (Exception $ex) {
            Core_Common::var_dump($ex);
            // ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),$arrData['account_id'],$arrData['name']);
            // print_r($ex->getMessage());
            $result = -1;
            // exit();
        }

        // return data
        return $result;
    }

     /**
     * @return <int>
     */
    public function updateUpperNews($newsID, $upperToday = 1)
    {
         //init return result
        $result = -1;
        try {
            
            $iUpdateDate = time();
            
            // Get Data Master Global
            $storage = Core_Global::getDbGlobalMaster();
            
            $sql = "UPDATE `post_news` SET 
                        `news_is_upper_today` = :p_news_is_upper_today,
                        `news_update_date` = :p_update_date
                    WHERE `news_id` = :p_news_id
                    LIMIT 1    
                    ";

            // Prepare store procude
            $stmt = $storage->prepare($sql);
                        
            $stmt->bindParam(':p_news_id', $newsID, PDO::PARAM_INT);
            $stmt->bindParam(':p_news_is_upper_today', $upperToday, PDO::PARAM_INT);
            $stmt->bindParam(':p_update_date', $iUpdateDate, PDO::PARAM_INT);
            $stmt->execute();

            // Fetch All Result
            $result = $stmt->rowCount();

            // Free cursor
            $stmt->closeCursor();
        } catch (Exception $ex) {
            // ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),$arrData['account_id'], $arrData['name']);
            $result = -1;
        }

        // return data
        return $result;
    }

    public function updateStatus($newsID, $active = 1)
    {
         //init return result
        $result = -1;
        try {
            
            $iUpdateDate = time();
            
            // Get Data Master Global
            $storage = Core_Global::getDbGlobalMaster();
            
            $sql = "UPDATE `post_news` SET 
                        `news_status` = :p_news_status,
                        `news_update_date` = :p_update_date
                    WHERE `news_id` = :p_news_id
                    LIMIT 1    
                    ";

            // Prepare store procude
            $stmt = $storage->prepare($sql);
                        
            $stmt->bindParam(':p_news_id', $newsID, PDO::PARAM_INT);
            $stmt->bindParam(':p_news_status', $active, PDO::PARAM_INT);
            $stmt->bindParam(':p_update_date', $iUpdateDate, PDO::PARAM_INT);
            $stmt->execute();

            // Fetch All Result
            $result = $stmt->rowCount();

            // Free cursor
            $stmt->closeCursor();
        } catch (Exception $ex) {
            // ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),$arrData['account_id'], $arrData['name']);
            $result = -1;
        }

        // return data
        return $result;
    }
    
    /**
     * @todo  Remove post news
     * @param <int> $newsId
     * @return <int>
     */
    public function removeNews($newsID) {
        $result = -1;
        try {
            // Get Data Master Global
            $storage = Core_Global::getDbGlobalMaster();
            $sql = "DELETE FROM `post_news` 
                    WHERE `news_id` = :p_news_id
                    LIMIT 1";
            // Prepare store procude
            $stmt = $storage->prepare($sql);
            $stmt->bindParam(':p_news_id', $newsID, PDO::PARAM_INT);
            $stmt->execute();

            // Fetch All Result            
            $result = 1;

            // Free cursor
            $stmt->closeCursor();
        } catch (Exception $ex) {
            ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),$iAccountID);
            $result = -1;
        }

        // return data
        return $result;
    }

    public function countNewsList($catId="", $subCatId="", $cityId="", $districtId="", $priceFrom="", $priceTo="", $active="", $txtSearch="", $accountId="") {
       
        $arrResult = array();
        $queryWhere = " WHERE 1=1 ";
        $arrParams = array(); 
        $totals = 0;
        try {          
                        
            // Get Data Master Global
            $storage = Core_Global::getDbGlobalSlave();

            $sql = "SELECT COUNT(`news_id`) as totals 
                    FROM `post_news`                    
                    ";

            //search by accountId
            if(!empty($accountId)){
                $queryWhere .= " AND `news_account_id` LIKE :p_accountID ";
                $arrParams[":p_accountID"] = "%".$accountId."%";
            }
            //search by catId
            if(!empty($catId)){
                $queryWhere .= " AND `news_cat_id` = :p_cat_id ";
                $arrParams[":p_cat_id"] = $catId;
            }
            //search by catId
            if(!empty($subCatId)){
                $queryWhere .= " AND `news_sub_cat_id` = :p_sub_cat_id ";
                $arrParams[":p_sub_cat_id"] = $subCatId;
            }
            //search by cityId
            if(!empty($cityId)){
                $queryWhere .= " AND `news_city_id` = :p_city_id ";
                $arrParams[":p_city_id"] = $cityId;
            }
            //search by districtId
            if(!empty($districtId)){
                $queryWhere .= " AND `news_district_id` = :p_district_id ";
                $arrParams[":p_district_id"] = $districtId;
            }
            
            if(!empty($priceFrom) && !empty($priceTo)){
                $queryWhere .= " AND `news_price` >= :p_news_price_from ";
                $queryWhere .= " AND `news_price` <= :p_news_price_to ";
                $arrParams[":p_news_price_from"] = $priceFrom;
                $arrParams[":p_news_price_to"] = $priceTo;
            }
            elseif(!empty($priceFrom)){
                $queryWhere .= " AND `news_price` >= :p_news_price_from ";
                $arrParams[":p_news_price_from"] = $priceFrom;
            }
            elseif(!empty($priceTo)){
                $queryWhere .= " AND `news_price` <= :p_news_price_to ";
                $arrParams[":p_news_price_to"] = $priceTo;
            }            
            //search by status
            if(!empty($active)){
                $queryWhere .= " AND `news_status` = :p_active ";
                $arrParams[":p_active"] = $active;
            }
            //search by name, username
            if(!empty($txtSearch)){
                $queryWhere .= " AND ( `news_price` LIKE :p_txtSearch ";
                $queryWhere .= "     OR `news_tensp` LIKE :p_txtSearch ";
                $queryWhere .= "     OR `news_tittle` LIKE :p_txtSearch ";
                $queryWhere .= "     OR `news_contact_name` LIKE :p_txtSearch ";
                $queryWhere .= "     OR `news_mobile` LIKE :p_txtSearch ";
                $queryWhere .= "     OR `news_email` LIKE :p_txtSearch ";
                $queryWhere .= "     OR `news_address` LIKE :p_txtSearch ";
                $queryWhere .= "     OR `news_detail` LIKE :p_txtSearch ";
                // $queryWhere .= "     OR `` LIKE :p_txtSearch ";
                $queryWhere .= " ) ";    
                $arrParams[":p_txtSearch"] = "%".$txtSearch."%";
                // $arrParams[":p_username"] = "%".$sName."%";
            }
            $sql .= $queryWhere;
error_log("sql = ".$sql);                      
            // Prepare store procude
            $stmt = $storage->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

            $stmt->execute($arrParams);

            // Fetch All Result
            $arrResult = $stmt->fetch();
            
            $totals = $arrResult["totals"];
            // Free cursor
            $stmt->closeCursor();                                    
            
        } catch (Exception $ex) {
            Core_Common::var_dump($ex);
            // ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),0,$sName);
            $totals = 0;
        }

        // return data
        return $totals;
    }

    public function getNewsList($catId="", $subCatId="", $cityId="", $districtId="", $priceFrom="", $priceTo="", $active="", $txtSearch="", $accountId="",  $sSortField="", $sSortType="", $iOffset=0, $iLimit=20) {
       
        $arrResult = array();
        $queryWhere = " WHERE 1=1 ";
        $arrParams = array(); 
        try {          
                        
            // Get Data Master Global
            $storage = Core_Global::getDbGlobalSlave();

            $sql = "SELECT *
                    FROM `post_news`                    
                    ";

            //search by accountId
            if(!empty($accountId)){
                $queryWhere .= " AND `news_account_id` LIKE :p_accountID ";
                $arrParams[":p_accountID"] = "%".$accountId."%";
            }
            //search by catId
            if(!empty($catId)){
                $queryWhere .= " AND `news_cat_id` = :p_cat_id ";
                $arrParams[":p_cat_id"] = $catId;
                
            }
            //search by catId
            if(!empty($subCatId)){
                $queryWhere .= " AND `news_sub_cat_id` = :p_sub_cat_id ";
                $arrParams[":p_sub_cat_id"] = $subCatId;
            }
            //search by cityId
            if(!empty($cityId)){
                $queryWhere .= " AND `news_city_id` = :p_city_id ";
                $arrParams[":p_city_id"] = $cityId;
            }
            //search by districtId
            if(!empty($districtId)){
                $queryWhere .= " AND `news_district_id` = :p_district_id ";
                $arrParams[":p_district_id"] = $districtId;
            }
            
            if(!empty($priceFrom) && !empty($priceTo)){
                $queryWhere .= " AND `news_price` >= :p_news_price_from ";
                $queryWhere .= " AND `news_price` <= :p_news_price_to ";
                $arrParams[":p_news_price_from"] = $priceFrom;
                $arrParams[":p_news_price_to"] = $priceTo;
            }
            elseif(!empty($priceFrom)){
                $queryWhere .= " AND `news_price` >= :p_news_price_from ";
                $arrParams[":p_news_price_from"] = $priceFrom;
            }
            elseif(!empty($priceTo)){
                $queryWhere .= " AND `news_price` <= :p_news_price_to ";
                $arrParams[":p_news_price_to"] = $priceTo;
            }            
            //search by status
            if(!empty($active)){
                $queryWhere .= " AND `news_status` = :p_active ";
                $arrParams[":p_active"] = $active;
            }
            //search by name, username
            if(!empty($txtSearch)){
                $queryWhere .= " AND ( `news_price` LIKE :p_txtSearch ";
                $queryWhere .= "     OR `news_tensp` LIKE :p_txtSearch ";
                $queryWhere .= "     OR `news_tittle` LIKE :p_txtSearch ";
                $queryWhere .= "     OR `news_contact_name` LIKE :p_txtSearch ";
                $queryWhere .= "     OR `news_mobile` LIKE :p_txtSearch ";
                $queryWhere .= "     OR `news_email` LIKE :p_txtSearch ";
                $queryWhere .= "     OR `news_address` LIKE :p_txtSearch ";
                $queryWhere .= "     OR `news_detail` LIKE :p_txtSearch ";
                // $queryWhere .= "     OR `` LIKE :p_txtSearch ";
                $queryWhere .= " ) ";    
                $arrParams[":p_txtSearch"] = "%".$txtSearch."%";
            }
            $sql .= $queryWhere;

            //Order by
            if(!empty($sSortField) && !empty($sSortType)){
                $sql .= " ORDER BY :p_sort_field :p_sort_type ";
                $arrParams[":p_sort_field"] = $sSortField;
                $arrParams[":p_sort_type"] = $sSortType;
            }            
            $sql .= " LIMIT ". (int)$iOffset . ", ". (int)$iLimit;
            
// error_log("sql=".$sql);            
// error_log("params=".json_encode($arrParams));
            // Prepare store procude
            // $stmt = $storage->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $txtSearch = "%".$txtSearch."%";
            $stmt = $storage->prepare($sql);
            /*$stmt->bindValue(':p_cat_id', (int)$catId, PDO::PARAM_INT);
            $stmt->bindValue(':p_txtSearch', $txtSearch, PDO::PARAM_STR);
            $stmt->bindValue(':p_sort_field', $sSortField, PDO::PARAM_STR);
            $stmt->bindValue(':p_sort_type', $sSortType, PDO::PARAM_STR);
            $stmt->bindValue(':p_offset', (int)$iOffset, PDO::PARAM_INT);
            $stmt->bindValue(':p_limit', (int)$iLimit, PDO::PARAM_INT);
            $stmt->execute();*/
            $stmt->execute($arrParams);

            // Fetch All Result
            $arrResult = $stmt->fetchAll();
            
            // Free cursor
            $stmt->closeCursor();                                   
            
        } catch (Exception $ex) {
            Core_Common::var_dump($ex);
            // ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),0,$sName);
            $arrResult = array();
        }

        // return data
        return $arrResult;
    }

    /*
     * 
     */
     public function getNewsByID($newsID, $active = 1) {
     	
     	$arrResult = array();
     	
        try {

            // Get Data Master Global
            $storage = Core_Global::getDbGlobalSlave();

            $sql = "SELECT * 
                    FROM `post_news` 
                    WHERE `news_id` = :p_news_id
                    AND `news_status` = :p_active
                    LIMIT 1";

            // Prepare store procude
            $stmt = $storage->prepare($sql);
            $stmt->bindParam(':p_news_id', $newsID, PDO::PARAM_INT);
            $stmt->bindParam(':p_active', $active, PDO::PARAM_INT);
            $stmt->execute();

            // Fetch All Result
            $arrResult = $stmt->fetch();

            // Free cursor
            $stmt->closeCursor();
        } catch (Exception $ex) {
            // ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),$iAccountID);
            $arrResult = array();
        }

        // return data
        return $arrResult;
    }
    

}