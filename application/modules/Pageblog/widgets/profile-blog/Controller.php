<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageblog
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Pageblog
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pageblog_Widget_ProfileBlogController extends Engine_Content_Widget_Abstract
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

    if (!in_array('pageblog', (array) $subject->getAllowedFeatures())){
      return $this->setNoRender();
    }

    $path = Zend_Controller_Front::getInstance()->getControllerDirectory('pageblog');
    $path = dirname($path) . '/views/scripts';
    $this->view->addScriptPath($path);

    $this->view->headTranslate(array(
      'Delete Blog',
      'Are you sure you want to delete this blog?',
      'Pageblog_Title_Empty',
      'Pageblog_Body_Empty'
    ));
    $auth = Engine_Api::_()->authorization()->context;

    $this->view->isAllowedPost = $isAllowedPost = $auth->isAllowed($subject, $viewer, 'blog_posting');;
    $this->view->isAllowedView = $isAllowedView = $subject->authorization()->isAllowed($viewer, 'view');
    $this->view->isAllowedComment = $subject->authorization()->isAllowed($viewer, 'comment');
    
    if (!$isAllowedView){
      return $this->setNoRender();
    }
    
    $p = 1;
    $this->view->content_info = $content_info = $subject->getContentInfo();
    if($content_info['content'] == 'blog_page'){
        if(!empty($content_info['content_id']))
            $p = $content_info['content_id'];
    }
      if (!empty($content_info['content'])){
        $this->view->init_js_str = $this->getApi()->getInitJs($content_info, $subject);
      }else{
        $this->view->init_js_str = "";
      }
    
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('pageblog');

    $this->view->blogs = $blogs = $this->getTable()->getBlogs(array('page_id' => $subject->getIdentity(), 'ipp' => $this->_getParam('itemCountPerPage', 10), 'p' => $p));
    $this->view->ipp = $this->_getParam('itemCountPerPage', 10);
    if ($this->_getParam('titleCount', false) && $blogs->getTotalItemCount() > 0){
      $this->_childCount = $blogs->getTotalItemCount();
    }
     
    if (!$isAllowedPost){
      return ;
    }
    
    $this->view->blogForm = $blogForm = new Pageblog_Form_Create();
    $blogForm->page_id->setValue($subject->getIdentity());
    $blogForm->setAction($this->view->url(array('action' => 'create'), 'page_blog'));
  }
  
  public function getApi()
  {
    return $this->api = Engine_Api::_()->getApi('core', 'pageblog');
  }
  
  public function getTable()
  {
    return Engine_Api::_()->getDbTable('pageblogs', 'pageblog');
  }
  
  public function getChildCount()
  {
    return $this->_childCount;
  }
}