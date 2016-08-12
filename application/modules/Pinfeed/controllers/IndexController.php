<?php

class Pinfeed_IndexController extends Core_Controller_Action_Standard
{
  public function indexAction()
  {
    // check public settings
    $require_check = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.portal', 1);
    if(!$require_check){
      if( !$this->_helper->requireUser()->isValid() ) return;
    }

    if( !Engine_Api::_()->user()->getViewer()->getIdentity() ) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = null;
    if( Engine_Api::_()->core()->hasSubject() ) {
      $subject = Engine_Api::_()->core()->getSubject();
      if( !$subject->authorization()->isAllowed($viewer, 'view') || !in_array($subject->getType(), Engine_Api::_()->wall()->getSupportedItems())) {
        return $this->setNoRender();
      }
    }

    // Render
    $this->_helper->content
      //->setNoRender()
      ->setEnabled();
  }
  public function profileAction()
  {
    if( !Engine_Api::_()->core()->hasSubject() )
    {
      $id = $this->_getParam('id');

      // use viewer ID if not specified
      //if( is_null($id) )
      //  $id = Engine_Api::_()->user()->getViewer()->getIdentity();

      if( null !== $id )
      {
        $subject = Engine_Api::_()->user()->getUser($id);
        if( $subject->getIdentity() )
        {
          Engine_Api::_()->core()->setSubject($subject);
        }
      }
    }

    $this->_helper->requireSubject('user');
    // check public settings
    $require_check = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.portal', 1);
    if(!$require_check){
      if( !$this->_helper->requireUser()->isValid() ) return;
    }

    if( !Engine_Api::_()->user()->getViewer()->getIdentity() ) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = null;
    if( Engine_Api::_()->core()->hasSubject() ) {
      $subject = Engine_Api::_()->core()->getSubject();
      if( !$subject->authorization()->isAllowed($viewer, 'view') || !in_array($subject->getType(), Engine_Api::_()->wall()->getSupportedItems())) {
        return $this->setNoRender();
      }
    }

    // Render
    $this->_helper->content
      //->setNoRender()
      ->setEnabled();
  }


}

