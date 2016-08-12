<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: TouchUserFriendship.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Touch_View_Helper_TouchUserFriendship extends Zend_View_Helper_Abstract
{
  public function touchUserFriendship($user, $viewer = null)
  {
    if( null === $viewer ) {
      $viewer = Engine_Api::_()->user()->getViewer();
    }

    if( !$viewer || !$viewer->getIdentity() || $user->isSelf($viewer) ) {
      return '';
    }

    // Get data
    $row = $viewer->membership()->getRow($user);

    // Render

    // Check if friendship is allowed in the network
    $eligible =  (int) Engine_Api::_()->getApi('settings', 'core')->__get('user.friends.eligible', 2);
    if($eligible == 0){
      return '';
    }
   
    // check admin level setting if you can befriend people in your network
    else if( $eligible == 1 ){

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
    if(isset($_SERVER['REDIRECT_URL'])){
      $return_url = urlencode($_SERVER['REDIRECT_URL']);
      if( null === $row ) {
        return $this->view->htmlLink(array('route' => 'user_extended', 'controller' => 'friends', 'action' => 'add', 'user_id' => $user->user_id, 'return_url' => $return_url), $this->view->translate('Add Friend'), array(
          'class' => 'buttonlink smoothbox'
        ));
      } else if( $row->user_approved == 0 ) {
        return $this->view->htmlLink(array('route' => 'user_extended', 'controller' => 'friends', 'action' => 'cancel', 'user_id' => $user->user_id, 'return_url' => $return_url), $this->view->translate('Cancel Request'), array(
          'class' => 'buttonlink smoothbox'
        ));
      } else if( $row->resource_approved == 0 ) {
        return $this->view->htmlLink(array('route' => 'user_extended', 'controller' => 'friends', 'action' => 'confirm', 'user_id' => $user->user_id, 'return_url' => $return_url), $this->view->translate('Accept Request'), array(
          'class' => 'buttonlink smoothbox'
        ));
      } else if( $row->active ) {
        return $this->view->htmlLink(array('route' => 'user_extended', 'controller' => 'friends', 'action' => 'remove', 'user_id' => $user->user_id, 'return_url' => $return_url), $this->view->translate('Remove Friend'), array(
          'class' => 'buttonlink smoothbox'
        ));
      }
    }
    return '';
  }
}