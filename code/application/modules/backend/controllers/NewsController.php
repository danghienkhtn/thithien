<?php
/**
 * @author      :   HoaiTN
 * @name        :   NewsController
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   News Controller 
 */
class Backend_NewsController extends Core_Controller_ActionBackend
{
    private $arrLogin;

    public function init()
    {
        parent::init();
        global $globalConfig;
        //define type
        $this->view->arrType = $globalConfig['news_type'];
        $this->arrLogin      = $this->view->arrLogin;

    }
    
    /**
     * Default action
     */
    public function indexAction()
    {

    }
    
   /**
     * Default action
     */
    public function addAction()
    {
        $news = array('title' => '', 'content' => '', 'source' => '', 'sort_order' => 0, 'image_name' => '', 'image_url' => '', 'ishot' => 1, 'active' => 1);
        if($this->_request->isPost())
        {
            $arrError   = array();
            // process params
            $news['title']  = trim($this->_getParam('title',''));
            $news['content']  = trim($this->_getParam('content',''));
            $news['source']  = trim($this->_getParam('source',''));
            $news['sort_order']  = $this->_getParam('sort_order',0);
            $news['image_name']  = $this->_getParam('image_name','');
            $news['image_url']  = $this->_getParam('image_url','');
            $news['ishot']  = $this->_getParam('ishot',1);
            $news['active']  = $this->_getParam('active',1);

            //validate data
            if(empty($news['title']))
                $arrError []= array('field'=>'title','message'=>'This field is required');

            if(empty($news['content']))
                $arrError []= array('field'=>'content','message'=>'This field is required');

            if(empty($news['image_url']))
                $arrError []= array('field'=>'image_url','message'=>'This field is required');

            if(!empty($arrError))
                $this->view->arrError   = $arrError;
            else{
                if(News::getInstance()->insertNews($news['title'],$news['content'],$news['image_url'],0,$news['source'],$news['sort_order'],$news['ishot'],$news['active']))
                {
                   ActionLog::getInstance()->insert($this->arrLogin['id'],ActionLog::$create,ActionLog::$news,$this->arrLogin['accountID'],$this->arrLogin['nickName'],'" '.$news['title'].' " news');
                   $this->_redirect(BASE_ADMIN_URL.'/news');
                    exit();
                }
                else
                    exit();
            }
        }
        $this->view->news   = $news;
    }
    
    /**
     * Default action
     */
    public function deleteAction()
    {
        $this->_helper->layout()->disableLayout();
        $error  = array('error' => false, 'message' => '');
        if($this->getRequest()->isPost()) {
            //get params
            $arrParam = $this->_request->getParams();
            //get params
            $arrNewsId = $arrParam['news_id'];
            if(is_array($arrNewsId))
            {
                foreach($arrNewsId as $iNewsId)
                {
                    $news   =  News::getInstance()->getNewsByID($iNewsId);
                    if($news)
                    {
                        ActionLog::getInstance()->insert($this->arrLogin['id'],ActionLog::$delete,ActionLog::$news,$this->arrLogin['accountID'],$this->arrLogin['nickName'],'" '.$news['title'].' " news');
                        $nameFile   = trim($news['image_url']);
                        News::getInstance()->removeNews($iNewsId);
                        if(!empty($nameFile))
                            Core_Image::delete(PATH_NEWS_UPLOAD_DIR.'/'.$nameFile);
                    }
                    else{
                        $error  = array('error' => true, 'message' => 'News Not Found');
                    }
                }
            }

        }

        echo Zend_Json::encode($error);
        exit();
    }

    public function lstnewsAction()
    {
        $this->_helper->layout()->disableLayout();

        // process params Get request of DataTable
        $draw =  $this->_getParam('draw',0);
        $limit  = $this->_getParam('length',ADMIN_PAGE_SIZE);
        $offset = $this->_getParam('start',0);

        $queryString = Core_Common::getQueryString();
        $search =  $queryString['search'];
        $columns = $queryString['columns'];
        $key    = isset($search['value']) ? $search['value'] : '';


        $type     = empty($columns[0]['search']['value']) ? '' : $columns[0]['search']['value'];

        // get all without action mode
        $news = News::getInstance()->getNewsList($key, $type, 2, 11, $offset, $limit);
        $data   = array();

        // process data response
        if(!empty($news)){
            foreach($news['data'] as $key=>$new)
            {
                $new      = Core_Common::newsProcess($new);
                $actions    =  ' <a href="'.BASE_ADMIN_URL.'/news/edit?news_id='.$new['news_id'].'" ><i class="fa fa-pencil-square-o"></i></a> ';
                $actions   .=  ' <a href="javascript:void(0);" data-action="new-delete" data-value="'.$new['news_id'].'"><i class="fa fa-trash-o"></i></a> ';
                $order      =  '<a href="javascript:void(0);" ><i class="fa fa-ellipsis-v"></i></a>';
                $title       = '  <a href="'.BASE_ADMIN_URL.'/news/edit?news_id='.$new['news_id'].'" >'. Core_Common::SubFullStrings($new['title'], 0, 40).'</a> ';
                $checkbox_delete = '<div class="checkbox-custom checkbox-primary"><input type="checkbox" id="inputUnchecked'.$key.'" data-action="check-delete" value="'.$new['news_id'].'"/><label for="inputUnchecked'.$key.'"></label></div>';
                // add data response
                $data[]= array('checkbox_delete'=>$checkbox_delete,'id'=>$new['news_id'],'title'=>$title,'image_tag'=>$new['image_tag'],
                    'hot_checkbox'=>$new['hot_checkbox'],'active_checkbox'=>$new['active_checkbox'], 'actions'=>$actions,'order'=>$order
                );
            }
            $result = array('draw'=>$draw, 'recordsFiltered'=>$news['total'],'recordsTotal'=>$news['total'],'data'=>$data);
        }
        else
            $result = array('draw'=>$draw, 'recordsFiltered'=>0,'recordsTotal'=>0,'data'=>array());

        echo Zend_Json::encode($result);
        exit();

    }

    public function editAction()
    {
        $iNewsId    = $this->_getParam('news_id',0);
        $news       = News::getInstance()->getNewsByID($iNewsId);
        if(empty($news))
        {
            $this->_redirect(BASE_ADMIN_URL.'/news');
            exit();
        }
        $news      = Core_Common::newsProcess($news);
        if($this->_request->isPost())
        {
            $arrError   = array();
            // process params
            $news['title']  = trim($this->_getParam('title',''));
            $news['content']  = trim($this->_getParam('content',''));
            $news['source']  = trim($this->_getParam('source',''));
            $news['sort_order']  = $this->_getParam('sort_order',0);
            $news['image_name']  = $this->_getParam('image_name','');
            $news['image_url']  = $this->_getParam('image_url','');
            $news['ishot']  = $this->_getParam('ishot',1);
            $news['active']  = $this->_getParam('active',1);

            //validate data
            if(empty($news['title']))
                $arrError []= array('field'=>'title','message'=>'This field is required');

            if(empty($news['content']))
                $arrError []= array('field'=>'content','message'=>'This field is required');


            if(empty($news['image_url']))
                $arrError []= array('field'=>'image_url','message'=>'This field is required');

            if(!empty($arrError))
                $this->view->arrError   = $arrError;
            else{
                if(News::getInstance()->updateNews($iNewsId, $news['title'],$news['content'],$news['image_url'],0,$news['source'],$news['sort_order'],$news['ishot'],$news['active']))
                {
                    ActionLog::getInstance()->insert($this->arrLogin['id'],ActionLog::$update,ActionLog::$news,$this->arrLogin['accountID'],$this->arrLogin['nickName'],'" '.$news['title'].' " news');
                    $this->_redirect(BASE_ADMIN_URL.'/news');
                    exit();
                }
                else
                    exit();
            }
        }
        $this->view->news   = $news;
    }
}

