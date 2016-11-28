<?php

/**
 * @author      :   Linuxpham
 * @name        :   Core_Utility
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to utility
 */
class Core_Utility
{
    /**
     * Hash
     * @var const
     */

    const VERSION = 1;
    const LONG_SESSION_TYPE = 32;
    const SHORT_SESSION_TYPE = 0;
    const AUTHORIZED_SESSION = 16;
    const UNAUTHORIZED_SESSION = 0;
    const MAGIC = 15;

    /**
     * Static list 
     */
    private static $arrMbID = array(
        'v.' => array(
            1, 3, 5, 7, 9
        ),
        'r.' => array(
            2, 4, 6, 8, 0
        )
    );

    /**
     * Get session hash ID
     * @return <string>
     */
    public static function getSessionHashId()
    {
        return md5((isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '') . self::getAltIp());
    }

    /**
     * Fetches an alternate IP address of the current visitor
     * attempting to detect proxies etc.
     * @return <string>
     */
    public static function getAltIp()
    {
        $alt_ip ='';
        
        if(isset($_SERVER['REMOTE_ADDR']))
        {
            $alt_ip = $_SERVER['REMOTE_ADDR'];
        } elseif(isset($_SERVER['HTTP_CLIENT_IP']))
        {
            $alt_ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $alt_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif(isset($_SERVER['HTTP_FROM']))
        {
            $alt_ip = $_SERVER['HTTP_FROM'];
        } elseif(isset($_SERVER['X-Real-IP']))
        {
            $alt_ip = $_SERVER['X-Real-IP'];
        } elseif(isset($_SERVER['X-Forwarded-For']))
        {
            $alt_ip = $_SERVER['X-Forwarded-For'];
        }

        return $alt_ip;
    }

    /**
     * random number generator
     * @param <int> $min
     * @param <int> $max
     * @param <int> $seed
     * @return <int>
     */
    public static function randomNumber($min, $max, $seed = -1)
    {
        if(!defined('RAND_SEEDED'))
        {
            if($seed == -1)
            {
                $seed = (double) microtime() * 1000000;
            }

            mt_srand($seed);

            define('RAND_SEEDED', true);
        }

        return mt_rand($min, $max);
    }

    /**
     * Random an word
     * @param <int> $number
     * @return <string>
     */
    public static function randomWord($number)
    {
        $string = 'abRSTUcdefghEFGHnrls456L78tiuxMyzIABCN1vwPQVjkmWXYZ12pq3DJK9';
        $string = Core_String::stringShuffle($string);
        return substr($string, self::randomNumber(0, (strlen($string) - $number)), $number);
    }

    /**
     * convert object to array
     * @param <object> $obj
     * @return <array>
     */
    public static function objectToArray($obj)
    {
        $arr = is_object($obj) ? get_object_vars($obj) : $obj;

        //Loop array
        foreach($arr as $key => $val)
        {
            $val = (is_array($val) || is_object($val)) ? self::objectToArray($val) : $val;
            $arr[$key] = $val;
        }

        return $arr;
    }

    /**
     * Check session key before send to server sso
     * @param <string> $key
     * @return <string>
     */
    public static function checkSessionKey($key)
    {
        $key = self::hexToBin($key);
        $result = unpack('CfirstByte/CsecondByte/Spadding/Lcrc/Ltime', $key);
        $temp = pack('CCSL', $result['firstByte'], $result['secondByte'], $result['padding'], $result['time']);
        $inputCrc = crc32($temp);

        if($result['crc'] < 0)
        {
            //for interger convert
            $result['crc'] += 4294967296;
        }

        if($inputCrc < 0)
        {
            //for interger convert
            $inputCrc += 4294967296;
        }

        //Return
        return $inputCrc == $result['crc'];
    }

    /**
     * Convert hex to bin string
     * @param <string> $str
     * @return <string>
     */
    public static function hexToBin($str)
    {
        $bin = "";
        $i = 0;
        $len = strlen($str);

        do
        {
            $bin .= chr(hexdec($str{$i} . $str{($i + 1)}));
            $i += 2;
        } while($i < $len);

        //Return
        return $bin;
    }

    /**
     * Generate session hask key
     * @param <boolean> $isAuthorized
     * @param <boolean> $isLong
     * @return <string>
     */
    public static function genSessionHashKey($isAuthorized = true, $isLong = true)
    {
        $firstByte = self::MAGIC;
        $secondByte = self::VERSION;
        $padding = self::randomNumber(0, 65535);
        $time = time();
        $temp = pack('CCSL', $firstByte, $secondByte, $padding, $time);
        $crc = crc32($temp);
        $sessionType = $isLong ? self::LONG_SESSION_TYPE : self::SHORT_SESSION_TYPE;
        $sessionAuthorized = $isAuthorized ? self::AUTHORIZED_SESSION : self::UNAUTHORIZED_SESSION;
        ;
        $result = pack('CCSLL', $firstByte, $secondByte, $padding, $crc, $time);
        $name = strtolower(bin2hex($result));
        return $name;
    }

    /**
     * Generate Globally Unique Identifier (GUID) : E.g. 2EF40F5A-ADE8-5AE3-2491-85CA5CBD6EA7
     * MSDN defines GUID as "a 128-bit integer (16 bytes) that can be used across all computers and networks
     * wherever a unique identifier is required. Such an identifier has a very low probability of being duplicated."
     * @return <string>
     */
    public static function genGuidKey()
    {
        return sprintf('%s-%04x%04x-%04x-%04x-%04x-%04x%04x%04x-%s',
                        // Rand 3 character
                        self::randomWord(3),
                        // 32 bits for "time_low"
                        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                        // 16 bits for "time_mid"
                        mt_rand(0, 0xffff),
                        // 16 bits for "time_hi_and_version",
                        // four most significant bits holds version number 4
                        mt_rand(0, 0x0fff) | 0x4000,
                        // 16 bits, 8 bits for "clk_seq_hi_res",
                        // 8 bits for "clk_seq_low",
                        // two most significant bits holds zero and one for variant DCE1.1
                        mt_rand(0, 0x3fff) | 0x8000,
                        // 48 bits for "node"
                        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                        // Add more session hash id
                        substr(self::getSessionHashId(), 7, 5)
        );
    }

    /**
     * Redirect url location
     * @param <string> $url
     */
    public static function redirect($url)
    {
        //Flush buffer
        if(ob_get_length())
        {
            ob_flush();
        }

        //Redirect
        header('Location:' . $url);
        exit();
    }

    /**
     * Redirect if check is mobile
     * @param <string> $url
     * @return <boolean>
     */
    public static function redirectIsMobile($url = 'http://m.mobion.com')
    {
        //Get cookie
        $mcookie = Core_Cookie::getCookie('_mobile');

        //If empty cookie
        if(empty($mcookie))
        {
            //Check mobile browser
            $mcookie = 0;
            if(Core_Valid::isMobileBrowser())
            {
                $mcookie = 1;
            }

            //Set cookie header
            header('P3P: CP="NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM"');
            Core_Cookie::setCookie('_mobile', $mcookie, 0);
        }

        //If is mobile
        if($mcookie)
        {
            self::redirect($url);
        }
        return false;
    }

    /**
     * Mb Encode string 
     * @param <string> $sMbId
     * @return <string> 
     */
    public static function mbIDEncode($sMbId)
    {
        //Get length data
        $iLength = strlen($sMbId);

        //Check data
        if($iLength < 3)
        {
            return $sMbId;
        }

        //Get first ID
        $sFirstID = substr($sMbId, 0, 2);

        //Get list keys
        $arrKey = array_keys(self::$arrMbID);

        //Check mbID
        if(!in_array($sFirstID, $arrKey))
        {
            return $sMbId;
        }

        //Get second data
        $sSecond = substr($sMbId, 2, ($iLength - 2));

        //Set realID
        $isRealID = true;

        //Check virtualID
        if($sFirstID == $arrKey[0])
        {
            $isRealID = false;
        }

        //Get random number
        $iRandomize = self::randomNumber(0, 4);

        //Check data
        if($isRealID)
        {
            $sSecond .= self::$arrMbID['r.'][$iRandomize];
        } else
        {
            $sSecond .= self::$arrMbID['v.'][$iRandomize];
        }

        //Return data
        return $sSecond;
    }

    /**
     * Mb Decode string 
     * @param <string> $sMbId
     * @return <string> 
     */
    public static function mbIDDecode($sMbId)
    {
        //Get length data
        $iLength = strlen($sMbId);

        //Check data
        if($iLength < 3)
        {
            return $sMbId;
        }

        //Get first ID
        $sFirstID = substr($sMbId, 0, -1);
        $sSecond = substr($sMbId, ($iLength - 1), 1);

        //Check mbID
        if($sSecond % 2 == 0)
        {
            $sFirstID = 'r.' . $sFirstID;
        } else
        {
            $sFirstID = 'v.' . $sFirstID;
        }

        //Return data
        return $sFirstID;
    }

    /**
     * Url encode string
     * @param <string> $url
     * @return <string>
     */
    public static function urlEncode($url)
    {
        $url = str_replace(array('+', '/', '=', 'http'), array('-', '_', '$', '#'), ($url));
        $url = strrev($url);
        $url = self::hexToBin($url);
        $url = str_replace(array('1', '2', '3', '4', '5', '6', '7', '8', '9', '0'), array("m", "n", "o", "p", "g", "h", "j", "k", "l", "z"), ($url));

        return ($url);
    }

    /**
     * Url decode string
     * @param <string> $url
     * @return <string>
     */
    public static function urlDecode($url)
    {
        $url = str_replace(array("m", "n", "o", "p", "g", "h", "j", "k", "l", "z"), array('1', '2', '3', '4', '5', '6', '7', '8', '9', '0'), ($url));
        $url = self::hexToBin($url);
        $url = strrev($url);
        $url = str_replace(array('-', '_', '$', '#'), array('+', '/', '=', 'http'), ($url));

        return $url;
    }

    /**
     * Get browser information
     * @return <array>
     */
    public static function getBrowser()
    {
        //Set default data
        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version = "";

        //First get the platform?
        if(preg_match('/linux/i', $u_agent))
        {
            $platform = 'linux';
        } elseif(preg_match('/macintosh|mac os x/i', $u_agent))
        {
            $platform = 'mac';
        } elseif(preg_match('/windows|win32/i', $u_agent))
        {
            $platform = 'windows';
        }

        //Next get the name of the useragent yes seperately and for good reason
        if(preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent))
        {
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        } elseif(preg_match('/Firefox/i', $u_agent))
        {
            $bname = 'Mozilla Firefox';
            $ub = "Firefox";
        } elseif(preg_match('/Chrome/i', $u_agent))
        {
            $bname = 'Google Chrome';
            $ub = "Chrome";
        } elseif(preg_match('/Safari/i', $u_agent))
        {
            $bname = 'Apple Safari';
            $ub = "Safari";
        } elseif(preg_match('/Opera/i', $u_agent))
        {
            $bname = 'Opera';
            $ub = "Opera";
        } elseif(preg_match('/Netscape/i', $u_agent))
        {
            $bname = 'Netscape';
            $ub = "Netscape";
        } else
        {
            $bname = 'Unknown';
            $ub = "Unknown";
        }

        //Finally get the correct version number
        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if(!preg_match_all($pattern, $u_agent, $matches))
        {
            
        }

        //See how many we have
        $i = count($matches['browser']);
        if($i != 1)
        {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if(strripos($u_agent, "Version") < strripos($u_agent, $ub))
            {
                $version = $matches['version'][0];
            } else
            {
                $version = $matches['version'][1];
            }
        } else
        {
            $version = $matches['version'][0];
        }

        //Check if we have a number
        if($version == null || $version == "")
        {
            $version = "?";
        }

        //Return data
        return array(
            'userAgent' => $u_agent,
            'name' => $bname,
            'version' => $version,
            'platform' => $platform,
            'pattern' => $pattern
        );
    }

    /**
     * Get Operating System of browser
     * @return <string>
     */
    public static function getBrowserOs()
    {
        //Get the user agent value - this should be cleaned to ensure no nefarious input gets executed
        $useragent = $_SERVER['HTTP_USER_AGENT'];

        //Get Operating System
        if(strstr($useragent, 'Win'))
        {
            $os = 'Win';
        } else if(strstr($useragent, 'Mac'))
        {
            $os = 'Mac';
        } else if(strstr($useragent, 'Linux'))
        {
            $os = 'Linux';
        } else if(strstr($useragent, 'Unix'))
        {
            $os = 'Unix';
        } else
        {
            $os = 'Other';
        }
        return $os;
    }

    /**
     * Dump of object
     * @param <object> $object
     * @return <string>
     */
    public static function getDump($object)
    {
        $string = '<pre>';
        $string .= print_r($object, true);
        $string .= '</pre>';
        return $string;
    }

    /**
     * Get xhp debug data
     * @param <array> $object
     * @return string
     */
    public static function getXhp($object, $sProfilerURL = "")
    {
        //Create start data
        $print = '<br/><br/><br/><table style="width:800px;" border="1" cellspacing="2" cellpadding="2"><tr><th colspan="7" bgcolor=\'#dddddd\'>Xhp debug Block</th></tr>';

        //Total tracking
        $printTotal = '<tr><th align="center" colspan="7">
                       Total Number Called Function : %s <br/>
                       Total Time Called Function : %s (microseconds)<br/>
                       </th></tr>';

        //Check profile URL
        if(!empty($sProfilerURL))
        {
            $printTotal = '<tr><th align="center" colspan="7"><a target="_blank" href="' . $sProfilerURL . '">Show Performance Graph</a></th></tr>';
        }

        //Loop keys
        $number = 0;
        $timeFunctionCall = 0;
        $printDetail = '';
        foreach($object as $key => $data)
        {
            $printDetail .= '<tr><td align="center">' . $number . '</td><td align="left">' . $key . '</td><td align="center">' . $data['ct'] . '</td><td align="center">' . $data['wt'] . '</td><td align="center">' . $data['cpu'] . '</td><td align="center">' . $data['mu'] . '</td><td align="center">' . $data['pmu'] . '</td></tr>';
            $timeFunctionCall += (int) $data['ct'];
            $number++;
        }

        //Add total
        $printTotal = vsprintf(
                $printTotal, array(
            $number,
            $timeFunctionCall
                )
        );

        //Add footer
        $print .= $printTotal;
        $print .= '<tr><td align="center" width="100">Number</td><td align="center">Php module</td><td align="center">number called from left Function</td><td align="center">inclusive time when called from left Function (microseconds)</td><td align="center">Cpu time when called from left Function (microseconds)</td><td align="center">Change in PHP memory usage (bytes)</td><td align="center">Change in PHP peak memory usage (bytes)</td></tr>';
        $print .= $printDetail;
        $print .= "</table>";

        //Return data
        return $print;
    }

    /**
     * Build dropdownlist
     * @param <array> $arrValue
     * @param <string> $dropdownlistName
     * @param <string> $cssClass
     * @param <string> $selectedValue
     * @param <string> $onChange
     * @param <string> $disable
     * @return <string>
     */
    public static function setDropdownList($arrValue, $dropdownlistName, $selectedValue = null, $onChange = null, $cssClass = null, $disable = false)
    {
        $sHTML = "<select name=\"$dropdownlistName\" id=\"$dropdownlistName\" class=\"$cssClass\"";
        $sHTML .= " onchange=\"$onChange\" ";
        if($disable == true)
        {
            $sHTML .= " disabled ";
        }

        $sHTML .= " >";
        if(is_array($arrValue))
        {
            foreach($arrValue as $key => $item)
            {
                if(!is_array($item))
                {
                    $item = array();
                    $item['name'] = $arrValue[$key];
                    $item['id'] = $key;
                }
                $id = $item['id'];
                $name = $item['name'];

                if($item['id'] == $selectedValue)
                {
                    $sHTML .= "<option selected value=\"$id\"> $name </option> ";
                } else
                {
                    $sHTML .= "<option value=\"$id\"> $name </option> ";
                }
            }
        }
        $sHTML .= "</select>";
        return $sHTML;
    }

    /**
     * Generate URL-encoded query string
     * @param <string> $data
     * @param <string> $numeric_prefix
     * @param <string> $arg_separator
     * @return <string>
     */
    public static function httpBuildQuery($data, $numeric_prefix = '', $arg_separator = '&')
    {
        return http_build_query($data, $numeric_prefix, $arg_separator);
    }

    /**
     * Parses the string into variables
     * @param <string> $string
     * @param <array> $output
     * If the second parameter $output is present,
     * variables are stored in this variable as array elements instead.
     */
    public static function parseString($string, &$output)
    {
        parse_str($string, $output);
    }

    /**
     * Parse a URL and return its components
     * @param <string> $url
     * @return <array>
     *  On seriously malformed URLs, parse_url()
     * may return FALSE and emit a E_WARNING.
     * Otherwise an associative array is returned, whose components may be (at least one):
     * scheme - e.g. http
     * host
     * port
     * user
     * pass
     * path
     * query - after the question mark ?
     * fragment - after the hashmark #
     */
    public static function parseUrl($url)
    {
        return parse_url($url);
    }

    /**
     * Build an URL
     * @param <string> $url
     * @param <array> $parts
     * @param <int> $flags
     * @return <string>
     */
    public static function httpBuildUrl($url, $parts, $flags = null)
    {
        if(is_null($flags))
        {
            $flags = HTTP_URL_STRIP_AUTH | HTTP_URL_JOIN_PATH | HTTP_URL_JOIN_QUERY | HTTP_URL_STRIP_FRAGMENT;
        }
        return http_build_url($url, $parts, $flags);
    }

    /**
     * Returns filename component of path
     * @param <string> $path
     * @param <string> $suffix
     * @return <string>
     */
    public static function baseName($path, $suffix = null)
    {
        return basename($path, $suffix);
    }

    /**
     * Returns directory name component of path
     * @param <string> $path
     * @return <string>
     */
    public static function dirName($path)
    {
        return dirname($path);
    }

    /**
     * Generate short link
     * @param <string> $in
     * @param <boolean> $to_num
     * @param <boolean> $pad_up
     * @param <string> $passKey
     * @return <string>
     */
    public static function genShortLink($in, $to_num = false, $pad_up = false, $passKey = '#123@sttm@xxx891#2011')
    {
        $index = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $indexLen = strlen($index);
        if($passKey !== null)
        {
            //Loop index
            for($n = 0; $n < $indexLen; $n++)
            {
                $i[] = substr($index, $n, 1);
            }

            //Generate 256 passkey
            $passhash = hash('sha256', $passKey);
            $passhash = (strlen($passhash) < strlen($index)) ? hash('sha512', $passKey) : $passhash;

            //Loop hash key
            for($n = 0; $n < $indexLen; $n++)
            {
                $p[] = substr($passhash, $n, 1);
            }

            //Sort passkey
            array_multisort($p, SORT_DESC, $i);
            $index = implode($i);
        }

        //Check decode and encode
        if($to_num)
        {
            $in = substr($in, 3, strlen($in));
            $in = strrev($in);
            $out = 0;
            $len = strlen($in) - 1;
            for($t = 0; $t <= $len; $t++)
            {
                $bcpow = bcpow($indexLen, $len - $t);
                $out = $out + strpos($index, substr($in, $t, 1)) * $bcpow;
            }

            if(is_numeric($pad_up))
            {
                $pad_up--;
                if($pad_up > 0)
                {
                    $out -= pow($indexLen, $pad_up);
                }
            }
            $out = sprintf('%F', $out);
            $out = substr($out, 0, strpos($out, '.'));
        } else
        {
            if(is_numeric($pad_up))
            {
                $pad_up--;
                if($pad_up > 0)
                {
                    $in += pow($indexLen, $pad_up);
                }
            }

            $out = "";
            for($t = floor(log($in, $indexLen)); $t >= 0; $t--)
            {
                $bcp = bcpow($indexLen, $t);
                $a = floor($in / $bcp) % $indexLen;
                $out = $out . substr($index, $a, 1);
                $in = $in - ($a * $bcp);
            }
            $out = strrev($out);
            $out = self::randomWord(3) . $out;
        }

        //Return data
        return $out;
    }

    /**
     * Get content Link
     * @param <string> $name
     * @param <string> $data
     * @param <string> $host
     * @param <string> $path
     * @param <string> $param1
     * @param <string> $param2 
     */
    public static function getContentLink(&$arrLink, $name, $data, $host = '', $path = '', $param1 = '', $param2 = '')
    {
        //Check data
        if(empty($data))
        {
            return false;
        }

        //Check type
        if($name == 'image')
        {
            //Check data
            $data = str_replace('\/', '/', $data);
            $data = str_replace('\\', '/', $data);
            $key = basename($data);

            //Get images
            if(@preg_match('@^(http://|https://)@i', $data))
            {
                $arrLink[$name][$key] = $data;
            } elseif(@preg_match('@^(//)@i', $data))
            {
                $arrLink[$name][$key] = (strpos($host, 'https://') !== false) ? 'https:' . $data : 'http:' . $data;
            } elseif(@preg_match('@^(/)@i', $data))
            {
                $arrLink[$name][$key] = $host . $data;
            } else
            {
                $arrLink[$name][$key] = $host . $path . '/' . $data;
            }

            //Check double slash
            if(isset($arrLink[$name][$key]) && !empty($arrLink[$name][$key]) && substr_count($arrLink[$name][$key], '//') >= 2)
            {
                $posLDSlash = strpos($arrLink[$name][$key], '//');
                $strImgL = substr($arrLink[$name][$key], 0, $posLDSlash + 2);
                $strImgR = substr($arrLink[$name][$key], $posLDSlash + 2);
                $strImgR = preg_replace('/(\/\/)/', '/', $strImgR);
                $arrLink[$name][$key] = $strImgL . $strImgR;
            }
        } elseif($name == 'link')
        {
            //Get params
            $param1 = str_replace('\\', '', $param1);
            $param2 = str_replace('\\', '', $param2);

            //Check images
            if(strpos($param1, 'image_src') !== false || strpos($param2, 'image_src'))
            {
                $arrLink[$name]['image_src'] = $data;
            } elseif(strpos($param1, 'video_src') !== false || strpos($param2, 'video_src'))
            {
                $arrLink[$name]['video_src'] = $data;
            }
        } elseif($name == 'meta')
        {
            switch($data)
            {
                case 'video_width':
                    $arrLink[$name]['video_width'] = $param1;
                    break;
                case 'video_height':
                    $arrLink[$name]['video_height'] = $param1;
                    break;
                case 'video_type':
                    $arrLink[$name]['video_type'] = $param1;
                    break;
                case 'og:title':/*                     * youtube - https* */
                    $arrLink[$name]['video_title'] = $param1;
                    break;
                case 'og:description':/*                     * youtube - https* */
                    $arrLink[$name]['video_desc'] = $param1;
                    break;
                case 'og:video':/*                     * youtube - https* */
                    $arrLink[$name]['video_src'] = $param1;
                    break;
                case 'og:video:type':/*                     * youtube - https* */
                    $arrLink[$name]['video_type'] = $param1;
                    break;
                case 'og:video:width':/*                     * youtube - https* */
                    $arrLink[$name]['video_width'] = $param1;
                    break;
                case 'og:video:height':/*                     * youtube - https* */
                    $arrLink[$name]['video_height'] = $param1;
                    break;
                case 'og:image':/*                     * youtube - https* */
                    $arrLink[$name]['video_img'] = $param1;
                    break;
            }
        } elseif($name != 'description' && (!isset($arrLink[$name]) || empty($arrLink[$name])))
        {
            $arrLink[$name] = $data;
            $arrLink[$name] = str_replace('\"', '"', $arrLink[$name]);
        } elseif($name == 'description')
        {
            if($path == 'content')
            {
                $data = str_replace('\"', '"', $data);
                @preg_match('#content=(\'|")(.*)(\\1)#i', $data, $m);
                $data = @html_entity_decode($m[2], ENT_QUOTES, 'UTF-8');
                $arrLink[$name] = $data;
            } elseif(strlen($data) > 150)
            {
                $arrLink[$name] .= ' ' . trim(strip_tags($data));
                $arrLink[$name] = str_replace('\"', '"', $arrLink[$name]);
            }
        }
    }

    /**
     * Get binary header
     * @param <string> $sURL
     * @param <string> $sHost
     * @return <string> 
     */
    public static function getArrBinaryHeaderLength($arrURL, $sProtocol, $sDomain, $userAgent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1', $iMinSize = 10240)
    {
        //Set array response
        $arrResponse = array();
        $iMaxLoop = 5;

        //Init multi curl instance
        $multiCurlInstance = curl_multi_init();

        //Set list connection
        $arrConnection = array();

        //Loop to push data
        foreach($arrURL as $sURL)
        {
            //Trim images
            $sURL = trim($sURL);

            //Check data
            if(@preg_match("/^http/i", $sURL))
            {
                //Nothing
            } else if(@preg_match("/^\//i", $sURL))
            {
                $sURL = $sProtocol . '://' . $sDomain . $sURL;
            } else
            {
                continue;
            }

            //Parse url
            $arrDetail = parse_url($sURL);

            //Check Host information
            if(!isset($arrDetail['host']))
            {
                continue;
            }

            //Init curl
            $itemCurlInstance = curl_init();

            //Push list curl
            $arrConnection[(string) $itemCurlInstance] = $sURL;

            //Set curl options 
            curl_setopt($itemCurlInstance, CURLOPT_HTTPHEADER, array("Expect:"));
            curl_setopt($itemCurlInstance, CURLOPT_BUFFERSIZE, 4096);
            curl_setopt($itemCurlInstance, CURLOPT_URL, $sURL);
            curl_setopt($itemCurlInstance, CURLOPT_REFERER, $arrDetail['host']);
            curl_setopt($itemCurlInstance, CURLOPT_USERAGENT, $userAgent);
            curl_setopt($itemCurlInstance, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($itemCurlInstance, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($itemCurlInstance, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($itemCurlInstance, CURLOPT_TIMEOUT, 5);
            curl_setopt($itemCurlInstance, CURLOPT_HEADER, 1);
            curl_setopt($itemCurlInstance, CURLOPT_CUSTOMREQUEST, 'HEAD');
            curl_setopt($itemCurlInstance, CURLOPT_NOBODY, true);

            //Multi add handler
            curl_multi_add_handle($multiCurlInstance, $itemCurlInstance);
        }

        //Set running status
        $isRunning = 0;
        $iLoop = 0;

        //Execute the handles 
        do
        {
            //Exec multi URL
            $iCode = curl_multi_exec($multiCurlInstance, $isRunning);

            //Check code
            if($iCode == CURLM_OK)
            {
                //Loop to read data
                while($arrDone = curl_multi_info_read($multiCurlInstance))
                {
                    //Set current curl instance
                    $itemCurlInstance = $arrDone['handle'];

                    //Check loop data
                    if($iLoop > $iMaxLoop)
                    {
                        //Remove handler
                        curl_multi_remove_handle($multiCurlInstance, $itemCurlInstance);

                        //Continue to close socket
                        continue;
                    }

                    //Get information of curl request
                    $iImgSize = curl_getinfo($itemCurlInstance, CURLINFO_CONTENT_LENGTH_DOWNLOAD);

                    //Get key data
                    $sKey = $arrConnection[(string) $itemCurlInstance];

                    //Check data information
                    if($iImgSize >= $iMinSize)
                    {
                        //Get response data
                        $arrResponse[$sKey] = $iImgSize;

                        //Remove handler
                        curl_multi_remove_handle($multiCurlInstance, $itemCurlInstance);

                        //Increase data loop
                        $iLoop++;
                    }
                }
            }
        } while($isRunning);

        //Close handle
        curl_multi_close($multiCurlInstance);

        //Return data
        return $arrResponse;
    }

    /**
     * Get binary header
     * @param <string> $sURL
     * @param <string> $sHost
     * @return <string> 
     */
    public static function getBinaryHeaderLength($sURL, $sHost, $userAgent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1')
    {
        //Init curl
        $curlInstance = curl_init();

        //Set curl options 
        curl_setopt($curlInstance, CURLOPT_HTTPHEADER, array("Expect:"));
        curl_setopt($curlInstance, CURLOPT_BUFFERSIZE, 4096);
        curl_setopt($curlInstance, CURLOPT_URL, $sURL);
        curl_setopt($curlInstance, CURLOPT_REFERER, $sHost);
        curl_setopt($curlInstance, CURLOPT_USERAGENT, $userAgent);
        curl_setopt($curlInstance, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlInstance, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curlInstance, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($curlInstance, CURLOPT_TIMEOUT, 5);
        curl_setopt($curlInstance, CURLOPT_HEADER, 1);
        curl_setopt($curlInstance, CURLOPT_CUSTOMREQUEST, 'HEAD');
        curl_setopt($curlInstance, CURLOPT_NOBODY, true);

        //Get content
        curl_exec($curlInstance);

        //Check error
        if(curl_error($curlInstance))
        {
            //Close curl
            curl_close($curlInstance);

            //Return data
            return false;
        }

        //Get data
        $sResponse = curl_getinfo($curlInstance, CURLINFO_CONTENT_LENGTH_DOWNLOAD);

        //Close curl
        curl_close($curlInstance);

        //Return data
        return $sResponse;
    }

    /**
     * Get binary content
     * @param <string> $sURL
     * @param <string> $sHost
     * @return <string> 
     */
    public static function getBinaryContent($sURL, $sHost, $userAgent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1')
    {
        //Init curl
        $curlInstance = curl_init();

        //Set curl options
        curl_setopt($curlInstance, CURLOPT_HTTPHEADER, array("Expect:"));
        curl_setopt($curlInstance, CURLOPT_BUFFERSIZE, 4096);
        curl_setopt($curlInstance, CURLOPT_URL, $sURL);
        curl_setopt($curlInstance, CURLOPT_REFERER, $sHost);
        curl_setopt($curlInstance, CURLOPT_USERAGENT, $userAgent);
        curl_setopt($curlInstance, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlInstance, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curlInstance, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($curlInstance, CURLOPT_TIMEOUT, 5);
        curl_setopt($curlInstance, CURLOPT_HEADER, 0);
        curl_setopt($curlInstance, CURLOPT_ENCODING, 1);

        //Get content
        $sResponse = curl_exec($curlInstance);

        //Check error
        if(curl_error($curlInstance))
        {
            //Close curl
            curl_close($curlInstance);

            //Return data
            return false;
        }

        //Close curl
        curl_close($curlInstance);

        //Check response data
        if(!empty($sResponse))
        {
            //Convert to UTF8 Data
            $sResponse = Core_String::convertToUtf8($sResponse);

            //Remove all Javascript content
            $sResponse = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $sResponse);
        }

        //Return data
        return $sResponse;
    }

    /**
     * Get external URL content
     * @param <string> $sURL
     * @return <array> 
     */
    public static function getExternURLContent($sURL, $sUserAgent = "", $iImgSize = 10240)
    {
        //Check user agent
        if(empty($sUserAgent))
        {
            $sUserAgent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2";
        }

        //Set data callback
        $arrData = array(
            'error' => 0,
            'response' => array()
        );

        //Check url
        if(empty($sURL))
        {
            return $arrData;
        }

        //Replace https to http
        $sURL = str_replace("https://", "http://", $sURL);

        //Check mobile site
        if((strpos($sURL, "http://m.") !== false) || (strpos($sURL, "http://www.m.") !== false))
        {
            $sUserAgent = "Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B334b Safari/531.21.10 FirePHP/0.7.1";
        }

        //Parse url
        $arrUrl = parse_url($sURL);

        //Set data        
        $pageTitle = $arrUrl['host'];
        $metaDesc = '';
        $domain = $arrUrl['host'];
        $siteImages = array();
        $protocol = $arrUrl['scheme'];

        //Get host URL
        $sHost = $arrUrl['scheme'] . '://' . $arrUrl['host'];
        $sHost = str_replace('://www.', '://', $sHost);
        $iPos = strpos($sURL, '#!?');

        /* Check Position */
        if($iPos !== false)
        {
            $sURL = substr($sURL, 0, $iPos);
        }

        //Check images URL
        if(Core_Valid::isImageUrl($sURL))
        {
            //Set data
            $arrData['response'] = array(
                "title" => $sURL,
                "description" => $sHost,
                "protocol" => $protocol,
                "images_list" => array($sURL)
            );

            //Return data
            return $arrData;
        }

        //Try to get data
        try
        {
            /* Check Host URL */
            if(($sHost == 'https://www.youtube.com') || ($sHost == 'https://youtube.com'))
            {
                //Init HTTP client by Zend framework
                $optionsConfig = array(
                    'maxredirects' => 2,
                    'adapter' => 'Zend_Http_Client_Adapter_Curl',
                    'curloptions' => array(
                        CURLOPT_HEADER => false,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_MAXREDIRS => 5,
                        CURLOPT_USERAGENT => $sUserAgent,
                        CURLOPT_REFERER => $sHost,
                        CURLOPT_FOLLOWLOCATION => true
                    )
                );

                //Set control flag
                $isGetRequest = true;

                //Try to get data
                try
                {
                    //Decode link
                    $linkDcode = preg_replace('/(&amp;)/i', '&', $sURL);

                    //Get instance client
                    $clientInstance = new Zend_Http_Client($linkDcode, $optionsConfig);
                } catch(Zend_Exception $ex)
                {
                    //Get data
                    try
                    {
                        $clientInstance = new Zend_Http_Client($sHost, $optionsConfig);
                    } catch(Zend_Exception $ex)
                    {
                        $isGetRequest = false;
                    }
                }

                //Check data
                if($isGetRequest == false)
                {
                    //Set error code
                    $arrData['error'] = 1;

                    //Return data
                    return $arrData;
                }

                //Set request control
                $requestSuccess = true;

                //Get data
                try
                {
                    $sResponse = $clientInstance->request();
                } catch(Zend_Exception $ex)
                {
                    $requestSuccess = false;
                }

                //Check request
                if($requestSuccess == false)
                {
                    //Set error code
                    $arrData['error'] = 1;

                    //Return data
                    return $arrData;
                }

                //Check status response
                if($sResponse->isSuccessful() == false)
                {
                    //Set error code
                    $arrData['error'] = 1;

                    //Return data
                    return $arrData;
                }

                //Get data
                $sUri = $clientInstance->getUri();
                $sResponse = $clientInstance->getLastResponse();
                $sResponse = $sResponse->getBody();

                //Check response data
                if(!empty($sResponse))
                {
                    //Convert to UTF8 Data
                    $sResponse = Core_String::convertToUtf8($sResponse);

                    //Remove all Javascript content
                    $sResponse = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $sResponse);
                }

                //Set data
                $arrLink = array();

                //Get metadata
                @preg_replace(array('#<meta property=(\'|")(.*)(\\1) content=(\'|")(.*)(\\4)>#esiU'), array("Core_Utility::getContentLink(" . $arrLink . ",'meta','\\2','','','\\5', '')"), $sResponse);

                //Check data
                if(isset($arrLink['meta']) && !empty($arrLink['meta']) && isset($arrLink['meta']['video_src']) && !empty($arrLink['meta']['video_src']))
                {
                    //Set data
                    $arrData['response'] = array(
                        "title" => $arrLink['meta']['video_title'],
                        "description" => $arrLink['meta']['video_desc'],
                        "protocol" => $protocol,
                        "images_list" => array($arrLink['meta']['video_img'])
                    );

                    //Return data
                    return $arrData;
                }

                //Set data
                $arrLink = array();

                //Set path link
                $sPath = strpos($sUri->getPath(), '.') ? dirname($sUri->getPath()) : $sUri->getPath();

                //Get link data            
                @preg_replace(array('#<meta[^>]+name=(\'|")(description)(\\1).*?>#ei',
                            '#<title>(.*)</title>#esiU',
                            '#<img[^>]+src=(\'|")(.*)(\\1)(.*)>#esiU'), array("Core_Utility::getContentLink(" . $arrLink . ",'description','\\0','','content')",
                            "Core_Utility::getContentLink(" . $arrLink . ",'title','\\1')",
                            "Core_Utility::getContentLink(" . $arrLink . ",'image','\\2','" . $sHost . "','" . $sPath . "')"), $sResponse);

                //Check image data
                if(isset($arrLink['image']) && !empty($arrLink['image']) && is_array($arrLink['image']))
                {
                    $countImg = count($arrLink['image']);
                    $arrLink['images_list'] = array_slice($arrLink['image'], 0, $countImg > 10 ? 10 : $countImg);
                }

                //Check description data
                if(!isset($arrLink['description']) || empty($arrLink['description']))
                {
                    //Get content
                    $sResponse = @preg_replace('/\s\s+/ui', '', $sResponse);
                    @preg_match('#<body>(.*?)</body>#', $sResponse, $aContent);

                    //Set content
                    $sResponse = isset($aContent[1]) ? $aContent[1] : $sResponse;

                    //Set pattern
                    $patterns[] = '#<script[^>]*>(.*)</script>#esiU';
                    $patterns[] = '#<a[^>]*>(.*)</a>#esiU';
                    $patterns[] = '#<ul[^>]*>(.*)</ul>#esiU';
                    $patterns[] = '#<nobr[^>]*>(.*)</nobr>#esiU';
                    $patterns[] = '#<select[^>]*>(.*)</select>#esiU';
                    $patterns[] = '#<table[^>]*none[^>]*>(.*)</table>#esiU';
                    $patterns[] = '#<div[^>]*javascript[^>]*>(.*)</div>#esiU';
                    $patterns[] = '#<div[^>]*none[^>]*>(.*)</div>#esiU';
                    $patterns[] = '#<p[^>]*none[^>]*>(.*)</p>#esiU';
                    $patterns[] = '#<span[^>]*none[^>]*>(.*)</span>#esiU';
                    $sResponse = @preg_replace($patterns, '', $sResponse);
                    $sResponse = strip_tags($content, '<div><p><span><h6><h5><h4><h3>');
                    $sResponse = @preg_replace("/<[\w]+[^>]*>[^<>]{0,100}<\[\w]+>/", '', $sResponse);

                    //Get content
                    @preg_replace('#<[^>]+>.*</[^>]+>#eisU', "Core_Utility::getContentLink(" . $arrLink . ",'description','\\0')", $sResponse);
                }

                //Set data
                $arrData['response'] = $arrLink;

                //Return data
                return $arrData;
            } elseif($sHost == 'http://youtube.com')
            {
                /* List site support */
                $arrSiteSuport = array(
                    'http://youtube.com' => 'http://youtube.com/watch[?]v=([a-zA-Z0-9_-]+)[&]?(.*)?'
                );

                //Parse data
                @preg_match('#' . $arrSiteSuport[$sHost] . '#i', str_replace('://www.', '://', $sURL), $arrLoop);

                //Check binary link
                if(!isset($arrLoop[1]))
                {
                    //Set error code
                    $arrData['error'] = 1;

                    //Return data
                    return $arrData;
                }

                //Get binary URL
                $binaryUrl = 'http://www.youtube.com/oembed?url=http%3A//www.youtube.com/watch?v%3D' . $arrLoop[1] . '&format=json';

                //Get binary content
                $sResponse = self::getBinaryContent($binaryUrl, $sHost, $sUserAgent);

                //Check data
                if(empty($sResponse))
                {
                    //Set error code
                    $arrData['error'] = 1;

                    //Return data
                    return $arrData;
                }

                //Decode data
                $arrResponse = Zend_Json::decode($sResponse);

                //Set data
                $arrLink = array();

                //Check data
                if(empty($arrResponse))
                {
                    //Set error code
                    $arrData['error'] = 1;

                    //Return data
                    return $arrData;
                }

                //Set data
                $arrLink['title'] = $arrResponse['title'];
                $arrLink['description'] = '';
                $arrLink['images_list'] = array('http://i.ytimg.com/vi/' . $arrLoop[1] . '/default.jpg');
                $arrLink['vid'] = $arrLoop[1];

                //Set data
                $arrData['response'] = $arrLink;

                //Return data
                return $arrData;
            }
        } catch(Zend_Exception $ex)
        {
            //Nothing
        }

        //Try to get data
        try
        {
            //Get binary content
            $sResponse = self::getBinaryContent($sURL, $sHost, $sUserAgent);

            //Check data
            if(empty($sResponse))
            {
                //Set error code
                $arrData['error'] = 1;

                //Return data
                return $arrData;
            }

            //Check title
            $patternTitle = "|<[\s]*title[\s]*>([^<]+)<[\s]*/[\s]*title[\s]*>|Ui";

            //Get title
            if(@preg_match($patternTitle, $sResponse, $arrMatches))
            {
                //Check data
                if($pageTitle != 'ERROR: The requested URL could not be retrieved')
                {
                    $pageTitle = $arrMatches[1];
                }
            }

            //Trim raw data
            $pageTitle = trim(strip_tags(rawurldecode($pageTitle)));

            //Check description
            if(@preg_match("/meta\s+name\s*=\s*[\"]?\s*description\s*[\"]?\s+content\s*=\s*[\"]?([^\">]+)/is", $sResponse, $arrMatches))
            {
                $metaDesc = $arrMatches[1];
                $metaDesc = trim(strip_tags(rawurldecode($metaDesc)));
            }

            //Set site iamges
            $siteImages = array();

            //Check images
            if(@preg_match_all("/img.*?src\s*=\s*[\"']?([^\"'>]+)/i", $sResponse, $arrMatches))
            {
                $siteImages = self::getArrBinaryHeaderLength($arrMatches[1], $protocol, $domain, $iImgSize);
            }

            //Set response data
            $arrData['response'] = array(
                "title" => $pageTitle,
                "description" => $metaDesc,
                "protocol" => $protocol,
                "images_list" => (sizeof($siteImages) > 0) ? array_keys($siteImages) : array()
            );
        } catch(Zend_Exception $ex)
        {
            //Set error code
            $arrData['error'] = 1;

            //Return data
            return $arrData;
        }

        //Return data
        return $arrData;
    }

    /**
     * Get encrypt token
     * @param <string> $sToken
     * @return <array> 
     */
    public static function getEncryptToken($sToken)
    {
        return $sToken;
        //Check token
        if(empty($sToken))
        {
            return $sToken;
        }

        //Set map data
        $arrMapping = array('1' => '9', '2' => '8', '3' => '7', '4' => '6', 'q' => 'm', 'w' => 'n', 'e' => 'b', 'r' => 'v', 't' => 'c', 'y' => 'x', 'u' => 'z', 'i' => 'l', 'o' => 'k', 'p' => 'j', 'a' => 'h', 's' => 'g', 'd' => 'f');

        //Return data
        return str_replace(array_keys($arrMapping), array_values($arrMapping), $sToken);
    }

    /**
     * Send not modified header
     * @param <string> $url
     */
    public static function sendNotModifiedForBrowser($iAge, $sEtag)
    {
        //Flush buffer
        if(ob_get_length())
        {
            ob_flush();
        }

        //Redirect
        header_remove("Cache-Control");
        header_remove("Pragma");
        header_remove("Expires");
        header('Not Modified', true, 304);
        header("Age: $iAge");
        header("Etag: $sEtag");
        exit();
    }

    /**
     * Set not modified header
     * @param <string> $url
     */
    public static function setNotModifiedHeader($iAge, $sEtag)
    {
        //Flush buffer
        if(ob_get_length())
        {
            ob_flush();
        }

        //Send 304 not modified header
        header_remove("Cache-Control");
        header_remove("Pragma");
        header_remove("Expires");
        header("Age: $iAge");
        header("Etag: $sEtag");
    }

    /**
     * Get etag header
     * @param <string> $url
     */
    public static function getEtagHeader()
    {
        return isset($_SERVER['HTTP_IF_NONE_MATCH']) ? $_SERVER['HTTP_IF_NONE_MATCH'] : null;
    }

    /**
     * Set define array
     * @param <string> $keyGlobalName
     * @param <array> $arrData
     * @param <bool> $isCaseSensitive 
     */
    public static function setDefineArray($keyGlobalName, $arrData = array(), $isCaseSensitive = true, $env = 'development')
    {
        if(function_exists('apc_load_constants') && ($env == 'production'))
        {
            if(apc_load_constants($keyGlobalName, $isCaseSensitive) == false)
            {
                apc_define_constants($keyGlobalName, $arrData, $isCaseSensitive);
                apc_load_constants($keyGlobalName);
            }
        } else
        {
            foreach($arrData as $keyName => $keyValue)
            {
                define($keyName, $keyValue, $isCaseSensitive);
            }
        }
    }

}

