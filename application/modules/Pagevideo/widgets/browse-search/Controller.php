<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagevideo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2012-02-13 17:46 ulan T $
 * @author     Ulan T
 */

class Pagevideo_Widget_BrowseSearchController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $searchForm = new Pagevideo_Form_Search();
    if( !Engine_Api::_()->user()->getViewer()->getIdentity() ) {
      $searchForm->removeElement('view');
    }

    $module = Engine_Api::_()->getDbTable('modules', 'core')->getModule('video');

    if( $module && $module->enabled ) {
      // prepare categories
      $categories = Engine_Api::_()->video()->getCategories();
      $categories_prepared[0] = "All Categories";
      foreach ($categories as $category){
        $categories_prepared[$category->category_id] = $category->category_name;
      }

    // category field
      $searchForm->addElement('Select', 'category', array(
        'label' => 'Category',
        'multiOptions' => $categories_prepared,
        'onchange' => 'this.form.submit();'
      ));
    }

    $searchForm->orderby->addMultiOption('rating', 'Highest Rated');
    $request = Zend_Controller_Front::getInstance()->getRequest();

    $searchForm->populate($request->getParams());
    $this->view->searchForm = $searchForm;
  }
}
