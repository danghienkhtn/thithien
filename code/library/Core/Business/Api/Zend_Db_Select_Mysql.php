<?php

/**
 * Created by PhpStorm.
 * User: thanh.lh
 * Date: 2016-05-18
 * Time: 13:48
 */

//require_once DOCUMENT_ROOT.'/../../../framework/php/Zend/Db/Adapter/Abstract.php';
require_once DOCUMENT_ROOT.'/../../../../framework/php/Zend/Db/Select.php';

class Zend_Db_Select_Mysql extends Zend_Db_Select
{
    const SQL_CALC_FOUND_ROWS = 'sqlCalcFoundRows';
    // add other options as needed

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
    public final static function getInstance(Zend_Db_Adapter_Abstract $adapter) {

        /**
         * Use array_merge() instead of simply setting a key
         * because the order of keys is significant to the
         * rendering of the query.
         */
        self::$_partsInit = array_merge(
            array(
                self::SQL_CALC_FOUND_ROWS => false
                // add other options as needed
            ),
            self::$_partsInit
        );
        parent::__construct($adapter);
        // check instance
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        // return instance
        return self::$_instance;
    }

//
//    public function __construct(Zend_Db_Adapter_Abstract $adapter)
//    {
//        /**
//         * Use array_merge() instead of simply setting a key
//         * because the order of keys is significant to the
//         * rendering of the query.
//         */
//        self::$_partsInit = array_merge(
//            array(
//                self::SQL_CALC_FOUND_ROWS => false
//                // add other options as needed
//            ),
//            self::$_partsInit
//        );
//        parent::__construct($adapter);
//    }
    public function sqlCalcFoundRows($flag = true)
    {
        $this->_parts[self::SQL_CALC_FOUND_ROWS] = (bool) $flag;
        return $this;
    }
    protected function _renderSqlCalcFoundRows($sql)
    {
        if ($this->_parts[self::SQL_CALC_FOUND_ROWS]) {
            $sql .= ' SQL_CALC_FOUND_ROWS';
        }
        return $sql;
    }
}