<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: WidgetController.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Group_WidgetController extends Touch_Controller_Action_Standard
{
  public function requestGroupAction()
  {
    $path = Engine_Api::_()->touch()->getScriptPath('group');
    $this->view->addScriptPath($path);


    $this->view->notification = $notification = $this->_getParam('notification');
  }
}