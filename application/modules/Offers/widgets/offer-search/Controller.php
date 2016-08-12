<?php

class Offers_Widget_OfferSearchController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $params = $request->getParams();

    if (!isset($params['filter']) || $params['filter'] != 'mine' && $params['filter'] != 'past') {
      $params['filter'] = 'upcoming';
    }
    if ($params['filter'] == 'mine') {
      if (!isset($params['my_offers_filter'])) {
        $params['my_offers_filter'] = 'upcoming';
      }
    }

    $this->view->form = $form = new Offers_Form_Search($params);
    $categories = Engine_Api::_()->offers()->getAllCategories();
    foreach($categories as $id => $title){
      $form->category_id->addMultiOption($id, $title);
    }
  }
}