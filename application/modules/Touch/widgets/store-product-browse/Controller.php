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
    
class Touch_Widget_StoreProductBrowseController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Product Search
    $translate = Zend_Registry::get('Zend_Translate');
    $request = Zend_Controller_Front::getInstance()->getRequest();

    $formValues = array(
      'search' => $request->getParam('search'),
//      'min_price' => $request->getParam('min_price', $translate->_('STORE_min')),
//      'max_price' => $request->getParam('max_price', $translate->_('STORE_max'))
    );

    $view = $this->view;
    $view->formFilter = $filterForm = new Touch_Form_Search();

    $filterForm->populate($formValues);

//    $view->topLevelId = $filterForm->getTopLevelId();
//    $view->topLevelValue = $filterForm->getTopLevelValue();


    // Browse Products {

    $page = $request->getParam('page', 1);
    $search = $request->getParam('search');
    $minPrice = $request->getParam('min_price');
    $maxPrice = $request->getParam('max_price');
    $sort = $request->getParam('sort', 'recent');
    $this->view->tag_id = $tag_id = $request->getParam('tag_id', 0);
    $category_id = $request->getParam('profile_type', 0);
    $this->view->cat_id = $category_id = ($category_id) ? $category_id : $request->getParam('cat', 0);

		/**
		 * @var $table Store_Model_DbTable_Products
		 */
		$table = Engine_Api::_()->getDbtable('products', 'store');
		$prefix = $table->getTablePrefix();

		$select = $table->select()
			->setIntegrityCheck(false)
  		->from(array('m'=>$prefix.'store_product_fields_maps'), array("m.*"))
			->where("m.option_id = ?", $category_id )
      ->where("m.field_id = ?", 1)
			->limit(1);


		if (null !== ( $row = $table->fetchRow($select))) {
      $this->view->child_id = $child_id = $row->child_id;
			$this->view->subCat_id = $subCat_id = ($request->getParam('field_' . $child_id)) ? $request->getParam('field_' . $child_id) : $request->getParam('sub_cat', 0);
		}

		/**
		 * @var $select Zend_Db_Table_Select
		 */

    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from($table->info('name'))
      ->joinLeft(array('v'=>$prefix.'store_product_fields_values'), "v.item_id = ".$prefix."store_products.product_id")
      ->joinLeft(array('o'=>$prefix.'store_product_fields_options'), "o.option_id = v.value AND o.field_id = 1", array("category" => "o.label"))
      ->group($prefix.'store_products.product_id');

		$select = $table->setStoreIntegrity($select);

		$productValues = array();

    $productValues = array_merge(array(
      'order' => $prefix.'store_products.product_id',
      'order_direction' => 'DESC',
    ), $productValues);

    $this->view->assign($productValues);
    $field = $request->getParam('field');
    if( !empty($field) )
    {
      $select
      	->where('v.field_id = 1 AND '.'v.value = ?', $field );
    }
    if (!empty($search)) {
      $select
      	->where($prefix.'store_products.title LIKE ?', '%' . $search . '%' );
    }
    if (!empty($minPrice) && is_numeric($minPrice)) {
      $select
      	->where($prefix.'store_products.price > ?', $minPrice);
    }
    if (!empty($maxPrice) && is_numeric($maxPrice)) {
      $select
      	->where($prefix.'store_products.price < ?', $maxPrice);
    }
    if (!empty($category_id)) {
      if (!empty($subCat_id)) {
        $select
        	->where('v.value = ?', $subCat_id);
      } else {
        $select
        	->where('o.option_id = ?', $category_id);
      }
    }// Tags
    if($tag_id !=0){
      $select
        ->joinLeft($prefix . 'core_tags', $prefix . "core_tags.tag_id = $tag_id")
        ->joinLeft($prefix . 'core_tagmaps', $prefix . "core_tagmaps.tag_id = $tag_id")
        ->where($prefix.'store_products.product_id = '.$prefix . 'core_tagmaps.resource_id')
        ->where($prefix . 'core_tagmaps.resource_type = ?', 'store_product');
    }

    switch ($sort) {
      case 'recent' :
        $select
          ->order($prefix.'store_products.creation_date DESC');
        break;
      case 'popular' :
        $select
          ->order($prefix.'store_products.view_count DESC');
        break;
      case 'sponsored' :
        $select
          ->where($prefix.'store_products.sponsored = ?', 1);
        break;
	    case 'featured' :
        $select
          ->where($prefix.'store_products.featured = ?', 1);
        break;
    }

		$select
			->order($prefix.'store_products.sponsored DESC')
			->order($prefix.'store_products.featured DESC');

    // Make paginator
		/**
		 * @var $viewer User_Model_User
		 * @var $paginator Zend_Paginator
		 */
    $viewer = Engine_Api::_()->user()->getViewer();
	  $this->view->is_like_enabled = Engine_Api::_()->getDbtable('modules', 'hecore')->isModuleEnabled('like');
    $this->view->view = $request->getParam('v', 'items');
    $this->view->sort = $sort;
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
		$paginator->setItemCountPerPage(15);
    $paginator->setCurrentPageNumber( $page );
    // } Browse Products
  }
}