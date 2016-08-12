<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: TouchActivity.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Touch_View_Helper_TouchActivity extends Zend_View_Helper_Abstract
{
  public function touchActivity(Activity_Model_Action $action = null, array $data = array(), $task = null)
  {
    if( null === $action )
    {
      return '';
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $activity_moderate = Engine_Api::_()->getDbtable('permissions', 'authorization')
        ->getAllowed('user', $viewer->level_id, 'activity');

    $form = new Activity_Form_Comment();
    $data = array_merge($data, array(
      'actions' => array($action),
      'commentForm' => $form,
      'user_limit' => Engine_Api::_()->getApi('settings', 'core')->__get('activity_userlength'),
      'allow_delete' => Engine_Api::_()->getApi('settings', 'core')->__get('activity_userdelete'),
      'activity_moderate' =>$activity_moderate,
    ));

    return $this->view->partial(
      '_touchActivityText.tpl',
      'activity',
      $data
    );
  }
}