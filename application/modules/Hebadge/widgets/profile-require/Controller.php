<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 02.04.12 09:12 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */



class Hebadge_Widget_ProfileRequireController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }

    $subject = Engine_Api::_()->core()->getSubject();

    if (!($subject instanceof Hebadge_Model_Badge)){
      return $this->setNoRender();
    }

    $this->view->require = $require = $subject->getRequire();
    $this->view->require_complete = array();
    if ($viewer->getIdentity()){
      $this->view->require_complete = Engine_Api::_()->getDbTable('require', 'hebadge')->getCompleteRequireIds($viewer, $subject);
    }

    $this->view->member = $member = $subject->getMember($viewer);


    if (empty($require)){
      return $this->setNoRender();
    }

  }



}