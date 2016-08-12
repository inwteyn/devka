<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Content.php 2010-09-09 10:15 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Updates_Model_DbTable_Content extends Engine_Db_Table
{
    protected $_serializedColumns = array('params');

    public function getContent($id)
    {
        $widgetTb = Engine_Api::_()->getDbtable('widgets', 'updates');
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from($this->info('name'))
            ->joinLeft($widgetTb->info('name'), $widgetTb->info('name') . '.id=' . $this->info('name') . '.widget_id', array($widgetTb->info('name') . '.title AS title', $widgetTb->info('name') . '.description AS description'))
            ->where($this->info('name') . '.id = ?', $id)
            ->limit(1);
        return $this->fetchRow($select);
    }

    public function prepareContentStructure($content, $current_id = null)
    {
        $parent_id = null;
        if (null !== $current_id) {
            $parent_id = $current_id;
        }

        $children = $content->getRowsMatching('parent_id', $parent_id);

        $struct = array();
        foreach ($children as $child) {
            $elStruct = $child->toArray();
            $elStruct['elements'] = $this->prepareContentStructure($content, $child->id);
            $struct[] = $elStruct;
        }

        return $struct;
    }

    public function getContentWidget($id)
    {
        $moduleTb = Engine_Api::_()->getDbtable('modules', 'core');
        $widgetTb = Engine_Api::_()->getDbtable('widgets', 'updates');

        $contentSelect = $this->select()
            ->setIntegrityCheck(false)
            ->from(array('c' => $this->info('name')), 'c.*')
            ->joinLeft(array('w' => $widgetTb->info('name')), 'w.name = c.name', array('w.module', 'w.description', 'w.module', 'w.last_sent_id', 'w.structure', 'w.blacklist')
            )
            ->where('c.type = ?', 'widget')
            ->where('c.id=?', $id)
            ->order('c.order ASC');

        return $this->fetchRow($contentSelect);
    }

    public function getContentWidgets()
    {
        $moduleTb = Engine_Api::_()->getDbtable('modules', 'core');
        $widgetTb = Engine_Api::_()->getDbtable('widgets', 'updates');
        $settings = Engine_Api::_()->getApi('settings', 'core');

        // Unite Album and Page Album if enabled "Unite"
        $unitePageAlbums = $settings->__get('page.browse.pagealbum');
        if ($unitePageAlbums) {
            if ($moduleTb->isModuleEnabled('pagealbum') && $moduleTb->isModuleEnabled('album')) {
                $select = $this->select()
                    ->from(array($this->info('name')))
                    ->where('name = "new_albums_page"');
                $pagealbums = $this->fetchAll($select);

                if ($pagealbums->count() == 0) {
                    $widgetSelect = $widgetTb->select()
                        ->from(array($widgetTb->info('name')), array('id'))
                        ->where('name = "new_albums_page"');
                    $widget = $widgetTb->fetchRow($widgetSelect);

                    $this->insert(array(
                        'name' => 'new_albums_page',
                        'type' => 'widget',
                        'widget_id' => $widget->id,
                        'parent_id' => 7,
                        'order' => 100,
                        'params' => '{"title":"New Page Albums","count":"3","name":"new_albums_page"}',
                    ));
                }
            }
        } else {
            $this->delete(array('name = ?' => 'new_albums_page'));
        }

        // Unite Event and Page Event if enabled "Unite"
        $unitePageEvents = $settings->__get('page.browse.pageevent');
        if ($unitePageEvents) {
            if ($moduleTb->isModuleEnabled('pageevent') && $moduleTb->isModuleEnabled('event')) {
                $select = $this->select()
                    ->from(array($this->info('name')))
                    ->where('name = "new_events_page"');
                $pageEvents = $this->fetchAll($select);

                if ($pageEvents->count() == 0) {
                    $widgetSelect = $widgetTb->select()
                        ->from(array($widgetTb->info('name')), array('id'))
                        ->where('name = "new_events_page"');
                    $widget = $widgetTb->fetchRow($widgetSelect);

                    $this->insert(array(
                        'name' => 'new_events_page',
                        'type' => 'widget',
                        'widget_id' => $widget->id,
                        'parent_id' => 7,
                        'order' => 100,
                        'params' => '{"title":"New Page Events","count":"4","name":"new_events_page"}',
                    ));
                }
            }
        } else {
            $this->delete(array('name = ?' => 'new_events_page'));
        }

        // Unite Video and Page Video if enabled "Unite"
        $unitePageVideos = $settings->__get('page.browse.pagevideo');
        if ($unitePageVideos) {
            if ($moduleTb->isModuleEnabled('pagevideo') && $moduleTb->isModuleEnabled('video')) {
                $select = $this->select()
                    ->from(array($this->info('name')))
                    ->where('name = "new_videos_page"');
                $pageVidoes = $this->fetchAll($select);

                if ($pageVidoes->count() == 0) {
                    $widgetSelect = $widgetTb->select()
                        ->from(array($widgetTb->info('name')), array('id'))
                        ->where('name = "new_videos_page"');
                    $widget = $widgetTb->fetchRow($widgetSelect);

                    $this->insert(array(
                        'name' => 'new_videos_page',
                        'type' => 'widget',
                        'widget_id' => $widget->id,
                        'parent_id' => 7,
                        'order' => 100,
                        'params' => '{"title":"New Page Videos","count":"3","name":"new_videos_page"}',
                    ));
                }
            }
        } else {
            $this->delete(array('name = ?' => 'new_videos_page'));
        }

        // Unite Blog and Page Blog if enabled "Unite"
        $unitePageBlogs = $settings->__get('page.browse.pageblog');
        if ($unitePageBlogs) {
            if ($moduleTb->isModuleEnabled('pageblog') && $moduleTb->isModuleEnabled('blog')) {
                $select = $this->select()
                    ->from(array($this->info('name')))
                    ->where('name = "new_blogs_page"');
                $pageBlogs = $this->fetchAll($select);

                if ($pageBlogs->count() == 0) {
                    $widgetSelect = $widgetTb->select()
                        ->from(array($widgetTb->info('name')), array('id'))
                        ->where('name = "new_blogs_page"');
                    $widget = $widgetTb->fetchRow($widgetSelect);

                    $this->insert(array(
                        'name' => 'new_blogs_page',
                        'type' => 'widget',
                        'widget_id' => $widget->id,
                        'parent_id' => 7,
                        'order' => 100,
                        'params' => '{"title":"New Page Blogs","count":"4","name":"new_blogs_page"}',
                    ));
                }
            }
        } else {
            $this->delete(array('name = ?' => 'new_blogs_page'));
        }

        // Unite Music and Page Music if enabled "Unite"
        $unitePageMusics = $settings->__get('page.browse.pagemusic');
        if ($unitePageMusics) {
            if ($moduleTb->isModuleEnabled('pagemusic') && $moduleTb->isModuleEnabled('music')) {
                $select = $this->select()
                    ->from(array($this->info('name')))
                    ->where('name = "new_playlists_page"');
                $pageBlogs = $this->fetchAll($select);

                if ($pageBlogs->count() == 0) {
                    $widgetSelect = $widgetTb->select()
                        ->from(array($widgetTb->info('name')), array('id'))
                        ->where('name = "new_playlists_page"');
                    $widget = $widgetTb->fetchRow($widgetSelect);

                    $this->insert(array(
                        'name' => 'new_playlists_page',
                        'type' => 'widget',
                        'widget_id' => $widget->id,
                        'parent_id' => 7,
                        'order' => 100,
                        'params' => '{"title":"New Page Musics","count":"3","name":"new_playlists_page"}',
                    ));
                }
            }
        } else {
            $this->delete(array('name = ?' => 'new_playlists_page'));
        }

        $contentSelect = $this->select()
            ->setIntegrityCheck(false)
            ->from(array('c' => $this->info('name')), 'c.*')
            ->joinLeft(array('w' => $widgetTb->info('name')), 'w.name = c.name', array('w.module', 'w.description', 'w.module', 'w.last_sent_id', 'w.structure', 'w.blacklist')
            )
            ->where('c.type = ?', 'widget')
            ->order('c.widget_id ASC');

        $contentWidgets = $this->fetchAll($contentSelect);

        $contents = array();
        $parentWidget = array();
        foreach ($contentWidgets as $widget) {
            if ($moduleTb->isModuleEnabled($widget->module)) {
                $title = (array_key_exists('title', $widget->params)) ? $widget->params['title'] : null;
                $count = (array_key_exists('count', $widget->params)) ? $widget->params['count'] : null;
                $html = (array_key_exists('html', $widget->params)) ? $widget->params['html'] : null;
                $select = (array_key_exists('select', $widget->params)) ? $widget->params['select'] : null;

                $contents[] = array(
                    'id' => $widget->id,
                    'title' => $title,
                    'select' => $select,
                    'description' => $widget->description,
                    'type' => $widget->type,
                    'name' => $widget->name,
                    'module' => $widget->module,
                    'count' => $count,
                    'html' => $html,
                    'last_sent_id' => $widget->last_sent_id,
                    'structure' => $widget->structure,
                    'blacklist' => $widget->blacklist,
                );

                // Changing parent_id and order for new_albums_page
                if ($widget->name == 'new_albums') {
                    $parentWidget['new_albums']['parent_id'] = $widget->parent_id;
                    $parentWidget['new_albums']['order'] = $widget->order;
                }
                if ($widget->name == 'new_albums_page') {
                    $last_key = end(array_keys($contents));
                    $contents[$last_key]['unite_widget'] = 1;
                    $parent_id = $parentWidget['new_albums']['parent_id'];
                    $order = $parentWidget['new_albums']['order'];
                    $where = array('name = ?' => 'new_albums_page');
                    $this->update(array('parent_id' => $parent_id, 'order' => $order), $where);
                }

                // Changing parent_id and order for new_events_page
                if ($widget->name == 'new_events') {
                    $parentWidget['new_events']['parent_id'] = $widget->parent_id;
                    $parentWidget['new_events']['order'] = $widget->order;
                }
                if ($widget->name == 'new_events_page') {
                    $last_key = end(array_keys($contents));
                    $contents[$last_key]['unite_widget'] = 1;
                    $parent_id = $parentWidget['new_events']['parent_id'];
                    $order = $parentWidget['new_events']['order'];
                    $where = array('name = ?' => 'new_events_page');
                    $this->update(array('parent_id' => $parent_id, 'order' => $order), $where);
                }

                // Changing parent_id and order for new_videos_page
                if ($widget->name == 'new_videos') {
                    $parentWidget['new_videos']['parent_id'] = $widget->parent_id;
                    $parentWidget['new_videos']['order'] = $widget->order;
                }
                if ($widget->name == 'new_videos_page') {
                    $last_key = end(array_keys($contents));
                    $contents[$last_key]['unite_widget'] = 1;
                    $parent_id = $parentWidget['new_videos']['parent_id'];
                    $order = $parentWidget['new_videos']['order'];
                    $where = array('name = ?' => 'new_videos_page');
                    $this->update(array('parent_id' => $parent_id, 'order' => $order), $where);
                }

                // Changing parent_id and order for new_blogs_page
                if ($widget->name == 'new_blogs') {
                    $parentWidget['new_blogs']['parent_id'] = $widget->parent_id;
                    $parentWidget['new_blogs']['order'] = $widget->order;
                }
                if ($widget->name == 'new_blogs_page') {
                    $last_key = end(array_keys($contents));
                    $contents[$last_key]['unite_widget'] = 1;
                    $parent_id = $parentWidget['new_blogs']['parent_id'];
                    $order = $parentWidget['new_blogs']['order'];
                    $where = array('name = ?' => 'new_blogs_page');
                    $this->update(array('parent_id' => $parent_id, 'order' => $order), $where);
                }

                // Changing parent_id and order for new_playlists_page
                if ($widget->name == 'new_playlists') {
                    $parentWidget['new_playlists']['parent_id'] = $widget->parent_id;
                    $parentWidget['new_playlists']['order'] = $widget->order;
                }
                if ($widget->name == 'new_playlists_page') {
                    $last_key = end(array_keys($contents));
                    $contents[$last_key]['unite_widget'] = 1;
                    $parent_id = $parentWidget['new_playlists']['parent_id'];
                    $order = $parentWidget['new_playlists']['order'];
                    $where = array('name = ?' => 'new_playlists_page');
                    $this->update(array('parent_id' => $parent_id, 'order' => $order), $where);
                }
            }
        }

        return $contents;
    }

    public function contentStructure($where = '')
    {
        if ($where == '') {
            $select = $this->select();
        } else {
            $select = $this->select()->where($where);
        }

        $contentList = $this->fetchAll($select);
        $enabled_moduleList = array();

        foreach ($contentList as $content) {
            if ($content->module != 'updates') {
                $module_name = $this->getItemModule($content->name);

                if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled($module_name)) {
                    $enabled_moduleList[] = "'" . $content->name . "'";
                }
            } else {
                $enabled_moduleList[] = "'" . $content->name . "'";
            }
        }

        $enabled_moduleList = (count($enabled_moduleList) > 0) ? implode(", ", $enabled_moduleList) : "' '";

        $select = $this->select()->where("name IN($enabled_moduleList)")->order('order ASC');
        return $this->fetchAll($select);
    }

    public function _reorderContentStructure($a, $b)
    {
        $sample = array('left', 'middle', 'right');
        $av = $a['name'];
        $bv = $b['name'];
        $ai = array_search($av, $sample);
        $bi = array_search($bv, $sample);
        if ($ai === false && $bi === false) return 0;
        if ($ai === false) return -1;
        if ($bi === false) return 1;
        $r = ($ai == $bi ? 0 : ($ai < $bi ? -1 : 1));
        return $r;
    }

    /**
     * @param string $method
     * @param array $params
     * @return
     */
    public function getContentData($method = '', $params = array())
    {
        if (method_exists($this, $method)) {
            return $this->$method($params);
        }
    }

    protected function new_members($content)
    {
        $table = Engine_Api::_()->getItemTable('user');
        $select = $table->select()
            ->where('search=1 && enabled=1 && verified=1')
            ->where('user_id>?', $content['last_sent_id'])
            ->order('user_id DESC')
            ->limit($content['count']);

        if (isset($content['displayed'])) {
            $select->where("user_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("user_id NOT IN ({$content['blacklist']})");
        }

        return $table->fetchAll($select);
    }

    protected function new_actions($content)
    {
        $usTable = Engine_Api::_()->getDbtable('actionSettings', 'activity');
        $sTable = Engine_Api::_()->getDbtable('stream', 'activity');
        $actionTypesTbl = Engine_Api::_()->getDbtable('actionTypes', 'activity');

        $allTypes = array('type', 'friends', 'friends_follow', 'network_join', 'post', 'post_self', 'profile_photo_update', 'signup', 'status', 'tagged');
        $actionTypes = $actionTypesTbl->select()
            ->from(array($actionTypesTbl->info('name')), 'type')
            ->where("type IN(?)", $allTypes)
            ->where('enabled = ?', 1)
            ->where('displayable > ?', 3)
            ->query()
            ->fetchAll(null, 'type');

        $groupActionTypes = array('group_create', 'group_join', 'group_photo_upload', 'group_promote', 'group_topic_create', 'group_topic_reply');
        $selectActionTypes = $actionTypesTbl->select()
            ->from(array($actionTypesTbl->info('name')), 'type')
            ->where("type IN(?)", $groupActionTypes)
            ->where('enabled = ?', 1)
            ->where('displayable = ?', 7)
            ->limit(6)
            ->query()
            ->fetchAll(null, 'type');

        $actionTypes = array_merge($actionTypes, $selectActionTypes);
        $actionTypes = ($actionTypes) ? $actionTypes : array('default');

        $table = Engine_Api::_()->getDbtable('actions', 'activity');

        $select = $table->select()
            ->setIntegrityCheck(false)
            ->from(array('a' => $table->info('name')))
            ->joinLeft(array('us' => $usTable->info('name')), "us.user_id=a.object_id && us.type=a.type", array())
            ->joinLeft(array('s' => $sTable->info('name')), "s.action_id=a.action_id", array())
            ->where("a.type IN(?)", $actionTypes)
            ->where("a.action_id >? ", $content['last_sent_id'])
            ->where("ISNULL(us.publish) OR us.publish = ?", 1)
            ->where("s.target_type=?", 'everyone')
            ->order('a.action_id DESC')
            ->limit($content['count']);

        if (isset($content['displayed'])) {
            $select->where("a.action_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("a.action_id NOT IN ({$content['blacklist']})");
        }

        return $table->fetchAll($select);
    }
    protected function popular_actions($content)
    {
        $usTable = Engine_Api::_()->getDbtable('actionSettings', 'activity');
        $sTable = Engine_Api::_()->getDbtable('stream', 'activity');
        $actionTypesTbl = Engine_Api::_()->getDbtable('actionTypes', 'activity');

        $allTypes = array('type', 'friends', 'friends_follow', 'network_join', 'post', 'post_self', 'profile_photo_update', 'signup', 'status', 'tagged');
        $actionTypes = $actionTypesTbl->select()
          ->from(array($actionTypesTbl->info('name')), 'type')
          ->where("type IN(?)", $allTypes)
          ->where('enabled = ?', 1)
          ->where('displayable > ?', 3)
          ->query()
          ->fetchAll(null, 'type');

        $groupActionTypes = array('group_create', 'group_join', 'group_photo_upload', 'group_promote', 'group_topic_create', 'group_topic_reply');
        $selectActionTypes = $actionTypesTbl->select()
          ->from(array($actionTypesTbl->info('name')), 'type')
          ->where("type IN(?)", $groupActionTypes)
          ->where('enabled = ?', 1)
          ->where('displayable = ?', 7)
          ->limit(6)
          ->query()
          ->fetchAll(null, 'type');

        $actionTypes = array_merge($actionTypes, $selectActionTypes);
        $actionTypes = ($actionTypes) ? $actionTypes : array('default');

        $table = Engine_Api::_()->getDbtable('actions', 'activity');
        $unixtime = strtotime("-2 month");

        $wweek2 =  date("Y-m-d h:i:s",$unixtime);
        $select = $table->select()
          ->setIntegrityCheck(false)
          ->from(array('a' => $table->info('name')))
          ->joinLeft(array('us' => $usTable->info('name')), "us.user_id=a.object_id && us.type=a.type", array())
          ->joinLeft(array('s' => $sTable->info('name')), "s.action_id=a.action_id", array())
          ->joinLeft(array('l' => new Zend_Db_Expr('(SELECT resource_id, COUNT(*) AS likes FROM engine4_activity_likes GROUP BY resource_id)')), "a.action_id = l.resource_id", array(new Zend_Db_Expr('IFNULL(l.likes,0) as likes')))
          ->joinLeft(array('c' =>  new Zend_Db_Expr('(SELECT resource_id, COUNT(*) AS comments FROM engine4_activity_comments GROUP BY resource_id)')), "a.action_id = c.resource_id", array(new Zend_Db_Expr('IFNULL(c.comments,0) as comments'),new Zend_Db_Expr('IFNULL(c.comments,0)+IFNULL(l.likes,0) AS total')))

          ->where("a.type IN(?)", $actionTypes)
          ->where("a.action_id >? ", $content['last_sent_id'])
          ->where("a.date >? ", $wweek2)
          ->where("ISNULL(us.publish) OR us.publish = ?", 1)
          ->where("s.target_type=?", 'everyone')
          ->order('total DESC')
          ->limit($content['count']);

        if (isset($content['displayed'])) {
            $select->where("a.action_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("a.action_id NOT IN ({$content['blacklist']})");
        }
        return $table->fetchAll($select);
    }
    protected function new_albums($content)
    {
        $table = Engine_Api::_()->getDbTable('albums', 'album');

        $select = $table->select()
            ->setIntegrityCheck(false)
            ->from(array('a' => $table->info('name')))
            ->join(array('au' => $content['authTb']), "au.resource_type='album' && au.resource_id=a.album_id", array())
            ->where("au.action = ?", 'view')
            ->where("au.role = ?", 'everyone')
            ->where('a.album_id > ?', $content['last_sent_id'])
            ->order('a.album_id DESC')
            ->limit($content['count']);

        if (isset($content['displayed'])) {
            $select->where("a.album_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("a.album_id NOT IN ({$content['blacklist']})");
        }

        return $table->fetchAll($select);
    }

    protected function new_albums_page($content)
    {
        $pageAlbumsTbl = Engine_Api::_()->getDbTable('pagealbums', 'pagealbum');
        $pageTbl = Engine_Api::_()->getDbTable('pages', 'page');

        $select = $pageAlbumsTbl->select()
            ->setIntegrityCheck(false)
            ->from(array('pa' => $pageAlbumsTbl->info('name')))
            ->join(array('au' => $content['authTb']), "au.resource_type='page' AND au.resource_id=pa.page_id", array())
            ->join(array('p' => $pageTbl->info('name')), "p.page_id=pa.page_id", array('url'))
            ->where("au.action = ?", 'view')
            ->where("au.role = ?", 'everyone')
            ->where('pa.pagealbum_id > ?', $content['last_sent_id'])
            ->order('pa.pagealbum_id DESC')
            ->limit($content['count']);

        if (isset($content['displayed'])) {
            $select->where("pa.pagealbum_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("pa.pagealbum_id NOT IN ({$content['blacklist']})");
        }

        return $pageAlbumsTbl->fetchAll($select);
    }

    protected function album_of_the_day($content)
    {
        $albumsTbl = Engine_Api::_()->getDbTable('albums', 'album');
        $itemsOfTheDayTbl = Engine_Api::_()->getDbTable('itemofthedays', 'sitealbum');

        $select = $albumsTbl->select()
            ->setIntegrityCheck(false)
            ->from(array('a' => $albumsTbl->info('name')))
            ->join(array('au' => $content['authTb']), "au.resource_type='album' AND au.resource_id=a.album_id", array())
            ->join(array('i' => $itemsOfTheDayTbl->info('name')), 'i.resource_id=a.album_id', array())
            ->where("au.action = ?", 'view')
            ->where("au.role = ?", 'everyone')
            ->where('a.album_id > ?', $content['last_sent_id'])
            ->order('Rand() ASC')
            ->limit(1);

        if (isset($content['displayed'])) {
            $select->where("a.album_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("a.album_id NOT IN ({$content['blacklist']})");
        }

        return $albumsTbl->fetchAll($select);
    }

    protected function photo_of_the_day($content)
    {
        $photosTbl = Engine_Api::_()->getDbTable('photos', 'album');
        $albumsTbl = Engine_Api::_()->getDbTable('albums', 'album');
        $itemsOfTheDayTbl = Engine_Api::_()->getDbTable('itemofthedays', 'sitealbum');

        $select = $photosTbl->select()
            ->setIntegrityCheck(false)
            ->from(array('ph' => $photosTbl->info('name')))
            ->join(array('a' => $albumsTbl->info('name')), 'a.album_id=ph.album_id')
            ->join(array('au' => $content['authTb']), "au.resource_type='album' AND au.resource_id=ph.album_id", array())
            ->join(array('i' => $itemsOfTheDayTbl->info('name')), 'i.resource_id=ph.photo_id', array())
            ->where('i.resource_type = ?', 'album_photo')
            ->where("au.action = ?", 'view')
            ->where("au.role = ?", 'everyone')
            ->where('ph.photo_id > ?', $content['last_sent_id'])
            ->order('Rand() ASC')
            ->limit(1);

        if (isset($content['displayed'])) {
            $select->where("ph.photo_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("ph.photo_id NOT IN ({$content['blacklist']})");
        }

        return $photosTbl->fetchAll($select);
    }

    protected function new_blogs($content)
    {
        $table = Engine_Api::_()->getDbtable('blogs', 'blog');
        $select = $table->select()
            ->setIntegrityCheck(false)
            ->from(array('b' => $table->info('name')))
            ->join(array('a' => $content['authTb']), "a.resource_type='blog' && a.resource_id=b.blog_id")
            ->where("a.action = ?", 'view')
            ->where("a.role = ?", 'everyone')
            ->where('b.blog_id>?', $content['last_sent_id'])
            ->where('b.draft = 0')
            ->order('b.blog_id DESC')
            ->limit($content['count']);

        if (isset($content['displayed'])) {
            $select->where("b.blog_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("b.blog_id NOT IN ({$content['blacklist']})");
        }

        return $table->fetchAll($select);
    }

    protected function new_blogs_page($content)
    {
        $pageBlogsTbl = Engine_Api::_()->getDbtable('pageblogs', 'pageblog');
        $pageTbl = Engine_Api::_()->getDbTable('pages', 'page');

        $select = $pageBlogsTbl->select()
            ->setIntegrityCheck(false)
            ->from(array('pb' => $pageBlogsTbl->info('name')))
            ->join(array('a' => $content['authTb']), "a.resource_type='page' AND a.resource_id=pb.page_id", array())
            ->join(array('p' => $pageTbl->info('name')), "p.page_id=pb.page_id", array('url'))
            ->where("a.action = ?", 'view')
            ->where("a.role = ?", 'everyone')
            ->where('pb.pageblog_id > ?', $content['last_sent_id'])
            ->order('pb.pageblog_id DESC')
            ->limit($content['count']);

        if (isset($content['displayed'])) {
            $select->where("pb.pageblog_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("pb.pageblog_id NOT IN ({$content['blacklist']})");
        }

        return $pageBlogsTbl->fetchAll($select);
    }

    protected function new_younet_blogs($content)
    {
        $moduleTb = Engine_Api::_()->getDbtable('modules', 'core');
        if ($moduleTb->isModuleEnabled('blog')) {
            return;
        }
        $table = Engine_Api::_()->getDbtable('blogs', 'ynblog');
        $select = $table->select()
            ->setIntegrityCheck(false)
            ->from(array('b' => $table->info('name')))
            ->join(array('a' => $content['authTb']), "a.resource_type='blog' && a.resource_id=b.blog_id")
            ->where("a.action = ?", 'view')
            ->where("a.role = ?", 'everyone')
            ->where('b.blog_id>?', $content['last_sent_id'])
            ->where('b.draft = 0')
            ->order('b.blog_id DESC')
            ->limit($content['count']);

        if (isset($content['displayed'])) {
            $select->where("b.blog_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("b.blog_id NOT IN ({$content['blacklist']})");
        }

        return $table->fetchAll($select);
    }

    protected function new_classifieds($content)
    {
        $table = Engine_Api::_()->getDbtable('classifieds', 'classified');
        $select = $table->select()
            ->setIntegrityCheck(false)
            ->from(array('c' => $table->info('name')))
            ->join(array('a' => $content['authTb']), "a.resource_type='classified' && a.resource_id=c.classified_id")
            ->where("a.action = ?", 'view')
            ->where("a.role = ?", 'everyone')
            ->where('c.classified_id>?', $content['last_sent_id'])
            ->where('c.closed = 0')
            ->order('c.classified_id DESC')
            ->limit($content['count']);

        if (isset($content['displayed'])) {
            $select->where("c.classified_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("c.classified_id NOT IN ({$content['blacklist']})");
        }
        return $table->fetchAll($select);
    }

    protected function new_events($content)
    {
        $table = Engine_Api::_()->getDbtable('events', 'event');
        $select = $table->select()
            ->setIntegrityCheck(false)
            ->from(array('e' => $table->info('name')))
            ->join(array('a' => $content['authTb']), "a.resource_type='event' AND a.resource_id=e.event_id")
            ->where("a.action = ?", 'view')
            ->where("a.role = ?", 'everyone')
            ->where('e.event_id>?', $content['last_sent_id'])
            ->where('e.endtime > NOW()')
            ->order('e.starttime')
            ->limit($content['count']);

        if (isset($content['displayed'])) {
            $select->where("e.event_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("e.event_id NOT IN ({$content['blacklist']})");
        }

        return $table->fetchAll($select);
    }

    protected function new_events_page($content)
    {
        $pageEventsTbl = Engine_Api::_()->getDbTable('pageevents', 'pageevent');
        $authAllowTbl = Engine_Api::_()->getDbTable('allow', 'authorization');
        $pageTbl = Engine_Api::_()->getDbTable('pages', 'page');

        $selectPageId = $authAllowTbl->select()
            ->distinct()
            ->from(array('au' => $authAllowTbl->info('name')), array('au.resource_id'))
            ->join(array('pe' => $pageEventsTbl->info('name')), "pe.page_id=au.resource_id AND au.resource_type='page'", array())
            ->where("au.action = 'view'")
            ->where("au.role = 'everyone'");

        $select = $pageEventsTbl->select()
            ->setIntegrityCheck(false)
            ->from(array('pe' => $pageEventsTbl->info('name')))
            ->join(array('au' => $content['authTb']), "au.resource_type='pageevent' AND au.resource_id=pe.pageevent_id", array())
            ->join(array('p' => $pageTbl->info('name')), "p.page_id=pe.page_id", array('url'))
            ->where("au.action = 'view'")
            ->where("au.role = 'everyone'")
            ->where('pe.pageevent_id > ?', $content['last_sent_id'])
            ->where('pe.endtime > NOW()')
            ->where("pe.page_id IN ({$selectPageId})")
            ->order('pe.starttime')
            ->limit($content['count']);

        if (isset($content['displayed'])) {
            $select->where("pe.pageevent_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("pe.pageevent_id NOT IN ({$content['blacklist']})");
        }

        return $pageEventsTbl->fetchAll($select);
    }

    protected function new_groups($content)
    {
        $table = Engine_Api::_()->getDbtable('groups', 'group');
        $select = $table->select()
            ->setIntegrityCheck(false)
            ->from(array('g' => $table->info('name')))
            ->join(array('a' => $content['authTb']), "a.resource_type='group' AND a.resource_id=g.group_id")
            ->where("a.action = ?", 'view')
            ->where("a.role = ?", 'everyone')
            ->where('g.group_id > ?', $content['last_sent_id'])
            ->order('g.group_id DESC')
            ->limit($content['count']);

        if (isset($content['displayed'])) {
            $select->where("g.group_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("g.group_id NOT IN ({$content['blacklist']})");
        }

        return $table->fetchAll($select);
    }

    protected function new_younet_groups($content)
    {
        $moduleTb = Engine_Api::_()->getDbtable('modules', 'core');
        if ($moduleTb->isModuleEnabled('group')) {
            return;
        }
        $table = Engine_Api::_()->getDbtable('groups', 'advgroup');
        $select = $table->select()
            ->setIntegrityCheck(false)
            ->from(array('g' => $table->info('name')))
            ->join(array('a' => $content['authTb']), "a.resource_type='group' AND a.resource_id=g.group_id")
            ->where("a.action = ?", 'view')
            ->where("a.role = ?", 'everyone')
            ->where('g.group_id > ?', $content['last_sent_id'])
            ->order('g.group_id DESC')
            ->limit($content['count']);

        if (isset($content['displayed'])) {
            $select->where("g.group_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("g.group_id NOT IN ({$content['blacklist']})");
        }

        return $table->fetchAll($select);
    }

    protected function new_playlists($content)
    {
        $table = Engine_Api::_()->getDbtable('playlists', 'music');
        $select = $table->select()
            ->setIntegrityCheck(false)
            ->from(array('p' => $table->info('name')))
            ->join(array('a' => $content['authTb']), "a.resource_type='music_playlist' AND a.resource_id=p.playlist_id")
            ->where("a.action = ?", 'view')
            ->where("a.role = ?", 'everyone')
            ->where('p.playlist_id>?', $content['last_sent_id'])
            ->order('p.playlist_id DESC')
            ->limit($content['count']);

        if (isset($content['displayed'])) {
            $select->where("p.playlist_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("p.playlist_id NOT IN ({$content['blacklist']})");
        }

        return $table->fetchAll($select);
    }

    protected function new_playlists_page($content)
    {
        $pagePlaylistsTbl = Engine_Api::_()->getDbTable('playlists', 'pagemusic');
        $pageTbl = Engine_Api::_()->getDbTable('pages', 'page');

        $select = $pagePlaylistsTbl->select()
            ->setIntegrityCheck(false)
            ->from(array('pp' => $pagePlaylistsTbl->info('name')))
            ->join(array('a' => $content['authTb']), "a.resource_type='page' AND a.resource_id=pp.page_id", array())
            ->join(array('p' => $pageTbl->info('name')), "p.page_id=pp.page_id", array('url'))
            ->where("a.action = ?", 'view')
            ->where("a.role = ?", 'everyone')
            ->where('pp.playlist_id > ?', $content['last_sent_id'])
            ->order('pp.playlist_id DESC')
            ->limit($content['count']);

        if (isset($content['displayed'])) {
            $select->where("pp.playlist_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("pp.playlist_id NOT IN ({$content['blacklist']})");
        }

        return $pagePlaylistsTbl->fetchAll($select);
    }

    protected function new_polls($content)
    {
        $table = Engine_Api::_()->getDbtable('polls', 'poll');
        $select = $table->select()
            ->setIntegrityCheck(false)
            ->from(array('p' => $table->info('name')))
            ->join(array('a' => $content['authTb']), "a.resource_type='poll' && " . "a.resource_id=p.poll_id")
            ->where("a.action = ?", 'view')
            ->where("a.role = ?", 'everyone')
            ->where('p.poll_id>?', $content['last_sent_id'])
            ->where('p.is_closed=?', 0)
            ->order('p.poll_id DESC')
            ->limit($content['count']);

        if (isset($content['displayed'])) {
            $select->where("p.poll_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("p.poll_id NOT IN ({$content['blacklist']})");
        }

        return $table->fetchAll($select);
    }

    protected function new_videos($content)
    {
        $table = Engine_Api::_()->getDbtable('videos', 'video');
        $select = $table->select()
            ->setIntegrityCheck(false)
            ->from(array('v' => $table->info('name')))
            ->join(array('a' => $content['authTb']), "a.resource_type='video' && a.resource_id=v.video_id")
            ->where("a.action = ?", 'view')
            ->where("a.role = ?", 'everyone')
            ->where('v.video_id>?', $content['last_sent_id'])
            ->where('v.status=?', 1)
            ->order('v.video_id DESC')
            ->limit($content['count']);

        if (isset($content['displayed'])) {
            $select->where("v.video_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("v.video_id NOT IN ({$content['blacklist']})");
        }

        return $table->fetchAll($select);
    }

    protected function new_videos_page($content)
    {
        $pageVideoTbl = Engine_Api::_()->getDbtable('pagevideos', 'pagevideo');
        $pageTbl = Engine_Api::_()->getDbTable('pages', 'page');

        $select = $pageVideoTbl->select()
            ->setIntegrityCheck(false)
            ->from(array('pv' => $pageVideoTbl->info('name')))
            ->join(array('a' => $content['authTb']), "a.resource_type='page' AND a.resource_id=pv.page_id", array())
            ->join(array('p' => $pageTbl->info('name')), "p.page_id=pv.page_id", array('url'))
            ->where("a.action = ?", 'view')
            ->where("a.role = ?", 'everyone')
            ->where('pv.pagevideo_id > ?', $content['last_sent_id'])
            ->where('pv.status=?', 1)
            ->order('pv.pagevideo_id DESC')
            ->limit($content['count']);

        if (isset($content['displayed'])) {
            $select->where("pv.pagevideo_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("pv.pagevideo_id NOT IN ({$content['blacklist']})");
        }

        return $pageVideoTbl->fetchAll($select);
    }

    protected function new_younet_videos($content)
    {
        $moduleTb = Engine_Api::_()->getDbtable('modules', 'core');
        if ($moduleTb->isModuleEnabled('video')) {
            return;
        }
        $table = Engine_Api::_()->getDbtable('videos', 'ynvideo');
        $select = $table->select()
            ->setIntegrityCheck(false)
            ->from(array('v' => $table->info('name')))
            ->join(array('a' => $content['authTb']), "a.resource_type='video' && a.resource_id=v.video_id")
            ->where("a.action = ?", 'view')
            ->where("a.role = ?", 'everyone')
            ->where('v.video_id > ?', $content['last_sent_id'])
            ->where('v.status=?', 1)
            ->order('v.video_id DESC')
            ->limit($content['count']);

        if (isset($content['displayed'])) {
            $select->where("v.video_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("v.video_id NOT IN ({$content['blacklist']})");
        }

        return $table->fetchAll($select);
    }

    protected function new_avp_videos($content)
    {
        $table = Engine_Api::_()->getDbtable('videos', 'avp');
        $select = $table->select()
            ->setIntegrityCheck(false)
            ->from(array('v' => $table->info('name')))
            ->join(array('a' => $content['authTb']), "a.resource_type='avp_video' AND a.resource_id=v.video_id")
            ->where("a.action = ?", 'view')
            ->where("a.role = ?", 'everyone')
            ->where('v.video_id > ?', $content['last_sent_id'])
            ->where('v.status=?', 1)
            ->order('v.video_id DESC')
            ->limit($content['count']);

        if (isset($content['displayed'])) {
            $select->where("v.video_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("v.video_id NOT IN ({$content['blacklist']})");
        }

        return $table->fetchAll($select);
    }

    protected function new_forums($content)
    {
        $table = Engine_Api::_()->getDbtable('forums', 'forum');
        $select = $table->select()
            ->setIntegrityCheck(false)
            ->from(array('f' => $table->info('name')))
            ->join(array('a' => $content['authTb']), "a.resource_type='forum' && " . "a.resource_id=f.forum_id")
            ->where("a.action = ?", 'view')
            ->where('f.forum_id>?', $content['last_sent_id'])
            ->order('f.forum_id DESC')
            ->limit($content['count']);

        if (isset($content['displayed'])) {
            $select->where("f.forum_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("f.forum_id NOT IN ({$content['blacklist']})");
        }
        return $table->fetchAll($select);
    }

    protected function new_forum_topics($content)
    {
        $table = Engine_Api::_()->getDbtable('topics', 'forum');
        $select = $table->select()
            ->setIntegrityCheck(false)
            ->from(array('t' => $table->info('name')))
            ->join(array('a' => $content['authTb']), "a.resource_type='forum' && " . "a.resource_id=t.forum_id")
            ->where("a.action = ?", 'view')
            ->where('t.topic_id>?', $content['last_sent_id'])
            ->order('t.topic_id DESC')
            ->limit($content['count']);

        if (isset($content['displayed'])) {
            $select->where("t.topic_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("t.topic_id NOT IN ({$content['blacklist']})");
        }

        return $table->fetchAll($select);
    }

    protected function new_quizzes($content)
    {
        $table = Engine_Api::_()->getDbtable('quizs', 'quiz');
        $select = $table->select()
            ->setIntegrityCheck(false)
            ->from(array('q' => $table->info('name')), 'q.*')
            ->join(array('a' => $content['authTb']), "a.resource_type='quiz' && " . "a.resource_id=q.quiz_id")
            ->where("a.action = ?", 'view')
            ->where("a.role = ?", 'everyone')
            ->where('q.quiz_id>?', $content['last_sent_id'])
            ->where('q.published=?', 1)
            ->where('q.approved=?', 1)
            ->order('q.quiz_id DESC')
            ->limit($content['count']);

        if (isset($content['displayed'])) {
            $select->where("q.quiz_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("q.quiz_id NOT IN ({$content['blacklist']})");
        }

        return $table->fetchAll($select);
    }

    protected function friend_suggests($content)
    {
        $table = Engine_Api::_()->getItemTable('user');
        $suggest_array = Engine_Api::_()->getDbtable('nonefriends', 'inviter')->getSuggests(array(
            'current_suggests' => null,
            'noneFriend_id' => 0,
            'widget' => true,
            'total_suggests' => 100
        ));
        $select = $suggest_array['suggestsSl']->limit($content['count']);

        if (isset($content['displayed'])) {
            $select->where("user_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("user_id NOT IN ({$content['blacklist']})");
        }

        return $table->fetchAll($select);
    }

    protected function popular_members($content)
    {
        $table = Engine_Api::_()->getItemTable('user');
        $select = $table->select()
            ->where('search = ?', 1)
            ->where('enabled = ?', 1)
            ->where('verified = ?', 1)
            ->where('member_count > ?', -1)
            ->limit($content['count'])
            ->order('member_count DESC');

        if (isset($content['displayed'])) {
            $select->where("user_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("user_id NOT IN ({$content['blacklist']})");
        }

        return $table->fetchAll($select);
    }

    protected function featured_members($content)
    {
        $table = Engine_Api::_()->getDbTable('users', 'user');
        $featuredTb = Engine_Api::_()->getDbTable('featureds', 'hecore');

        $select = $table->select()
            ->setIntegrityCheck(false)
            ->from(array('u' => $table->info('name')), 'u.*')
            ->join(array('f' => $featuredTb->info('name')), 'f.user_id = u.user_id', 'f.featured_id')
            ->limit($content['count'])
            ->order('RAND()');

        if (isset($content['displayed'])) {
            $select->where("u.user_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("u.user_id NOT IN ({$content['blacklist']})");
        }

        return $table->fetchAll($select);
    }

    protected function new_questions($content)
    {
        $table = Engine_Api::_()->getDbtable('questions', 'question');
        $select = $table->select()
            ->setIntegrityCheck(false)
            ->from(array('q' => $table->info('name')), 'q.*')
            ->join(array('a' => $content['authTb']), "a.resource_type='question' && " . "a.resource_id=q.question_id")
            ->where("a.action = ?", 'view')
            ->where("a.role = ?", 'everyone')
            ->where('q.question_id>?', $content['last_sent_id'])
            ->order('q.question_id DESC')
            ->limit($content['count']);

        if (isset($content['displayed'])) {
            $select->where("q.question_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("q.question_id NOT IN ({$content['blacklist']})");
        }

        return $table->fetchAll($select);
    }

    protected function new_pages($content)
    {
        $table = Engine_Api::_()->getDbtable('pages', 'page');
        $select = $table->select()
            ->setIntegrityCheck(false)
            ->from(array('p' => $table->info('name')))
            ->join(array('a' => $content['authTb']), "a.resource_type='page' && " . "a.resource_id=p.page_id")
            ->where("a.action = ?", 'view')
            ->where("a.role = ?", 'everyone')
            ->where('p.page_id>?', $content['last_sent_id'])
            ->where('p.approved=?', 1)
            ->order('p.page_id DESC')
            ->limit($content['count']);

        if (isset($content['displayed'])) {
            $select->where("p.page_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("p.page_id NOT IN ({$content['blacklist']})");
        }

        return $table->fetchAll($select);
    }

    protected function most_liked_pages($content)
    {
        $table = Engine_Api::_()->getDbtable('pages', 'page');
        $likesTb = Engine_Api::_()->getDbTable('likes', 'core');

        $select = $table->select()
            ->setIntegrityCheck(false)
            ->from(array('p' => $table->info('name')))
            ->join(array('a' => $content['authTb']), "a.resource_type='page' && " . "a.resource_id=p.page_id")
            ->join(array('l' => $likesTb->info('name')), 'l.resource_id = p.page_id', array('like_count' => 'COUNT(*)'))
            ->where("a.action = ?", 'view')
            ->where("a.role = ?", 'everyone')
            ->where('p.page_id>?', $content['last_sent_id'])
            ->where('p.approved=?', 1)
            ->where('l.resource_type = ?', 'page')
            ->order('like_count DESC')
            ->group('l.resource_id')
            ->limit($content['count']);

        if (isset($content['displayed'])) {
            $select->where("p.page_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("p.page_id NOT IN ({$content['blacklist']})");
        }

        return $table->fetchAll($select);
    }

    protected function most_liked_members($content)
    {
        $table = Engine_Api::_()->getItemTable('user');
        $likesTb = Engine_Api::_()->getDbTable('likes', 'core');

        $select = $table->select()
            ->setIntegrityCheck(false)
            ->from(array('u' => $table->info('name')))
            ->join(array('l' => $likesTb->info('name')), 'l.resource_id = u.user_id', array('like_count' => 'COUNT(*)'))
            ->where('u.enabled=?', 1)
            ->where('u.verified=?', 1)
            ->where('l.resource_type            = ?', 'user')
            ->order('like_count DESC')
            ->group('l.resource_id')
            ->limit($content['count']);

        if (isset($content['displayed'])) {
            $select->where("u.user_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("u.user_id NOT IN ({$content['blacklist']})");
        }

        return $table->fetchAll($select);
    }

    protected function new_articles($content)
    {
        $table = Engine_Api::_()->getDbtable('articles', 'article');
        $select = $table->select()
            ->setIntegrityCheck(false)
            ->from(array('ar' => $table->info('name')), 'ar.*')
            ->join(array('a' => $content['authTb']), "a.resource_type='article' && " . "a.resource_id=ar.article_id")
            ->where("a.action = ?", 'view')
            ->where("a.role = ?", 'everyone')
            ->where('ar.article_id > ?', $content['last_sent_id'])
            ->where('ar.published = ?', 1)
            ->order('ar.article_id DESC')
            ->limit($content['count']);

        if (isset($content['displayed'])) {
            $select->where("ar.article_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("ar.article_id NOT IN ({$content['blacklist']})");
        }

        return $table->fetchAll($select);
    }

    protected function new_advanced_articles($content)
    {
        $table = Engine_Api::_()->getDbtable('artarticles', 'advancedarticles');
        $select = $table->select()
            ->setIntegrityCheck(false)
            ->from(array('ar' => $table->info('name')))
            ->join(array('a' => $content['authTb']), "a.resource_type='artarticle' AND a.resource_id=ar.artarticle_id")
            ->where("a.action = ?", 'view')
            ->where("a.role = ?", 'everyone')
            ->where('ar.artarticle_id > ?', $content['last_sent_id'])
            ->where('ar.status = ?', 'active')
            ->order('ar.artarticle_id DESC')
            ->limit($content['count']);

        if (isset($content['displayed'])) {
            $select->where("ar.artarticle_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("ar.artarticle_id NOT IN ({$content['blacklist']})");
        }

        return $table->fetchAll($select);
    }

    protected function featured_articles($content)
    {
        $table = Engine_Api::_()->getDbtable('articles', 'article');
        $select = $table->select()
            ->setIntegrityCheck(false)
            ->from(array('ar' => $table->info('name')), 'ar.*')
            ->join(array('a' => $content['authTb']), "a.resource_type='article' && " . "a.resource_id=ar.article_id")
            ->where("a.action = ?", 'view')
            ->where("a.role = ?", 'everyone')
            ->where('ar.article_id>?', $content['last_sent_id'])
            ->where('ar.published=?', 1)
            ->where('ar.featured=?', 1)
            ->order('RAND()')
            ->limit($content['count']);

        if (isset($content['displayed'])) {
            $select->where("ar.article_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("ar.article_id NOT IN ({$content['blacklist']})");
        }

        return $table->fetchAll($select);
    }


    protected function most_liked_articles($content)
    {
        $likesTb = Engine_Api::_()->getDbTable('likes', 'core');
        $table = Engine_Api::_()->getDbtable('articles', 'article');
        $select = $table->select()
            ->setIntegrityCheck(false)
            ->from(array('ar' => $table->info('name')), 'ar.*')
            ->join(array('a' => $content['authTb']), "a.resource_type='article' && " . "a.resource_id=ar.article_id")
            ->join(array('l' => $likesTb->info('name')), 'l.resource_id = ar.article_id', array('like_count' => 'COUNT(*)'))
            ->where("a.action = ?", 'view')
            ->where("a.role = ?", 'everyone')
            ->where('ar.published=?', 1)
            ->where('ar.featured=?', 1)
            ->where('l.resource_type = ?', 'article')
            ->order('like_count DESC')
            ->group('l.resource_id')
            ->limit($content['count']);

        if (isset($content['displayed'])) {
            $select->where("ar.article_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("ar.article_id NOT IN ({$content['blacklist']})");
        }

        return $table->fetchAll($select);
    }

    protected function mixed_recommendation($content = null)
    {
        /**
         * @var $api Suggest_Api_Core
         * @var $table Suggest_Model_DbTable_Recommendations
         * @var $viewer User_Model_User
         */

        $api = Engine_Api::_()->suggest();
        $table = Engine_Api::_()->getDbTable('recommendations', 'suggest');
        $viewer = Engine_Api::_()->user()->getViewer();
        $limit = $content['count'];
        $types = $api->getMixItems();

        if (count($types) <= 0) return;

        $types = str_replace(array("'user', ", ", 'user'", "'user'"), "", "'" . implode("', '", $types) . "'");

        $select = $table
            ->select()
            ->where("object_type = 'user' AND object_id != " . $viewer->getIdentity());

        if (strlen($types) > 0) {
            $select->orWhere('object_type IN(' . $types . ')');
        }

        $select
            ->order('date DESC')
            ->limit($limit);

        if (isset($content['displayed'])) {
            $select->where("recommendation_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("recommendation_id NOT IN ({$content['blacklist']})");
        }

        $arrTypes = explode(',', $types);
        $i = -1;
        foreach ($arrTypes as $type) {
            $i++;
            $arrTypes[$i] = trim(str_replace("'", "", $type));
        }

        $query = $select->query();
        $recommendations = $query->fetchAll();
        $deletedRecommends = array();
        $i = -1;

        // checking if exist deleted recommendations
        foreach ($recommendations as $recommendation) {
            foreach ($arrTypes as $type) {
                if ($recommendation['object_type'] == $type && $recommendation['object_type'] != 'music_playlist' && $recommendation['object_type'] != 'store_product') {
                    $typeTable = Engine_Api::_()->getDbTable($type . 's', $type);
                    $selectType = $typeTable->select()
                        ->where($type . '_id = ?', $recommendation['object_id']);
                    $typeResult = $typeTable->fetchRow($selectType);
                    if (!$typeResult) {
                        $i++;
                        $deletedRecommends[$i] = $recommendation['recommendation_id'];
                    }
                } else if ($recommendation['object_type'] == 'music_playlist') {
                    $playlistTable = Engine_Api::_()->getDbTable('playlists', 'music');
                    $selectPlaylist = $playlistTable->select()
                        ->where('playlist_id = ?', $recommendation['object_id']);
                    $playlist = $playlistTable->fetchRow($selectPlaylist);
                    if (!$playlist) {
                        $i++;
                        $deletedRecommends[$i] = $recommendation['recommendation_id'];
                    }
                } else if ($recommendation['object_type'] == 'store_product') {
                    $productsTbl = Engine_Api::_()->getDbTable('products', 'store');
                    $selectProducts = $productsTbl->select()
                        ->where('product_id = ?', $recommendation['object_id']);
                    $products = $productsTbl->fetchRow($selectProducts);
                    if (!$products) {
                        $i++;
                        $deletedRecommends[$i] = $recommendation['recommendation_id'];
                    }
                }
            }
        }

        if (!empty($deletedRecommends)) {
            $select->where('recommendation_id NOT IN (?)', $deletedRecommends);
        }

        return $table->fetchAll($select);
    }

    protected function recommended_members($content = null)
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $recommendationsTbl = Engine_Api::_()->getDbTable('recommendations', 'suggest');
        $userTbl = Engine_Api::_()->getDbtable('users', 'user');

        $select = $userTbl->select()
            ->setIntegrityCheck(false)
            ->from(array('u' => $userTbl->info('name')))
            ->join(array('sr' => $recommendationsTbl->info('name')), 'u.user_id = sr.object_id')
            ->where('u.search=1 && u.enabled=1 && u.verified=1')
            ->where('u.user_id > ?', $content['last_sent_id'])
            ->where('u.user_id <> ?', $viewer_id)
            ->where('sr.object_type = "user"')
            ->order('u.user_id DESC')
            ->limit($content['count']);

        if (isset($content['displayed'])) {
            $select->where("u.user_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("u.user_id NOT IN ({$content['blacklist']})");
        }

        return $userTbl->fetchAll($select);
    }

    protected function recommended_pages($content = null)
    {
        $recommendationsTbl = Engine_Api::_()->getDbTable('recommendations', 'suggest');
        $pagesTbl = Engine_Api::_()->getDbtable('pages', 'page');

        $select = $pagesTbl->select()
            ->setIntegrityCheck(false)
            ->from(array('p' => $pagesTbl->info('name')))
            ->join(array('a' => $content['authTb']), "a.resource_type='page' AND a.resource_id=p.page_id", array())
            ->join(array('sr' => $recommendationsTbl->info('name')), 'p.page_id = sr.object_id')
            ->where("a.action = ?", 'view')
            ->where("a.role = ?", 'everyone')
            ->where('p.search=1 && p.enabled=1 && p.approved=1')
            ->where('p.page_id > ?', $content['last_sent_id'])
            ->where('sr.object_type = "page"')
            ->order('p.page_id DESC')
            ->limit($content['count']);

        if (isset($content['displayed'])) {
            $select->where("p.page_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("p.page_id NOT IN ({$content['blacklist']})");
        }
        return $pagesTbl->fetchAll($select);
    }

    protected function recommended_groups($content = null)
    {
        $recommendationsTbl = Engine_Api::_()->getDbTable('recommendations', 'suggest');
        $groupsTbl = Engine_Api::_()->getDbtable('groups', 'group');

        $select = $groupsTbl->select()
            ->setIntegrityCheck(false)
            ->from(array('g' => $groupsTbl->info('name')))
            ->join(array('a' => $content['authTb']), "a.resource_type='group' AND a.resource_id=g.group_id", array())
            ->join(array('sr' => $recommendationsTbl->info('name')), 'g.group_id = sr.object_id')
            ->where("a.action = ?", 'view')
            ->where("a.role = ?", 'everyone')
            ->where('g.group_id > ?', $content['last_sent_id'])
            ->where('sr.object_type = "group"')
            ->order('g.group_id DESC')
            ->limit($content['count']);

        if (isset($content['displayed'])) {
            $select->where("g.group_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("g.group_id NOT IN ({$content['blacklist']})");
        }
        return $groupsTbl->fetchAll($select);
    }

    protected function recommended_events($content = null)
    {
        $recommendationsTbl = Engine_Api::_()->getDbTable('recommendations', 'suggest');
        $eventsTbl = Engine_Api::_()->getDbtable('events', 'event');

        $select = $eventsTbl->select()
            ->setIntegrityCheck(false)
            ->from(array('e' => $eventsTbl->info('name')))
            ->join(array('a' => $content['authTb']), "a.resource_type='event' AND a.resource_id=e.event_id", array())
            ->join(array('sr' => $recommendationsTbl->info('name')), 'e.event_id = sr.object_id', array())
            ->where("a.action = ?", 'view')
            ->where("a.role = ?", 'everyone')
            ->where('e.event_id > ?', $content['last_sent_id'])
            ->where('sr.object_type = "event"')
            ->order('e.event_id DESC')
            ->limit($content['count']);

        if (isset($content['displayed'])) {
            $select->where("e.event_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("e.event_id NOT IN ({$content['blacklist']})");
        }
        return $eventsTbl->fetchAll($select);
    }

    protected function recommended_products($content = null)
    {
        $recommendationsTbl = Engine_Api::_()->getDbTable('recommendations', 'suggest');
        $productsTbl = Engine_Api::_()->getDbtable('products', 'store');

        $select = $productsTbl->select()
            ->setIntegrityCheck(false)
            ->from(array('sp' => $productsTbl->info('name')))
            ->join(array('sr' => $recommendationsTbl->info('name')), 'sp.product_id = sr.object_id', array())
            ->join(array('a' => $content['authTb']), "a.resource_type='page' AND a.resource_id=sp.page_id", array())
            ->where("a.action = ?", 'view')
            ->where("a.role = ?", 'everyone')
            ->where('sp.product_id > ?', $content['last_sent_id'])
            ->where('sr.object_type = "store_product"')
            ->order('sp.product_id DESC')
            ->limit($content['count']);

        if (isset($content['displayed'])) {
            $select->where("sp.product_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("sp.product_id NOT IN ({$content['blacklist']})");
        }
        return $productsTbl->fetchAll($select);
    }

    protected function new_products($content)
    {
        $productsTbl = Engine_Api::_()->getDbtable('products', 'store');

        $select = $productsTbl->select()
            ->setIntegrityCheck(false)
            ->from(array('sp' => $productsTbl->info('name')))
            ->join(array('a' => $content['authTb']), "a.resource_type='page' AND a.resource_id=sp.page_id", array())
            ->where("a.action = ?", 'view')
            ->where("a.role = ?", 'everyone')
            ->where('sp.search=1')
            ->where('sp.product_id > ?', $content['last_sent_id'])
            ->order('sp.product_id DESC')
            ->limit($content['count']);

        if (isset($content['sp.displayed'])) {
            $select->where("product_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("sp.product_id NOT IN ({$content['blacklist']})");
        }

        return $productsTbl->fetchAll($select);
    }

    protected function new_stores($content)
    {
        /**
         * @var $table Page_Model_DbTable_Pages
         * @var $pageApi Store_Api_Page
         */
        $pageTbl = Engine_Api::_()->getDbTable('pages', 'page');
        $productsTbl = Engine_Api::_()->getDbTable('products', 'store');

        $select = $pageTbl->select()
            ->setIntegrityCheck(false)
            ->from(array('p' => $pageTbl->info('name')))
            ->join(array('sp' => $productsTbl->info('name')), "p.page_id = sp.page_id", array())
            ->join(array('a' => $content['authTb']), "a.resource_type = 'page' AND a.resource_id = p.page_id", array())
            ->where("a.action = ?", 'view')
            ->where("a.role = ?", 'everyone')
            ->where('p.page_id > ?', $content['last_sent_id'])
            ->order('p.page_id DESC')
            ->limit($content['count']);

        if (isset($content['displayed'])) {
            $select->where("p.page_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("p.page_id NOT IN ({$content['blacklist']})");
        }

        return $pageTbl->fetchAll($select);
    }

    protected function notifications($content)
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $table = Engine_Api::_()->getDbtable('notifications', 'activity');
        $select = $table->select()
            ->setIntegrityCheck(false)
            ->from(array('n' => $table->info('name')))
            ->where('n.notification_id>?', $content['last_sent_id'])
            ->where('n.subject_id=?', $viewer_id)
            ->order('n.notification_id DESC')
            ->limit($content['count']);

        return $table->fetchAll($select);
    }

    protected function new_listings($content)
    {
        $table = Engine_Api::_()->getDbtable('listings', 'list');
        $select = $table->select()
            ->setIntegrityCheck(false)
            ->from(array('l' => $table->info('name')))
            ->join(array('a' => $content['authTb']), "a.resource_type='list_listing' AND a.resource_id=l.listing_id", array())
            ->where("a.action = ?", 'view')
            ->where("a.role = ?", 'everyone')
            ->where('l.listing_id > ?', $content['last_sent_id'])
            ->where('l.approved = 1')
            ->where('l.closed = 0')
            ->order('l.listing_id DESC')
            ->limit($content['count']);

        if (isset($content['displayed'])) {
            $select->where("l.listing_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("l.listing_id NOT IN ({$content['blacklist']})");
        }

        return $table->fetchAll($select);
    }

    protected function new_documents($content)
    {
        $table = Engine_Api::_()->getDbtable('documents', 'document');
        $select = $table->select()
            ->setIntegrityCheck(false)
            ->from(array('d' => $table->info('name')))
            ->join(array('a' => $content['authTb']), "a.resource_type='document' AND a.resource_id=d.document_id", array())
            ->where("a.action = ?", 'view')
            ->where("a.role = ?", 'everyone')
            ->where('d.document_id > ?', $content['last_sent_id'])
            ->where('d.status = 1')
            ->where('d.approved = 1')
            ->where('d.draft = 0')
            ->order('d.document_id DESC')
            ->limit($content['count']);

        if (isset($content['displayed'])) {
            $select->where("d.document_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("d.document_id NOT IN ({$content['blacklist']})");
        }

        return $table->fetchAll($select);
    }

    protected function new_badges($content)
    {
        $badgesTbl = Engine_Api::_()->getDbtable('badges', 'hebadge');

        $select = $badgesTbl->select()
            ->from(array('b' => $badgesTbl->info('name')))
            ->order('b.badge_id DESC')
            ->limit($content['count']);

        if (isset($content['displayed'])) {
            $select->where("b.badge_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("b.badge_id NOT IN ({$content['blacklist']})");
        }

        return $badgesTbl->fetchAll($select);
    }

    protected function last_members_got_badges($content)
    {
        $membersTbl = Engine_Api::_()->getDbtable('members', 'hebadge');
        $badgesTbl = Engine_Api::_()->getDbtable('badges', 'hebadge');
        $usersTbl = Engine_Api::_()->getDbtable('users', 'user');

        $select = $membersTbl->select()
            ->setIntegrityCheck(false)
            ->from(array('u' => $usersTbl->info('name')), array('user_id', 'email', 'username', 'displayname', 'photo_id'))
            ->join(array('m' => $membersTbl->info('name')), 'u.user_id=m.object_id')
            ->join(array('b' => $badgesTbl->info('name')), 'b.badge_id=m.badge_id', array('title', 'badge_photo_id' => 'photo_id'))
            ->where('m.member_id > ?', $content['last_sent_id'])
            ->where('m.approved = 1')
            ->where('u.enabled=1 && u.verified=1 && u.approved=1')
            ->where('u.updates_subscribed = 1')
            ->order('m.member_id DESC')
            ->limit($content['count']);

        if (isset($content['displayed'])) {
            $select->where("m.member_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("m.member_id NOT IN ({$content['blacklist']})");
        }

        return $membersTbl->fetchAll($select);
    }

    protected function new_jobs($content)
    {
        $table = Engine_Api::_()->getDbtable('jobs', 'job');
        $select = $table->select()
            ->setIntegrityCheck(false)
            ->from(array('j' => $table->info('name')))
            ->join(array('a' => $content['authTb']), "a.resource_type='job' AND a.resource_id=j.job_id")
            ->where("a.action = ?", 'view')
            ->where("a.role = ?", 'everyone')
            ->where('j.job_id>?', $content['last_sent_id'])
            ->order('j.job_id DESC')
            ->limit($content['count']);

        if (isset($content['displayed'])) {
            $select->where("j.job_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("j.job_id NOT IN ({$content['blacklist']})");
        }

        return $table->fetchAll($select);
    }

    protected function trending_news($content)
    {
        $moduleTb = Engine_Api::_()->getDbtable('modules', 'core');
        if (!$moduleTb->isModuleEnabled('ultimatenews')) {
            return;
        }
        $table = Engine_Api::_()->getDbtable('Contents', 'ultimatenews');
        $limit = 5;
        $select = $table->select('engine4_ultimatenews_contents')->setIntegrityCheck(false)
            ->joinLeft("engine4_ultimatenews_categories", "engine4_ultimatenews_categories.category_id= engine4_ultimatenews_contents.category_id", array('logo' => 'engine4_ultimatenews_categories.category_logo', 'logo_icon' => 'engine4_ultimatenews_categories.logo', 'display_logo' => 'engine4_ultimatenews_categories.display_logo', 'mini_logo' => 'engine4_ultimatenews_categories.mini_logo'))
            ->where('engine4_ultimatenews_categories.is_active= ? ', 1)
            ->order('engine4_ultimatenews_contents.count_view DESC')
            ->limit($limit);
        if (isset($content['blacklist'])) {
            $select->where("engine4_ultimatenews_contents.content_id NOT IN ({$content['blacklist']})");
        }
        return $table->fetchAll($select);
    }

    protected function latest_news($content)
    {
        $moduleTb = Engine_Api::_()->getDbtable('modules', 'core');
        if (!$moduleTb->isModuleEnabled('ultimatenews')) {
            return;
        }
        $limit = 5;
        $table = Engine_Api::_()->getDbtable('Contents', 'ultimatenews');
        $select = $table->select('engine4_ultimatenews_contents')->setIntegrityCheck(false)
            ->joinLeft("engine4_ultimatenews_categories", "engine4_ultimatenews_categories.category_id= engine4_ultimatenews_contents.category_id", array('logo' => 'engine4_ultimatenews_categories.category_logo', 'logo_icon' => 'engine4_ultimatenews_categories.logo', 'display_logo' => 'engine4_ultimatenews_categories.display_logo', 'mini_logo' => 'engine4_ultimatenews_categories.mini_logo'))
            ->order('engine4_ultimatenews_contents.pubDate DESC')
            ->where('engine4_ultimatenews_categories.is_active= ? ', 1)
            ->limit($limit);
        if (isset($content['blacklist'])) {
            $select->where("engine4_ultimatenews_contents.content_id NOT IN ({$content['blacklist']})");
        }
        return $table->fetchAll($select);
    }

    protected function new_productsSEAO($content)
    {

        $productsTbl = Engine_Api::_()->getDbtable('listings', 'sitereview');
        $type = Engine_Api::_()->getDbtable('listingtypes', 'sitereview');

        $select = $productsTbl->select()
          ->setIntegrityCheck(false)
          ->from(array('l' => $productsTbl->info('name')))
          ->join(array('a' => $type->info('name')), "a.listingtype_id=l.listingtype_id", array())
          ->where('a.listingtype_id = ?',$content['select'])
          ->order('l.listing_id DESC')
          ->limit($content['count']);;

        if (isset($content['sp.displayed'])) {
            $select->where("l.listing_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("l.listing_id NOT IN ({$content['blacklist']})");
        }

        return $productsTbl->fetchAll($select);
    }
    protected function new_younet_resume($content)
    {
        $moduleTb = Engine_Api::_()->getDbtable('modules', 'core');

        if (!$moduleTb->isModuleEnabled('ynresume')) {
            return;
        }
        $table = Engine_Api::_() -> getItemTable('ynresume_resume');
        $select = $table->select()
          ->setIntegrityCheck(false)
          ->from(array('r' => $table->info('name')))
          ->join(array('a' => $content['authTb']), "a.resource_type='ynresume_resume' && a.resource_id=r.resume_id")
          ->where("a.action = ?", 'view')
          ->where("a.role = ?", 'everyone')
          ->order('r.resume_id DESC')
          ->limit($content['count']);

        if (isset($content['displayed'])) {
            $select->where("r.resume_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("r.resume_id NOT IN ({$content['blacklist']})");
        }

        return $table->fetchAll($select);
    }
    protected function new_younet_jod($content)
    {

        $moduleTb = Engine_Api::_()->getDbtable('modules', 'core');

        if (!$moduleTb->isModuleEnabled('ynjobposting')) {
            return;
        }

        $table = Engine_Api::_() -> getItemTable('ynjobposting_job');
        $select = $table->select()
          ->setIntegrityCheck(false)
          ->from(array('r' => $table->info('name')))
          ->join(array('a' => $content['authTb']), "a.resource_type='ynjobposting_job' && a.resource_id=r.job_id")
          ->where("a.action = ?", 'view')
          ->where("a.role = ?", 'everyone')
          ->where("r.status = ?", 'published')
          ->order('r.job_id DESC')
          ->limit($content['count']);

        if (isset($content['displayed'])) {
            $select->where("r.job_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("r.job_id NOT IN ({$content['blacklist']})");
        }
        return $table->fetchAll($select);
    }
    protected function new_younet_companies($content)
    {

        $moduleTb = Engine_Api::_()->getDbtable('modules', 'core');

        if (!$moduleTb->isModuleEnabled('ynjobposting')) {
            return;
        }

        $table = Engine_Api::_() -> getItemTable('ynjobposting_company');
        $select = $table->select()
          ->setIntegrityCheck(false)
          ->from(array('r' => $table->info('name')))
          ->join(array('a' => $content['authTb']), "a.resource_type='ynjobposting_company' && a.resource_id=r.company_id")
          ->where("a.action = ?", 'view')
          ->where("a.role = ?", 'everyone')
          ->where("r.status = ?", 'published')
          ->order('r.company_id DESC')
          ->limit($content['count']);

        if (isset($content['displayed'])) {
            $select->where("r.company_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("r.company_id NOT IN ({$content['blacklist']})");
        }
        return $table->fetchAll($select);
    }
    protected function new_younet_pages($content)
    {

        $table = Engine_Api::_()->getDbtable('business', 'ynbusinesspages');
        $select = $table->select()
          ->setIntegrityCheck(false)
          ->from(array('p' => $table->info('name')))
          ->join(array('a' => $content['authTb']), "a.resource_type='ynbusinesspages_business' && " . "a.resource_id=p.business_id")
          ->where("a.action = ?", 'view')
          ->where("a.role = ?", 'everyone')
          ->where('p.business_id>?', $content['last_sent_id'])
          ->where('p.status=?','published')
          ->order('p.business_id DESC')
          ->limit($content['count']);

        if (isset($content['displayed'])) {
            $select->where("p.business_id IN ({$content['displayed']})");
        }

        if (isset($content['blacklist'])) {
            $select->where("p.business_id NOT IN ({$content['blacklist']})");
        }

        return $table->fetchAll($select);
    }
}