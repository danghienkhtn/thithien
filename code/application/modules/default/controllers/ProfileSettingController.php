<?php
/**
 * Setting user profile
 *
 * @category  Controller
 * @package   ProfileSettingController
 * @author    Tai Le Thanh <tai.lt@vn.gnt-global.com>
 * @copyright 2016 Gianty
 * @license   http://www.gianty.com.vn CIO Team
 * @link      http://svn.sgcharo.com/svn/portal/sourcecode
 */

class ProfileSettingController extends Core_Controller_Action
{
     
    public function init() 
    {  
        parent::init();
    
        //Get Controller
        $controller = $this->_request->getParam('controller');
        $this->view->controller = $controller;
    }

    public function indexAction()
    {
        $arrLogin       = Admin::getInstance()->getLogin();
        $accountId = $arrLogin['accountID'];
        
        $getLanguageParam = array(
            'accountId' => $accountId,
            'key' => USER_CONFIG_LANGUAGE,
        );
        $getTimezoneParam = array(
            'accountId' => $accountId,
            'key' => USER_CONFIG_TIMEZONE,
        );

        $languageConfig = UserConfig::getInstance()->getUserConfigByKey($getLanguageParam);
        $timezoneConfig = UserConfig::getInstance()->getUserConfigByKey($getTimezoneParam);

        $language = DEFAULT_LANGUAGE;
        $timezone = DEFAULT_TIMEZONE;
        isset($languageConfig['value']) && $language = $languageConfig['value'];
        isset($timezoneConfig['value']) && $timezone = $timezoneConfig['value'];
        
        if ($this->_request->isPost()) {
            $language = $this->_request->getPost('language');
            $timezone = $this->_request->getPost('timezone');
            
            //filter language
            $language = Core_Common::getLanguage($language);
            $timezone = Core_Common::fillterTimezone($timezone);
            
            //change language to session
            $_SESSION['language'] = $language;
            
            $updateLanguageParam = array(
                'accountId' => $accountId,
                'key' => USER_CONFIG_LANGUAGE,
                'value' => $language,
            );
            $updateTimezoneParam = array(
                'accountId' => $accountId,
                'key' => USER_CONFIG_TIMEZONE,
                'value' => $timezone,
            );
            
            //update language to user config
            UserConfig::getInstance()->writeUserConfig($updateLanguageParam);
            //update timezone to user config
            UserConfig::getInstance()->writeUserConfig($updateTimezoneParam);
            
            //redirect to web app reload new language session
            $url = $this->_request->getServer('HTTP_REFERER');
            $this->_redirect($url);
            exit();
        }
        
        $this->view->language = $language;
        $this->view->timezone = $timezone;
    }
}

