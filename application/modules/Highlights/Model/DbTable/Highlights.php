<?php
/***/
class Highlights_Model_DbTable_Highlights extends Engine_Db_Table {
  protected $_rowClass = 'Highlights_Model_Highlight';
  public function getHighlightsSelect($params = array())
  {
    $tblUser = Engine_Api::_()->getDbtable('users', 'user');
    $select = $tblUser->select()
      ->from(array('user' => $tblUser->info('name')), array('user_id'))
      ->setIntegrityCheck(false)
      ->joinLeft(array('h' => $this->info('name')), 'h.owner_id = user.user_id AND h.date_finish > NOW()');
    if (isset($params['highlight']) && $params['highlight'] == 1) {
      $select->where('h.owner_id IS NOT NULL');
    }

    if (isset($params['displayname']) && trim($params['displayname']) != '') {
      $select->where("user.displayname LIKE '%" . trim($params['displayname']) ."%'");
    }

    if (isset($params['email']) && trim($params['email']) != '') {
      $select->where("user.email LIKE '%" . trim($params['email']) ."%'");
    }

    if (isset($params['level_id']) && intval($params['level_id']) > 0) {
      $select->where("user.level_id = " . intval($params['level_id']));
    }
    return $this->fetchAll($select);
  }

  public function getHighlight($id)
  {
    $highlight = $this->select()
      ->where('highlight_id=?', $id);
    return $this->fetchRow($highlight);
  }

  public function getHighlightsPaginator($params = array())
  {
    $paginator = Zend_Paginator::factory($this->getHighlightsSelect($params));
    if (!empty($params['page'])) {
      $paginator->setCurrentPageNumber($params['page']);
    }
    if (!empty($params['limit'])) {
      $paginator->setItemCountPerPage($params['limit']);
    }

    if (empty($params['limit'])) {
      $page = Engine_Api::_()->getApi('settings', 'core')->getSetting('article.per.page', 10);
      $paginator->setItemCountPerPage($page);
    }
    return $paginator;
  }

  public function getByUserId($userId)
  {
    $highlight = $this->select()
      ->where('owner_id=?', $userId);

    return $this->fetchRow($highlight);
  }

  public function getLogs($params = array())
  {
    $actiontypeTable = Engine_Api::_()->getDbTable('actionTypes', 'credit');
    $logTable = Engine_Api::_()->getDbTable('logs', 'credit');
    $logs = $logTable->select()
      ->from(array('l' => $logTable->info('name')), array('user_id', 'creation_date', 'credit'))
      ->setIntegrityCheck(false)
      ->joinLeft(array('t' => $actiontypeTable->info('name')), "t.action_name = 'buy_highlight' AND t.action_id = l.action_id", array())
      ->where('l.credit <= 0')
      ->order('l.creation_date DESC');

    return $this->fetchAll($logs);
  }
  public function  getLogsPaginator($params = array())
  {
    $paginator = Zend_Paginator::factory($this->getLogs($params));
    if (!empty($params['page'])) {
      $paginator->setCurrentPageNumber($params['page']);
    }
    $paginator->setItemCountPerPage(15);
    return $paginator;
  }
}