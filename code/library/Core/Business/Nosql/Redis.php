<?php

/**
 * @author      :   HoaiTN
 * @name        :   Core_Business_Nosql_Redis
 * @version     :   201200607
 * @copyright   :   GNT
 * @todo        :   Fan
 */
class Core_Business_Nosql_Redis {

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
     * @return <int> $iResult
     */
    public function setList($key, $value, $score=0) 
    {
        //Intval
        //$value = intval($value);
        if($value ==0)
        {
             return 0;
        }
         
        // Default data
        $iResult = 0;
        
        if(empty($score))
        {
            $score = time();
        }
       

        try {
            // Get list Stored
            $listStoredInstance = Core_Global::getRedisInstance();
            
         
            // put value to key
            $listStoredInstance->zAdd($key, $score, $value);
          
            if($this->isList($key, $value)) {
                $iResult = 1;
            }
            
            
        } catch (Exception $ex) {
            $iResult = -1;
        }

        // Set return data
        return $iResult;
    }
    
    
    /**
     * @return <int> $iResult
     */
    public function getListTotal($key) 
    {
        // Default data
        $iResult = 0;

        try {
            // Get list Stored
            $listStoredInstance = Core_Global::getRedisInstance();
            
            // put value to key
            $iResult = $listStoredInstance->zSize($key);
        } catch (Exception $ex) {
            
            
           
            $iResult = -1;
        }

        // Set return data
        return $iResult;
    }
    
    /*
     *  Get List
     */
    public function getList($key, $iStart = 0, $iEnd)
    {
        // Default data
        $arrResult = array();

        try {
            // Get list Stored
            $listStoredInstance = Core_Global::getRedisInstance();
           
            // get data
            $arrResult = $listStoredInstance->zRange($key, $iStart, $iEnd);
            
        } catch (Exception $ex) {
            
        }

        // Set return data
        return $arrResult;
    }
   
  
    /**
     * remove a item in list
     * @return <int> $iResult
     */
    public function deleteItemList($key, $value) 
    {
        // Default data
        $iResult = 0;

        try {
            // Get list Stored
            $listStoredInstance = Core_Global::getRedisInstance();

            // put value to key
            $iResult = $listStoredInstance->zDelete($key, $value);
        } catch (Exception $ex) {
            $iResult = -1;
        }

        // Set return data
        return $iResult;
    }
    
    /**
     * remove a item in list
     * @return <int> $iResult
     */
    public function deleteKey($key) 
    {
        // Default data
        $iResult = 0;

        try {
            // Get list Stored
            $listStoredInstance = Core_Global::getRedisInstance();

            // put value to key
            $iResult = $listStoredInstance->delete($key);
        } catch (Exception $ex) {
            $iResult = -1;
        }

        // Set return data
        return $iResult;
    }
      

    /**
     * get Fan count of Brand
     * @param <string> $sBrandShopID
     * @return <int> $iFanCount
     */
    public function isList($key, $value) 
    {
        // Default data
        $iResult = 1;

        try {
            // Get list Stored
            $listStoredInstance = Core_Global::getRedisInstance();
            
            // get data
            $iResult = $listStoredInstance->zRank($key, $value);
            if($iResult === false)
            {
                  return 0;
            }
            else
            {
                $iResult =1;
            }
            
        } catch (Exception $ex) {
            $iResult = 0;
        }

        // Set return data
        return $iResult;
    }
    
    
    /**
     * set Key
     * @return <int> $iResult
     */
    public function setKey($key, $value) 
    {
        // Default data
        $iResult = 0;

        try {
            // Get list Stored
            $listStoredInstance = Core_Global::getRedisInstance();

            // put value to key
            $iResult = $listStoredInstance->set($key, $value);
        } catch (Exception $ex) {
            $iResult = -1;
        }

        // Set return data
        return $iResult;
    }
    
     /**
     * set Key
     * @return <int> $iResult
     */
    public function getKey($key) 
    {
        // Default data
        $iResult = '';

        try {
            // Get list Stored
            $listStoredInstance = Core_Global::getRedisInstance();

            // put value to key
            $iResult = $listStoredInstance->get($key);
        } catch (Exception $ex) {
            $iResult = '';
        }

        // Set return data
        return $iResult;
    }
    
    
    /**
     * set Key
     * @return <int> $iResult
     */
    public function setKeyExpire($key, $value, $time=31536000) 
    {
        // Default data
        $iResult = 0;
        
        try {
            // Get list Stored
            $listStoredInstance = Core_Global::getRedisInstance();

            // put value to key
            $iResult = $listStoredInstance->set($key, $value);
           // $listStoredInstance->expire($key, $time);

        } catch (Exception $ex) {
            $iResult = -1;
        }

        // Set return data
        return $iResult;
    }
    

    
    
     /*
     *  Get List
     */
    public function getListByScore($key, $iStart = 0, $iEnd)
    {

        $iStart = intval($iStart);
        $iEnd = intval($iEnd);
     
        $iMin = 0;
        $iMax = 999999999999;
        
        // Default data
        $arrResult = array();

        try {
            // Get list Stored
            $listStoredInstance = Core_Global::getRedisInstance();
           
           $arrResult = $listStoredInstance->ZREVRANGEBYSCORE($key,$iMax, $iMin,array('withscores' => true,'limit' => array($iStart,$iEnd)));
           
           if(!empty($arrResult))
           {
           	$arrResult = array_keys($arrResult);
           }
            
        } catch (Exception $ex) {
          
        }

        // Set return data
        return $arrResult;
    }
    
    
    /* Set Hash*/
    
    public function setHash($key, $field, $value)
    {

        $iResult =0;

        try {
            // Get list Stored
            $listStoredInstance = Core_Global::getRedisInstance();
           
            $iResult = $listStoredInstance->HSET($key,$field, $value);
           
        
            
        } catch (Exception $ex) {
          
        }

        // Set return data
        return $iResult;
    }
    
    /*
     * get Hash By field
     */
    public function getHashByField($key, $field)
    {

        $result ='';

        try {
            // Get list Stored
            $listStoredInstance = Core_Global::getRedisInstance();
           
            $result = $listStoredInstance->HGET($key,$field);
           
        
            
        } catch (Exception $ex) {
          
        }

        // Set return data
        return $result;
    }
    
    /*
     * get Hash By field
     */
    public function getHash($key)
    {

        $result ='';

        try {
            // Get list Stored
            $listStoredInstance = Core_Global::getRedisInstance();
           
            $result = $listStoredInstance->HGETALL($key);
           
        
            
        } catch (Exception $ex) {
          
        }

        // Set return data
        return $result;
    }
    
    
    /*
     * get Hash By field
     */
    public function deleteHashField($key, $field)
    {

        $result = 0;

        try {
            // Get list Stored
            $listStoredInstance = Core_Global::getRedisInstance();
           
            $result = $listStoredInstance->HDEL($key,$field);
           
        
            
        } catch (Exception $ex) {
          
        }

        // Set return data
        return $result;
    }
    
    
    public function deleteHash($key)
    {

        $result = 0;

        try {
            // Get list Stored
            $listStoredInstance = Core_Global::getRedisInstance();
           
            $arrResult = $listStoredInstance->getHash($key);
            if(!empty($arrResult))
            {
                  foreach($arrResult as $key=>$value)
                  {
                       if(!empty($key))
                       {
                          $this->deleteHashField($key, $key);
                       }
                  }
                  
                  return 1;
            }
           
        
            
        } catch (Exception $ex) {
          
        }

        // Set return data
        return $result;
    }

    public function sort($key,$sortType='asc',$iOffset = 0,$iLimit = MAX_QUERY_LIMIT){
        $listStoredInstance = Core_Global::getRedisInstance();


        return $listStoredInstance->sort($key,array('sort' => $sortType,'limit' => array($iOffset,$iLimit)));
    }
    
}
