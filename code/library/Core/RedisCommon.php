<?php
/**
 * Do get/set data on redis db
 *
 * @category  lib
 * @package   Core_RedisCommon
 * @author    Tai Le Thanh <tai.lt@vn.gnt-global.com>
 * @copyright 2016 Gianty
 * @license   http://www.gianty.com.vn CIO Team
 * @link      http://svn.sgcharo.com/svn/portal/sourcecode
 */
class Core_RedisCommon
{
  
    public static function getNotificationSettingKey($accountId, $groupId, $notificationType)
    {
        $prefixKey = self::getNotificatonSettingPrefixKey();
        
        $res = Core_Global::getKeyPrefixCaching($prefixKey);
        $res .= ':' . $accountId;
        $res .= ':' . $groupId;
        $res .= ':' . $notificationType;
        
        return $res;
    }
    
    public static function saveNotificationSetting($accountId, $groupId, $notificationType, $value)
    {
        $key = self::getNotificationSettingKey($accountId, $groupId, $notificationType);
        Core_Business_Nosql_Redis::getInstance()->setKey($key, $value);
    }
    
    /*
        retrun:
     *          1:          disable notification
     *          0 or FALSE: enable notificaton
    */
    public static function getNotificationSetting($accountId, $groupId, $notificationType)
    {
        $key = self::getNotificationSettingKey($accountId, $groupId, $notificationType);
        return Core_Business_Nosql_Redis::getInstance()->getKey($key);        
    }
    
    public static function getNotificatonSettingPrefixKey()
    {
        $res = 'notification_setting_detail'; 
        return $res;
    }
}
?>
