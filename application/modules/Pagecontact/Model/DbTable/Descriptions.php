<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagecontact
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Descriptions.php 2011-07-13 16:01 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Pagecontact
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagecontact_Model_DbTable_Descriptions extends Engine_Db_Table
{
  protected $_rowClass = 'Pagecontact_Model_Description';

  public function getDescription($page_id)
  {
    if (!$page_id) {
      return false;
    }

    $description = $this->fetchRow($this->select()->where('page_id = ?', $page_id));

    if ($description !== null) {
      return $description->description;
    } else {
      return '';
    }
  }

	public function findPage_id($page_id)
	{
		$select = $this->select()
			->where('page_id = ?',  $page_id);

		$countPage_id = $this->fetchAll($select);
		return ($countPage_id->count() !== 0) ? true : false;
	}
}
