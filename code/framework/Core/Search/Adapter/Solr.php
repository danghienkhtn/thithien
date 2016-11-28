<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Search_Adapter_Solr
 * @version     :   201008
 * @copyright   :   My company
 * @todo        :   Using to search
 */
class Core_Search_Adapter_Solr extends Core_Search_Adapter_Abstract
{        
    /**
    * Curl handle
    * @var var
    */
    private static $curlInstance = null;

    /**
    * Solr address
    * @var string $solrAddress
    */
    private $solrAddress = '127.0.0.1';

    /**
     * Solr core
     * @var <string>
     */
    private $solrCore = '';

    /**
     * Get CLI control 
     */
    private static $isCli = false;
    
    /**
     * Arrary query type accepted 
     */
    private static $arrQueryType = array(
        'json'  =>  'json',
        'phps'  =>  'phps'
    );
    
    /**
    * Constructor
    * @param array $options
    */
    public function __construct(array $options=array())
    {
        //Get options child
        $options = $this->getOptions($options);
        
        //Check host
        if(empty($options['host']))
        {
            throw new Core_Search_Exception('Input host for Solr.');
        }

        //Check port
        if(empty($options['port']))
        {
            throw new Core_Search_Exception('Input port for Solr.');
        }

        //Check admin core
        if(!empty($options['core']))
        {
            $this->solrCore = $options['core'];
            $this->solrCore = rtrim($this->solrCore, '/');
        }

        //Set solr url
        $this->solrAddress = 'http://'.$options['host'].':'.$options['port'];
        
        //Check CLI console
        self::$isCli = Core_Valid::isCli();
    }

    /**
     * Get curl instance
     * @return <Curl> 
     */
    protected static function getPoolCurl()
    {
        //Check instance
        if(is_object(self::$curlInstance) && (!self::$isCli))
        {
            //Return instance
            return self::$curlInstance;
        }

        //Init curl
        self::$curlInstance = curl_init();
        
        //Set buffer size option
        curl_setopt(self::$curlInstance, CURLOPT_BUFFERSIZE, 8192);
        
        //Return instance
        return self::$curlInstance;
    }
    
    /**
     * Close instance of curl 
     */
    protected static function closePoolCurl()
    {
        //Close curl instance
        if(is_object(self::$curlInstance) && (self::$isCli))
        {
            //Try to close curl instance
            @curl_close(self::$curlInstance);
            
            //Set to null pointer
            self::$curlInstance = null;
        }
    }
    
    /**
    * Destructor
    */
    public function __destruct()
    {
        //Close curl
        if(self::$curlInstance)
        {
            @curl_close(self::$curlInstance);
        }

        //Unset curl
        unset($this->solrAddress, $this->solrCore);
    }

    /**
    * Ping server
    */
    public function ping()
    {
        //Set params
        $params = array('wt'=>'json');
        $params = $this->buildQueryString($params);

        //Check multicore
        $solrAddress = $this->solrAddress . '/solr';
        if(!empty($this->solrCore))
        {
            $solrAddress .= '/' .$this->solrCore;
        }

        //Get pool curl
        $curlInstance = self::getPoolCurl();
        
        //Set curl options
        curl_setopt($curlInstance, CURLOPT_URL, $solrAddress.'/admin/ping');
        curl_setopt($curlInstance, CURLOPT_HTTPHEADER, array('Content-Type'=>'text/html; charset=UTF-8'));
        curl_setopt($curlInstance, CURLOPT_HEADER, false);
        curl_setopt($curlInstance, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curlInstance, CURLOPT_POST, true);
        curl_setopt($curlInstance, CURLOPT_POSTFIELDS, $params);
        curl_setopt($curlInstance, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlInstance, CURLOPT_TIMEOUT, 60);
        curl_setopt($curlInstance, CURLOPT_BINARYTRANSFER, true);

        //Execute curl
        $response = curl_exec($curlInstance);

        //Check curl error
        if(curl_errno($curlInstance))
        {
            //Close curl instance
            self::closePoolCurl();
            
            //Return response
            return false;
        }
        
        //Close curl instance
        self::closePoolCurl();

        //Decode status
        $response = Zend_Json::decode($response);
        
        //Check status
        if($response['status']=='OK')
        {
            return true;
        }

        //Return response
        return false;
    }
    
    /**
     * Check query type
     * @param <string> $sQueryType
     * @return string 
     */
    private function checkQueryType($sQueryType)
    {
        //Check query tyep allowed
        if(!isset(self::$arrQueryType[$sQueryType]))
        {
            return '{"responseHeader":{"status":1,"QTime":0,"params":{"indent":"off","start":"0","q":"solr","wt":"json","version":"2.2","rows":"10"}},"response":{"numFound":0,"start":0,"docs":[]}}';
        }
        
        //Return null data
        return "";
    }

    /**
    * Search query string
    * @param array $options
    * q: The query string, aka the user query or just query for short.
    * This typically originates directly from user input.
    * The query syntax will be discussed shortly.
    * start: This is the zero based index of the first document to be returned from the result set.
    * In other words, this is the number of documents to skip from the beginning of the search results.
    * If this number exceeds the result count, then it will simply return no documents,
    * but it is not considered as an error.
    * rows: This is the number of documents to be returned in the response XML starting at index start.
    * Fewer rows will be returned if there aren't enough matching documents.
    * This number is basically the number of results displayed at a time on your search user interface.
    * fl: This is the field list, separated by commas and/or spaces.
    * These fields are to be returned in the response.
    * Use * to refer to all of the fields but not the score.
    * In order to get the score, you must specify the pseudo-field score.
    * sort: A comma-separated field listing, with a directionality specifier (asc or desc) after each field.
    * Example: r_name asc, score desc.
    * The default is score desc.
    * There is more to sorting than meets the eye, which is explained later in this chapter.
    * wt: A reference to the writer type includes :
    *     + xml (aliased to standard, the default): This is the XML format seen throughout most of the book.
    *     + javabin: A compact binary output used by SolrJ.
    *     + json: The JavaScript Object Notation format for JavaScript clients using eval(). http://www.json.org/
    *     + python: For Python clients using eval().
    *     + php: For PHP clients using eval(). Prefer phps instead.
    *     + phps: PHP's serialization format for use with unserialize(). http://www.hurring.com/scott/code/perl/serialize/
    *     + ruby: For Ruby clients using eval().
    *     + xslt: An extension mechanism using the eXtensible Stylesheet Transformation Language to output other formats.
    * @return : jsonObject
    */
    public function search(array $options=array())
    {        
        //Check wt output
        if(empty($options['wt']))
        {
            $options['wt'] = 'phps';
        }
        
        //Check query type data
        $sResponse = $this->checkQueryType($options['wt']);
        
        //Check response data
        if(!empty($sResponse))
        {
            return $sResponse;
        }

        //Set wt
        $options['wt'] = $options['wt'];
        
        //Check query
        if(empty($options['q']))
        {
            //Set error return
            $errString = '{"responseHeader":{"status":1,"QTime":0,"params":{"indent":"off","start":"0","q":"solr","wt":"json","version":"2.2","rows":"10"}},"response":{"numFound":0,"start":0,"docs":[]}}';
            
            //Check php output
            if($options['wt'] == 'phps')
            {
                return serialize(Zend_Json::decode($errString));
            }
            
            //Return default
            return $errString;
        }
        
        //Check offset start
        if(empty($options['start']))
        {
            $options['start'] = 0;
        }

        //Check limit
        if(empty($options['rows']))
        {
            $options['rows'] = 10;
        }

        //Check field
        if(empty($options['fl']))
        {
            $options['fl'] = '*';
        }

        //Set json.nl (flat,map,arrarr,arrmap)
        $options['json.nl'] = 'map';

        //Check indent
        if(empty($options['indent']))
        {
            $options['indent'] = 'off';
        }

        //Check version
        if(empty($options['version']))
        {
            $options['version'] = '2.2';
        }        

        //Build params
        $params = $this->buildQueryString($options);
        
        //Check multicore
        $solrAddress = $this->solrAddress . '/solr';
        if(!empty($this->solrCore))
        {
            $solrAddress .= '/' .$this->solrCore;
        }
        
        //Check Post Request
        $isPost = isset($options['is.post']) ? 1 : 0;
        
        //Get pool curl
        $curlInstance = self::getPoolCurl();
        
        //Set curl options
        if($isPost == 1)
        {
            curl_setopt($curlInstance, CURLOPT_URL, $solrAddress.'/select');
            curl_setopt($curlInstance, CURLOPT_POST, true);
            curl_setopt($curlInstance, CURLOPT_POSTFIELDS, $params);
            curl_setopt($curlInstance, CURLOPT_BINARYTRANSFER, true);
        }
        else
        {
            curl_setopt($curlInstance, CURLOPT_URL, $solrAddress.'/select?'.$params);
        }
        curl_setopt($curlInstance, CURLOPT_HTTPHEADER, array('Content-Type'=>'text/plain; charset=UTF-8'));
        curl_setopt($curlInstance, CURLOPT_HEADER, false);
        curl_setopt($curlInstance, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);        
        curl_setopt($curlInstance, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlInstance, CURLOPT_TIMEOUT, 60);        

        //Execute curl
        $sResponse = curl_exec($curlInstance);
        
        //Check http status code
        $iHttpCode = curl_getinfo($curlInstance, CURLINFO_HTTP_CODE);

        //Check curl error
        if(curl_errno($curlInstance) || ($iHttpCode != 200))
        {
            //Set error return
            $errString = '{"responseHeader":{"status":'.curl_error(self::$curlInstance).',"QTime":0},"spellcheck":{"suggestions":[]}}';
            
            //Check php output
            if($options['wt'] == 'phps')
            {
                return serialize(Zend_Json::decode($errString));
            }
            
            //Close curl instance
            self::closePoolCurl();
            
            //Return default
            return $errString;
        }

        //Close curl instance
        self::closePoolCurl();
        
        //Return response
        return $sResponse;
    }

    /**
    * Add index to search engine
    * <?xml version="1.0" encoding="UTF-8"?>
    * <response>
    *    <lst name="responseHeader"><int name="status">0</int><int name="QTime">89</int></lst>
    * </response>
    * @param array $arrDocuments
    */
    public function index(array $arrDocuments, $iBoost="1.0")
    {
        //Xml string
        $xml = '<add overwrite="true">';

        //Research array
        $number = 0;
        foreach($arrDocuments as $docs)
        {
            if(is_array($docs) && !empty($docs))
            {
                //Start doc
                $xml .= '<doc boost="'.$iBoost.'">';

                //Add field
                foreach($docs as $field_name => $field_value)
                {                    
                    if(is_array($field_value))
                    {
                        foreach($field_value as $value)
                        {
                            if(is_array($value))
                            {
                                foreach($value as $iBoostField => $valueField)
                                {
                                    $xml .= '<field name="'.htmlspecialchars($field_name, ENT_QUOTES, 'UTF-8').'" boost="'.$iBoostField.'">'.htmlspecialchars($valueField, ENT_NOQUOTES, 'UTF-8').'</field>';
                                }
                            }
                            else
                            {
                                $xml .= '<field name="'.htmlspecialchars($field_name, ENT_QUOTES, 'UTF-8').'">'.htmlspecialchars($value, ENT_NOQUOTES, 'UTF-8').'</field>';
                            }
                        }
                    }
                    else
                    {
                        $xml .= '<field name="'.htmlspecialchars($field_name, ENT_QUOTES, 'UTF-8').'">'.htmlspecialchars($field_value, ENT_NOQUOTES, 'UTF-8').'</field>';
                    }
                }

                //End doc
                $xml .= '</doc>';

                //Increment number
                $number++;
            }
        }

        //Add xml string
        $xml .= '</add>';

        //Strip xml
        $xml = $this->stripCtrlChars($xml);

        //If empty number
        if($number == 0)
        {
            return false;
        }

        //Add params
        $params = array('stream.body'=>$xml,'stream.contentType'=>'text/xml; charset=UTF-8');
        $params = $this->buildQueryString($params);

        //Check multicore
        $solrAddress = $this->solrAddress . '/solr';
        if(!empty($this->solrCore))
        {
            $solrAddress .= '/' .$this->solrCore;
        }
        
        //Get pool curl
        $curlInstance = self::getPoolCurl();

        //Set curl options
        curl_setopt($curlInstance, CURLOPT_URL, $solrAddress.'/update');
        curl_setopt($curlInstance, CURLOPT_HTTPHEADER, array('Content-Type'=>'text/xml; charset=UTF-8'));
        curl_setopt($curlInstance, CURLOPT_HEADER, false);
        curl_setopt($curlInstance, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curlInstance, CURLOPT_POST, true);
        curl_setopt($curlInstance, CURLOPT_POSTFIELDS, $params);
        curl_setopt($curlInstance, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlInstance, CURLOPT_TIMEOUT, 180);
        curl_setopt($curlInstance, CURLOPT_BINARYTRANSFER, true);

        //Execute curl
        $sResponse = curl_exec($curlInstance);
        
        //Check http status code
        $iHttpCode = curl_getinfo($curlInstance, CURLINFO_HTTP_CODE);

        //Check curl error
        if(curl_errno($curlInstance) || ($iHttpCode != 200))        
        {
            //Close curl instance
            self::closePoolCurl();
        
            //Return data
            return false;
        }
        
        //Close curl instance
        self::closePoolCurl();
        
        //Convert to json
        $arrResponse = Zend_Json::fromXml($sResponse, true);
        $arrResponse = Zend_Json::decode($arrResponse, true);
        
        //Check status
        if($arrResponse['response']['lst']['int'][0] == 0)
        {
            return true;
        }

        //Return response
        return false;
    }

    /**
    * Update index to search engine
    * <?xml version="1.0" encoding="UTF-8"?>
    * <response>
    *    <lst name="responseHeader"><int name="status">0</int><int name="QTime">89</int></lst>
    * </response>
    * @param array $arrDocuments
    */
    public function update(array $arrDocuments, $uniqueID = "", $iBoost="1.0")
    {
        //Xml string
        $xml = '<add overwrite="true">';

        //Set default data
        $number = 0;
                
        //Research array
        foreach($arrDocuments as $docs)
        {
            if(is_array($docs) && !empty($docs))
            {
                //Start doc
                $xml .= '<doc boost="'.$iBoost.'">';

                //Add field
                foreach($docs as $field_name => $field_value)
                {
                    //Check unique filed
                    $sAddPlus = '';
                    if(($field_name != $uniqueID) && !empty($uniqueID))
                    {
                        $sAddPlus = 'update="set"';
                    }
                    
                    //Check array data
                    if(is_array($field_value))
                    {
                        foreach($field_value as $value)
                        {
                            if(is_array($value))
                            {
                                foreach($value as $iBoostField => $valueField)
                                {
                                    $xml .= '<field name="'.htmlspecialchars($field_name, ENT_QUOTES, 'UTF-8').'" boost="'.$iBoostField.'">'.htmlspecialchars($valueField, ENT_NOQUOTES, 'UTF-8').'</field>';
                                }
                            }
                            else
                            {
                                $xml .= '<field name="'.htmlspecialchars($field_name, ENT_QUOTES, 'UTF-8').'">'.htmlspecialchars($value, ENT_NOQUOTES, 'UTF-8').'</field>';
                            }                            
                        }
                    }
                    else
                    {
                        $xml .= '<field '. $sAddPlus .' name="'.htmlspecialchars($field_name, ENT_QUOTES, 'UTF-8').'">'.htmlspecialchars($field_value, ENT_QUOTES, 'UTF-8').'</field>';
                    }                    
                }

                //End doc
                $xml .= '</doc>';

                //Increment number
                $number++;
            }
        }

        //Add xml string
        $xml .= '</add>';

        //Strip xml
        $xml = $this->stripCtrlChars($xml);

        //If empty number
        if($number == 0)
        {
            return false;
        }

        //Add params
        $params = array('stream.body'=>$xml,'stream.contentType'=>'text/xml; charset=UTF-8');
        $params = $this->buildQueryString($params);

        //Check multicore
        $solrAddress = $this->solrAddress . '/solr';
        if(!empty($this->solrCore))
        {
            $solrAddress .= '/' .$this->solrCore;
        }

        //Get pool curl
        $curlInstance = self::getPoolCurl();
        
        //Set curl options
        curl_setopt($curlInstance, CURLOPT_URL, $solrAddress.'/update');
        curl_setopt($curlInstance, CURLOPT_HTTPHEADER, array('Content-Type'=>'text/xml; charset=utf-8'));
        curl_setopt($curlInstance, CURLOPT_HEADER, false);
        curl_setopt($curlInstance, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curlInstance, CURLOPT_POST, true);
        curl_setopt($curlInstance, CURLOPT_POSTFIELDS, $params);
        curl_setopt($curlInstance, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlInstance, CURLOPT_TIMEOUT, 180);
        curl_setopt($curlInstance, CURLOPT_BINARYTRANSFER, true);

        //Execute curl
        $sResponse = curl_exec($curlInstance);

        //Check http status code
        $iHttpCode = curl_getinfo($curlInstance, CURLINFO_HTTP_CODE);

        //Check curl error
        if(curl_errno($curlInstance) || ($iHttpCode != 200))
        {
            //Close curl instance
            self::closePoolCurl();
        
            //Return data
            return false;
        }
        
        //Close curl instance
        self::closePoolCurl();

        //Convert to json
        $arrResponse = Zend_Json::fromXml($sResponse, true);
        $arrResponse = Zend_Json::decode($arrResponse, true);
        
        //Check status
        if($arrResponse['response']['lst']['int'][0] == 0)
        {
            return true;
        }
        
        //Return response
        return false;
    }
    
    /**
    * Add index to search engine
    * <?xml version="1.0" encoding="UTF-8"?>
    * <response>
    *    <lst name="responseHeader"><int name="status">0</int><int name="QTime">89</int></lst>
    * </response>
    * @param array $arrDocuments
    */
    public function add(array $arrDocuments, $uniqueID="", $iBoost="1.0")
    {
        //Xml string
        $xml = '<add overwrite="true">';

        //Set default data
        $number = 0;
                
        //Research array
        foreach($arrDocuments as $docs)
        {
            if(is_array($docs) && !empty($docs))
            {
                //Start doc
                $xml .= '<doc boost="'.$iBoost.'">';

                //Add field
                foreach($docs as $field_name => $field_value)
                {
                    //Check unique filed
                    $sAddPlus = '';
                    if(($field_name != $uniqueID) && !empty($uniqueID))
                    {
                        $sAddPlus = 'update="add"';
                    }

                    //Check array data
                    if(is_array($field_value))
                    {
                        foreach($field_value as $value)
                        {                            
                            if(is_array($value))
                            {
                                foreach($value as $iBoostField => $valueField)
                                {
                                    $xml .= '<field name="'.htmlspecialchars($field_name, ENT_QUOTES, 'UTF-8').'" boost="'.$iBoostField.'">'.htmlspecialchars($valueField, ENT_NOQUOTES, 'UTF-8').'</field>';
                                }
                            }
                            else
                            {
                                $xml .= '<field name="'.htmlspecialchars($field_name, ENT_QUOTES, 'UTF-8').'">'.htmlspecialchars($value, ENT_NOQUOTES, 'UTF-8').'</field>';
                            }                            
                        }
                    }
                    else
                    {
                        $xml .= '<field '. $sAddPlus .' name="'.htmlspecialchars($field_name, ENT_QUOTES, 'UTF-8').'">'.htmlspecialchars($field_value, ENT_QUOTES, 'UTF-8').'</field>';
                    }                    
                }

                //End doc
                $xml .= '</doc>';

                //Increment number
                $number++;
            }
        }

        //Add xml string
        $xml .= '</add>';

        //Strip xml
        $xml = $this->stripCtrlChars($xml);

        //If empty number
        if($number == 0)
        {
            return false;
        }

        //Add params
        $params = array('stream.body'=>$xml,'stream.contentType'=>'text/xml; charset=UTF-8');
        $params = $this->buildQueryString($params);

        //Check multicore
        $solrAddress = $this->solrAddress . '/solr';
        if(!empty($this->solrCore))
        {
            $solrAddress .= '/' .$this->solrCore;
        }

        //Get pool curl
        $curlInstance = self::getPoolCurl();
        
        //Set curl options
        curl_setopt($curlInstance, CURLOPT_URL, $solrAddress.'/update');
        curl_setopt($curlInstance, CURLOPT_HTTPHEADER, array('Content-Type'=>'text/xml; charset=utf-8'));
        curl_setopt($curlInstance, CURLOPT_HEADER, false);
        curl_setopt($curlInstance, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curlInstance, CURLOPT_POST, true);
        curl_setopt($curlInstance, CURLOPT_POSTFIELDS, $params);
        curl_setopt($curlInstance, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlInstance, CURLOPT_TIMEOUT, 180);
        curl_setopt($curlInstance, CURLOPT_BINARYTRANSFER, true);

        //Execute curl
        $sResponse = curl_exec($curlInstance);

        //Check http status code
        $iHttpCode = curl_getinfo($curlInstance, CURLINFO_HTTP_CODE);

        //Check curl error
        if(curl_errno($curlInstance) || ($iHttpCode != 200))
        {
            //Close curl instance
            self::closePoolCurl();
        
            //Return data
            return false;
        }
        
        //Close curl instance
        self::closePoolCurl();

        //Convert to json
        $arrResponse = Zend_Json::fromXml($sResponse, true);
        $arrResponse = Zend_Json::decode($arrResponse, true);
        
        //Check status
        if($arrResponse['response']['lst']['int'][0] == 0)
        {
            return true;
        }
        
        //Return response
        return false;
    }
    
    /**
    * Increment index to search engine
    * <?xml version="1.0" encoding="UTF-8"?>
    * <response>
    *    <lst name="responseHeader"><int name="status">0</int><int name="QTime">89</int></lst>
    * </response>
    * @param array $arrDocuments
    */
    public function increment(array $arrDocuments, $uniqueID="", $iBoost="1.0")
    {
        //Xml string
        $xml = '<add overwrite="true">';

        //Set default data
        $number = 0;
                
        //Research array
        foreach($arrDocuments as $docs)
        {
            if(is_array($docs) && !empty($docs))
            {
                //Start doc
                $xml .= '<doc boost="'.$iBoost.'">';

                //Add field
                foreach($docs as $field_name => $field_value)
                {
                    //Check unique filed
                    $sAddPlus = '';
                    if(($field_name != $uniqueID) && !empty($uniqueID))
                    {
                        $sAddPlus = 'update="inc"';
                    }
                        
                    //Check array data
                    if(is_array($field_value))
                    {
                        //Check array data
                        foreach($field_value as $value)
                        {
                            if(is_array($value))
                            {
                                foreach($value as $iBoostField => $valueField)
                                {
                                    $xml .= '<field name="'.htmlspecialchars($field_name, ENT_QUOTES, 'UTF-8').'" boost="'.$iBoostField.'">'.htmlspecialchars($valueField, ENT_NOQUOTES, 'UTF-8').'</field>';
                                }
                            }
                            else
                            {
                                $xml .= '<field name="'.htmlspecialchars($field_name, ENT_QUOTES, 'UTF-8').'">'.htmlspecialchars($value, ENT_NOQUOTES, 'UTF-8').'</field>';
                            }                            
                        }
                    }
                    else
                    {
                        $xml .= '<field '. $sAddPlus .' name="'.htmlspecialchars($field_name, ENT_QUOTES, 'UTF-8').'">'.htmlspecialchars($field_value, ENT_QUOTES, 'UTF-8').'</field>';
                    }                    
                }

                //End doc
                $xml .= '</doc>';

                //Increment number
                $number++;
            }
        }

        //Add xml string
        $xml .= '</add>';

        //Strip xml
        $xml = $this->stripCtrlChars($xml);

        //If empty number
        if($number == 0)
        {
            return false;
        }

        //Add params
        $params = array('stream.body'=>$xml,'stream.contentType'=>'text/xml; charset=UTF-8');
        $params = $this->buildQueryString($params);

        //Check multicore
        $solrAddress = $this->solrAddress . '/solr';
        if(!empty($this->solrCore))
        {
            $solrAddress .= '/' .$this->solrCore;
        }

        //Get pool curl
        $curlInstance = self::getPoolCurl();
        
        //Set curl options
        curl_setopt($curlInstance, CURLOPT_URL, $solrAddress.'/update');
        curl_setopt($curlInstance, CURLOPT_HTTPHEADER, array('Content-Type'=>'text/xml; charset=utf-8'));
        curl_setopt($curlInstance, CURLOPT_HEADER, false);
        curl_setopt($curlInstance, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curlInstance, CURLOPT_POST, true);
        curl_setopt($curlInstance, CURLOPT_POSTFIELDS, $params);
        curl_setopt($curlInstance, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlInstance, CURLOPT_TIMEOUT, 180);
        curl_setopt($curlInstance, CURLOPT_BINARYTRANSFER, true);

        //Execute curl
        $sResponse = curl_exec($curlInstance);

        //Check http status code
        $iHttpCode = curl_getinfo($curlInstance, CURLINFO_HTTP_CODE);

        //Check curl error
        if(curl_errno($curlInstance) || ($iHttpCode != 200))
        {
            //Close curl instance
            self::closePoolCurl();
        
            //Return data
            return false;
        }
        
        //Close curl instance
        self::closePoolCurl();

        //Convert to json
        $arrResponse = Zend_Json::fromXml($sResponse, true);
        $arrResponse = Zend_Json::decode($arrResponse, true);
        
        //Check status
        if($arrResponse['response']['lst']['int'][0] == 0)
        {
            return true;
        }
        
        //Return response
        return false;
    }

    /**
    * Delete index by id
    * ID here means the value of the uniqueKey field declared in the schema
    * @param var $id
    */
    public function deleteById($id)
    {
        //Check id
        if(is_null($id))
        {
            return false;
        }

        //Xml string
        $xml = '<delete fromPending="true" fromCommitted="true"><id>'.htmlspecialchars($id, ENT_NOQUOTES, 'UTF-8').'</id></delete>';

        //Add params
        $params = array('stream.body'=>$xml,'stream.contentType'=>'text/xml; charset=UTF-8');
        $params = $this->buildQueryString($params);

        //Check multicore
        $solrAddress = $this->solrAddress . '/solr';
        if(!empty($this->solrCore))
        {
            $solrAddress .= '/' .$this->solrCore;
        }

        //Get pool curl
        $curlInstance = self::getPoolCurl();
        
        //Set curl options
        curl_setopt($curlInstance, CURLOPT_URL, $solrAddress.'/update');
        curl_setopt($curlInstance, CURLOPT_HTTPHEADER, array('Content-Type'=>'text/xml; charset=utf-8'));
        curl_setopt($curlInstance, CURLOPT_HEADER, false);
        curl_setopt($curlInstance, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curlInstance, CURLOPT_POST, true);
        curl_setopt($curlInstance, CURLOPT_POSTFIELDS, $params);
        curl_setopt($curlInstance, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlInstance, CURLOPT_TIMEOUT, 180);
        curl_setopt($curlInstance, CURLOPT_BINARYTRANSFER, true);

        //Execute curl
        $sResponse = curl_exec($curlInstance);

        //Check http status code
        $iHttpCode = curl_getinfo($curlInstance, CURLINFO_HTTP_CODE);

        //Check curl error
        if(curl_errno($curlInstance) || ($iHttpCode != 200))
        {
            //Close curl instance
            self::closePoolCurl();
        
            //Return data
            return false;
        }
        
        //Close curl instance
        self::closePoolCurl();

        //Convert to json
        $arrResponse = Zend_Json::fromXml($sResponse, true);
        $arrResponse = Zend_Json::decode($arrResponse, true);
        
        //Check status
        if($arrResponse['response']['lst']['int'][0] == 0)
        {
            return true;
        }
        
        //Return response
        return false;
    }

    /**
    * Delete index by array id
    * @param array $arrIds
    */
    public function deleteByIds(array $arrIds=array())
    {
        //Check id
        if(sizeof($arrIds) == 0)
        {
            return false;
        }

        //Xml string
        $xml = '<delete fromPending="true" fromCommitted="true">';
        foreach($arrIds as $id)
        {
                $xml .= '<id>'.htmlspecialchars($id, ENT_NOQUOTES, 'UTF-8').'</id>';
        }
        $xml .= '</delete>';

        //Add params
        $params = array('stream.body'=>$xml,'stream.contentType'=>'text/xml; charset=UTF-8');
        $params = $this->buildQueryString($params);

        //Check multicore
        $solrAddress = $this->solrAddress . '/solr';
        if(!empty($this->solrCore))
        {
            $solrAddress .= '/' .$this->solrCore;
        }
        
        //Get pool curl
        $curlInstance = self::getPoolCurl();

        //Set curl options
        curl_setopt($curlInstance, CURLOPT_URL, $solrAddress.'/update');
        curl_setopt($curlInstance, CURLOPT_HTTPHEADER, array('Content-Type'=>'text/xml; charset=utf-8'));
        curl_setopt($curlInstance, CURLOPT_HEADER, false);
        curl_setopt($curlInstance, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curlInstance, CURLOPT_POST, true);
        curl_setopt($curlInstance, CURLOPT_POSTFIELDS, $params);
        curl_setopt($curlInstance, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlInstance, CURLOPT_TIMEOUT, 180);
        curl_setopt($curlInstance, CURLOPT_BINARYTRANSFER, true);

        //Execute curl
        $sResponse = curl_exec($curlInstance);

        //Check http status code
        $iHttpCode = curl_getinfo($curlInstance, CURLINFO_HTTP_CODE);

        //Check curl error
        if(curl_errno($curlInstance) || ($iHttpCode != 200))
        {
            //Close curl instance
            self::closePoolCurl();
        
            //Return data
            return false;
        }
        
        //Close curl instance
        self::closePoolCurl();
        
        //Convert to json
        $arrResponse = Zend_Json::fromXml($sResponse, true);
        $arrResponse = Zend_Json::decode($arrResponse, true);
        
        //Check status
        if($arrResponse['response']['lst']['int'][0] == 0)
        {
            return true;
        }
        
        //Return response
        return false;
    }

    /**
    * Delete index by query string
    * @param string $query
    */
    public function deleteByQuery(string $query)
    {
        //Check query string
        if(empty($query))
        {
            return false;
        }

        //Xml string
        $xml = '<delete><query>'.htmlspecialchars($query, ENT_NOQUOTES, 'UTF-8').'</query></delete>';

        //Add params
        $params = array('stream.body'=>$xml,'stream.contentType'=>'text/xml; charset=UTF-8');
        $params = $this->buildQueryString($params);

        //Check multicore
        $solrAddress = $this->solrAddress . '/solr';
        if(!empty($this->solrCore))
        {
            $solrAddress .= '/' .$this->solrCore;
        }

        //Get pool curl
        $curlInstance = self::getPoolCurl();
        
        //Set curl options
        curl_setopt($curlInstance, CURLOPT_URL, $solrAddress.'/update');
        curl_setopt($curlInstance, CURLOPT_HTTPHEADER, array('Content-Type'=>'text/xml; charset=utf-8'));
        curl_setopt($curlInstance, CURLOPT_HEADER, false);
        curl_setopt($curlInstance, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curlInstance, CURLOPT_POST, true);
        curl_setopt($curlInstance, CURLOPT_POSTFIELDS, $params);
        curl_setopt($curlInstance, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlInstance, CURLOPT_TIMEOUT, 180);
        curl_setopt($curlInstance, CURLOPT_BINARYTRANSFER, true);

        //Execute curl
        $sResponse = curl_exec($curlInstance);

        //Check http status code
        $iHttpCode = curl_getinfo($curlInstance, CURLINFO_HTTP_CODE);

        //Check curl error
        if(curl_errno($curlInstance) || ($iHttpCode != 200))
        {
            //Close curl instance
            self::closePoolCurl();
            
            //Return data
            return false;
        }
        
        //Close curl instance
        self::closePoolCurl();
        
        //Convert to json
        $arrResponse = Zend_Json::fromXml($sResponse, true);
        $arrResponse = Zend_Json::decode($arrResponse, true);
        
        //Check status
        if($arrResponse['response']['lst']['int'][0] == 0)
        {
            return true;
        }

        //Return response
        return false;
    }

    /**
    * Commit search engine
    * @param array $options
    */
    public function commit(array $options=array())
    {
        //Xml string
        $xml = '<commit/>';

        //Add params
        $params = array('stream.body'=>$xml,'stream.contentType'=>'text/xml; charset=UTF-8');
        $params = $this->buildQueryString($params);

        //Check multicore
        $solrAddress = $this->solrAddress . '/solr';
        if(!empty($this->solrCore))
        {
            $solrAddress .= '/' .$this->solrCore;
        }

        //Get pool curl
        $curlInstance = self::getPoolCurl();
        
        //Set curl options
        curl_setopt($curlInstance, CURLOPT_URL, $solrAddress.'/update');
        curl_setopt($curlInstance, CURLOPT_HTTPHEADER, array('Content-Type'=>'text/xml; charset=utf-8'));
        curl_setopt($curlInstance, CURLOPT_HEADER, false);
        curl_setopt($curlInstance, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curlInstance, CURLOPT_POST, true);
        curl_setopt($curlInstance, CURLOPT_POSTFIELDS, $params);
        curl_setopt($curlInstance, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlInstance, CURLOPT_TIMEOUT, 180);
        curl_setopt($curlInstance, CURLOPT_BINARYTRANSFER, true);

        //Execute curl
        curl_exec($curlInstance);

        //Check http status code
        $iHttpCode = curl_getinfo($curlInstance, CURLINFO_HTTP_CODE);

        //Check curl error
        if(curl_errno($curlInstance) || ($iHttpCode != 200))
        {
            //Close curl instance
            self::closePoolCurl();
            
            //Return data
            return false;
        }
        
        //Close curl instance
        self::closePoolCurl();

        //Return response
        return true;
    }

    /**
    * Optimize search engine
    * @param array $options
    */
    public function optimize(array $options=array())
    {
        //Xml string
        $xml = '<optimize/>';

        //Add params
        $params = array('stream.body'=>$xml,'stream.contentType'=>'text/xml; charset=UTF-8');
        $params = $this->buildQueryString($params);

        //Check multicore
        $solrAddress = $this->solrAddress . '/solr';
        if(!empty($this->solrCore))
        {
            $solrAddress .= '/' .$this->solrCore;
        }
        
        //Get pool curl
        $curlInstance = self::getPoolCurl();

        //Set curl options
        curl_setopt($curlInstance, CURLOPT_URL, $solrAddress.'/update');
        curl_setopt($curlInstance, CURLOPT_HTTPHEADER, array('Content-Type'=>'text/xml; charset=utf-8'));
        curl_setopt($curlInstance, CURLOPT_HEADER, false);
        curl_setopt($curlInstance, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curlInstance, CURLOPT_POST, true);
        curl_setopt($curlInstance, CURLOPT_POSTFIELDS, $params);
        curl_setopt($curlInstance, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlInstance, CURLOPT_TIMEOUT, 180);
        curl_setopt($curlInstance, CURLOPT_BINARYTRANSFER, true);

        //Execute curl
        curl_exec($curlInstance);

        //Check http status code
        $iHttpCode = curl_getinfo($curlInstance, CURLINFO_HTTP_CODE);

        //Check curl error
        if(curl_errno($curlInstance) || ($iHttpCode != 200))
        {
            //Close curl instance
            self::closePoolCurl();
            
            //Return data
            return false;
        }
        
        //Close curl instance
        self::closePoolCurl();

        //Return response
        return true;
    }

    /**
    * The rollback command rollbacks all add/deletes made to the index since the last commit.
    * It neither calls any event listeners nor creates a new searcher.
    */
    public function rollback()
    {
        //Xml string
        $xml = '<rollback/>';

        //Add params
        $params = array('stream.body'=>$xml,'stream.contentType'=>'text/xml; charset=UTF-8');
        $params = $this->buildQueryString($params);

        //Check multicore
        $solrAddress = $this->solrAddress . '/solr';
        if(!empty($this->solrCore))
        {
            $solrAddress .= '/' .$this->solrCore;
        }

        //Get pool curl
        $curlInstance = self::getPoolCurl();
        
        //Set curl options
        curl_setopt($curlInstance, CURLOPT_URL, $solrAddress.'/update');
        curl_setopt($curlInstance, CURLOPT_HTTPHEADER, array('Content-Type'=>'text/xml; charset=utf-8'));
        curl_setopt($curlInstance, CURLOPT_HEADER, false);
        curl_setopt($curlInstance, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curlInstance, CURLOPT_POST, true);
        curl_setopt($curlInstance, CURLOPT_POSTFIELDS, $params);
        curl_setopt($curlInstance, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlInstance, CURLOPT_TIMEOUT, 180);
        curl_setopt($curlInstance, CURLOPT_BINARYTRANSFER, true);

        //Execute curl
        curl_exec($curlInstance);

        //Check http status code
        $iHttpCode = curl_getinfo($curlInstance, CURLINFO_HTTP_CODE);

        //Check curl error
        if(curl_errno($curlInstance) || ($iHttpCode != 200))
        {
            //Close curl instance
            self::closePoolCurl();
            
            //Return data
            return false;
        }
        
        //Close curl instance
        self::closePoolCurl();

        //Return response
        return true;
    }

    /**
    * rebuild all index
    */
    public function flush()
    {
        //Xml string
        $xml = '<delete><query>*:*</query></delete>';

        //Add params
        $params = array('stream.body'=>$xml,'stream.contentType'=>'text/xml; charset=UTF-8');
        $params = $this->buildQueryString($params);

        //Check multicore
        $solrAddress = $this->solrAddress . '/solr';
        if(!empty($this->solrCore))
        {
            $solrAddress .= '/' .$this->solrCore;
        }

        //Get pool curl
        $curlInstance = self::getPoolCurl();
        
        //Set curl options
        curl_setopt($curlInstance, CURLOPT_URL, $solrAddress.'/update');
        curl_setopt($curlInstance, CURLOPT_HTTPHEADER, array('Content-Type'=>'text/xml; charset=utf-8'));
        curl_setopt($curlInstance, CURLOPT_HEADER, false);
        curl_setopt($curlInstance, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curlInstance, CURLOPT_POST, true);
        curl_setopt($curlInstance, CURLOPT_POSTFIELDS, $params);
        curl_setopt($curlInstance, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlInstance, CURLOPT_TIMEOUT, 180);
        curl_setopt($curlInstance, CURLOPT_BINARYTRANSFER, true);

        //Execute curl
        curl_exec($curlInstance);

        //Check http status code
        $iHttpCode = curl_getinfo($curlInstance, CURLINFO_HTTP_CODE);

        //Check curl error
        if(curl_errno($curlInstance) || ($iHttpCode != 200))
        {
            //Close curl instance
            self::closePoolCurl();
            
            //Return data
            return false;
        }
        
        //Close curl instance
        self::closePoolCurl();

        //Return response
        return true;
    }
    
    /**
    * Suggest query string
    * @param array $options
    * q: The query string, aka the user query or just query for short.
    * This typically originates directly from user input.
    * The query syntax will be discussed shortly.
    * start: This is the zero based index of the first document to be returned from the result set.
    * In other words, this is the number of documents to skip from the beginning of the search results.
    * If this number exceeds the result count, then it will simply return no documents,
    * but it is not considered as an error.
    * rows: This is the number of documents to be returned in the response XML starting at index start.
    * Fewer rows will be returned if there aren't enough matching documents.
    * This number is basically the number of results displayed at a time on your search user interface.
    * fl: This is the field list, separated by commas and/or spaces.
    * These fields are to be returned in the response.
    * Use * to refer to all of the fields but not the score.
    * In order to get the score, you must specify the pseudo-field score.
    * sort: A comma-separated field listing, with a directionality specifier (asc or desc) after each field.
    * Example: r_name asc, score desc.
    * The default is score desc.
    * There is more to sorting than meets the eye, which is explained later in this chapter.
    * wt: A reference to the writer type includes :
    *     + xml (aliased to standard, the default): This is the XML format seen throughout most of the book.
    *     + javabin: A compact binary output used by SolrJ.
    *     + json: The JavaScript Object Notation format for JavaScript clients using eval(). http://www.json.org/
    *     + python: For Python clients using eval().
    *     + php: For PHP clients using eval(). Prefer phps instead.
    *     + phps: PHP's serialization format for use with unserialize(). http://www.hurring.com/scott/code/perl/serialize/
    *     + ruby: For Ruby clients using eval().
    *     + xslt: An extension mechanism using the eXtensible Stylesheet Transformation Language to output other formats.
    * @return : jsonObject
    */
    public function suggest(array $options=array())
    {        
        //Check wt output
        if(empty($options['wt']))
        {
            $options['wt'] = 'phps';
        }

        //Check query type data
        $response = $this->checkQueryType($options['wt']);
        
        //Check response data
        if(!empty($response))
        {
            return $response;
        }
        
        //Check query
        if(empty($options['q']))
        {
            //Set error return
            $errString = '{"responseHeader":{"status":0,"QTime":0},"spellcheck":{"suggestions":[]}}';
            
            //Check php output
            if($options['wt'] == 'phps')
            {
                return serialize(Zend_Json::decode($errString));
            }
            
            //Return default
            return $errString;
        }   

        //Set json.nl (flat,map,arrarr,arrmap)
        $options['json.nl'] = 'map';

        //Check indent
        if(empty($options['indent']))
        {
            $options['indent'] = 'off';
        }

        //Check version
        if(empty($options['version']))
        {
            $options['version'] = '2.2';
        }        

        //Build params
        $params = $this->buildQueryString($options);
        
        //Check multicore
        $solrAddress = $this->solrAddress . '/solr';
        if(!empty($this->solrCore))
        {
            $solrAddress .= '/' .$this->solrCore;
        }
        
        //Check Post Request
        $isPost = isset($options['is.post']) ? 1 : 0;
        
        //Get pool curl
        $curlInstance = self::getPoolCurl();
        
        //Set curl options
        if($isPost == 1)
        {
            curl_setopt($curlInstance, CURLOPT_URL, $solrAddress.'/suggest');
            curl_setopt($curlInstance, CURLOPT_POST, true);
            curl_setopt($curlInstance, CURLOPT_POSTFIELDS, $params);
            curl_setopt($curlInstance, CURLOPT_BINARYTRANSFER, true);
        }
        else
        {
            curl_setopt($curlInstance, CURLOPT_URL, $solrAddress.'/suggest?'.$params);
        }
        curl_setopt($curlInstance, CURLOPT_HTTPHEADER, array('Content-Type'=>'text/plain; charset=UTF-8'));
        curl_setopt($curlInstance, CURLOPT_HEADER, false);
        curl_setopt($curlInstance, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);        
        curl_setopt($curlInstance, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlInstance, CURLOPT_TIMEOUT, 60);        

        //Execute curl
        $response = curl_exec($curlInstance);

        //Check http status code
        $iHttpCode = curl_getinfo($curlInstance, CURLINFO_HTTP_CODE);

        //Check curl error
        if(curl_errno($curlInstance) || ($iHttpCode != 200))
        {
            //Set error return
            $errString = '{"responseHeader":{"status":'.curl_error(self::$curlInstance).',"QTime":0},"spellcheck":{"suggestions":[]}}';
            
            //Check php output
            if($options['wt'] == 'phps')
            {
                return serialize(Zend_Json::decode($errString));
            }
            
            //Close curl instance
            self::closePoolCurl();
            
            //Return default
            return $errString;
        }

        //Close curl instance
        self::closePoolCurl();
        
        //Return response
        return $response;
    }
}

