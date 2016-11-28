<?php
/**
 * @author      :   Hoaitn
 * @name        :   ApiController
 * @version     :   20110214
 * @copyright   :   My company
 * @todo        :   controller API 
 */
class Api_AlbumController extends Zend_Controller_Action
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

    	//Set option for server
    	$options = array(
    			'adapter' => 'rest'
    	);
    
    	//Get server instance
    	$serverInstance = Core_Server::getInstance($options);
    
    	//Register class call
    	$serverInstance->setClass('ApiAlbum');
    
    	//Hanlde instance
    	$serverInstance->handle($this->_request);
    }
    
   
    
}

