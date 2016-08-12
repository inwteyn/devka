<?php

class Suggest_Widget_RecommendedFriendsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $type = 'friend';
    $viewer = Engine_Api::_()->user()->getViewer();
    $items = Engine_Api::_()->suggest()->getRecommendations($viewer->getIdentity(), $type);

    if (!$viewer->getIdentity()) {
      return $this->setNoRender();
    }

    if (!$items || (count($items['admin']) <= 0 && count($items['user']) <= 0)) {
      return $this->setNoRender();
    }

    $this->view->type = $type;
    $this->view->items = $items;
  }
}