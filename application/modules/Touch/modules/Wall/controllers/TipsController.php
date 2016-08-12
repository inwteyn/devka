<?php

class Wall_TipsController extends Touch_Controller_Action_Standard
{

  public function indexAction()
  {
    $subject = Engine_Api::_()->core()->getSubject();
  }

  public function likeAction()
  {
    $is_unlike = $this->_getParam('is_unlike');
    
    $subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$viewer->getIdentity() || !$subject){
      return ;
    }

    if ($is_unlike){
      $subject->likes()->removeLike($viewer);
    } else {
      $subject->likes()->addLike($viewer);
    }

    $this->view->result = true;

  }
  

}
