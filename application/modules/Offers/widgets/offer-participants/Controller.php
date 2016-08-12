<?php

class Offers_Widget_OfferParticipantsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    if (!Engine_Api::_()->core()->hasSubject('offer')) {
      return $this->setNoRender();
    }

    $subject = Engine_Api::_()->core()->getSubject();
    $users = Engine_Api::_()->offers()->getUsersSubscription($subject->getIdentity(), 9);

    if (count($users) == 0) {
      $this->setNoRender();
    }

    $this->view->offer_id = $subject->getIdentity();
    $this->view->users = $users;
  }
}