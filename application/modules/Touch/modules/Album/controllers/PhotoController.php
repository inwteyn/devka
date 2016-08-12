<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: PhotoController.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Album_PhotoController extends Touch_Controller_Action_Standard
{
  public function init()
  {

    if( !$this->_helper->requireAuth()->setAuthParams('album', null, 'view')->isValid() ) return;

    if( 0 !== ($photo_id = (int) $this->_getParam('photo_id')) &&
        null !== ($photo = Engine_Api::_()->getItem('album_photo', $photo_id)) )
    {
      Engine_Api::_()->core()->setSubject($photo);
    }

    /*
    else if( 0 !== ($album_id = (int) $this->_getParam('album_id')) &&
        null !== ($album = Engine_Api::_()->getItem('album', $album_id)) )
    {
      Engine_Api::_()->core()->setSubject($album);
    }
     */
  }

  public function viewAction()
  {
    if( !$this->_helper->requireSubject('album_photo')->isValid() ) return;

    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->photo = $photo = Engine_Api::_()->core()->getSubject();

    if (method_exists($photo, 'getCollection')) {
      $this->view->album = $album = $photo->getCollection();
    } else {
      $this->view->album = $album = $photo->getAlbum();
    }

    if( !$viewer || !$viewer->getIdentity() || !$album->isOwner($viewer) ) {
      $photo->view_count = new Zend_Db_Expr('view_count + 1');
      $photo->save();
    }

    $this->view->photoIndex = (method_exists($photo, 'getCollectionIndex')) ? $photo->getCollectionIndex() : $photo->getPhotoIndex();
    $this->view->nextPhoto = (method_exists($photo, 'getNextCollectible')) ? $photo->getNextCollectible() : $this->getNextPhoto($photo);
    $this->view->previousPhoto = (method_exists($photo, 'getPrevCollectible')) ? $photo->getPrevCollectible() : $this->getPreviousPhoto($photo);

    // if this is sending a message id, the user is being directed from a coversation
    // check if member is part of the conversation
    $message_id = $this->getRequest()->getParam('message');
    $message_view = false;
    if ($message_id){
      $conversation = Engine_Api::_()->getItem('messages_conversation', $message_id);
      if($conversation->hasRecipient(Engine_Api::_()->user()->getViewer())) $message_view = true;
    }
    $this->view->message_view = $message_view;

    //if( !$this->_helper->requireAuth()->setAuthParams(null, null, 'view')->isValid() ) return;
    if(!$message_view && !$this->_helper->requireAuth()->setAuthParams($photo, null, 'view')->isValid() ) return;

    $checkAlbum = Engine_Api::_()->getItem('album', $this->_getParam('album_id'));
//    $album_id = isset($photo->collection_id) ? $photo->collection_id : $photo->album_id;

		if( !($checkAlbum instanceof Core_Model_Item_Abstract) || !$checkAlbum->getIdentity() /*|| $checkAlbum->album_id != $album_id */)
    {
      $this->_forward('requiresubject', 'error', 'core');
      return;
    }

		$this->view->canEdit = $canEdit = $album->authorization()->isAllowed($viewer, 'edit');
    $this->view->canDelete = $canDelete = $album->authorization()->isAllowed($viewer, 'delete');
  }

  public function deleteAction()
  {
    if( !$this->_helper->requireSubject('album_photo')->isValid() ) return;
    if( !$this->_helper->requireAuth()->setAuthParams(null, null, 'delete')->isValid() ) return;

    $viewer = Engine_Api::_()->user()->getViewer();
    $photo = Engine_Api::_()->core()->getSubject('album_photo');

    if (method_exists($photo, 'getCollection')) {
      $album = $photo->getCollection();
    } else {
      $album = $photo->getAlbum();
    }

    $db = $photo->getTable()->getAdapter();
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

		if( $this->_helper->contextSwitch->getCurrentContext() == 'json' ){
			$this->view->status = 1;
			$this->view->message = Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.');
			return;
		}

    return $this->_forward('success', 'utility', 'touch', array(
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.')),
			'redirect' => false,
      'parentRedirect' => $album->getHref(),
    ));
  }
  private function getNextPhoto($photo)
  {
    $table = $photo->getTable();
    $select = $table->select()
        ->where('album_id = ?', $photo->album_id)
        ->where('`order` > ?', $photo->order)
        ->order('order ASC')
        ->limit(1);
    $nextphoto = $table->fetchRow($select);

    if( !$nextphoto ) {
      $select = $table->select()
          ->where('album_id = ?', $photo->album_id)
          ->where('`photo_id` > ?', $photo->photo_id)
          ->order('photo_id ASC')
          ->limit(1);
      $nextphoto = $table->fetchRow($select);
    }

    if( !$nextphoto ) {
      // Get first photo instead
      $select = $table->select()
          ->where('album_id = ?', $photo->album_id)
          ->order('order ASC')
          ->limit(1);
      $nextphoto = $table->fetchRow($select);
    }

    return $nextphoto;
  }

  private function getPreviousPhoto($photo)
  {
    $table = $photo->getTable();
    $select = $table->select()
        ->where('album_id = ?', $photo->album_id)
        ->where('`order` < ?', $photo->order)
        ->order('order DESC')
        ->limit(1);
    $prevphoto = $table->fetchRow($select);

    if( !$prevphoto ) {
      $select = $table->select()
          ->where('album_id = ?', $photo->album_id)
          ->where('photo_id < ?', $photo->photo_id)
          ->order('photo_id DESC')
          ->limit(1);
      $prevphoto = $table->fetchRow($select);
    }

    if( !$prevphoto ) {
      $select = $table->select()
          ->where('album_id = ?', $photo->album_id)
          ->order('order DESC')
          ->order('photo_id DESC')
          ->limit(1);
      $prevphoto = $table->fetchRow($select);
    }

    return $prevphoto;
  }

}