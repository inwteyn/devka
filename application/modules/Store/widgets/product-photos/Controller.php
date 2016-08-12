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

class Store_Widget_ProductPhotosController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    if (Engine_Api::_()->core()->hasSubject()) {
      /**
       * @var $subject Store_Model_Product
       */
      $subject = Engine_Api::_()->core()->getSubject();

      if (!$subject instanceof Store_Model_Product) {
        return $this->setNoRender();
      }
    } else {
      return $this->setNoRender();
    }

    $subject->getBundle();

		/**
		 * @var $product Store_Model_Product
		 * @var $paginator Zend_Paginator
		 */
    $this->view->product = $product = $subject;
    $this->view->paginator = $paginator = $product->getCollectiblesPaginator();

    $paginator->setItemCountPerPage(100);

    $hecoreModules = Engine_Api::_()->getDbTable('modules', 'hecore');
    $this->view->enabled = $isEnabled = $hecoreModules->isModuleEnabled('storebundle');
    if($isEnabled) {
      $bundlesTable = Engine_Api::_()->getDbTable('storebundles', 'storebundle');
      $this->view->bundle = $bundlesTable->getProductBundle($subject->getIdentity());
    }
  }
}