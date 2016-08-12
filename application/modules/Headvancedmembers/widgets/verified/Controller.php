<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Headvancedmembers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: VerifyController.php 2015-10-06 16:58:20  $
 * @author     Bolot
 */

/**
 * @category   Application_Extensions
 * @package    Headvancedmembers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Headvancedmembers_Widget_VerifiedController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {

    if (Engine_Api::_()->core()->hasSubject()) {
      $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
    }
    if (!$subject) {
      $this->view->subject = $subject = Engine_Api::_()->user()->getViewer();
    }
    if (!$subject) {
      return $this->setNoRender();
    }
    if($subject->getType() != 'user'){
      return $this->setNoRender();
    }
    $this->view->verified = $verified = Engine_Api::_()->headvancedmembers()->isActive($subject);
  }
}