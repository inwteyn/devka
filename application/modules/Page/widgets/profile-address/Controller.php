<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Widget_ProfileAddressController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
      // Get subject and check auth
      $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('page');
      $viewer = Engine_Api::_()->user()->getViewer();

      if (!$subject->authorization()->isAllowed($viewer, 'view')) {
          return $this->setNoRender();
      }

      $pageObject = Engine_Api::_()->getItem('page', $subject->getIdentity());

      $this->view->markers = $pageMarker = Engine_Api::_()->getApi('gmap', 'page')->getPageMarker($pageObject);

      if(!$pageMarker){
          return $this->setNoRender();
      }

      $this->view->bounds = Engine_Api::_()->getApi('gmap', 'page')->getMapBounds($pageMarker);

      $this->view->page_title = $pageObject->getTitle();
  }
}