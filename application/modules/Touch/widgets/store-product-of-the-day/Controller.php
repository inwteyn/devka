<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2011-08-19 17:07:11 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Touch_Widget_StoreProductOfTheDayController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $productsTable = Engine_Api::_()->getItemTable('store_product');

		if (null == ($product = $productsTable->getProductOfTheDay())){
			$this->setNoRender( $this );
			return;
		}

    $this->view->product = $product;
    $this->view->owner = $owner = $product->getOwner();
    $this->view->photo = $product->photo_id==0 ? false : true;
	  $this->view->widget_title = $this->getElement()->getTitle();
	  $this->getElement()->setTitle('');
  }
}