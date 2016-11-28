<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_String
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to string
 */
class Core_String
{
    /**
     * Dem so ky tu cua tu truyen vao, neu vuot qua so ky tu cho phep
     * thi tien hanh cat chuoi.
     * @param <string> $word
     * @param <int> $numChar
     * @return <string>
     */
    private static function _wrapWord($word, $numChar)
    {
        //Split string
        $aGiatri = str_split($word);
        preg_match_all('/./u', $word, $aGiatri);

        //Cut string using span and wbr
        $dem = 0;
        $strNew = "";
        $strSpan = "";
        $aGiatriLength = count($aGiatri);

        //Loop data
        for($i=0; $i<$aGiatriLength; $i++)
        {
            $aSubGiatriLength = count($aGiatri[$i]);
            
            for($j=0; $j< $aSubGiatriLength; $j++)
            {
                if($dem == $numChar)
                {
                    $strSpan .= sprintf("<span>%s</span><wbr/>", $strNew);
                    $strNew = "";
                    $dem = 0;
                }
                $strNew .= $aGiatri[$i][$j];
                $dem = $dem + 1;
            }

            if($dem <= $numChar && $strNew != "")
            {
                $strSpan .= $strNew;
                $strNew = "";
                $dem = 0;
            }
        }

        //Unset all temp variable
        unset($strNew, $word, $dem);

        //Return data
        return $strSpan;
    }

    /**
     * Get length of utf8 string
     * @param <string> $string
     * @param <string> $ismultibyte
     * @param <string> $charset
     * @return <string>
     */
    public static function lengthString($string, $ismultibyte = true, $charset='UTF-8')
    {
        //Check is multibyte string
        if(!$ismultibyte)
        {
            $charset = 'ISO-8859-1';
        }

        return iconv_strlen($string, $charset);
    }

    /**
    * cut UTF8 string
    * @param <string> $string
    * @param <int> $start
    * @param <int> $len
    * @param <string> $charlim    
    * @param <boolean> $ismultibyte
    * @param <string> $charset
    * @return <string>
    */
    public static function subString($string, $start=0 , $len=20, $charlim = '...', $ismultibyte = true, $charset='UTF-8')
    {
        //Strip tags html
        $string = strip_tags($string);

        //Check is multibyte string
        if(!$ismultibyte)
        {
            $charset = 'ISO-8859-1';
        }

        //Check length
        if(self::lengthString($string, $ismultibyte, $charset) <= $len)
        {
            return $string;
        }

        //Cut utf8 string
        $string = iconv_substr($string, $start, $len, $charset);
        return $string.$charlim;
    }

    /**
     * cut UTF8 string return full string
     * @param <string> $string
     * @param <int> $start
     * @param <int> $len
     * @param <string> $charlim
     * @return <string>
     */
    public static function subFullString($string, $start=0 , $len=20, $charlim = '...')
    {
        //Strip tags html
        $string = strip_tags($string);

        //Check length
        if(self::lengthString($string, true, 'UTF-8') <= $len)
        {
            return $string;
        }

        //Get list chars
        $arrList = explode(' ', $string);

        //If one element
        if(count($arrList) == 1)
        {            
            return self::subString($string, $start, $len);
        }
        
		//Loop to check data
        $idexLoop = 0;
        $lenNumber = 0;
        foreach($arrList as $spliceString)
        {
            //Add length
            $lenNumber += self::lengthString($spliceString, true, 'UTF-8');

			//Incement len
            $idexLoop++;

            //Check length
            if($lenNumber >= $len)
            {
                break;
            }
        }

        //Get list slice
        $arrList = array_slice($arrList, $start, $idexLoop);
        
        //Get string
        $newString = implode(' ', $arrList);
        
        //Check string
        if($newString == $string)
        {
            return $string;
        }
        
        //Return default
        return $newString.$charlim;
    }

    /**
     * Finds position of first occurrence of a needle
     * @param <string> $string
     * @param <string> $needle
     * @param <boolean> $ismultibyte
     * @param <string> $charset
     * @param <int> $offset
     * @return <string>
     */
    public static function strposString($string, $needle, $ismultibyte = true, $charset='UTF-8', $offset=0)
    {
        //Check is multibyte string
        if(!$ismultibyte)
        {
            $charset = 'ISO-8859-1';
        }

        //Finds position of first occurrence of a needle
        return iconv_strpos($string, $needle, $offset, $charset);
    }

    /**
     * Finds the last occurrence of a needle
     * @param <string> $string
     * @param <string> $needle
     * @param <boolean> $ismultibyte
     * @param <string> $charset
     * @param <int> $offset
     * @return <string>
     */
    public static function strrposString($string, $needle, $ismultibyte = true, $charset='UTF-8', $offset=0)
    {
        //Check is multibyte string
        if(!$ismultibyte)
        {
            $charset = 'ISO-8859-1';
        }

        //Finds the last occurrence of a needle
        return iconv_strrpos($string, $needle, $offset, $charset);
    }

    /**
     * Dem so ky tu cua moi tu trong chuoi, neu vuot qua so ky tu cho phep thi
     * tien hanh cat chuoi(chen tag <span> va <wbr>).
     * @param <string> $string
     * @param <int> $numChar
     * @param <boolean> $trimEnter
     * @return <string>
     */
    public static function wrapString($string, $numChar = 10, $trimEnter = true)
    {
        // Kiem tra cho phep xuong dong
        if($trimEnter == true)
        {
            $content = preg_replace("/\n\n+/", "\n", $string);
        }
        else
        {
            $content = preg_replace("/\n\n+/", "", $string);
        }

        // Kiem tra tren tung dong
        $arrContent = explode("\n", $content);
        foreach($arrContent as $rowKey => $rowValue)
        {
            // Kiem tra tren tung tu
            $arrPlText = explode(" ", $rowValue);
            foreach($arrPlText as $key => $value)
            {
                $value = self::_wrapWord($value, $numChar);
                $arrPlText[$key] = $value;
            }
            $stringFormat = implode(" ",$arrPlText);
            $arrContent[$rowKey] = $stringFormat;
        }

        $stringFormat = implode("<br/>", $arrContent);
        return $stringFormat;
    }

    /**
     * word wrap string
     * @param <string> $string
     * @param <string> $charsplit
     * @param <string> $charlim
     * @return <string>
     */
    public static function wordWrap($string, $charsplit = "\n", $charlim = '76')
    {
        // Se the character limit
        if(!is_numeric($charlim))
        {
            $charlim = 76;
        }

        // Reduce multiple spaces
        $string = preg_replace("| +|", " ", $string);

        // Standardize newlines
        $string = preg_replace("/\r\n|\r/", $charsplit, $string);

        // If the current word is surrounded by {unwrap} tags we'll
        // strip the entire chunk and replace it with a marker.
        $unwrap = array();
        if(preg_match_all("|(\{unwrap\}.+?\{/unwrap\})|s", $string, $matches))
        {
            for($i = 0; $i < count($matches['0']); $i++)
            {
                $unwrap[] = $matches['1'][$i];
                $string = str_replace($matches['1'][$i], "{{unwrapped".$i."}}", $string);
            }
        }

        // Use PHP's native function to do the initial wordwrap.
        // We set the cut flag to TRUE so that any individual words that are
        // too long get left alone.  In the next step we'll deal with them.
        $string = wordwrap($string, $charlim, $charsplit, TRUE);

        // Split the string into individual lines of text and cycle through them
        $output = "";
        foreach(explode($charsplit, $string) as $line)
        {
            // Is the line within the allowed character count?
            // If so we'll join it to the output and continue
            if(strlen($line) <= $charlim)
            {
                $output .= $line.$charsplit;
                continue;
            }

            $temp = '';
            while((strlen($line)) > $charlim)
            {
                // If the over-length word is a URL we won't wrap it
                if(preg_match("!\[url.+\]|://|wwww.!", $line))
                {
                    break;
                }

                // Trim the word down
                $temp .= substr($line, 0, $charlim-1);
                $line = substr($line, $charlim-1);
            }

            // If $temp contains data it means we had to split up an over-length
            // word into smaller chunks so we'll add it back to our current line
            if($temp != '')
            {
                $output .= $temp.$charsplit.$line;
            }
            else
            {
                $output .= $line;
            }
            $output .= $charsplit;
        }

        // Put our markers back
        if(count($unwrap) > 0)
        {
            foreach($unwrap as $key => $val)
            {
                $output = str_replace("{{unwrapped".$key."}}", $val, $output);
            }
        }

        // Remove the unwrap tags
        $output = str_replace(array('{unwrap}', '{/unwrap}'), '', $output);        
        return $output;
    }

    /**
     * word censor string
     * @param <string> $string
     * @param <string> $censored
     * @param <string> $replacement
     * @return <string>
     */
    public static function wordCensor($string, $censored, $replacement = '')
    {
        if(!is_array($censored))
        {
            return $string;
        }

        //Loop to censor
        $string = ' '.$string.' ';
        foreach($censored as $badword)
        {
            if($replacement != '')
            {
                $string = preg_replace("/\b(".str_replace('\*', '\w*?', preg_quote($badword)).")\b/i", $replacement, $string);
            }
            else
            {
                $string = preg_replace("/\b(".str_replace('\*', '\w*?', preg_quote($badword)).")\b/ie", "str_repeat('#', strlen('\\1'))", $string);
            }
        }
        
        return trim($string);
    }

    /**
     * Un-quotes a quoted string
     * @param <string> $jsonString
     * @return <string>
     */
    public static function stripSlashes($string)
    {
        return stripslashes($string);
    }

    /**
     * Quote string with slashes
     * @param <string> $string
     * @return <string>
     */
    public static function addSlashes($string)
    {
        return addslashes($string);
    }

    /**
     * Inserts HTML line breaks before all newlines in a string
     * @param <string> $string
     * @return <string>
     */
    public static function nl2br($string)
    {
        return nl2br($string);
    }

    /**
     * Return information about words used in a string
     * @param <string> $string
     * @param <int> $format
     * Specify the return value of this function. The current supported values are:
     * 0 - returns the number of words found
     * 1 - returns an array containing all the words found inside the string
     * 2 - returns an associative array, where the key is the numeric position of the word inside the string  and the value is the actual word itself
     * @return <object>
     */
    public static function stringWordCount($string, $format=0)
    {
        return str_word_count($string, $format);
    }

    /**
     * Get fast substring
     * @param <string> $string
     * @param <int> $start
     * @param <int> $length
     * @return <string>
     */
    public static function subFastString($string, $start=0 , $length=20)
    {
        //Strip tags html
        $string = strip_tags($string);

        //Get list number of string
        $arr = self::stringWordCount($string, 1);

        //If one element
        if(count($arr) == 1)
        {
            return $string;
        }

        //Get split array
        $add = '';
        if(count($arr) > $length)
        {
            $arr = array_slice($arr, $start, $length);
            $add = '...';
        }

        //Get string
        return implode(' ', $arr) . $add;
    }

    /**
     * Convert all applicable characters to HTML entities
     * @param <string> $string
     * @param <int> $quote_style
     * ENT_COMPAT : Will convert double-quotes and leave single-quotes alone.
     * ENT_QUOTES : Will convert both double and single quotes.
     * @param <string> $charset
     * @return <string>
     */
    public static function htmlEntities($string,  $quote_style=ENT_COMPAT, $charset='UTF-8')
    {
        return htmlentities($string,  $quote_style, $charset);
    }

    /**
     * Parse a CSV string into an array
     * @param <string> $string
     * @param <string> $delimiter
     * @param <string> $enclosure
     * @return <array>
     */
    public static function csvStringToArray($string, $delimiter=';', $enclosure='\\')
    {
        return str_getcsv($string, $delimiter, $enclosure);
    }

    /**
     * Pad a string to a certain length with another string
     * @param <string> $string
     * @param <int> $pad_length
     * @param <string> $pad_string
     * @param <int> $pad_type
     * Optional argument pad_type can be STR_PAD_RIGHT, STR_PAD_LEFT, or STR_PAD_BOTH.
     * If pad_type is not specified it is assumed to be STR_PAD_RIGHT
     * @return <string>
     */
    public static function stringPad($string, $pad_length=10, $pad_string='', $pad_type=STR_PAD_LEFT)
    {
        return str_pad($string, $pad_length, $pad_string, $pad_type);
    }

    /**
     * Repeat a string
     * @param <string> $string
     * @param <int> $multiplier
     * Number of time the input string should be repeated.
     * multiplier has to be greater than or equal to 0.
     * If the multiplier is set to 0, the function will return an empty string.
     * @return <string>
     */
    public static function stringRepeat($string, $multiplier=1)
    {
        return str_repeat($string, $multiplier);
    }

    /**
     * Replace all occurrences of the search string with the replacement string
     * @param <mixed> $search
     * @param <mixed> $replace
     * @param <mixed> $subject
     * The string or array being searched and replaced on, otherwise known as the haystack
     * If subject is an array, then the search and replace is performed with every entry of subject,
     * and the return value is an array as well.
     * @param <int> $count
     * @return <mixed>
     */
    public static function stringReplace( $search,  $replace, $subject, &$count)
    {
        return str_replace( $search,  $replace, $subject, $count);
    }

    /**
     * Randomly shuffles a string
     * @param <string> $string
     * @return <string>
     */
    public static function stringShuffle($string)
    {
        return str_shuffle($string);
    }

    /**
     * Find first occurrence of a string
     * @param <string> $string
     * @param <string> $needle
     * @param <boolean> $before_needle
     * @return <string>
     */
    public static function stringStr($string, $needle, $before_needle=false)
    {
        return strstr($string, $needle, $before_needle);
    }

    /**
     * Case-insensitive strstr()
     * @param <string> $string
     * @param <string> $needle
     * @param <boolean> $before_needle
     * @return <string>
     */
    public static function stringIstr($string, $needle, $before_needle=false)
    {
        return stristr($string, $needle, $before_needle);
    }

    /**
     * Convert special characters to HTML entities
     * @param <type> $string
     * @param <type> $quote_style
     * @param <type> $charset
     * @return <type>
     */
    public static function htmlSpecialchars($string, $quote_style=ENT_COMPAT, $charset='UTF-8')
    {
        return htmlspecialchars($string, $quote_style, $charset);
    }

    /**
     * cut UTF8 string without href return full string
     * @param <string> $string
     * @param <string> $urlImage
     * @param <int> $maxWordLenght
     * @return <string>
     */
    public static function subStringWithoutHref($string, $urlImage, $maxWordLenght = 10, $imgPath='/icons/')
    {
        //Loai bo tat ca tag
        $content = strip_tags($string);

        //Thay the hinh mat cuoi
        $content = Core_Filter::replaceEmoticons($content, $urlImage);

        //Cat chuoi
        $reg2 = "/<[^>]*>/Ui";
        preg_match_all($reg2, $content,$kq,PREG_OFFSET_CAPTURE);
        $strSplit = preg_split($reg2, $content, -1, PREG_SPLIT_OFFSET_CAPTURE);
        unset($reg2);
        $arrMerge = array_merge($kq[0], $strSplit);
        $i = 0;

        //Duyet mang da cat
        foreach($strSplit as $plText)
        {
            $arrPlText = explode(" ",$plText[0]);
            array_walk($arrPlText, array("Core_String","splitText"), $maxWordLenght );

            implode(" ",$arrPlText);
            $str1 = implode(" ",$arrPlText);
            $lenText = strlen($plText[0]);
            $vitri = $plText[1];

            $lenStrTag = strlen($kq[0][$i][0]);
            if($kq[0][$i][1] + $lenStrTag == $vitri)
            {
                $str1 = $kq[0][$i][0] . $str1;
                $i = $i + 1;

                if($kq[0][$i][1] == $vitri + $lenText)
                {
                    $str1 = $str1 .$kq[0][$i][0];
                    $i = $i + 1;
                }
            }
            
            $strNew .= $str1;
        }        
        $content = $strNew;

        //Clean all temp variable
        unset($strSplit, $plText, $str1, $kq, $strNew);

        return $content;
    }

    /**
     * Split text to list character
     * @param <string> $value
     * @param <string> $key
     * @param <int> $numText
     */
    public static function splitText(&$value, $key, $numText)
    {
        $giatri = $value;
        $aGiatri = str_split($giatri);
        preg_match_all('/./u', $giatri, $aGiatri);

        // Cat chuoi dung span va wbr
        $dem = 0;
        $strNew = "";
        $strSpan = "";
        $aGiatriLength = count($aGiatri);

        //No cat tung chu ra, va khoang trang vao
        for($i=0; $i<$aGiatriLength; $i++)
        {
            $aSubGiatriLength = count($aGiatri[$i]);
            for($j=0; $j< $aSubGiatriLength; $j++)
            {
                if($dem == $numText)
                {
                    $strSpan .= sprintf("<span>%s</span><wbr/>", $strNew);
                    $strNew = "";
                    $dem = 0;
                }
                $strNew .= $aGiatri[$i][$j];
                $dem = $dem + 1;
            }
            if($dem <= $numText && $strNew != "")
            {
                $strSpan .= sprintf("<span>%s</span><wbr/>", $strNew);
                $strNew = "";
                $dem = 0;
            }
        }
        $value = $strSpan;
        
        unset($strNew,$giatri, $dem);
    }
    
    /**
     * Convert string to array
     * @param <string> $string
     * @param <int> $iSplitLen
     * @return <array> 
     */
    public static function stringToArray($string, $iSplitLen=1)
    {
        //Set data
        $arrData = array();
        
        //Split data
        preg_match_all('`.`u', $string, $arrData);
        
        //Check data
        if(empty($arrData) || empty($arrData[0]))
        {
            return array();
        }
        
        //Chunk data
        $arrData = array_chunk($arrData[0], $iSplitLen);
        
        //Return data
        return array_map('implode', $arrData);        
    }
    
    /**
     * Convert to UTF8 data
     * @param <string> $sData
     * @return <string> 
     */
    public static function convertToUtf8($sData)
    {
        //Check data
        if(empty($sData))
        {
            return "";
        }
        
        //Set array encoding
        $arrEncoding = array("ascii","eucjp-win","sjis-win","UTF-8","shift-jis","JIS","EUC-JP","SJIS");
        
        //Get encoding data
        $sEncoding = mb_detect_encoding($sData, $arrEncoding, true);
        
        //If not detect
        if(!$sEncoding)
        {
            return $sData;
        }
        
        //Set default data
        $sEncodeData = $sData;
        
        //Check encoding
        if(strcasecmp($sEncoding, 'UTF-8') != 0)
        {
            //Convert data            
            $sEncodeData = mb_convert_encoding($sData, 'UTF-8', $sEncoding);
            
            //Check encoding
            if(!$sEncodeData)
            {
                $sEncodeData = $sData;
            }
        }
        
        //Return data
        return $sEncodeData;
    }
}

