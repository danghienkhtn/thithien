<?php

/**
 * @author      :   HIennd
 * @name        :   Core_Business_Api_General
 * @version     :   20101111
 * @copyright   :   My company
 * @todo        :   Using for account service
 */
class Core_Business_Api_News
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
    protected function __construct()
    {
        //Nothing
    }

    /**
     * Get singletom instance
     * @return <object>
     */
    public final static function getInstance()
    {
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
    public function insertNews($sTitle, $sContent, $sImage, $iType, $sSource, $iSortOrder, $ishot, $iActive)
    {
        //init return result
        $result = 0;


        try {

            $iUpdateDate = time();

            # Get Data Master Global
            $storage = Core_Global::getDbGlobalMaster();

            # Prepare store procude
            $stmt = $storage->prepare("CALL sp_news_insert(:p_title,:p_content,:p_image_url,:p_type,:p_source,:p_sort_order,
             :p_ishot,:p_active,:p_create_date,:p_update_date,@p_RowCount)");

            $stmt->bindParam('p_title', $sTitle, PDO::PARAM_STR);
            $stmt->bindParam('p_content', $sContent, PDO::PARAM_STR);
            $stmt->bindParam('p_image_url', $sImage, PDO::PARAM_STR);
            $stmt->bindParam('p_type', $iType, PDO::PARAM_INT);
            $stmt->bindParam('p_source', $sSource, PDO::PARAM_STR);
            $stmt->bindParam('p_sort_order', $iSortOrder, PDO::PARAM_INT);
            $stmt->bindParam('p_ishot', $ishot, PDO::PARAM_INT);
            $stmt->bindParam('p_active', $iActive, PDO::PARAM_INT);
            $stmt->bindParam('p_create_date', $iUpdateDate, PDO::PARAM_INT);
            $stmt->bindParam('p_update_date', $iUpdateDate, PDO::PARAM_INT);
            $stmt->execute();

            # Fetch All Result
            $stmt = $storage->query("SELECT @p_RowCount");
            $result = $stmt->fetchColumn();

            # Free cursor
            $stmt->closeCursor();
        } catch (Exception $ex) {
            ErrorLog::getInstance()->insert(__CLASS__, __FUNCTION__, $ex->getMessage());
            $result = 0;

        }

        // return data
        return $result;
    }

    public function updateNews($iNewsID, $sTitle, $sContent, $sImage, $iType, $sSource, $iSortOrder, $ishot, $iActive)
    {
        //init return result
        $result = 0;

        try {

            $iUpdateDate = time();

            # Get Data Master Global
            $storage = Core_Global::getDbGlobalMaster();

            # Prepare store procude
            $stmt = $storage->prepare("CALL sp_news_update(:p_news_id,:p_title,:p_content,:p_image_url,:p_type,:p_source,:p_sort_order,
             :p_ishot,:p_active,:p_update_date,@p_RowCount)");


            $stmt->bindParam('p_news_id', $iNewsID, PDO::PARAM_INT);
            $stmt->bindParam('p_title', $sTitle, PDO::PARAM_STR);
            $stmt->bindParam('p_content', $sContent, PDO::PARAM_STR);
            $stmt->bindParam('p_image_url', $sImage, PDO::PARAM_STR);
            $stmt->bindParam('p_type', $iType, PDO::PARAM_INT);
            $stmt->bindParam('p_source', $sSource, PDO::PARAM_STR);
            $stmt->bindParam('p_sort_order', $iSortOrder, PDO::PARAM_INT);
            $stmt->bindParam('p_ishot', $ishot, PDO::PARAM_INT);
            $stmt->bindParam('p_active', $iActive, PDO::PARAM_INT);
            $stmt->bindParam('p_update_date', $iUpdateDate, PDO::PARAM_INT);
            $stmt->execute();

            # Fetch All Result
            $stmt = $storage->query("SELECT @p_RowCount");
            $result = $stmt->fetchColumn();

            # Free cursor
            $stmt->closeCursor();
        } catch (Exception $ex) {
            ErrorLog::getInstance()->insert(__CLASS__, __FUNCTION__, $ex->getMessage());
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
    public function removeNews($iID)
    {

        $result = 0;
        try {
            # Get Data Master Global
            $storage = Core_Global::getDbGlobalMaster();

            # Prepare store procude
            $stmt = $storage->prepare("CALL sp_news_delete(:p_news_id, @p_RowCount)");
            $stmt->bindParam('p_news_id', $iID, PDO::PARAM_INT);
            $stmt->execute();

            # Fetch All Result
            $stmt = $storage->query("SELECT @p_RowCount");
            $result = $stmt->fetchColumn();

            # Free cursor
            $stmt->closeCursor();
        } catch (Exception $ex) {
            ErrorLog::getInstance()->insert(__CLASS__, __FUNCTION__, $ex->getMessage());
            print_r($ex->getMessage());
        }

        // return data
        return $result;
    }

    /**
     * @return <array>
     */

    public function getNewsList($sTitle, $iType, $isHot, $iActive, $iOffset, $iLimit)
    {

        $arrResult = array();

        try {

            # Get Data Master Global
            $storage = Core_Global::getDbGlobalSlave();

            # Prepare store procude
            $stmt = $storage->prepare("CALL sp_news_select(:p_title,:p_type,:p_ishot,:p_active, :p_offset, :p_limit, @p_RowCount)");

            $stmt->bindParam('p_title', $sTitle, PDO::PARAM_STR);
            $stmt->bindParam('p_type', $iType, PDO::PARAM_INT);
            $stmt->bindParam('p_ishot', $isHot, PDO::PARAM_INT);
            $stmt->bindParam('p_active', $iActive, PDO::PARAM_INT);

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
            ErrorLog::getInstance()->insert(__CLASS__, __FUNCTION__, $ex->getMessage());
        }

        // return data
        return $arrResult;
    }


    /**
     * @return <array>
     */

    public function getNewsList2($iOffset, $iLimit)
    {

        $arrResult = array();

        try {

            # Get Data Master Global
            $storage = Core_Global::getDbGlobalSlave();

            # Prepare store procude
            $stmt = $storage->prepare("CALL sp_news_select2(:p_offset, :p_limit, @p_RowCount)");

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
            ErrorLog::getInstance()->insert(__CLASS__, __FUNCTION__, $ex->getMessage());
        }

        // return data
        return $arrResult;
    }


    /*
     * Select By ID
     */
    public function getNewsByID($iID)
    {
        try {

            # Get Data Master Global
            $storage = Core_Global::getDbGlobalSlave();

            # Prepare store procude
            $stmt = $storage->prepare("CALL sp_news_select_byid(:p_news_id, @p_RowCount)");
            $stmt->bindParam('p_news_id', $iID, PDO::PARAM_INT);
            $stmt->execute();

            # Fetch All Result
            $arrResult = $stmt->fetch();

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