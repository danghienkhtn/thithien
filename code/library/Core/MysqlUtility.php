<?php
class Core_MysqlUtility
{
    /**
     * Escape special char for mysql
     * 
     * @param string $text
     * @return string
     * @author Tai Le Thanh <tai.lt@vn.gnt-global.com>
     */
    public static function escapeSpecialChar($text)
    {
        //replace one backslash to four backslash			
        $text = preg_replace('/(\\\\)/', '\\\\\\\\\\\\\\\\', $text);

        //ecapse special char for mysql: % ' _
        $pattern = array('/(%)/', '/(\')/', '/(_)/');
        $text = preg_replace($pattern, '\\\\$1', $text);
        
        return $text;
    }
}