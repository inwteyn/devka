<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagealbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: PageAlbums.php 2010-09-06 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Pagealbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagealbum_Model_DbTable_Pagealbums extends Engine_Db_Table
{
    protected $_name = 'page_albums';

    protected $_rowClass = 'Pagealbum_Model_Pagealbum';

    public function getAlbums($params = array())
    {
        if (!empty($params['count']) && $params['count']) {
            return $this->getAdapter()->fetchOne($this->getSelect($params));
        }

        return $this->getPaginator($params);
    }

    public function getAlbum($params = array())
    {
        $select = $this->getSelect($params);
        return $this->fetchRow($select);
    }

    public function getSelect($params = array())
    {
        $select = $this->select();

        $prefix = $this->getTablePrefix();

        $select
            ->setIntegrityCheck(false);

        if (!empty($params['count']) && $params['count']) {
            $select
                ->from($prefix . 'page_albums', array('count' => 'COUNT(*)'))
                ->group($prefix . 'page_albums.page_id');
        } else {
            $select
                ->from($prefix . 'page_albums');
        }

        $select
            ->joinLeft($prefix . 'users', $prefix . 'users.user_id = ' . $prefix . 'page_albums.user_id', array());

        if (!empty($params['page_id'])) {
            $select
                ->where($prefix . "page_albums.page_id = {$params['page_id']}");
        }

        if (!empty($params['user_id'])) {
            $select
                ->where($prefix . "page_albums.user_id = {$params['user_id']}");
        }

        if (!empty($params['album'])) {
            $select
                ->where($prefix . "page_albums.pagealbum_id = {$params['album']}");
        }

        if ($params['nonempty']) {

            $subquery = $this->select();

            $subquery
                ->setIntegrityCheck(false);

            $subquery
                ->from($prefix . 'page_album_photos', array('collection_id'))
                ->where($prefix . 'page_album_photos.collection_id != 0')
                ->group($prefix . 'page_album_photos.collection_id')
                ->having('count('.$prefix . 'page_album_photos.collection_id) > 0');

            $select
                ->where($prefix . "page_albums.pagealbum_id in ($subquery)");
        }

        return $select;
    }

    public function getPaginator($params = array())
    {
        $select = $this->getSelect($params);
        $paginator = Zend_Paginator::factory($select);

        if (!empty($params['ipp'])) {
            $paginator->setItemCountPerPage($params['ipp']);
        }

        if (!empty($params['p'])) {
            $paginator->setCurrentPageNumber($params['p']);
        }

        return $paginator;
    }

    public function createAlbum(array $values)
    {
        if (empty($values['page_id'])) {
            throw new Exception('Can not create album without page.');
        }

        if (empty($values['user_id'])) {
            $values['user_id'] = Engine_Api::_()->user()->getViewer()->getIdentity();
        }

        $row = $this->createRow();
        $row->setFromArray($values);
        $row->save();

        return $row;
    }

    public function getAllPhotos($page_id, $limit)
    {
        $table = Engine_Api::_()->getDbTable('pagealbumphotos', 'pagealbum');
        $select = $table->select()
            ->from(array('p' => $table->info('name')), 'p.*')
            ->join(array('a' => $this->info('name')), 'p.collection_id = a.pagealbum_id', array())
            ->where('a.page_id = ?', $page_id)
            ->order('rand()')
      ->limit($limit)
    ;

        return $table->fetchAll($select);
    }

  public function getSpecialAlbum(Page_Model_Page $page, $type)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $select = $this->select()
      ->where('type = ?', $type)
      ->where('user_id = ?', $viewer->getIdentity())
      ->where('page_id = ?', $page->getIdentity())
      ->order('pagealbum_id ASC')
      ->limit(1);

    $album = $this->fetchRow($select);

    // Create wall photos album if it doesn't exist yet
    if( null === $album ) {
      $translate = Zend_Registry::get('Zend_Translate');

      $album = $this->createRow();
      $album->user_id = $viewer->getIdentity();
      $album->title = $translate->_(ucfirst($type) . ' Photos');
      $album->page_id = $page->getIdentity();
      $album->type = $type;

      $album->save();

      // Authorizations

      $auth = Engine_Api::_()->authorization()->context;
      $auth->setAllowed($album, 'everyone', 'view',    true);
      $auth->setAllowed($album, 'everyone', 'comment', true);

    }

    return $album;
  }
}
