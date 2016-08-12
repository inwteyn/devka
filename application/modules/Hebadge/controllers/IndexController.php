<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 02.04.12 09:12 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Hebadge_IndexController extends Core_Controller_Action_Standard
{
	public function indexAction()
	{
		$this->view->enabled = $this->_helper
			->requireAuth()
			->setAuthParams('usernotes', null, 'enabled')
			->checkRequire();

		$isAllowed = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('habadge.allow.public.view', 1);
		

		if (!$isAllowed && !$this->view->enabled) {
			return $this->setNoRender();
		}

		$this->_helper->content
			->setNoRender()
			->setEnabled();

		try {
			$jobTable = Engine_Api::_()->getDbtable('jobs', 'core');
			$jobtype = 'hebadge_maintenance_rebuild_user';
			$activeJobs = $jobTable->getActiveJobs(array('jobtype' => $jobtype));
			if (count($activeJobs) == 0) {
				$jobTable->addJob($jobtype, array());
			}
		} catch (Exception $e) {

		}
	}

	public function viewAction()
	{
		$badge = Engine_Api::_()->getItem('hebadge_badge', $this->_getParam('id'));

		if (!$badge) {
			return $this->_helper->redirector->gotoRoute(array(), 'hebadge_general', true);
		}

		Engine_Api::_()->core()->setSubject($badge);

		$this->_helper->content
			->setNoRender()
			->setEnabled();

	}

	public function manageAction()
	{
		$this->_helper->content
			->setNoRender()
			->setEnabled();
	}


	public function approvedAction()
	{
		$badge = Engine_Api::_()->getItem('hebadge_badge', $this->_getParam('badge_id'));
		if (!$badge) {
			return;
		}
		$viewer = Engine_Api::_()->user()->getViewer();
		$member = $badge->getMember($viewer);
		if (!$member) {
			return;
		}

		$member->setApproved($this->_getParam('approved'));

	}


}
