<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Views.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Model_DbTable_Views extends Engine_Db_Table
{
	protected $_rowClass = 'Page_Model_View';

  public function getOldRowsInfo()
  {
    $select = $this->select()
      ->from($this, array('count' => new Zend_Db_Expr('COUNT(*)'), 'min_id' => new Zend_Db_Expr('MIN(`view_id`)')))
      ->where(new Zend_Db_Expr('ISNULL(country)'));

    return $this->getAdapter()->fetchRow($select);
  }

  public function upgradeOldRows()
  {
    $params = $this->getOldRowsInfo();

    if (!$params['count']) {
      return;
    }

    $select = $this->select()
      ->from($this, array('view_id'))
      ->where(new Zend_Db_Expr('ISNULL(country)'))
      ->order('view_id ASC')
      ->limit(1);

    $start_id = $this->getAdapter()->fetchOne($select);

    if (!$start_id) {
      return;
    }

    $select = $this->select()
      ->from($this, array('view_id'))
      ->where(new Zend_Db_Expr('ISNULL(country)'))
      ->order('view_id ASC')
      ->limit(1, 100);

    $end_id = $this->getAdapter()->fetchOne($select);

    if (!$end_id) {
      $select = $this->select()
        ->from($this, array(new Zend_Db_Expr('MAX(view_id)')))
        ->where(new Zend_Db_Expr('ISNULL(country)'))
        ->order('view_id ASC');

      $end_id = $this->getAdapter()->fetchOne($select);
    }

    $locationsTbl = Engine_Api::_()->getDbTable('locations', 'page');
    $locName = $locationsTbl->info('name');
    $viewName = $this->info('name');

    $sql = "UPDATE `{$viewName}` "
      . "INNER JOIN `{$locName}` ON (ISNULL(`{$viewName}`.country) AND `{$viewName}`.view_id >= {$start_id}  AND `{$viewName}`.view_id <= {$end_id} AND `{$locName}`.begin_num <= `{$viewName}`.ip AND `{$locName}`.end_num >= `{$viewName}`.ip) "
      . "SET `{$viewName}`.country = `{$locName}`.name";

    $this->getAdapter()->query($sql);

    $sql = "UPDATE `{$viewName}` SET `{$viewName}`.country = 'localhost' "
      . "WHERE ISNULL(`{$viewName}`.country) AND `{$viewName}`.view_id >= {$start_id}  AND `{$viewName}`.view_id <= {$end_id}";

    $this->getAdapter()->query($sql);
  }
  public function send(Core_Model_Item_Abstract $user, $recipients, $title, $body, $attachment = null)
  {
    $resource = null;

    // Case: single user
    if( $recipients instanceof User_Model_User ) {
      $recipients = array($recipients->getIdentity());
    }
    // Case: group/event members
    else if( $recipients instanceof Core_Model_Item_Abstract &&
      method_exists($recipients, 'membership') ) {
      $resource = $recipients;
      $recipients = array();
      foreach( $resource->membership()->getMembers() as $member ) {
        if( $member->getIdentity() != $user->getIdentity() ) {
          $recipients[] = $member->getIdentity();
        }
      }
    }
    // Case: single id
    else if( is_numeric($recipients) ) {
      $recipients = array($recipients);
    }
    // Case: array
    else if( is_array($recipients) && !empty($recipients) ) {
      // Ok
    }
    // Whoops
    else {
      throw new Messages_Model_Exception("A message must have recipients");
    }

    // Create conversation
    $conversation = $this->createRow();
    $conversation->setFromArray(array(
      'user_id' => $user->getIdentity(),
      'title' => $title,
      'recipients' => count($recipients),
      'modified' => date('Y-m-d H:i:s'),
      'locked' => ( $resource ? true : false ),
      'resource_type' => ( !$resource ? null : $resource->getType() ),
      'resource_id' => ( !$resource ? 0 : $resource->getIdentity() ),
    ));
    $conversation->save();

    // Create message
    $message = Engine_Api::_()->getItemTable('messages_message')->createRow();
    $message->setFromArray(array(
      'conversation_id' => $conversation->getIdentity(),
      'user_id' => $user->getIdentity(),
      'title' => $title,
      'body' => $body,
      'date' => date('Y-m-d H:i:s'),
      'attachment_type' => ( $attachment ? $attachment->getType() : '' ),
      'attachment_id' => ( $attachment ? $attachment->getIdentity() : 0 ),
    ));
    $message->save();

    // Create sender outbox
    Engine_Api::_()->getDbtable('recipients', 'messages')->insert(array(
      'user_id' => $user->getIdentity(),
      'conversation_id' => $conversation->getIdentity(),
      'outbox_message_id' => $message->getIdentity(),
      'outbox_updated' => date('Y-m-d H:i:s'),
      'outbox_deleted' => 0,
      'inbox_deleted' => 1,
      'inbox_read' => 1
    ));

    // Create recipients inbox
    foreach( $recipients as $recipient_id ) {
      Engine_Api::_()->getDbtable('recipients', 'messages')->insert(array(
        'user_id' => $recipient_id,
        'conversation_id' => $conversation->getIdentity(),
        'inbox_message_id' => $message->getIdentity(),
        'inbox_updated' => date('Y-m-d H:i:s'),
        'inbox_deleted' => 0,
        'inbox_read' => 0,
        'outbox_message_id' => 0,
        'outbox_deleted' => 1,
      ));
    }

    return $conversation;
  }
}