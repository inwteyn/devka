<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2011-11-16 09:07 alexander $
 * @author     Alexander
 */

/**
 * @category   Application_Extensions
 * @package    Pageevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pageevent_Widget_ProfileEventController extends Engine_Content_Widget_Abstract
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

    if (!in_array('pageevent', (array) $subject->getAllowedFeatures())){
      return $this->setNoRender();
    }

    // Set Current Path
    $path = Zend_Controller_Front::getInstance()->getControllerDirectory('pageevent');
    $path = dirname($path) . '/views/scripts';
    $this->view->addScriptPath($path);

    $page_enabled = Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('page');

    // If page not installed
    if (!$page_enabled || !$subject || !Engine_Api::_()->getApi('core', 'page')->isAllowedView($subject)) {
      return $this->setNoRender();
    }

    $auth = Engine_Api::_()->authorization()->context;
    $isAllowedPost = $auth->isAllowed($subject, $viewer, 'event_posting');
    $this->view->assign(array(
      'page_id' => $subject->getIdentity(),
      'navigation' => Engine_Api::_()->getApi('menus', 'core')->getNavigation('pageevent'),
      'form' => new Pageevent_Form_Form($subject),
      'isAllowedPost' => $isAllowedPost
    ));

    $this->view->content_info = $content_info = $subject->getContentInfo();
    if (!empty($content_info['content']) ){
      $method = Zend_Controller_Front::getInstance()->getRequest()->getParam('method', false);
      $this->view->init_js_str = $this->getApi()->getInitJs($content_info, $method, $subject);
    }else{
        $this->view->init_js_str = "";
    }

    $tbl = Engine_Api::_()->getDbTable('pageevents', 'pageevent');
    $p = $this->_getParam('page', 1);
    if($content_info['content'] == 'event_page'){
        if(!empty($content_info['content_id'])){
            $p = $content_info['content_id'];
        }
    }
    $this->view->paginator = $paginator = $tbl->getPaginator(
      $subject->getIdentity(),
      $this->_getParam('show'),
      $p,
      $viewer->getIdentity(),
      $this->_getParam('itemCountPerPage', 10)
    );

    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 10));
    $this->view->ipp = $this->_getParam('itemCountPerPage', 10);

    $this->view->event_id = $content_info['content_id'];

    $this->view->isTeamMember = $subject->isTeamMember($viewer);
    $this->view->viewer = $viewer;

    if ($this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ){
      $this->_childCount = $paginator->getTotalItemCount();//$tbl->getCount($subject->getIdentity());
    }

  }

  public function getChildCount()
  {
    return $this->_childCount;
  }

  public function getApi()
  {
	return $this->api = Engine_Api::_()->getApi('core', 'pageevent');
  }

}