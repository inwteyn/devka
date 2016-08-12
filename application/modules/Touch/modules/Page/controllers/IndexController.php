<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Page_IndexController extends Touch_Controller_Action_Standard
{
	public function init()
  {
  	
  }

  public function getNavigation()
  {
    $navigation = new Zend_Navigation();
    $navigation->addPages(array(
      array(
        'label' => "Pages",
        'route' => 'page_browse',
        'action' => 'index'
      )));

    $viewer = Engine_Api::_()->user()->getViewer();

    if ($viewer->getIdentity()) {
      $navigation->addPage(array(
          'label' => 'My Pages',
          'route'=> 'page_manage',
          'action' => 'manage'
        ));

    }
    if ($viewer->getIdentity()) {
      $navigation->addPage(array(
          'label' => 'Create Page',
          'route'=> 'page_create',
          'action' => 'create'
        ));

    }

    return $navigation;
  }

  public function indexAction()
  {
		$page_num = $this->_getParam('page', 1);
  	$table = Engine_Api::_()->getDbTable('pages', 'page');
    $settings = Engine_Api::_()->getApi('settings', 'core');

    $this->view->formFilter = $formFilter = new Touch_Form_Search();
    $ipp = $settings->getSetting('page.browse_count', 10);

    $this->view->params = $params = array();

    $this->view->user = $user = (int)$this->_getParam('user');
    $this->view->search = $search = $this->_getParam('search');

    $formFilter->search->setValue($search);
    $formFilter->setAction($this->view->url(array(
      'route' => 'page_browse',
    )) . '?user=' . $user );

    if ($user){
      $params['where'] = 'user_id='.$user;
      $this->view->userObj = Engine_Api::_()->user()->getUser($user);
    }
    if ($search){
      $params['search'] = 1;
      $params['keyword'] = $search;
    }

    $this->view->page_num = $page_num;

    $select = $table->getSelect($params);
    $select->order('featured DESC');

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $this->view->count = $paginator->getTotalItemCount();
    $paginator->setItemCountPerPage($ipp);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

		$page_ids = array();
		foreach ($paginator as $page){
			$page_ids[] = $page->getIdentity();
		}

		$this->view->page_tags = Engine_Api::_()->page()->getPageTags($page_ids);
		$this->view->page_likes = Engine_Api::_()->like()->getLikesCount('page', $page_ids);

		$this->view->navigation = $navigation = $this->getNavigation();
  }

	public function viewAction()
	{
    $content_type = $this->_getParam('content');
    $content_id = (int)$this->_getParam('content_id');
    $subject = null;
    $page = $this->_getParam('page', $this->_getParam('page_id'));

  		if ($page == null){
  			$this->_redirectCustom(array('route' => 'page_browse'));
    		return ;
  		}

  		$pageTable = Engine_Api::_()->getDbTable('pages', 'page');


  	  $id = $this->_getParam('id', $this->_getParam('page_id'));
  	  if( null !== $id )
  	  {
  		$select = $pageTable->select()->where('url = ?', $page);
  		$subject = $pageObject = $pageTable->fetchRow($select);
  		if( $subject && $subject->getIdentity() )
  		{
  		  $subject->setContentInfo($content_type, $content_id);
  		  Engine_Api::_()->core()->setSubject($subject);
  		}
  	  }
if($subject)
    if ($content_type == 'pagealbum')
    {
      $this->_redirect($this->view->url(array(
        'action' => 'view',
        'album_id' => $content_id
      ), 'page_album'), array('prependBase' => false));
    }
    elseif ($content_type == 'pagealbumphoto')
    {
      $this->_redirect($this->view->url(array(
        'action' => 'view-photo',
        'photo_id' => $content_id
      ), 'page_album'), array('prependBase' => false));
    }
    elseif ($content_type == 'blog'){
      $this->_redirect($this->view->url(array(
        'action' => 'view',
        'blog_id' => $content_id
      ), 'page_blog'), array('prependBase' => false));
    }
    elseif ($content_type == 'playlist'){
      $this->_redirect($this->view->url(array(
        'action' => 'view',
        'page_id' => $subject->getIdentity(),
        'playlist_id' => $content_id
      ), 'page_music'), array('prependBase' => false));
    }
    elseif ($content_type == 'video'){
      $this->_redirect($this->view->url(array(
        'action' => 'view',
        'page_id' => $subject->getIdentity(),
        'video_id' => $content_id
      ), 'page_video'), array('prependBase' => false));
    }
    elseif ($content_type == 'page_event')
    {
      $this->_redirect($this->view->url(array(
        'action' => 'view',
        'event_id' => $content_id
      ), 'page_event'), array('prependBase' => false));
    }
    elseif ($content_type == 'review'){
      $this->_redirect($this->view->url(array(
        'action' => 'view',
        'review_id' => $content_id
      ), 'page_review'), array('prependBase' => false));
    }



		if ($pageObject == null){
			$this->_redirectCustom(array('route' => 'page_browse'));
  		return ;
		}

//		$this->_helper->requireSubject('page');
		$viewer = Engine_Api::_()->user()->getViewer();

		if( !$this->_helper->requireSubject()->isValid() ) return;
    if( !$this->_helper->requireAuth()->setAuthParams($pageObject, $viewer, 'view')->isValid() ) return;

		$pageObject->viewPage();
		$pageObject->description = stripslashes($pageObject->description);


    $content = Engine_Content::getInstance();
		$table = Engine_Api::_()->getDbtable('pages', 'touch');
		$content->setStorage($table);
		$this->_helper->content->setContent($content);

    // Render
    $this->_helper->content
        ->setNoRender()
        ->setEnabled()
        ;


		if (null !== $pageObject){

			return;
		}

		throw new Zend_Controller_Exception(sprintf('Page %s does not exist', $page), 404);
	}

  public function manageAction()
  {
  	if ( !$this->_helper->requireUser->isValid() ) return ;

  	$this->view->navigation = $navigation = $this->getNavigation();

    $this->view->formFilter = $formFilter = new Touch_Form_Search();

    $this->view->search = $search = $this->_getParam('search');
    $formFilter->search->setValue($search);

    $viewer = $this->_helper->api()->user()->getViewer();
    $table = $this->_helper->api()->getDbtable('pages', 'page');

    $select = $table->select();
    $select->where('user_id = ?', $viewer->getIdentity());
    $this->view->owner = $owner = Engine_Api::_()->user()->getViewer();

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $ipp = $settings->getSetting('page.browse_count', 10);

    $paginator->setItemCountPerPage($ipp);
    $paginator->setCurrentPageNumber($this->_getParam('page'));
  }

  public function createAction()
  {
    if ($this->is_iPhoneUploading()){
      if (!isset($_FILES['picup-image-upload'])){
        return ;
      }
      $file = $_FILES['picup-image-upload'];
      $file = $this->fileUpload($file, $this->_getParam('owner_id'));
      $this->view->photo_name = (isset($file['name'])) ? $file['name'] : '';
      $this->view->photo_id = $file->file_id;
      return;
    } else {
      if( !$this->_helper->requireAuth()->setAuthParams('page', null, 'create')->isValid() ) return;
      $this->_createDefaultContent();

      /**
       * @var $table Page_Model_DbTable_Pages
       * @var $page Page_Model_Page
       */
      $table = $this->_helper->api()->getDbtable('pages', 'page');
      $page = $table->createRow();

      $page_id = $table->lastInsertId();

      $page->page_id = $page_id;
      $page->setIdentity($page_id);
      $this->view->navigation = $navigation = $this->getNavigation();

      $aliasedFields = $page->fields()->getFieldsObjectsByAlias();

      $this->view->topLevelId = $topLevelId = 0;
      $this->view->topLevelValue = $topLevelValue = null;


      if( isset($aliasedFields['profile_type']) ) {
        $aliasedFieldValue = $aliasedFields['profile_type']->getValue($page);
        $topLevelId = $aliasedFields['profile_type']->field_id;
        $topLevelValue = ( is_object($aliasedFieldValue) ? $aliasedFieldValue->value : null );
        if( !$topLevelId || !$topLevelValue ) {
          $topLevelValue = null;
        }
        $this->view->topLevelId = $topLevelId;
        $this->view->topLevelValue = $topLevelValue;
      }

      $parent_type = $this->getRequest()->getParam('parent_type', 'user');
      $subject_id = $this->getRequest()->getParam('subject_id', Engine_Api::_()->user()->getViewer()->getIdentity());

      $this->view->form = $form = new Touch_Form_Page_Create(array('item' => $page,'topLevelId' => $topLevelId,'topLevelValue' => $topLevelValue,
      'parent_type' => 'user', 'parent_id' => $subject_id));

      if( !$this->getRequest()->isPost() ){
        return;
      }

       $values = $this->getRequest()->getPost();
       $extra = $values['extra'];

       if (!$form->isValid($values)){
         $form->populate($values);
         return ;
       }

      if ($table->checkUrl($extra['url'])){
        $form->populate($values);
        $form->addError(Zend_Registry::get('Zend_Translate')->_('This URL is already taken by other page.'));
        return ;
      }

       $extra['url'] = strtolower(trim($extra['url']));
      $extra['url'] = preg_replace('/[^a-z0-9-]/', '-', $extra['url']);
      $extra['url'] = preg_replace('/-+/', "-", $extra['url']);

      $db = Engine_Api::_()->getDbtable('pages', 'page')->getAdapter();
      $db->beginTransaction();

      try
      {
        $form->saveValues();
        $viewer = $this->_helper->api()->user()->getViewer();

        $extra['user_id'] = $viewer->getIdentity();
        $extra['parent_type'] = $this->getRequest()->getParam('parent_type', 'user');
        $extra['parent_id'] =  $this->getRequest()->getParam('subject_id', $viewer->getIdentity());

        $raw_tags = preg_split('/[,]+/', $extra['tags']);
        $tags = array();
        foreach ($raw_tags as $tag){
          $tag = trim(Engine_String::strip_tags($tag));
          if ($tag == ""){
            continue ;
          }
          $tags[] = $tag;
        }
        $page->tags()->addTagMaps($viewer, $tags);
        unset($extra['tags']);

        $settings = Engine_Api::_()->getApi('settings', 'core');

  //			$extra['description'] = utf8_encode($extra['description']);
  //			$extra['title'] = utf8_encode($extra['title']);
        $temp_photo_id = null;
        if ( !empty($extra['photo_id'])){
          $temp_photo_id = $extra['photo_id'];
          unset($extra['photo_id']);
        }

        $page->setFromArray($extra);
        $page->displayname = $page->title;
        $page->name = $page->url;

        $page->keywords = implode(",", $tags);
        $page->approved = $settings->getSetting('page.approval', 1);

        $page->save();

        $page->membership()->addMember($viewer)->setUserApproved($viewer)->setResourceApproved($viewer);
        $page->getTeamList()->add($viewer);

        if ( !is_null( $temp_photo_id ) ){
          if ( null != ($photo = Engine_Api::_()->storage()->get($temp_photo_id)) )
          {
            $page->setPhoto($photo->storage_path);
          }
        }elseif( !$_FILES['photo']['error'] ) {
          $page->setPhoto($form->getSubForm('extra')->photo);
        }

        $page->createContent();

        $availableLabels = array(
          'everyone' => 'Everyone',
          'registered' => 'Registered Members',
          'likes' => 'Likes, Admins and Owner',
          'team' => 'Admins and Owner Only'
        );

        if ( $settings->getSetting('page.package.enabled') )
        {
          /**
           * @var $package Page_Model_Package
           */
          $package = Engine_Api::_()->getItemTable('page_package')->getDefaultPackage();
          $page->package_id = $package->getIdentity();
          $page->featured = $package->featured;
          $page->sponsored = $package->sponsored;
          $page->approved = $package->autoapprove;
          $page->enabled = $package->enabled;

          $view_options = array_intersect_key($availableLabels, array_flip($package->auth_view));
          $comment_options = array_intersect_key($availableLabels, array_flip($package->auth_comment));
          $posting_options = array_intersect_key($availableLabels, array_flip($package->auth_posting));

        } else {

          /**
           * @var $authTb Authorization_Model_DbTable_Permissions
           */
          $authTb = Engine_Api::_()->authorization()->getAdapter('levels');
          $page->approved = (int) $authTb->getAllowed('page', $viewer, 'auto_approve');
          $page->featured = (int) $authTb->getAllowed('page', $viewer, 'featured');
          $page->sponsored = (int) $authTb->getAllowed('page', $viewer, 'sponsored');
          $page->enabled = 1;

        $view_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('page', $viewer, 'auth_view');
        $view_options = array_intersect_key($availableLabels, array_flip($view_options));

        $comment_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('page', $viewer, 'auth_comment');
        $comment_options = array_intersect_key($availableLabels, array_flip($comment_options));

        $posting_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('page', $viewer, 'auth_posting');
        $posting_options = array_intersect_key($availableLabels, array_flip($posting_options));
        }

        if ( $page->save() )
        {
          $values = array('auth_view' => key($view_options), 'auth_comment' => key($comment_options), 'auth_posting' => key($posting_options));
          $page->setPrivacy($values);

          $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
          $action = $activityApi->addActivity($viewer, $page, 'page_create', null, array('is_mobile' => true));

          if ($action) {
          $activityApi->attachActivity($action, $page);
          }
        }

        $db->commit();
      }
      catch( Engine_Image_Exception $e )
      {
        $db->rollBack();
        $form->addError(Zend_Registry::get('Zend_Translate')->_('The image you selected was too large.'));
      }
      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }
      return $this->_forward('success', 'utility', 'touch', array(
        'messages' =>array($this->view->translate("TOUCH_Page created successfully") ),
        'parentRedirect' => $this->view->url(array('action' => 'edit', 'page_id' => $page->page_id), 'page_team', true),
      ));
    }
  }

  public function validateAction()
  {
    if( !$this->_helper->requireUser()->isValid() || !$this->_helper->requireAuth()->setAuthParams('page', null, 'create')->checkRequire() ) {
      return;
    }

    $url = $this->_getParam('url');

    $url = strtolower(trim($url));
    $url = preg_replace('/[^a-z0-9-]/', '-', $url);
    $url = preg_replace('/-+/', "-", $url);

    $table = Engine_Api::_()->getDbTable('pages', 'page');

    if ($table->checkUrl($url)){
      $this->view->success = 0;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_("Page with this url is already exists.");
    }else{
      $this->view->success = 1;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_("This url is free.");
    }

    return;
}

  public function fileUpload($file, $user_id)
  {
    $user = Engine_Api::_()->getItem('user', $user_id);
    if (!$user){
      return ;
    }
    try {
      $params = array(
        'parent_type' => 'temporary',
        'parent_id' => 0,
        'user_id' => $user->getIdentity()
      );
      return Engine_Api::_()->storage()->create($file, $params);

    } catch (Exception $e){
      return ;
    }

  }
  private function _createDefaultContent()
  {
    $pageTable = Engine_Api::_()->getDbTable('pages', 'page');
    $page = "default";

    $pageObject = $pageTable->fetchRow($pageTable->select()->where('name = ?', $page)->orWhere('page_id = ?', $page));
		$contentTable = Engine_Api::_()->getDbtable('content', 'page');

    $contentDefault = $contentTable->fetchAll($contentTable->select()->where('page_id=?', $pageObject->getIdentity()));

    if(count($contentDefault) == 0)
    {
      $pageTable->createContentFirstTime($pageObject->getIdentity());
    }
  }


}