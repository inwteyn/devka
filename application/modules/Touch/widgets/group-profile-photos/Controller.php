<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Touch_Widget_GroupProfilePhotosController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;
  
  public function indexAction()
  {

    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject('group');
    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }
    $this->getElement()->removeDecorator('Title');

    // Get paginator
    $album = $subject->getSingletonAlbum();

    $select = $album->getCollectiblesSelect();

    $request = Zend_Controller_Front::getInstance()->getRequest();
    $search = $request->getParam('search');
    $page = (int) $request->getParam('page');
    $table = Engine_Api::_()->getDbTable('photos', 'group');
    $tableName = $table->info('name');

    $this->view->form = $form = new Touch_Form_Search();
    if (!empty($search)){
      $form->getElement('search')->setValue($search);
      $select->where("$tableName.title LIKE ? OR $tableName.description LIKE ?", '%'.$search.'%');
    }

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);

    $paginator->setItemCountPerPage(20);
    $paginator->setCurrentPageNumber($page);

    $this->view->canUpload = $canUpload = $subject->authorization()->isAllowed(null,  'photo');

    // Do not render if nothing to show and cannot upload
    if( $paginator->getTotalItemCount() <= 0 && !$canUpload ) {
      return $this->setNoRender();
    }

    // Add count to title if configured
    if( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
      $this->_childCount = $paginator->getTotalItemCount();
    }
  }

  public function getChildCount()
  {
    return $this->_childCount;
  }
}