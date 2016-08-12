<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2012-02-13 17:46 ulan T $
 * @author     Ulan T
 */

class Pageevent_Widget_UpcomingController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $params = Zend_Controller_Front::getInstance()->getRequest()->getParams();

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $params['ipp'] = $settings->getSetting('event.page', 10);
    $params['filter'] = 'future';

    $paginator = Engine_Api::_()->getApi('core','pageevent')->getEventsPaginator($params);

    if( $paginator->getTotalItemCount() <= 0 ) {
      return $this->setNoRender();
    }

    $this->view->paginator = $paginator;

    // Check to make sure we have a title?
    if( '' == $this->getElement()->getTitle() ) {
      $this->getElement()->setTitle('Upcoming Events');
    }
  }
}