<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page Documents
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2011-09-01 13:17:53 kirill $
 * @author     Kirill
 */

/**
 * @category   Application_Extensions
 * @package    Page Documents
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Touch_Widget_PageProfileDocumentController extends Engine_Content_Widget_Abstract
{
	protected $_childCount;
	
  public function indexAction()
  {
      $this->view->categories = Engine_Api::_()->getDbTable('categories', 'pagedocument')->getPaginator();
      $this->view->uncategorized = Engine_Api::_()->getDbTable('pagedocuments', 'pagedocument')->getUncategorizedDocumentsCount();
      $this->view->active_category = -1;
      $this->view->view_type = 'all';
    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('page');
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

    if ( !($subject instanceof Page_Model_Page) ){
      return $this->setNoRender();
    }

    if (!in_array('pagedocument', (array) $subject->getAllowedFeatures())){
      return $this->setNoRender();
    }
  	$path = Zend_Controller_Front::getInstance()->getControllerDirectory('pagedocument');
    $path = dirname($path) . '/views/scripts';
  	$this->view->addScriptPath($path);

    $this->view->headTranslate(array(
      'pagedocument_Delete Document',
      'pagedocument_Delete_confirmation'
    ));
  	
  	$this->view->subject = $subject = Engine_Api::_()->core()->getSubject('page');
  	$this->view->isAllowedPost = $isAllowedPost = $subject->authorization()->isAllowed($viewer, 'posting');
  	$this->view->isAllowedView = $isAllowedView = $subject->authorization()->isAllowed($viewer, 'view');
  	$this->view->isAllowedComment = $subject->authorization()->isAllowed($viewer, 'comment');
  	
  	if (!$isAllowedView){
  		return $this->setNoRender();
  	}
  	
      $this->view->content_info = $content_info = $subject->getContentInfo();
      if (!empty($content_info['content'])){
        $this->view->init_js_str = $this->getApi()->getInitJs($content_info);
      }else{
        $this->view->init_js_str = "";
      }

  	$this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('pagedocument');
      $p = 1;
      if($content_info['content'] == 'document_page') {
          if(!empty($content_info['content_id'])){
              $p = $content_info['content_id'];
          }
      }
   	$this->view->documents = $documents = $this->getTable()->getProcessedDocuments(array('page_id' => $subject->getIdentity(), 'ipp' => 10, 'p' => $p));
    
    // Do not render if nothing to show
    if( $documents->getTotalItemCount() <= 0 ){
      return $this->setNoRender();
    }

  	if ($this->_getParam('titleCount', false) && $documents->getTotalItemCount() > 0){
      $this->_childCount = $documents->getTotalItemCount();
    }

  	if (!$isAllowedPost){
  		return 0;
  	}

  	$this->view->documentForm = $documentForm = new Pagedocument_Form_Create();
    $this->view->page_id = $subject->getIdentity();
  	$documentForm->page_id->setValue($subject->getIdentity());
  	$documentForm->setAction($this->view->url(array('action' => 'create'), 'page_document'));
  }
  
  public function getApi()
  {
		return $this->api = Engine_Api::_()->getApi('core', 'pagedocument');
  }
  
	public function getTable()
  {
  	return Engine_Api::_()->getDbTable('pagedocuments', 'pagedocument');
  }
  
	public function getChildCount()
  {
    return $this->_childCount;
  }
}