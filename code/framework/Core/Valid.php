<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Valid
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to valid data
 */
class Core_Valid
{
    /**
     * List all Vietnamese Utf8
     * @var <array>
     */
    private static $arrCharFrom = array("ạ","á","à","ả","ã","Ạ","Á","À","Ả","Ã","â","ậ","ấ","ầ","ẩ","ẫ","Â","Ậ","Ấ","Ầ","Ẩ","Ẫ","ă","ặ","ắ","ằ","ẳ","ẵ","ẫ","Ă","Ắ","Ằ","Ẳ","Ẵ","Ặ","Ẵ","ê","ẹ","é","è","ẻ","ẽ","Ê","Ẹ","É","È","Ẻ","Ẽ","ế","ề","ể","ễ","ệ","Ế","Ề","Ể","Ễ","Ệ","ọ","ộ","ổ","ỗ","ố","ồ","Ọ","Ộ","Ổ","Ỗ","Ố","Ồ","Ô","ô","ó","ò","ỏ","õ","Ó","Ò","Ỏ","Õ","ơ","ợ","ớ","ờ","ở","ỡ","Ơ","Ợ","Ớ","Ờ","Ở","Ỡ","ụ","ư","ứ","ừ","ử","ữ","ự","Ụ","Ư","Ứ","Ừ","Ử","Ữ","Ự","ú","ù","ủ","ũ","Ú","Ù","Ủ","Ũ","ị","í","ì","ỉ","ĩ","Ị","Í","Ì","Ỉ","Ĩ","ỵ","ý","ỳ","ỷ","ỹ","Ỵ","Ý","Ỳ","Ỷ","Ỹ","đ","Đ");

    /**
     * List all Alphabet
     * @var <array>
     */
    private static $arrAlphabet = array('a','á','à','ã','ả','ạ','ă','ắ','ằ','ẵ','ẳ','ặ','â','ấ','ầ','ẫ','ẩ','ậ','A','Á','À','Ã','Ả','Ạ','Ă','Ắ','Ằ','Ẵ','Ẳ','Ặ','Â','Ấ','Ầ','Ẫ','Ẩ','Ậ','b','c','B','C','d','đ','D','Đ','e','é','è','ẽ','ẻ','ẹ','ế','ề','ê','ễ','ể','ệ','E','É','È','Ẽ','Ẻ','Ẹ','Ế','Ề','Ê','Ễ','Ể','Ệ','f','g','h','F','G','H','i','í','ì','ĩ','ỉ','ị','I','Í','Ì','Ĩ','Ỉ','Ị','j','k','l','m','n','J','K','L','M','N','o','ó','ò','õ','ỏ','ọ','ô','ố','ồ','ỗ','ổ','ộ','ơ','ớ','ờ','ỡ','ở','ợ','O','Ó','Ò','Õ','Ỏ','Ọ','Ô','Ố','Ồ','Ỗ','Ổ','Ộ','Ơ','Ớ','Ờ','Ỡ','Ở','Ợ','p','q','r','s','t','P','Q','R','S','T','u','ú','ù','ũ','ủ','ụ','ư','ứ','ừ','ữ','ử','ự','U','Ú','Ù','Ũ','Ủ','Ụ','Ư','Ứ','Ừ','Ữ','Ử','Ự','v','w','x','V','W','X','y','ý','ỳ','ỹ','ỷ','ỵ','Y','Ý','Ỳ','Ỹ','Ỷ','Ỵ','z','Z',' ','1','2','3','4','5','6','7','8','9','0');

    /**
    * Check is Ascii string
    * @param <string> $string
    * @return <string>
    */
    public static function isAsciiString($string)
    {
        return !(preg_match('/[^\x00-\x7F]/S', $string));
    }

    /**
     * Check is invalid utf8 string
     * @param <string> $string
     * @return <boolean>
     */
    public static function isInvalidUtf8String($string)
    {
        //Set dault data
        $array = array();
        
        //Check invalid char
        if(preg_match('/^.{1}/us', $string, $array) != 1)
        {
            return true;
        }
        return false;
    }

    /**
     * Check string is VN type
     * @param <string> $string
     * @return <boolean>
     */
    public static function isWordVn($string)
    {
        $length = count(self::$arrCharFrom);
        for($i=0; $i<$length; $i++)
        {
            if(Core_String::strposString($string, self::$arrCharFrom[$i]))
            {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Check string is Japanese type
     * @param <string> $string
     * @return <boolean>
     */
    public static function isWordJP($string)
    {
        return preg_match('/[\x{4E00}-\x{9FBF}\x{3040}-\x{309F}\x{30A0}-\x{30FF}]/u', $string);
    }
    
    /**
     * Check string is Japanese Kanji type
     * @param <string> $string
     * @return <boolean>
     */
    public static function isWordKanji($string)
    {
        return preg_match('/[\x{4E00}-\x{9FBF}]/u', $string) > 0;
    }
    
    /**
     * Check string is Japanese Hiragana type
     * @param <string> $string
     * @return <boolean>
     */
    public static function isWordHiragana($string)
    {
        return preg_match('/[\x{3040}-\x{309F}]/u', $string) > 0;
    }
    
    /**
     * Check string is Japanese Katakana type
     * @param <string> $string
     * @return <boolean>
     */
    public static function isWordKatakana($string)
    {
        return preg_match('/[\x{30A0}-\x{30FF}]/u', $string) > 0;
    }

    /**
     * Check valid email
     * @param <string> $email
     * @return <boolean>
     */
    public static function isEmail($email)
    {
        if(!preg_match("/^([a-zA-Z0-9_.-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)+/", $email))
        {
            return false;
    	}
        return true;
    }

    /**
     * Check valid vn Alphabet
     * @param <string> $string
     * @return <boolean>
     */
    public static function isAlphabetVn($string)
    {
        //Set default data
        $nameArr = array();
        
        //Check data
        preg_match_all('/./u', $string, $nameArr);
        
        //Loop to check data
        foreach($nameArr[0] as $char)
        {
            if(!in_array($char, self::$arrAlphabet))
            {
                return false;
            }
        }
        
        //Return default
        return true;
    }

    /**
     * Check valid phone number
     * @param <string> $string
     * @return <boolean>
     */
    public static function isPhone($string)
    {
        return (!preg_match('/^[0-9+$/i', $string))?true:false;
    }

    /**
     * Check valid Alphabet
     * @param <string> $string
     * @return <boolean>
     */
    public static function isAlphabet($string)
    {
        return ctype_alnum($string);
    }

    /**
     * Check valid url
     * @param <string> $url
     * @return <boolean>
     */
    public static function isUrl($url)
    {
        return (!preg_match('/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i',$url))?false:true;
    }
    
    /**
     * Check valid image
     * @param <string> $url
     * @return <boolean>
     */
    public static function isImageUrl($url)
    {
        //Get final character
        $iPos = strrpos( $url, ".");
        
        //Check pos identifier
        if($iPos === false)
        {
            return false;
        }
        
        //Get extension
        $sExtension = strtolower(trim(substr( $url, $iPos)));
        
        //List images
        $arrExtension = array(
            ".gif", 
            ".jpg", 
            ".jpeg", 
            ".png", 
            ".tiff", 
            ".tif",
            ".bmp"
        );
        
        //Check in list
        if(in_array($sExtension, $arrExtension))
        {
            return true;
        }
        
        //Return data
        return false;
    }

    /**
     * Check valid Alpha
     * @param <string> $string
     * @return <boolean>
     */
    public static function isAlpha($string)
    {
        return ctype_alpha($string);
    }

    /**
     * Check valid number
     * @param <string> $string
     * @return <boolean>
     */
    public static function isNumber($string)
    {
        return preg_match('/^[0-9]+$/',$string)?true:false;
    }

    /**
     * Check valid timestamp
     * @param <string> $string
     * @return <boolean>
     */
    public static function isTimestamp($string)
    {
        return checkdate(date('n',$string), date('j', $string), date('Y', $string));
    }

    /**
     * Check valid Us Phone
     * @param <string> $string
     * @return <boolean>
     */
    public static function isUsPhone($string)
    {
        return preg_match("/^(\(|){1}[2-9][0-9]{2}(\)|){1}([\.- ]|)[2-9][0-9]{2}([\.- ]|)[0-9]{4}$/", $string)?true:false;
    }

    /**
     * Check valid date
     * @param <int> $month
     * @param <int> $day
     * @param <int> $year
     * @return <boolean>
     */
    public static function isDate($month, $day, $year)
    {
        if(trim($month)>12 || trim($day)>31 || strlen(trim($year)) != 4)
        {
            return false;
        }
        return checkdate($month, $day, $year);
    }

    /**
     * Check valid max length of string
     * @param <string> $string
     * @param <int> $maxLength
     * @param <boolean> $ismultibyte
     * @param <string> $charset
     * @return <boolean>
     */
    public static function isValidLength($string, $maxLength=100, $ismultibyte = true, $charset='UTF-8')
    {
        //Get length of string
        $length = Core_String::lengthString($string, $ismultibyte, $charset);

        //Check length
        return ($length <= $maxLength)?true:false;
    }

    /**
     * Determine if supplied string is a valid GUID
     * @param <string> $string
     * @return <boolean>
     */
    public static function isValidGuid($string)
    {
        //Return true
        return true;

        //Check empty
        if(empty($string))
        {
            return false;
        }

        //Remove 3 random string
        $string = substr($string, 4, strlen($string));

        //Get random hashid
        $hash_id = substr($string, -5, strlen($string));

        //Check hashid
        if($hash_id != substr(Core_Utility::getSessionHashId(), 7, 5))
        {
            return false;
        }

        //get guid
        $guid = str_replace('-'.$hash_id, '', $string);

        //Check valid
        return preg_match('/^\{?[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}\}?$/', $guid);
    }

   /**
    * Check tid in javascript
    * @param <string> $tid
    * @param <int> $expired
    * @return <boolean>
    */
    public static function isValidTid($tid, $expired=10)
    {
        //Using script
        $crypt = Core_Crypt::getInstance(array('adapter'=>'xor'));

        //Decode tid
        $tid = $crypt->decode($tid);

        //Get now time
        $now = mktime();

        //Sub data
        $sub = $now - $tid;

        //Check sub
        if($sub > $expired)
        {
            return false;
        }
        return true;
    }
    
    /**
     * Check CLI console
     * @return string
     */
    public static function isCli()
    {
        return (PHP_SAPI === "cli");
    }
    
    /**
     * Check CLI console
     * @return string
     */
    public static function isFpmFcgi()
    {
        return (PHP_SAPI === "fpm-fcgi");
    }
}

