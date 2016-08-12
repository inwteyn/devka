<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagemusic
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2012-02-20 15:35 Ulan T $
 * @author     Ulan T
 */

class Pagemusic_Widget_BrowseSearchController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $form = new Pagemusic_Form_Search();
    if( !Engine_Api::_()->user()->getViewer()->getIdentity() ) {
      $form->removeElement('show');
    }
    $params = Zend_Controller_Front::getInstance()->getRequest()->getParams();

    $form->populate($params);

    $this->view->form = $form;
  }
}
