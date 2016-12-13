<?php
/**
 * Created by PhpStorm.
 * User: Hoang-Thanh
 * Date: 7/5/15
 * Time: 11:53 AM
 */

class Validate {
    public static function checkEmpty($arrField, $errorMessage = 'this field is required',$excepts = array())
    {
        if(!is_array($arrField))
            return array('');

        $arrError   = array();
        foreach($arrField as $key=>$value)
        {
            if(is_string($value))
            {
                $value = trim($value);
                if( empty($value) && !in_array($key,$excepts)){
                    $arrError []= array('field'=>$key, 'message' => $errorMessage);
                }
            }

        }
        return array_filter($arrError);
    }


    public static function checkNegativeNumber($arrField, $errorMessage = 'this field is numeric only and >=0')
    {
        if(!is_array($arrField))
            return array('');

        $arrError   = array();
        foreach($arrField as $key=>$value)
        {
            $value = trim($value);
            if(!is_numeric ($value) && $value < 0){
                $arrError []= array('field'=>$key, 'message' => $errorMessage);
            }
        }
        return $arrError;
    }

    public static function checkIsNumber($arrField, $errorMessage = 'this field is numeric only')
    {
        if(!is_array($arrField))
            return array('');

        $arrError   = array();
        foreach($arrField as $key=>$value)
        {
            $value = trim($value);
            if(!is_numeric ($value) && $value != '0'){
                $arrError []= array('field'=>$key, 'message' => $errorMessage);
            }
        }
        return $arrError;
    }

    public static function checkDateTime($arrField, $errorMessage = 'this field is DateTime only')
    {
        if(!is_array($arrField))
            return array('');

        $arrError   = array();
        foreach($arrField as $key=>$value)
        {
            $value = trim($value);
            if(trim($value) == '')
            {
                $arrError []= array('field'=>$key, 'message' => $errorMessage);
            }else {
                try {
                    $value = new DateTime($value);
                } catch (Exception $ex) {
                    $arrError [] = array('field' => $key, 'message' => $errorMessage);
                }
            }
        }
        return $arrError;
    }

    public static function encodeValues($values,$encode = false, $stripTags = true)
    {
        if(is_array($values))
        {
            foreach($values as $key=>$value)
            {
                if(is_string($value)) {
                    if($encode)
                        $value = htmlspecialchars($value, ENT_QUOTES, "UTF-8");
//                    $value = preg_replace('/(<[^>]>+) style=".*?"/i','',$value);
                    $value = preg_replace('/(<[^>]>+) style=".*?"/i','',$value);

                    if($stripTags)
                        $value = strip_tags($value, '<a><br><br/>');

                    $values[$key] = $value;

                }
            }
        }
        return $values;
    }

    public static function stripTagsFeed($msg)
    {
        $msg = strip_tags($msg, '<a><p><div><h1><h2><h3><h4><h5><h6><br><br/><span><img>');

       /* $msg = preg_replace("/<b[^>]*?>/", "", $msg);
        $msg = str_replace("</b>", "", $msg);*/

        /*$msg = preg_replace("/<span[^>]*?>/", "", $msg);
        $msg = str_replace("</span>", "", $msg);*/

        $msg = preg_replace("/<p[^>]*?>/", "", $msg);
        $msg = str_replace("</p>", "", $msg);

        /*$msg = preg_replace("/<div[^>]*?>/", "", $msg);
        $msg = str_replace("</div>", "", $msg);*/

        /*$msg = preg_replace("/<img[^>]*?>/", "", $msg);*/


        $msg = preg_replace("/<h[^>]*?>/", "", $msg);
        $msg = str_replace("</h*>", "", $msg);



        return $msg;

    }

    public static function replaceLinkToHtmlTag($string)
    {
        $url = '~(?:(https?)://([^\s<]+)|(http?)://([^\s<]+)|(www\.[^\s<]+?\.[^\s<]+))(?<![\.,:])~i';
        $string = preg_replace($url, '<a href="$0" target="_blank" title="$0">$0</a>', $string);
//        $string = preg_replace($url, ' $0 ', $string);
        return $string;
//        $str = $string;
//        $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
//        $urls = array();
//        $urlsToReplace = array();
//        if(preg_match_all($reg_exUrl, $str, $urls)) {
//            $numOfMatches = count($urls[0]);
//            $numOfUrlsToReplace = 0;
//            for($i=0; $i<$numOfMatches; $i++) {
//                $alreadyAdded = false;
//                $numOfUrlsToReplace = count($urlsToReplace);
//                for($j=0; $j<$numOfUrlsToReplace; $j++) {
//                    if($urlsToReplace[$j] == $urls[0][$i]) {
//                        $alreadyAdded = true;
//                    }
//                }
//                if(!$alreadyAdded) {
//                    array_push($urlsToReplace, $urls[0][$i]);
//                }
//            }
//            $numOfUrlsToReplace = count($urlsToReplace);
//            for($i=0; $i<$numOfUrlsToReplace; $i++) {
//                $str = str_replace($urlsToReplace[$i], "<a href=\"".$urlsToReplace[$i]."\">".$urlsToReplace[$i]."</a> ", $str);
//            }
//            return $str;
//        } else {
//            return $str;
//        }
    }

    public static function replaceLinkToEmpty($string)
    {
        $url = '~(?:(https?)://([^\s<]+)|(http?)://([^\s<]+)|(www\.[^\s<]+?\.[^\s<]+))(?<![\.,:])~i';
        $string = preg_replace($url, '', $string);
        return $string;

    }

    public static function remove_empty_tags_recursive ($str, $repto = NULL)
    {
        //** Return if string not given or empty.
        if (!is_string ($str)
            || trim ($str) == '')
            return $str;

        //** Recursive empty HTML tags.
        return preg_replace (

        //** Pattern written by Junaid Atari.
        '/<([^<\/>]*)([^<\/>]*)>([\s]*?|(?R))<\/\1>/imsU',
            //** Replace with nothing if string empty.
            !is_string ($repto) ? '' : $repto,

            //** Source string
            $str
        );
    }

    public static function removeMultipleBrTag($string, $removeFirstEnd = true)
    {
        $string = preg_replace('/(?:\s*<br[^>]*>\s*){2,}/s', "<br>", $string);
        if($removeFirstEnd)
            $string = self::removeFirstAndLastBrTags($string);

        return $string;
    }

    public static function removeFirstAndLastBrTags($string)
    {
        $string = preg_replace('{^(| )+}i', '', $string);
        $string = preg_replace('{(| )+$}i', '', $string);
        return $string;
    }
} 