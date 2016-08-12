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

class Touch_Widget_PageProfileVideoController extends Engine_Content_Widget_Abstract
{
	protected $_childCount;

  public function indexAction()
  {
    if(!Engine_Api::_()->touch()->isModuleEnabled('pagevideo'))
      return $this->setNoRender();
  	$path = Zend_Controller_Front::getInstance()->getControllerDirectory('pagevideo');
    $path = dirname($path) . '/views/scripts';

    $this->view->form_filter = new Touch_Form_Search();
  	$this->view->addScriptPath($path);
  	$this->view->subject = $subject = Engine_Api::_()->core()->getSubject('page');
  	$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
  	$table = $this->getTable();
    $settings = Engine_Api::_()->getApi('settings', 'core');
  	$this->view->isAllowedPost = $isAllowedPost = $subject->authorization()->isAllowed($viewer, 'posting');

  	$this->view->isAllowedView = $subject->authorization()->isAllowed($viewer, 'view');
  	$this->view->isAllowedComment = $subject->authorization()->isAllowed($viewer, 'comment');
  	$this->view->isTeamMember = $isTeamMember = $subject->isTeamMember();

  	$this->view->can_create = $isAllowedPost || $isTeamMember;

  	$this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('pagevideo', array());

  	$params = array('page_id' => $subject->getIdentity(), 'status' => 1, 'ipp' => $settings->getSetting('pagevideo.page', 10), 'p' => 1);

    $content_info = $subject->getContentInfo();
    if (!empty($content_info['content'])){
      $this->view->init_js_str = $this->getApi()->getInitJs($content_info);
    }else{
      $this->view->init_js_str = "";
    }

    $themes = Zend_Registry::get('Themes');
    $theme_name = 'default';

    if (is_array($themes)) {
      foreach ($themes as $key => $value){
        $theme_name = $key;
      }
    }
    if ($theme_name == 'midnight'){
      $this->view->theme_class = 'dark';
    }else{
      $this->view->theme_class = 'light';
    }

    $data = $table->getVideos($params, true);

  	$this->view->videos = $videos = $data['paginator'];
    $total_item_count = $videos->getTotalItemCount();
    $videos->setItemCountPerPage($total_item_count);
  	$this->view->files = $data['files'];

  	$this->view->videoEditForm = new Pagevideo_Form_Edit();
    // Do not render if nothing to show
    if( $videos->getTotalItemCount() <= 0 ){
      return $this->setNoRender();
    }

    if ($this->_getParam('titleCount', false) && $videos->getTotalItemCount() > 0){
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