<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: TouchActivityLoop.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Touch_View_Helper_TouchActivityLoop extends Activity_View_Helper_Activity
{
  public function touchActivityLoop($actions = null, $data = array())
  {
    if( null == $actions || (!is_array($actions) && !($actions instanceof Zend_Db_Table_Rowset_Abstract)) ) {
      return '';
    }

		$form = new Activity_Form_Comment();
    $viewer = Engine_Api::_()->user()->getViewer();
    $activity_moderate = "";
    if($viewer->getIdentity()){
      $activity_moderate = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('user', $viewer->level_id, 'activity');
    }
    $data = array_merge($data, array(
      'actions' => $actions,
			'commentForm' => $form,
      'user_limit' => Engine_Api::_()->getApi('settings', 'core')->__get('activity_userlength'),
      'allow_delete' => Engine_Api::_()->getApi('settings', 'core')->__get('activity_userdelete'),
      'activity_moderate' =>$activity_moderate,
    ));

    return $this->view->partial(
      '_touchActivityText.tpl',
      'touch',
      $data
    );
  }
}