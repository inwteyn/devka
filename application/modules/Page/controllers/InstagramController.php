<?php

class Page_InstagramController extends Core_Controller_Action_Standard
{
    public function moreAction()
    {
        $this->_helper->layout->disableLayout();
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $option = $settings->getSetting('page.count.item.on.page');
        if(Engine_Api::_()->core()->hasSubject('page')){
            $subject_id = Engine_Api::_()->core()->getSubject('page')->page_id;
            $this->view->page_id = $subject_id;
        }
            if($this->_getParam('page')){
                $page = $this->_getParam('page')+1;
                $option = $option*$page;
            }else{
                $page = 2;
                $option = $option*$page;
            }
                $table = Engine_Api::_()->getDbTable('instagrams', 'page');
                $select = $table->select()
                                ->where('page_id = ?',$_REQUEST['page_id']);
                $paginator = Zend_Paginator::factory($select);
                $paginator->setItemCountPerPage($option);
                $this->view->optopn = $option;
                $this->view->page = $page;
                $this->view->paginator = $paginator->setCurrentPageNumber( 1 );
    }
    public function indexAction()
    {

        $this->_helper->layout->disableLayout();


        $settings = Engine_Api::_()->getApi('settings', 'core');
        $instagram_option = $settings->getSetting('page.instagram.option' );

        $instagram = new Experts_Service_Instagram_InstagramException($instagram_option);
        $option = $settings->getSetting('page.count.item.on.page');
        if ($this->_getParam('tag') && $this->_getParam('tag') != '') {
            $tag = $this->_getParam('tag');
        if($this->_getParam('page')){
                $page = $this->_getParam('page')+1;
                $option = $option*$page;
            }else{
                $page = 1;
            }
            $media = $instagram->getTagMedia($tag,$option);
            if(count($media->data)>0){
                $paginator = Zend_Paginator::factory((array)$media->data);
                $paginator->setItemCountPerPage($option);
                $this->view->tag = $this->_getParam('tag');
                $this->view->page = $page;
                $this->view->optopn = $option;
                $this->view->paginator = $paginator->setCurrentPageNumber( $page );
            }else{
                $user = $instagram->searchUser($tag);
                $media = $instagram->getUserMedia($user->data[0]->id,$option);
                $paginator = Zend_Paginator::factory((array)$media->data);
                $paginator->setItemCountPerPage($option);
                $this->view->tag = $this->_getParam('tag');
                $this->view->page = $page;
                $this->view->optopn = $option;
                $this->view->paginator = $paginator->setCurrentPageNumber( $page );
            }

        }

    }
    public function saveAction(){
        $array_data = json_decode($_REQUEST['json']);
        $table = Engine_Api::_()->getDbTable('instagrams', 'page');
        if(isset($array_data)){
            $db = $table->getAdapter();
            $db->beginTransaction();
            try {
                foreach($array_data as $item){
                        $page = $table->createRow();
                        $page->user_id = $item->user_id;
                        $page->href = $item->href;
                        $page->user_name = $item->username;
                        $page->user_img = $item->profile_picture;
                        $page->count_comment = $item->comments;
                        $page->count_like = $item->likes;
                        $page->link = $item->link;
                        $page->page_id = $_REQUEST['page_id'];
                        $page->description = $item->caption;
                        $page->save();
                }
            $db->commit();
            } catch( Exception $e ) {
                $db->rollBack();
                throw $e;
            }
        }
        $select = $table->select()->where('page_id = ?',$_REQUEST['page_id']);
        $media = $table->fetchAll($select);
        $this->view->tag = $media;
    }
    public function deleteAction(){
        $id = $_REQUEST['id'];
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        if(isset($id)){
            try
            {
                $record = Engine_Api::_()->getItem('page_instagram', $id);
                $record->delete();
                $db->commit();
            }
            catch( Exception $e )
            {
                $db->rollBack();
                throw $e;
            }
        }
        $table = Engine_Api::_()->getDbTable('instagrams', 'page');
        $select = $table->select()->where('page_id = ?',$_REQUEST['page_id']);
        $media = $table->fetchAll($select);
        $this->view->page_id = $_REQUEST['page_id'];
        $this->view->tag = $media;
    }
    public function editAction(){
        $table = Engine_Api::_()->getDbTable('instagrams', 'page');
        $select = $table->select()->where('page_id = ?',$_REQUEST['page_id']);
        $media = $table->fetchAll($select);
        $this->view->page_id = $_REQUEST['page_id'];
        $this->view->tag = $media;
    }
}