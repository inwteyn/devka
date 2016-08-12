<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Blog_IndexController extends Touch_Controller_Action_Standard
{
  public function init()
  {
    // only show to member_level if authorized
    if( !$this->_helper->requireAuth()->setAuthParams('blog', null, 'view')->isValid() ) return;
  }

  public function indexAction()
  {
    // Enable content helper?
    //$this->_helper->content->setEnabled();

    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'touch')
      ->getNavigation('blog_main');

    // Prepare data
//    $viewer = $this->_helper->api()->user()->getViewer();
    //if( !$this->_helper->requireAuth()->setAuthParams('blog', null, 'view')->isValid()) return;
    
    $this->view->form = $form = new Touch_Form_Search();

    $form->getElement('search')->setValue($this->_getParam('search'));

    $this->view->canCreate = $this->_helper->requireAuth()->setAuthParams('blog', null, 'create')->checkRequire();

		// Process form
    $form->isValid($this->_getAllParams());
    $values = $form->getValues();
    $this->view->formValues = array_filter($values);
    $values['draft'] = "0";
    $values['visible'] = "1";

    $this->view->assign($values);

    $blogApi = Engine_Api::_()->getApi('core', 'blog');
    $blogsTbl = Engine_Api::_()->getDbTable('blogs', 'blog');
    $blogCatsTbl = Engine_Api::_()->getDbTable('categories', 'blog');

    if (method_exists($blogApi, 'getBlogsPaginator')) {
      $paginator = $blogApi->getBlogsPaginator($values);
    } else {
      $paginator = $blogsTbl->getBlogsPaginator($values);
    }

    $paginator->setItemCountPerPage(5);

    $this->view->paginator = $paginator->setCurrentPageNumber( $this->_getParam('page', 1));

    if (!empty($values['category'])) {
      if (method_exists($blogApi, 'getCategory')) {
        $this->view->categoryObject = $blogApi->getCategory($values['category']);
      } else {
        $this->view->categoryObject = $blogCatsTbl->find($values['category'])->current();
      }
    }
  }
  
  public function viewAction()
  {
    // Check permission
    $viewer = $this->_helper->api()->user()->getViewer();
    $blog = Engine_Api::_()->getItem('blog', $this->_getParam('blog_id'));

		if( $blog ) {
      Engine_Api::_()->core()->setSubject($blog);
    }

    if( !$this->_helper->requireSubject()->isValid() ) return;
  	if( !$this->_helper->requireAuth()->setAuthParams($blog, $viewer, 'view')->isValid()) return;

    $blogApi = Engine_Api::_()->getApi('core', 'blog');
    $blogsTbl = Engine_Api::_()->getDbTable('blogs', 'blog');
    $blogCatsTbl = Engine_Api::_()->getDbTable('categories', 'blog');

    // Prepare data
    if (method_exists($blogApi, 'getArchiveList')) {
      $archiveList = $blogApi->getArchiveList($blog->owner_id);
    } else {
      $archiveList = $blogsTbl->getArchiveList($blog->getOwner());
    }

    $this->view->archive_list = $this->_handleArchiveList($archiveList);
    $this->view->viewer = $viewer;
    $blog->view_count++;
    $blog->save();

    $this->view->blog = $blog;

    $this->view->blogTags = $blog->tags()->getTagMaps();
    $this->view->userTags = $blog->tags()->getTagsByTagger($blog->getOwner());
    //$this->view->blogTags = Engine_Api::_()->blog()->getBlogTags($blog_id);
    //$this->view->userTags = Engine_Api::_()->blog()->getUserTags($blog->owner_id);

    if ($blog->category_id != 0) {
      $this->view->category = (method_exists($blogApi, 'getCategory'))
        ? $blogApi->getCategory($blog->category_id)
        : $blogCatsTbl->find($blog->category_id)->current();
    }

    $this->view->userCategories = (method_exists($blogApi, 'getUserCategories'))
      ? $blogApi->getUserCategories($this->view->blog->owner_id)
      : $blogCatsTbl->getUserCategoriesAssoc($this->view->blog->getOwner());

    // Get styles
    $this->view->owner = $user = $blog->getOwner();
    $table = Engine_Api::_()->getDbtable('styles', 'core');
    $select = $table->select()
      ->where('type = ?', 'user_blog')
      ->where('id = ?', $user->getIdentity())
      ->limit();

    $row = $table->fetchRow($select);

    if( null !== $row && !empty($row->style) )
    {
      $this->view->headStyle()->appendStyle($row->style);
    }

  }

  // USER SPECIFIC METHODS
  public function manageAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;

    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'touch')
      ->getNavigation('blog_main');

    // Prepare data
    $viewer = $this->_helper->api()->user()->getViewer();

    $this->view->form = $form = new Touch_Form_Search();
    $form->getElement('search')->setValue($this->_getParam('search'));

    // Process form
    $form->isValid($this->_getAllParams());
    $values = $form->getValues();
    $values['user_id'] = $viewer->getIdentity();

    $blogApi = Engine_Api::_()->getApi('core', 'blog');
    $blogsTbl = Engine_Api::_()->getDbTable('blogs', 'blog');

    // Get paginator
    $this->view->paginator = $paginator = (method_exists($blogApi, 'getBlogsPaginator'))
      ? $blogApi->getBlogsPaginator($values)
      : $blogsTbl->getBlogsPaginator($values);

    $items_per_page = Engine_Api::_()->getApi('settings', 'core')->blog_page;
    $paginator->setItemCountPerPage(5);
    $this->view->paginator = $paginator->setCurrentPageNumber( $this->_getParam('page', 1) );

    $this->view->canCreate = $this->_helper->requireAuth()->setAuthParams('blog', null, 'create')->checkRequire();

  }

  public function listAction()
  {
    // Preload info
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->owner = $owner = Engine_Api::_()->getItem('user', $this->_getParam('user_id'));

    // Make form
    $this->view->form = $form = new Touch_Form_Search();

    $form->addElement('Select', 'category', array(
      'label' => 'Category',
      'multiOptions' => array(
        '0' => 'All Categories',
      ),
      'style' => 'display:none',
    ));

    $blogApi = Engine_Api::_()->getApi('core', 'blog');
    $blogsTbl = Engine_Api::_()->getDbTable('blogs', 'blog');
    $blogCatsTbl = Engine_Api::_()->getDbTable('categories', 'blog');

    // Populate form
    $this->view->categories = $categories = $blogCatsTbl->fetchAll($blogCatsTbl->select()->order('category_name ASC'));
    foreach( $categories as $category ) {
      $form->category->addMultiOption($category->category_id, $category->category_name);
    }
		
    // Process form
    $form->isValid($this->_getAllParams());
    $values = $form->getValues();
    $values['user_id'] = $owner->getIdentity();
    $values['draft'] = "0";
    $values['visible'] = "1";


    $this->view->assign($values);

    // Get paginator
    $this->view->paginator = $paginator = (method_exists($blogApi, 'getBlogsPaginator'))
      ? $blogApi->getBlogsPaginator($values)
      : $blogsTbl->getBlogsPaginator($values);

    $paginator->setItemCountPerPage(5);
    $this->view->paginator = $paginator->setCurrentPageNumber( $this->_getParam('page', 1) );

    $this->view->userTags = Engine_Api::_()->getDbtable('tags', 'core')->getTagsByTagger('blog', $owner);

    if (method_exists($blogApi, 'getUserCategories')) {
      $this->view->userCategories = $blogApi->getUserCategories($owner->getIdentity());
    } else {
      $this->view->userCategories = $blogCatsTbl->getUserCategoriesAssoc($owner);
    }
  }

  public function deleteAction()
  {
    // Check permissions
    $viewer = $this->_helper->api()->user()->getViewer();
    $this->view->blog = $blog = Engine_Api::_()->getItem('blog', $this->_getParam('blog_id'));

    $this->view->form = new Touch_Form_Blog_Delete();

    if( !$this->_helper->requireUser()->isValid() ) return;
    if( !$this->_helper->requireAuth()->setAuthParams($blog, $viewer, 'delete')->isValid() ) return;
	
    // Check post/form
    if( !$this->getRequest()->isPost() ) {
      return;
    }

    $table = $blog->getTable();
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try {
      $blog->delete();
      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('The blog successfully has been deleted');

    return $this->_forward('success', 'utility', 'touch', array(
      'smoothboxClose' => true,
      'parentRefresh' => true,
      'messages' =>array($this->view->message),
    ));


  }


  public function createAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;
    if( !$this->_helper->requireAuth()->setAuthParams('blog', null, 'create')->isValid()) return;

    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'touch')
      ->getNavigation('blog_main');

    $this->view->page_id = $this->_getParam('page_id');

    // Prepare form
    $this->view->form = $form = new Touch_Form_Blog_Create();

    // If not post or form not valid, return
    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }


    // Process
    $table = Engine_Api::_()->getItemTable('blog');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      // Create blog
      $viewer = Engine_Api::_()->user()->getViewer();
      $values = array_merge($form->getValues(), array(
        'owner_type' => $viewer->getType(),
        'owner_id' => $viewer->getIdentity(),
      ));

      $values['body'] = nl2br(htmlspecialchars($values['body']));

      $blog = $table->createRow();
      $blog->setFromArray($values);
      $blog->save();

      // Auth
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

      if( empty($values['auth_view']) ) {
        $values['auth_view'] = 'everyone';
      }

      if( empty($values['auth_comment']) ) {
        $values['auth_comment'] = 'everyone';
      }

      $viewMax = array_search($values['auth_view'], $roles);
      $commentMax = array_search($values['auth_comment'], $roles);

      foreach( $roles as $i => $role ) {
        $auth->setAllowed($blog, $role, 'view', ($i <= $viewMax));
        $auth->setAllowed($blog, $role, 'comment', ($i <= $commentMax));
      }

      // Add tags
      $tags = preg_split('/[,]+/', $values['tags']);
      $blog->tags()->addTagMaps($viewer, $tags);

      // Add activity only if blog is published
      if( $values['draft'] == 0 ) {
        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $blog, 'blog_new', null, array('is_mobile' => true));

        // make sure action exists before attaching the blog to the activity
        if( $action ) {
          Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $blog);
        }

      }

      // Commit
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    return $this->_forward('success', 'utility', 'touch', array(
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('TOUCH_BLOG_FORM_CREATE_SUCCESS')),
      'parentRedirect' => $this->view->url(array('action' => 'manage')),
    ));

  }



  public function editAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;

    $viewer = $this->_helper->api()->user()->getViewer();
    $blog = Engine_Api::_()->getItem('blog', $this->_getParam('blog_id'));
    if( !Engine_Api::_()->core()->hasSubject('blog') ) {
      Engine_Api::_()->core()->setSubject($blog);
    }

    if( !$this->_helper->requireSubject()->isValid() ) return;
    if( !$this->_helper->requireAuth()->setAuthParams($blog, $viewer, 'edit')->isValid() ) return;

    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'touch')
      ->getNavigation('blog_main');

    // Prepare form
    $this->view->form = $form = new Touch_Form_Blog_Edit();

    // Populate form
    $form->populate($blog->toArray());
    $form->body->setValue(Engine_String::strip_tags($blog->body));

    $tagStr = '';
    foreach( $blog->tags()->getTagMaps() as $tagMap ) {
      $tag = $tagMap->getTag();
      if( !isset($tag->text) ) continue;
      if( '' !== $tagStr ) $tagStr .= ', ';
      $tagStr .= $tag->text;
    }
    $form->populate(array(
      'tags' => $tagStr,
    ));
    $this->view->tagNamePrepared = $tagStr;

    $auth = Engine_Api::_()->authorization()->context;
    $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

    foreach( $roles as $role ) {
      if ($form->auth_view){
        if( $auth->isAllowed($blog, $role, 'view') ) {
         $form->auth_view->setValue($role);
        }
      }

      if ($form->auth_comment){
        if( $auth->isAllowed($blog, $role, 'comment') ) {
          $form->auth_comment->setValue($role);
        }
      }
    }

    // hide status change if it has been already published
    if( $blog->draft == "0" ) {
      $form->removeElement('draft');
    }


    // Check post/form
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }


    // Process
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try
    {
      $values = $form->getValues();
      $values['body'] = nl2br(htmlspecialchars($values['body']));

      $blog->setFromArray($values);
      $blog->modified_date = date('Y-m-d H:i:s');
      $blog->save();

      // Auth
      if( empty($values['auth_view']) ) {
        $values['auth_view'] = 'everyone';
      }

      if( empty($values['auth_comment']) ) {
        $values['auth_comment'] = 'everyone';
      }

      $viewMax = array_search($values['auth_view'], $roles);
      $commentMax = array_search($values['auth_comment'], $roles);

      foreach( $roles as $i => $role ) {
        $auth->setAllowed($blog, $role, 'view', ($i <= $viewMax));
        $auth->setAllowed($blog, $role, 'comment', ($i <= $commentMax));
      }

      // handle tags
      $tags = preg_split('/[,]+/', $values['tags']);
      $blog->tags()->setTagMaps($viewer, $tags);

      // insert new activity if blog is just getting published
      $action = Engine_Api::_()->getDbtable('actions', 'activity')->getActionsByObject($blog);
      if( count($action->toArray()) <= 0 && @$values['draft'] == '0' ) {
        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $blog, 'blog_new', null, array('is_mobile' => true));
          // make sure action exists before attaching the blog to the activity
        if( $action != null ) {
          Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $blog);
        }
      }

      // Rebuild privacy
      $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
      foreach( $actionTable->getActionsByObject($blog) as $action ) {
        $actionTable->resetActivityBindings($action);
      }

      $db->commit();

    }
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    return $this->_forward('success', 'utility', 'touch', array(
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('TOUCH_BLOG_FORM_EDIT_SUCCESS')),
      'parentRedirect' => $this->view->url(array('action' => 'manage')),
    ));

  }


  protected function _handleArchiveList($results)
  {
    $localeObject = Zend_Registry::get('Locale');

    $blog_dates = array();
    foreach ($results as $ts => $result) {
      $blog_dates[] = $ts; //todo check
    }

    // GEN ARCHIVE LIST
    $time = time();
    $archive_list = array();

    foreach( $blog_dates as $blog_date )
    {
      $ltime = localtime($blog_date, TRUE);
      $ltime["tm_mon"] = $ltime["tm_mon"] + 1;
      $ltime["tm_year"] = $ltime["tm_year"] + 1900;

      // LESS THAN A YEAR AGO - MONTHS
      if( $blog_date+31536000>$time )
      {
        $date_start = mktime(0, 0, 0, $ltime["tm_mon"], 1, $ltime["tm_year"]);
        $date_end = mktime(0, 0, 0, $ltime["tm_mon"]+1, 1, $ltime["tm_year"]);
        //$label = date('F Y', $blog_date);
        $type = 'month';

        $dateObject = new Zend_Date($blog_date);
        $format = $localeObject->getTranslation('MMMMd', 'dateitem', $localeObject);
        $label = $dateObject->toString($format, $localeObject);
      }

      // MORE THAN A YEAR AGO - YEARS
      else
      {
        $date_start = mktime(0, 0, 0, 1, 1, $ltime["tm_year"]);
        $date_end = mktime(0, 0, 0, 1, 1, $ltime["tm_year"]+1);
        //$label = date('Y', $blog_date);
        $type = 'year';

        $dateObject = new Zend_Date($blog_date);
        $format = $localeObject->getTranslation('yyyy', 'dateitem', $localeObject);
        if( !$format ) {
          $format = $localeObject->getTranslation('y', 'dateitem', $localeObject);
        }
        $label = $dateObject->toString($format, $localeObject);
      }

      if( !isset($archive_list[$date_start]) )
      {
        $archive_list[$date_start] = array(
          'type' => $type,
          'label' => $label,
          'date_start' => $date_start,
          'date_end' => $date_end,
          'count' => 1
        );
      }
      else
      {
        $archive_list[$date_start]['count']++;
      }
    }

    //krsort($archive_list);
    return $archive_list;
  }



}

