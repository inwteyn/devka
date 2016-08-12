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

class Album_IndexController extends Touch_Controller_Action_Standard
{
  public function browseAction()
  {
    if (!$this->_helper->requireAuth()->setAuthParams('album', null, 'view')->isValid() ||
        $this->is_iPhoneUploading()
    ) {
      return;
    }

    $form = $this->view->form_filter = new Touch_Form_Search();

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $form->getElement('search')->setValue($this->_getParam('search'));
    }
    $settings = Engine_Api::_()->getApi('settings', 'core');

    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'touch')
      ->getNavigation('album_main');

    // Prepare data
    $table = Engine_Api::_()->getItemTable('album');

    $select = $table->select()
      ->where("search = 1")
      ->order('modified_date DESC');

    if ($this->_getParam('search', false)) {
      $select->where('title LIKE ? OR description LIKE ?', '%' . $this->_getParam('search') . '%');
    }

    $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('album', null, 'create');

    $paginator = $this->view->paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage($settings->getSetting('album_page', 5));
    $paginator->setCurrentPageNumber($this->_getParam('page'));
  }

  public function manageAction()
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    if (!$this->_helper->requireUser()->isValid()) return;
    if (!$this->_helper->requireAuth()->setAuthParams('album', null, 'create')->isValid() ||
        $this->is_iPhoneUploading()
    ) {
      return;
    }
    $form = $this->view->form_filter = new Touch_Form_Search();
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $form->getElement('search')->setValue($this->_getParam('search'));
    }

    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'touch')
      ->getNavigation('album_main');

    // Get params
    $this->view->page = $page = $this->_getParam('page');

    // Prepare data
    $user = Engine_Api::_()->user()->getViewer();
    $table = Engine_Api::_()->getItemTable('album');

    $select = $table->select()
      ->where('owner_id = ?', $user->getIdentity())->order('modified_date DESC');

    if ($this->_getParam('search', false)) {
      $select->where('title LIKE ? OR description LIKE ?', '%' . $this->_getParam('search') . '%');
    }

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage($settings->getSetting('album_page', 5));
    $paginator->setCurrentPageNumber($page);
  }

  public function uploadAction()
  {

    if ($this->is_iPhoneUploading()) {

      if (isset($_FILES['picup-image-upload']['name'])) {
        $this->view->photo_name = $_FILES['picup-image-upload']['name'];
      }

      $this->view->photo_id = $this->uploadPhoto($_FILES['picup-image-upload'], $this->_getParam('owner_id', 0));
      return;
    } else {

    if (!$this->_helper->requireAuth()->setAuthParams('album', null, 'create')->isValid() ||
        $this->is_iPhoneUploading()
        ) {
      return;
    }

    if (!$this->_helper->requireUser()->checkRequire()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
      return;
    }

    $this->view->album_id = $album_id = $this->_getParam('album_id');

    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'touch')
      ->getNavigation('album_main');
    
    // Get form
    $this->view->form = $form = new Touch_Form_Album_Album();
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$this->getRequest()->isPost()) {
        if ($album_id != null) {
            $form->populate(array(
            'album' => $album_id
        ));
        }
        return;
    }

    $posts = $this->getRequest()->getPost();
    
    $photo_ids = array();
    if (array_key_exists('photos', $posts)) {
      $photo_ids = explode(',', $posts['photos']);
    }

    if (!$form->isValid($posts)) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    if (!empty($_FILES['file'])) {
      $order = 0;
      if (is_array($_FILES['file']['tmp_name'])) {
        foreach ($_FILES['file']['tmp_name'] as $k => $v) {
          $file['name'] = $_FILES['file']['name'][$k];
          $file['type'] = $_FILES['file']['type'][$k];
          $file['tmp_name'] = $_FILES['file']['tmp_name'][$k];
          $file['error'] = $_FILES['file']['error'][$k];
          $file['size'] = $_FILES['file']['size'][$k];
 
          $photo_ids[] = $this->uploadPhoto($file, $viewer->getIdentity(), /* It is for SE 418 Compatibility ->*/$order);
          $order++;
        }
      } else {
        $photo_ids[] = $this->uploadPhoto($_FILES['file'], $viewer->getIdentity(), $order);
      }
    } else {
    }


    foreach ($photo_ids as $key => $photo_id) {
      if (!$photo_id) {
        unset($photo_ids[$key]);
      }
    }
    if (count($photo_ids) > 0) {
      $form->getElement('photos')->setValue($photo_ids);
    } else {
      $form->getElement('photos')->addError('TOUCH_NO_PHOTOS');
      return;
    }

    $db = Engine_Api::_()->getItemTable('album')->getAdapter();
    $db->beginTransaction();

    try
    {
      $album = $form->saveValues();

      $db->commit();
    }
    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }



    $this->_forward('success', 'utility', 'touch', array(
        'messages' => Zend_Registry::get('Zend_Translate')->_('TOUCH_Album has been successfully created.'),
        'parentRedirect' => $this->view->url(array('action' => 'editphotos', 'album_id' => $album->getIdentity()), 'album_specific', true)
    ));
  }
  }

  public function uploadPhoto($file, $owner_id, /* It is for SE 418 Compatibility ->*/$order = 0)
  {

    if (!isset($file) || !is_uploaded_file($file['tmp_name'])) {
      return false;
    }

    $db = Engine_Api::_()->getDbtable('photos', 'album')->getAdapter();
    $db->beginTransaction();

    try
    {
      $photoTable = Engine_Api::_()->getDbtable('photos', 'album');
  
      $photo = $photoTable->createRow();
      $photo->setFromArray(array(
                                'owner_type' => 'user',
                                'owner_id' => $owner_id,
                                'order' => /* It is for SE 418 Compatibility ->*/$order
                           ));
   
        
      $this->view->saved = $photo->save();
    
      $photo->setPhoto($file);
      if(!$order)
        $photo->order = $photo->file_id;
    /* It is for SE 418 Compatibility ^ */
      
      $this->view->file_id = $photo->file_id;
        
      $db->commit();

      return $photo->photo_id;

    } catch (Album_Model_Exception $e) {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = $this->view->translate($e->getMessage());
      throw $e;

      return;

    } catch (Exception $e) {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
      throw $e;
      return;
    }
  }
}