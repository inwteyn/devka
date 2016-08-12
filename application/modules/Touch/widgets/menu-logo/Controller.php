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

class Touch_Widget_MenuLogoController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $this->view->logo = $this->_getParam('logo');
    $this->view->title = $this->_getParam('title');
	}

  public function getCacheKey()
  {
    //return true;
  }
}