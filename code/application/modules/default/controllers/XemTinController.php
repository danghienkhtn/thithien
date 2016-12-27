<?php
/**
 * @name        :   XemTinController
 * @version     :   20161226
 * @copyright   :   DaHi
 * @todo        :   controller default 
 */

class XemTinController extends Core_Controller_Action
{     
     public function init() {
        parent::init();
                
        //Get Controller
        $controller = $this->_request->getParam('controller');
        $this->view->controller = $controller;
       
    }

    /**
     * Default action
     */
    public function indexAction()
    {
        $newsId = $this->_getParam('nid', '');
        $returnUrl = BASE_URL;
        if(empty($newsId) || !Core_Validate::checkNumber($newsId)){
            error_log("nid null");
            $this->_redirect('/');
            exit;   
        }
        else{
            error_log("nid=".$newsId);
            $this->view->newsId = $newsId;
            $this->view->returnUrl = $returnUrl;
            /*if(Core_Validate::checkNumber($newsId)){
                $arrNewsDetail = News::getInstance()->getNewsByID($newsId);
                if(is_array($arrNewsDetail) && sizeof($arrNewsDetail)>0){
                    switch ($arrNewsDetail['news_type']){
                        case 1://properties

                            break;
                        case 2://job

                            break;
                        case 3://car

                            break;
                        case 4://bike

                            break;
                        case 6://electic    
                        default:

                            break;
                                
                    }
                }
            }
            else{
                $this->_redirect('/');
                exit;
            }*/
        }
       // $this->_redirect('/feed');
       // exit();   
    }
     
}

