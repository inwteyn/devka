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

class Classified_IndexController extends Touch_Controller_Action_Standard
{
  public function init()
  {
    if (!$this->_helper->requireAuth()->setAuthParams('classified', null, 'view')->isValid() ||
        $this->is_iPhoneUploading()
    ) {
      return;
    }
  }

  // NONE USER SPECIFIC METHODS
  public function indexAction()
  {
    // Check auth
    if (!$this->_helper->requireAuth()->setAuthParams('classified', null, 'view')->isValid()) return;

    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'touch')
      ->getNavigation('classified_main');

    // Prepare form
    $this->view->form = $form = new Touch_Form_Search();

    $this->view->can_create = $this->_helper->requireAuth()->setAuthParams('classified', null, 'create')->checkRequire();

    // Process form
    if ($form->isValid($this->getRequest()->getParams())) {
      $values = $form->getValues();
    } else {
      $values = array();
    }

    $this->view->assign($values);

    // items needed to show what is being filtered in browse page
    if (!empty($values['tag'])) $this->view->tag_text = Engine_Api::_()->getItem('core_tag', $values['tag'])->text;

    $view = $this->view;
    $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');

    $classifiedApi = Engine_Api::_()->getApi('core', 'classified');
    $classifiedsTbl = Engine_Api::_()->getDbTable('classifieds', 'classified');
    
    $select = (method_exists($classifiedApi, 'getClassifiedsSelect'))
      ? $classifiedApi->getClassifiedsSelect($values)
      : $classifiedsTbl->getClassifiedsSelect($values);

    $this->view->search = $search = $this->_getParam('search');

    if (!empty($search)) {
      $select->where('title LIKE ? OR body = ?', '%' . $search . '%');
    }

    $paginator = Zend_Paginator::factory($select);
    $paginator
      ->setCurrentPageNumber($this->_getParam('page', 1))
      ->setItemCountPerPage(50);

    $this->view->paginator = $paginator;

  }


  public function viewAction()
  {
    $viewer = $this->_helper->api()->user()->getViewer();
    $classified = Engine_Api::_()->getItem('classified', $this->_getParam('classified_id'));

    if ($classified) {
      Engine_Api::_()->core()->setSubject($classified);
    }

    // Check auth
    if (!$this->_helper->requireAuth()->setAuthParams($classified, null, 'view')->isValid()) {
      return;
    }

    $this->view->canEdit = $canEdit = $classified->authorization()->isAllowed(null, 'edit');
    $this->view->canDelete = $canDelete = $classified->authorization()->isAllowed(null, 'delete');
    $this->view->canUpload = $canUpload = $classified->authorization()->isAllowed(null, 'photo');

    // Get navigation
    $this->view->gutterNavigation = $gutterNavigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('classified_gutter');

    if ($classified) {
      $this->view->owner = $owner = Engine_Api::_()->getItem('user', $classified->owner_id);
      $this->view->viewer = $viewer;

      if (!$owner->isSelf($viewer)) {
        $classified->view_count++;
        $classified->save();
      }

      $this->view->classified = $classified;
      if ($classified->photo_id) {
        $this->view->main_photo = $classified->getPhoto($classified->photo_id);
      }

      // get tags
      $this->view->classifiedTags = $classified->tags()->getTagMaps();
      $this->view->userTags = $classified->tags()->getTagsByTagger($classified->getOwner());

      // get custom field values
      //$this->view->fieldsByAlias = Engine_Api::_()->fields()->getFieldsValuesByAlias($classified);
      // Load fields view helpers
      $view = $this->view;
      $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
      $this->view->fieldStructure = $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($classified);

      // album material
      $this->view->album = $album = $classified->getSingletonAlbum();

      $this->view->search = $search = $this->_getParam('search');
      $table = Engine_Api::_()->getDbTable('photos', 'classified');
      $tableName = $table->info('name');

      $select = $album->getCollectiblesSelect();

      if (!empty($search)) {
        $select->where("$tableName.title LIKE ? OR $tableName.description LIKE ?", '%' . $search . '%');
      }

      $select->reset(Zend_Db_Select::ORDER);
      $select->order(new Zend_Db_Expr("($tableName.file_id = {$classified->photo_id}) DESC, `photo_id` ASC"));

      $this->view->paginator = $paginator = Zend_Paginator::factory($select);
      $this->view->form = $form = new Touch_Form_Search();
      $form->search->setValue($this->_getParam('search'));

      // Prepare params
      $this->view->page = $page = $this->_getParam('page');

      // Prepare data
      $paginator->setItemCountPerPage(20);
      $paginator->setCurrentPageNumber($page);

      // Do other stuff
      if (!$album->getOwner()->isSelf(Engine_Api::_()->user()->getViewer())) {
        $album->view_count++;
        $album->save();
      }

      if ($canUpload) {

        $this->view->navigation = new Zend_Navigation(array(

                                                           new Zend_Navigation_Page_Uri(array(
                                                                                             'uri' => $this->view->url(),
                                                                                             'label' => $this->view->translate("Photos"),
                                                                                             'active' => true
                                                                                        )),
                                                           new Zend_Navigation_Page_Uri(array(
                                                                                             'uri' => $this->view->url(array('controller' => 'photo', 'action' => 'upload', 'classified_id' => $classified->getIdentity()), 'classified_extended', true),
                                                                                             'label' => $this->view->translate('Add Photos'),
                                                                                             'active' => false,
                                                                                             'full_redirect' => 1
                                                                                        ))
                                                      ));

      }

      $classifiedApi = Engine_Api::_()->getApi('core', 'classified');
      $classifiedCatsTbl = Engine_Api::_()->getDbTable('categories', 'classified');

      if ($classified->category_id) {
        $this->view->category = method_exists($classifiedApi, 'getCategory')
          ? $classifiedApi->getCategory($classified->category_id)
          : $classifiedCatsTbl->find($classified->category_id)->current();
      }
      $this->view->userCategories = $this->getUserCategoriesAssoc($this->view->classified->owner_id);
    }
  }

  // USER SPECIFIC METHODS
  public function manageAction()
  {
    if (!$this->_helper->requireUser()->isValid()) return;

    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'touch')
      ->getNavigation('classified_main');

    $viewer = $this->_helper->api()->user()->getViewer();

    $this->view->can_create = $this->_helper->requireAuth()->setAuthParams('classified', null, 'create')->checkRequire();
    $this->view->allowed_upload = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'classified', 'photo');

    $this->view->form = $form = new Touch_Form_Search();

    // Process form
    if ($form->isValid($this->getRequest()->getParams())) {
      $values = $form->getValues();
    } else {
      $values = array();
    }

    //$customFieldValues = $form->getSubForm('custom')->getValues();
    $values['user_id'] = $viewer->getIdentity();

    $this->view->assign($values);

    $classifiedApi = Engine_Api::_()->getApi('core', 'classified');
    $classifiedsTbl = Engine_Api::_()->getDbTable('classifieds', 'classified');

    $select = (method_exists($classifiedApi, 'getClassifiedsSelect'))
      ? $classifiedApi->getClassifiedsSelect($values)
      : $classifiedsTbl->getClassifiedsSelect($values);

    $this->view->search = $search = $this->_getParam('search');

    if (!empty($search)) {
      $select->where('title LIKE ? OR body = ?', '%' . $search . '%');
    }

    $paginator = Zend_Paginator::factory($select);
    $paginator
      ->setCurrentPageNumber($this->_getParam('page', 1))
      ->setItemCountPerPage(5);

    $this->view->paginator = $paginator;

    $view = $this->view;
    $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');

    // maximum allowed classifieds
    $this->view->quota = $quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'classified', 'max');
    $this->view->current_count = $paginator->getTotalItemCount();
  }

  public function deleteAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $classified = Engine_Api::_()->getItem('classified', $this->getRequest()->getParam('classified_id'));
    if (!$this->_helper->requireAuth()->setAuthParams($classified, null, 'delete')->isValid()) return;

    $this->view->form = $form = new Touch_Form_Classified_Delete();


    if (!$classified) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Classified listing doesn\'t exist or not authorized to delete');

      return $this->_forward('success', 'utility', 'touch', array(
                                                                 'status' => $this->view->status,
                                                                 'messages' => array($this->view->error),
                                                            ));

    }

    if (!$this->getRequest()->isPost()) {
      return;
    }

    $db = $classified->getTable()->getAdapter();
    $db->beginTransaction();

    try
    {
      $classified->delete();
      $db->commit();
    }

    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Your classified listing has been deleted.');

    return $this->_forward('success', 'utility', 'touch', array(
                                                               'messages' => array($this->view->message),
                                                               'parentRedirect' => $this->view->url(array('action' => 'manage'), 'classified_general', true),
                                                          ));


  }

  public function createAction()
  {
    if ($this->is_iPhoneUploading()) {
      if (!isset($_FILES['picup-image-upload'])) {
        return;
      }
      $file = $_FILES['picup-image-upload'];
      $file = $this->fileUpload($file, $this->_getParam('owner_id'));
      $this->view->photo_name = (isset($file['name'])) ? $file['name'] : '';
      $this->view->photo_id = $file->file_id;
      return;
    } else {

      // Check auth
      if (!$this->_helper->requireUser()->isValid()) return;
      if (!$this->_helper->requireAuth()->setAuthParams('classified', null, 'create')->isValid()) return;

      // Get navigation
      $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('classified_main');


      $this->view->form = $form = new Touch_Form_Classified_Create();

      // set up data needed to check quota
      $viewer = Engine_Api::_()->user()->getViewer();
      $values['user_id'] = $viewer->getIdentity();

      $classifiedApi = Engine_Api::_()->getApi('core', 'classified');
      $classifiedsTbl = Engine_Api::_()->getDbTable('classifieds', 'classified');

      $paginator = (method_exists($classifiedApi, 'getClassifiedsPaginator'))
        ? $classifiedApi->getClassifiedsPaginator($values)
        : $classifiedsTbl->getClassifiedsPaginator($values);

      $this->view->quota = $quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'classified', 'max');
      $this->view->current_count = $paginator->getTotalItemCount();

      // If not post or form not valid, return
      if (!$this->getRequest()->isPost()) {
        return;
      }

      if (!$form->isValid($this->getRequest()->getPost())) {
        return;
      }


      // Process
      $table = Engine_Api::_()->getItemTable('classified');
      $db = $table->getAdapter();
      $db->beginTransaction();

      try
      {
        // Create classified
        $values = array_merge($form->getValues(), array(
                                                       'owner_type' => $viewer->getType(),
                                                       'owner_id' => $viewer->getIdentity(),
                                                  ));

        $classified = $table->createRow();
        $classified->setFromArray($values);
        $classified->save();

        if ($values['photo_id']) {
          $photo = Engine_Api::_()->storage()->get($values['photo_id']);
          if ($photo) {
            $classified->setPhoto($photo->storage_path);
          }
        }

        // Set photo
        if (!empty($values['photo'])) {
          $classified->setPhoto($form->photo);
        }

        // Add tags
        $tags = preg_split('/[,]+/', $values['tags']);
        $tags = array_filter(array_map("trim", $tags));
        $classified->tags()->addTagMaps($viewer, $tags);

        // Add fields
        $customfieldform = $form->getSubForm('fields');
        $customfieldform->setItem($classified);
        $customfieldform->saveValues();

        // Set privacy
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

        if (empty($values['auth_view'])) {
          $values['auth_view'] = array("everyone");
        }
        if (empty($values['auth_comment'])) {
          $values['auth_comment'] = array("everyone");
        }

        $viewMax = array_search($values['auth_view'], $roles);
        $commentMax = array_search($values['auth_comment'], $roles);

        foreach ($roles as $i => $role) {
          $auth->setAllowed($classified, $role, 'view', ($i <= $viewMax));
          $auth->setAllowed($classified, $role, 'comment', ($i <= $commentMax));
        }

        // Commit
        $db->commit();
      }

      catch (Exception $e)
      {
        $db->rollBack();
        throw $e;
      }

      $db->beginTransaction();
      try {
        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $classified, 'classified_new', null, array('is_mobile' => true));
        if ($action != null) {
          Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $classified);
        }
        $db->commit();
      }

      catch (Exception $e)
      {
        $db->rollBack();
        throw $e;
      }

      $allowed_upload = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'classified', 'photo');
      $redirect_url = ($allowed_upload)
        ? $this->view->url(array('action' => 'success', 'classified_id' => $classified->classified_id), 'classified_specific', true)
        : $classified->getHref();


      return $this->_forward('success', 'utility', 'touch', array(
                                                                 'messages' => array(Zend_Registry::get('Zend_Translate')->_('TOUCH_CLASSIFIED_FORM_CREATE_SUCCESS')),
                                                                 'parentRedirect' => $redirect_url,
                                                            ));



    }
  }

  public function editAction()
  {
    if (!$this->_helper->requireUser()->isValid()) return;

    $viewer = $this->_helper->api()->user()->getViewer();
    $classified = Engine_Api::_()->getItem('classified', $this->_getParam('classified_id'));
    if (!Engine_Api::_()->core()->hasSubject('classified')) {
      Engine_Api::_()->core()->setSubject($classified);
    }
    $this->view->classified = $classified;

    // Check auth
    if (!$this->_helper->requireSubject()->isValid()) {
      return;
    }
    if (!$this->_helper->requireAuth()->setAuthParams($classified, $viewer, 'edit')->isValid()) {
      return;
    }

    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('classified_main', array(), 'classified_main_manage');


    // Prepare form
    $this->view->form = $form = new Touch_Form_Classified_Edit(array(
                                                                    'item' => $classified
                                                               ));

    $form->removeElement('photo');

    //$customfieldform->getFieldsValuesByAlias($classified);


    // Save classified entry
    $saved = $this->_getParam('saved');
    if (!$this->getRequest()->isPost() || $saved) {

      if ($saved) {
        $url = $this->_helper->url->url(array('user_id' => $viewer->getIdentity(), 'classified_id' => $classified->getIdentity()), 'classified_entry_view');
        $savedChangesNotice = Zend_Registry::get('Zend_Translate')->_("Your changes were saved. Click %s to view your listing.", '<a href="' . $url . '">here</a>');
        $form->addNotice($savedChangesNotice);
      }

      // prepare tags
      $classifiedTags = $classified->tags()->getTagMaps();
      //$form->getSubForm('custom')->saveValues();

      $tagString = '';
      foreach ($classifiedTags as $tagmap)
      {
        if ($tagString !== '') $tagString .= ', ';
        $tagString .= $tagmap->getTag()->getTitle();
      }

      $this->view->tagNamePrepared = $tagString;
      $form->tags->setValue($tagString);

      // etc
      $form->populate($classified->toArray());
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      foreach ($roles as $role)
      {
        if (1 === $auth->isAllowed($classified, $role, 'view')) {
          $form->auth_view->setValue($role);
        }
        if (1 === $auth->isAllowed($classified, $role, 'comment')) {
          $form->auth_comment->setValue($role);
        }
      }

      return;
    }
    if (!$form->isValid($this->getRequest()->getPost())) {
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
      $classified->setFromArray($values);
      $classified->modified_date = date('Y-m-d H:i:s');

      $classified->tags()->setTagMaps($viewer, $tags);
      $classified->save();

      // Save custom fields
      $customfieldform = $form->getSubForm('fields');
      $customfieldform->setItem($classified);
      $customfieldform->saveValues();

      // CREATE AUTH STUFF HERE
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      if (!empty($values['auth_view'])) {
        $auth_view = $values['auth_view'];
      } else {
        $auth_view = "everyone";
      }
      $viewMax = array_search($auth_view, $roles);

      foreach ($roles as $i => $role)
      {
        $auth->setAllowed($classified, $role, 'view', ($i <= $viewMax));
      }

      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      if (!empty($values['auth_comment'])) {
        $auth_comment = $values['auth_comment'];
      } else {
        $auth_comment = "everyone";
      }
      $commentMax = array_search($auth_comment, $roles);

      foreach ($roles as $i => $role)
      {
        $auth->setAllowed($classified, $role, 'comment', ($i <= $commentMax));
      }

      $db->commit();

    }
    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }


    $db->beginTransaction();
    try {
      // Rebuild privacy
      $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
      foreach ($actionTable->getActionsByObject($classified) as $action) {
        $actionTable->resetActivityBindings($action);
      }

      $db->commit();
    }
    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    return $this->_forward('success', 'utility', 'touch', array(
                                                               'messages' => array(Zend_Registry::get('Zend_Translate')->_('TOUCH_CLASSIFIED_FORM_EDIT_SUCCESS')),
                                                               'parentRedirect' => $this->view->url(array('action' => 'manage'), 'classified_general', true),
                                                          ));

  }

  public function successAction()
  {
    if (!$this->_helper->requireUser()->isValid()) return;

    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('classified_main', array(), 'classified_main_manage');


    $viewer = $this->_helper->api()->user()->getViewer();
    $this->view->classified = $classified = Engine_Api::_()->getItem('classified', $this->_getParam('classified_id'));

    if ($viewer->getIdentity() != $classified->owner_id) {
      return $this->_forward('requireauth', 'error', 'core');
    }

  }


  public function closeAction()
  {
    if (!$this->_helper->requireUser()->isValid()) return;

    $viewer = $this->_helper->api()->user()->getViewer();
    $classified = Engine_Api::_()->getItem('classified', $this->_getParam('classified_id'));
    if (!Engine_Api::_()->core()->hasSubject('classified')) {
      Engine_Api::_()->core()->setSubject($classified);
    }
    $this->view->classified = $classified;

    // Check auth
    if (!$this->_helper->requireSubject()->isValid()) {
      return;
    }
    if (!$this->_helper->requireAuth()->setAuthParams($classified, $viewer, 'edit')->isValid()) {
      return;
    }

    // @todo convert this to post only

    $table = $classified->getTable();
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $classified->closed = $this->_getParam('closed');
      $classified->save();

      $db->commit();
    }

    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    $redirect_url = ($this->_getParam('return_url'))
      ? $this->_getParam('return_url')
      : $this->view->url(array('action' => 'manage'), 'classified_general', true);

    $message = ($this->_getParam('closed'))
      ? 'TOUCH_CLASSIFIED_CLOSED'
      : 'TOUCH_CLASSIFIED_OPENED';

    $this->_forward('success', 'utility', 'touch', array(
                                                        'messages' => array(Zend_Registry::get('Zend_Translate')->_($message)),
                                                        'parentRedirect' => $redirect_url,
                                                   ));

  }

  public function fileUpload($file, $user_id)
  {
    $user = Engine_Api::_()->getItem('user', $user_id);
    if (!$user) {
      return;
    }
    try {
      $params = array(
        'parent_type' => 'temporary',
        'parent_id' => 0,
        'user_id' => $user->getIdentity()
      );
      return Engine_Api::_()->storage()->create($file, $params);

    } catch (Exception $e) {
      return;
    }

  }

  private function getUserCategoriesAssoc($user_id)
  {
    if (!$user_id) {
      return array();
    }

    $classifiedCatsTbl = Engine_Api::_()->getDbTable('categories', 'classified');

    $stmt = $classifiedCatsTbl->getAdapter()
        ->select()
        ->from('engine4_classified_categories', array('category_id', 'category_name'))
        ->joinLeft('engine4_classified_classifieds', "engine4_classified_classifieds.category_id = engine4_classified_categories.category_id")
        ->group("engine4_classified_categories.category_id")
        ->where('engine4_classified_classifieds.owner_id = ?', $user_id)
        ->order('category_name ASC')
        ->query();

    $data = array();
    foreach( $stmt->fetchAll() as $category ) {
      $data[$category['category_id']] = $category['category_name'];
    }

    return $data;
  }

}

