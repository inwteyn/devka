<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Markers.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Headvancedmembers_Model_DbTable_Markers extends Engine_Db_Table
{
 //protected $_rowClass = 'Headvancedmembers_Model_Marker';

  public function getByPageIds($pageIds = array())
  {
    if (!$pageIds) {
      return array();
    }

    $select = $this->select()
      ->where('user_id IN (?)', $pageIds);
  
    return $this->fetchAll($select);
  }
}