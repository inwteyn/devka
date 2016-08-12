<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagealbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2012-02-13 17:46 ulan T $
 * @author     Ulan T
 */
class Pagealbum_Widget_BrowseSearchController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    $searchForm = new Pagealbum_Form_Search();

    if( !$viewer->getIdentity() ) {
       $searchForm->removeElement('view');
    }

    $request = Zend_Controller_Front::getInstance()->getRequest();

    $searchForm->setMethod('get')
      ->populate($request->getParams());
    $this->view->searchForm = $searchForm;
  }
}
