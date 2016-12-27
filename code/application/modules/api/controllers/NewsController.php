<?php
class Api_NewsController extends Zend_Controller_Action
{
    public function init() {        
        //Disale layout
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
    }
    
    public function  indexAction()
    {
        echo 'hi news api';
        exit;
    }

    public function  getTemplateAction()
    {
    	$page = "";
    	$nType = $this->_getParam('ntype', '');
    	switch($nType){
    		case '1':    		
    			$page = 'properties';
    		break;
    		case '2':
    			$page = 'job';
    		break;
    		case '3':
    			$page = 'car';
    		break;
    		case '4':
    			$page = 'bike';
    		break;
    	}
    	if(!empty($page)){
    		$this->_helper->layout->setLayout($page);
	        echo $this->_helper->layout->render();
	        exit;
    	}
    	else{
    		echo Zend_Json::encode(Core_Server::setOutputData(true, 'Not found template page', array()));
            exit;
    	}
        
    }
}
?>

