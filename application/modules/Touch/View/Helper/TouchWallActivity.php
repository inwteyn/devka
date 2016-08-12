<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: WallActivity.php 2011-12-23 14:55:03 ulan $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Touch_View_Helper_TouchWallActivity extends Zend_View_Helper_Abstract
{
  public function touchWallActivity(Activity_Model_Action $action = null, array $data = array())
  {
    if( null === $action ) {
      return '';
    }

    $viewer = Engine_Api::_()->user()->getViewer();

    $activity_moderate = "";
    
    if ($viewer->getIdentity()) {
      $activity_moderate = Engine_Api::_()->getDbtable('permissions', 'authorization')
          ->getAllowed('user', $viewer->level_id, 'activity');
    }

    if (isset($data['checkin']) && $data['checkin']) {
      $checkinTable = Engine_Api::_()->getDbTable('checks', 'checkin');
      $action = $checkinTable->getActionById($action->action_id);
      $matchedCheckinsCount = array();
      if ($action->google_id) {
        $matchedCheckinsCount[$action->check_id] = $checkinTable->getMatchedChekinsCount($action->google_id, $action->user_id);
      } else {
        $matchedCheckinsCount[$action->check_id] = $checkinTable->getMatchedChekinsCount(0, $action->user_id, $action->page_id);
      }
      $data = array_merge($data, array('matchedCheckinsCount' => $matchedCheckinsCount));
    }
    $privacy_list = null;
    try {
      $privacy_list = Engine_Api::_()->getDbTable('privacy', 'wall')->getPrivacyList(array($action));
    } catch(Exception $e){
      $privacy_list = null;
    }


    $form = new Wall_Form_Comment();
    $data = array_merge($data, array(
      'actions' => array($action),
      'itemAction' => true,
      'commentForm' => $form,
      'user_limit' => Engine_Api::_()->getApi('settings', 'core')->getSetting('activity_userlength'),
      'allow_delete' => Engine_Api::_()->getApi('settings', 'core')->getSetting('activity_userdelete'),
      'activity_moderate' => $activity_moderate,
      'privacy_list' => $privacy_list
    ));

    if (isset($data['checkin']) && $data['checkin']) {
      return $this->view->partial(
        '_checkinWall.tpl',
        'checkin',
        $data
      );
    }
    $module = (!empty($data['module']) && $data['module'] == 'timeline')?'timeline':'wall';

    return $this->view->partial(
      '_activityText.tpl',
      $module,
      $data
    );
  }
}
