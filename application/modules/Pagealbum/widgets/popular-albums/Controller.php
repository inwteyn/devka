<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagealbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2012-02-20 15:35 Ulan T $
 * @author     Ulan T
 */

class Pagealbum_Widget_PopularAlbumsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $params['sort'] = 'popular';
    $params['ipp'] = $this->_getParam('itemCountPerPage', 4);
    $params['page'] = $this->_getParam('page', 1);

    $paginator = Engine_Api::_()->getApi('core','pagealbum')->getAlbumPaginator($params);

    // Hide if nothing to show
    if( $paginator->getTotalItemCount() <= 0 ) {
      return $this->setNoRender();
    }
    $this->view->paginator = $paginator;

    // Check to make sure we have a title?
    if( '' == $this->getElement()->getTitle() ) {
      $this->getElement()->setTitle('Popular Albums');
    }

  }
}