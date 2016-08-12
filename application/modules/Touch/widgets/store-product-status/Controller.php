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
    
class Touch_Widget_StoreProductStatusController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
		if( !Engine_Api::_()->core()->hasSubject() ) return;

		$this->view->product = $product = Engine_Api::_()->core()->getSubject();
    $this->view->photo = $product->photo_id==0 ? false : true;
  }
}