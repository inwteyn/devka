<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: WallActivityLoop.php 2011-04-26 11:18:13 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Touch_View_Helper_TouchWallActivityLoop extends Zend_View_Helper_Abstract
{

  public function touchWallActivityLoop($actions = null, array $data = array())
  {
    if( null == $actions || (!is_array($actions) && !($actions instanceof Zend_Db_Table_Rowset_Abstract)) ) {
      return '';
    }

    $form = new Wall_Form_Comment();
    $viewer = Engine_Api::_()->user()->getViewer();
    $activity_moderate = "";
    if($viewer->getIdentity()){
      $activity_moderate = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('user', $viewer->level_id, 'activity');
    }
    $privacy_list = null;
    try{
      $privacy_list = Engine_Api::_()->getDbTable('privacy', 'wall')->getPrivacyList($actions);
    } catch(Exception $e){
      $privacy_list = null;
    }

    $data = array_merge($data, array(
      'actions' => $actions,
      'commentForm' => $form,
      'user_limit' => Engine_Api::_()->getApi('settings', 'core')->getSetting('activity_userlength'),
      'allow_delete' => Engine_Api::_()->getApi('settings', 'core')->getSetting('activity_userdelete'),
      'activity_moderate' =>$activity_moderate,
      'privacy_list' => $privacy_list
    ));

    $module = (!empty($data['module']) && $data['module'] == 'timeline')?'timeline':'wall';
    return $this->view->partial(
      '_activityText.tpl',
      $module,
      $data
    );
  }

}