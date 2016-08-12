<?php
/**
 * SocialEngine
 *
 * @category Application_Extensions
 * @package Timeline
 * @copyright Copyright Hire-Experts LLC
 * @license http://www.hire-experts.com
 * @version $Id: LifeEventController.php 2/16/12 11:09 AM mt.uulu $
 * @author Mirlan
 */

/**
 * @category Application_Extensions
 * @package Timeline
 * @copyright Copyright Hire-Experts LLC
 * @license http://www.hire-experts.com
 */


class Timeline_LifeEventController extends Touch_Controller_Action_Standard
{
  public function init()
  {
    // @todo this may not work with some of the content stuff in here, double-check
    $subject = null;
    if (!Engine_Api::_()->core()->hasSubject()) {
      $this->view->id = $id = $this->_getParam('id');

      if (null !== $id) {
        $subject = Engine_Api::_()->user()->getUser($id);
        if ($subject->getIdentity()) {
          $subject = Engine_Api::_()->getDbTable('users', 'timeline')->findRow($subject->getIdentity());
          Engine_Api::_()->core()->setSubject($subject);
        }
      }
    }

    $this->_helper->requireSubject('user');
    $this->_helper->requireAuth()->setNoForward()->setAuthParams(
      $subject,
      Engine_Api::_()->user()->getViewer(),
      'view'
    );

    $this->_helper->contextSwitch
      ->addActionContext('born', 'html')
//      ->addActionContext('dates', 'json')
      ->initContext();
  }

  public function indexAction(){

    $type = $this->_getParam('type', 'born');

    if( !$type || !method_exists($this, $type.'Action')){
      return $this->_helper->content->setNoRender();
    }

    return $this->_forward($type);
  }

  public function bornAction()
  {
    /**
     * @var $subject Timeline_Model_User
     * @var $viewer User_Model_User
     */
    $subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();

    // check public settings
    $require_check = Engine_Api::_()->getApi('settings', 'core')->core_general_profile;
    if (!$require_check && !$this->_helper->requireUser()->isValid()) {
      return;
    }

    // Check enabled
    if (!$subject->enabled && !$viewer->isAdmin()) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    // Check block
    if ($viewer->isBlockedBy($subject)) {
      return $this->_forward('requireauth', 'error', 'core');
    }


    /**
     * @var $table User_Model_DbTable_Settings
     */
    $table = Engine_Api::_()->getDbTable('settings', 'user');
    $position = unserialize($table->getSetting($subject, 'timeline-born-position'));

    if ( !is_array($position) ||  !array_key_exists('top', $position)  || !array_key_exists('left', $position)) {
      $position = array('top' => 0, 'left' => 0);
    }

    /**
     * Assign Values
     */

    $this->view->albumPhoto = $subject->getTimelineAlbumPhoto('born');
    $this->view->photoExists  = $subject->hasTimelinePhoto('born');
    $this->view->position = $position;
    $this->view->isAlbumEnabled = Engine_Api::_()->touch()->isModuleEnabled('album');

    $this->view->canEdit = $subject->authorization()->isAllowed($viewer, 'edit');
    $this->view->birthdate = $subject->getBirthdate();
  }
}
