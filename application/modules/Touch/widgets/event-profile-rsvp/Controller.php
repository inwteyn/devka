<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Touch_Widget_EventProfileRsvpController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer->getIdentity()){
      return $this->setNoRender();
    }
    $subject = (Engine_Api::_()->core()->hasSubject()) ? Engine_Api::_()->core()->getSubject('event') : null;
    if (!$subject){
      return $this->setNoRender();
    }
    if (!$subject->authorization()->isAllowed($viewer, 'view')){
      return $this->setNoRender();
    }
    if (!$subject->membership()->isMember($viewer)){
      return $this->setNoRender();
    }
    $this->view->subject = $subject;
    $this->view->member = $subject->membership()->getRow($viewer);

  }

}