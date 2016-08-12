<?php

class Offers_Widget_OfferSubscribersController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {
    if (Engine_Api::_()->core()->hasSubject('offer')) {
      $offer = Engine_Api::_()->core()->getSubject();
    } else {
      return $this->setNoRender();
    }

    if (!$offer->isOwner()) {
      $this->setNoRender();
    }

    $this->view->subscribers = $offer->getSubscriptions();
    $this->view->offer = $offer;
  }

}