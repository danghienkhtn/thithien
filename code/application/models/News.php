<?php
/**
 * @author      :   HienND
 * @name        :   Model News
 * @version     :   20161221
 * @copyright   :   DAHI
 * @todo        :   AccountInfo model
 */
class News
{
    /**
     * Parent instance
     * @var <object>
     */
    private $_modeParent = null;

    /**
     * @var type
     */
    protected static $_instance = null;

    /**
     * Constructor of class
     * we don't permit an explicit call of the constructor! (like $v = new Singleton())
     */
    protected function __construct()
    {
        // Int Parent Model
        $this->_modeParent = Core_Business_Api_News::getInstance();
    }

    /**
     * Get singletom instance
     * @return <object>
     */
    public final static function getInstance()
    {        
        // Check Instance
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
// error_log("done");
        // Return Instance
        return self::$_instance;
    }

    public function insertJobNews($arrData)
    {
        //Get data
        $iResult = $this->_modeParent->insertJobNews($arrData);
        //Return result data
        return $iResult;
    }
    public function insertPropertiesNews($arrData)
    {
        //Get data
        $iResult = $this->_modeParent->insertPropertiesNews($arrData);
        //Return result data
        return $iResult;
    }
    public function insertCarNews($arrData)
    {
        //Get data
        $iResult = $this->_modeParent->insertCarNews($arrData);
        //Return result data
        return $iResult;
    }
    public function insertBikeNews($arrData)
    {
        //Get data
        $iResult = $this->_modeParent->insertBikeNews($arrData);
        //Return result data
        return $iResult;
    }
    public function updateJobNews($arrData, $accountId = 0)
    {
        //Get data
        $iResult = $this->_modeParent->updateJobNews($arrData, $accountId);
        //Return result data
        return $iResult;
    }    
    public function updatePropertiesNews($arrData, $accountId = 0)
    {
        //Get data
        $iResult = $this->_modeParent->updatePropertiesNews($arrData, $accountId);
        //Return result data
        return $iResult;
    }
    public function updateCarNews($arrData, $accountId = 0)
    {
        //Get data
        $iResult = $this->_modeParent->updateCarNews($arrData, $accountId);
        //Return result data
        return $iResult;
    }
    public function updateBikeNews($arrData, $accountId = 0)
    {
        //Get data
        $iResult = $this->_modeParent->updateBikeNews($arrData, $accountId);
        //Return result data
        return $iResult;
    }

    /**
     * @todo  Update
     * @return <int>
     */
    public function updateStatus($newsID, $active = 1)
    {
        //Get data
        $iResult = $this->_modeParent->updateStatus($newsID, $active);
        //Return result data
        return $iResult;
    }

    /**
     * @todo  Update
     * @return <int>
     */
    public function updateUpperNews($newsID, $upperToday = 1)
    {
        //Get data
        $iResult = $this->_modeParent->updateUpperNews($newsID, $upperToday);
        //Return result data
        return $iResult;
    }   

    /**
     * @todo  Remove GiveAway
     * @return <int>
     */
    public function removeNews($newsID)
    {
        //Get data
        $iResult = $this->_modeParent->removeNews($newsID);
        //Return result data
        return $iResult;
    }

    /**
     * @todo Get all Give Away
     * @return <array>
     */
    public function getNewsList($catId="", $subCatId="", $cityId="", $districtId="", $priceFrom="", $priceTo="", $active="", $txtSearch="", $accountId="",  $sSortField="", $sSortType="", $iOffset=0, $iLimit=MAX_QUERY_LIMIT)
    {
        //Get data
        $arrResult = $this->_modeParent->getNewsList($catId, $subCatId, $cityId, $districtId, $priceFrom, $priceTo, $active, $txtSearch, $accountId,  $sSortField, $sSortType, $iOffset, $iLimit);
        //Return result data
        return $arrResult;
    }

    /**
     * @todo count all Give Away
     * @return <array>
     */
    public function countNewsList($catId="", $subCatId="", $cityId="", $districtId="", $priceFrom="", $priceTo="", $active="", $txtSearch="", $accountId="")
    {    
        $totals = $this->_modeParent->countNewsList($catId, $subCatId, $cityId, $districtId, $priceFrom, $priceTo, $active, $txtSearch, $accountId);
        return $totals;

    }

    /**
     * @todo Get news detail 
     * @return <array>
     */
    public function getNewsByID($newsID, $active = 1)
    {
        //Get data
        $arrResult = $this->_modeParent->getNewsByID($newsID, $active);
        //Return result data
        return $arrResult;
    }
}
