<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2012-09-13 11:42:11 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Offers_Widget_RecentOffersController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $table = Engine_Api::_()->getDbTable('offers', 'offers');
    $limit = $this->_getParam('itemCountPerPage', 5);

    $params = array('sort' => 'recent', 'limit' => $limit);
    $this->view->recentOffes = $recentOffersPaginator = $table->getOffersPaginator($params);

    $recentOffersPaginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 5));
    $recentOffersPaginator->setCurrentPageNumber($this->_getParam('page', 1));

    if (!$recentOffersPaginator->getTotalItemCount()){
      return $this->setNoRender();
    }
  }
}