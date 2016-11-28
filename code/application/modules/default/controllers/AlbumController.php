<?php
/**
 * Created by PhpStorm.
 * User: thanh.lh
 * Date: 7/2/2015
 * Time: 10:47 AM
 */

class AlbumController extends Core_Controller_Action{


    public function showMoreAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $groupId = $this->_getParam('groupId','');
        $offset = $this->_getParam('offset',0);
        $limit = $this->_getParam('limit',4);
        $limit = ($limit > 4) ? 4 : $limit;

//        if(!$this->_request->isPost())
//        {
//            echo Zend_Json::encode(array('html' => '', 'total' => 0, 'offset' => 1000, 'show_more' => false, 'error' => 'POST only support'));
//            exit();
//        }

        $group = Group::getInstance()->getGroupByID($groupId);
        if(empty($group)) {
            echo Zend_Json::encode(array('html' => '', 'total' => 0, 'offset' => 0, 'show_more' => false, 'error' => 'group not found'));
            exit();
        }


        $name = '';
        $iYear = '';
        $type = 0;
        $iActive = 1;
        $offsetTmp = ($offset > 0) ? $offset  * $limit :0;


        $arrAlbum = Album::getInstance()->getAlbumList($name, $iYear, $type,$iActive, $groupId, $offsetTmp, $limit);

        $showMoreAlbums = Album::getInstance()->getAlbumList($name, $iYear, $type,$iActive, $groupId, $offsetTmp + $limit, 1);
        $bShowMore = (empty($showMoreAlbums['data'])) ? false : true;
//        Core_Common::var_dump($arrAlbum);
        $total = count($arrAlbum['data']);
        if(!empty($arrAlbum['data']))
            $offset++;

        $html   =  $this->view->partial('album/lst-album.phtml',array('group' => $group, 'arrAlbum' => $arrAlbum));
        echo Zend_Json::encode(array( "offset" => $offset, "total" => $total, "html" => htmlspecialchars($html, ENT_QUOTES, "UTF-8"), 'showMore'=>$bShowMore));
    }

}