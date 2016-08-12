<?php
/**
 * SocialEngine
 *
 * @category Application_Extensions
 * @package Timeline
 * @copyright Copyright Hire-Experts LLC
 * @license http://www.hire-experts.com
 * @version $Id: Controller.php 2/9/12 11:03 AM mt.uulu $
 * @author Mirlan
 */

/**
 * @category Application_Extensions
 * @package Timeline
 * @copyright Copyright Hire-Experts LLC
 * @license http://www.hire-experts.com
 */


class Touch_Widget_TimelineHeaderController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject('user');

    $likeEnabled = false;
    if (Engine_Api::_()->touch()->isModuleEnabled('like')) {
      $likeEnabled = true;
    }

    $this->view->isLikeEnabled = $likeEnabled;
    if (!$subject->authorization()->isAllowed($viewer, 'view')) {

      $this->view->private = 1;

    } else {
      /**
       * Profile Applications
       */
      /**
       * @var $timeline Timeline_Api_Core
       * @var $contentTb Core_Model_DbTable_Content
       * @var $pagesTb Core_Model_DbTable_Pages
       * @var $page Core_Model_Page
       * @var $tabs Engine_Db_Table_Row
       */
      $active = array();
      $noneActive = array();
      $timeline = Engine_Api::_()->timeline();

      $contentTb = Engine_Api::_()->getDbTable('content', 'touch');
      $pagesTb = Engine_Api::_()->getDbTable('pages', 'touch');

      $select = $pagesTb->select()->where('name=?', 'user_profile_index')->limit(1);
      $page = $pagesTb->fetchRow($select);

      $select = $contentTb->select()->where('page_id=?', $page->getIdentity())->where('name=?', 'touch.container-tabs')->limit(1);
      if (null != ($tabs = $contentTb->fetchRow($select))) {
        $select = $contentTb->select()
          ->where('page_id=?', $page->getIdentity())
          ->where('parent_content_id=?', $tabs->content_id)
          ->where('type=?', 'widget');

        $tmp_widgets = $contentTb->fetchAll($select);
        $widgets = array();
        $params = array();

        foreach ($tmp_widgets as $content) {
          try {
            $tmp_params = (is_array($content->params))?$content->params:array();
            $tmp_params['from_tl'] = true;
            $child = new Engine_Content_Element_Widget(array(
              'identity' => $content->content_id,
              'name' => $content->name,
              'order' => $content->order,
              'params' => $tmp_params,
              'elements' => array()
            ));
            $child->render();


            if ($child->getNoRender()) {
              continue;
            }

            $widgets[$content->name] = $content;
            $params[$content->name]['title'] = (is_string($content->params['title']) && strlen($content->params['title']) > 0) ? $content->params['title'] : "TIMELINE_Application";

            if (method_exists($child, 'getWidget') && method_exists($child->getWidget(), 'getChildCount')) {
              $params[$content->name]['count'] = $child->getWidget()->getChildCount();
            }
          } catch (Exception $e) {
            print_log($e);
          }
        }
        $applications = $timeline->getApplications($tmp_widgets);

        foreach ($applications as $key => $application)
        {
          if (array_key_exists($key, $params) && array_key_exists('title', $application) && $params[$key]['title'] == "TIMELINE_Application") {
            $params[$key]['title'] = $application['title'];
          }

          if (
            !array_key_exists('add-link', $applications[$key]) ||
            array_key_exists($key, $params) && array_key_exists('count', $params[$key]) && $params[$key]['count'] > 0 ||
            $application['items'] instanceof Zend_Paginator && $application['items']->count() > 0
          ) {
            $active[$key] = $application;
          } elseif (array_key_exists('add-link', $application)) {
            $noneActive[$key] = $application;
          }
        }
      } else {
      }

      $this->view->isLikeEnabled = $likeEnabled;
      $this->view->private = 0;
      $this->view->profile_navigation = Engine_Api::_()->getApi('menus', 'touch')->getNavigation('user_profile');
      $this->view->widgets = $widgets;
      $this->view->widget_params = $params;
      $this->view->activeApplications = $active;
      $this->view->noneActiveApplications = $noneActive;
    }
  }
}