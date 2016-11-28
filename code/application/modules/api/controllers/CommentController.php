<?php
/**
 * @author      :   Hoaitn
 * @name        :   ApiController
 * @version     :   20110214
 * @copyright   :   My company
 * @todo        :   controller API 
 */
class Api_CommentController extends Zend_Controller_Action
{
    
    /**
     * init of controller
     */
    public function init()
    {
        //Disale layout
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

   
    }
    
    public function indexAction()
    {
//        $arrLogin = Admin::getInstance()->getLogin();
//        if($arrLogin['username'] == 'thanh.lh') {
////        Core_Common::var_dump(Admin::getInstance()->getLogin());
//            $locales = new Zend_Locale($arrLogin['lang']);
//            Core_Common::var_dump($this->view->locales->validate);
//        }

    	//Set option for server
    	$options = array(
    			'adapter' => 'rest'
    	);
    
    	//Get server instance
    	$serverInstance = Core_Server::getInstance($options);
    
    	//Register class call
    	$serverInstance->setClass('ApiComment');
    
    	//Hanlde instance
    	$serverInstance->handle($this->_request);
    }
    
   
    
}

