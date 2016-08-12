<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Register
 * @copyright  Copyright 2009-2012 Hire-Experts Developments
 * @license    http://hire-experts.com
 * @version    $Id: TJFriendship.php 9747 2012-12-04 02:08:08Z teajay $
 * @author     TJ
 */

/**
 * @category   Application_Core
 * @package    Register
 * @copyright  Copyright 2009-2012 Hire-Experts Developments
 * @license    http://hire-experts.com
 */
class Register_View_Helper_TJFriendship extends Zend_View_Helper_Abstract
{
  public function TJFriendship($user, $viewer = null)
  {
    if( null === $viewer ) {
      $viewer = Engine_Api::_()->user()->getViewer();
    }

    if( !$viewer || !$viewer->getIdentity() || $user->isSelf($viewer) ) {
      return '';
    }

    $direction = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction', 1);

    // Get data
    if( !$direction ) {
       $row = $user->membership()->getRow($viewer);
    }
    else $row = $viewer->membership()->getRow($user);

    // Render

    // Check if friendship is allowed in the network
    $eligible =  (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.eligible', 2);
    if($eligible == 0){
      return '';
    }
   
    // check admin level setting if you can befriend people in your network
    else if( $eligible == 1 ) {

      $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
      $networkMembershipName = $networkMembershipTable->info('name');

      $select = new Zend_Db_Select($networkMembershipTable->getAdapter());
      $select
        ->from($networkMembershipName, 'user_id')
        ->join($networkMembershipName, "`{$networkMembershipName}`.`resource_id`=`{$networkMembershipName}_2`.resource_id", null)
        ->where("`{$networkMembershipName}`.user_id = ?", $viewer->getIdentity())
        ->where("`{$networkMembershipName}_2`.user_id = ?", $user->getIdentity())
        ;

      $data = $select->query()->fetch();

      if(empty($data)){
        return '';
      }
    }
    
    if( !$direction ) {
      // one-way mode
      if( null === $row ) {
        return $this->view->htmlLink(array('route' => 'user_extended', 'controller' => 'friends', 'action' => 'add', 'user_id' => $user->user_id), $this->view->translate('Follow'), array(
          'class' => 'buttonlink smoothbox icon_friend_add'
        ));
      } else if( $row->resource_approved == 0 ) {
        return $this->view->htmlLink(array('route' => 'user_extended', 'controller' => 'friends', 'action' => 'cancel', 'user_id' => $user->user_id), $this->view->translate('Cancel Follow Request'), array(
          'class' => 'buttonlink smoothbox icon_friend_cancel'
        ));
      } else {
        return $this->view->htmlLink(array('route' => 'user_extended', 'controller' => 'friends', 'action' => 'remove', 'user_id' => $user->user_id), $this->view->translate('Unfollow'), array(
          'class' => 'buttonlink smoothbox icon_friend_remove'
        ));
      }

    } else {
      // two-way mode
      if( null === $row ) {
        return $this->view->htmlLink(array('route' => 'register_url', 'action' => 'add', 'user_id' => $user->user_id), $this->view->translate('Add Friend'), array(
          'class' => 'buttonlink smoothbox icon_friend_add'
        ));
      } else if( $row->user_approved == 0 ) {
        return $this->view->htmlLink(array('route' => 'user_extended', 'controller' => 'friends', 'action' => 'cancel', 'user_id' => $user->user_id), $this->view->translate('Cancel Request'), array(
          'class' => 'buttonlink smoothbox icon_friend_cancel'
        ));
      } else if( $row->resource_approved == 0 ) {
        return $this->view->htmlLink(array('route' => 'user_extended', 'controller' => 'friends', 'action' => 'confirm', 'user_id' => $user->user_id), $this->view->translate('Accept Request'), array(
          'class' => 'buttonlink smoothbox icon_friend_add'
        ));
      } else if( $row->active ) {
        return $this->view->htmlLink(array('route' => 'user_extended', 'controller' => 'friends', 'action' => 'remove', 'user_id' => $user->user_id), $this->view->translate('Remove Friend'), array(
          'class' => 'buttonlink smoothbox icon_friend_remove'
        ));
      }
    }

    return '';
  }
}