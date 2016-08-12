<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Pagealbum_IndexController extends Touch_Controller_Action_Standard
{
    private $_subject;

    public function init()
    {
        $page_id = (int)$this->_getParam('page_id');
        $subject = null;
        $navigation = new Zend_Navigation();

        if ($page_id) {
            $subject = Engine_Api::_()->getDbTable('pages', 'page')->findRow($page_id);
        }

        if ($subject && !Engine_Api::_()->getApi('core', 'page')->isAllowedView($subject)) {
            $subject = null;
        }

        $viewer = Engine_Api::_()->user()->getViewer();

        if ($subject) {
            Engine_Api::_()->core()->setSubject($subject);

            $navigation->addPage(array(
                                      'label' => 'Browse Albums',
                                      'route' => 'page_album',
                                      'action' => 'index',
                                      'params' => array(
                                          'page_id' => $subject->getIdentity()
                                      )
                                 ));

            if ($subject->authorization()->isAllowed($viewer, 'posting')) {

                $navigation->addPage(array(
                                          'label' => 'Manage Albums',
                                          'route' => 'page_album',
                                          'action' => 'manage',
                                          'params' => array(
                                              'page_id' => $subject->getIdentity()
                                          )
                                     ));

            }

        }

        $this->_subject = $this->view->subject = $subject;
        $this->view->navigation = $navigation;

    }

    public function indexAction()
    {
        if (!$this->_subject) {
            return $this->_forward('index', 'index', 'page');
        }

    // Prepare data -=: By Ulan :=-
      $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('pagealbum', null, 'create');
      $this->view->paginator = $this->getPaginator();
    // Prepare data -=: By Ulan :=-
    }

    public function mineAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();

        if (!$this->_subject || !$viewer->getIdentity()) {
          return $this->_forward('index', 'index', 'page');
        }
        $user = Engine_Api::_()->user()->getViewer();
        $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('pagealbum', null, 'create');
        // Prepare data -=: By Ulan :=-
        $this->view->paginator = $this->getPaginator($user->getIdentity());
        // Prepare data -=: By Ulan :=-

        $params = array(
            'page_id' => $this->_subject->getIdentity(),
            'ipp' => 10,
            'p' => $this->_getParam('page'),
            'user_id' => $viewer->getIdentity());

        $this->view->paginator = Engine_Api::_()->getDbTable('pagealbums', 'pagealbum')->getAlbums($params);

    }

    public function viewAction()
    {
        $album_id = (int)$this->_getParam('album_id');
        $album = null;

        $table = Engine_Api::_()->getDbTable('pagealbums', 'pagealbum');

        if ($album_id) {
            $album = $table->findRow($album_id);
        }

        if (!$album) {
          return $this->_forward('index', 'index', 'page');
        }

        $this->view->album = $album;
        $this->view->subject = $album->getParent();
        $this->view->paginator = $paginator = $album->getCollectiblesPaginator();

        $paginator->setCurrentPageNumber($this->_getParam('page'));

        Engine_Api::_()->core()->setSubject($album);

    }

    public function viewPhotoAction()
    {
        $photo_id = (int)$this->_getParam('photo_id');
        $photo = null;

        if ($photo_id) {
            $photo = Engine_Api::_()->getDbTable('pagealbumphotos', 'pagealbum')->findRow($photo_id);
        }

        if (!$photo) {
          return $this->_forward('index', 'index', 'page');
        }

        $this->view->photo = $photo;
        $this->view->album = $photo->getParent();
        $this->view->subject = $this->view->album->getParent();
        $this->view->url = $this->view->url(array('controller'=>'index', 'action'=>'view-photo', 'content_id'=>$this->view->identity), 'page_album', true);
    }

    public function deleteAction()
    {
        if (!$this->_subject || !Engine_Api::_()->getApi('core', 'page')->isAllowedPost($this->_subject)) {
          return $this->_forward('index', 'index', 'page');
        }

        $page_id = $this->_subject->getIdentity();
        $album = (int)$this->_getParam('album');

        $this->view->form = $form = new Touch_Form_Standard;

        $form->setTitle('Delete Album')
                ->setDescription('Are you sure you want to delete this album?')
                ->setAttrib('class', 'global_form_popup')
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
                ->setMethod('POST');

        $form->addElement('Button', 'submit', array(
                                                   'label' => 'Delete album',
                                                   'type' => 'submit',
                                                   'ignore' => true,
                                                   'decorators' => array('ViewHelper')
                                              ));

        $form->addElement('Cancel', 'cancel', array(
                                                   'label' => 'cancel',
                                                   'link' => true,
                                                   'prependText' => ' or ',
                                                   'href' => urldecode($this->_getParam('return_url')),
                                                   'decorators' => array(
                                                       'ViewHelper'
                                                   )
                                              ));

        $form->addDisplayGroup(array('submit', 'cancel'), 'buttons');

        $form->setAction($this->view->url(array(
                                               'action' => 'delete',
                                               'page_id' => $page_id,
                                               'album' => $album,
                                               'return_url' => $this->_getParam('return_url')
                                          ), 'page_album'));

        if (!$this->getRequest()->isPost() || !$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $table = Engine_Api::_()->getDbTable('pagealbums', 'pagealbum');

        $album = $table->fetchRow($table->select()
                                          ->where("page_id = {$page_id}")
                                          ->where("pagealbum_id = {$album}"));

        if (!$album) {
            return;
        }

        $select = $album->getCollectiblesSelect();
        $photo_id = Engine_Api::_()->getDbTable('pagealbumphotos', 'pagealbum')->getAdapter()->fetchOne($select);

        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            if (!empty($photo_id)) {
                $attachmentTable = Engine_Api::_()->getDbtable('attachments', 'activity');
                $name = $attachmentTable->info('name');
                $select = $attachmentTable->select()
                        ->setIntegrityCheck(false)
                        ->from($name, array('action_id'))
                        ->where('type = ?', "pagealbumphoto")
                        ->where('id = ?', $photo_id);

                $action_id = (int)$attachmentTable->getAdapter()->fetchOne($select);
                $where = array('action_id = ?' => $action_id);

                $actionsTable = Engine_Api::_()->getDbtable('actions', 'activity');
                $actionsTable->delete($where);

                $streamTable = Engine_Api::_()->getDbtable('stream', 'activity');
                $streamTable->delete($where);

                $attachmentTable->delete($where);

                $where = array('resource_id = ?' => $action_id);

                $commentTable = Engine_Api::_()->getDbtable('comments', 'activity');
                $commentTable->delete($where);

                $likeTable = Engine_Api::_()->getDbtable('likes', 'activity');
                $likeTable->delete($where);
            }

            $album->delete();

            $db->commit();
        }
        catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        //    return $this->_forward('success', 'utility', 'touch', array(
        //      'messages' => array(Zend_Registry::get('Zend_Translate')->_('Album was deleted.')),
        //      'return_url'=>urldecode($this->_getParam('return_url')),
        //    ));
        return $this->_forward('success', 'utility', 'touch', array(
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.')),
          'parentRedirect' => $this->view->url(array('action' => 'mine')),
        ));

    }

    public function uploadAction()
    {

        if ($this->is_iPhoneUploading()) {

          if (isset($_FILES['picup-image-upload']['name'])) {
            $this->view->photo_name = $_FILES['picup-image-upload']['name'];
          }

          $this->view->photo_id = $this->uploadPhoto($_FILES['picup-image-upload'], $this->_getParam('owner_id', 0));
          return;
        }

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

        $pagealbumw = Engine_Api::_()->core()->getSubject();

        $this->view->pagealbum_id = $pagealbum_id = $this->_getParam('pagealbum_id');

        $this->view->page_id = $page_id = $this->_getParam('page_id', 0);
        $this->view->form = $form = new Touch_Form_Pagealbum_Album($page_id);
        // Get form
        $viewer = Engine_Api::_()->user()->getViewer();

        $posts = $this->getRequest()->getPost();

        // Not post/invalid
        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        if (!$this->getRequest()->isPost()) {
            if ($pagealbum_id != null) {
                $form->populate(array(
                                     'album' => $pagealbum_id
                                ));
            }
            return;
        }

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

            if (is_array($_FILES['file']['tmp_name'])) {
                foreach ($_FILES['file']['tmp_name'] as $k => $v) {
                    $file['name'] = $_FILES['file']['name'][$k];
                    $file['type'] = $_FILES['file']['type'][$k];
                    $file['tmp_name'] = $_FILES['file']['tmp_name'][$k];
                    $file['error'] = $_FILES['file']['error'][$k];
                    $file['size'] = $_FILES['file']['size'][$k];


                    $photo_ids[] = $this->uploadPhoto($file, $viewer->getIdentity());

                }
            } else {
                $photo_ids[] = $this->uploadPhoto($_FILES['file'], $viewer->getIdentity());
            }
        }
        ;

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


        $db = Engine_Api::_()->getDbtable('pagealbums', 'pagealbum')->getAdapter();
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


//    return $this->_forward('success', 'utility', 'touch', array(
//        'messages' => array(Zend_Registry::get('Zend_Translate')->_('TOUCH_Album has been successfully created.')),
//        'parentRedirect' => $pagealbumw->getHref(), 'page_album', true)
//    );
//        return $this->_forward('success', 'utility', 'touch', array(
//           'messages' => array(Zend_Registry::get('Zend_Translate')->_('TOUCH_Changes have been saved.')),
//           'parentRedirect' => $this->view->url(array('action' => 'view', 'album_id' => $this->_getParam('pagealbum_id')), 'page_album', true),
//        ));

//        return $this->_forward('success', 'utility', 'core', array(
//          'messages' => array(Zend_Registry::get('Zend_Translate')->_('TOUCH_Album has been successfully created.')),
//          'parentRedirect' => $this->view->url(array('action' => 'manage')),
//        ));
        $this->_redirectCustom(array('route' => 'page_view', 'action' => 'view', 'page_id' => $this->_getParam('page_id')));
    }

    public function editAction()
    {

        $pagealbumw = Engine_Api::_()->core()->getSubject();

        if (!$this->_helper->requireUser()->isValid()) return;
        $viewer = Engine_Api::_()->user()->getViewer();

        $pagealbum = Engine_Api::_()->getItem('pagealbum', $this->_getParam('pagealbum_id'));

        if( !$this->_helper->requireSubject()->isValid() ) return;

        $this->view->pagealbum = $pagealbum;

        // Make form
        $this->view->form = $form = new Touch_Form_Pagealbum_Edit();

        $form->getElement('title')->setValue($pagealbum->title);
        $form->getElement('description')->setValue($pagealbum->description);

        // prepare tags
        $pagealbumTags = $pagealbum->tags()->getTagMaps();

        $tagString = '';
        foreach ($pagealbumTags as $tagmap)
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
        $db = Engine_Api::_()->getDbtable('pagealbums', 'pagealbum')->getAdapter();
        $db->beginTransaction();
        
        try
        {
            $values = $form->getValues();
            $pagealbum->setFromArray($values);

            // Add tags
            $tags = preg_split('/[,]+/', $tagString);
            $pagealbum->tags()->setTagMaps($viewer, $tags);

            $pagealbum->save();

            $db->commit();

            
        }catch(Exception $e){
            $db->rollBack();
            throw $e;
        }

//        $this->_redirectCustom(array('route' => 'page_album', 'action' => 'manage', 'page_id' => $this->_getParam('page_id')));

//        return $this->_forward('success', 'utility', 'touch', array(
//            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.')),
//			'redirect' => false,
//            'parentRedirect' => $pagealbumw->getHref(),
//        ));

    }
    
    public function uploadPhoto($file, $owner_id)
    {

        if (!isset($file) || !is_uploaded_file($file['tmp_name'])) {
            return false;
        }

        $db = Engine_Api::_()->getDbtable('pagealbumphotos', 'pagealbum')->getAdapter();
        $db->beginTransaction();
        try
        {
            $photoTable = Engine_Api::_()->getDbtable('pagealbumphotos', 'pagealbum');
            $photo = $photoTable->createRow();
            $photo->setFromArray(array(
                                      'owner_id' => $owner_id
                                 ));

            $this->view->saved = $photo->save();

            $photo->setPhoto($file);
            $this->view->file_id = $photo->file_id;
            $db->commit();

            return $photo->pagealbumphoto_id;


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

    function getValues()
    {
        return array(
            'description' => trim($this->_getParam('description')),
            'title' => trim($this->_getParam('title')),
            'page_id' => (int)$this->_getParam('page_id'),
            'album' => (int)($this->_getParam('album', $this->_getParam('album_id'))),
            'file' => $this->_getParam('file'),
            'tags' => $this->_getParam('tags')
        );
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

 protected function getTable(){
   return Engine_Api::_()->getDbTable('pagealbums', 'pagealbum');
 }
}