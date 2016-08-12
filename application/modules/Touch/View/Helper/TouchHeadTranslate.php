<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: TouchHeadTranslate.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

 
class Touch_View_Helper_TouchHeadTranslate extends Zend_View_Helper_Abstract
{
	public function touchHeadTranslate()
	{
		if (!Engine_Api::_()->touch()->isTouchMode()){
			return;
		}

    // Arg should be an instance of Zend_View
		$this->view->headTranslate(array(
			"TOUCH_Select a sample image to add to %1\$s",
			"TOUCH_Choose Photo...",
			"TOUCH_File Upload",
			"TOUCH_Picup application is required! If you can't upload photo, please follow the following url to install Picup application. %s",
			"TOUCH_Available in the AppStore",
      "TOUCH_We're down for maintenance. You cannot upload photo with Picup Application.",
      "TOUCH_Unread_Messages",
      "TOUCH_Unread_Message",
      "TOUCH_Select",
      "TOUCH_HTML5_AUDIO_DOESNT_SUPPORT_MP3",
      "TOUCH_HTML5_AUDIO_DOESNT_SUPPORT",
      "Attach", "The chat room has been disabled by the site admin.", "Add Video",'TOUCH_Import', 'cancel', 'Cancel'
		));



		/* Supported Modules Translates */
		if (Engine_Api::_()->touch()->isModuleEnabled('like')){
			$this->view->headTranslate(array(
				'like_You', 'like_and', 'like_people', 'like_like it', 'like_Suggest to Friends', 'like_Unlike', 'like_Show Like', 'like_Hide', 'like_Like',
				'You and %s people like it.', 'You and %s person like it.', 'You like it.', 'No one like it.', 'like_What do you like to read?',
				'like_What pages do you want to visit?', 'like_What kind of events do you like?', 'like_What groups do you like?', 'like_What classifieds do you like?',
				'like_What albums do you like?', 'like_What videos do you like?', 'like_What music do you like?', 'like_What quizzes do you like to experience?',
				'like_What polls do you like?', 'like_Are you sure you want to unlike this?', "like_You like it.", "like_You and %s other person like it.",
				"like_You and %s other people like it.", "like_%s other people like it.", "like_%s other person like it.", "like_No one like it.", "like_I like %s.",
				"like_%s likes %s."
			));
		}
	}
}