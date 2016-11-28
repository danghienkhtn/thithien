<?php
class Api_IndexController extends Zend_Controller_Action
{
    public function init() {        
        parent::init();
    }
    
    public function  indexAction()
    {
        echo 'hi api';
        exit;
    }
}
?>

