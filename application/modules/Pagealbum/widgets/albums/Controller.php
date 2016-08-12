<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagealbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2012-02-13 17:46 ulan T $
 * @author     Ulan T
 */

class Pagealbum_Widget_AlbumsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    if( !Engine_Api::_()->getApi('core', 'authorization')->isAllowed('album', null, 'view')) {
      return $this->setNoRender();
    }

    $settings = Engine_Api::_()->getApi('settings', 'core');

    $request = Zend_Controller_Front::getInstance()->getRequest();
    $params = $request->getParams();
    $params['ipp'] = $settings->getSetting('pagealbum.page', 10);

    if( !empty($params['view']) ) $this->view->view = $params['view'];
    $this->view->paginator = Engine_Api::_()->getApi('core', 'pagealbum')->getAlbumPaginator($params);

    $searchParams = array();

    if( !empty($params['search']) )
      $searchParams['search'] = $params['search'];

    if( !empty($params['sort']) )
      $searchParams['sort'] = $params['sort'];

    if( !empty($params['view']) )
      $searchParams['view'] = $params['view'];

    if( !empty($params['category_id']) )
      $searchParams['category_id'] = $params['category_id'];

    $this->view->searchParams = $searchParams;

  }
}
