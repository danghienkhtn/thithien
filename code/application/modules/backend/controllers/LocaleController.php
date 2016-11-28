<?php

/**
 * @name        :   LocaleController
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   controller locale
 */
class LocaleController extends Core_Controller_ActionBackend
{

    /**
     * init of controller
     */
    public function init()
    {
        //Disable and render
        $this->_helper->layout()->disableLayout();
    }

    /**
     * Default action
     */
    public function indexAction()
    {
        $iTimeCached = 24 * 3600;
        $gmdTime     = gmdate("D, d M Y H:i:s", time() + $iTimeCached) . " GMT";
        header("Expires: $gmdTime");
        header("Pragma: cache");
        header("Cache-Control: max-age=$iTimeCached");
        header('Content-Type: application/x-javascript');
    }

}

