<?php

class Wall_PhotoController extends Touch_Controller_Action_Standard
{

  public function indexAction()
  {
    $subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$subject){
      return ;
    }
    if (!Engine_Api::_()->wall()->isPhotoType($subject->getType())){
      return ;
    }

    $authSubject = null;
    if ($subject->getType() == 'pagealbumphoto'){
      $authSubject = $subject->getPage();
    } else {
      $authSubject = $subject;
    }

    if (!$authSubject->authorization()->isAllowed($viewer, 'view')){
      return ;
    }

    $this->view->subject_id = $subject->getIdentity();


    if ($subject->getType() == 'album_photo'){
      $collection = $subject->getAlbum();
      $collection_key = 'album_id';
    } else {
      $collection = $subject->getCollection();
      $collection_key = 'collection_id';
    }

    $table = $subject->getTable();

    $matches = $table->info('primary');
    $primary = array_pop($matches);
    
    $is_order = isset($subject->order);

    $select = $table->select()
        ->where($collection_key.' = ?', $collection->getIdentity());

    if ($is_order){
      $select->order('order ASC');
    }
    $select->order(''.$primary.' ASC');


    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(1);




    $page = $this->_getParam('p');

    if (!$page){

      $sort_where = '';
      if ($is_order) {
        $sort_where .= '`order` < '.$subject->order.' OR (`order` = 0 AND '.$primary.' < '.$subject->getIdentity().')';
      } else {
        $sort_where .= '('.$primary.' < '.$subject->getIdentity().')';
      }

      $select = $table->select()
          ->from($table->info('name'), new Zend_Db_Expr('COUNT(*)'))
          ->where( $sort_where )
          ->where($collection_key . ' = ?', $collection->getIdentity());

      if ($is_order){
        $select->order('order ASC');
      }

      $select->order(''.$primary.' ASC');

      $result = (int) $table->getAdapter()->fetchOne($select);
      $page = intval($result/$paginator->getItemCountPerPage())+1;

    }


    $this->view->page = $page;
    $paginator->setCurrentPageNumber($page);

      $viewer = Engine_Api::_()->user()->getViewer();

    $items = $paginator->getCurrentItems();
    $this->view->photo = $photo = (isset($items[0]) ? $items[0] : null);


      $this->view->prev = $page == 0 ? 0 : $page-1;
      $this->view->next = $page == $paginator->getTotalItemCount() ? 0 : $page+1;

      $this->view->photo_url = $photo->getPhotoUrl();
      $this->view->comment_html = $this->view->wallComments($photo, $viewer);

    if( !$viewer || !$viewer->getIdentity() || !$collection->isOwner($viewer) ) {
      try{
      $subject->view_count = new Zend_Db_Expr('view_count + 1');
      } catch (Exception $e){}
      $subject->save();
    }

    $this->view->canEdit = $canEdit = $collection->authorization()->isAllowed($viewer, 'edit');
    $this->view->canDelete = $canEdit;
    $this->view->makePhoto = true;

    if ($subject->getType() == 'album_photo'){
      $this->view->canDelete = $canDelete = $collection->authorization()->isAllowed($viewer, 'delete');
    } else if ($subject->getType() == 'pagealbumphoto'){
      $this->view->makePhoto = false;
    }


  }

  public function externalPhotoAction()
  {
    $user = Engine_Api::_()->user()->getViewer();

    // Get photo
    $photo = Engine_Api::_()->getItemByGuid($this->_getParam('photo'));
    if( !$photo || empty($photo->photo_id) )
    {
      $this->_forward('requiresubject', 'error', 'core');
      return;
    }

    if( !$photo->getAlbum()->authorization()->isAllowed(null, 'view') )
    {
      $this->_forward('requireauth', 'error', 'core');
      return;
    }


    // Make form
    $this->view->form = $form = new User_Form_Edit_ExternalPhoto();
    $this->view->photo = $photo;

    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Process
    $db = $user->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      // Get the owner of the photo
      $photoOwnerId = null;
      if( isset($photo->user_id) ) {
        $photoOwnerId = $photo->user_id;
      } else if( isset($photo->owner_id) && (!isset($photo->owner_type) || $photo->owner_type == 'user') ) {
        $photoOwnerId = $photo->owner_id;
      }

      // if it is from your own profile album do not make copies of the image
      if( $photo instanceof Album_Model_Photo &&
          ($photoParent = $photo->getParent()) instanceof Album_Model_Album &&
          $photoParent->owner_id == $photoOwnerId &&
          $photoParent->type == 'profile' ) {

        // ensure thumb.icon and thumb.profile exist
        $newStorageFile = Engine_Api::_()->getItem('storage_file', $photo->file_id);
        $filesTable = Engine_Api::_()->getDbtable('files', 'storage');
        if( $photo->file_id == $filesTable->lookupFile($photo->file_id, 'thumb.profile') ) {
          try {
            $tmpFile = $newStorageFile->temporary();
            $image = Engine_Image::factory();
            $image->open($tmpFile)
              ->resize(200, 400)
              ->write($tmpFile)
              ->destroy();
            $iProfile = $filesTable->createFile($tmpFile, array(
              'parent_type' => $user->getType(),
              'parent_id' => $user->getIdentity(),
              'user_id' => $user->getIdentity(),
              'name' => basename($tmpFile),
            ));
            $newStorageFile->bridge($iProfile, 'thumb.profile');
            @unlink($tmpFile);
          } catch( Exception $e ) { echo $e; die(); }
        }
        if( $photo->file_id == $filesTable->lookupFile($photo->file_id, 'thumb.icon') ) {
          try {
            $tmpFile = $newStorageFile->temporary();
            $image = Engine_Image::factory();
            $image->open($tmpFile);
            $size = min($image->height, $image->width);
            $x = ($image->width - $size) / 2;
            $y = ($image->height - $size) / 2;
            $image->resample($x, $y, $size, $size, 48, 48)
              ->write($tmpFile)
              ->destroy();
            $iSquare = $filesTable->createFile($tmpFile, array(
              'parent_type' => $user->getType(),
              'parent_id' => $user->getIdentity(),
              'user_id' => $user->getIdentity(),
              'name' => basename($tmpFile),
            ));
            $newStorageFile->bridge($iSquare, 'thumb.icon');
            @unlink($tmpFile);
          } catch( Exception $e ) { echo $e; die(); }
        }

        // Set it
        $user->photo_id = $photo->file_id;
        $user->save();

        // Insert activity
        // @todo maybe it should read "changed their profile photo" ?
        $action = Engine_Api::_()->getDbtable('actions', 'activity')
            ->addActivity($user, $user, 'profile_photo_update',
                '{item:$subject} changed their profile photo.');
        if( $action ) {
          // We have to attach the user himself w/o album plugin
          Engine_Api::_()->getDbtable('actions', 'activity')
              ->attachActivity($action, $photo);
        }
      }

      // Otherwise copy to the profile album
      else {
        $user->setPhoto($photo);

        // Insert activity
        $action = Engine_Api::_()->getDbtable('actions', 'activity')
            ->addActivity($user, $user, 'profile_photo_update',
                '{item:$subject} added a new profile photo.');

        // Hooks to enable albums to work
        $newStorageFile = Engine_Api::_()->getItem('storage_file', $user->photo_id);
        $event = Engine_Hooks_Dispatcher::_()
          ->callEvent('onUserProfilePhotoUpload', array(
              'user' => $user,
              'file' => $newStorageFile,
            ));

        $attachment = $event->getResponse();
        if( !$attachment ) {
          $attachment = $newStorageFile;
        }

        if( $action  ) {
          // We have to attach the user himself w/o album plugin
          Engine_Api::_()->getDbtable('actions', 'activity')
              ->attachActivity($action, $attachment);
        }
      }

      $db->commit();
    }

    // Otherwise it's probably a problem with the database or the storage system (just throw it)
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    return $this->_forward('success', 'utility', 'core', array(
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('Set as profile photo')),
      'smoothboxClose' => true,
    ));
  }

  public function photoAction(){

    if ($this->is_iPhoneUploading()) {

      if (isset($_FILES['picup-image-upload']['name'])) {
        $this->view->photo_name = $_FILES['picup-image-upload']['name'];
      }

      $owner_id = $this->_getParam('owner_id', 0);
      
      $this->view->photo_id  = $photo_id = $this->uploadPhoto($_FILES['picup-image-upload'], $owner_id);
      if($photo_id != 0){
        $photo = Engine_Api::_()->getItem('photo', $photo_id);
        $photo_src = $photo->getPhotoUrl('thumb.normal');
        $tmp = explode('?', $photo_src);
        $this->view->photo_src = $tmp[0];
      }
      return;
    }

    $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->form = $form =  new Touch_Form_Wall_Photo();
    $form->owner_id->setValue($viewer->getIdentity());

    if( !Engine_Api::_()->user()->getViewer()->getIdentity() )
    {
      $this->_redirect('login');
      return;
    }

    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid method');
      return;
    }
    
    $parameters = $this->_getAllParams();
    if (!$form->isValid($parameters)) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    if( empty($_FILES['file']) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    $this->view->photo_result = $photo_id = $this->uploadPhoto($_FILES['file'], $viewer->getIdentity());

    $text = $this->_getParam('text', '');
    $url = $this->_getParam('src_url', '');
      
    if($url != "''"){
        $url = substr($url, 1, strlen($url) - 2);

        $url .= '/photo_id/' . $photo_id;
        if($text != 'null')
            $url .= '/text/' . $text;
        $this->_redirectCustom($url); 
    }else {
      if($text != 'null')
            $this->_redirectCustom($this->view->url(array('module'=>'user', 'controller'=>'index', 'action'=>'home', 'photo_id'=>$photo_id, 'text' => $text), 'default'));
     else
            $this->_redirectCustom($this->view->url(array('module'=>'user', 'controller'=>'index', 'action'=>'home', 'photo_id'=>$photo_id), 'default'));
//    $this->_forward('success', 'utility', 'touch', array(
////        'messages' => Zend_Registry::get('Zend_Translate')->_('TOUCH_Album has been successfully created.'),
//        'parentRedirect' => $this->view->url(array('action' => 'home', 'photo_id' => $photo_id), 'wall_post_photo', true)));
    }
  }

  public function uploadPhoto($file, $owner_id) {

    if (!isset($file) || !is_uploaded_file($file['tmp_name'])) {
      return false;
    }

    $db = Engine_Api::_()->getDbtable('photos', 'album')->getAdapter();
    $db->beginTransaction();

    $type = 'wall';
    $album_table = Engine_Api::_()->getDbtable('albums', 'album');
//    $viewer = Engine_Api::_()->user()->getViewer();
    $owner = Engine_Api::_()->getItem('user', $owner_id);
    $album = $album_table->getSpecialAlbum($owner, $type);

    try
    {
      $photoTable = Engine_Api::_()->getDbtable('photos', 'album');

      $photo = $photoTable->createRow();
      $photo->setFromArray(array(
                                'owner_type' => 'user',
                                'owner_id' => $owner_id,
                                'album_id' => $album->album_id
                           ));


      $this->view->saved = $photo->save();

      $photo->setPhoto($file);
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

//  public function deletephotoAction() {
//    $params = $this->_getAllParams();
//
//    if(!empty($params['photo_id'])){
//      $table = Engine_Api::_()->getDbtable('photos', 'album');
//      $db = $table->getAdapter();
//      $db->beginTransaction();
//      try{
//        $photo_id = $params['photo_id'];
//        $photo = Engine_Api::_()->getItem('photo', $photo_id);
//
//        $photo->delete();
//
//        $db->commit();
//
//      }catch(Exception $e){$db->rollback();}
//    }
//  }

}
