<?php
/**
 * @author      :   Hiennd
 * @name        :   Model Search
 * @version     :   20161221
 * @copyright   :   Dahi
 * @todo        :   Search model
 */
class Search
{
    /**
     * Parent instance
     * @var <object>
     */
    private $_modeParent        = null;    

    /**
     * @var type 
     */
    protected static $_instance = null;
    
    /**
     * Constructor of class
     * we don't permit an explicit call of the constructor! (like $v = new Singleton())
     */
    protected function __construct() {
        // Int Parent Model
        $this->_modeParent = Core_Business_Api_Search::getInstance();        
    }
 

    /**
     * Get singletom instance
     * @return <object>
     */
    public final static function getInstance()
    {        
        // Check Instance
        if(is_null(self::$_instance))
        {
            self::$_instance = new self();
        }

        // Return Instance
        return self::$_instance;
    }

    public function getProfileSearchTmp($arrData)
    {

        $arrTmp =  array(
            'account_id' => '',
            'name' => '',
            'email' => '',
            'phone' => '',
            'birthday' => '',
            'picture' => '',
            'avatar' => '',
            'id' => '',
            'identity' => 0,
            'tax_code' => '',
            'address' => '',
            'position' => 0,
            'department_id' => 0,
            'team_id' => 0,
            'direct_manager' => 0,
            'skype_account' => '',
            'mobion_account' => '',
            'start_date' => '',
            'end_date' => '',
            'contract_type' => 0,
            'contract_sign_date' => '',
            'country_id' => 0,
            'status' => 0,
            'active' => 0,
            'username' => '',
            'team_name' => '',
            'manager_type' => 0,
            'top_people' => 0,
            'create_date' => time(),
            'update_date' => time()

        );

        foreach($arrData as $key=>$data)
        {
            if(isset($arrTmp[$key]))
                $arrTmp[$key] = $data;
        }
        return $arrTmp;
    }

    /**
     * @todo  Add new GiveAway
     */
    public function insert($arrData)
    {
        
        //check birthday
        if(empty($sBirthday))
        {
            $sBirthday ='0000-00-00';
        }
        
        $sBirthday = date('c', strtotime($sBirthday)).'.000Z';
        
        //Check start date
        if(empty($startDate))
        {
            $startDate ='0000-00-00';
        }
        
        $startDate = date('c', strtotime($startDate)).'.000Z';
        
        //End date
        if(empty($endDate))
        {
            $endDate ='0000-00-00';
        }
        
        $endDate = date('c', strtotime($endDate)).'.000Z';
        
        //Signd date
        if(empty($sContractSignDate))
        {
            $sContractSignDate ='0000-00-00';
        }
        
        $sContractSignDate = date('c', strtotime($sContractSignDate)).'.000Z';
        
        //check data
        $arrData['birthday'] = $sBirthday;
        $arrData['start_date'] = $startDate;
        $arrData['end_date'] = $endDate;
        $arrData['contract_sign_date'] = $sContractSignDate;
        
          //not need description
        unset($arrData['description']);

        //Get data
        $iResult = $this->_modeParent->insert($arrData);
        
        //Return result data
        return $iResult;
    }
    
    
        /**
     * @todo  Add new GiveAway
     */
    public function insertBase($iAccountID, $sName, $sEmail,$sPicture,$sUserName, $sTeamName)
    {
        
        //check birthday
        $sDate = date('c', strtotime('0000-00-00')).'.000Z';
      
        
        $arrData = array(
            'account_id'  => $iAccountID,
            'name'        => $sName,
            'email'       => empty($sEmail) ? ' ' : $sEmail,
            'phone'       => '',
            'birthday'    => $sDate,
            'picture'     => $sPicture,
            'avatar'      => $sPicture,
            'id'          => 0,
            'identity'    => '',
            'tax_code'    => '',
            'address'     => '',
            'position'    => 0,
            'department_id'   => 0,
            'team_id'         => 0,
            'direct_manager'  => 0,
            'skype_account'   => '',
            'mobion_account'  => '',
            'start_date'      => $sDate,
            'end_date'        => $sDate,
            'contract_type'   => 0,
            'contract_sign_date'   => $sDate,
            'country_id'           => 0,
            'status'               => 0,
            'active'               => 1,
            'username'           => $sUserName,
            'team_name'          => $sTeamName,
            'manager_type'       => 0,
            'top_people'         => 0
        );
       
        
        //Get data
        $iResult = $this->_modeParent->insert($arrData);
        
        //Return result data
        return $iResult;
    }
    
    /**
     * @todo  Update 
     
     * @return <int>
     */
    public function update($arrData)
    {
        
        
         //check birthday
        if(empty($sBirthday))
        {
            $sBirthday ='0000-00-00';
        }
        
        $sBirthday = date('c', strtotime($sBirthday)).'.000Z';
        
        //Check start date
        if(empty($startDate))
        {
            $startDate ='0000-00-00';
        }
        
        $startDate = date('c', strtotime($startDate)).'.000Z';
        
        //End date
        if(empty($endDate))
        {
            $endDate ='0000-00-00';
        }
        
        $endDate = date('c', strtotime($endDate)).'.000Z';
        
        //Signd date
        if(empty($sContractSignDate))
        {
            $sContractSignDate ='0000-00-00';
        }
        
        $sContractSignDate = date('c', strtotime($sContractSignDate)).'.000Z';
        
        //check data
        $arrData['birthday'] = $sBirthday;
        $arrData['start_date'] = $startDate;
        $arrData['end_date'] = $endDate;
        $arrData['contract_sign_date'] = $sContractSignDate;
       
        
        //not need description
//        unset($arrData['description']);
       
        //Get data
        $iResult = $this->_modeParent->update($arrData);
       
        //Return result data
        return $iResult;
    }
    
    
    
    /**
     * @todo  Update 
     
     * @return <int>
     */
    public function updateMyProfile($iAccountID,$sName,$sPhone,$sBirthday,$sAvatar,$iID,$iIdentity, $sAddress,$sSkype,
            $sMobionAccount, $iCountryID)
    {
        
        
         //check birthday
        if(empty($sBirthday))
        {
            $sBirthday ='0000-00-00';
        }
        
        $sBirthday = date('c', strtotime($sBirthday)).'.000Z';
   
        
         $arrData = array(
            'account_id'  => $iAccountID,
            'name'        => $sName,
            'phone'       => $sPhone,
            'birthday'    => $sBirthday,
            'avatar'     => $sAvatar,
            'id'          => intval($iID),
            'identity'    => $iIdentity,
            'address'     => $sAddress,
            'skype_account'   => $sSkype,
            'mobion_account'  => $sMobionAccount,
            'country_id'           => intval($iCountryID)
        );
         
         
        //Get data
        $iResult = $this->_modeParent->update($arrData);
       
        //Return result data
        return $iResult;
    }
    
   
    
    /**
     * @todo  Remove GiveAway
     * @return <int>
     */
    public function delete($iAccountID)
    {
        //Get data
        $iResult = $this->_modeParent->delete($iAccountID);
       
        //Return result data
        return $iResult;
    }

    public function getMemberSearch($offset = 0, $limit = MAX_QUERY_LIMIT, $name = '', $email = '',$key='',$sSort='') {
        // thieu gender, birthday, location address
        $query = "";
        $qlf = "";
        $qvf = "";
        $fl = "";
        $arrQueryField = array();

        $name = urldecode($name);
//        echo $name;
        //search name or email
        if (!empty($name)) {
            if (!empty($query)) {
                $query.= " AND ";
            }
            $query.= "name:" . $name;
        }

        if (!empty($email)) {
            if (!empty($query)) {
                $query .= " AND ";
            }
            $query.= "email:" . $email;
        }

        if (!empty($key)) {
            if (!empty($query)) {
                $query.= " AND ";
            }
            $query.= "name:" . $key;
            $query.= " OR email:" . $key;
        }

        if (empty($query)) {
            $query = "*:*";
        }


        //Get result search
        $arrResultSearch = $this->_modeParent->query($query, $offset, $limit, $sSort, $fl, $qlf, $qvf, DEBUG_SOLR);

        //Return result
        return $arrResultSearch;
//        }
    }

    /**
     * search profile
     * @param <array> $arrParam
     * @return <boolean>
     */
    public function getProfileSearch($arrParam, $offset, $limit, $sSort)
    {
        $query         = "";
        $qlf           = "";
        $qvf           = "";
        $fl            = "";
        $arrQueryField = array();


        //Check params
        if (!empty($arrParam))
        {
            //search keyword
            if (!empty($arrParam["name"]))
            {
                //Escape query data
                $arrParam["name"] = Core_Global::escapeQueryString($arrParam["name"]);

                //Add query
                $query.= "name:".$arrParam["name"]." OR name:*".$arrParam["name"].'*';

                //Put to query value
                $qvf = $arrParam["name"];

                //Put to query field
                $arrQueryField[] = "name";
            }

            //search mail
            if (!empty($arrParam["email"]))
            {
                if (!empty($query))
                {
                    $query.= " OR ";
                }

                //Escape query data
                $arrParam["email"] = Core_Global::escapeQueryString($arrParam["email"]);

                //Add query
                $query.= "email:".$arrParam["email"];
            }

            //Search ID
            if (!empty($arrParam["id"]) && $arrParam["id"] > 0)
            {
                if (!empty($query))
                {
                    $query.= " AND ";
                }

                $query.= "id:" . $arrParam["id"];
            }

            //Identity
            if (!empty($arrParam["identity"]))
            {
                if (!empty($query))
                {
                    $query.= " AND ";
                }

                $query.= "identity:" . $arrParam["identity"];
            }
            
            //Tax Code
            if (!empty($arrParam["taxcode"]))
            {
                if (!empty($query))
                {
                    $query.= " AND ";
                }

                $query.= "tax_code:" . $arrParam["taxcode"];
            }
            
            
             //Position
            if (!empty($arrParam["position"]))
            {
                if (!empty($query))
                {
                    $query.= " AND ";
                }

                $query.= "position:" . $arrParam["position"];
            }
            
              //Team
            if (!empty($arrParam["teamid"]))
            {
                if (!empty($query))
                {
                    $query.= " AND ";
                }

                $query.= "team_id:" . $arrParam["teamid"];
            }
            
            
            //Department
            if (!empty($arrParam["departmentid"]))
            {
                if (!empty($query))
                {
                    $query.= " AND ";
                }

                $query.= "department_id:" . $arrParam["departmentid"];
            }
            
            
            
            $qlf = 'name'; 
            
            if(empty($query))
            {
                $query = "*:*";
            }
            

           
            //Get result search
            $arrResultSearch = $this->_modeParent->query($query, $offset, $limit, $sSort, $fl, $qlf, $qvf, DEBUG_SOLR);

            //Return result
            return $arrResultSearch;
        }
    }
    
}
