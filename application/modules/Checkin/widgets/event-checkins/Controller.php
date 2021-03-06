<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Checkin
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 09.12.11 11:32 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Checkin
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Checkin_Widget_EventCheckinsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    if (!Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('event')) {
      return $this->setNoRender();
    }

    if (!Engine_Api::_()->core()->hasSubject('event')) {
      return $this->setNoRender();
    }

    $placesTbl = Engine_Api::_()->getDbTable('places', 'checkin');

    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
    $this->view->place = $place = $placesTbl->findByObject('event', $subject->getIdentity());

    if (!$place) {
      return $this->setNoRender();
    }

    if ($this->_getParam('page', false) !== false) {
      $this->getElement()->removeDecorator('Title');
    }

    $path = Zend_Controller_Front::getInstance()->getControllerDirectory('wall');
    $path = dirname($path) . '/views/scripts';
    $this->view->addScriptPath($path);

    $path = Zend_Controller_Front::getInstance()->getControllerDirectory('checkin');
    $path = dirname($path) . '/views/scripts';
    $this->view->addScriptPath($path);


    $this->view->actions = $actions = Engine_Api::_()->getDbTable('checks', 'checkin')->getActionsByObject('event', $subject->getIdentity());

    $actions->setItemCountPerPage($this->_getParam('itemCountPerPage', 8));
    $actions->setCurrentPageNumber($this->_getParam('page', 1));
    $actions->setCurrentPageNumber($this->_getParam('page', 1));

    if (!$actions->getTotalItemCount()) {
      return $this->setNoRender();
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $activity_moderate = "";
    if($viewer->getIdentity()) {
      $activity_moderate = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('user', $viewer->level_id, 'activity');
    }


    $unique = rand(11111, 99999);
    $this->view->feed_uid = 'wall_' . $unique;

    $this->view->commentForm = new Wall_Form_Comment();
    $this->view->user_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('activity_userlength');
    $this->view->allow_delete = Engine_Api::_()->getApi('settings', 'core')->getSetting('activity_userdelete');
    $this->view->activity_moderate = $activity_moderate;
  }
}