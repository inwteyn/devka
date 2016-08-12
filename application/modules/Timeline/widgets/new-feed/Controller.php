<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2012-02-01 16:58:20 mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Timeline_Widget_NewFeedController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        if (!Engine_Api::_()->core()->hasSubject()) {
            return $this->setNoRender();
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();


        $this->view->type = $subject->getType();


        $element = $this->getElement();
        $action_id = (int)Zend_Controller_Front::getInstance()->getRequest()->getParam('action_id');
        $activeTab = $action_id ? 'activity.feed' : $this->_getParam('tab');


        $subjectType = $subject->getType();
        $deprecated = array(
            'like.status',
            'page.profile-options',
            'page.profile-photo'
        );

        $tabs = array();
        $params = array();
        foreach ($element->getElements() as $child) {
            if (null === $activeTab) {
                $activeTab = $child->getIdentity();
            }
            $child->clearDecorators();

            $id = $child->getIdentity();
            $title = $child->getTitle();
            $name = $child->getName();

            if($subjectType == 'page' && in_array($name, $deprecated)) {
                continue;
            }

            $childCount = null;
            if (method_exists($child, 'getWidget') && method_exists($child->getWidget(), 'getChildCount')) {
                $childCount = $child->getWidget()->getChildCount();
            }
            if (!$title) $title = $name;

            $content = $child->render();

            if ($child->getNoRender()) {
                continue;
            }

            $tabs[] = array(
                'id' => $id,
                'name' => $name,
                'title' => $title,
                'childCount' => $childCount,
                'content' => $content . PHP_EOL
            );

            $params[$name]['title'] = ( is_array($child->params) && (string)(array_key_exists('title', $child->params))) ? $child->params['title'] : "TIMELINE_Application";

            if (method_exists($child, 'getWidget') && method_exists($child->getWidget(), 'getChildCount')) {
                $params[$name]['count'] = $child->getWidget()->getChildCount();
            }
        }

        $this->view->tabs = $tabs;

        $applications = Engine_Api::_()->timeline()->getApplications($tabs);
        $active = array();
        $noneActive = array();

        foreach ($applications as $key => $application) {
            if (array_key_exists($key, $params) && array_key_exists('title', $application) && $params[$key]['title'] == "TIMELINE_Application") {
                $params[$key]['title'] = $application['title'];
            }

            if (
                !array_key_exists('add-link', $applications[$key]) ||
                array_key_exists($key, $params) && array_key_exists('count', $params[$key]) && $params[$key]['count'] > 0 ||
                array_key_exists('items', $application) && $application['items'] instanceof Zend_Paginator && $application['items']->count() > 0
            ) {
                $active[$key] = $application;
            } elseif (array_key_exists('add-link', $application)) {
                $noneActive[$key] = $application;
            }
        }

        $this->widget_params = $params;
        $this->view->activeApps = $active;
        $this->view->notActiveApps = $noneActive;
    }
}
