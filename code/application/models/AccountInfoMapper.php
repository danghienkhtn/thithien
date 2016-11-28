<?php

/**
 * Created by PhpStorm.
 * User: thanh.lh
 * Date: 8/15/2016
 * Time: 10:28 AM
 */
class AccountInfoMapper extends Zend_Db_Table_Abstract
{
    protected static $_instance = null;
    protected $_name = 'account_info';

    protected $_account_id = 'account_id';
    protected $_gender = 'gender';
    protected $_birthday = 'birthday';
    protected $_place_of_birth = 'place_of_birth';
    protected $_home_town = 'home_town';
    protected $_address = 'address';
    protected $_identity = 'identity';

    protected $_identity_date = 'identity_date';
    protected $_identity_place = 'identity_place';
    protected $_passport = 'passport';
    protected $_passport_date = 'passport_date';
    protected $_passport_place = 'passport_place';
    protected $_social_insurance = 'social_insurance';
    protected $_tax_code = 'tax_code';
    protected $_bank_account = 'bank_account';
    protected $_bank_account_id = 'bank_account_id';
    protected $_bank_account_branch = 'bank_account_branch';

    protected $_marital_status = 'marital_status';
    protected $_no_of_children = 'no_of_children';
    protected $_contract_type = 'contract_type';
    protected $_end_probation = 'end_probation';
    protected $_date_end_probation = 'date_end_probation';
    protected $_level = 'level';
    protected $_email = 'email';
    protected $_phone = 'phone';
    protected $_picture = 'picture';
    protected $_avatar = 'avatar';
    protected $_id = 'id';
    protected $_position = 'position';

    protected $_department_id = 'department_id';
    protected $_skype_account = 'skype_account';
    protected $_start_date = 'start_date';
    protected $_end_date = 'end_date';
    protected $_country_id = 'country_id';
    protected $_contract_sign_date = 'contract_sign_date';
    protected $_account_name = 'name';
    protected $_first_name = 'first_name';
    protected $_last_name = 'last_name';
    protected $_personal_email = 'personal_email';

    protected $_contact_name = 'contact_name';
    protected $_contact_relationship = 'contact_relationship';
    protected $_contact_address = 'contact_address';
    protected $_contact_phone = 'contact_phone';
    protected $_description = 'description';
    protected $_status = 'status';

    protected $_active = 'active';
    protected $_username = 'username';
    protected $_team_name = 'team_name';
    protected $_manager_type = 'manager_type';
    protected $_top_people = 'top_people';
    protected $_lang = 'lang';

    protected $_created = 'created';
    protected $_updated = 'updated';


    protected static $db;
    private static $fields = null;

    public final static function getInstance()
    {
        // Check Instance
        if (is_null(self::$_instance)) {
            self::$db = Core_Global::getDbGlobalMaster();
            Zend_Db_Table_Abstract::setDefaultAdapter(self::$db);
            self::$_instance = new self();
        }
        if (is_null(self::$fields)) {
            self::$fields = new Fields();
        }
        // Return Instance
        return self::$_instance;
    }

    public function selectWithField(array $fields, $where = '',$iOffset = 0, $iLimit = MAX_QUERY_LIMIT, $sSortField = 'account_id', $sSortType = 'ASC'){

        $sortFields = array('account_id','username','name','email','avatar');
        $sortTypes = array('ASC','DESC');
        $sSortType = strtoupper($sSortType);
        if(empty($fields)){
            return array('data'=>array(),'total'=>0);
        }

        $select = self::$db->select()
            ->from(array('a'=>$this->_name), new Zend_Db_Expr ( 'SQL_CALC_FOUND_ROWS '.implode(',',$fields)))
            ->limit($iLimit,$iOffset);
        if(!empty($where)){

            $select->where($where);
        }
        if(in_array($sSortField,$sortFields) && in_array($sSortType,$sortTypes)){

            $select->order("$sSortField $sSortType");
        }

//        $sql = $select->__toString();
//        echo "$sql\n";
//        die;
        $stmt = $select->query();
        $result = $stmt->fetchAll();
        $iTotal = self::$db->fetchOne("select FOUND_ROWS()");
        return array('data'=>$result,'total'=>$iTotal);

    }

    public function selectOneWithField(array $fields, $where = ''){
        $accountInfo = array();
        if(empty($fields)){
            return array('data'=>array(),'total'=>0);
        }

        $select = self::$db->select()
            ->from(array('a'=>$this->_name), new Zend_Db_Expr ( implode(',',$fields)));

        if(!empty($where)){

            $select->where($where);
            $stmt = $select->query();
            $accountInfo =  $stmt->fetch();
            $accountInfo = ($accountInfo) ? $accountInfo : array();
        }
        return $accountInfo;
    }


}