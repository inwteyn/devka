<?php

class Offers_Widget_OfferContactsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    if (!Engine_Api::_()->core()->hasSubject('offer')) {
      return $this->setNoRender();
    }

    $subject = Engine_Api::_()->core()->getSubject();
    $id = $subject->getIdentity();
    $contactsRow = Engine_Api::_()->offers()->getContactsOffer($id, array('country', 'state', 'city', 'address', 'website', 'phone'));

    $contacts = array();

    if (!$contactsRow) {
      return $this->setNoRender();
    }

    foreach($contactsRow as $key => $value){
      if (!empty($value)) {
        $contacts[$key] = $value;
      }
    }

    if (empty($contacts)) {
      return $this->setNoRender();
    }

    $this->view->cords = Engine_Api::_()->offers()->getPositionMarker($id);
    $this->view->phone = $contacts['phone']; unset($contacts['phone']);
    $this->view->website = $contacts['website']; unset($contacts['website']);
    $this->view->subject = $subject;
    $this->view->contacts = $contacts;
  }
}