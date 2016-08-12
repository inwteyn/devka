<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2012-09-12 11:42:11 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Offers_Widget_PopularOffersController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $table = Engine_Api::_()->getDbTable('offers', 'offers');
    $limit = $this->_getParam('itemCountPerPage', 5);

    $params = array('filter' => 'upcoming', 'sort' => 'popular', 'limit' => $limit);
    $this->view->popularOffes = $popularOffersPaginator = $table->getOffersPaginator($params);

    $popularOffersPaginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 5));
    $popularOffersPaginator->setCurrentPageNumber($this->_getParam('page', 1));

    if (!$popularOffersPaginator->getTotalItemCount()){
      return $this->setNoRender();
    }
  }
}