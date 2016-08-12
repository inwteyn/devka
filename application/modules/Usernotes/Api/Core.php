<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Usernotes
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2010-07-30 18:00 vadim $
 * @author     Vadim
 */

/**
 * @category   Application_Extensions
 * @package    Usernotes
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Usernotes_Api_Core extends Core_Api_Abstract
{
  public function getNotesPaginator($params = array())
  {
    $paginator = Zend_Paginator::factory($this->getNotesSelect($params));

    if (!empty($params['page']))
    {
      $paginator->setCurrentPageNumber($params['page']);
    }

    if (!empty($params['limit']))
    {
      $paginator->setItemCountPerPage($params['limit']);
    }

    if (empty($params['limit']))
    {
      $paginator->setItemCountPerPage(10);
    }

    return $paginator;
  }

  public function getNotesSelect($params = array())
  {
    $table = Engine_Api::_()->getDbtable('usernote', 'usernotes');;
    $select = $table->select();
    
    if (!empty($params['usernote_id']) && is_numeric($params['usernote_id']))
    {
      $select->where('usernote_id = ?', $params['usernote_id']);
    }

    if (!empty($params['user_id']) && is_numeric($params['user_id']))
    {
      $select->where('user_id = ?', $params['user_id']);
    }

    if (!empty($params['owner_id']) && is_numeric($params['owner_id']))
    {
      $select->where('owner_id = ?', $params['owner_id']);
    }

    return $select;
  }

  public function getUsernote($usernote_id)
  {
    $table  = Engine_Api::_()->getDbtable('usernote', 'usernotes');
    $select = $table->select()
      ->where('usernote_id = ?', $usernote_id);

    return $table->fetchRow($select);
  }


  public function getUsernoteByOwner($owner_id, $user_id)
  {
    $table  = Engine_Api::_()->getDbtable('usernote', 'usernotes');
    $select = $table->select()
      ->where('owner_id = ?', $owner_id)
      ->where('user_id = ?', $user_id);

    return $table->fetchRow($select);
  }


  public function deleteUsernote($usernote_id)
  {
    Engine_Api::_()->getItem('usernote', $usernote_id)->delete();

    return;
  }
}