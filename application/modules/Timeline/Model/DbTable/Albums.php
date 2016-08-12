<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Albums.php 2012-02-01 16:58:20 mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Timeline_Model_DbTable_Albums extends Album_Model_DbTable_Albums
{
    protected $_name = 'album_albums';

    protected $_rowClass = 'Timeline_Model_Album';

    public function getSpecialPageAlbum(User_Model_User $user, $page, $type)
    {
        if ($type != 'page_cover') {
            throw new Album_Model_Exception('Unknown special album type');
        }

        if(!$user->getidentity()) {
            return false;
        }


        $isPageAlbumEnabled = Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('pagealbum');
        $table = $this;

        $album = null;
        $page_album = null;

        if ($isPageAlbumEnabled) {
            $pa_table = Engine_Api::_()->getDbTable('pagealbums', 'pagealbum');
            $pa_select = $pa_table->select()
                ->where('user_id = ?', $user->getIdentity())
                ->where('page_id = ?', $page->getIdentity())
                ->where('type = ?', $type)
                ->order('pagealbum_id ASC')
                ->limit(1);
            $page_album = $pa_table->fetchRow($pa_select);
            if ($page_album) return $page_album;
        }
        $select = $table->select()
            ->where('owner_type = ?', $user->getType())
            ->where('owner_id = ?', $user->getIdentity())
            ->where('type = ?', $type)
            ->order('album_id ASC')
            ->limit(1);
        $album = $table->fetchRow($select);
        if ($album) return $album;

        if (!$isPageAlbumEnabled) {
            if (null === $album) {
                $translate = Zend_Registry::get('Zend_Translate');

                $title = 'Page Cover Photos';

                $album = $table->createRow();
                if ($isPageAlbumEnabled) {
                    $album->page_id = $page->getIdentity();
                    $album->user_id = $user->getIdentity();
                    $album->title = $translate->_($title);
                } else {
                    $album->owner_type = 'user';
                    $album->owner_id = $user->getIdentity();
                    $album->title = $translate->_($title);
                    $album->type = $type;
                }

                if ($type == 'message') {
                    $album->search = 0;
                } else {
                    $album->search = 1;
                }

                $album->save();

                // Authorizations
                $auth = Engine_Api::_()->authorization()->context;
                if ($type != 'message' && $type != 'page_cover') {
                    $auth->setAllowed($album, 'everyone', 'view', true);
                    $auth->setAllowed($album, 'everyone', 'comment', true);
                }

                return $album;
            }
        } else {
            if (null === $page_album) {
                $translate = Zend_Registry::get('Zend_Translate');

                $title = 'Page Cover Photos';

                $page_album = $pa_table->createRow();
                $page_album->page_id = $page->getIdentity();
                $page_album->user_id = $user->getIdentity();
                $page_album->title = $translate->_($title);
                $page_album->type = 'page_cover';

                $page_album->save();

                // Authorizations
                $auth = Engine_Api::_()->authorization()->context;
                if ($type != 'message' && $type != 'page_cover') {
                    $auth->setAllowed($page_album, 'everyone', 'view', true);
                    $auth->setAllowed($page_album, 'everyone', 'comment', true);
                }
            }
            return $page_album;
        }
        return false;
    }

    public function getSpecialAlbum(Core_Model_Item_Abstract $subject, $type)
    {
        /*if (!in_array($type, array('wall', 'profile', 'message', 'blog', 'cover', 'page_cover', 'birth'))) {
            throw new Album_Model_Exception('Unknown special album type');
        }*/

        $item_id = $subject->getIdentity();
        $item_type = $subject->getType();

        $select = $this->select()
            ->where('owner_type = ?', $item_type)
            ->where('owner_id = ?', $item_id)
            ->where('type = ?', $type)
            ->order('album_id ASC')
            ->limit(1);
        $album = $this->fetchRow($select);

        // Create wall photos album if it doesn't exist yet
        if (null === $album) {
            $translate = Zend_Registry::get('Zend_Translate');
            $title = ucfirst($item_type) . ' ' . ucfirst($type) . ' Photos';

            $album = $this->createRow();

            $album->owner_type = $item_type;
            $album->owner_id = $item_id;
            $album->title = $translate->_($title);
            $album->type = $type;

            if ($type == 'message') {
                $album->search = 0;
            } else {
                $album->search = 1;
            }

            $album->save();
            // Authorizations
            $auth = Engine_Api::_()->authorization()->context;
            if ($type != 'message' && $type != 'page_cover') {
                $auth->setAllowed($album, 'everyone', 'view', true);
                $auth->setAllowed($album, 'everyone', 'comment', true);
            }
        }
        return $album;
    }


    public function getSpecialEventAlbum(Core_Model_Item_Abstract $subject, $type)
    {

        $item_id = $subject->getIdentity();
        $item_type = $subject->getType();

        $table = Engine_Api::_()->getDbtable('albums', 'event');

        $select = $table->select()
            ->where('event_id = ?', $item_id)
            ->where('type = ?', $type)
            ->order('album_id ASC')
            ->limit(1);
        $album = $this->fetchRow($select);

        // Create wall photos album if it doesn't exist yet
        if (null === $album) {
            $translate = Zend_Registry::get('Zend_Translate');
            $title = 'Event Cover Photos';
            $album = $table->createRow();

            $album->event_id = $item_id;
            $album->title = $translate->_($title);
            $album->type = $type;
            $album->search = 1;

            $album->save();

            $auth = Engine_Api::_()->authorization()->context;
            $auth->setAllowed($album, 'everyone', 'view', true);
            $auth->setAllowed($album, 'everyone', 'comment', true);
        }
        return $album;
    }


    public function getSpecialGroupAlbum(Core_Model_Item_Abstract $subject, $type = 'cover')
    {
        $item_id = $subject->getIdentity();
        $item_type = $subject->getType();

        $table = Engine_Api::_()->getDbtable('albums', 'group');

        $select = $table->select()
            ->where('group_id = ?', $item_id)
            ->where('type = ?', $type)
            ->order('album_id ASC')
            ->limit(1);
        $album = $this->fetchRow($select);

        // Create wall photos album if it doesn't exist yet
        if (null === $album) {
            $translate = Zend_Registry::get('Zend_Translate');
            $title = 'Group Cover Photos';
            $album = $table->createRow();

            $album->group_id = $item_id;
            $album->title = $translate->_($title);
            $album->type = $type;
            $album->search = 1;

            $album->save();

            $auth = Engine_Api::_()->authorization()->context;
            $auth->setAllowed($album, 'everyone', 'view', true);
            $auth->setAllowed($album, 'everyone', 'comment', true);
        }
        return $album;
    }

}
