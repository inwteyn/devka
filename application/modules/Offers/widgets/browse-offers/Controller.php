<?php

class Offers_Widget_BrowseOffersController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $params = $request->getParams();

    $viewer = Engine_Api::_()->user()->getViewer();
    $user_id = $viewer->getIdentity();
    $params['user_id'] = $user_id;

    if (!isset($params['filter']) || $params['filter'] != 'mine' && $params['filter'] != 'past') {
      $params['filter'] = 'upcoming';
    }
    if ($params['filter'] == 'mine') {
      if (!isset($params['my_offers_filter'])) {
        $params['my_offers_filter'] = 'upcoming';
      }
    }

    $this->view->isSuggestEnabled = $isSuggestEnabled = Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('suggest');

    $this->view->currentDate = $currentDate = date('Y-m-d h:i:s');
    $this->view->filter = $params['filter'];
    $this->view->my_offers_filter = $my_offers_filter = isset($params['my_offers_filter']) ? $params['my_offers_filter'] : false;

    $paginator = Engine_Api::_()->getDbTable('offers', 'offers')->getOffersPaginator($params);

    $offerTbl = Engine_Api::_()->getDbTable('subscriptions','offers');
    foreach ($paginator as $offer) {
      if ($offer->time_limit == 'limit' && $my_offers_filter == 'past' && $currentDate > $offer->endtime) {
        $offerTbl->update(array('status' => 'expired'), array('offer_id = ?' => $offer->offer_id));
      }
    }
    $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('offers', 'offers')->getOffersPaginator($params);

  }
}