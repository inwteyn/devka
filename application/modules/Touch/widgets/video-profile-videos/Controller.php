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

class Touch_Widget_VideoProfileVideosController extends Engine_Content_Widget_Abstract
{

  protected $_childCount;

  public function indexAction()
  {

    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();

    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject();

    if( !Engine_Api::_()->core()->hasSubject() || !Engine_Api::_()->touch()->isModuleEnabled('video') || !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }

    // Just remove the title decorator
    $this->getElement()->removeDecorator('Title');

    $this->view->form = $form = new Touch_Form_Search();
    $search = $this->_getParam('search');
    if (!empty($search)){
      $form->getElement('search')->setValue($search);
    }

    // Get paginator
    $profile_owner_id = $subject->getIdentity();
    $this->view->paginator = $paginator = Engine_Api::_()->video()->getVideosPaginator(array(
      'user_id' => $profile_owner_id,
      'status' => 1,
      'search' => 1 ,
      'text' => $this->_getParam('search')
    ));

    // Set item count per page and current page number
    $paginator->setItemCountPerPage(5);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Do not render if nothing to show
    if( $paginator->getTotalItemCount() <= 0 && !$search) {
      return $this->setNoRender();
    } else {
      $this->_childCount = $paginator->getTotalItemCount();
    }
  }

  public function getChildCount()
  {
    return $this->_childCount;
  }
}