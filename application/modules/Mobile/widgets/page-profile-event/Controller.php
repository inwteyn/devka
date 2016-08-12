<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2011-02-14 07:29:38 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    

class Mobile_Widget_PageProfileEventController extends Engine_Content_Widget_Abstract
{

  protected $_childCount;
  protected $_widget_url;

  public function indexAction()
  {
    $api = Engine_Api::_()->core();
    $subject_id = ($api->hasSubject()) ? $api->getSubject()->getIdentity() : 0;

    if (!Engine_Api::_()->mobile()->checkPageWidget($subject_id, 'mobile.page-profile-event')){
      return $this->setNoRender();
    }

    $subject = Engine_Api::_()->core()->getSubject('page');
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$subject->authorization()->isAllowed($viewer, 'view')){
      return $this->setNoRender();
    }

    $this->_widget_url = $this->view->url(array(
      'action' => 'index',
      'page_id' => $subject->getIdentity()
    ),'page_event');

    $tbl = Engine_Api::_()->getDbTable('pageevents', 'pageevent');

    $this->view->paginator = $paginator = $tbl->getPaginator(
      $subject->getIdentity(),
      $this->_getParam('show'),
      $this->_getParam('page', 1),
      $viewer->getIdentity()
    );

    if ($this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ){
      $this->_childCount = $tbl->getCount($subject->getIdentity());
    }
  }

  public function getChildCount()
  {
    return $this->_childCount;
  }

  public function getHref()
  {
    return $this->_widget_url;
  }

}