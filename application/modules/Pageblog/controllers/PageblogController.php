<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageblog
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: BlogsController.php 2012-02-20 15:35 Ulan T $
 * @author     Ulan T
 */
class Pageblog_PageblogController extends Core_Controller_Action_Standard
{
  protected $subject;
  protected $page_id;

  public function init()
  {
    $path = Zend_Controller_Front::getInstance()->getControllerDirectory('pageblog');
    $path = dirname($path) . '/views/scripts';

    $this->view->addScriptPath($path);

    $path = Zend_Controller_Front::getInstance()->getControllerDirectory('like');
    $path = dirname($path) . '/views/scripts';

    $this->view->addScriptPath($path);

      if($this->page_id){
          $this->subject = Engine_Api::_()->getItem('page', $this->page_id);
          $this->page_id = $this->_getParam('page_id');
      }
  }

  public function uploadPhotosAction()
  {

      $this->page_id = $this->_getParam('page_id');
      $this->subject = Engine_Api::_()->getItem('page', $this->page_id);
      $viewer = Engine_Api::_()->user()->getViewer();

    $this->_helper->layout->disableLayout();

    if( !Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('pagealbum') ) {
      return false;
    }

    /*if( !Engine_Api::_()->pagealbum()->isAllowedPost($this->subject) ) {
      return false;
    }*/

//    if( !$this->_helper->requireAuth()->setAuthParams('album', null, 'create')->isValid() ) return;

    if( !$this->_helper->requireUser()->checkRequire() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
      return;
    }

    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    if( !isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name']) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
      return;
    }

    $db = Engine_Api::_()->getDbtable('pagealbumphotos', 'pagealbum')->getAdapter();
    $db->beginTransaction();

    try
    {
      $viewer = Engine_Api::_()->user()->getViewer();

      $photoTable = Engine_Api::_()->getDbtable('pagealbumphotos', 'pagealbum');
      $photo = $photoTable->createRow();
      $photo->setFromArray(array(
        'collection_id' => $this->subject->getIdentity(),
        'owner_id' => $viewer->getIdentity()
      ));
      $photo->save();

      $photo->setPhoto($_FILES['Filedata']);

      $this->view->status = true;
      $this->view->name = $_FILES['Filedata']['name'];
      $this->view->photo_id = $photo->pagealbumphoto_id;
      $this->view->photo_url = $photo->getPhotoUrl();

      $table = Engine_Api::_()->getDbtable('pagealbums', 'pagealbum');
      $album = $table->getSpecialAlbum($this->subject, 'pageblog');

      $photo->collection_id = $album->pagealbum_id;
      $photo->save();

      if( !$album->photo_id )
      {
        $album->photo_id = $photo->getIdentity();
        $album->save();
      }

      $auth      = Engine_Api::_()->authorization()->context;
      $auth->setAllowed($photo, 'everyone', 'view',    true);
      $auth->setAllowed($photo, 'everyone', 'comment', true);
      $auth->setAllowed($album, 'everyone', 'view',    true);
      $auth->setAllowed($album, 'everyone', 'comment', true);


      $db->commit();

    } catch( Album_Model_Exception $e ) {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = $this->view->translate($e->getMessage());
      throw $e;
      return;

    } catch( Exception $e ) {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
      throw $e;
      return;
    }
  }
}