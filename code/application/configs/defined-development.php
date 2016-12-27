<?php
/**
 * @name        :   defined.php
 * @version     :   201611
 * @copyright   :   DaHi
 * @todo        :   Load define configuration
 */

function setDefineArray($keyGlobalName, $arrData = array(), $isCaseSensitive = true)
{
    if (function_exists('apc_load_constants') && (APP_ENV == 'production')) {
        if (apc_load_constants($keyGlobalName, $isCaseSensitive) == false) {
            apc_define_constants($keyGlobalName, $arrData, $isCaseSensitive);
        }
    } else {
        foreach ($arrData as $keyName => $keyValue) {
            define($keyName, $keyValue, $isCaseSensitive);
        }
    }
}

//Define key name of constant
define('ARRPC_MAIN_INTERNAL_PROJECT_APP_DEFINE', 'ARRPC_MAIN_INTERNAL_PROJECT_APP_DEFINE');
define('ARRPC_MAIN_INTERNAL_PROJECT_BUSINESS_DEFINE', 'ARRPC_MAIN_INTERNAL_PROJECT_BUSINESS_DEFINE');

define('URL', 'http://thithien.com');
define('BASE_ADMIN_URL', 'http://thithien.com/backend/');
define('BASE_ADMIN', 'thiehtien.com/backend');

define('ROOT_CACHE_FILE_PATH', './data/cache');
define('ROOT_IMAGES_PATH', './data/upload');
define('ROOT_IMAGES_URL', URL . '/upload');
define('MAX_UPLOAD_SIZE', 51200000); //50MB
define('MAX_UPLOAD_PHOTO_SIZE', 51200000); //50MB

define('MIN_WIDTH', 400);//px
define('MIN_HEIGHT', 400);//px

define('TOKEN_API', 'token-api');

//folder image
define('FOLDER_AVATARS', 'avatar');
define('FOLDER_IMAGES', 'images');
define('FOLDER_FILES', 'files');
define('FOLDER_NEWS', 'news');
define('FOLDER_MAIL', 'mail');
define('FOLDER_GROUP', 'group');
define('FOLDER_CALENDAR_FILES', 'calendar');
define('THUMBNAIL', 'thumbnail');
define('DETAIL', 'detail');

//folder cache
define('FOLDER_USER', 'user');

//Facebook API
define('FB_APP_ID','1243244549097286');
define('FB_APP_SECRET','b5fb564db22753f04dfca58eaa85948c');

define('GG_RECAPTCHA_SITE_KEY','6Lc8Ww4UAAAAADex6QFz4YTmWdUW9nB6a6AQTY8z');
define('GG_RECAPTCHA_SECRET','6Lc8Ww4UAAAAAKLadWT-J3Rfwwea-_4vE-CIOorN');

define('GG_CREDENTIALS_KEY','668835253562-h1ceu9fp00vq0tb04b1cv5pvf4oqv6bf.apps.googleusercontent.com');
define('GG_CREDENTIALS_SECRET','dOQNZ3PyQeS5NANYBEqDMyXC');
define('GG_API_KEY','thithien-150805');
define('GG_APP_NAME','Login thithien.com');



// define('FOLDER_GROUP_MEMBER', 'group-member');

//----------------START define key REDIS---------------------------------------//
//GROUP
/*define('REDIS_GROUP_ALL_LIST', 'redis_group_all_list_key');
define('REDIS_GROUP_INVITE_MEMBER_LIST', 'redis_group_invite_member_key');
define('REDIS_GROUP_REQUEST_MEMBER_LIST', 'redis_group_request_member_key');
define('REDIS_GROUP_SUGGESTION_MEMBER_LIST', 'redis_group_suggestion_member_key');*/

// GROUP MEMBER
/*define('REDIS_GROUPMEMBER_MEMBER_ID', 'redis_groupmember_member_id_key');
define('REDIS_GROUPMEMBER_GROUP_ID', 'redis_groupmember_group_id_key');*/

// FEED
/*define('REDIS_FEED_LIKE', 'redis_feed_like_key');
define('REDIS_FEED_ACCOUNT_LIST_KEY', 'redis_feed_account_list_key');
define('REDIS_FEED_LIST_KEY', 'redis_feed_list_key');
define('REDIS_FEED_COMMENT_USER_TAG', 'redis_feed_comment_user_tag');
define('REDIS_FEED_POST_USER_TAG', 'redis_feed_post_user_tag');

define('REDIS_FEED_GROUP_NOTIFY', 'redis_group_feed_');
define('REDIS_FEED_GROUP_PHOTO_LIST', 'redis_groupfeed_photo_list_key');
define('REDIS_FEED_GROUP_LIST', 'redis_group_feed_list_key');*/

//COMMENT
/*define('REDIS_COMMNET_LIST_KEY', 'redis_commnet_list_key');
define('REDIS_COMMENT_LIST_KEY', 'redis_comment_list_key');
define('REDIS_COMMENT_PHOTOFEED_LIST_KEY', 'redis_comment_photofeed_list_key');*/


// FILE
/*define('REDIS_FEED_FILE_LIST', 'redis_feed_file_list');
define('REDIS_FILEFEED_LIST_KEY', 'redis_filefeed_list_key');
*/
// Notification
/*define('REDIS_NEW_ACTIVITY_NOTIFY_KEY', 'redis_new_activity_notify_key');
define('REDIS_ACTIVITY_NOTIFY_KEY', 'redis_activity_notify_key');
define('REDIS_TURN_OFF_NOTIFY_GROUP_KEY', 'redis_turn_off_notify_group_key');
define('REDIS_SOUND_GROUP_ACTIVITY_NOTIFY_KEY', 'redis_sound_group_activity_notify_key');

define('REDIS_NEW_TOOL_NOTIFY_KEY', 'redis_new_tool_notify_key');
define('REDIS_TOOL_NOTIFY_KEY', 'redis_tool_notify_key');
*/
//----------------END define key REDIS----------------------------------------//




//----------------START define key MEMCACHED----------------------------------//
// GROUP
/*define('CACHE_GROUP_DETAIL_KEY', 'group_detail_key');
define('CACHE_GROUP_DETAIL_EXPIRED', 'group_detail_expired');
*/
// GROUP MEMBER
/*define('CACHE_GROUPMEMBER_LIST', 'groupmember_list_key');
define('CACHE_GROUPMEMBER_EXPIRED', 'groupmember_list_expired');
define('CACHE_GROUPMEMBER_ALL_LIST', 'groupmember_all_list_key');
define('CACHE_GROUPMEMBER_ALL_EXPIRED', 'groupmember_all_list_expired');
define('CACHE_GROUPMEMBER_GROUP_ACCOUNT', 'groupmember_group_account_key');
define('CACHE_GROUPMEMBER_GROUP_ACCOUNT_EXPIRED', 'groupmember_group_account_expired');
*/
// ACCOUNT
/*define('CACHE_ACCOUNT_DETAIL_SHORT_KEY', 'account_detail_short_key');
define('CACHE_ACCOUNT_DETAIL_SHORT_EXPIRED', 'account_detail_short_expired');
*/
// FEED
/*define('FEED_DETAIL_EXPIRED', 'feed_detail_expired');
define('FEED_DETAIL_KEY', 'feed_detail_key');
*/
/*define('FEED_LIST_EXPIRED', 'feed_list_expired');
define('FEED_LIST_KEY', 'feed_list_key');
*/

//COMMENT
/*define('CACHE_COMMENT_DETAIL_KEY', 'comment_detail_key');
define('CACHE_COMMENT_DETAIL_EXPIRED', 'comment_detail_key_expired');
define('COMMENT_PHOTOFEED_DETAIL_KEY', 'comment_photofeed_detail_key');
define('COMMENT_PHOTOFEED_DETAIL_EXPIRED', 'gcomment_photofeed_detail_expired');
*/

// FILE
/*define('FILE_DETAIL_KEY', 'file_detail_key');
define('FILE_DETAIL_EXPIRED', 'file_detail_expired');
*/
//----------------End define key MEMCACHED----------------------------------//


//Set global main application
$arrGlobalMainApplication = array(
    'BASE_URL' => URL,
    'BASE_API_URL' => URL . '/api',
    'BASE_STATIC_URL' => URL,
    'DOMAIN' => 'thithien.com',
    
    /* Ldap config  'thithien\it-tool',  thithien\all */
    /*'LDAP_NAME' => 'thithien.com',
    'LDAP_SERVER' => 'dc.thithien.com',
    'LDAP_PORT' => '389',
    'LDAP_CONNECTION_FILTER' => '(&(objectClass=user)(objectCategory=person)(!(userAccountControl:1.2.840.113556.1.4.803:=2)))',
    'LDAP_BASEDN' => 'DC=thithien,DC=com',
    'LDAP_ROOTDN' => 'OU=GNT Japan,DC=thithien,DC=com',*/
//    'LDAP_ROOTDN' => 'OU=GNT Japan,DC=thithien,DC=com',
//    'LDAP_ROOTDN' => 'OU=GNT Viet Nam,DC=thithien,DC=com',
//    'LDAP_USER_AUTH' => 'hoai.tn@thithien.com',
//    'LDAP_PASS_AUTH' => 'hoaigntvietnam@',
    /*'LDAP_USER_AUTH' => 'portal@thithien.com',
    'LDAP_PASS_AUTH' => 'Abc123!',*/
    'DOMAIN_NAME_EMAIL' => 'thithien.com',

    'PATH_AVATAR_URL' => ROOT_IMAGES_URL . '/' . FOLDER_AVATARS,
    'PATH_AVATAR_UPLOAD_DIR' => ROOT_IMAGES_PATH . '/' . FOLDER_AVATARS,

    'PATH_MAIL_URL' => ROOT_IMAGES_URL . '/' . FOLDER_MAIL,
    'PATH_MAIL_UPLOAD_DIR' => ROOT_IMAGES_PATH . '/' . FOLDER_MAIL,

    'PATH_NEWS_URL' => ROOT_IMAGES_URL . '/' . FOLDER_NEWS,
    'PATH_NEWS_UPLOAD_DIR' => ROOT_IMAGES_PATH . '/' . FOLDER_NEWS,

    'PATH_IMAGES_URL' => ROOT_IMAGES_URL . '/' . FOLDER_IMAGES,
    'PATH_IMAGES_UPLOAD_DIR' => ROOT_IMAGES_PATH . '/' . FOLDER_IMAGES,

    'PATH_FILES_URL' => ROOT_IMAGES_URL . '/' . FOLDER_FILES,
    'PATH_FILES_UPLOAD_DIR' => ROOT_IMAGES_PATH . '/' . FOLDER_FILES,

    'PATH_VIDEOS_URL' => ROOT_IMAGES_URL . '/videos',
    'PATH_VIDEOS_UPLOAD_DIR' => ROOT_IMAGES_PATH . '/videos',

/*    'PATH_ABSENCES_URL' => ROOT_IMAGES_URL . '/absences',
    'PATH_ABSENCES_UPLOAD_DIR' => ROOT_IMAGES_PATH . '/absences',

    'PATH_GROUPS_URL' => ROOT_IMAGES_URL . '/group',
    'PATH_GROUPS_UPLOAD_DIR' => ROOT_IMAGES_PATH . '/group',
*/
    /* Server Exchange */
    //'SERVER_EXCHANGE_API' => 'mail3.thithien.com',
    // 'SERVER_EXCHANGE_API' => '123.30.163.222'
);

//Init global define
setDefineArray(ARRPC_MAIN_INTERNAL_PROJECT_APP_DEFINE, $arrGlobalMainApplication, true);

//Set business define
$arrGlobalBusinessApplication = array(
    /* Main Config */
    'APP_CONFIG' => 'appTablerAdminConfig',
    'APP_MOBILE_CONFIG' => 'appTablerAdminMobileConfig',
    'CACHING_CONFIG' => 'cachingTablerAdminConfig',
    'VIEWS_CONFIG' => 'viewsTablerAdminConfig',
    'LOCALE_CONFIG' => 'localesTablerAdminConfig',
    'LANG_CONFIG' => 'langTablerAdminCurrentConfig',


    /* PREFIX CACHE */
    'CACHING_PREFIX' => 'internal:project:',

    /* DOMAIN CONFIG */
    'DOMAIN_COOKIE_NAME' => 'DEV_PREF_PORTAL_MAIN',
    'DOMAIN_COOKIE_EXPIRED' => 60 * 60 * 24 * 120,//20 days

    'AUTH_USER_LOGIN_TOKEN' => "tt_user_token",
    'AUTH_ADMIN_LOGIN_TOKEN' => "tt_auth_token",

    'AUTH_LANGUAGE' => "tt_auth_lang",
    'AUTH_SAYHI' => "tt_sayhi",

    'STATIC_LOCALE_VERSION' => '',
    'GLOBAL_MOBILE_FLAG' => 'mbFlagConfig',
    'ERROR_SUCCESS' => 0,
    'ADMIN_PAGE_SIZE' => 15,
    'TEXTBOX_ATTRIBUTE_TYPE' => 1,
    'TEXTAREA_ATTRIBUTE_TYPE' => 2,
    'DEBUG_SOLR' => 0,
    'MAX_QUERY_LIMIT' => 10000,
    'MAX_PAGE_LIMIT' => 30,
    'PHOTO_LIMIT' => 10, //10GB
    'FILE_LIMIT' => 10, //10GB

    /* Notify Action */
/*    'NOTIFY_APP_FEED' => 1,
    'NOTIFY_POST_FEED' => 1,
    'NOTIFY_LIKE_FEED' => 2,
    'NOTIFY_COMMENT_FEED' => 3,
    'NOTIFY_ADDTAG_FEED' => 4,

    'NOTIFY_GROUP_APP' => 5,
    'NOTIFY_GROUP_REQUEST_PRIVATE' => 6,//group is private
    'NOTIFY_GROUP_REQUEST_PUBLICH' => 7,//group is public
    'NOTIFY_GROUP_ACCEPT_REQUEST' => 8,//admin accept request
    'NOTIFY_GROUP_ACCEPT_INVITE' => 9,//user accept invite
    'NOTIFY_GROUP_INVITE_JOIN' => 10,//admin invite join group

    'NOTIFY_ABSENCE_APP' => 11,//absence
    'NOTIFY_ABSENCE_REQUEST' => 12,//user sent request absence to last account
    'NOTIFY_MANUAL_ABSENCE_ATTENDANCE' => 13,//check manual absence by attendance
    'NOTIFY_CHECK_CARD_ATTENDANCE_MACHINE' => 14,//check user card  by attendance

    'NOTIFY_APP_CALENDAR' => 15, // Application calendar
    'NOTIFY_ADD_CALENDAR_EVENT' => 16, // add new event


    'NOTIFY_EXPENSE_APP' => 17,//expense
    'NOTIFY_EXPENSE_REQUEST' => 18,//expense request*/
);

//Init business define
setDefineArray(ARRPC_MAIN_INTERNAL_PROJECT_BUSINESS_DEFINE, $arrGlobalBusinessApplication, true);

//Cleanup resource
unset($arrGlobalBusinessApplication, $arrGlobalMainApplication);

//limit file
define('LIMIT4', 4);

// define for admin type
define('NORMAL_ADMIN', 0);
define('SUPER_ADMIN', 1);

//define for File
define('FILE', 0);
define('FOLDER', 1);


// other album img
define('AlbumOther','default_album.jpg');

//group avatar default
// define('GroupAvatar','gnt-logo.png');
// define('AvatarGroupOther','avatar-group-other-default.jpg');
define('AvatarDefault','default-avatar.png');
define('AvatarAdminDefault','default-admin-avatar.png');
// define for admin status
define('DEACTIVE_ADMIN_STATUS', 0);
define('ACTIVE_ADMIN_STATUS', 1);

// define for menu
define('PAGE', 1);
define('TOOL', 2);

// define day index in week
define('MON', 1);
define('TUE', 2);
define('WED', 3);
define('THU', 4);
define('FRI', 5);
define('SAT', 6);
define('SUN', 7);

//define for calendar event
/*define('EVENT_PROJECT',      1);
define('EVENT_TEAM',         2);
define('EVENT_TOOL',         3);
define('EVENT_PERSONAL',     4);
define('EVENT_ANUAL_LEAVE',  5);
define('EVENT_DEFAULT_GROUP',     6);
define('EVENT_OTHER_GROUP',     7);
define('CALENDAR_EVENT_FROM_TIME',  '8:00');//start from 8:00 AM
define('CALENDAR_EVENT_TO_TIME',  '23:30');//to 23:30 PM
define('EVENT_LOW_PRIORITY', 1);
define('EVENT_NORMAL_PRIORITY', 2);
define('EVENT_HEIGH_PRIORITY', 3);
define('EVENT_DAILY', 1);
define('EVENT_WEEKLY', 2);
define('EVENT_MONTHLY', 3);
define('EVENT_YEARLY', 4);
define('EVENT_ALLDAY', 1);
define('EVENT_REPEAT', 1);
define('EVENT_MIN_REPEAT', 1);
define('EVENT_MAX_REPEAT', 30);
define('EVENT_SEARCH_USER_BY_NAME', 1);
define('EVENT_SEARCH_USER_BY_EMAIL', 2);
define('PERMISSION_READ_WRITE_FILE', 0777);
define('CALENDAR_DAY_TAB', 1);
define('CALENDAR_WEEK_TAB', 2);
define('CALENDAR_MONTH_TAB', 3);
define('CALENDAR_WORK_WEEK_TAB', 4);*/

//define for group
/*define('DEFAULT_GROUP', 1);
define('TEAM_GROUP', 2);
define('PROJECT_GROUP', 3);
define('OTHER_GROUP', 4);*/

//define for absence
/*define('ABSENCE_PENDING_STATUS', 1);
define('ABSENCE_OK_STATUS', 3);
define('ABSENCE_REJECT_STATUS', 4);*/

//define for language
define('LANGUAGE_EN', 'en');
define('LANGUAGE_JP', 'ja');
define('LANGUAGE_VI', 'vi');
define('DEFAULT_LANGUAGE', LANGUAGE_VI);

//define for user config
define('USER_CONFIG_LANGUAGE', 1);
define('USER_CONFIG_TIMEZONE', 2);

//define timezone
define('TIMEZONE_VI', 'Asia/Ho_Chi_Minh');
define('TIMEZONE_JA', 'Asia/Tokyo');
define('TIMEZONE_EN', 'Europe/London');
define('DEFAULT_TIMEZONE', TIMEZONE_VI);

//define file extendsion
define('ENABLE_EXT_LIST', 'doc, docx, xls, xlsx, ppt, pptx, pdf');

//define for notification settings
/*define('NOTIFICATION_SETTING_SOUND',                        1);
define('NOTIFICATION_SETTING_FEED_MESSAGE_ON_BELL',         2);
define('NOTIFICATION_SETTING_CALENDAR_EVENT_ON_BELL',       3);
define('NOTIFICATION_SETTING_NEW_ACTIVITIES_ON_MAIN_FEED',  4);
*/
//Define for Docs onlyoffice
/*define('FOLDER_USER_DOCS_SIZE_MAX', 1024*1024*1024);//1 GB
define('FOLDER_GROUP_DOCS_SIZE_MAX', 20*1024*1024*1024);//20 GB
define('FILE_SIZE_MAX', 50*1024*1024);//50 MB
define('STORAGE_PATH', '');

define('MODE', '');

define('DOC_SERV_VIEWD', '.ppt,.pps,.odp,.pdf,.djvu,.fb2,.epub,.xps');
define('DOC_SERV_EDITED', '.docx,.doc,.odt,.xlsx,.xls,.ods,.csv,.pptx,.ppsx,.rtf,.txt,.mht,.html,.htm');
define('DOC_SERV_CONVERT', '.doc,.odt,.xls,.ods,.ppt,.pps,.odp,.rtf,.mht,.html,.htm,.fb2,.epub');
define('DOC_SERV_UPLOAD_DENIED', '.exe,.sh,.msi');

define('DOC_SERV_TIMEOUT', '120000');

define('DOC_SERV_IP', '192.168.38.183');
define('DOC_SERV_STORAGE_URL', 'https://docs.thithien.com/FileUploader.ashx');
define('DOC_SERV_CONVERTER_URL', 'https://docs.thithien.com/ConvertService.ashx');
define('DOC_SERV_API_URL',  'https://docs.thithien.com/OfficeWeb/apps/api/documents/api.js');

define('DOC_SERV_PRELOADER_URL', 'https://docs.thithien.com/OfficeWeb/apps/api/documents/cache-scripts.html');


define('EXTS_SPREADSHEET', '.xls,.xlsx,.ods,.csv');

define('EXTS_PRESENTATION', '.pps,.ppsx,.ppt,.pptx,.odp');

define('EXTS_DOCUMENT',  '.docx,.doc,.odt,.rtf,.txt,.html,.htm,.mht,.pdf,.djvu,.fb2,.epub,.xps');

define('EXTS_IMAGE',  '.jpg,.gif,.png,.jpeg');

define('DOC_BACKUP_PATH', '/data/docs/backup');
define('DOC_ROOT_PATH', '/data/docs/store');
define('ROOT_DOC_URL', URL . '/docs/store');
define('DOC_TOKEN_EXPIRED', 365*24*60*60);//1 year
*/
if ( !defined('ServiceConverterMaxTry') )
    define( 'ServiceConverterMaxTry', 3);

if ( !defined('ServiceConverterTenantId') )
    define( 'ServiceConverterTenantId', '');
if ( !defined('ServiceConverterKey') )
    define( 'ServiceConverterKey', '');

// Chat
// define('CHAT_SERVER_IP', '192.168.34.22');