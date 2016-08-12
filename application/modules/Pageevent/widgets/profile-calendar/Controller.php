<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2010-07-02 19:54 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Pageevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pageevent_Widget_ProfileCalendarController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;

  public function indexAction()
  {
    // Set Current Path
    $path = Zend_Controller_Front::getInstance()->getControllerDirectory('pageevent');
    $path = dirname($path) . '/views/scripts';
    $this->view->addScriptPath($path);

    $page_enabled = Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('page');

    $api = Engine_Api::_()->core();
    $subject = ($api->hasSubject()) ? $api->getSubject('page') : null;

    // If page not installed
    if (!$page_enabled || !$subject || !Engine_Api::_()->getApi('core', 'page')->isAllowedView($subject)){
      return $this->setNoRender();
    }

    // Check Index Widget
    $widget_tbl = Engine_Api::_()->getDbTable('content', 'page');
    $select = $widget_tbl->select()
        ->where('page_id = ?', $subject->getIdentity())
        ->where('name = ?', 'pageevent.profile-event', 'STRING');

    if (!$widget_tbl->fetchRow($select)){
      return $this->setNoRender();
    }

    $tbl = Engine_Api::_()->getDbTable('pageevents', 'pageevent');
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->paginator = $paginator = $tbl->getPaginator(
      $subject->getIdentity(),
      $this->_getParam('show'),
      $this->_getParam('page', 1),
      $viewer->getIdentity()
    );

    if (!count($paginator)){
      return $this->setNoRender();
    }

    if ($this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ){
      $this->_childCount = $paginator->getTotalItemCount();
    }

  }

  public function getChildCount()
  {
    return $this->_childCount;
  }

}