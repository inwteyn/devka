<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2010-09-09 10:15 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Updates_Widget_HomeSubscriberController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
		if(!Engine_Api::_()->authorization()->isAllowed('updates', null, 'use'))
		{
			return $this->setNoRender();
		}

		$this->view->headTranslate(array(
      'UPDATES_name...',
			'UPDATES_email...'
    ));

  	$this->view->form = $form = new Updates_Form_Widgets_Subscribe();
  }
}