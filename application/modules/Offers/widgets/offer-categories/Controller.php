<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2012-08-12 11:42:11 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Offers_Widget_OfferCategoriesController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $params = $request->getParams();

    if (!isset($params['filter']) || $params['filter'] != 'mine' && $params['filter'] != 'past') {
      $params['filter'] = 'upcoming';
    }
    if ($params['filter'] == 'mine') {
      if (!isset($params['my_offers_filter'])) {
        $params['my_offers_filter'] = 'upcoming';
      }
    }

    $this->view->filter = $params['filter'];
    $this->view->my_offers_filter = isset($params['my_offers_filter']) ? $params['my_offers_filter'] : false;
    $this->view->params = $params;
		$this->view->categories = $categories = Engine_Api::_()->offers()->getCategories();

		if (!count($categories)){
			return $this->setNoRender();
		}
  }
}