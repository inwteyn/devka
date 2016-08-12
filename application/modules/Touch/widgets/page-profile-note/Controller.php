<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2011-02-14 07:29:38 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    

class Touch_Widget_PageProfileNoteController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $api = Engine_Api::_()->core();
    $subject_id = ($api->hasSubject()) ? $api->getSubject()->getIdentity() : 0;

    if (!Engine_Api::_()->touch()->checkPageWidget($subject_id, 'touch.page-profile-note')){
      return $this->setNoRender();
    }
    

    $this->getElement()->setTitle('');

  	$this->view->subject = $subject = Engine_Api::_()->core()->getSubject('page');
		$this->view->isTeamMember = $subject->isTeamMember();
		
		if (!$this->view->isTeamMember && trim($subject->note) == ''){
			$this->setNoRender();
		}
  }
}