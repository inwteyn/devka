<?php
class Timeline_Widget_TileFriendsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {

    //General Friend settings
    $this->view->make_list = Engine_Api::_()->getApi('settings', 'core')->user_friends_lists;

    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    // Don't render this if friendships are disabled
    if (!Engine_Api::_()->getApi('settings', 'core')->user_friends_eligible) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject('user');
    if (!$subject->authorization()->isAllowed($viewer, 'view')) {
      return $this->setNoRender();
    }

    // Multiple friend mode
    $select = $subject->membership()->getMembersOfSelect();
    $this->view->friends = $friends = $paginator = Zend_Paginator::factory($select);

    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 5));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Get stuff
    $ids = array();
    foreach ($friends as $friend) {
      $ids[] = $friend->resource_id;
    }
    $this->view->friendIds = $ids;

    // Get the items
    $friendUsers = array();
    foreach (Engine_Api::_()->getItemTable('user')->find($ids) as $friendUser) {
      $friendUsers[$friendUser->getIdentity()] = $friendUser;
    }
    $this->view->friendUsers = $friendUsers;

    // Do not render if nothing to show
    if ($paginator->getTotalItemCount() <= 0) {
      return $this->setNoRender();
    }
  }
}
