<?php
/**
 * @author      :   HoaiTN
 * @name        :   Core_Business_Api_Account
 * @version     :   20101111
 * @copyright   :   My company
 * @todo        :   Using for account service
 */
class Core_Business_Api_AccountInfo
{
   /**
     *
     * @var <type>
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
     * @return <object>
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
     * @return <int>
     */
    public function insertAccountInfo($arrData)
    {
        
   
         //init return result
        $result = 0;
        $arrData['leader_id'] = isset($arrData['leader_id'])?$arrData['leader_id']:0;
        $arrData['manager_id'] = isset($arrData['manager_id'])?$arrData['manager_id']:0;
        $arrData = Validate::encodeValues($arrData);
        try {
            
            $iCreateDate = time();
            
            # Get Data Master Global
            $storage = Core_Global::getDbGlobalMaster();
                    
            # Prepare store procude
            $stmt = $storage->prepare("CALL sp_account_info_insert(:p_name,:p_email,:p_phone,:p_birthday,
                :p_picture,:p_avatar,:p_id,:p_identity,:p_tax_code,:p_address,:p_position,:p_department_id,:p_team_id,:p_leader_id,:p_manager_id,:p_direct_manager,:p_skype_account,
                :p_mobion_account,:p_start_date,:p_end_date,:p_contract_type,:p_contract_sign_date,:p_country_id,
                :p_description, :p_status, :p_active,:p_username,:p_team_name,:p_manager_type,:p_create_date, :p_update_date,
            	:p_first_name, :p_last_name,
            	@p_RowCount)");
            
            
            $stmt->bindParam('p_name', $arrData['name'], PDO::PARAM_STR);
            $stmt->bindParam('p_email', $arrData['email'], PDO::PARAM_STR);
            $stmt->bindParam('p_phone', $arrData['phone'], PDO::PARAM_STR);
            $stmt->bindParam('p_birthday', $arrData['birthday'], PDO::PARAM_STR);
            $stmt->bindParam('p_picture', $arrData['picture'], PDO::PARAM_STR);
            $stmt->bindParam('p_avatar', $arrData['avatar'], PDO::PARAM_STR);
            $stmt->bindParam('p_id', $arrData['id'], PDO::PARAM_INT);
            $stmt->bindParam('p_identity', $arrData['identity'], PDO::PARAM_STR);
            $stmt->bindParam('p_tax_code', $arrData['tax_code'], PDO::PARAM_STR);
            $stmt->bindParam('p_address', $arrData['address'], PDO::PARAM_STR);
            $stmt->bindParam('p_position', $arrData['position'], PDO::PARAM_INT);
            $stmt->bindParam('p_department_id', $arrData['department_id'], PDO::PARAM_INT);
            $stmt->bindParam('p_team_id', $arrData['team_id'], PDO::PARAM_INT);
            $stmt->bindParam('p_leader_id', $arrData['leader_id'], PDO::PARAM_INT);
            $stmt->bindParam('p_manager_id', $arrData['manager_id'], PDO::PARAM_INT);
            
            $stmt->bindParam('p_direct_manager', $arrData['direct_manager'], PDO::PARAM_INT);
            $stmt->bindParam('p_skype_account', $arrData['skype_account'], PDO::PARAM_STR);
            $stmt->bindParam('p_mobion_account', $arrData['mobion_account'], PDO::PARAM_STR);
            
            $stmt->bindParam('p_start_date', $arrData['start_date'], PDO::PARAM_STR);
            $stmt->bindParam('p_end_date', $arrData['end_date'], PDO::PARAM_STR);
            $stmt->bindParam('p_contract_type', $arrData['contract_type'], PDO::PARAM_INT);
            $stmt->bindParam('p_contract_sign_date', $arrData['contract_sign_date'], PDO::PARAM_STR);
            $stmt->bindParam('p_country_id', $arrData['country_id'], PDO::PARAM_INT);
            $stmt->bindParam('p_description', $arrData['description'], PDO::PARAM_STR);
            $stmt->bindParam('p_status', $arrData['status'], PDO::PARAM_INT);
            $stmt->bindParam('p_active', $arrData['active'], PDO::PARAM_INT);
            
            
            $stmt->bindParam('p_username', $arrData['username'], PDO::PARAM_STR);
            $stmt->bindParam('p_team_name', $arrData['team_name'], PDO::PARAM_STR);
            $stmt->bindParam('p_manager_type', $arrData['manager_type'], PDO::PARAM_INT);
            
            $stmt->bindParam('p_create_date', $iCreateDate, PDO::PARAM_INT);
            $stmt->bindParam('p_update_date', $iCreateDate, PDO::PARAM_INT);
            
            $stmt->bindParam('p_first_name', $arrData['first_name'], PDO::PARAM_STR);
            $stmt->bindParam('p_last_name', $arrData['last_name'], PDO::PARAM_STR);
            
            $stmt->execute();

            # Fetch All Result
            $stmt = $storage->query("SELECT @p_RowCount");
            $result = $stmt->fetchColumn();
            

            # Free cursor
            $stmt->closeCursor();
        } catch (Exception $ex) {
            Core_Common::var_dump($ex);
            ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),'',$arrData['name']);
            $result = 0;
        }

        // return data
        return $result;
    }
    
    
    
        /**
     * @return <int>
     */
    public function updateAccountInfo($arrData) 
    {
         //init return result
        $result = 0;
        $arrData['leader_id'] = isset($arrData['leader_id'])?$arrData['leader_id']:0;
        $arrData['manager_id'] = isset($arrData['manager_id'])?$arrData['manager_id']:0;
        $arrData['end_probation'] = isset($arrData['end_probation'])?$arrData['end_probation']:0;
        $arrData = Validate::encodeValues($arrData);
        try {
            
            $iUpdateDate = time();
           // IN p_gender INT, IN p_place_of_birth INT, IN p_home_town INT, IN p_identity_date DATE, IN p_identity_place INT, IN p_passport VARCHAR(20), IN p_passport_date DATE, IN p_social_insurance VARCHAR(20), IN p_bank_account VARCHAR(20), IN p_bank_account_id INT, IN p_bank_account_branch INT, IN p_marital_status INT, IN p_no_of_children INT, IN p_level INT, OUT p_RowCount TINYINT(1)
           //IN p_first_name VARCHAR(255), IN p_last_name VARCHAR(255), IN p_personal_email VARCHAR(255), IN p_contact_name VARCHAR(255), IN p_contact_address VARCHAR(255), IN p_contact_phone VARCHAR(11),
           
            $storage = Core_Global::getDbGlobalMaster();
                    
            # Prepare store procude
            $stmt = $storage->prepare("CALL sp_account_info_update(:p_account_id,:p_name, :p_email, :p_phone,:p_birthday,
                :p_picture,:p_avatar,:p_id,:p_identity,:p_tax_code,:p_address,:p_position,:p_department_id,:p_team_id,:p_leader_id,:p_manager_id,:p_direct_manager,:p_skype_account,
                :p_mobion_account,:p_start_date,:p_end_date,:p_contract_type,
                :p_end_probation, :p_date_end_probation, :p_contract_sign_date,:p_country_id,
                :p_description, :p_status, :p_active,:p_manager_type,:p_top_people, :p_update_date,
            	:p_gender, :p_place_of_birth, :p_home_town, :p_identity_date, :p_identity_place, :p_passport_place, :p_passport, :p_passport_date, :p_social_insurance,
                :p_bank_account, :p_bank_account_id, :p_bank_account_branch, :p_marital_status, :p_no_of_children, :p_level,
                :p_first_name, :p_last_name, :p_personal_email, :p_contact_name, :p_contact_address, :p_contact_phone, :p_contact_relationship, :p_team_name,
            	@p_RowCount)");

            $arrData['date_end_probation'] = isset($arrData['date_end_probation']) ? $arrData['date_end_probation'] : '0000-00-00';

            $stmt->bindParam('p_account_id', $arrData['account_id'], PDO::PARAM_INT);
            $stmt->bindParam('p_name', $arrData['name'], PDO::PARAM_STR);
            $stmt->bindParam('p_email', $arrData['email'], PDO::PARAM_STR);
            $stmt->bindParam('p_phone', $arrData['phone'], PDO::PARAM_STR);
            $stmt->bindParam('p_birthday', $arrData['birthday'], PDO::PARAM_STR);
            $stmt->bindParam('p_picture', $arrData['picture'], PDO::PARAM_STR);
            $stmt->bindParam('p_avatar', $arrData['avatar'], PDO::PARAM_STR);
            
            $stmt->bindParam('p_id', $arrData['id'], PDO::PARAM_INT);
            $stmt->bindParam('p_identity', $arrData['identity'], PDO::PARAM_STR);
            $stmt->bindParam('p_tax_code', $arrData['tax_code'], PDO::PARAM_STR);
            $stmt->bindParam('p_address', $arrData['address'], PDO::PARAM_STR);
            $stmt->bindParam('p_position', $arrData['position'], PDO::PARAM_INT);
            $stmt->bindParam('p_department_id', $arrData['department_id'], PDO::PARAM_INT);
            $stmt->bindParam('p_team_id', $arrData['team_id'], PDO::PARAM_INT);
            $stmt->bindParam('p_leader_id', $arrData['leader_id'], PDO::PARAM_INT);
            $stmt->bindParam('p_manager_id', $arrData['manager_id'], PDO::PARAM_INT);
            
            $stmt->bindParam('p_direct_manager', $arrData['direct_manager'], PDO::PARAM_INT);
            $stmt->bindParam('p_skype_account', $arrData['skype_account'], PDO::PARAM_STR);
            $stmt->bindParam('p_mobion_account', $arrData['mobion_account'], PDO::PARAM_STR);
             
            $stmt->bindParam('p_start_date', $arrData['start_date'], PDO::PARAM_STR);
            $stmt->bindParam('p_end_date', $arrData['end_date'], PDO::PARAM_STR);
            $stmt->bindParam('p_contract_type', $arrData['contract_type'], PDO::PARAM_INT);
            $stmt->bindParam('p_end_probation', $arrData['end_probation'], PDO::PARAM_INT);
            $stmt->bindParam('p_date_end_probation', $arrData['date_end_probation'], PDO::PARAM_STR);
            $stmt->bindParam('p_contract_sign_date', $arrData['contract_sign_date'], PDO::PARAM_STR);
            $stmt->bindParam('p_country_id', $arrData['country_id'], PDO::PARAM_INT);
            $stmt->bindParam('p_description', $arrData['description'], PDO::PARAM_STR);
            $stmt->bindParam('p_status', $arrData['status'], PDO::PARAM_INT);
            $stmt->bindParam('p_active', $arrData['active'], PDO::PARAM_INT);
            $stmt->bindParam('p_manager_type', $arrData['manager_type'], PDO::PARAM_INT);
            $stmt->bindParam('p_top_people', $arrData['top_people'], PDO::PARAM_INT);
            $stmt->bindParam('p_update_date', $iUpdateDate, PDO::PARAM_INT);
            
            $stmt->bindParam('p_gender', $arrData['gender'], PDO::PARAM_INT);
            $stmt->bindParam('p_place_of_birth', $arrData['place_of_birth'], PDO::PARAM_STR);
            $stmt->bindParam('p_home_town', $arrData['home_town'], PDO::PARAM_INT);
            $stmt->bindParam('p_identity_date', $arrData['identity_date'], PDO::PARAM_STR);
            $stmt->bindParam('p_identity_place', $arrData['identity_place'], PDO::PARAM_INT);
            $stmt->bindParam('p_passport', $arrData['passport'], PDO::PARAM_STR);
            $stmt->bindParam('p_passport_date', $arrData['passport_date'], PDO::PARAM_STR);
            $stmt->bindParam('p_passport_place', $arrData['passport_place'], PDO::PARAM_INT);
            $stmt->bindParam('p_social_insurance', $arrData['social_insurance'], PDO::PARAM_STR);
            $stmt->bindParam('p_bank_account', $arrData['bank_account'], PDO::PARAM_STR);
            $stmt->bindParam('p_bank_account_id', $arrData['bank_account_id'], PDO::PARAM_INT);
            $stmt->bindParam('p_bank_account_branch', $arrData['bank_account_branch'], PDO::PARAM_INT);
            $stmt->bindParam('p_marital_status', $arrData['marital_status'], PDO::PARAM_INT);
            $stmt->bindParam('p_no_of_children', $arrData['no_of_children'], PDO::PARAM_INT);
            $stmt->bindParam('p_level', $arrData['level'], PDO::PARAM_INT);
            
            $stmt->bindParam('p_first_name', $arrData['first_name'], PDO::PARAM_STR);
            $stmt->bindParam('p_last_name', $arrData['last_name'], PDO::PARAM_STR);
            $stmt->bindParam('p_personal_email', $arrData['personal_email'], PDO::PARAM_STR);
            $stmt->bindParam('p_contact_name', $arrData['contact_name'], PDO::PARAM_STR);
            $stmt->bindParam('p_contact_address', $arrData['contact_address'], PDO::PARAM_STR);
            $stmt->bindParam('p_contact_phone', $arrData['contact_phone'], PDO::PARAM_STR);
            $stmt->bindParam('p_contact_relationship', $arrData['contact_relationship'], PDO::PARAM_STR);
            $stmt->bindParam('p_team_name', $arrData['team_name'], PDO::PARAM_STR);
            $stmt->execute();

            # Fetch All Result
            $stmt = $storage->query("SELECT @p_RowCount");
            $result = $stmt->fetchColumn();

            # Free cursor
            $stmt->closeCursor();
        } catch (Exception $ex) {
            Core_Common::var_dump($ex);
            ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),$arrData['account_id'],$arrData['name']);
        	print_r($ex->getMessage());
            $result = -1;
            exit();
        }

        // return data
        return $result;
    }
    
    public function updateGeneral($arrData)
    {
    	//init return result
    	$result = 0;
        $arrData = Validate::encodeValues($arrData);
    	try {
    
    		$iUpdateDate = time();
    	
    		$storage = Core_Global::getDbGlobalMaster();
    
    		# Prepare store procude
    		$stmt = $storage->prepare("CALL sp_account_info_update_general(
    				:p_account_id
    				, :p_first_name
    				, :p_last_name
    				, :p_name
    				, :p_id
    				, :p_team_id
    				, :p_position
    				, :p_email
    				, :p_skype_account
    				, :p_birthday
    				, :p_phone
    				, :p_contact_address
    				, :p_personal_email
    				, :p_picture
            		, @p_RowCount)");
    
    
            $stmt->bindParam('p_account_id', $arrData['account_id'], PDO::PARAM_INT);
            $stmt->bindParam('p_first_name', $arrData['first_name'], PDO::PARAM_STR);
            $stmt->bindParam('p_last_name', $arrData['last_name'], PDO::PARAM_STR);
            $stmt->bindParam('p_name', $arrData['name'], PDO::PARAM_STR);
            $stmt->bindParam('p_id', $arrData['id'], PDO::PARAM_INT);
            $stmt->bindParam('p_team_id', $arrData['team_id'], PDO::PARAM_INT);
            $stmt->bindParam('p_position', $arrData['position'], PDO::PARAM_INT);
            $stmt->bindParam('p_email', $arrData['email'], PDO::PARAM_STR);
            $stmt->bindParam('p_skype_account', $arrData['skype_account'], PDO::PARAM_STR);
            $stmt->bindParam('p_birthday', $arrData['birthday'], PDO::PARAM_STR);
            $stmt->bindParam('p_phone', $arrData['phone'], PDO::PARAM_STR);
            $stmt->bindParam('p_contact_address', $arrData['contact_address'], PDO::PARAM_STR);
            $stmt->bindParam('p_personal_email', $arrData['personal_email'], PDO::PARAM_STR);
            $stmt->bindParam('p_picture', $arrData['picture'], PDO::PARAM_STR);

            
    		$stmt->execute();
    															
    		$stmt = $storage->query("SELECT @p_RowCount");
    		$result = $stmt->fetchColumn();
    
            $stmt->closeCursor();
            
    	} catch (Exception $ex) {
            ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),'',$arrData['name']);
    		print_r($ex->getMessage());
    		$result = -1;
    	}
    
	    // return data
	    return $result;
    }
    
    public function updateJob($arrData)
    {
    	//init return result
    	$result = 0;
        $arrData = Validate::encodeValues($arrData);
    	try {
    
    		$iUpdateDate = time();
    		 
    		$storage = Core_Global::getDbGlobalMaster();
    
    		# Prepare store procude
    		$stmt = $storage->prepare("CALL sp_account_info_update_job(
    				  :p_account_id
    				, :p_contract_type
					, :p_level
					, :p_start_date
					, :p_sub_leader_id
    				, :p_leader_id
    				, :p_deputy_manager_id
    				, :p_manager_id
    				, :p_bom_id
					, :p_team_id
            		, @p_RowCount)");
    
    
    			$stmt->bindParam('p_account_id', $arrData['account_id'], PDO::PARAM_INT);
    			$stmt->bindParam('p_contract_type', $arrData['contract_type'], PDO::PARAM_INT);
    			$stmt->bindParam('p_level', $arrData['level'], PDO::PARAM_INT);
    			$stmt->bindParam('p_start_date', $arrData['start_date'], PDO::PARAM_STR);
    			
    			$stmt->bindParam('p_sub_leader_id', $arrData['sub_leader_id'], PDO::PARAM_INT);
    			$stmt->bindParam('p_leader_id', $arrData['leader_id'], PDO::PARAM_INT);
    			$stmt->bindParam('p_deputy_manager_id', $arrData['deputy_manager_id'], PDO::PARAM_INT);
    			$stmt->bindParam('p_manager_id', $arrData['manager_id'], PDO::PARAM_INT);
    			$stmt->bindParam('p_bom_id', $arrData['bom_id'], PDO::PARAM_INT);
    			
    			$stmt->bindParam('p_team_id', $arrData['team_id'], PDO::PARAM_INT);
    				
    				 
    
    			$stmt->execute();
    					
    			$stmt = $storage->query("SELECT @p_RowCount");
        		$result = $stmt->fetchColumn();
    
    			$stmt->closeCursor();
    
    	} catch (Exception $ex) {
            ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),$arrData['account_id']);
    		print_r($ex->getMessage());
    		$result = -1;
    	}
    
    	// return data
    	return $result;
    }
    
    
    public function updatePersonal($arrData)
    {
    	//init return result
    	$result = 0;
        $arrData = Validate::encodeValues($arrData);
    	try {
    
    		$iUpdateDate = time();
    		 
    		$storage = Core_Global::getDbGlobalMaster();
    
    		# Prepare store procude
    		$stmt = $storage->prepare("CALL sp_account_info_update_personal(
    											 :p_account_id
    				 							, :p_gender
												, :p_place_of_birth
												, :p_home_town
												, :p_address
												, :p_identity
												, :p_identity_date
												, :p_identity_place
												, :p_social_insurance
												, :p_tax_code
												, :p_bank_account
												, :p_bank_account_id
												, :p_bank_account_branch
												, :p_marital_status
												, :p_no_of_children
												, :p_contact_name
												, :p_contact_relationship
												, :p_contact_address
								    			, :p_passport
												, :p_passport_date
												, :p_passport_place
    				
            		, @p_RowCount)");
    
    
    		$stmt->bindParam('p_account_id', $arrData['account_id'], PDO::PARAM_INT);
    		$stmt->bindParam('p_gender', $arrData['gender'], PDO::PARAM_INT);
    		$stmt->bindParam('p_place_of_birth', $arrData['place_of_birth'], PDO::PARAM_INT);
    	    $stmt->bindParam('p_home_town', $arrData['home_town'], PDO::PARAM_INT);
    		$stmt->bindParam('p_address', $arrData['address'], PDO::PARAM_STR);
    		$stmt->bindParam('p_identity', $arrData['identity'], PDO::PARAM_STR);
    		$stmt->bindParam('p_identity_date', $arrData['identity_date'], PDO::PARAM_STR);
    		$stmt->bindParam('p_identity_place', $arrData['identity_place'], PDO::PARAM_INT);
    		$stmt->bindParam('p_social_insurance', $arrData['social_insurance'], PDO::PARAM_STR);
    		$stmt->bindParam('p_tax_code', $arrData['tax_code'], PDO::PARAM_STR);
    		$stmt->bindParam('p_bank_account', $arrData['bank_account'], PDO::PARAM_STR);
    		$stmt->bindParam('p_bank_account_id', $arrData['bank_account_id'], PDO::PARAM_INT);
    		$stmt->bindParam('p_bank_account_branch', $arrData['bank_account_branch'], PDO::PARAM_INT);
    		
    		$stmt->bindParam('p_marital_status', $arrData['marital_status'], PDO::PARAM_INT);
    		$stmt->bindParam('p_no_of_children', $arrData['no_of_children'], PDO::PARAM_INT);
    		$stmt->bindParam('p_contact_name', $arrData['contact_name'], PDO::PARAM_STR);
    		$stmt->bindParam('p_contact_relationship', $arrData['contact_relationship'], PDO::PARAM_STR);
    		$stmt->bindParam('p_contact_address', $arrData['contact_address'], PDO::PARAM_STR);
    		
    		$stmt->bindParam('p_passport', $arrData['passport'], PDO::PARAM_STR);
    		$stmt->bindParam('p_passport_date', $arrData['passport_date'], PDO::PARAM_STR);
    		$stmt->bindParam('p_passport_place', $arrData['passport_place'], PDO::PARAM_INT);
    				 
    
    		$stmt->execute();
    					
    		$stmt = $storage->query("SELECT @p_RowCount");
        		$result = $stmt->fetchColumn();
    
    				$stmt->closeCursor();
    
    	} catch (Exception $ex) {
    	print_r($ex->getMessage());
            ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),$arrData['account_id']);
    		$result = -1;
    	exit();
    	}
    
    	// return data
    	return $result;
    	}
    
    
     /**
     * @return <int>
     */
    public function updateMyAccountInfo($arrData)
    {
         //init return result
        $result = 0;
        $arrData = Validate::encodeValues($arrData);
        try {
            
            $iUpdateDate = time();
            
            # Get Data Master Global
            $storage = Core_Global::getDbGlobalMaster();
                    
            # Prepare store procude
            $stmt = $storage->prepare("CALL sp_account_info_my_update(:p_account_id,:p_name,:p_phone,:p_birthday,
                :p_avatar,:p_id,:p_identity,:p_address,:p_skype_account,:p_mobion_account,:p_country_id,
                :p_description, :p_update_date,@p_RowCount)");
            
            
            $stmt->bindParam('p_account_id', $arrData['account_id'], PDO::PARAM_INT);
            $stmt->bindParam('p_name', $arrData['name'], PDO::PARAM_STR);
            $stmt->bindParam('p_phone', $arrData['phone'], PDO::PARAM_STR);
            $stmt->bindParam('p_birthday', $arrData['birthday'], PDO::PARAM_STR);
            $stmt->bindParam('p_id', $arrData['id'], PDO::PARAM_INT);
            $stmt->bindParam('p_identity', $arrData['identity'], PDO::PARAM_STR);
            $stmt->bindParam('p_address', $arrData['address'], PDO::PARAM_STR);
            $stmt->bindParam('p_skype_account', $arrData['skype_account'], PDO::PARAM_STR);
            $stmt->bindParam('p_mobion_account', $arrData['mobion_account'], PDO::PARAM_STR);
            $stmt->bindParam('p_avatar', $arrData['avatar'], PDO::PARAM_STR);
             
            $stmt->bindParam('p_country_id', $arrData['country_id'], PDO::PARAM_INT);
            $stmt->bindParam('p_description', $arrData['description'], PDO::PARAM_STR);

            $stmt->bindParam('p_update_date', $iUpdateDate, PDO::PARAM_INT);
            $stmt->execute();

            # Fetch All Result
            $stmt = $storage->query("SELECT @p_RowCount");
            $result = $stmt->fetchColumn();

            # Free cursor
            $stmt->closeCursor();
        } catch (Exception $ex) {
            ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),$arrData['account_id'], $arrData['name']);
            $result = 0;
        }

        // return data
        return $result;
    }

    
    
    
        
    /**
     * @return <int>
     */
    public function updateAccountInfoStatus($iAccountID, $iActive) 
    {
         //init return result
        $result = 0;
        
        try {
            
            $iUpdateDate = time();
            
            # Get Data Master Global
            $storage = Core_Global::getDbGlobalMaster();
                    
            # Prepare store procude
            $stmt = $storage->prepare("CALL sp_account_info_update_status(:p_account_id,:p_active,@p_RowCount)");
            
            
            $stmt->bindParam('p_account_id', $iAccountID, PDO::PARAM_INT);
            $stmt->bindParam('p_active', $iActive, PDO::PARAM_INT);
            
            $stmt->execute();

            # Fetch All Result
            $stmt = $storage->query("SELECT @p_RowCount");
            $result = $stmt->fetchColumn();

            # Free cursor
            $stmt->closeCursor();
        } catch (Exception $ex) {
            ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),$iAccountID);
            $result = 0;
        }

        // return data
        return $result;
    }
    

    /**
     * @todo  Remove event
     * @param <int> $iEventId
     * @return <int>
     */
    public function removeAccountInfo($iAccountID) {
        try {
            # Get Data Master Global
            $storage = Core_Global::getDbGlobalMaster();

            # Prepare store procude
            $stmt = $storage->prepare("CALL sp_account_info_delete(:p_account_id, @p_RowCount)");
            $stmt->bindParam('p_account_id', $iAccountID, PDO::PARAM_INT);
            $stmt->execute();

            # Fetch All Result
            $stmt = $storage->query("SELECT @p_RowCount");
            $result = $stmt->fetchColumn();

            # Free cursor
            $stmt->closeCursor();
        } catch (Exception $ex) {
            ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),$iAccountID);
            $result = -1;
        }

        // return data
        return $result;
    }

    /**
     * @return $arrResult = array(
                'total' => $iTotal,
                'data' => $arrResult
            );
     */
    public function getAccountInfoList($sName,$sEmail, $iID, $iIdentity, $sTaxCode, $iPosition,
            $iDepartmentID,$iTeamID, $iLevel, $iActive, $sSortField, $sSortType, $iOffset, $iLimit) {
       
        $arrResult = array();
         
        try {          
                        
            # Get Data Master Global
            $storage = Core_Global::getDbGlobalSlave();

            # Prepare store procude
            $stmt = $storage->prepare("CALL sp_account_info_select(:p_name,:p_email,:p_id,:p_identity,
                :p_tax_code, :p_position, :p_department, :p_team, :p_level, :p_active, :p_sort_field, :p_sort_type, :p_offset, :p_limit, @p_RowCount)");
            $stmt->bindParam('p_name',$sName, PDO::PARAM_STR);
            $stmt->bindParam('p_email',$sEmail, PDO::PARAM_STR);
            $stmt->bindParam('p_id',$iID, PDO::PARAM_INT);
            $stmt->bindParam('p_identity',$iIdentity, PDO::PARAM_INT);
            $stmt->bindParam('p_tax_code',$sTaxCode, PDO::PARAM_STR);
            $stmt->bindParam('p_position',$iPosition, PDO::PARAM_INT);
            $stmt->bindParam('p_department',$iDepartmentID, PDO::PARAM_INT);
            $stmt->bindParam('p_team',$iTeamID, PDO::PARAM_INT);
            $stmt->bindParam('p_level',$iLevel, PDO::PARAM_INT);
            $stmt->bindParam('p_active',$iActive, PDO::PARAM_INT);
            $stmt->bindParam('p_sort_field',$sSortField, PDO::PARAM_STR);
            $stmt->bindParam('p_sort_type',$sSortType, PDO::PARAM_STR);

            $stmt->bindParam('p_offset', $iOffset, PDO::PARAM_INT);
            $stmt->bindParam('p_limit', $iLimit, PDO::PARAM_INT);
            $stmt->execute();

            # Fetch All Result
            $arrResult = $stmt->fetchAll();
            
            # Free cursor
            $stmt->closeCursor();
            
             //Fetch Total Result
            $stmt = $storage->query("SELECT @p_RowCount");

            //Get total data
            $iTotal = $stmt->fetchColumn();

            //Free cursor
            $stmt->closeCursor();

            //Return data
            $arrResult = array(
                'total' => $iTotal,
                'data' => $arrResult
            );
            
            
        } catch (Exception $ex) {
            Core_Common::var_dump($ex);
            ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),0,$sName);
        }

        // return data
        return $arrResult;
    }

    public function getAccountInfoListByLikeEmail($sEmail, $iOffset, $iLimit) {

        $arrResult = array('data'=>array(),'total'=>0);

        try {

            # Get Data Master Global
            $storage = Core_Global::getDbGlobalSlave();

            # Prepare store procude
            $stmt = $storage->prepare("CALL sp_account_info_select_like_email(:p_email, :p_offset, :p_limit, @p_RowCount)");
            $stmt->bindParam('p_email',$sEmail, PDO::PARAM_STR);
            $stmt->bindParam('p_offset', $iOffset, PDO::PARAM_INT);
            $stmt->bindParam('p_limit', $iLimit, PDO::PARAM_INT);
            $stmt->execute();

            # Fetch All Result
            $arrResult = $stmt->fetchAll();

            # Free cursor
            $stmt->closeCursor();

            //Fetch Total Result
            $stmt = $storage->query("SELECT @p_RowCount");

            //Get total data
            $iTotal = $stmt->fetchColumn();

            //Free cursor
            $stmt->closeCursor();

            //Return data
            $arrResult = array(
                'total' => $iTotal,
                'data' => $arrResult
            );


        } catch (Exception $ex) {
            ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),0,$sName);
        }

        // return data
        return $arrResult;
    }

     /**
     * @return <array>
     */
        
        
    public function getAccountInfoListShort($sName,$sEmail, $iID, $iIdentity, $sTaxCode, $iPosition,
            $iDepartmentID,$iTeamID, $iOffset, $iLimit) {
       
        $arrResult = array();
         
        try {          
                        
            # Get Data Master Global
            $storage = Core_Global::getDbGlobalSlave();

            # Prepare store procude
            $stmt = $storage->prepare("CALL sp_account_info_short_select(:p_name,:p_email,:p_id,:p_identity,
                :p_tax_code, :p_position, :p_department, :p_team, :p_offset, :p_limit, @p_RowCount)");
            $stmt->bindParam('p_name',$sName, PDO::PARAM_STR);
            $stmt->bindParam('p_email',$sEmail, PDO::PARAM_STR);
            $stmt->bindParam('p_id',$iID, PDO::PARAM_INT);
            $stmt->bindParam('p_identity',$iIdentity, PDO::PARAM_INT);
            $stmt->bindParam('p_tax_code',$sTaxCode, PDO::PARAM_STR);
            $stmt->bindParam('p_position',$iPosition, PDO::PARAM_INT);
            $stmt->bindParam('p_department',$iDepartmentID, PDO::PARAM_INT);
            $stmt->bindParam('p_team',$iTeamID, PDO::PARAM_INT);
            
            $stmt->bindParam('p_offset', $iOffset, PDO::PARAM_INT);
            $stmt->bindParam('p_limit', $iLimit, PDO::PARAM_INT);
            $stmt->execute();

            # Fetch All Result
            $arrResult = $stmt->fetchAll();
            
            # Free cursor
            $stmt->closeCursor();
            
            
            
        } catch (Exception $ex) {
            ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),0,$sName);
        }

        // return data
        return $arrResult;
    }
    
    
    
     public function getAccountInfoListTop($iOffset, $iLimit) {
       
        $arrResult = array();
         
        try {          
                        
            # Get Data Master Global
            $storage = Core_Global::getDbGlobalSlave();

            # Prepare store procude
            $stmt = $storage->prepare("CALL sp_account_info_short_select_top(:p_offset, :p_limit, @p_RowCount)");
            
            $stmt->bindParam('p_offset', $iOffset, PDO::PARAM_INT);
            $stmt->bindParam('p_limit', $iLimit, PDO::PARAM_INT);
            $stmt->execute();

            # Fetch All Result
            $arrResult = $stmt->fetchAll();
            
            # Free cursor
            $stmt->closeCursor();
            
            
            
        } catch (Exception $ex) {
            ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage());
        }

        // return data
        return $arrResult;
    }
    
    
    /*
     * 
     */
     public function getAccountInfoByAccountID($iAccountID) {
     	
     	$arrResult = array();
     	
        try {

            # Get Data Master Global
            $storage = Core_Global::getDbGlobalSlave();

            # Prepare store procude
            $stmt = $storage->prepare("CALL sp_account_info_by_accountid(:p_account_id, @p_RowCount)");
            $stmt->bindParam('p_account_id', $iAccountID, PDO::PARAM_INT);
            $stmt->execute();

            # Fetch All Result
            $arrResult = $stmt->fetch();

            # Free cursor
            $stmt->closeCursor();
        } catch (Exception $ex) {
            ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),$iAccountID);
            $arrResult = array();
        }

        // return data
        return $arrResult;
    }
    
    /*
     * 
     */
     public function getAccountInfoByEmail($sEmail) {
         
         $arrResult = array();
         
        try {

            # Get Data Master Global
            $storage = Core_Global::getDbGlobalSlave();

            # Prepare store procude
            $stmt = $storage->prepare("CALL sp_account_info_by_email(:p_email, @p_RowCount)");
            $stmt->bindParam('p_email', $sEmail, PDO::PARAM_STR);
            $stmt->execute();

            # Fetch All Result
            $arrResult = $stmt->fetch();

            # Free cursor
            $stmt->closeCursor();
        } catch (Exception $ex) {
            ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage());
            $arrResult = array();
        }

        // return data
        return $arrResult;
    }
    
    
    /*
     * 
     */
     public function getAccountInfoByUserName($sUserName, $iActive) {
         
         $arrResult = array();
         
        try {

            # Get Data Master Global
            $storage = Core_Global::getDbGlobalSlave();

            # Prepare store procude
            $stmt = $storage->prepare("CALL sp_account_info_by_username(:p_username, :p_active, @p_RowCount)");
            $stmt->bindParam('p_username', $sUserName, PDO::PARAM_STR);
            $stmt->bindParam('p_active', $iActive, PDO::PARAM_INT);
            $stmt->execute();

            # Fetch All Result
            $arrResult = $stmt->fetch();

            # Free cursor
            $stmt->closeCursor();
        } catch (Exception $ex) {
            Core_Common::var_dump($ex->getMessage());
            ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),0,$sUserName);
            $arrResult = array();
        }

        // return data
        return $arrResult;
    }

    public function getAccountInfoBySkype($sSkype) {

        $arrResult = array();

        try {

            # Get Data Master Global
            $storage = Core_Global::getDbGlobalSlave();

            # Prepare store procude
            $stmt = $storage->prepare("CALL sp_account_info_by_skype(:p_skype_account, @p_RowCount)");
            $stmt->bindParam('p_skype_account', $sSkype, PDO::PARAM_STR);
            $stmt->execute();

            # Fetch All Result
            $arrResult = $stmt->fetch();

            # Free cursor
            $stmt->closeCursor();
        } catch (Exception $ex) {
            ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),0,$sSkype);
            $arrResult = array();
        }

        // return data
        return $arrResult;
    }
    
    /**
     * @return <int>
     */
    public function updateAvatar($iAccountID, $sPicture) 
    {
         //init return result
        $result = 0;
        
        try {
            
            
            # Get Data Master Global
            $storage = Core_Global::getDbGlobalMaster();
                    
            # Prepare store procude
            $stmt = $storage->prepare("CALL sp_account_info_update_avatar(:p_account_id,:p_picture,@p_RowCount)");
            
            $stmt->bindParam('p_account_id', $iAccountID, PDO::PARAM_INT);
            $stmt->bindParam('p_picture', $sPicture, PDO::PARAM_STR);
           
         
            $stmt->execute();

            # Fetch All Result
            $stmt = $storage->query("SELECT @p_RowCount");
            $result = $stmt->fetchColumn();

            # Free cursor
            $stmt->closeCursor();
        } catch (Exception $ex) {
            ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),$iAccountID);
            $result = 0;
        }

        // return data
        return $result;
    }
    
    
    
     /*
     *  Account Info many AccountIDs
     */
     public function getAccountInfoByAccountIDs($sAccountID) 
     {
         
        $arrResult = array();
        
        try {

            # Get Data Master Global
            $storage = Core_Global::getDbGlobalSlave();

            # Prepare store procude
            $stmt = $storage->prepare("CALL sp_account_info_selectbyids(:p_account_ids, @p_RowCount)");
            $stmt->bindParam('p_account_ids', $sAccountID, PDO::PARAM_STR);
            $stmt->execute();

            # Fetch All Result
            $arrResult = $stmt->fetchAll();

            # Free cursor
            $stmt->closeCursor();
        } catch (Exception $ex) {
            ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),$sAccountID);
            $arrResult = array();
        }

        // return data
        return $arrResult;
    }
    
    /*
     * 
     */
    
     public function getAccountInfoSuggestion($sName, $sPosition,$sEmail, $iOffset, $iLimit) {
       
        $arrResult = array();
         
        try {          
                        
            # Get Data Master Global
            $storage = Core_Global::getDbGlobalSlave();

            # Prepare store procude
            $stmt = $storage->prepare("CALL sp_account_info_suggestion(:p_name,:p_position,:p_email,:p_offset, :p_limit, @p_RowCount)");
            $stmt->bindParam('p_name',$sName, PDO::PARAM_STR);
            $stmt->bindParam('p_email',$sEmail, PDO::PARAM_STR);
            $stmt->bindParam('p_position',$sPosition, PDO::PARAM_STR);
            
            $stmt->bindParam('p_offset', $iOffset, PDO::PARAM_INT);
            $stmt->bindParam('p_limit', $iLimit, PDO::PARAM_INT);
            $stmt->execute();

            # Fetch All Result
            $arrResult = $stmt->fetchAll();
            
            # Free cursor
            $stmt->closeCursor();
            
            
        } catch (Exception $ex) {
            ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage(),0,$sName);
        }
        
        // return data
        return $arrResult;
    }
    
    
    
    /**
     * Get Account info 
     * @param <array> $sDeviceID
     * @return <array>
     */
    public function getAccountListIN($sAccountIDs) {

        $arrResult = array();

        //Connect to get data
        try {
            //Get DB Slave Global
            $storage = Core_Global::getDbGlobalSlave();

            //Prepare store procedure
            $stmt = $storage->prepare("CALL sp_account_inselect(:p_account_ids,@p_RowCount);");
            $stmt->bindParam('p_account_ids', $sAccountIDs, PDO::PARAM_STR);

            //Excute store procedure
            $stmt->execute();

            //Fetch All Result
            $arrResult = $stmt->fetchAll();

            # Free cursor
            $stmt->closeCursor();
        } catch (Exception $ex) {
            ErrorLog::getInstance()->insert(__CLASS__,__FUNCTION__,$ex->getMessage());
        }

        //Return data
        return $arrResult;
    }
    
    public function countUserActive() {
    	
    	$total = 0;
    	
    	try {
    
    		# Get Data Master Global
    		$storage = Core_Global::getDbGlobalSlave();
    
    		# Prepare store procude
    		$stmt = $storage->prepare("CALL sp_account_info_count_active()");
       		$stmt->execute();
    
    		# Fetch All Result
    		$arrResult = $stmt->fetch();
    		$total = $arrResult['total'];
    		# Free cursor
    		$stmt->closeCursor();
    	} catch (Exception $ex) {
    		$total = 0;
    	}
    
    	// return data
    	return $total;
    }
    
    /**
     * getAccountInfoByFirstName
     * 
     * @param string $name
     * @return array
     * @author @author Tai Le Thanh <tai.lt@vn.gnt-global.com>
     */
    public function getAccountInfoByLikeName($name)
    {

        $arrResult = array();

        try {

            # Get Data Master Global
            $storage = Core_Global::getDbGlobalSlave();

            # Prepare store procude
            $stmt = $storage->prepare("CALL sp_account_info_select_all_by_like_name(:name)");
            $stmt->bindParam('name', $name, PDO::PARAM_STR);
            $stmt->execute();

            # Fetch All Result
            $arrResult = $stmt->fetchAll();

            # Free cursor
            $stmt->closeCursor();
        } catch (Exception $ex) {
            ErrorLog::getInstance()->insert(__CLASS__, __FUNCTION__, $ex->getMessage());
            $arrResult = array();
        }

        // return data
        return $arrResult;
    }
    /**
     * getAccountInfoByFirstName
     * 
     * @param string $email
     * @return array
     * @author @author Tai Le Thanh <tai.lt@vn.gnt-global.com>
     */
    public function getAccountInfoByLikeEmail($email)
    {

        $arrResult = array();

        try {

            # Get Data Master Global
            $storage = Core_Global::getDbGlobalSlave();

            # Prepare store procude
            $stmt = $storage->prepare("CALL sp_account_info_select_all_by_like_email(:email)");
            $stmt->bindParam('email', $email, PDO::PARAM_STR);
            $stmt->execute();

            # Fetch All Result
            $arrResult = $stmt->fetchAll();

            # Free cursor
            $stmt->closeCursor();
        } catch (Exception $ex) {
            ErrorLog::getInstance()->insert(__CLASS__, __FUNCTION__, $ex->getMessage());
            $arrResult = array();
        }

        // return data
        return $arrResult;
    }

}