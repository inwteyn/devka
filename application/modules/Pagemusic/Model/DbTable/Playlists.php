<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagemusic
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Playlists.php 2010-10-21 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Pagemusic
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagemusic_Model_DbTable_Playlists extends Engine_Db_Table
{
  protected $_name = 'page_music_playlists';

  protected $_rowClass = 'Pagemusic_Model_Playlist';

  public function getSelect($params)
  {
    $select = $this->select();

    if (!empty($params['page_id'])){
      $select
        ->where('page_id = ?', $params['page_id']);
    }

    if (!empty($params['user_id'])){
      $select
        ->where('owner_id = ?', $params['user_id']);
    }

    return $select;
  }

  public function getPaginator($params)
  {
    $select = $this->getSelect($params);
		$paginator = Zend_Paginator::factory($select);
		if (!empty($params['ipp'])){
			$paginator->setItemCountPerPage($params['ipp']);
		}

		if (!empty($params['p'])){
			$paginator->setCurrentPageNumber($params['p']);
		}

    return $paginator;
  }

  public function getCount($params)
  {
    $select = $this->getSelect($params);
    $name = $this->info('name');

    $select
      ->setIntegrityCheck(false)
      ->from($name, array('count' => 'COUNT(*)'));

    return (int)$this->getAdapter()->fetchOne($select);
  }

}
