<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2011-02-14 07:29:38 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Touch_Widget_PageProfileWidgetsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
		$this->view->left = $left =  $this->_getParam('left');
		$this->view->right = $right =  $this->_getParam('right');
  }
}