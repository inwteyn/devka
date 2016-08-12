<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminMembersContoller.php 02.04.12 09:12 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */




class Hebadge_AdminMembersController extends Core_Controller_Action_Admin
{

  public function indexAction()
  {

    $table = Engine_Api::_()->getDbTable('users', 'user');

    $this->view->params = $params = array(
      'displayname' => $this->_getParam('displayname'),
      'username' => $this->_getParam('username'),
      'email' => $this->_getParam('email'),
      'level' => $this->_getParam('level')
    );


    $levels = Engine_Api::_()->getDbtable('levels', 'authorization')->getLevelsAssoc();
    $levelMultiOptions = array(0 => ' ');
    foreach ($levels as $key => $value) {
      $levelMultiOptions[$key] = $value;
    }

    $select = $table->select();

    if (!empty($params['displayname'])){
      $select->where('displayname LIKE ?', '%' . $params['displayname'] . '%');
    }
    if (!empty($params['username'])){
      $select->where('username LIKE ?', $params['username'] . '%');
    }
    if (!empty($params['email'])){
      $select->where('email LIKE ?', $params['email'] . '%');
    }
    if (!empty($params['level'])){
      if (in_array($params['level'], $levelMultiOptions)){
        $select->where('email LIKE ?', $params['email'] . '%');
      }
    }

  }

}