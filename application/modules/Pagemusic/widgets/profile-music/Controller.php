<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagemusic
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2010-10-21 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Pagemusic
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagemusic_Widget_ProfileMusicController extends Engine_Content_Widget_Abstract
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

    if (!in_array('pagemusic', (array) $subject->getAllowedFeatures())){
      return $this->setNoRender();
    }

    $path = Zend_Controller_Front::getInstance()->getControllerDirectory('pagemusic');
    $path = dirname($path) . '/views/scripts';
    $this->view->addScriptPath($path);
    
    $playlistTable = $this->getTable('playlists');


    $auth = Engine_Api::_()->authorization()->context;
    $this->view->isAllowedPost = $isAllowedPost = $auth->isAllowed($subject, $viewer, 'music_posting');

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('pagemusic');
    
    $params = array('page_id' => $subject->getIdentity());
    
    $this->view->content_info = $content_info = $subject->getContentInfo();

    if (!empty($content_info['content'])){
      $this->view->init_js_str = $this->getApi()->getInitJs($content_info, $subject);
    }else{
      $this->view->init_js_str = "";
    }
    $p = 1;
if($content_info['content'] == 'music_page'){
    if(!empty($content_info['content_id'])){
        $p = $content_info['content_id'];
    }
}
    $params['p'] = $p;
    $params['ipp'] = $this->_getParam('itemCountPerPage', 10);
    $playlists = $this->view->playlists = $playlistTable->getPaginator($params);
    $this->view->ipp = $this->_getParam('itemCountPerPage', 10);

    if ($this->_getParam('titleCount', false) && $playlists->getTotalItemCount() > 0){
      $this->_childCount = $playlists->getTotalItemCount();
    }
    
    if ($isAllowedPost){
      $this->view->createForm = $createForm = new Pagemusic_Form_Music();
      $createForm->page_id->setValue($subject->getIdentity());      
    }
  }
    
  public function getChildCount()
  {
    return $this->_childCount;
  }

  public function getTable($itemName)
  {
    return Engine_Api::_()->getDbTable($itemName, 'pagemusic');
  }

  public function getApi()
  {
    return $this->api = Engine_Api::_()->getApi('core', 'pagemusic');
  }
}