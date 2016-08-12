<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: PageController.php 02.04.12 09:12 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */



class Hebadge_PageController extends Core_Controller_Action_User
{

  public function init()
  {
    if ($this->_getParam('page_id')){
      $subject = Engine_Api::_()->getItem('page', $this->_getParam('page_id'));
      if ($subject){
        Engine_Api::_()->core()->setSubject($subject);
      }
    }

    if (!Engine_Api::_()->core()->hasSubject() || !Engine_Api::_()->core()->getSubject('page')){
      die('Ups..');
    }
  }

  public function indexAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();

    $table = Engine_Api::_()->getDbTable('pagebadges', 'hebadge');
    $this->view->paginator = $paginator = $table->getPaginator();
    $paginator->setItemCountPerPage( 15 );
    $paginator->setCurrentPageNumber( $this->_getParam('page') );

    $ids = array();
    foreach ($paginator->getCurrentItems() as $item){
      $ids[] = $item->getIdentity();
    }

    $this->view->members = $table->getOwnerMembersByBadgeIds($ids, $subject);


    $this->view->simple_name = 'hebadge_page_badges';
    $this->view->params = array();

  }

  public function requestAction()
  {
    $badge_id = $this->_getParam('pagebadge_id');
    $badge = Engine_Api::_()->getItem('hebadge_pagebadge', $badge_id);
    if (!$badge){
      return ;
    }
    $viewer = Engine_Api::_()->user()->getViewer();

    $subject = null;
    if (Engine_Api::_()->core()->hasSubject()){
      $subject = Engine_Api::_()->core()->getSubject();
    }
    if (!$subject){
      return ;
    }


    $badge->addMember($subject);

    $message = strip_tags($this->_getParam('message'));
    $badge->getMember($subject)->setFromArray(array('message' => $message))->save();

  }

  public function requestCancelAction()
  {
    $badge_id = $this->_getParam('pagebadge_id');
    $badge = Engine_Api::_()->getItem('hebadge_pagebadge', $badge_id);
    if (!$badge){
      return ;
    }

    $viewer = Engine_Api::_()->user()->getViewer();

    $subject = null;
    if (Engine_Api::_()->core()->hasSubject()){
      $subject = Engine_Api::_()->core()->getSubject();
    }
    if (!$subject){
      return ;
    }

    $badge->removeMember($subject);

  }

  public function requestRejectAction()
  {
    $badge_id = $this->_getParam('pagebadge_id');
    $badge = Engine_Api::_()->getItem('hebadge_pagebadge', $badge_id);
    if (!$badge){
      return ;
    }

    $viewer = Engine_Api::_()->user()->getViewer();

    $subject = null;
    if (Engine_Api::_()->core()->hasSubject()){
      $subject = Engine_Api::_()->core()->getSubject();
    }
    if (!$subject){
      return ;
    }

    $badge->removeMember($subject);

  }


}