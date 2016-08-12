<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagevideo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2010-09-20 17:46 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Pagevideo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagevideo_Widget_ProfileVideoController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;
  
  public function indexAction()
  {
    /**
     * @var $subject Page_Model_Page
     */
    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('page');
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

    if (!$subject->authorization()->isAllowed($viewer, 'view')) {
      return $this->setNoRender();
    }

    if ( !($subject instanceof Page_Model_Page) ){
      return $this->setNoRender();
    }

    if (!in_array('pagevideo', (array) $subject->getAllowedFeatures())){
      return $this->setNoRender();
    }

    $path = Zend_Controller_Front::getInstance()->getControllerDirectory('pagevideo');
    $path = dirname($path) . '/views/scripts';
    $this->view->addScriptPath($path);
    
    $table = $this->getTable();
    $settings = Engine_Api::_()->getApi('settings', 'core');

    $auth = Engine_Api::_()->authorization()->context;
    $this->view->isAllowedPost = $isAllowedPost = $auth->isAllowed($subject, $viewer, 'video_posting');
    $this->view->isAllowedView = $subject->authorization()->isAllowed($viewer, 'view');
    $this->view->isAllowedComment = $subject->authorization()->isAllowed($viewer, 'comment');
    $this->view->isTeamMember = $isTeamMember = $subject->isTeamMember();
    
    $this->view->can_create = $isAllowedPost || $isTeamMember; 
    
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('pagevideo', array());

    $this->view->content_info = $content_info = $subject->getContentInfo();
    if (!empty($content_info['content'])) {
      $this->view->init_js_str = $this->getApi()->getInitJs($content_info, $subject);
    } else {
      $this->view->init_js_str = "";
    }

    $p = 1;
    if($content_info['content'] == 'video_page') {
      if(!empty($content_info['content_id'])) {
          $p = $content_info['content_id'];
      }
    }
    $params = array('page_id' => $subject->getIdentity(), 'status' => 1, 'ipp' => $settings->getSetting('pagevideo.page', 10), 'p' => $p);
    
    $themes = Zend_Registry::get('Themes');
    $theme_name = 'default';
    
    if (is_array($themes)) {
      foreach ($themes as $key => $value) {
        $theme_name = $key;
      }
    }

    if ($theme_name == 'midnight') {
      $this->view->theme_class = 'dark';
    } else {
      $this->view->theme_class = 'light';
    }
    
    $data = $table->getVideos($params, true);
    $this->view->videos = $videos = $data['paginator']; 
    $this->view->files = $data['files'];
    
    $this->view->videoEditForm = new Pagevideo_Form_Edit();
    
    if ($this->_getParam('titleCount', false) && $videos->getTotalItemCount() > 0) {
      $this->_childCount = $videos->getTotalItemCount();
    }
    
    if (!$this->view->can_create){
      return ;
    }

    $this->view->videoUploadForm = new Pagevideo_Form_Video();
    $this->view->videoUploadForm->setAction($this->view->url(array('action' => 'create'), 'page_video'));
  }
  
  public function getApi()
  {
    return $this->api = Engine_Api::_()->getApi('core', 'pagevideo');
  }
  
  public function getTable()
  {
    return Engine_Api::_()->getDbTable('pagevideos', 'pagevideo');
  }
  
  public function getChildCount()
  {
    return $this->_childCount;
  }
}