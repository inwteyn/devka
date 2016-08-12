<?php

/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Article
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
class Article_IndexController extends Touch_Controller_Action_Standard
{
  protected $_navigation;
  protected $_version;
  public function init()
  {
    $this->view->version = $this->_version = Engine_Api::_()->getDbtable('modules', 'core')->getModule('article')->version;
    $this->view->modulename = $this->_version = Engine_Api::_()->getDbtable('modules', 'core')->getModule('article')->name;
    if( !$this->_helper->requireAuth()->setAuthParams('article', null, 'view')->isValid() ) return;

    if( !Engine_Api::_()->core()->hasSubject() )
    {
      if( 0 !== ($article_id = (int) $this->_getParam('article_id')) &&
          null !== ($article = Engine_Api::_()->getItem('article', $article_id)) )
      {
        Engine_Api::_()->core()->setSubject($article);
      }
    }
    
    $this->_helper->requireUser->addActionRequires(array(
      'create',
      'delete',
      'edit',
      'manage',
      'success',
      'publish'
    ));

    $this->_helper->requireSubject->setActionRequireTypes(array(
      'delete' => 'article',
      'edit' => 'article',
      'success' => 'article',
      'publish' => 'article',
      'view' => 'article',
    ));
  }

  public function indexAction()
  {
    return $this->_forward('success', 'utility', 'touch', array(
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('Browse Articles')),
      'parentRedirect' => $this->view->url(array('controller'=>'index','action'=>'browse'), 'article_browse', true),
    ));
  }
  
  // NONE USER SPECIFIC METHODS
  public function browseAction()
  {
    $this->publicViewPermissionRequires();
    
    $viewer = Engine_Api::_()->user()->getViewer();
 
    $this->view->navigation =     $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'touch')
      ->getNavigation('article_main');
    $this->view->can_create = $this->_helper->requireAuth()->setAuthParams('article', null, 'create')->checkRequire();

    $this->view->form = $form = new Article_Form_Search();

    if( !$viewer->getIdentity() )
    {
      $form->removeElement('show');
    }

    $values = array();
    // Populate form data
    if( $form->isValid($this->_getAllParams()) )
    {
      $values = $form->getValues();
    }   
    
    $values = Engine_Api::_()->getApi('filter','radcodes')->removeKeyEmptyValues($values);
    

    if( $viewer->getIdentity() && @$values['show'] == 1 )
    {   	
      $values['users'] = array();
      foreach( $viewer->membership()->getMembersInfo(true) as $memberinfo )
      {
        $values['users'][] = $memberinfo->user_id;
      }
    }

    // check to see if request is for specific user's articles
    $user_id = $this->_getParam('user');
    if ($user_id) $values['user_id'] = $user_id;
   
    $this->view->assign($values);
    


    $view = $this->view;
    $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
    
    $values['limit'] = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('article.page', 10);
    $values['pre_order'] = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('article.sorting', 1);
    
    $this->view->paginator = $paginator = Engine_Api::_()->article()->getPublishedArticlesPaginator($values);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
  }

  public function manageAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;
 
    $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->can_create = $this->_helper->requireAuth()->setAuthParams('article', null, 'create')->checkRequire();
    $this->view->allowed_upload = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'article', 'photo');

    $this->view->navigation =     $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'touch')
      ->getNavigation('article_main');
    $this->view->form = $form = new Article_Form_Search();
    $form->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array(),'article_manage',true));
    
    $form->removeElement('show');

    $values = array();
    // Populate form data
    if( $form->isValid($this->_getAllParams()) )
    {
      $values = $form->getValues();
    }

    $values = Engine_Api::_()->getApi('filter','radcodes')->removeKeyEmptyValues($values);
    $this->view->formValues = $values;
    
        
    //$customFieldValues = $form->getSubForm('custom')->getValues();
    $values['user_id'] = $viewer->getIdentity();
    $this->view->assign($values);
    
    // items needed to show what is being filtered in browse page
    if( !empty($values['category']) )
    {
      $this->view->categoryObject = Engine_Api::_()->article()->getCategory($values['category']);
    }
    if (!empty($values['tag']))
    {
      $this->view->tagObject = Engine_Api::_()->getItem('core_tag', $values['tag']);
    }
    
    $view = $this->view;
    $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
       
    $values['limit'] = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('article.page', 10);

    // Get paginator
    $this->view->paginator = $paginator = Engine_Api::_()->article()->getArticlesPaginator($values);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));    

    // maximum allowed articles
    $this->view->quota = $quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'article', 'max');
    $this->view->current_count = $paginator->getTotalItemCount();
  }

  public function viewAction()
  {
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->article = $article = Engine_Api::_()->core()->getSubject('article');

    // require log in --- and -- not logged in => show log in screen
    if ( !Engine_Api::_()->getApi('settings', 'core')->getSetting('article.public', 1) && !$this->_helper->requireUser()->isValid() ) { 
      return;
    }
    
    // logged in && no view permission => show no permission
    if ( $this->_helper->requireUser()->checkRequire() && !$this->_helper->requireAuth()->setAuthParams($article, null, 'view')->isValid()) {
      return;
    }
    else if (!$this->_helper->requireUser()->checkRequire()) {
      if (!$this->_helper->requireAuth()->setAuthParams($article, null, 'view')->checkRequire()) {
        return $this->_forward('requireuser', 'error', 'core');
      }
    }    

    
    $this->view->owner = $owner = Engine_Api::_()->user()->getUser($article->owner_id);
    
    $this->view->canEdit = $this->_helper->requireAuth()->setAuthParams($article, null, 'edit')->checkRequire();
    $this->view->canUpload = $this->_helper->requireAuth()->setAuthParams($article, null, 'photo')->checkRequire();
    $this->view->canDelete = $this->_helper->requireAuth()->setAuthParams($article, null, 'delete')->checkRequire();
    $this->view->canPublish = $article->isOwner($viewer) && !$article->isPublished();
    

    $archiveList = Engine_Api::_()->article()->getArchiveList(array('user_id'=>$article->owner_id,'published'=>1));
    
    $article->view_count++;
    $article->save();
    
    $this->view->article = $article;
    if ($article->photo_id)
    {
      $this->view->main_photo = $article->getPhoto($article->photo_id);
    }
    // get tags
    $this->view->articleTags = $article->tags()->getTagMaps();
    $this->view->userTags = $article->tags()->getTagsByTagger($article->getOwner());
    
    $view = $this->view;
    $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
    $this->view->fieldStructure = $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($article);
    
    // album material
    $this->view->album = $album = $article->getSingletonAlbum();
    $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    $paginator->setItemCountPerPage(Engine_Api::_()->getApi('settings', 'core')->getSetting('article.gallery', 4));
    
    if($article->category_id !=0) $this->view->category = Engine_Api::_()->article()->getCategory($article->category_id);
    $this->view->userCategories = Engine_Api::_()->article()->getUserCategories($this->view->article->owner_id);
        
  }

  public function getForm($options = null)
  {
    $form = new Article_Form_Create($options);
    if(!$options){
      $form = new Article_Form_Create();
    }
    $user_level = Engine_Api::_()->user()->getViewer()->level_id;
    $allowed_html = Engine_Api::_()->authorization()->getPermission($user_level, 'article', 'auth_html');
    $form->addElement('Textarea', 'ta', array(
      'label' => 'Content',
      'required' => true,
      'allowEmpty' => false,
      'filters' => array(
        new Engine_Filter_Censor(),
        new Engine_Filter_Html(array('AllowedTags'=>$allowed_html))
      ),
      'style' => 'height:100px;'
    ));

    $ta = $form->getElement('ta');
    $form->removeElement('ta');
    $elems = $form->getElements();
    $ta->setName("body");
    $elems['body'] = $ta;
    $form->removeElement('submit');
    $form->removeElement('cancel');
    $form->removeElement('file');
    $form->clearElements();
    $form->setElements($elems);
    return $form;
  }

  public function createAction()
  {
    if( !$this->_helper->requireAuth()->setAuthParams('article', null, 'create')->isValid()) return;
    $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->navigation =     $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'touch')
      ->getNavigation('article_main');
    $this->view->form = $form = $this->getForm();

    $form->removeElement('photo');

    // set up data needed to check quota
    $values['user_id'] = $viewer->getIdentity();
    $paginator = $this->_helper->api()->getApi('core', 'article')->getArticlesPaginator($values);


    $this->view->quota = $quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'article', 'max');
    $this->view->current_count = $paginator->getTotalItemCount();


    // If not post or form not valid, return
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )
    {
      $table = Engine_Api::_()->getItemTable('article');
      $db = $table->getAdapter();
      $db->beginTransaction();

      try
      {
      	$featured = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'article', 'featured') ? 1 : 0;
        $sponsored = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'article', 'sponsored') ? 1 : 0;
      	
        // Create article
        $values = array_merge($form->getValues(), array(
          'owner_type' => $viewer->getType(),
          'owner_id' => $viewer->getIdentity(),
          'featured' => $featured,
          'sponsored' => $sponsored,
        ));

        $article = $table->createRow();
        $article->setFromArray($values);
        $article->save();

        // Set photo
        if( !empty($values['photo']) ) {
          $article->setPhoto($form->photo);
        }

        // Add tags
        $tags = preg_split('/[,]+/', $values['tags']);
        $tags = array_filter(array_map("trim", $tags));
        $article->tags()->addTagMaps($viewer, $tags);


        $customfieldform = $form->getSubForm('customField');
        $customfieldform->setItem($article);
        $customfieldform->saveValues();

        // CREATE AUTH STUFF HERE
        $auth = Engine_Api::_()->authorization()->context;  
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
  
        $auth_keys = array(
         'view' => 'everyone',
         'comment' => 'registered',
        );
        
        foreach ($auth_keys as $auth_key => $auth_default)
        {
          $auth_value = isset($values['auth_'.$auth_key]) ? $values['auth_'.$auth_key] : $auth_default;
          $authMax = array_search($auth_value, $roles);
          
          foreach( $roles as $i => $role )
          {
            $auth->setAllowed($article, $role, $auth_key, ($i <= $authMax));
          }
        }

        
        // Add activity only if article is published
        if ($article->isPublished()) {
          $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $article, 'article_new');
          if($action!=null){
            Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $article);
          }
        }

        
        
        // Commit
        $db->commit();
        

        // Redirect
//        $allowed_upload = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'article', 'photo');

//        if( $allowed_upload )
//        {
//          return $this->_forward('success', 'utility', 'touch', array(
//            'messages' => array(Zend_Registry::get('Zend_Translate')->_('TOUCH_ARTICLE_FORM_CREATE_SUCCESS')),
//            'parentRedirect' => $this->view->url(array('controller'=>'photo','action'=>'upload','subject'=>$article->getGuid(), 'created'=>true), 'article_extended', true),
//          ));
//        }
//        else
        {
          return $this->_forward('success', 'utility', 'touch', array(
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('TOUCH_ARTICLE_FORM_CREATE_SUCCESS')),
            'parentRedirect' => $this->view->url(array('controller' => 'index', 'action' => 'manage'), 'article_manage', true),
          ));
        }
        
      }

      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }
    }
  }

  public function editAction()
  {

    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->article = $article = Engine_Api::_()->core()->getSubject('article');
    
    if( !$this->_helper->requireAuth()->setAuthParams($article, null, 'edit')->isValid())
    {
      return $this->_forward('requireauth', 'error', 'core');
    }
    // Get navigation
    $navigation = $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'touch')
      ->getNavigation('article_main');

    $this->view->navigation = $navigation;

    $this->view->form = $form =  $this->getForm(array(
      'item' => $article
    ));
    // only for create
    $form->removeElement('photo');

    $form->populate($article->toArray());

    $auth = Engine_Api::_()->authorization()->context;
    $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
    $auth_keys = array(
     'view' => 'everyone',
     'comment' => 'registered',
    );

    // Save article entry
    if( !$this->getRequest()->isPost() )
    {
      // prepare tags
      $articleTags = $article->tags()->getTagMaps();

      $tagString = '';
      foreach( $articleTags as $tagmap )
      {
        if( $tagString !== '' ) $tagString .= ', ';
        $tagString .= $tagmap->getTag()->getTitle();
      }

      $this->view->tagNamePrepared = $tagString;
      $form->tags->setValue($tagString);

      foreach ($auth_keys as $auth_key => $auth_default)
      {
        $auth_field = 'auth_'.$auth_key;

        foreach( $roles as $i => $role )
        {
          if (isset($form->$auth_field->options[$role]) && 1 === $auth->isAllowed($article, $role, $auth_key))
          {
            $form->$auth_field->setValue($role);
          }
        }
      }

      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      return;
    }

    // Process

    // handle save for tags
    $values = $form->getValues();
    $tags = preg_split('/[,]+/', $values['tags']);
    $tags = array_filter(array_map("trim", $tags));

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try
    {
      $article->setFromArray($values);
      $article->modified_date = date('Y-m-d H:i:s');

      $article->tags()->setTagMaps($viewer, $tags);
      $article->save();

      // Save custom fields
      $customfieldform = $form->getSubForm('customField');
      $customfieldform->setItem($article);
      $customfieldform->saveValues();

      // CREATE AUTH STUFF HERE
      $values = $form->getValues();

      // CREATE AUTH STUFF HERE
      foreach ($auth_keys as $auth_key => $auth_default)
      {
        $auth_value = isset($values['auth_'.$auth_key]) ? $values['auth_'.$auth_key] : $auth_default;
        $authMax = array_search($auth_value, $roles);

        foreach( $roles as $i => $role )
        {
          $auth->setAllowed($article, $role, $auth_key, ($i <= $authMax));
        }
      }

      // Add activity only if article is published
      $action = Engine_Api::_()->getDbtable('actions', 'activity')->getActionsByObject($article);
      if (count($action->toArray())<=0 && $article->isPublished()){
      	if( $viewer->getIdentity() != $article->owner_id)
      	{
      		$owner = Engine_Api::_()->user()->getUser($article->owner_id);
      	}
      	else {
      		$owner = $viewer;
      	}

        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $article, 'article_new');
        if($action!=null){
          Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $article);
        }
      }

      // Rebuild privacy
      $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
      foreach( $actionTable->getActionsByObject($article) as $action ) {
        $actionTable->resetActivityBindings($action);
      }
      $db->commit();


      $savedChangesNotice = Zend_Registry::get('Zend_Translate')->_("Your changes were saved.");
      $form->addNotice($savedChangesNotice);
      $customfieldform->removeElement('submit');
    }
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    return $this->_forward('success', 'utility', 'touch', array(
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('TOUCH_ARTICLE_FORM_EDIT_SUCCESS')),
      'parentRedirect' => $this->view->url(array('controller' => 'index', 'action' => 'manage'), 'article_manage', true),
    ));
  }
  
  public function deleteAction()
  {
    // Check permissions
    $viewer = $this->_helper->api()->user()->getViewer();
    $this->view->article = $article = Engine_Api::_()->getItem('article', $this->_getParam('article_id'));

    $this->view->form = new Touch_Form_Article_Delete();

    if( !$this->_helper->requireUser()->isValid() ) return;
    if( !$this->_helper->requireAuth()->setAuthParams($article, $viewer, 'delete')->isValid() ) return;

    // Check post/form
    if( !$this->getRequest()->isPost() ) {
      return;
    }

    $table = $article->getTable();
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try {
      $article->delete();
      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('TOUCH_ARTICLE_FORM_DELETE_SUCCESS');

    return $this->_forward('success', 'utility', 'touch', array(
      'smoothboxClose' => true,
      'parentRefresh' => true,
      'messages' =>array($this->view->message),
      'parentRedirect' => $this->view->url(array('controller' => 'index', 'action' => 'manage'), 'article_manage', true)
    ));
  }
  
  public function publishAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
  	$this->view->article = $article = Engine_Api::_()->core()->getSubject('article');
  	
  	// only owner can publish
    if( $viewer->getIdentity() != $article->owner_id)
    {
      return $this->_forward('requireauth', 'error', 'core');
    }
  	
    
    if ($article->isPublished())
    {
      return $this->_helper->redirector->gotoRoute(array(), 'article_manage', true);
    }
    
    
    $table = $article->getTable();
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $article->published = 1;
      $article->save();
      
      // Add activity only if article is published
      if ($article->isPublished()) 
      {
        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $article, 'article_new');
        if($action!=null){
          Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $article);
        }
      }
      
      $db->commit();
      
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    return $this->_helper->redirector->gotoRoute(array(), 'article_manage', true);
  }
  
  public function getNavigation($active = false)
  {
    if( is_null($this->_navigation) )
    {
      $navigation = $this->_navigation = new Zend_Navigation();

      if( Engine_Api::_()->user()->getViewer()->getIdentity() )
      {
        $navigation->addPage(array(
          'label' => Zend_Registry::get('Zend_Translate')->_('Browse Articles'),
          'route' => 'article_browse',
          'module' => 'article',
          'controller' => 'index',
          'action' => 'browse'
        ));

        $navigation->addPage(array(
          'label' => Zend_Registry::get('Zend_Translate')->_('My Articles'),
          'route' => 'article_manage',
          'module' => 'article',
          'controller' => 'index',
          'action' => 'manage',
          'active' => $active
        ));
        if( $this->_helper->requireAuth()->setAuthParams('article', null, 'create')->checkRequire()){
          $navigation->addPage(array(
            'label' => Zend_Registry::get('Zend_Translate')->_('Post New Article'),
            'route' => 'article_create',
            'module' => 'article',
            'controller' => 'index',
            'action' => 'create'
          ));
        }
      }
    }
    return $this->_navigation;
  }

  protected function publicViewPermissionRequires()
  {
    // NOTE: boolean return is not really needed - since it auto redirect to login/no permission screen
    
    // require log in --- and -- not logged in => show log in screen
    if ( !Engine_Api::_()->getApi('settings', 'core')->getSetting('article.public', 1) && !$this->_helper->requireUser()->isValid() )
    { 
      return false;
    }
    
    // logged in && no view permission => show no permission
    if ( $this->_helper->requireUser()->checkRequire() && !$this->_helper->requireAuth()->setAuthParams('article', null, 'view')->isValid())
    {
      return false;
    }
    
    return true;
  }  
}

