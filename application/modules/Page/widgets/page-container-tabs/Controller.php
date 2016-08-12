<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Controller.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Page_Widget_PageContainerTabsController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        // Set up element
        $element = $this->getElement();
        $element->clearDecorators()
            //->addDecorator('Children', array('placement' => 'APPEND'))
            ->addDecorator('Container');

        // If there is action_id make the activity_feed tab active
        $action_id = (int)Zend_Controller_Front::getInstance()->getRequest()->getParam('action_id');
        $activeTab = $action_id ? 'activity.feed' : Zend_Controller_Front::getInstance()->getRequest()->getParam('tab');

        if (empty($activeTab)) {
            $activeTab = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab');
        }

        $this->view->widgets_pseudonyms = $widgets_pseudonyms = Engine_Api::_()->page()->getWidgetsPseudonyms();

        // Iterate over children
        $tabs = array();
        $childrenContent = '';

        foreach ($element->getElements() as $child) {
            // Set specific class name
            $child_class = $child->getDecorator('Container')->getParam('class');
            $child->getDecorator('Container')->setParam('class', $child_class . ' tab_' . $child->getIdentity());

            // Remove title decorator
            $child->removeDecorator('Title');
            // Render to check if it actually renders or not

            if($widgets_pseudonyms[$activeTab] == $child->getName() || !$activeTab) {
                $childrenContent = $child->render() . PHP_EOL;
                $activeTab = $child->getName();
            }

            // Get title and childcount
            $title = $child->getTitle();
            $childCount = null;
            if (method_exists($child, 'getWidget') && method_exists($child->getWidget(), 'getChildCount')) {
                $childCount = $child->getWidget()->getChildCount();
            }
            if (!$title) $title = $child->getName();
            // If it does render, add it to the tab list


                $tabs[] = array(
                    'id' => $child->getIdentity(),
                    'name' => $child->getName(),
                    'containerClass' => $child->getDecorator('Container')->getClass(),
                    'title' => $title,
                    'childCount' => $childCount
                );


        }

        // Don't bother rendering if there are no tabs to show
        if (empty($tabs)) {
            return $this->setNoRender();
        }

        $this->view->activeTab = $activeTab;
        $this->view->tabs = $tabs;
        $this->view->childrenContent = $childrenContent;
        $this->view->max = $this->_getParam('max');
    }
}