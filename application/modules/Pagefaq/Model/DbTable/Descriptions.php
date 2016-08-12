<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagefaq
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Descriptions.php 2011-09-28 15:18 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Pagefaq
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagefaq_Model_DbTable_Descriptions extends Engine_Db_Table
{
  public function saveDescription($params)
  {
    // if description exists then update
    if (!$params['description_id']) {
      $data = array(
        'page_id' => $params['page_id'],
        'description' => $params['description']
      );
      return $this->insert($data);
    }

    // if description not exist then insert
    $where = array('description_id = ?' => $params['description_id']);
	  return $this->update(array('page_id' => $params['page_id'], 'description' => $params['description']), $where);
  }

  public function getDescription($page_id)
  {
    return $this->fetchRow($this->select()->where('page_id = ?', $page_id));
  }
}
