<?php
/***/
class Highlights_AdminFieldController extends Fields_Controller_AdminAbstract {

  protected $_fieldType = 'user';

  protected $_requireProfileType = true;

  public function indexAction()
  {
    parent::indexAction();
    $this->view->option_id = $option_id = (int) $this->_getParam('option_id', 1);
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('highlight_admin_main', array(), 'highlight_admin_main_userfields');
    $this->view->tipsMeta = Engine_Api::_()->highlights()->getTipsMeta('user', $option_id);
    $this->view->tipsMaps = Engine_Api::_()->highlights()->getTipsMap('user', $option_id);
  }
  public function addTipAction()
  {
    $tipsData = array(
      'tip_id' => (int) $this->_getParam('tip_id'),
      'option_id' => (int) $this->_getParam('option_id'),
      'tip_type' => (string) $this->_getParam('type', 'user'),
    );

    $newTip = Engine_Api::_()->getDbTable('maps', 'highlights')->addTip($tipsData);
    $this->view->html= $this->view->adminTipsMeta($newTip);
    $this->view->hehe = array('sadasd');
  }

  public function deleteTipAction()
  {
    $tip_id = (int) $this->_getParam('tip_id');
    Engine_Api::_()->getDbTable('maps', 'highlights')->deleteTip($tip_id);
  }

  public function orderTipsAction()
  {
    $tips_ids = $this->_getParam('tips_ids');
    Engine_Api::_()->getDbTable('maps', 'highlights')->orderTips($tips_ids);
  }
}