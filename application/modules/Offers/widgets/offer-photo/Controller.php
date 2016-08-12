<?php

class Offers_Widget_OfferPhotoController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {
    if (Engine_Api::_()->core()->hasSubject('offer')) {
      $this->view->offer = $offer = Engine_Api::_()->core()->getSubject();
    }
    else {
      $this->setNoRender();
    }

  }

}