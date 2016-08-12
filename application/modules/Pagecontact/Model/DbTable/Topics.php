<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagecontact
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Topics.php 2011-07-13 16:01 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Pagecontact
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagecontact_Model_DbTable_Topics extends Engine_Db_Table
{
  protected $_rowClass = 'Pagecontact_Model_Topic';

  public function getTopics($page_id)
  {
    $select = $this->select()
      ->where('page_id = ?', $page_id)
			->order('topic_id ASC');

    $topics = $this->fetchAll($select);
    return $topics;
  }


	public function getEmails($page_id, $topic_id)
	{
		$select = $this->select()
			->from(array($this->info('name')), array('emails'))
			->where('page_id = ?', $page_id)
			->where('topic_id = ?', $topic_id);

		$query = $select->query();
		$emails = $query->fetchAll();
		$emails = $emails[0]['emails'];
		return $emails;
	}
}
