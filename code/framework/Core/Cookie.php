<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Cookie
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to cookie
 */
class Core_Cookie
{
    /**
     * Default domain
     */
    const COOKIES_DOMAIN='.mobion.com';

    /**
     * Default expired time : 2 days
     */
    const COOKIES_EXPIRE=7200;

    /**
    * Get cookie value
    * @param <string> $name
    * @param <string> $defaultValue
    * @return <string>
    */
    public static function getCookie($name, $defaultValue = '')
    {
        return isset($_COOKIE[$name]) ? $_COOKIE[$name]:$defaultValue;
    }

    /**
    * Set cookie
    * @param <string> $name
    * @param <string> $value
    * @param <int> $expire
    * @param <string> $path
    * @param <string> $domain
    * @param <boolean> $secure
    * @param <boolean> $httponly
    * @return <boolean>
    */
    public static function setCookie($name , $value , $expire=self::COOKIES_EXPIRE , $path='/', $domain=self::COOKIES_DOMAIN, $secure=false, $httponly=false)
    {
        //Flush buffer
        if(ob_get_length() > 0)
        {
            ob_end_flush();
        }
        
        //Turn on buffer
        ob_start();
        
        //Send cookie header
        @header('P3P: CP="NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM"');
        $cookieExpire = ($expire ==0) ? 0 : time() + (10 * 365 * 24 * 60 * 60); // 10 years
        return @setcookie($name, $value, $cookieExpire, $path, $domain, $secure, $httponly);
    }

    /**
    * Set cookie extend timeout expired
    * @param <string> $name
    * @param <string> $value
    * @param <int> $expire
    * @param <string> $path
    * @param <string> $domain
    * @param <boolean> $secure
    * @param <boolean> $httponly
    * @return <boolean>
    */
    private static function createCookies($name , $value , $expire=self::COOKIES_EXPIRE , $path='/', $domain=self::COOKIES_DOMAIN, $secure=false, $httponly=false)
    {
        //Flush buffer
        if(ob_get_length() > 0)
        {
            ob_end_flush();
        }
        
        //Turn on buffer
        ob_start();
        
        //Send cookie header
        @header('P3P: CP="NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM"');
        $cookieExpire = ($expire ==0) ? 0 :  time() + $expire; // 10 years
        return @setcookie($name, $value, $cookieExpire, $path, $domain, $secure, $httponly);
    }

    /**
    * Clear cookie
    * @param <string> $name
    * @param <string> $path
    * @param <string> $domain
    * @param <boolean> $secure
    * @param <boolean> $httponly
    * @return <boolean>
    */
    public static function clearCookies($name , $path='/', $domain=self::COOKIES_DOMAIN, $secure=false, $httponly=false)
    {
        //Flush buffer
        if(ob_get_length() > 0)
        {
            ob_end_flush();
        }
        
        //Turn on buffer
        ob_start();
        
        //Send cookie header
        @header('P3P: CP="NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM"');
        return @setcookie($name, 'deleted', time()-(10 * 365 * 24 * 60 * 60), $path, $domain, $secure, $httponly);
    }

    /**
    * Extend expired cookie
    * @param <string> $name
    * @param <int> $expire
    * @param <string> $path
    * @param <string> $domain
    * @return <boolean>
    */
    public static function extendExpireTime($name ,$expire=self::COOKIES_EXPIRE, $path='/', $domain=self::COOKIES_DOMAIN)
    {
        if(isset($_COOKIE[$name]))
        {
            $expire = ($expire == 0) ? 0 : time()+$expire;
            $value  = $_COOKIE[$name];
            self::createCookies($name,$value,$expire,$path,$domain);
            return true;
        }
        return false;
    }
}

