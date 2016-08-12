<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminVideosController.php 08.09.11 17:33 taalay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Store_AdminVideosController extends Core_Controller_Action_Admin
{
  public function init()
  {
    try {
      $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

      /**
       * @var $product Store_Model_Product
       */

      $this->view->product = $product = Engine_Api::_()->getItem('store_product', $this->_getParam('product_id'));

      if (!Engine_Api::_()->core()->hasSubject('store_product')) {
        Engine_Api::_()->core()->setSubject($product);
      }

      if (!$product->isOwner($viewer))  {
        return;
      }

      $this->view->hasVideo = $product->hasVideo();

      $this->view->menu = $this->_getParam('action');

      $pid = $this->_getParam('product_id');
      $productsTbl = Engine_Api::_()->getItemTable('store_product');
      $this->view->next = $this->next = $productsTbl->fetchRow($productsTbl->select()->where('page_id = 0')->where('product_id > ?', $pid)->limit(1)->order('product_id asc'));
      $this->view->prev = $this->prev = $productsTbl->fetchRow($productsTbl->select()->where('page_id = 0')->where('product_id < ?', $pid)->limit(1)->order('product_id desc'));

      $this->view->activeMenu = "store_admin_main_products";
    } catch (Exception $e) {
      throw $e;
    }
  }

  public function editVideoAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if ($this->next) {
      $this->view->nextHref = $this->view->url(array('module' => 'store', 'controller' => 'videos', 'action' => 'edit-video', 'product_id' => $this->next->getIdentity()));
    }
    if ($this->prev) {
      $this->view->prevHref = $this->view->url(array('module' => 'store', 'controller' => 'videos', 'action' => 'edit-video', 'product_id' => $this->prev->getIdentity()));
    }
    $this->view->section_title = $this->view->translate('STORE_Admin Section Edit video');
    $product = $this->view->product;

    $this->view->video = $video = $product->getVideo();

    // Make form
    $this->view->form = $form = new Store_Form_Admin_Video_Edit($video);
    if ($video) {
      $form->populate($video->toArray());
    }

    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$video) {
      $table = Engine_Api::_()->getItemTable('store_video');
    } else {
      $table = $video->getTable();
    }

    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      $values = $this->_getAllParams();

      if (!$video) {
        $video = $table->createRow();
        $video->product_id = (int)$this->_getParam('product_id');
        $video->owner_id = $viewer->getIdentity();
        $video->status = 1;
        $video->save();
        $video->type = $values['type'];
      }


      $api = Engine_Api::_()->getApi('core', 'store');

      switch ($values['type']) {
        case 3: //desktop
          if($video->file_id && empty($_FILES['Filedata'])) {
            break;
          }
          if (empty($_FILES['Filedata'])) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('No file');
            return;
          }

          $illegal_extensions = array('php', 'pl', 'cgi', 'html', 'htm', 'txt');
          if ((!isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name'])) ||
            (in_array(pathinfo($_FILES['Filedata']['name'], PATHINFO_EXTENSION), $illegal_extensions))
          ) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload') . print_r($_FILES, true);
            return;
          }

          $video = $api->createVideo(
            array('owner_type' => 'user', 'owner_id' => $viewer->getIdentity()),
            $_FILES['Filedata'],
            $video
          );
          break;
        case 0: // none
          $video->delete();
          break;
        default: // service
          if ($video->type == 3 || ($video->url != '' && $video->url != $values['url'])) {

            $api->deleteVideo($video);
            $video = $table->createRow();
          }

          $video->status = 1;
          $video->product_id = (int)$this->_getParam('product_id');
          $video->owner_id = $viewer->getIdentity();
      }

      if(!$video->photo_id)
        Engine_Api::_()->getApi('core', 'store')->createThumbnail($video);
      $this->view->status = true;

      $video->setFromArray($values);
      $video->save();
      $this->view->preview = $video->getRichContent(1);
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  public function addVideoAction()
  {
    if ($this->next) {
      $this->view->nextHref = $this->view->url(array('module' => 'store', 'controller' => 'videos', 'action' => 'add-video', 'product_id' => $this->next->getIdentity()));
    }
    if ($this->prev) {
      $this->view->prevHref = $this->view->url(array('module' => 'store', 'controller' => 'videos', 'action' => 'add-video', 'product_id' => $this->prev->getIdentity()));
    }
    $this->view->section_title = $this->view->translate('STORE_Admin Section Add video');
    $viewer = $this->view->viewer;
    $product = $this->view->product;

    if ($this->view->hasVideo) {
      $this->redirect('edit-video');
    }

    $this->view->video = $product->getVideo();

    // Create form
    $this->view->form = $form = new Store_Form_Admin_Video_Edit();

    $form->getDecorator('description')->setOption('escape', false);
    if ($this->_getParam('type', false)) $form->getElement('type')->setValue($this->_getParam('type'));

    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->_getAllParams())) {
      $values = $form->getValues('url');
      return;
    }

    // Process
    $values = $form->getValues();

    $table = Engine_Api::_()->getDbtable('videos', 'store');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      // Create video
      $video = $table->createRow();

      $video->setFromArray($values);
      $video->product_id = (int)$this->_getParam('product_id');
      $video->owner_id = $viewer->getIdentity();
      $video->status = 1;
      $video->save();

      Engine_Api::_()->getApi('core', 'store')->createThumbnail($video);

      $db->commit();
      $this->redirect('edit-video');
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  public function deleteAction()
  {
    $product = $this->view->product;
    $this->view->video = $video = $product->getVideo();
    $this->view->status = false;

    if (!$video) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Video doesn't exists or not authorized to delete");
      return;
    }

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    $db = $video->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      Engine_Api::_()->getApi('core', 'store')->deleteVideo($video);
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Video has been deleted.');
  }

  public function validationAction()
  {
    $video_type = $this->_getParam('type');
    $code = $this->_getParam('code');
    $ajax = $this->_getParam('ajax', false);
    $valid = false;

    // check which API should be used
    if ($video_type == "youtube") {
      $valid = $this->checkYouTube($code);
    }
    if ($video_type == "vimeo") {
      $valid = $this->checkVimeo($code);
    }

    $this->view->code = $code;
    $this->view->ajax = $ajax;
    $this->view->valid = $valid;
  }

  // YouTube Functions
  /*public function checkYouTube($code)
  {
    if (!$data = @file_get_contents("http://gdata.youtube.com/feeds/api/videos/" . $code)) return false;
    if ($data == "Video not found") return false;
    return true;
  }*/
  public function checkYouTube($code){
    $key = Engine_Api::_()->getApi('settings', 'core')->getSetting('video.youtube.apikey');
    if (!$data = @file_get_contents('https://www.googleapis.com/youtube/v3/videos?part=id&id=' . $code . '&key=' . $key)) return false;

    $data = Zend_Json::decode($data);
    if (empty($data['items'])) return false;
    return true;
  }
  
  // Vimeo Functions
  public function checkVimeo($code)
  {
    //http://www.vimeo.com/api/docs/simple-api
    //http://vimeo.com/api/v2/video
    $data = @simplexml_load_file("http://vimeo.com/api/v2/video/" . $code . ".xml");
    $id = count($data->video->id);
    if ($id == 0) return false;
    return true;
  }

  public function redirect($action)
  {
    $this->_redirectCustom(
      $this->view->url(
        array(
          'module' => 'store',
          'controller' => 'videos',
          'action' => $action,
          'product_id' => $this->view->product->getIdentity()
        ),
        'admin_default', true
      )
    );
  }
}