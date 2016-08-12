<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagediscussion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Pagepost.php 2010-07-02 19:54 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Pagediscussion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagediscussion_Model_Pagepost extends Core_Model_Item_Abstract
{
  protected $_shortType = 'post';
  protected $_type = 'pagediscussion_pagepost';

  public function getHref()
  {
    if ($parent = $this->getParent()) {
      return $parent->getHref(array('child_id' => $this->getIdentity()));
    }
    return '';
  }

  public function getTitle()
  {
    if ($parent = $this->getParent()) {
      return $parent->getTitle();
    }
    return '';
  }

  public function getDescription()
  {
    return Engine_String::substr(preg_replace('/\[[^\[\]]+?\]/', '', $this->body), 0, 255);
  }

  public function getParent($recurseType = null)
  {
    return Engine_Api::_()->getDbTable('pagetopics', 'pagediscussion')->findRow($this->topic_id);
  }

  public function isFirstPost()
  {
    $table = Engine_Api::_()->getDbTable('pageposts', 'pagediscussion');
    $select = $table->select()
      ->from($table->info('name'), new Zend_Db_Expr('post_id'))
      ->where('topic_id = ?', $this->topic_id)
      ->order('post_id ASC');
    return ($table->getAdapter()->fetchOne($select) == $this->getIdentity());
  }

  protected function _insert()
  {
    if( $this->_disableHooks ) return;

    if ($topic = $this->getParent()) {
      $topic->lastposter_id = $this->user_id;
      $topic->modified_date = date('Y-m-d H:i:s');
      $topic->post_count++;
      $topic->save();
    }

    parent::_insert();
  }

  protected function _delete()
  {
    if( $this->_disableHooks ) return;

    if ($topic = $this->getParent())
    {
      $topic->post_count--;

      if ($topic->post_count == 0) {
        $topic->delete();
      } else {
        $topic->save();
      }
    }
    // Delete Page Search
    Engine_Api::_()->getDbTable('search', 'page')->deleteData(array(
      'object' => $this->getType(),
      'object_id' => $this->getIdentity(),
      'page_id' => $this->page_id
    ));


    // Delete Actions
    $tbl = Engine_Api::_()->getDbTable('attachments', 'activity');
    $action_ids = $tbl->select()
        ->from($tbl->info('name'), new Zend_Db_Expr('action_id'))
        ->where('type = ?', $this->getType())
        ->where('id = ?', $this->getIdentity())
        ->query()
        ->fetchAll(Zend_Db::FETCH_COLUMN);

    $tbl->delete(array(
      'type = ?' => $this->getType(),
      'id = ?' => $this->getIdentity()
    ));

    if ($action_ids){
      Engine_Api::_()->getDbTable('actions', 'activity')->delete(array(
        'action_id IN (?)' => $action_ids
      ));
    }

    parent::_delete();

  }

}