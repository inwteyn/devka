<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagevideo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2012-02-13 17:46 ulan T $
 * @author     Ulan T
 */


class Pagevideo_Widget_VideosController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    if( !Engine_Api::getInstance()->getApi('core', 'authorization')->isAllowed('page', null, 'view') ) {
      return $this->setNoRender();
    }

    // Get setting
    $settings = Engine_Api::_()->getApi('settings', 'core');

    $request = Zend_Controller_Front::getInstance()->getRequest();
    $params = $request->getParams();
    $params['ipp'] = $settings->getSetting('pagevideo.page', 10);

    $formValues = array();

    if( !empty($params['text']) ) {
      $formValues['text'] = $params['text'];
    }

    if( !empty($params['orderby']) ) {
      $formValues['orderby'] = $params['orderby'];
    }

    if( !empty($params['view']) ) {
      $formValues['view'] = $params['view'];
    }

    if( !empty($params['category']) ) {
      $formValues['category'] = $params['category'];
    }

    $this->view->formValues = $formValues;

    //Paginator
    $this->view->paginator = Engine_Api::_()->getApi('core', 'pagevideo')->getVideoPaginator($params);
  }
}
