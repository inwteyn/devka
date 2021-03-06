<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: ProfileController.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Event_ProfileController extends Touch_Controller_Action_Standard
{
  public function init()
  {
    // @todo this may not work with some of the content stuff in here, double-check
    $subject = null;
    if( !Engine_Api::_()->core()->hasSubject() )
    {
      $id = $this->_getParam('id');
      if( null !== $id )
      {
        $subject = Engine_Api::_()->getItem('event', $id);
        if( $subject && $subject->getIdentity() )
        {
          Engine_Api::_()->core()->setSubject($subject);
        }
      }
    }

    if ($subject){
      $this->_helper->requireSubject();
      $this->_helper->requireAuth()->setNoForward()->setAuthParams(
        $subject,
        Engine_Api::_()->user()->getViewer(),
        'view'
      );
    }

  }

  public function indexAction()
  {
    $subject = (Engine_Api::_()->core()->hasSubject()) ? Engine_Api::_()->core()->getSubject() : null;
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$this->_helper->requireAuth()->setAuthParams('event', null, 'view')->isValid() ) {
      return;
    }

    if( $viewer->isBlockedBy($subject))
    {
      return $this->_forward('requireauth', 'error', 'touch');
    }
    if( !$this->_helper->requireAuth()->setAuthParams($subject, $viewer, 'view')->isValid()) return;

    // Increment view count
    if( !$subject->getOwner()->isSelf($viewer) )
    {
      $subject->view_count++;
      $subject->save();
    }

    // Get styles
    $table = Engine_Api::_()->getDbtable('styles', 'core');
    $select = $table->select()
      ->where('type = ?', $subject->getType())
      ->where('id = ?', $subject->getIdentity())
      ->limit();

    $row = $table->fetchRow($select);

    if( null !== $row && !empty($row->style) ) {
      $this->view->headStyle()->appendStyle($row->style);
    }

    $content = Engine_Content::getInstance();
		$table = Engine_Api::_()->getDbtable('pages', 'touch');
		$content->setStorage($table);
		$this->_helper->content->setContent($content);

    // Render
    $this->_helper->content
        ->setNoRender()
        ->setEnabled()
        ;
  }
}