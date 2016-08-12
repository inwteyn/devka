<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 2011-08-05 13:41:27 ulan $
 * @author     Ulan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

 
class Article_PhotoController extends Touch_Controller_Action_Standard
{
  public function init()
  {
    if( !Engine_Api::_()->core()->hasSubject() )
    {
      if( 0 !== ($photo_id = (int) $this->_getParam('photo_id')) &&
          null !== ($photo = Engine_Api::_()->getItem('article_photo', $photo_id)) )
      {
        Engine_Api::_()->core()->setSubject($photo);
      }

      else if( 0 !== ($article_id = (int) $this->_getParam('article_id')) &&
          null !== ($article = Engine_Api::_()->getItem('article', $article_id)) )
      {
        Engine_Api::_()->core()->setSubject($article);
      }
    }

    $this->_helper->requireUser->addActionRequires(array(
      'upload',
      'upload-photo',
      'edit',
      'delete',
      'manage'
    ));

    $this->_helper->requireSubject->setActionRequireTypes(array(
      'list' => 'article',
      'upload' => 'article',
      'view' => 'article_photo',
      'edit' => 'article_photo',
      'delete' => 'article_photo',
      'manage' => 'article',
    ));
  }

  public function listAction()
  {
    $this->view->photo = $photo = Engine_Api::_()->core()->getSubject();
    $this->view->article = $article = Engine_Api::_()->core()->getSubject();
    $this->view->album = $album = $article->getSingletonAlbum();
    $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->owner = $owner = Engine_Api::_()->getItem('user', $article->owner_id);
    
    $this->view->canUpload = $this->_helper->requireAuth()->setAuthParams($article, null, 'photo')->checkRequire();

    $album->view_count++;
    $album->save();
  }

  public function manageAction()
  {
    $this->view->article = $article = Engine_Api::_()->core()->getSubject();
    
    if( !$this->_helper->requireAuth()->setAuthParams($article, null, 'photo')->isValid() ) return;
    
    //echo 'stupid';
    //if( !$this->_helper->requireUser()->isValid() ) return;
    //if( !$this->_helper->requireAuth()->setAuthParams($article, null, 'edit')->isValid() ) return;

    $viewer = Engine_Api::_()->user()->getViewer();
    //$this->view->canUpload = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'article', 'photo');

    
    // Prepare data
    $this->view->article = $article = Engine_Api::_()->core()->getSubject();
    $this->view->album = $album = $article->getSingletonAlbum();
    $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
    
    $paginator->setCurrentPageNumber($this->_getParam('page',1));
    $paginator->setItemCountPerPage($paginator->getTotalItemCount());

    // Make form
    $this->view->form = $form = new Article_Form_Photo_Manage();
    
    //$form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes were saved.'));

    foreach( $paginator as $photo )
    {
      $subform = new Article_Form_Photo_Manage_Edit(array('elementsBelongTo' => $photo->getGuid()));
      $subform->populate($photo->toArray());
      $form->addSubForm($subform, $photo->getGuid());
      $form->cover->addMultiOption($photo->getIdentity(), $photo->getIdentity());
    }

    if( !$this->getRequest()->isPost() )
    {
      return;
    }
    
    if( !$form->isValid($this->getRequest()->getPost()))
    {
      return;
    }

    $table = $article->getTable();
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $values = $form->getValues();
      if( !empty($values['cover']) ) {
        $article->photo_id = $values['cover'];
        $article->save();
      }


      // Process
      foreach( $paginator as $photo )
      {
        $subform = $form->getSubForm($photo->getGuid());
        $values = $subform->getValues();

        $values = $values[$photo->getGuid()];
        unset($values['photo_id']);
        if( isset($values['delete']) && $values['delete'] == '1' )
        {
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

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    
    return $this->_helper->redirector->gotoRoute(array('controller'=>'photo', 'action' => 'list', 'subject' => $article->getGuid()), 'article_extended', true);
    
  }  
  
  public function getUploadForm()
  {
    $form = new Article_Form_Photo_Upload();
    $form->removeElement('file');
    $form->removeElement('submit');
    $form->addElement('hidden', 'photos');
    $form->addElement('File', 'file', array(
			'label' => 'Photo',
		));
    $form->addElement('Button', 'submit', array(
      'label' => 'Save Photos',
      'type' => 'submit',
    ));
    $form->file->addValidator('Extension', false, 'jpg,png,gif');
    return $form;
  }

  public function uploadAction()
  {
  	$this->view->article = $article = Engine_Api::_()->core()->getSubject();
    if ($this->is_iPhoneUploading()) {
      if (isset($_FILES['picup-image-upload']['name'])) {
        $this->view->photo_name = $_FILES['picup-image-upload']['name'];
      }

      $this->view->photo_id = $this->uploadPhoto($_FILES['picup-image-upload'], $this->_getParam('owner_id', 0));
      return;
    }
    if (!$this->_helper->requireAuth()->setAuthParams($article, null, 'photo')->isValid() ||
        $this->is_iPhoneUploading()
    ) {
      return;
    }
    if (!$this->_helper->requireUser()->checkRequire()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
      return;
    }

    $this->view->album_id = $album_id = $article->getSingletonAlbum()->album_id;

    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'touch')
      ->getNavigation('article_main');
    
    // Get form
    $this->view->form = $form = $this->getUploadForm();

    //if article just created then following expression must be true
    if($this->_getParam('created')){
      $this->view->created=true;
    };
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$this->getRequest()->isPost())
        return;

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

    $db = Engine_Api::_()->getItemTable('article_photo')->getAdapter();
    $db->beginTransaction();

    try
    {
      $values = $form->getValues();
      $values['album'] = $album_id;

      $album = $this->updateArticleAlbum($values);

      $db->commit();
    }
    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }


    $this->_helper->redirector->gotoRoute(array('controller'=>'photo', 'action' => 'manage', 'subject' => $article->getGuid()), 'article_extended', true);
  }

  public function viewAction()
  {
    $this->view->photo = $photo = Engine_Api::_()->core()->getSubject();
    $this->view->album = $album = $photo->getCollection();
    $this->view->article = $article = $photo->getArticle();
    
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    
    $this->view->canUpload = $this->_helper->requireAuth()->setAuthParams($article, null, 'photo')->checkRequire();
    
    $photo->view_count++;
    $photo->save();
    $ff=new Zend_View();
  }

  public function editAction()
  {
  	
    $photo = Engine_Api::_()->core()->getSubject();

    $this->view->article = $article = $photo->getArticle();
    if( !$this->_helper->requireAuth()->setAuthParams($article, null, 'photo')->isValid() ) return;
    $this->view->form = $form = new Article_Form_Photo_Edit();

    if( !$this->getRequest()->isPost() )
    {
      $form->populate($photo->toArray());
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('photos', 'article')->getAdapter();
    $db->beginTransaction();

    try
    {
      $photo->setFromArray($form->getValues())->save();

      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Your changes were saved.');

    return $this->_forward('success', 'utility', 'touch', array(
      'smoothboxClose' => true,
      'parentRefresh' => true,
      'messages' =>array($this->view->message),
      'parentRedirect' => $photo->getHref()
    ));
  }

  public function deleteAction()
  { 
    $photo = Engine_Api::_()->core()->getSubject();
    $this->view->article = $article = $photo->getParent('article');

    if( !$this->_helper->requireAuth()->setAuthParams($article, null, 'photo')->isValid() ) return;

    $this->view->form = $form = new Article_Form_Photo_Delete();
    if( !$this->getRequest()->isPost() )
    {
      $form->populate($photo->toArray());
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('photos', 'article')->getAdapter();
    $db->beginTransaction();

    try
    {
      $photo->delete();

      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    $parentRedirect = Zend_Controller_Front::getInstance()->getRouter()
      ->assemble(array('controller'=>'photo', 'action' => 'list', 'subject' => $article->getGuid()), 'article_extended', true);
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('TOUCH_ARTICLE_FORM_DELETE_SUCCESS');

    return $this->_forward('success', 'utility', 'touch', array(
      'smoothboxClose' => true,
      'parentRefresh' => true,
      'messages' =>array($this->view->message),
      'parentRedirect' => $this->view->url(array('controller'=>'photo', 'action' => 'list', 'subject' => $article->getGuid()), 'article_extended', true)
    ));
    
  }
    
  public function uploadPhoto($file, $owner_id)
  {
    if (!isset($file) || !is_uploaded_file($file['tmp_name'])) {
      return false;
    }

    $db = Engine_Api::_()->getDbtable('photos', 'article')->getAdapter();
    $db->beginTransaction();

    try
    {
      $photoTable = Engine_Api::_()->getDbtable('photos', 'article');

      $photo = $photoTable->createRow();
      $album = Engine_Api::_()->core()->getSubject()->getSingletonAlbum();
      $photo->setFromArray(array(
                                'article_id' => $album->getArticle()->article_id,
                                'owner_type' => 'user',
                                'user_id' => $owner_id
                           ));
      $this->setPhoto($photo, $file);
      $photo->collection_id = $album->album_id;
      $photo->album_id = $album->album_id;
      $this->view->saved = $photo->save();


      $db->commit();

      return $photo->photo_id;

    } catch (Article_Model_Exception $e) {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('TOUCH_ERROR');
      throw $e;
      return;
    } catch (Exception $e) {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('TOUCH_ERROR');
      throw $e;
      return;
    }
  }

  public function setPhoto($photo, $storage)
  {
    if( is_array($storage) && !empty($storage['tmp_name']) ) {
      $file = $storage['tmp_name'];
      $fileName = $storage['name'];
    } else {
      throw new User_Model_Exception('invalid argument passed to setPhoto');
    }

    if( !$fileName ) {
      $fileName = $file;
    }

    $name = basename($file);
    $extension = ltrim(strrchr($fileName, '.'), '.');
    $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $params = array(
      'parent_type' => $photo->getType(),
      'parent_id' => $photo->getIdentity(),
      'user_id' => $photo->user_id,
      'name' => $fileName,
    );

    // Save
    $filesTable = Engine_Api::_()->getDbtable('files', 'storage');

    // Resize image (main)
    $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(720, 720)
      ->write($mainPath)
      ->destroy();

    // Resize image (normal)
    $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(140, 160)
      ->write($normalPath)
      ->destroy();

    // Store
    try {
      $iMain = $filesTable->createFile($mainPath, $params);
      $iIconNormal = $filesTable->createFile($normalPath, $params);

      $iMain->bridge($iIconNormal, 'thumb.normal');
    } catch( Exception $e ) {
      // Remove temp files
      @unlink($mainPath);
      @unlink($normalPath);
      // Throw
      if( $e->getCode() == Storage_Model_DbTable_Files::SPACE_LIMIT_REACHED_CODE ) {
        throw new Album_Model_Exception($e->getMessage(), $e->getCode());
      } else {
        throw $e;
      }
    }

    // Remove temp files
    @unlink($mainPath);
    @unlink($normalPath);

    // Update row
    $photo->modified_date = date('Y-m-d H:i:s');
    $photo->file_id = $iMain->file_id;
    $photo->save();

    return $photo;
  }

  public function updateArticleAlbum($values)
  {
    $this->view->article = $article = Engine_Api::_()->core()->getSubject();
    $set_cover = false;
    $params = Array();
    if ((empty($values['owner_type'])) || (empty($values['user_id'])))
    {
      $values['user_id'] = Engine_Api::_()->user()->getViewer()->user_id;
      $values['owner_type'] = 'user';
    }
    else
      throw new Zend_Exception("Non-user article albums not yet implemented");

    $album = Engine_Api::_()->getItem('article_album', $values['album']);
    // Add action and attachments
    $api = Engine_Api::_()->getDbtable('actions', 'activity');
    $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), $article, 'article_photo_upload', null, array('count' => count($values['file'])));

    // Do other stuff
    $count = 0;


    foreach( $values['photos'] as $photo_id )
    {
      $photo = Engine_Api::_()->getItem("article_photo", $photo_id);
      if( !($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity() ) continue;

      if( $set_cover )
      {
        $album->photo_id = $photo_id;
        $album->save();
        $set_cover = false;
      }

      $photo->collection_id = $album->album_id;
      $photo->save();

      if( $action instanceof Activity_Model_Action && $count < 8 )
      {
        $api->attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
      }
      $count++;
    }
    if($action)
    $action->setFromArray(array('params' => array('count' => $count)))->save();

    return $album;
  }
}