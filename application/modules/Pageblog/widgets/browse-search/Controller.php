<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageblog
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2012-02-20 15:35 Ulan T $
 * @author     Ulan T
 */

class Pageblog_Widget_BrowseSearchController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $searchForm = new Pageblog_Form_Search();

    $request = Zend_Controller_Front::getInstance()->getRequest();

    $viewer = Engine_Api::_()->user()->getViewer();

    if( !$viewer->getIdentity() ) {
      $searchForm->removeElement('show');
    }

    $searchForm->populate($request->getParams());

    $this->view->searchForm = $searchForm;
  }
}
