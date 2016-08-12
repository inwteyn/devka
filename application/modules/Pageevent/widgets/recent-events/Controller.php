<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.tpl 2012-02-20 15:35 Ulan T $
 * @author     Ulan T
 */

class Pageevent_Widget_RecentEventsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $params = $request->getParams();
    $params['order'] = 'creation_date DESC';
    $params['ipp'] = 5;
    $params['filter'] = null;
    $params['view'] = null;
    $params['search'] = null;
    $params['category_id'] = null;

    $paginator = Engine_Api::_()->getApi('core','pageevent')->getEventsPaginator($params);

    // Hide if nothing to show
    if( $paginator->getTotalItemCount() <= 0 ) {
      return $this->setNoRender();
    }
    $this->view->paginator = $paginator;

    // Check to make sure we have a title?
    if( '' == $this->getElement()->getTitle() ) {
      $this->getElement()->setTitle('Recent Events');
    }
  }
}