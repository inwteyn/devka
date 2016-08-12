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

class Pageblog_Widget_PopularBlogsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $params['orderby'] = 'view_count';
    $params['ipp'] = $this->_getParam('itemCountPerPage', 4);
    $params['page'] = $this->_getParam('page', 1);

    $paginator = Engine_Api::_()->getApi('core','pageblog')->getBlogsPaginator($params);

    // Hide if nothing to show
    if( $paginator->getTotalItemCount() <= 0 ) {
      return $this->setNoRender();
    }

    $this->view->paginator = $paginator;

    // Check to make sure we have a title?
    if( '' == $this->getElement()->getTitle() ) {
      $this->getElement()->setTitle('Popular Blog Entries');
    }

  }
}