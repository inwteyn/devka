<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2012-02-20 15:35 Ulan T $
 * @author     Ulan T
 */

class Pageevent_Widget_PopularEventsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $params['order'] = 'view_count DESC';
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
      $this->getElement()->setTitle('Popular Events');
    }
  }
}