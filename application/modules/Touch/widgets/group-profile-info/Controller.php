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

class Touch_Widget_GroupProfileInfoController extends Engine_Content_Widget_Abstract
{
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

    // Get staff
    $ids = array();
    $ids[] = $subject->getOwner()->getIdentity();
    $list = $subject->getOfficerList();
    foreach( $list->getAll() as $listiteminfo )
    {
      $ids[] = $listiteminfo->child_id;
    }

    $staff = array();
    foreach( $ids as $id )
    {
      $user = Engine_Api::_()->getItem('user', $id);
      $staff[] = array(
        'membership' => $subject->membership()->getMemberInfo($user),
        'user' => $user,
      );
    }

    $this->view->group = $subject;
    $this->view->staff = $staff;
  }
}