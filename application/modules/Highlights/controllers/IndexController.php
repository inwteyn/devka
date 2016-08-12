<?php

class Highlights_IndexController extends Core_Controller_Action_Standard
{
  public function indexAction()
  {
    $this->view->someVar = 'someVal';
    $this->_helper->content
      //->setNoRender()
      ->setEnabled();
  }
  public function addAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!Engine_Api::_()->getDbTable('permissions', 'authorization')->isAllowed('highlight', $viewer, 'buy_highlight')) {
      return false;
    }
    if (!$this->getRequest()->isPost())
    {
      return;
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer->getIdentity()) {
      return $this->_helper->layout->disableLayout(true);
    }
    $translate = Zend_Registry::get('Zend_Translate');
    $balance = Engine_Api::_()->getItem('credit_balance', $viewer->getIdentity());
    if (!$balance) {
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 2000,
          'parentRefresh'=> false,
          'messages' => array($translate->translate('HIGHLIGHT_Not enough credits to buy highlight.')))
        );
      return;
    }
    $credits = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('highlight.cost', 10);

    if ($credits > $balance->current_credit) {
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 2000,
          'parentRefresh'=> false,
          'messages' => array($translate->translate('HIGHLIGHT_Not enough credits to buy highlight.')))
      );
      return;
    }

    $table = Engine_Api::_()->getDbTable('highlights', 'highlights');
    $db = $table->getAdapter();
    $db->beginTransaction();
    try {
      $check = $table->getByUserId($viewer->getIdentity());
      if (empty($check) || strtotime($check['date_finish']) < time()) {
        if (!isset($check['date_finish']))
        {
          $highlight = $table->createRow();
          $values = array(
            'owner_id' => $viewer->getIdentity(),
            'date_start' => new Zend_Db_Expr('NOW()'),
            'date_finish' => new Zend_Db_Expr('DATE_ADD(NOW(), INTERVAL ' . Engine_Api::_()->getApi('settings', 'core')->getSetting('highlight.num.days', 10) . ' DAY)'),
          );
          $highlight->setFromArray($values);
          $highlight->save();
        } else {
          $check['date_start'] = new Zend_Db_Expr('NOW()');
          $check['date_finish'] = new Zend_Db_Expr('DATE_ADD(NOW(), INTERVAL ' . Engine_Api::_()->getApi('settings', 'core')->getSetting('highlight.num.days', 10) . ' DAY)');
          $check->save();
        }
        $api = Engine_Api::_()->credit();
        $api->buyHighlight($viewer, (-1)*$credits);
        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => 2000,
            'parentRefresh'=> true,
            'messages' => array($translate->translate('HIGHLIGHT_You successfully joined to highlight list')))
        );
      } else {
        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => 2000,
            'parentRefresh'=> false,
            'messages' => array($translate->translate('HIGHLIGHT_You are already in highlight list')))
        );
      }
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->view->result = 1;
    return ;
  }
}
