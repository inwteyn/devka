<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Headvancedmembers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: VerifyController.php 2015-10-06 16:58:20  $
 * @author     Bolot
 */

/**
 * @category   Application_Extensions
 * @package    Headvancedmembers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Headvancedmembers_Widget_VerifyController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer->getIdentity()) {
      return $this->setNoRender();
    }
    $settings = Engine_Api::_()->getApi('settings', 'core');
   
    if ($settings->getSetting('headvancedmembers.verification', 0) == 1) {
      return $this->setNoRender();
    }
    $subject = Engine_Api::_()->core()->getSubject();
    $table = Engine_Api::_()->getDbTable('status', 'headvancedmembers');
    $select  = $table->select()->where('user_id = ?',$subject->getIdentity());
    $row = $table->fetchRow($select);
    if($row){
      return $this->setNoRender();
    }
    $table = Engine_Api::_()->getDbTable('verification', 'headvancedmembers');
    $select  = $table->select()->where('user_id = ?',$subject->getIdentity())->where('verified_id = ?',$viewer->getIdentity());
    $rows = $table->fetchRow($select);
    $isSelf = 0;
    if($viewer->getIdentity() == $subject->getIdentity()){
      $isSelf = 1;
    }
    $disable = 0;
    if($rows){
      $disable = 1;
    }
    $this->view->disable = $disable;
    $this->view->subject_identity = $subject->getIdentity();
    $this->view->isSelf = $isSelf;
  }
}