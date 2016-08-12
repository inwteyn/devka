<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagevideo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 2010-09-20 17:46 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Pagevideo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagevideo_IndexController extends Touch_Controller_Action_Standard
{
    protected $_subject;

    public function init()
    {
      $page_id = (int)$this->_getParam('page_id');
      if ($page_id){
        $subject = Engine_Api::_()->getDbTable('pages', 'page')->findRow($page_id);
      }

      if ($subject && !Engine_Api::_()->getApi('core', 'page')->isAllowedView($subject)){
        $subject = null;
      }
        if ($this->_getParam('action') == 'validation') {
            return;
        }

        $settings = Engine_Api::_()->getApi('settings', 'core');
        
        $p = $this->_getParam('p', 1);
        $page_id = $this->_getParam('page_id');
        $video_id = $this->_getParam('video_id');

        if ($video_id) {
            $this->view->video = $video = Engine_Api::_()->getItem('pagevideo', $video_id);
        }

        if ($page_id) {
            $this->view->pageObject = $pageObject = Engine_Api::_()->getItem('page', $page_id);
            $this->view->isAllowedView = $this->getApi()->isAllowedView($pageObject);
            $this->view->isTeamMember = $pageObject->isTeamMember();

            if (!$this->view->isAllowedView) {
                $this->view->isAllowedPost = false;
                $this->view->isAllowedComment = false;
                return;
            }

            $this->view->isAllowedPost = $this->getApi()->isAllowedPost($pageObject);
            $this->view->isAllowedComment = $this->getApi()->isAllowedComment($pageObject);
        }

        if ($video_id != null) {
            if (!Engine_Api::_()->core()->hasSubject()) {
                if ($video && $video->getIdentity()) {
                    Engine_Api::_()->core()->setSubject($video);
                }
            }
        }

        $this->view->params = array('page_id' => $page_id, 'ipp' => $settings->getSetting('pagevideo.page', 9), 'p' => $p);
        $this->view->params = array('page_id' => $page_id, 'ipp' => $settings->getSetting('pagevideo.page', 9), 'p' => $p);
        $this->_subject = $this->view->subject = $subject;
    }

    public function indexAction()
    {
        if (!$this->view->isAllowedView) {
            $this->view->message = Zend_Registry::get('Zend_Translate')->_('You can not view this.');
            $this->view->html = $this->view->render('error.tpl');
            return;
        }
        $this->view->paginator = $this->getPaginator();
    }

//    public function manageAction()
//    {
//        if (!$this->view->isAllowedView) {
//            $this->view->message = Zend_Registry::get('Zend_Translate')->_('You can not view this.');
//            $this->view->html = $this->view->render('error.tpl');
//            return;
//        }
//
//        $table = $this->getTable();
//        $this->view->params['user_id'] = Engine_Api::_()->user()->getViewer()->getIdentity();
//        $data = $table->getVideos($this->view->params, true);
//
//        $this->view->videos = $data['paginator'];
//        $this->view->files = $data['files'];
//
//        $this->view->html = $this->view->render('manage.tpl');
//    }

    public function manageAction()
    {
        if( !$this->_helper->requireUser()->isValid() ) return;
        $this->view->paginator = $this->getPaginator(Engine_Api::_()->user()->getViewer()->getIdentity());
    }

    public function viewAction()
    {
        if (!$this->view->isAllowedView) {
            $this->view->message = Zend_Registry::get('Zend_Translate')->_('You can not view this.');
            $this->view->html = $this->view->render('error.tpl');
            return;
        }

        $path = Zend_Controller_Front::getInstance()->getControllerDirectory('like');
        $path = dirname($path) . '/views/scripts';

        $this->view->addScriptPath($path);
        $video = $this->view->video;
        $viewer = $this->_helper->api()->user()->getViewer();

        $this->view->videoTags = $video->tags()->getTagMaps();

        $this->view->can_edit = false;
        $this->view->can_delete = false;
        if ($viewer->getIdentity()) {
            $this->view->can_edit = $this->view->isAllowedPost && ($video->user_id == $viewer->getIdentity() || $this->view->isTeamMember);
            $this->view->can_delete = $this->view->isAllowedPost && ($video->user_id == $viewer->getIdentity() || $this->view->isTeamMember);
        }

        // increment count
        $embedded = "";
        if ($video->type != 3) {
            $video->view_count++;
            $video->save();
            $embedded = $video->getRichContent(true);
        }

        if ($video->type == 3 && $video->status != 0) {
            $video->view_count++;
            $video->save();

            if (!empty($video->file_id)) {
                $storage_file = Engine_Api::_()->getItem('storage_file', $video->file_id);
                if ($storage_file) {
                    $this->view->video_location = $storage_file->map();
                }
            }
        }

        $this->view->comment_form_id = "video-comment-form";

        $this->view->viewer_id = $viewer->getIdentity();
        $this->view->video = $video;
        $this->view->videoEmbedded = $embedded;
        $this->view->subject = $video;
        $this->view->page = $page = $this->_getParam('page');
        $this->view->viewAllLikes = $this->_getParam('viewAllLikes', false);
        $this->view->likes = $video->likes()->getLikePaginator();
        $this->view->comments = $this->getApi()->getComments($page, $video);

        if ($viewer->getIdentity() && $this->view->isAllowedComment) {
            $this->view->form = $form = new Core_Form_Comment_Create();
            $form->addElement('Hidden', 'form_id', array('value' => 'video-comment-form'));
            $form->populate(array(
                                 'identity' => $video->getIdentity(),
                                 'type' => $video->getType(),
                            ));
        }


    }

    public function uploadAction()
    {
        $this->view->name = $this->_getParam('name');
    }

    public function getValues()
    {
        return array(
            'page_id' => (int)$this->_getParam('page_id'),
            'video_id' => (int)$this->_getParam('video_id'),
            'title' => $this->_getParam('video_title'),
            'description' => $this->_getParam('video_description'),
            'type' => (int)$this->_getParam('video_type'),
            'code' => $this->_getParam('video_code')
        );
    }

    public function createAction()
    {
        if (!$this->_helper->requireUser->isValid()) return;
        //      if( !$this->_helper->requireAuth()->setAuthParams('video', null, 'create')->isValid()) return;

        $viewer = $this->_helper->api()->user()->getViewer();
        $values['user_id'] = $viewer->getIdentity();

        // Create form
        $this->view->form = $form = new Touch_Form_Pagevideo_Video();

        if ($this->_getParam('video_type', false))
            $form->getElement('video_type')->setValue($this->_getParam('video_type'));

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->_getAllParams())) {
            $values = $form->getValues('video_url');
            return;
        }

        // Process

        $page_id = (int)$this->_getParam('page_id');

        $values = $form->getValues();

        $values['user_id'] = $viewer->getIdentity();

        $insert_action = false;

        $db = Engine_Api::_()->getDbtable('pagevideos', 'pagevideo')->getAdapter();
        $db->beginTransaction();

        try
        {
            //Create video
            $table = $this->_helper->api()->getDbtable('pagevideos', 'pagevideo');
            if ($values['video_type'] == 3) {

                $params = array(
                    'user_id' => $viewer->getIdentity()
                );

                $video = $this->uploadVideo($_FILES['file'], $params);

                $video->page_id = $this->_getParam('page_id');

                $video->save();

                if (!$video) {
                    $form->file->addError('Invalid Upload');
                    return;
                }

            } else

                $video = $table->createRow();
                $video['user_id'] = $viewer->getIdentity();

                $video->setFromArray($this->getValues());
                $video->save();

            // Now try to create thumbnail
            $thumbnail = $this->handleThumbnail($video->type, $video->code);

            $ext = ltrim(strrchr($thumbnail, '.'), '.');
            $thumbnail_parsed = @parse_url($thumbnail);


            if (@GetImageSize($thumbnail)) {
                $valid_thumb = true;
            } else {
                $valid_thumb = false;
            }

            if ($valid_thumb && $thumbnail && $ext && $thumbnail_parsed && in_array($ext, array('jpg', 'jpeg', 'gif', 'png'))) {
                $tmp_file = APPLICATION_PATH . '/temporary/link_' . md5($thumbnail) . '.' . $ext;
                $thumb_file = APPLICATION_PATH . '/temporary/link_thumb_' . md5($thumbnail) . '.' . $ext;

                $src_fh = fopen($thumbnail, 'r');
                $tmp_fh = fopen($tmp_file, 'w');
                stream_copy_to_stream($src_fh, $tmp_fh, 1024 * 1024 * 2);

                $image = Engine_Image::factory();
                $image->open($tmp_file)
                        ->resize(120, 240)
                        ->write($thumb_file)
                        ->destroy();

                try {
                    $thumbFileRow = Engine_Api::_()->storage()->create($thumb_file, array(
                     'parent_type' => $video->getType(),
                     'parent_id' => $video->getIdentity()
                ));

                    // Remove temp file
                    @unlink($thumb_file);
                    @unlink($tmp_file);

                }
                catch (Exception $e)
                {

                }
                   
                $information = $this->handleInformation($video->type, $video->code);

                $video->duration = $information['duration'];
                if (!$video->description) $video->description = $information['description'];
                $video->photo_id = $thumbFileRow->file_id;
                $video->status = 1;
                $video->save();


                // Insert new action item
                $insert_action = true;
            }

            if ($values['video_ignore'] == true) {
                $video->status = 1;
                $video->save();
                $insert_action = true;
            }

            // Add tags
            $tags = preg_split('/[,]+/', $values['tags']);
            $video->tags()->setTagMaps($viewer, $tags);

            $search_api = Engine_Api::_()->getDbTable('search', 'page');
            $search_api->saveData($video);


            $db->commit();


        }

        catch (Exception $e)
        {
            $db->rollBack();
            throw $e;
        }
        
        $redirect_url = ($video->type == 3)
            ? $this->view->url(array('action' => 'manage', 'page_id' => $this->_getParam('page_id')), 'page_video', true)
            : $this->view->url(array('action' => 'manage', 'page_id' => $this->_getParam('page_id')), 'page_video', true);

        return $this->_forward('success', 'utility', 'touch', array(
          'messages' =>array($this->view->translate("TOUCH_VIDEO_FORM_EDIT_SUCCESS") ),
          'parentRedirect' => $this->view->url(array('action' => 'view', 'page_id' => $page_id), 'page_view', true),
        ));
//
//        $this->_forward('success', 'utility', 'touch', array(
//           'messages' => array(Zend_Registry::get('Zend_Translate')->_('TOUCH_Changes have been saved.')),
//           'parentRedirect' => $this->view->url(array('action' => 'manage', 'album_id' => $this->_getParam('pagealbum_id')), 'page_album', true),
//        ));

    }

    public function uploadVideo($file, $params)
    {
        if (!isset($file) || !is_uploaded_file($file['tmp_name'])) {
            return;
        }
        $illegal_extensions = array('php', 'pl', 'cgi', 'html', 'htm', 'txt');
        if (in_array(pathinfo($file['name'], PATHINFO_EXTENSION), $illegal_extensions)) {
            return;
        }
        $db = Engine_Api::_()->getDbtable('pagevideos', 'pagevideo')->getAdapter();
        
        $db->beginTransaction();
        try
        {
            $viewer = Engine_Api::_()->user()->getViewer();
            $values['user_id'] = $viewer->getIdentity();
            $video = Engine_Api::_()->pagevideo()->createVideo($params, $file, $values);
            $video->title = $file['name'];
            $video->user_id = $viewer->getIdentity();
            $video->save();
            $db->commit();

            return $video;

        }

        catch (Exception $e)
        {
            $db->rollBack();
            //             throw $e;
            return;
        }
    }

    public function editAction()
    {
        if (!$this->_helper->requireUser()->isValid()) return;
        $viewer = Engine_Api::_()->user()->getViewer();

        $video = Engine_Api::_()->getItem('pagevideo', $this->_getParam('video_id'));

        if( !$this->_helper->requireSubject()->isValid() ) return;

        $this->view->video = $video;
        $this->view->form = $form = new Touch_Form_Pagevideo_Edit();
        
        $form->getElement('title')->setValue($video->title);
        $form->getElement('description')->setValue($video->description);

        // prepare tags
        $videoTags = $video->tags()->getTagMaps();

        $tagString = '';
        foreach ($videoTags as $tagmap)
        {
            if ($tagString !== '') $tagString .= ', ';
            $tagString .= $tagmap->getTag()->getTitle();
        }

        $this->view->tagNamePrepared = $tagString;

        $form->tags->setValue($tagString);

        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
            return;
        }

        // Process
        $db = Engine_Api::_()->getDbtable('pagevideos', 'pagevideo')->getAdapter();
        $db->beginTransaction();

        try {

            $values = $form->getValues();
            $video->setFromArray($values);

            // Add tags
            $tags = preg_split('/[,]+/', $tagString);
            $video->tags()->setTagMaps($viewer, $tags);
            
            $video->save();


            $db->commit();

        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }


        $this->_redirectCustom(array('route' => 'page_video', 'action' => 'view',
                                    'page_id' => $video->page_id, 'video_id' => $video->getIdentity()));


    }

    public function deleteAction()
    {
        {

            $viewer = Engine_Api::_()->user()->getViewer();
            $video = Engine_Api::_()->getItem('pagevideo', $this->getRequest()->getParam('video_id'));

            $this->view->form = $form = new Video_Form_Delete();

            if (!$video) {
                $this->view->status = false;
                $this->view->error = Zend_Registry::get('Zend_Translate')->_("Video doesn't exists or not authorized to delete");
                return;
            }

            if (!$this->getRequest()->isPost()) {
                $this->view->status = false;
                $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
                return;
            }

            $db = $this->getTable()->getAdapter();
            $db->beginTransaction();

            try
            {
                Engine_Api::_()->getApi('core', 'pagevideo')->deleteVideo($video);
                $db->commit();
            }

            catch (Exception $e)
            {
                $db->rollBack();
                throw $e;
            }

            $this->view->status = true;
            $this->view->message = Zend_Registry::get('Zend_Translate')->_('Video has been deleted.');


            return $this->_forward('success', 'utility', 'touch', array(
                                                                       'messages' => array($this->view->message),
                'parentRedirect' => $this->view->url(array('action' => 'manage'), 'page_video'),
                                                                  ));

        }
    }

    public function extractCode($url, $type)
    {
        switch ($type) {
            //youtube
            case "1":
                // change new youtube URL to old one
                $url = preg_replace("/#!/", "?", $url);

                // get v variable from the url
                $arr = array();
                $arr = @parse_url($url);
                $code = "video_code";
                $parameters = $arr["query"];
                parse_str($parameters, $data);
                $code = $data['v'];

                return $code;
            //vimeo
            case "2":
                // get the first variable after slash
                $code = @pathinfo($url);
                return $code['basename'];
        }
    }

    // YouTube Functions
    public function checkYouTube($code)
    {
        if (!$data = @file_get_contents("http://gdata.youtube.com/feeds/api/videos/" . $code)) return false;
        if ($data == "Video not found") return false;
        return $data;
    }

    // Vimeo Functions
    public function checkVimeo($code)
    {
        //http://www.vimeo.com/api/docs/simple-api
        //http://vimeo.com/api/v2/video
        $data = @simplexml_load_file("http://vimeo.com/api/v2/video/" . $code . ".xml");
//        $id = count($data->video->id);
//        if ($data == "") return false;
        return true;
    }

    // handles thumbnails
    public function handleThumbnail($type, $code = null)
    {
        switch ($type) {
            //youtube
            case "1":
                // http://img.youtube.com/vi/E98IYokujSY/default.jpg
                return "http://img.youtube.com/vi/$code/0.jpg";
            // vimeo
            case "2":
                // thumbnail_medium
                $data = simplexml_load_file("http://vimeo.com/api/v2/video/" . $code . ".xml");
                $thumbnail = $data->video->thumbnail_large;
                return $thumbnail;
        }
    }

    // retrieves infromation and returns title + desc
    public function handleInformation($type, $code)
    {
        switch ($type) {
            //youtube
            case "1":
                $yt = new Zend_Gdata_YouTube();
                $youtube_video = $yt->getVideoEntry($code);
                $information = array();
                $information['title'] = $youtube_video->getTitle();
                $information['description'] = $youtube_video->getVideoDescription();
                $information['duration'] = $youtube_video->getVideoDuration();

                return $information;
            //vimeo
            case "2":
                //thumbnail_medium
                $data = simplexml_load_file("http://vimeo.com/api/v2/video/" . $code . ".xml");
                $thumbnail = $data->video->thumbnail_medium;
                $information = array();
                $information['title'] = $data->video->title;
                $information['description'] = $data->video->description;
                $information['duration'] = $data->video->duration;

                return $information;
        }
    }

    public function validationAction()
    {
        $this->_helper->layout->disableLayout();
        
        $video_type = $this->_getParam('type');
        $code = $this->_getParam('video_code');
        $ajax = $this->_getParam('ajax', false);
        $valid = false;

        // check which API should be used
        if ($video_type == "youtube") {
            $valid = $this->checkYouTube($code);
        }
        if ($video_type == "vimeo") {
            $valid = $this->checkVimeo($code);
        }

        $this->view->video_code = $code;
        $this->view->ajax = $ajax;
        $this->view->valid = $valid;
    }

    protected function getApi()
    {
        return Engine_Api::_()->getApi('core', 'pagevideo');
    }

    protected function getTable()
    {
        return Engine_Api::_()->getDbTable('pagevideos', 'pagevideo');
    }

  protected function getPaginator($viewer_id = 0, $page = 1)
  {
    $table = $this->getTable();
    $this->view->form_filter = $form = new Touch_Form_Search();
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $this->view->form_value = $this->_getParam('search');
    }
    $select = $table->select()
        ->where('page_id = ?', $this->_subject->getIdentity());

    if ($viewer_id)
    {
      $select->where('user_id = ?', $viewer_id);
    }

    $select->order('modified_date DESC');
    if ($this->_getParam('search', false)) {
      $select->where('title LIKE ? OR description LIKE ?', '%' . $this->_getParam('search') . '%');
    }
    $paginator = Zend_Paginator::factory($select);
    $paginator->setCurrentPageNumber($page);
    $paginator->setItemCountPerPage(5);
    $paginator->setCurrentPageNumber($this->_getParam('page'));
    return $paginator;

  }
}