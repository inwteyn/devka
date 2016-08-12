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

class Touch_Widget_UserProfileFriendsController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;
  
  public function indexAction()
  {
    // Just remove the title decorator
    $this->view->element = $element = $this->getElement()->removeDecorator('Title');
    //General Friend settings
    $this->view->make_list = Engine_Api::_()->getApi('settings', 'core')->user_friends_lists;

    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject('user');
    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }

    // Multiple friend mode
    $select = $subject->membership()->getMembersSelect();

    $request = Zend_Controller_Front::getInstance()->getRequest();
    $search = $request->getParam('search');
    $page = (int) $request->getParam('page');
    $table = Engine_Api::_()->getDbtable('users', 'user');
    $tableName = $table->info('name');

    $this->view->form = $form = new Touch_Form_Search();

    if (!empty($search)){
      $form->getElement('search')->setValue($search);
      $membershipTbl = Engine_Api::_()->getDbTable('membership', 'user');
      $membershipTblName = $membershipTbl->info('name');

      $select = $membershipTbl->select()
        ->from($membershipTblName)
        ->joinLeft($tableName, $tableName.'.user_id = '.$membershipTblName.'.user_id', null)
        ->where("$tableName.username LIKE ? OR $tableName.displayname LIKE ?", '%'.$search.'%')
        ->where($membershipTblName.'.resource_id = ?', $viewer->getIdentity())
        ->where($membershipTblName.'.active = ?', true);
    }

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);

    // Set item count per page and current page number
    $paginator->setItemCountPerPage(5);
    $paginator->setCurrentPageNumber($page);

    $this->view->friends = $friends = $paginator;

    // Get stuff
    $ids = array();
    foreach( $friends as $friend )
    {
      $ids[] = $friend->user_id;
    }
    $this->view->friendIds = $ids;

    // Get the items
    $friendUsers = array();
    foreach( Engine_Api::_()->getItemTable('user')->find($ids) as $friendUser )
    {
      $friendUsers[$friendUser->getIdentity()] = $friendUser;
    }
    $this->view->friendUsers = $friendUsers;

    // Do not render if nothing to show
    if( $paginator->getTotalItemCount() <= 0 && !$page && !$search ) {
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