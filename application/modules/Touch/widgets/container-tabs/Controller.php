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

class Touch_Widget_ContainerTabsController extends Engine_Content_Widget_Abstract
{
  var $subnav = 'touch_sub_navigation';
  var $page = '/page/';
  public function indexAction()
  {
    // Set up element
    $element = $this->getElement();
    $element->clearDecorators()
      //->addDecorator('Children', array('placement' => 'APPEND'))
      ->addDecorator('Container');

    // If there is action_id make the activity_feed tab active
    $action_id = (int) Zend_Controller_Front::getInstance()->getRequest()->getParam('action_id');
    $activeTab = $action_id ? '/touch.wall|touch.activity-feed/i' : $this->_getParam('tab');
    if( empty($activeTab) ) {
      $activeTab = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab');
    }

    // Iterate over children
    $tabs = array();
    $childrenContent = '';
    foreach( $element->getElements() as $child ) {

      // First tab is active if none supplied
      if( null === $activeTab ) {
        $activeTab = $child->getIdentity();
      }
      // If not active, set to display none
      if(!is_numeric($activeTab) && !preg_match($activeTab, $child->getName())) {
        $child->getDecorator('Container')->setParam('style', 'display:none;');
      }

      // Set specific class name
      $child_class = $child->getDecorator('Container')->getParam('class');
      $child->getDecorator('Container')->setParam('class', $child_class . ' tab_'.$child->getIdentity());

      // Remove title decorator
      $child->removeDecorator('Title');

      if($child->getIdentity() == $activeTab || (!is_numeric($activeTab) && preg_match($activeTab.'', $child->getName()))) {
				$activeTab = $child->getIdentity();
        // Render to check if it actually renders or not
        $childrenContent .= $child->render() . PHP_EOL;
      } else {
        $child->render();
      }

      // Get title and childcount
      $title = $child->getTitle();
      $childCount = null;
      if( method_exists($child, 'getWidget') && method_exists($child->getWidget(), 'getChildCount') ) {
        $childCount = $child->getWidget()->getChildCount();
      }
      if( !$title ) $title = $child->getName();
      // If it does render, add it to the tab list
      if( !$child->getNoRender() ) {
        $tabs[] = array(
          'id' => $child->getIdentity(),
          'name' => $child->getName(),
          'containerClass' => $child->getDecorator('Container')->getClass(),
          'title' => $title,
          'childCount' => $childCount
        );
      }
    }

    // Don't bother rendering if there are no tabs to show
    if( empty($tabs) ) {
      return $this->setNoRender();
    }

    $this->view->activeTab = $activeTab;
    $this->view->tabs = $tabs;
    $this->view->childrenContent = $childrenContent;
    $this->view->max =  $this->_getParam('max');
    $this->view->is_tl =  $this->_getParam('from_tl',isset($_GET['from_tl'])?$_GET['from_tl']:false);
  }
}