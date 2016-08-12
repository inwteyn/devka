<?php

/**
 * Created by PhpStorm.
 * User: Медербек
 * Date: 03.03.2015
 * Time: 14:31
 */
class Heemoticon_AdminIndexController extends Core_Controller_Action_Admin
{
  public function init()
  {
    if (isset($_GET['ul']) || isset($_FILES['Filedata'])) $this->_forward('upload-stickers', null, null, array('format' => 'json'));
    if (isset($_GET['rp'])) $this->_forward('remove-sticker', null, null, array('format' => 'json'));
    if (isset($_GET['cs'])) $this->_forward('changestatus', null, null, array('format' => 'json'));

  }

  public function collectionsAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('heemoticon_admin_main', array(), 'heemoticon_admin_collections');

    $this->view->filterForm = $filterForm = new Heemoticon_Form_Admin_Filter();
    $values = array();

    if ($filterForm->isValid($this->_getAllParams())) {
      $values = $filterForm->getValues();
    }
    // Check license
    $hecoreApi = Engine_Api::_()->getApi('core', 'hecore');
    $product_result = $hecoreApi->checkProduct('heemoticon');
    if (isset($product_result['result']) && !$product_result['result']) {
      $filterForm->addError($product_result['message']);
      return;
    }
    $page = $this->_getParam('page', 1);

    foreach ($values as $key => $value) {
      if (null === $value) {
        unset($values[$key]);
      }
    }

    $collectionsTable = Engine_Api::_()->getDbTable('collections', 'heemoticon');
    $select = $collectionsTable->select()
      ->setIntegrityCheck(false)
      ->where('name LIKE ?', '%' . $values['title'] . '%');
    $this->view->creditModuleStatus = $this->getCreditModuleStatus();
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $this->view->paginator = $paginator->setCurrentPageNumber($page);
    $paginator->setItemCountPerPage(10);
    $this->view->formValues = array_filter($values);
  }

  public function levelAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('heemoticon_admin_main', array(), 'heemoticon_admin_level_setting');
  }

  public function addcollectionAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('heemoticon_admin_main', array(), 'heemoticon_admin_add_collection');

    $form = new Heemoticon_Form_Admin_Newcollection();
    $this->view->form = $form;

    if (!$this->getRequest()->isPost()) {
      return false;
    }

    $params = $this->getRequest()->getParams();

    if (!$form->isValid($params)) {
      $form->populate($form->getValues());
      return 0;
    }
    // Check license
    $hecoreApi = Engine_Api::_()->getApi('core', 'hecore');
    $product_result = $hecoreApi->checkProduct('heemoticon');
    if (isset($product_result['result']) && !$product_result['result']) {
      $form->addError($product_result['message']);
      return;
    }
    $table = Engine_Api::_()->getItemTable('collection');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      $values = $form->getValues();
      $values['name'] = strip_tags($values['name']);
      $values['description'] = strip_tags($values['description']);
      $values['type'] = 1;
      $values['status'] = 1;
      if (!$values['author'] || $values['author'] == ' ') {
        $values['author'] = Engine_Api::_()->user()->getViewer()->username;
      }
      if (count($values['file']) <= 1) {
        foreach ($values['file'] as $f) {
          if (!$f || $f == "" || empty($f)) {
            $form->populate($form->getValues());
            return $form->addError('Please select stickers');
          }
        }
      }
      $privacy = array();

      $levels = Engine_Api::_()->getItemTable('authorization_level')->fetchAll();
      if ($levels) {
        foreach ($levels as $key => $level) {
          if ($values['level_' . $level->getIdentity()] == 1) {
            $privacy[$key] = $level->getIdentity();
          }
        }
      }

      if (sizeof($levels) == sizeof($privacy) || !sizeof($privacy)) {
        $values['pivacy'] = 0;
      } else {
        $values['privacy'] = implode(',', $privacy);
      }
      if ($values['price'] == null){
        $values['price'] = '0';
      }
      $collection = $table->createRow();
      $collection->setFromArray($values);
      $collection->save();

      $collection->setStickers($values['file'], explode(',', $values['order']));

      $db->commit();

    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    return $this->_helper->redirector->gotoRoute(array('action' => 'collections'));

  }

  public function deletecollectionAction()
  {
    $collection_id = (int)$this->_getParam('collection_id');
    if ($collection_id) {
      Engine_Api::_()->getDbTable('collections', 'heemoticon')->deleteCollection($collection_id);
    }
    return $this->_helper->redirector->gotoRoute(array('action' => 'collections'));
  }

  public function editcollectionAction()
  {
    $creditsModuleStatus = $this->getCreditModuleStatus();
    $collection = Engine_Api::_()->getItem('collection', $this->_getParam('collection_id'));
    $stick_db = Engine_Api::_()->getDbTable('stickers', 'heemoticon');
    $levels = Engine_Api::_()->getItemTable('authorization_level')->fetchAll();

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('heemoticon_admin_main', array(), 'heemoticon_admin_add_collection');

    $form = new Heemoticon_Form_Admin_Newcollection();

    $populate_values = $collection->toArray();

    if ($levels) {
      foreach ($levels as $key => $level) {
        if (in_array($level->getIdentity(), explode(',', $populate_values['privacy'])) || !$populate_values['privacy']) {
          $populate_values['level_' . $level->getIdentity()] = '1';
        } else {
          $populate_values['level_' . $level->getIdentity()] = 'unchecked';
        }
      }
    }

    if (!$creditsModuleStatus) { //check Credits enabled
      $price = $populate_values['price'];
      $populate_values['price'] = '';
    }

    $form->populate($populate_values);
    $this->view->form = $form;

    $this->view->stickers = $stick_db->getSickersByCollectionID($this->_getParam('collection_id'));
    $this->view->collection = $collection;

    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }
    $hecoreApi = Engine_Api::_()->getApi('core', 'hecore');;
    $product_result = $hecoreApi->checkProduct('heemoticon');
    if (isset($product_result['result']) && !$product_result['result']) {
      $form->addError($product_result['message']);
      return;
    }
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try {
      $values = $form->getValues();
      if (!$values['author'] || $values['author'] == ' ') {
        $values['author'] = Engine_Api::_()->user()->getViewer()->username;
      }
      $values['type'] = 1;

      $privacy = array();

      if ($levels) {
        foreach ($levels as $key => $level) {
          if ($values['level_' . $level->getIdentity()] == 1) {
            $privacy[$key] = $level->getIdentity();
          }
        }
      }

      if (sizeof($levels) == sizeof($privacy) || !sizeof($privacy)) {
        $values['privacy'] = "0";
      } else {
        $values['privacy'] = implode(',', $privacy);
      }
      if (empty($privacy) && sizeof($privacy) == 0) {
        $values['privacy'] = "-1"; //None Level
      }

      if (!$creditsModuleStatus){
        $values['price'] = $price;
      }
      if ($values['price'] == null) {
        $values['price'] = '0';
      }

      $collection->setStickers($values['file'], explode(',', $values['order']));
      $collection->setFromArray($values);
      $collection->save();


      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    return $this->_helper->redirector->gotoRoute(array('action' => 'collections'));
  }

  public function changestatusAction()
  {
    try {
      $collection_id = $this->_getParam('collection_id');
      $collection_status = $this->_getParam('collection_status');

      if ($collection_id) {
        Engine_Api::_()->getDbTable('collections', 'heemoticon')->changeCollectionStatus($collection_id, $collection_status);
      }
    } catch (Exception $e) {
      $this->view->success = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Unknown database error');
      throw $e;
    }
    $this->view->success = true;
    $this->view->new_status = $collection_status ? 0 : 1;
  }

  public function uploadStickersAction()
  {
    try {
      if (!$this->_helper->requireUser()->checkRequire()) {
        $this->view->status = false;
        $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
        return;
      }

      if (!$this->getRequest()->isPost()) {
        $this->view->status = false;
        $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
        return;
      }

      $values = $this->getRequest()->getPost();

      if (empty($values['Filename'])) {
        $this->view->status = false;
        $this->view->error = Zend_Registry::get('Zend_Translate')->_('No file');
        return;
      }

      if (!isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
        $this->view->status = false;
        $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
        return;
      }
    } catch (Exception $e) {
      $e->getMessage();
    }
    try {
      $sticker = $this->setSticker($_FILES['Filedata']);

      $this->view->status = true;
      $this->view->photo_id = $sticker['id'];
      $this->view->url = $sticker['url'];
    } catch (Exception $e) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');

      return;
    }
  }

  public function setSticker($photo)
  {
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $filePath = $path . DIRECTORY_SEPARATOR . $photo['name'];
    if (is_array($photo) && !empty($photo['tmp_name'])) {
      $file = $photo['tmp_name'];
      move_uploaded_file($file, $filePath);
      $file = $filePath;
    } else if ($photo instanceof Zend_Form_Element_File) {
      $file = $photo->getFileName();
    } else if ($photo instanceof Storage_Model_File) {
      $file = $photo->temporary();
    } else if ($photo instanceof Core_Model_Item_Abstract && !empty($photo->file_id)) {
      $tmpRow = Engine_Api::_()->getItem('storage_file', $photo->file_id);
      $file = $tmpRow->temporary();
    } else if (is_array($photo) && !empty($photo['tmp_name'])) {
      $file = $photo['tmp_name'];
    } else if (is_string($photo) && file_exists($photo)) {
      $file = $photo;
    } else {
      throw new Exception('invalid argument passed to setPhoto');
    }
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';

    $name = basename($file);
    $params = array(
      'parent_type' => 'heemoticon_sticker'
    );

    // Save
    $storage = Engine_Api::_()->storage();

    // Resize image (normal)
    $iconPath = $path . '/ico_' . $name;
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(48, 48)
      ->write($iconPath)
      ->destroy();

    // Resize image (cover)
    $coverPath = $path . '/cvr_' . $name;
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(150, 150)
      ->write($coverPath)
      ->destroy();

    // Store
    try {

      $iMain = $storage->create($file, $params);
      $iIcon = $storage->create($iconPath, $params);
      $iCover = $storage->create($coverPath, $params);

      $iMain->bridge($iIcon, 'thumb.icon');
      $iMain->bridge($iCover, 'thumb.cover');

    } catch (Exception $e) {
      // Remove temp files
      @unlink($iconPath);
      @unlink($coverPath);
      // Throw
      if ($e->getCode() == Storage_Model_DbTable_Files::SPACE_LIMIT_REACHED_CODE) {
        throw new Album_Model_Exception($e->getMessage(), $e->getCode());
      } else {
        throw $e;
      }
    }

    // Remove temp files
    @unlink($iconPath);
    @unlink($coverPath);
    @unlink($file);

    // Update row
    $sticker['id'] = $iMain->file_id;
    $sticker['url'] = $iMain->storage_path;

    return $sticker;
  }

  public function removeStickerAction()
  {
    $photo_id = $this->_getParam('photo_id', 0);
    $sticker_id = $this->_getParam('sticker_id', 0);

    if ($photo_id == null || $photo_id == '' || !$photo_id) {
      return;
    }

    if ($sticker_id) {
      Engine_Api::_()->getDbTable('stickers', 'heemoticon')->deleteSticker($sticker_id);
    }

    // delete files from server
    $filesDB = Engine_Api::_()->getDbtable('files', 'storage');

    try {
      $filePath = $filesDB->fetchRow($filesDB->select()->where('file_id = ?', $photo_id))->storage_path;
      unlink($filePath);

      $thumbPath = $filesDB->fetchRow($filesDB->select()->where('parent_file_id = ?', $photo_id))->storage_path;
      unlink($thumbPath);

      // Delete image and thumbnail
      $filesDB->delete(array('file_id = ?' => $photo_id));
      $filesDB->delete(array('parent_file_id = ?' => $photo_id));

      $this->view->success = true;
    } catch (Exception $e) {
      $this->view->success = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Unknown database error');
      throw $e;
    }
  }

  public function importCollectionAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('heemoticon_admin_main', array(), 'heemoticon_admin_import_collection');

    $this->view->form = $form = new Heemoticon_Form_Admin_ImportCollection();

    $ds = DIRECTORY_SEPARATOR;
    $upload_tmp_dir = APPLICATION_PATH . $ds . 'temporary' . $ds . 'emoticon' . $ds;

    if (!$this->getRequest()->isPost()) {
      return;
    }

    $form->getValues();
    $file = $form->import->getFileName();
    $name = basename($file);
    $fileNameArray = explode('.', $name);
    $name = $fileNameArray[0];

    if (end($fileNameArray) != 'zip') {
      return $form->addError('Wrong file selected, please select zip file!');
    }

    $photo = new ZipArchive();
    $opent = $photo->open($file);

    $stickers = array();


    if ($opent) {
      $photo->extractTo($upload_tmp_dir . $ds . $name);
      $photo->close();

      $json_file = file_get_contents($upload_tmp_dir . $ds . $name . $ds . 'info.json');
      $json = json_decode($json_file, true);

      if ($json) {

        $table = Engine_Api::_()->getItemTable('collection');
        $collection = $table->createRow();
        $collection->name = $json['title'];
        $collection->description = $json['description'];
        $collection->author = $json['author'];
        $collection->save();

        $objects = scandir($upload_tmp_dir . $ds . $name);
        foreach ($objects as $emo) {
          if ($emo != "." && $emo != ".." && $emo != 'info.json') {
            $sticker = $this->setSticker($upload_tmp_dir . $ds . $name . $ds . $emo);
            @unlink($upload_tmp_dir . $ds . $name . $ds . $emo);
            array_push($stickers, $sticker['id']);
          }
        }
        $collection->setStickers($stickers, $stickers);
        @unlink($upload_tmp_dir . $ds . $name . $ds . 'info.json');
      } else {
        $objects = scandir($upload_tmp_dir . $ds . $name);
        foreach ($objects as $emo) {
          if ($emo != "." && $emo != "..") {
            @unlink($upload_tmp_dir . $ds . $name . $ds . $emo);
          }
        }
        return $form->addError('Wrong file selected, Please check your archive file!');
      }

      return $this->_helper->redirector->gotoRoute(array('action' => 'editcollection', 'collection_id' => $collection->getIdentity()));
    }
  }

  public function exportCollectionAction()
  {
    $id = $this->_getParam('collection_id', 0);
    $viewer = Engine_Api::_()->user()->getViewer();

    $db = Engine_Db_Table::getDefaultAdapter();
    $collections = Engine_Api::_()->getDbTable('collections', 'heemoticon');
    $stickers = Engine_Api::_()->getDbTable('stickers', 'heemoticon');
    $select = $db->select()
      ->from(array('c' => $collections->info('name')), array('c.name as colection_name', 'c.privacy', 'c.stickers_view', 'c.description', 'c.author', 'c.cover'))
      ->joinLeft(array('s' => $stickers->info('name')), "s.collection_id = c.collection_id", array('s.*'))
      ->where('c.collection_id = ?', $id);

    $collections_array = $select->query()->fetchAll();

    $zip = new ZipArchive();
    $zip_name = $collections_array[0]['colection_name'] . ".zip";
    $temporary = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary' . DIRECTORY_SEPARATOR;

    if ($zip->open($zip_name, ZIPARCHIVE::CREATE) !== TRUE) {

      $error = "* Sorry ZIP creation failed at this time";
    }
    $newFilesToRemove = array();
    foreach ($collections_array as $file) {
      $newFile = basename($file['url']);
      if (copy($file['url'], $newFile)) {
        $zip->addFile($newFile);
        array_push($newFilesToRemove, $newFile);
      }
    }
    $myfile = fopen($temporary . "info.json", "w");
    $array = array(
      'title' => $collections_array[0]['colection_name'],
      'description' => $collections_array[0]['description'],
      'author' => $collections_array[0]['author'] ? $collections_array[0]['author'] : ' '
    );
    fwrite($myfile, json_encode($array));

    if (copy($temporary . "info.json", "info.json")) {
      $zip->addFile("info.json");
      array_push($newFilesToRemove, "info.json");
    }
    $zip->close();

    foreach ($newFilesToRemove as $fileR) {
      @unlink($fileR);
    }
    if (file_exists($zip_name)) {
      header('Content-type: application/zip');
      header('Content-Disposition: attachment; filename="' . $zip_name . '"');
      readfile($zip_name);
    }
    @unlink($zip_name);
    exit;

  }
  /**
   * @return bool
   */
  private function getCreditModuleStatus()
  {
    $enabled = Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('credit');
    if ($enabled) {
      $table = Engine_Api::_()->getDbTable('modules', 'core');
      $select = $table->select()->where('name = ?', 'credit');
      $row = $table->fetchRow($select);
      $version = $row->version;
      if (version_compare($version, '4.3.1') < 0) {
        return false;
      } else return true;
    } else {
      return false;
    }
  }
}