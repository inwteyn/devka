<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    PageVideo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 2011-12-26 17:46 taalay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    PageVideo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagevideo_IndexController extends Core_Controller_Action_Standard
{
  public function init()
  {
    if ($this->_getParam('action') == 'validation') {
      return ;
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
      $this->view->isAllowedView = $this->getPageApi()->isAllowedView($pageObject);
      $this->view->isTeamMember = $pageObject->isTeamMember();
      
      if (!$this->view->isAllowedView) {
        $this->view->isAllowedPost = false;
        $this->view->isAllowedComment = false;
        return ;
      }
      
      $this->view->isAllowedPost = $this->getApi()->isAllowedPost($pageObject);
      $this->view->isAllowedComment = $this->getPageApi()->isAllowedComment($pageObject);
    }
    
    if ($video_id != null) {
      if( !Engine_Api::_()->core()->hasSubject() ) {
        if( $video && $video->getIdentity() ) {
          Engine_Api::_()->core()->setSubject($video);
        }
      }
    }
    
    $this->view->params = array('page_id' => $page_id, 'ipp' => $settings->getSetting('pagevideo.page', 9), 'p' => $p);
  }
  
  public function indexAction()
  {
    if (!$this->view->isAllowedView) {
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('You can not view this.');
      $this->view->html = $this->view->render('error.tpl');
      return ;
    }
    
    $themes = Zend_Registry::get('Themes');
    $theme_name = 'default';

    if (is_array($themes)) {
      foreach ($themes as $key => $value) {
        $theme_name = $key;
      }
    }

    if ($theme_name == 'midnight') {
      $this->view->theme_class = 'dark';
    } else {
      $this->view->theme_class = 'light';
    }
    
    $table = $this->getTable();
    $this->view->params['status'] = 1;
    $data = $table->getVideos($this->view->params, true);
    $this->view->videos = $data['paginator'];
    $this->view->files = $data['files'];
    $this->view->count = $data['paginator']->getTotalItemCount();
    
    $this->view->html = $this->view->render('index.tpl');
  }
  
  public function manageAction()
  {
    if (!$this->view->isAllowedView) {
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('You can not view this.');
      $this->view->html = $this->view->render('error.tpl');
      return ;
    }
    
    $table = $this->getTable();
    $this->view->params['user_id'] = Engine_Api::_()->user()->getViewer()->getIdentity();
    $data = $table->getVideos($this->view->params, true);
    
    $this->view->videos = $data['paginator'];
    $this->view->files = $data['files'];
    
    $this->view->html = $this->view->render('manage.tpl');
  }
  
  public function viewAction()
  {
    if (!$this->view->isAllowedView) {
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('You can not view this.');
      $this->view->html = $this->view->render('error.tpl');
      return ;
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
    if ( $video->type != 3 ) {
      $video->view_count++;
      $video->save();
      $embedded = $video->getRichContent(true);
    }
    
    if( $video->type == 3 && $video->status != 0 ) {
      $video->view_count++;
      $video->save();

      if( !empty($video->file_id) ) {
        $storage_file = Engine_Api::_()->getItem('storage_file', $video->file_id);
        if( $storage_file ) {
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

    if( $viewer->getIdentity() && $this->view->isAllowedComment){
      $this->view->form = $form = new Core_Form_Comment_Create();
      $form->addElement('Hidden', 'form_id', array('value' => 'video-comment-form'));
      $form->populate(array(
        'identity' => $video->getIdentity(),
        'type' => $video->getType(),
      ));
    }

		$this->view->likeHtml = $this->view->render('comment/list.tpl');
		$this->view->likeUrl = $this->view->url(array('action' => 'like'), 'like_comment');
		$this->view->unlikeUrl = $this->view->url(array('action' => 'unlike'), 'like_comment');
		$this->view->hintUrl = $this->view->url(array('action' => 'hint'), 'like_comment');
		$this->view->showLikesUrl = $this->view->url(array('action' => 'list'), 'like_comment');
		$this->view->postCommentUrl = $this->view->url(array('action' => 'create'), 'like_comment');
    if($this->_getParam('format')=='json') {
      $this->view->html = $this->view->render('index/view.tpl');
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
    // Upload video
    if( isset($_GET['ul']) || isset($_FILES['Filedata']) ) {
      return $this->_forward('upload-video', null, null, array('format' => 'json'));
    }
    
    if (!$this->view->isAllowedPost) {
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('You can not post videos.');
      $this->view->html = $this->view->render('error.tpl');
      return ;
    }
    
    $values = array();
    $viewer = $this->_helper->api()->user()->getViewer();
    $values['user_id'] = $viewer->getIdentity();
  
    // Create form
    $this->view->form = $form = new Pagevideo_Form_Video();

    if ($this->_getParam('video_type', false)) $form->getElement('video_type')->setValue( $this->_getParam('video_type') );
   
    if ( !$this->getRequest()->isPost() ) {
      return;
    }

    if ( !$form->isValid($this->_getAllParams()) ) {
      $values = $form->getValues('video_url');
      return ;
    }

    // Process
    $values = $form->getValues();
    $values['user_id'] = $viewer->getIdentity();

    $insert_action = false;

    $db = Engine_Api::_()->getDbtable('pagevideos', 'pagevideo')->getAdapter();
    $db->beginTransaction();

    try
    {
      // Create video
      $table = $this->_helper->api()->getDbtable('pagevideos', 'pagevideo');
      if($values['video_type'] == 3){
        $video = Engine_Api::_()->getItem('pagevideo', $this->_getParam('video_id'));
      }
      else {
        $video = $table->createRow();
      }

      $this->view->break = 1;
      
      $video->setFromArray($this->getValues());
      $video->page_id = (int)$this->_getParam('page_id');
      $video->user_id = $viewer->getIdentity();
      $video->save();

      // Now try to create thumbnail
      $thumbnail = $this->handleThumbnail($video->type, $video->code);
      $ext = ltrim(strrchr($thumbnail, '.'), '.');
      $thumbnail_parsed = @parse_url($thumbnail);

      if (@GetImageSize($thumbnail)){
        $valid_thumb = true;
      } else {
        $valid_thumb = false;
      }

      if( $valid_thumb && $thumbnail && $ext && $thumbnail_parsed && in_array($ext, array('jpg', 'jpeg', 'gif', 'png')) ){
        $tmp_file = APPLICATION_PATH . '/temporary/link_'.md5($thumbnail).'.'.$ext;
        $thumb_file = APPLICATION_PATH . '/temporary/link_thumb_'.md5($thumbnail).'.'.$ext;
				$mini_file = APPLICATION_PATH . '/temporary/link_mini_'.md5($thumbnail).'.'.$ext;
        $icon_file = APPLICATION_PATH . '/temporary/link_thumb_icon_'.md5($thumbnail).'.'.$ext;
        $normal_file = APPLICATION_PATH.'/temporary/link_thumb_normal_'.md5($thumbnail).'.'.$ext;

        $src_fh = fopen($thumbnail, 'r');
        $tmp_fh = fopen($tmp_file, 'w');
        stream_copy_to_stream($src_fh, $tmp_fh, 1024 * 1024 * 2);

        $image = Engine_Image::factory();
        $image->open($tmp_file)
          ->resize(240, 180)
          ->write($thumb_file)
          ->destroy();

				$image = Engine_Image::factory();
        $image->open($tmp_file)
          ->resize(34, 34)
          ->write($mini_file)
          ->destroy();

        $image = Engine_Image::factory();
        $image->open($tmp_file)
          ->resize(48, 48)
          ->write($icon_file)
          ->destroy();

        $image = Engine_Image::factory();
        $image->open($tmp_file)
          ->resize(120, 240)
          ->write($normal_file)
          ->destroy();

        try {
          $thumbFileRow = Engine_Api::_()->storage()->create($thumb_file, array(
            'parent_type' => $video->getType(),
            'parent_id' => $video->getIdentity()
          ));

					$thumbMiniFileRow = Engine_Api::_()->storage()->create($mini_file, array(
            'parent_type' => $video->getType(),
            'parent_id' => $video->getIdentity()
          ));

          $thumbIconFileRow = Engine_Api::_()->storage()->create($icon_file, array(
            'parent_type' => $video->getType(),
            'parent_id' => $video->getIdentity()
          ));

          $thumbNormaloFile = Engine_Api::_()->storage()->create($normal_file, array(
            'parent_type' => $video->getType(),
            'parent_id' => $video->getIdentity()
          ));

					$thumbFileRow->bridge($thumbMiniFileRow, 'thumb.mini');
          $thumbFileRow->bridge($thumbIconFileRow, 'thumb.icon');
          $thumbFileRow->bridge($thumbNormaloFile, 'thumb.norm');

          // Remove temp file
          @unlink($thumb_file);
					@unlink($mini_file);
          @unlink($tmp_file);
          @unlink($icon_file);
          @unlink($normal_file);
        }
        catch (Exception $e)
        {
					throw $e;
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
      $tags = preg_split('/[,]+/', $values['video_tags']);
      $video->tags()->setTagMaps($viewer, $tags);

			$search_api = Engine_Api::_()->getDbTable('search', 'page');
			$search_api->saveData($video);

      Engine_Api::_()->page()->sendNotification($video, 'post_pagevideo');

      $db->commit();
      $this->view->eval = 'page_video.inc_count(1); ';
    }

    catch( Exception $e )
    {
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Video was not created.');
      $this->view->html = $this->view->render('error.tpl');
      
      $db->rollBack();
      throw $e;
    }
    
    $this->view->eval .= 'page_video.manage();';
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Video was successfully created.');
    $this->view->html = $this->view->render('success.tpl');
    $this->view->video_id = $video->getIdentity();
    $this->view->video = $table->getVideoFileInfo($video);

    $db->beginTransaction();
    try {
      if($insert_action){
        $owner = $video->getOwner();
        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $video->getPage(), 'pagevideo_new');
        if ($action != null) {
          Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $video);
        }
      }
      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }
  }
  
  public function uploadVideoAction()
  {
    if( !$this->_helper->requireUser()->checkRequire() ) {
      $this->view->status = false;
      $this->view->error  = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
      return;
    }

    if( !$this->getRequest()->isPost() ) {
      $this->view->status = false;
      $this->view->error  = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    $values = $this->getRequest()->getPost();

    if( empty($values['Filename']) ) {
      $this->view->status = false;
      $this->view->error  = Zend_Registry::get('Zend_Translate')->_('No file');
      return;
    }

    if( !isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name']) ) {
      $this->view->status = false;
      $this->view->error  = Zend_Registry::get('Zend_Translate')->_('Invalid Upload').print_r($_FILES, true);
      return;
    }

    $values['page_id'] = $this->_request->getParam('page_id', 0);

    $db = Engine_Api::_()->getDbtable('pagevideos', 'pagevideo')->getAdapter();
    $db->beginTransaction();

    try
    {
      $viewer = Engine_Api::_()->user()->getViewer();
      $values['user_id'] = $viewer->getIdentity();

      $params = array(
        'owner_type' => 'user',
        'owner_id' => $viewer->getIdentity(),
    );
      
      $video = Engine_Api::_()->pagevideo()->createVideo($params, $_FILES['Filedata'], $values);

      $this->view->status   = true;
      $this->view->name     = $_FILES['Filedata']['name'];
      $this->view->code     = $video->code;
      $this->view->video_id = $video->getIdentity();

      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = $e->getMessage();//Zend_Registry::get('Zend_Translate')->_('An error occurred.');

      // throw $e;
      return;
    }
  }
  
  public function editAction()
  {
    $video = Engine_Api::_()->getItem('pagevideo', $this->_getParam('video_id'));
    $this->view->video = $video->toArray();
    $videoTags = $video->tags()->getTagMaps();

    $tagString = '';
    foreach( $videoTags as $tagmap ){
      if( $tagString !== '' ) $tagString .= ', ';
      $tagString .= $tagmap->getTag()->getTitle();
    }

    $this->view->tags = $tagString;
  }
  
  public function saveAction()
  {
    if (!$this->view->isAllowedView) {
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('You can not view this.');
      $this->view->html = $this->view->render('error.tpl');
      return ;
    }
    
    $viewer = Engine_Api::_()->user()->getViewer();

    $video = Engine_Api::_()->getItem('pagevideo', $this->_getParam('video_id'));

    $form = new Pagevideo_Form_Edit();
    
    if( !$this->getRequest()->isPost() ) {
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      $this->view->html = $this->view->render('error.tpl');
      return;
    }

    if( !$form->isValid($this->_getAllParams())){
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      $this->view->html = $this->view->render('error.tpl');
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('pagevideos', 'pagevideo')->getAdapter();
    $db->beginTransaction();
    $this->view->eval = "page_video.manage();";
    
    try {
      $values = $form->getValues();
      $video->title = $this->_getParam('video_title');
      $video->description = $this->_getParam('video_description');
      $video->save();

			$search_api = Engine_Api::_()->getDbTable('search', 'page');
			$search_api->saveData($video);

      // Add tags
      $tags = preg_split('/[,]+/', $values['video_tags']);
      $video->tags()->setTagMaps($viewer, $tags);

      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    $db->beginTransaction();
    try {
      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Changes were not applied.');
      $this->view->html = $this->view->render('error.tpl');
    }
    
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Changes successfully applied.');
    $this->view->html = $this->view->render('success.tpl');
  }

  public function deleteAction()
  {
    if (!$this->view->isAllowedView) {
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('You can not view this.');
      $this->view->html = $this->view->render('error.tpl');
      return ;
    }
    
    $viewer = Engine_Api::_()->user()->getViewer();
    $video = Engine_Api::_()->getItem('pagevideo', $this->getRequest()->getParam('video_id'));

    $this->view->eval = 'page_video.manage();';
    
    if( !$video ) {
      $this->view->message = 'Video was not deleted.';
      $this->view->html = $this->view->render('error.tpl');
      return;
    }

    $db = $this->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      Engine_Api::_()->getApi('core', 'pagevideo')->deleteVideo($video);
      $db->commit();
      $this->view->eval .= 'page_video.inc_count(-1);';
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Video was deleted.');
    $this->view->html = $this->view->render('success.tpl');
  }
  
  public function extractCode($url, $type)
  {
    switch ($type) {
      //youtube
      case "1":
        // change new youtube URL to old one
        $url= preg_replace("/#!/", "?", $url);

        // get v variable from the url
        $arr = array();
        $arr = @parse_url($url);
        $code = "code";
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
  /*public function checkYouTube($code)
  {
    if (!$data = @file_get_contents("http://gdata.youtube.com/feeds/api/videos/".$code)) return false;
    if ($data == "Video not found") return false;
    return $data;
  }*/
  public function checkYouTube($code){
    $key = Engine_Api::_()->getApi('settings', 'core')->getSetting('video.youtube.apikey', 'AIzaSyDpUPT_nafV_MFSAlc-8AH4e1Gy578iK0M');
    if (!$data = @file_get_contents('https://www.googleapis.com/youtube/v3/videos?part=id&id=' . $code . '&key=' . $key)) return false;

    $data = Zend_Json::decode($data);
    if (empty($data['items'])) return false;
    return true;
  }

  // Vimeo Functions
  public function checkVimeo($code)
  {
    //http://www.vimeo.com/api/docs/simple-api
    //http://vimeo.com/api/v2/video
    $data = @simplexml_load_file("http://vimeo.com/api/v2/video/".$code.".xml");
    $id = count($data->video->id);
    if ($id == 0) return false;
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
        $data = simplexml_load_file("http://vimeo.com/api/v2/video/".$code.".xml");
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
        $api_key = Engine_Api::_()->getApi('settings', 'core')->getSetting('video.youtube.apikey', 'AIzaSyDpUPT_nafV_MFSAlc-8AH4e1Gy578iK0M');
        $url = "https://www.googleapis.com/youtube/v3/videos?id=$code&key=$api_key&part=snippet,contentDetails";
        $data = @file_get_contents($url);
        $data = json_decode($data);
        $youtube_video = $data ->items[0];

        $information = array();
        $information['title'] = $youtube_video->snippet->title;
        $information['description'] = $youtube_video->snippet->description;
        $start = new DateTime('@0'); // Unix epoch
        $start->add(new DateInterval($youtube_video->contentDetails->duration));
        $duration = $start->format('H')*60*60 + $start->format('i')*60 + $start->format('s');
        $information['duration'] = $duration;

        return $information;
      //vimeo
      case "2":
        //thumbnail_medium
        $data = simplexml_load_file("http://vimeo.com/api/v2/video/".$code.".xml");
        $thumbnail = $data->video->thumbnail_medium;
        $information = array();
        $information['title'] =  $data->video->title;
        $information['description'] = $data->video->description;
        $information['duration'] = $data->video->duration;

        return $information;
    }
  }

  public function validationAction()
  {
    $this->_helper->layout->disableLayout();
    
    $video_type = $this->_getParam('type');
    $code = $this->_getParam('code');
    $ajax = $this->_getParam('ajax', false);
    $valid = false;

    // check which API should be used
    if ($video_type=="youtube"){
      $valid = $this->checkYouTube($code);
    }
    if ($video_type=="vimeo"){
      $valid = $this->checkVimeo($code);
    }

    $this->view->code = $code;
    $this->view->ajax = $ajax;
    $this->view->valid = $valid;
  }
  
  protected function getApi()
  {
    return Engine_Api::_()->getApi('core', 'pagevideo'); 
  }

  protected function getPageApi()
  {
    return Engine_Api::_()->getApi('core', 'page');
  }

  protected function getTable() 
  {
    return Engine_Api::_()->getDbTable('pagevideos', 'pagevideo');
  }
}