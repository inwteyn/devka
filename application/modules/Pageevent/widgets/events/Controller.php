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

class Pageevent_Widget_EventsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    if( !Engine_Api::_()->getApi('core', 'authorization')->isAllowed('event', null, 'view') ) {
      return $this->setNoRender();
    }

    $settings = Engine_Api::_()->getApi('settings', 'core');

    $request = Zend_Controller_Front::getInstance()->getRequest();
    $params = $request->getParams();
    $params['ipp'] = $settings->getSetting('pageevent.page', 10);

    $this->view->paginator = Engine_Api::_()->getApi('core','pageevent')->getEventsPaginator($params);

    $formValues = array();

    if( !empty($params['view']) ) {
      $formValues['view'] = $params['view'];
    }

    if( !empty($params['order']) ) {
      $formValues['order'] = $params['order'];
    }

    if( !empty($params['category_id']) ) {
      $formValues['category_id'] = $params['category_id'];
    }

    $this->view->formValues = $formValues;
  }
}
