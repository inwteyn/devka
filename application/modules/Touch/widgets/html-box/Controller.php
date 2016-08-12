<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2011-05-19 16:34:00 kirill $
 * @author     Kirill
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Touch_Widget_HtmlBoxController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $this->view->data = $this->_getParam('data');
  }
}