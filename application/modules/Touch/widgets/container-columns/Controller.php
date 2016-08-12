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

class Touch_Widget_ContainerColumnsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    if($this->_getParam('from_tl',isset($_GET['from_tl'])?$_GET['from_tl']:false))
      return $this->setNoRender();
    // Set up element
    $this->view->element = $element = $this->getElement();

	}
}