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

class Touch_Widget_ListRequestsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Must be logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer->getIdentity() ) {
      return $this->setNoRender();
    }

    // Get requests
    $this->view->requests = Engine_Api::_()->getDbtable('notifications', 'activity')->getRequestCountsByType($viewer);

    // If no requests, just skip rendering
    if( empty($this->view->requests) ) {
      return $this->setNoRender();
    }
  }
}