<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AlbumController.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Album_AlbumController extends Touch_Controller_Action_Standard
{
  public function init()
  {
    if (!$this->_helper->requireAuth()->setAuthParams('album', null, 'view')->isValid() )
      return;
    if (0 !== ($photo_id = (int)$this->_getParam('photo_id')) &&
        null !== ($photo = Engine_Api::_()->getItem('album_photo', $photo_id))
    ) {
      Engine_Api::_()->core()->setSubject($photo);
    }

    else if (0 !== ($album_id = (int)$this->_getParam('album_id')) &&
             null !== ($album = Engine_Api::_()->getItem('album', $album_id))
    ) {
      Engine_Api::_()->core()->setSubject($album);
    }
  }

  public function editAction()
  {
    if (!$this->_helper->requireUser()->isValid()) return;
    if (!$this->_helper->requireSubject('album')->isValid()) return;
    if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid())
      return;

    // Prepare data
    $this->view->album = $album = Engine_Api::_()->core()->getSubject();

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'touch')->getAlbumNavigation($album, 'edit', true);

    // Make form
    $this->view->form = $form = new Album_Form_Album_Edit();

    if (!$this->getRequest()->isPost()) {
      $form->populate($album->toArray());
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      foreach ($roles as $role) {
        if (1 === $auth->isAllowed($album, $role, 'view')) {
          $form->auth_view->setValue($role);
        }
        if (1 === $auth->isAllowed($album, $role, 'comment')) {
          $form->auth_comment->setValue($role);
        }
        if (1 === $auth->isAllowed($album, $role, 'tag')) {
          $form->auth_tag->setValue($role);
        }
      }

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
    $db = $album->getTable()->getAdapter();
    $db->beginTransaction();

    try
    {
      $values = $form->getValues();
      $album->setFromArray($values);
      $album->save();

      // CREATE AUTH STUFF HERE
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

      if (empty($values['auth_view'])) {
        $values['auth_view'] = key($form->auth_view->options);
        if (empty($values['auth_view'])) {
          $values['auth_view'] = 'everyone';
        }
      }
      if (empty($values['auth_comment'])) {
        $values['auth_comment'] = key($form->auth_comment->options);
        if (empty($values['auth_comment'])) {
          $values['auth_comment'] = 'owner_member';
        }
      }
      if (empty($values['auth_tag'])) {
        $values['auth_tag'] = key($form->auth_tag->options);
        if (empty($values['auth_tag'])) {
          $values['auth_tag'] = 'owner_member';
        }
      }

      $viewMax = array_search($values['auth_view'], $roles);
      $commentMax = array_search($values['auth_comment'], $roles);
      $tagMax = array_search($values['auth_tag'], $roles);

      foreach ($roles as $i => $role) {
        $auth->setAllowed($album, $role, 'view', ($i <= $viewMax));
        $auth->setAllowed($album, $role, 'comment', ($i <= $commentMax));
        $auth->setAllowed($album, $role, 'tag', ($i <= $tagMax));
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
      foreach ($actionTable->getActionsByObject($album) as $action) {
        $actionTable->resetActivityBindings($action);
      }
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    return $this->_forward('success', 'utility', 'touch', array(
       'messages' => array(Zend_Registry::get('Zend_Translate')->_('TOUCH_Changes have been saved.')),
       'parentRedirect' => $this->view->url(array('action' => 'view', 'album_id' => $album->getIdentity()), 'album_specific', true),
    ));
  }

  public function editphotosAction()
  {
    if (!$this->_helper->requireUser()->isValid()) return;
    if (!$this->_helper->requireSubject('album')->isValid()) return;
    if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid())
      return;

    // Prepare data
    $this->view->album = $album = Engine_Api::_()->core()->getSubject();

    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'touch')->getAlbumNavigation($album, 'editphotos', true);

    if (method_exists($album, 'getCollectiblesPaginator')) {
      $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
    } else {
      $photoTable = Engine_Api::_()->getItemTable('album_photo');
      $this->view->paginator = $paginator = $photoTable->getPhotoPaginator(array(
        'album' => $album,
      ));
    }

    $paginator->setCurrentPageNumber($this->_getParam('page'));
    $paginator->setItemCountPerPage(5);

    // Make form
    $this->view->form = $form = new Album_Form_Album_Photos();

    foreach ($paginator as $photo) {
      $subform = new Touch_Form_Album_EditPhoto(array('elementsBelongTo' => $photo->getGuid()));
      $title1 = $subform->getElement('title')->getValue();
      $caption1 = $subform->getElement('description')->getValue();
      $subform->populate($photo->toArray());
      $title2 = $subform->getElement('title')->getValue();
      $caption2 = $subform->getElement('description')->getValue();

      if (empty($title2)) {
        $subform->getElement('title')->setValue($title1);
      } else {
        $subform->getElement('title')->setAttrib('class', '');
      }

      if (empty($caption2)) {
        $subform->getElement('description')->setValue($caption1);
      } else {
        $subform->getElement('description')->setAttrib('class', '');
      }

      $form->addSubForm($subform, $photo->getGuid());
      $form->cover->addMultiOption($photo->getIdentity(), $photo->getIdentity());
    }

    if (!$this->getRequest()->isPost()) {
      return;
    }
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    $table = $album->getTable();
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $values = $form->getValues();
      if (!empty($values['cover'])) {
        $album->photo_id = $values['cover'];
        $album->save();
      }


      // Process
      foreach ($paginator as $photo)
      {
        $subform = $form->getSubForm($photo->getGuid());
        $values = $subform->getValues();

        $values = $values[$photo->getGuid()];
        unset($values['photo_id']);
        if (isset($values['delete']) && $values['delete'] == '1') {
          $photo->delete();
        }
        else
        {
          $photo->setFromArray($values);
          $photo->save();
        }
      }

      $db->commit();
    }

    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    $this->_forward('success', 'utility', 'touch', array(
                                                        'parentRedirect' => $this->view->url(array('action' => 'view', 'album_id' => $album->album_id), 'album_specific', true),
                                                        'messages' => Array(Zend_Registry::get('Zend_Translate')->_('TOUCH_Changes have been saved.'))
                                                   ));
  }

  public function viewAction()
  {
    if (!$this->_helper->requireSubject('album')->isValid()) return;

    $this->view->album = $album = Engine_Api::_()->core()->getSubject();
    if (!$this->_helper->requireAuth()->setAuthParams($album, null, 'view')->isValid()) return;

    // Prepare params
    $this->view->page = $page = $this->_getParam('page');

    // Prepare data
    if (method_exists($album, 'getCollectiblesPaginator')) {
      $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
    } else {
      $photoTable = Engine_Api::_()->getItemTable('album_photo');
      $this->view->paginator = $paginator = $photoTable->getPhotoPaginator(array(
        'album' => $album,
      ));
    }

    $paginator->setItemCountPerPage(20);
    $paginator->setCurrentPageNumber($page);

    // Do other stuff
    $this->view->mine = $mine = true;
    $this->view->can_edit = $can_edit = $this->_helper->requireAuth()->setAuthParams($album, null, 'edit')->checkRequire();
    if (!$album->getOwner()->isSelf(Engine_Api::_()->user()->getViewer())) {
      $album->view_count++;
      $album->save();
      $mine = false;
    }

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'touch')->getAlbumNavigation($album, 'view', $can_edit, $mine);
  }

  public function deleteAction()
  {
    $album = Engine_Api::_()->getItem('album', $this->getRequest()->getParam('album_id'));

    if (!$this->_helper->requireAuth()->setAuthParams($album, null, 'delete')->isValid()) return;

    $this->view->form = $form = new Album_Form_Album_Delete();

    if (!$album) {
      $message = Zend_Registry::get('Zend_Translate')->_("Album doesn't exists or not authorized to delete");
      return $this->_forward('success', 'utility', 'touch', array(
                                                                 'return_url' => $this->_getParam('return_url'),
                                                                 'messages' => Array($message)
                                                            ));
    }

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    $db = $album->getTable()->getAdapter();
    $db->beginTransaction();

    try
    {
      $album->delete();

      $db->commit();
    }

    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    $parentRedirect = false;
    $parentRefresh = false;
    if ((int)$this->_getParam('redirect', 0)) {
      $parentRedirect = $this->view->url(array('action' => 'manage'), 'album_general');
    } else {
      $parentRefresh = true;
    }

    $message = Zend_Registry::get('Zend_Translate')->_('Album has been deleted.');

    return $this->_forward('success', 'utility', 'touch', array(
                                                               'parentRedirect' => $parentRedirect,
                                                               'parentRefresh' => $parentRefresh,
                                                               'messages' => Array($message)
                                                          ));
  }

  private function getNavigation($can_edit = false, $mine = false)
  {

  }
}