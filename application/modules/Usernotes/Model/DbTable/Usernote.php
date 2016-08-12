<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Usernotes
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Usernote.php 2010-07-30 18:00 vadim $
 * @author     Vadim
 */

/**
 * @category   Application_Extensions
 * @package    Usernotes
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Usernotes_Model_DbTable_Usernote extends Engine_Db_Table
{
  protected $_primary = 'usernote_id';
  protected $_rowClass = 'Usernotes_Model_Usernote';

  public function getOwnerNotes($owner_id)
  {
    $where = 'owner_id = '. $owner_id;
    return $this->fetchAll($where);
  }
}