<?php
/**
 * Created by PhpStorm.
 * User: thanh.lh
 * Date: 2/22/2016
 * Time: 9:31 AM
 */

class ValidateController extends Core_Controller_Action{

    private $arrLogin;

    function init()
    {
        parent::init();
        $this->arrLogin = $this->view->arrLogin;
    }
    public function metaTagAction()
    {
        $sURL = $this->_getParam('url','');

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

        $sHost = $arrUrl['scheme'] . '://' . $arrUrl['host'];
        $sHost = str_replace('://www.', '://', $sHost);
        $protocol = $arrUrl['scheme'];

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
//        Core_Common::var_dump($clientInstance);
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
//        Core_Common::var_dump($sResponse);
        //Check response data
        if(!empty($sResponse))
        {
            //Convert to UTF8 Data
            $sResponse = Core_String::convertToUtf8($sResponse);

            //Remove all Javascript content
            $sResponse = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $sResponse);
        }
//        Core_Common::var_dump($sResponse);
        //Set data
        $arrLink = array();
        Core_Common::var_dump($arrLink);
        //Get metadata
        @preg_replace(array('#<meta property=(\'|")(.*)(\\1) content=(\'|")(.*)(\\4)>#esiU'), array("Core_Utility::getContentLink(" . $arrLink . ",'meta','\\2','','','\\5', '')"), $sResponse);
        Core_Common::var_dump($arrLink);
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
        Core_Common::var_dump($metaTags);
    }

    public function get_tags($url) {

        $html = file_get_contents($url);

        @libxml_use_internal_errors(true);
        $dom = new DomDocument();
        $dom->loadHTML($html);
        $xpath = new DOMXPath($dom);
        $query = '//*/meta[starts-with(@property, \'og:\')]';
        $result = $xpath->query($query);

        foreach ($result as $meta) {
            $property = $meta->getAttribute('property');
            $content = $meta->getAttribute('content');

            // replace og
            $property = str_replace('og:', '', $property);
            $list[$property] = $content;
        }
        return $list;
    }
    function getUrlData($url)
    {
        $result = false;

        $contents = $this->getUrlContents($url);
        Core_Common::var_dump($contents);
        if (isset($contents) && is_string($contents))
        {
            $title = null;
            $metaTags = null;

            preg_match('/<title>([^>]*)<\/title>/si', $contents, $match );

            if (isset($match) && is_array($match) && count($match) > 0)
            {
                $title = strip_tags($match[1]);
            }

            preg_match_all('/<[\s]*meta[\s]*name="?' . '([^>"]*)"?[\s]*' . 'content="?([^>"]*)"?[\s]*[\/]?[\s]*>/si', $contents, $match);

            if (isset($match) && is_array($match) && count($match) == 3)
            {
                $originals = $match[0];
                $names = $match[1];
                $values = $match[2];

                if (count($originals) == count($names) && count($names) == count($values))
                {
                    $metaTags = array();

                    for ($i=0, $limiti=count($names); $i < $limiti; $i++)
                    {
                        $metaTags[$names[$i]] = array (
                            'html' => htmlentities($originals[$i]),
                            'value' => $values[$i]
                        );
                    }
                }
            }

            $result = array (
                'title' => $title,
                'metaTags' => $metaTags
            );
        }

        return $result;
    }

    function getUrlContents($url, $maximumRedirections = null, $currentRedirection = 0)
    {
        $result = false;

        $contents = @file_get_contents($url);

        // Check if we need to go somewhere else

        if (isset($contents) && is_string($contents))
        {
            preg_match_all('/<[\s]*meta[\s]*http-equiv="?REFRESH"?' . '[\s]*content="?[0-9]*;[\s]*URL[\s]*=[\s]*([^>"]*)"?' . '[\s]*[\/]?[\s]*>/si', $contents, $match);

            if (isset($match) && is_array($match) && count($match) == 2 && count($match[1]) == 1)
            {
                if (!isset($maximumRedirections) || $currentRedirection < $maximumRedirections)
                {
                    return getUrlContents($match[1][0], $maximumRedirections, ++$currentRedirection);
                }

                $result = false;
            }
            else
            {
                $result = $contents;
            }
        }

        return $contents;
    }


    public function allowTagHtmlAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if($this->_request->isPost())
        {
            $string = $this->_getParam('html','');
            $string = preg_replace('/[ ](?=[^>]*(?:<|$))/', '&nbsp', $string);
            $string = htmlspecialchars_decode($string);
            $string = nl2br($string);
//            Core_Common::var_dump(htmlspecialchars_decode($string));
//            $string = Validate::encodeValues(array($string));
//            $string = $string[0];
//            $string = strip_tags($string, '<span><br><br/>');

//            $string = Validate::stripTagsFeed($string);
//            $string = Validate::replaceLinkToHtmlTag($string);
//            $string = Validate::replaceLinkToEmpty($string);
//            $string = Validate::remove_empty_tags_recursive($string);
//            $string = Validate::removeMultipleBrTag($string);

//            $string = preg_replace("~src=[']([^']+)[']~e", '"src=\'" . convert_url("$1") . "\'"', $string);
            /*$string = preg_replace('/<img*src="(.*?)".*\/?>/', '$1', $string);*/
            /*$string = preg_replace("/<img[^>]+\>/i", "(image) ",  $string);*/
            /*$string = preg_replace('/<img src=\"(.*?)\".*\/>/', '<div>$1</div>', $string);*/


            $arrError = array('error'=>false,'message'=>$string);
        }else
            $arrError = array('error'=>true,'message'=>'post support only');

        echo Zend_Json::encode($arrError);

    }
}