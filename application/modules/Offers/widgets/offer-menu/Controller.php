<?php

class Offers_Widget_OfferMenuController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {
    if (Engine_Api::_()->core()->hasSubject('offer')) {
      $this->view->menuNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('offer_profile');
    } else {
      $this->setNoRender();
    }
  }

}