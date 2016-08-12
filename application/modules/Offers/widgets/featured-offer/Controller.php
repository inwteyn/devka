<?php

class Offers_Widget_FeaturedOfferController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $offer = Engine_Api::_()->offers()->getSpecialOffer('featured');

    if (!$offer) {
      return $this->setNoRender();
    }

    $this->view->offer = $offer;

  }
}