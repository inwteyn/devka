<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageblog
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2012-02-20 15:35 Ulan T $
 * @author     Ulan T
 */

class Pageblog_Widget_BlogsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    if( !Engine_Api::_()->getApi('core', 'authorization')->isAllowed('blog', null, 'view')) {
      return $this->setNoRender();
    }

    //Get settings
    $settings = Engine_Api::_()->getApi('settings', 'core');

    //Get Request
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $params = $request->getParams();
    $params['ipp'] = $settings->getSetting('pageblog.page', 10);

    $this->view->paginator = Engine_Api::_()->getApi('core', 'pageblog')->getBlogsPaginator($params);

    $formValues = array();

    if( !empty($params['search']) ) $formValues['search'] = $params['search'];
    if( !empty($params['orderby']) ) $formValues['orderby'] = $params['orderby'];
    if( !empty($params['category']) ) $formValues['category'] = $params['category'];

    $this->view->formValues = $formValues;
  }
}
