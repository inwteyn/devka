<?php

class Touch_Widget_ChatPanelController extends Engine_Content_Widget_Abstract{
  public function indexAction(){
    if(!Engine_Api::_()->touch()->isModuleEnabled('chat'))
      return $this->setNoRender();
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    if($viewer->getIdentity() ) {

      // Check if enabled
      $this->view->canChat = $canChat = Engine_Api::_()->authorization()->isAllowed('chat', $viewer, 'chat');
      $this->view->canIM = $canIM = Engine_Api::_()->authorization()->isAllowed('chat', $viewer, 'im');
      if( !$canIM ) return;

      // Check if friends-only or all members
      $memberIm = Engine_Api::_()->getApi('settings', 'core')->getSetting('chat.im.privacy', 'friends');
      $this->view->memberIm = 'everyone' === $memberIm
                ? 'true'
                : 'false';

      $this->view->identity = sprintf('%d', $viewer->getIdentity());
      $this->view->delay = Engine_Api::_()->getApi('settings', 'core')->getSetting('chat.general.delay', '5000');

      $this->view->canIM = ($canIM ? 'true' : 'false');
      $this->view->canChat = ($canChat ? 'true' : 'false');


    }

  }
}
