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

class Touch_Widget_PageProfileMusicController extends Engine_Content_Widget_Abstract
{
	protected $_childCount;
	
  public function indexAction()
  {
    if(!Engine_Api::_()->touch()->isModuleEnabled('pagemusic'))
      return $this->setNoRender();
    $path = Zend_Controller_Front::getInstance()->getControllerDirectory('pagemusic');
    $path = dirname($path) . '/views/scripts';
  	$this->view->addScriptPath($path);
    
    $playlistTable = $this->getTable('playlists');

    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('page');
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->isAllowedPost = $isAllowedPost = $subject->authorization()->isAllowed($viewer, 'posting');

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('pagemusic');
    
    $params = array('page_id' => $subject->getIdentity());
    $playlists = $this->view->playlists = $playlistTable->getPaginator($params);
		$playlists->setItemCountPerPage(10);

    // Do not render if nothing to show
    if( $playlists->getTotalItemCount() <= 0 ) {
      return $this->setNoRender();
    }
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