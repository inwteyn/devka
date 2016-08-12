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

class Touch_Widget_ClassifiedProfileClassifiedsController extends Engine_Content_Widget_Abstract
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
    $subject = Engine_Api::_()->core()->getSubject();
    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }

    // Just remove the title decorator
    $this->getElement()->removeDecorator('Title');

    $request = Zend_Controller_Front::getInstance()->getRequest();
    $search = $request->getParam('search');
    $page = (int) $request->getParam('page');
    $table = Engine_Api::_()->getDbTable('classifieds', 'classified');
    $tableName = $table->info('name');

    $classifiedApi = Engine_Api::_()->getApi('core', 'classified');
    $classifiedsTbl = Engine_Api::_()->getDbTable('classifieds', 'classified');

    if (method_exists($classifiedApi, 'getClassifiedsSelect')) {
      $select = $classifiedApi->getClassifiedsSelect(array(
        'orderby' => 'creation_date',
        'user_id' => $subject->getIdentity(),
      ));
    } else {
      $select = $classifiedsTbl->getClassifiedsSelect(array(
        'orderby' => 'creation_date',
        'user_id' => $subject->getIdentity(),
      ));
    }

    $this->view->form = $form = new Touch_Form_Search();
    if (!empty($search)){
      $form->getElement('search')->setValue($search);
      $select->where("$tableName.title LIKE ? OR $tableName.body LIKE ?", '%'.$search.'%');
    }

    // Get paginator
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    // Set item count per page and current page number
    $paginator->setItemCountPerPage(5);
    $paginator->setCurrentPageNumber($page);

    // Do not render if nothing to show
    if( $paginator->getTotalItemCount() <= 0 && !$page && !$search) {
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