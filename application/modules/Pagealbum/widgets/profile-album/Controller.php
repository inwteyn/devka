<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagealbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2010-09-06 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Pagealbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagealbum_Widget_ProfileAlbumController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;
  
  public function indexAction()
  {
    /**
     * @var $subject Page_Model_Page
     */
    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('page');
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

    if ( !($subject instanceof Page_Model_Page) ){
      return $this->setNoRender();
    }

    if (!$subject->authorization()->isAllowed($viewer, 'view')) {
      return $this->setNoRender();
    }

    if (!in_array('pagealbum', (array) $subject->getAllowedFeatures())){
      return $this->setNoRender();
    }

    $path = Zend_Controller_Front::getInstance()->getControllerDirectory('pagealbum');
    $path = dirname($path) . '/views/scripts';
    $this->view->addScriptPath($path);

    $this->view->headTranslate(array(
      'Delete Album',
      'Are you sure you want to delete this album?'
    ));
    $auth = Engine_Api::_()->authorization()->context;


    $this->view->isAllowedPost = $isAllowedPost = $auth->isAllowed($subject, $viewer, 'album_posting');
    $this->view->isAllowedView = $isAllowedView = $subject->authorization()->isAllowed($viewer, 'view');
    $this->view->isAllowedComment = $isAllowedComment = $subject->authorization()->isAllowed($viewer, 'comment');
    $this->view->isTeamMember = $subject->isTeamMember();
    
    if (!$isAllowedView){
      return $this->setNoRender();
    }
    $check_photoviewer = 	 Engine_Api::_()->getDbTable('modules', 'core');
    $select = $check_photoviewer->select()
      ->where('name = ?', 'photoviewer')
      ->where('enabled = ?', 1);
    $viewer_photo = $check_photoviewer->fetchRow($select);
    if($viewer_photo->enabled==1){
      $this->view->photoviewer = 1;
    }else{
      $this->view->photoviewer = 0;
    }
    $this->view->content_info = $content_info = $subject->getContentInfo();
    if (!empty($content_info['content'])) {
      $this->view->init_js_str = $this->getApi()->getInitJs($content_info, $subject);
    } else {
      $this->view->init_js_str = "";
    }

    $request = Zend_Controller_Front::getInstance()->getRequest();
    if($request->getParam('album_id')){
      $this->view->album_open = $request->getParam('album_id');
      $this->view->photo_open = $request->getParam('content_id');
    }
    $p = 1;
    if($content_info['content'] == 'album_page') {
      if(!empty($content_info['content_id'])) {
        $p = $content_info['content_id'];
      }
    }
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('pagealbum');
    $this->view->albums = $albums = $this->getTable()->getAlbums(array('page_id' => $subject->getIdentity(), 'ipp' => $this->_getParam('itemCountPerPage', 10), 'p' => $p, 'nonempty' => true));
    $this->view->ipp = $this->_getParam('itemCountPerPage', 10);
    
    if ($this->_getParam('titleCount', false) && $albums->getTotalItemCount() > 0) {
      $this->_childCount = $albums->getTotalItemCount();
    }
    
    $temp = array();
    if ($albums->getTotalItemCount() > 0){
      foreach ($albums as $album){
          if($album->count() == 0) {}
        $temp[$album->getIdentity()]['title'] = $album->getTitle();
        $temp[$album->getIdentity()]['description'] = $album->getDescription(); 
      }
    }
    
    $this->view->albums_js = Zend_Json_Encoder::encode($temp);
    
    $this->view->albumForm = $albumForm = new Pagealbum_Form_Album();
    $this->view->editForm = $editForm = new Pagealbum_Form_Edit();

    $albumForm->page_id->setValue($subject->getIdentity());
    $editForm->page_id->setValue($subject->getIdentity());
    $editForm->setAction($this->view->url(array('action' => 'edit-album'), 'page_album'));
    
    if ($viewer->getIdentity()){
      $params = array('page_id' => $subject->getIdentity());
      if (!$subject->isTeamMember()){
        $params['user_id'] = $viewer->getIdentity();
      }
      $my_albums = $this->getTable()->getAlbums($params);
      if ($my_albums->getTotalItemCount() > 0){
        $album_options = Array();
        foreach( $my_albums as $my_album ){
          $album_options[$my_album->getIdentity()] = htmlspecialchars_decode($my_album->getTitle());
        }
        $albumForm->album->addMultiOptions($album_options);
      }
    }
    
    $albumForm->setAction($this->view->url(array('action' => 'upload'), 'page_album'));
  }
  
  public function getApi()
  {
    return $this->api = Engine_Api::_()->getApi('core', 'pagealbum');
  }
  
  public function getTable()
  {
    return Engine_Api::_()->getDbTable('pagealbums', 'pagealbum');
  }
  
  public function getChildCount()
  {
    return $this->_childCount;
  }
}