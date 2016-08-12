<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Headvancedmembers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @author     Bolot
 */

/**
 * @category   Application_Extensions
 * @package    Headvancedmembers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Headvancedmembers_View_Helper_HeuserFriends extends Zend_View_Helper_Abstract
{
  public function heuserFriends($user, $viewer = null)
  {
    if( null === $viewer ) {
      $viewer = Engine_Api::_()->user()->getViewer();
    }

/*    if( !$viewer || !$viewer->getIdentity() || $user->isSelf($viewer) ) {
      return '';
    }*/
    $return_array = array();
    $select = $user->membership()->getMembersOfSelect();
    $friends = $paginator = Zend_Paginator::factory($select);

    $paginator->setItemCountPerPage(4);
    $return_array = array(
      'count' => $paginator->getTotalItemCount(),
      'friends' => $paginator
    );

    $moduleTb = Engine_Api::_()->getDbtable('modules', 'core');
    if ($moduleTb->isModuleEnabled('album')) {
      $photoTable  =Engine_Api::_()->getDbTable('photos','album');
      $select = $photoTable->select()->where('owner_type=?','user')->where('owner_id=?',$user->getIdentity());
      $result = $photoTable->fetchAll($select);
      $return_array['photo_count'] = count($result);
      $return_array['photo'] = true;
    }else{
      $return_array['photo'] =  false;
    }
    if ($moduleTb->isModuleEnabled('video')) {
      $vidoeTable  =Engine_Api::_()->getDbTable('videos','video');
      $select = $vidoeTable->select()->where('owner_id=?',$user->getIdentity());
      $result = $vidoeTable->fetchAll($select);
      $return_array['video_count'] = count($result);
      $return_array['video'] = true;
    }else{
      $return_array['video'] =  false;
    }
    return $return_array;
  }
}