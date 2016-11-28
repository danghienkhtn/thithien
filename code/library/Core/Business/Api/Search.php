<?php

/**
 * @author      :   HoaiTN
 * @name        :   Core_Business_Api_Search 
 * @version     :   201011
 * @copyright   :   My company
 * @todo        :   Search profile
 */
class Core_Business_Api_Search 
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

    }

    /**
     * Get singletom instance
     * @return <object>
     */
    public final static function getInstance() {
        //Check instance
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        //Return instance
        return self::$_instance;
    }

    /**
     * Insert data to Solr search
     * @param <array> $params
     * @return <boolean>
     */
    public function insert($arrData) 
    {
        //Constructor default result
        $bResult = false;
        
        //Try get all data from search server
        try {
            //Init instance
            $searchProfile = Core_Global::getProfileSearch();

            //Array search to index data
            $arrSearchData = array();
           
            //asign data search
            $arrSearchData[]        = $arrData;
            
            
            //Index data
            $bResult = $searchProfile->index($arrSearchData);
            
            $searchProfile->commit();
        } catch (Exception $ex) {
            Core_Common::var_dump($ex);
            ErrorLog::getInstance()->insert(__CLASS__, __FUNCTION__, $ex->getMessage());
            $bResult = false;
        }

        //Return data
        return $bResult;
    }
    
    /**
     * Insert data to Solr search
     * @param <array> $params
     * @return <boolean>
     */
    public function update($arrData) 
    {
        //check ForumID
        if(!isset($arrData['account_id']))
        {
            return false;
        }
        
        //Constructor default result
        $bResult = false;
        
        //Try get all data from search server
        try {
            //Init instance
            $searchProfile = Core_Global::getProfileSearch();

            //Array search to index data
            $arrSearchData = array();
            
            
            //asign data
            $arrSearchData[]  = $arrData; 
                        
            //Index data
            $bResult = $searchProfile->update($arrSearchData, 'account_id');
            
            $searchProfile->commit();
        } catch (Exception $ex) {
            ErrorLog::getInstance()->insert(__CLASS__, __FUNCTION__, $ex->getMessage());
            $bResult = false;
        }

        //Return data
        return $bResult;
    }

    /**
     * Search news core
     * @param <string> $query
     * @param <int> $offset
     * @param <int> $limit
     * @param <string> $sort
     * @param <string> $fl
     * @return <array>
     */
    public function query($query, $offset, $limit, $sort='', $fl='', $qlf='', $qvf='', $debug = false) {
        //Try get all data from search server
        try {
            //Init instance
            $searchProfile = Core_Global::getProfileSearch();

            //Check query
            if (empty($query)) {
                return array(
                    'total' => 0,
                    'data' => array()
                );
            }

            //Put data search
            $arrQuery = array();
            $arrQuery['q'] = $query;
            $arrQuery['start'] = $offset;
            $arrQuery['rows'] = $limit;

            //Check sort
            if (!empty($sort)) {
                $arrQuery['sort'] = $sort;                
            }

            //Check fl
            if (!empty($fl)) {
                $arrQuery['fl'] = $fl;
            }
            
            //Check q.lf
            if(!empty($qlf))
            {
                $arrQuery['q.lf'] = $qlf;
            }
            
            //Check q.vf
            if(!empty($qvf))
            {
                $arrQuery['q.vf'] = $qvf;
            }
            
             //Check debug
            if($debug)
            {
                $arrQuery['q.vdg'] = 'on';
            }

            
            //Search data
            $arrData = $searchProfile->search($arrQuery);
            
            //Json decode data
            $arrData = unserialize($arrData);
            
            //Return data
            return array(
                'total' => $arrData['response']['numFound'],
                'data'  => $arrData['response']['docs']
            );
        } catch (Exception $ex) {
            ErrorLog::getInstance()->insert(__CLASS__, __FUNCTION__, $ex->getMessage());
            //Nothing
        }

        //Return default
        return array(
            'total' => 0,
            'data'  => array()
        );
    }

    /**
     * Delete data to Solr search
     * @param <int> $newsID
     * @return <boolean>
     */
    public function delete($AccountID) {
        //Constructor default result
        $bResult = false;
        
        //Try get all data from search server
        try {
            //Init instance
            $searchProfile = Core_Global::getProfileSearch();

            //Index data
            $bResult = $searchProfile->deleteById($AccountID);

            $searchProfile->commit();
        } catch (Exception $ex) {
            ErrorLog::getInstance()->insert(__CLASS__, __FUNCTION__, $ex->getMessage(), $AccountID);
            $bResult = false;
        }

        //Return data
        return $bResult;
    }

    /**
     * Commit data to Solr search
     * @return <boolean>
     */
    public function commit() {
        //Try get all data from search server
        try {
            //Init instance
            $searchProfile = Core_Global::getProfileSearch();

            //Index data
            $searchProfile->commit();

            //Return data
            return true;
        } catch (Exception $ex) {
            ErrorLog::getInstance()->insert(__CLASS__, __FUNCTION__, $ex->getMessage());
            //Nothing
        }

        //Return data
        return false;
    }

}