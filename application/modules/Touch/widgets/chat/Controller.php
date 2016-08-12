<?php
class Touch_Widget_ChatController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Must be logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer->getIdentity() ) {
      return $this->setNoRender();
    }

    $this->view->tmpId = md5('chat_box_' . microtime(true));
  }
}
