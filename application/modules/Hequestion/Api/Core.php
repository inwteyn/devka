<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hequestion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 17.08.12 06:04 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Hequestion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Hequestion_Api_Core extends Core_Api_Abstract
{

  public function getItemTableClass($type)
  {
    if ($type == 'hequestion'){
      return 'Hequestion_Model_DbTable_Questions';
    } else if ($type == 'hequestion_option'){
      return 'Hequestion_Model_DbTable_Options';
    }
  }



  public function getQuestionVoters($params = array())
  {
    $keyword = (isset($param['keyword'])) ? $param['keyword'] : '';

    $question = Engine_Api::_()->getItem('hequestion', @$params['question_id']);
    if (!$question){
      return ;
    }
    $option = $question->getOption(@$params['option_id']);

    $viewer = Engine_Api::_()->user()->getViewer();

    return $option->getVoteMembers($viewer, $params);

  }



  public function getFriends($params)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $table = Engine_Api::_()->getItemTable('user');
    $prefix = $table->getTablePrefix();

    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from($prefix.'users')
      ->joinLeft($prefix.'user_membership', $prefix.'user_membership.user_id = '.$prefix.'users.user_id', array())
      ->where($prefix.'user_membership.resource_id = ?', $viewer->getIdentity())
      ->where($prefix.'user_membership.resource_approved = 1')
      ->where($prefix.'user_membership.user_approved = 1');



    if( !empty($params['keyword']) ) {
      $select->where($prefix.'users.displayname LIKE ?', '%'.$params['keyword'].'%');
    }

    return Zend_Paginator::factory($select);
  }




}