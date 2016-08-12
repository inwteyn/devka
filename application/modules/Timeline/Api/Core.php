<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2012-02-01 16:58:20 mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Timeline_Api_Core extends Core_Api_Abstract
{
    protected $_params = array();
    /**
     * @var $_subject Page_Model_Page
     */
    protected $_subject;

    /**
     * @var Zend_View
     */
    protected $_view;

    protected $feedTabParams = array(
        'ipp' => 9,
        'limit' => 9
    );

    public function getHeight($img)
    {
        $file = '';

        if (is_file($img)) {
            $file = $img;
        } elseif (is_file($_SERVER['DOCUMENT_ROOT'] . $img)) {
            $file = $_SERVER['DOCUMENT_ROOT'] . $img;
        } else {
            preg_match('/< *img[^>]*src *= *["\']?([^?"\']*)/i', $img, $matches);

            foreach ($matches as $match) {
                if (is_file($match)) {
                    $file = $match;
                    break;
                } elseif (is_file($_SERVER['DOCUMENT_ROOT'] . $match)) {
                    $file = $_SERVER['DOCUMENT_ROOT'] . $match;
                    break;
                }
            }
        }

        if (is_file($file)) {
            $size = getimagesize($file);
            if (array_key_exists(1, $size) && is_numeric($size[1])) return $size[1];
        }

        return 0;
    }

    public function getApplicationsOld($content)
    {
        // Don't render this if subject doesn't exist
        if (!Engine_Api::_()->core()->hasSubject()) {
            return null;
        }

        // Get subject
        $this->_subject = $subject = Engine_Api::_()->core()->getSubject();

        try {
            $allApplications = require APPLICATION_PATH_MOD . '/Timeline/settings/applications.php';
            if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('touch') && Engine_Api::_()->touch()->isTouchMode())
                $allApplications = require APPLICATION_PATH_MOD . '/Touch/settings/tl_apps.php';
        } catch (Exception $e) {
            print_log($e);
            return array();
        }

        /**
         * @var $table Core_Model_DbTable_Modules
         */
        $mTable = Engine_Api::_()->getDbTable('modules', 'core');
        $applications = array();

        foreach ($content as $widget) {

            if (
                !array_key_exists($widget->name, $allApplications) ||
                !$mTable->isModuleEnabled($allApplications[$widget->name]['module'])
            ) continue;

            $application = $allApplications[$widget->name];

            try {
                $parts = explode('.', $widget->name);

                $applications[$widget->name] = $application;

                if (isset($allApplications[$widget->name]) &&
                    array_key_exists('render', $allApplications[$widget->name]) &&
                    !$allApplications[$widget->name]['render']
                ) continue;

                $parts = explode('-', $parts[1]);
                foreach ($parts as $key => $value) {
                    if ($key == 0) continue;
                    $parts[$key] = ucfirst($value);
                }

                $method = '_' . implode('', $parts);

                /*$this->_params = $widget->params;

                if (method_exists($this, $method)) {
                    $applications[$widget->name]['items'] = $this->$method();
                }*/
            } catch (Exception $e) {
                print_log($e);
            }
        }

        return $applications;
    }

    public function getPageApplications($content)
    {
        // Don't render this if subject doesn't exist
        if (!Engine_Api::_()->core()->hasSubject()) {
            return null;
        }

        // Get subject
        $this->_subject = $subject = Engine_Api::_()->core()->getSubject('page');

        try {
            $allApplications = require APPLICATION_PATH_MOD . '/Timeline/settings/page_applications.php';
        } catch (Exception $e) {
            print_log($e);
            return array();
        }
        /**
         * @var $table Core_Model_DbTable_Modules
         */
        $mTable = Engine_Api::_()->getDbTable('modules', 'core');
        $applications = array();

        foreach ($content as $widget) {

            if (
                !array_key_exists($widget->name, $allApplications) ||
                !$mTable->isModuleEnabled($allApplications[$widget->name]['module'])
            ) {
                $test[] = $widget->name;
                continue;
            }

            $application = $allApplications[$widget->name];

            try {
                $parts = explode('.', $widget->name);

                $applications[$widget->name] = $application;

                if (
                    array_key_exists('render', $allApplications[$widget->name]) &&
                    !$allApplications[$widget->name]['render']
                ) continue;

                $parts = explode('-', $parts[1]);
                foreach ($parts as $key => $value) {
                    if ($key == 0) continue;
                    $parts[$key] = ucfirst($value);
                }

                $method = '_' . implode('', $parts);

                $this->_params = $widget->params;

                if (method_exists($this, $method)) {
                    $applications[$widget->name]['items'] = $this->$method();
                }
            } catch (Exception $e) {
                print_log($e);
            }
        }

        return $applications;
    }

    /********************************* Timeline Page ***********************************/
    /* _profileAlbum */
    protected function _profileAlbum()
    {
        /**
         * Get paginator
         *
         * @var $paginator Zend_Paginator
         */
        $select = Engine_Api::_()->getItemTable('pagealbum')->select()
            ->where('page_id = ?', $this->_subject->getIdentity())
            ->limit($this->limit)
            ->order('RAND()');

        $this->view->paginator = $paginator = Zend_Paginator::factory($select);

        // Set item count per page and current page number
        $paginator->setItemCountPerPage($this->ipp);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        // Do not render if nothing to show
        if ($paginator->getTotalItemCount() <= 0) {
            return null;
        }

        return $paginator;
    }

    /* _profileDocument */
    protected function _profileDocument()
    {
        /**
         * Get paginator
         *
         * @var $paginator Zend_Paginator
         */
        $select = Engine_Api::_()->getItemTable('pagedocument')->select()
            ->where('page_id = ?', $this->_subject->getIdentity())
            ->limit($this->limit)
            ->order('RAND()');

        $this->view->paginator = $paginator = Zend_Paginator::factory($select);

        // Set item count per page and current page number
        $paginator->setItemCountPerPage($this->ipp);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        // Do not render if nothing to show
        if ($paginator->getTotalItemCount() <= 0) {
            return null;
        }

        return $paginator;
    }

    /* _profileDiscussion */
    protected function _profileEvent()
    {
        /**
         * Get paginator
         *
         * @var $paginator Zend_Paginator
         */
        $select = Engine_Api::_()->getItemTable('pageevent')->select()
            ->where('page_id = ?', $this->_subject->getIdentity())
            ->limit($this->limit)
            ->order('RAND()');

        $this->view->paginator = $paginator = Zend_Paginator::factory($select);

        // Set item count per page and current page number
        $paginator->setItemCountPerPage($this->ipp);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        // Do not render if nothing to show
        if ($paginator->getTotalItemCount() <= 0) {
            return null;
        }

        return $paginator;
    }

    /* _profileVideo */
    protected function _profileVideo()
    {
        /**
         * Get paginator
         *
         * @var $paginator Zend_Paginator
         */
        $select = Engine_Api::_()->getItemTable('pagevideo')->select()
            ->where('page_id = ?', $this->_subject->getIdentity())
            ->limit($this->limit)
            ->order('RAND()');

        $this->view->paginator = $paginator = Zend_Paginator::factory($select);

        // Set item count per page and current page number
        $paginator->setItemCountPerPage($this->ipp);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        // Do not render if nothing to show
        if ($paginator->getTotalItemCount() <= 0) {
            return null;
        }

        return $paginator;
    }


    /********************************* Timeline Page ***********************************/

    var $limit = 9;
    var $ipp = 9;

    protected function _profileFriends()
    {
        // Multiple friend mode
        $select = $this->_subject->membership()->getMembersOfSelect();
        $friends = $paginator = Zend_Paginator::factory($select);

        // Do not render if nothing to show
        if ($paginator->getTotalItemCount() <= 0) {
            return null;
        }

        // Get stuff
        $ids = array();
        foreach ($friends as $friend) {
            $ids[] = $friend->resource_id;
        }

        $table = Engine_Api::_()->getItemTable('user');
        $select = $table
            ->select()
            ->where('user_id IN( ' . implode(',', $ids) . ')')
            ->limit($this->limit)
            ->order('RAND()');
        $paginator = Zend_Paginator::factory($select);

        $paginator->setItemCountPerPage($this->ipp);
        $paginator->setCurrentPageNumber(1);

        return $paginator;
    }

    protected function _profileFriendsFollowers()
    {

        // Multiple friend mode
        $select = $this->_subject->membership()->getMembersSelect();
        $friends = $paginator = Zend_Paginator::factory($select);


        // Do not render if nothing to show
        if ($paginator->getTotalItemCount() <= 0) {
            return null;
        }


        // Get stuff
        $ids = array();
        foreach ($friends as $friend) {
            $ids[] = $friend->user_id;
        }

        $table = Engine_Api::_()->getItemTable('user');
        $select = $table
            ->select()
            ->limit($this->limit)
            ->where('user_id IN( ' . implode(',', $ids) . ')')
            ->order('RAND()');

        $paginator = Zend_Paginator::factory($select);
        $paginator->setItemCountPerPage($this->ipp);
        $paginator->setCurrentPageNumber(1);

        return $paginator;
    }

    protected function _profileFriendsFollowing()
    {
        // Multiple friend mode
        $select = $this->_subject->membership()->getMembersOfSelect();
        $friends = $paginator = Zend_Paginator::factory($select);


        // Do not render if nothing to show
        if ($paginator->getTotalItemCount() <= 0) {
            return null;
        }

        // Get stuff
        $ids = array();
        foreach ($friends as $friend) {
            $ids[] = $friend->resource_id;
        }

        $table = Engine_Api::_()->getItemTable('user');
        $select = $table
            ->select()
            ->where('user_id IN( ' . implode(',', $ids) . ')')
            ->limit($this->limit)
            ->order('RAND()');

        $paginator = Zend_Paginator::factory($select);
        $paginator->setItemCountPerPage($this->ipp);
        $paginator->setCurrentPageNumber(1);

        return $paginator;
    }

    protected function _profileAlbums()
    {
        /**
         * Get paginator
         *
         * @var $paginator Zend_Paginator
         */
        $select = Engine_Api::_()->getItemTable('album')->select()
            ->where('owner_type = ?', $this->_subject->getType())
            ->where('owner_id = ?', $this->_subject->getIdentity())
            ->limit($this->limit)
            ->order('RAND()');

        $this->view->paginator = $paginator = Zend_Paginator::factory($select);

        // Set item count per page and current page number
        $paginator->setItemCountPerPage($this->ipp);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        // Do not render if nothing to show
        if ($paginator->getTotalItemCount() <= 0) {
            return null;
        }

        return $paginator;
    }

    protected function _profileEvents()
    {
        /**
         * Get paginator
         *
         * @var $paginator Zend_Paginator
         */
        $membership = Engine_Api::_()->getDbtable('membership', 'event');
        $this->view->paginator = $paginator = Zend_Paginator::factory($membership->getMembershipsOfSelect($this->_subject)->order('rand()'));

        // Set item count per page and current page number
        $paginator->setItemCountPerPage($this->ipp);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        // Do not render if nothing to show
        if ($paginator->getTotalItemCount() <= 0) {
            return null;
        }

        return $paginator;
    }

    protected function _profileGroups()
    {
        /**
         * Get paginator
         *
         * @var $paginator Zend_Paginator
         */
        $membership = Engine_Api::_()->getDbtable('membership', 'group');
        $paginator = Zend_Paginator::factory($membership->getMembershipsOfSelect($this->_subject)->order('rand()'));

        // Set item count per page and current page number
        $paginator->setItemCountPerPage($this->limit);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        // Do not render if nothing to show
        if ($paginator->getTotalItemCount() <= 0) {
            return null;
        }

        return $paginator;
    }

    protected function _profilePages()
    {
        /**
         * Get paginator
         *
         * @var $paginator Zend_Paginator
         */
        $table = Engine_Api::_()->getDbtable('membership', 'page');
        $itemTable = Engine_Api::_()->getDbTable('pages', 'page');

        $itName = $itemTable->info('name');
        $mtName = $table->info('name');
        $col = current($itemTable->info('primary'));

        $select = $itemTable->select()
            ->setIntegrityCheck(false)
            ->from($itName)
            ->joinLeft($mtName, "`{$mtName}`.`resource_id` = `{$itName}`.`{$col}`", array('admin_title' => "{$mtName}.title"))
            ->where("`{$mtName}`.`user_id` = ?", $this->_subject->getIdentity())
            ->where("`{$mtName}`.`active` = 1")
            ->where("`{$itName}`.`approved` = 1")
            ->limit($this->limit)
            ->order('RAND()');

        $paginator = Zend_Paginator::factory($select);
        $paginator->setItemCountPerPage($this->ipp);

        // Do not render if nothing to show
        if ($paginator->getTotalItemCount() <= 0) {
            return null;
        }

        return $paginator;
    }

    protected function _profileClassifieds()
    {
        /**
         * Get paginator
         *
         * @var $table Classified_Model_DbTable_Classifieds
         * @var $paginator Zend_Paginator
         */
        $table = Engine_Api::_()->getItemTable('classified');
        $paginator = $table->getClassifiedsPaginator(array(
            'user_id' => $this->_subject->getIdentity(),
            'orderby' => 'creation_date',
        ));

        // Set item count per page and current page number
        $paginator->setItemCountPerPage($this->ipp);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        // Do not render if nothing to show
        if ($paginator->getTotalItemCount() <= 0) {
            return null;
        }

        return $paginator;
    }

    protected function _profileLikes()
    {
        /**
         * Get paginator
         *
         * @var $api Like_Api_Core
         * @var $table Core_Model_DbTable_Likes
         */
        $api = Engine_Api::_()->like();
        $itemTypes = array_keys($api->getSupportedModulesLabels());
        $table = Engine_Api::_()->getDbTable('likes', 'core');
        $select = $table->select()
            ->where('poster_type = ?', $this->_subject->getType())
            ->where('poster_id = ?', $this->_subject->getIdentity())
            ->where('resource_type IN ("' . implode('","', $itemTypes) . '")')
            ->order('RAND()')
            ->limit($this->limit);
        $items = array();
        foreach ($table->fetchAll($select) as $row) {
            if (null == ($item = Engine_Api::_()->getItem($row->resource_type, $row->resource_id))) continue;
            $items[] = $item;

            if (count($items) == 2) break;
        }

        return $items;
    }

    protected function _profileVideos()
    {
        /**
         * Get paginator
         *
         * @var $api Video_Api_Core
         * @var $paginator Zend_Paginator
         */
        $api = Engine_Api::_()->getApi('core', 'video');
        $paginator = $api->getVideosPaginator(array(
            'user_id' => $this->_subject->getIdentity(),
            'status' => 1,
            'search' => 1,
            'limit' => $this->limit,
        ));

        // Set item count per page and current page number
        $paginator->setItemCountPerPage($this->ipp);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        // Do not render if nothing to show
        if ($paginator->getTotalItemCount() <= 0) {
            return null;
        }

        return $paginator;
    }

    /*for std*/
    /*for touch {*/
    protected function _userProfileFriends()
    {
        return $this->_profileFriends();
    }

    protected function _albumProfileAlbums()
    {
        return $this->_profileAlbums();
    }

    protected function _eventProfileEvents()
    {
        return $this->_profileEvents();
    }

    protected function _groupProfileGroups()
    {
        return $this->_profileGroups();
    }

    protected function _pagesProfilePages()
    {
        return $this->_profilePages();
    }

    protected function _classifiedsProfileClassifieds()
    {
        return $this->_profileClassifieds();
    }

    protected function _likeProfileLikes()
    {
        return $this->_profileLikes();
    }

    protected function _videoProfileVideos()
    {
        return $this->_profileVideos();
    }

    /*} for touch */

    protected function _getParam($name, $default = null)
    {
        if (array_key_exists($name, $this->_params))
            return $this->_params[$name];

        return $default;
    }

    public function getSupportedItems()
    {
        return array(
            'user',
            'group',
            'page'
        );
    }

    public function timelineDates(Timeline_Model_User $subject)
    {
        /**
         * Timeline Dates
         *
         * @var $actionsTb Wall_Model_DbTable_Actions
         */
        $actionsTb = Engine_Api::_()->getDbTable('actions', 'wall');

        $birthdate = $subject->getBirthdate();

        $select = $actionsTb
            ->select()
            ->setIntegrityCheck(false)
            ->from(
                $actionsTb->info('name'),
                array(
                    'year' => 'DATE_FORMAT(`date`, \'%Y\')',
                    'month' => 'DATE_FORMAT(`date`, \'%m\')',
                    'UNIX_TIMESTAMP(MAX(date)) AS date',
                    'max_id' => 'MAX(action_id)',
                    'min_id' => 'MIN(action_id)'
                ))
            ->where('subject_type = ?', 'user')
            ->where('subject_id = ?', $subject->getIdentity());

        if ($birthdate) {
            $select->where('date >= ?', $birthdate);
        }

        $select
            ->orWhere('object_type = ?', 'user')
            ->where('object_id = ?', $subject->getIdentity());

        if ($birthdate) {
            $select->where('date >= ?', $birthdate);
        }

        $select
            ->group('DATE_FORMAT(`date`, \'%Y%m\')')
            ->order('date DESC');

        $dates = $actionsTb->fetchAll($select);

        return $this->_reorderDates($dates->toArray(), $birthdate);
    }

    public function timelinePageDates(Page_Model_Page $subject)
    {
        /**
         * Timeline Dates
         *
         * @var $actionsTb Wall_Model_DbTable_Actions
         */
        $actionsTb = Engine_Api::_()->getDbTable('actions', 'wall');

        $birthdate = $subject->creation_date;

        $select = $actionsTb
            ->select()
            ->setIntegrityCheck(false)
            ->from(
                $actionsTb->info('name'),
                array(
                    'year' => 'DATE_FORMAT(`date`, \'%Y\')',
                    'month' => 'DATE_FORMAT(`date`, \'%m\')',
                    'UNIX_TIMESTAMP(MAX(date)) AS date',
                    'max_id' => 'MAX(action_id)',
                    'min_id' => 'MIN(action_id)'
                ))
            ->where('subject_type = ?', 'page')
            ->where('subject_id = ?', $subject->getIdentity());

        if ($birthdate) {
            $select->where('date >= ?', $birthdate);
        }

        $select
            ->orWhere('object_type = ?', 'page')
            ->where('object_id = ?', $subject->getIdentity());

        if ($birthdate) {
            $select->where('date >= ?', $birthdate);
        }

        $select
            ->group('DATE_FORMAT(`date`, \'%Y%m\')')
            ->order('date DESC');

        $dates = $actionsTb->fetchAll($select);
        return $this->_reorderDates($dates->toArray(), $birthdate);
    }

    public function timelineGroupDates(Group_Model_Group $subject)
    {
        /**
         * Timeline Dates
         *
         * @var $actionsTb Wall_Model_DbTable_Actions
         */
        $actionsTb = Engine_Api::_()->getDbTable('actions', 'wall');

        $birthdate = $subject->creation_date;

        $select = $actionsTb
            ->select()
            ->setIntegrityCheck(false)
            ->from(
                $actionsTb->info('name'),
                array(
                    'year' => 'DATE_FORMAT(`date`, \'%Y\')',
                    'month' => 'DATE_FORMAT(`date`, \'%m\')',
                    'UNIX_TIMESTAMP(MAX(date)) AS date',
                    'max_id' => 'MAX(action_id)',
                    'min_id' => 'MIN(action_id)'
                ))
            ->where('subject_type = ?', $subject->getType())
            ->where('subject_id = ?', $subject->getIdentity());

        if ($birthdate) {
            $select->where('date >= ?', $birthdate);
        }

        $select
            ->orWhere('object_type = ?', $subject->getType())
            ->where('object_id = ?', $subject->getIdentity());

        if ($birthdate) {
            $select->where('date >= ?', $birthdate);
        }

        $select
            ->group('DATE_FORMAT(`date`, \'%Y%m\')')
            ->order('date DESC');

        $dates = $actionsTb->fetchAll($select);
        return $this->_reorderDates($dates->toArray(), $birthdate);
    }

    protected function _reorderDates(array $dates_array, $birthdate = null)
    {
        $dates = array();
        $year = date('Y', time());
        $month = date('m', time());
        $translate = Zend_Registry::get('Zend_Translate');

        foreach ($dates_array as $key => $date) {
            $date['name'] = $translate->_(date('F', strtotime($date['year'] . '-' . $date['month'] . '-01')));
            $date['title'] = date('M', strtotime($date['year'] . '-' . $date['month'] . '-01'));
            $date['key'] = $date['year'] . '-' . $date['month'];

            if ($year == $date['year'] && $month == $date['month']) {
                $date['title'] = 'Now';
                $dates['now'] = $date;
                continue;
            }

            if (!isset($dates['last_month'])) {
                $date['title'] = $date['name'];
                $dates['last_month'] = $date;
                continue;
            }

            $dates['years']['y' . $date['year']]['m' . $date['month']] = $date;
        }

        if ($birthdate) {
            $b_arr = explode('-', $birthdate);
            $dates['born']['year'] = (int)$b_arr[0];
            $dates['born']['month'] = ($b_arr[1] < 9) ? '0' . $b_arr[1] : $b_arr[1];
            $dates['born']['day'] = ($b_arr[2] < 9) ? '0' . $b_arr[2] : $b_arr[2];
        }
        return $dates;
    }

    public function get_age($birthday)
    {
        list($by, $bm, $bd) = explode('-', $birthday);
        list($cd, $cm, $cy) = explode('-', date('d-m-Y'));
        $cd -= $bd;
        $cm -= $bm;
        $cy -= $by;
        if ($cd < 0) $cm--;
        if ($cm < 0) $cy--;
        return $cy;
    }


    public function getApplications($content)
    {
        // Don't render this if subject doesn't exist
        if (!Engine_Api::_()->core()->hasSubject()) {
            return null;
        }

        // Get subject
        $this->_subject = $subject = Engine_Api::_()->core()->getSubject();

        try {
            $allApplications = require APPLICATION_PATH_MOD . '/Timeline/settings/applications.php';
            if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('touch') && Engine_Api::_()->touch()->isTouchMode())
                $allApplications = require APPLICATION_PATH_MOD . '/Touch/settings/tl_apps.php';
        } catch (Exception $e) {
            print_log($e);
            return array();
        }
        /**
         * @var $table Core_Model_DbTable_Modules
         */
        $mTable = Engine_Api::_()->getDbTable('modules', 'core');
        $applications = array();

        foreach ($content as $widget) {

            if (
                !array_key_exists($widget['name'], $allApplications) ||
                !$mTable->isModuleEnabled($allApplications[$widget['name']]['module'])
            ) {
                continue;
            }

            $application = $allApplications[$widget['name']];

            try {
                $parts = explode('.', $widget['name']);

                $applications[$widget['name']] = $application;

                if (isset($allApplications[$widget['name']]) &&
                    array_key_exists('render', $allApplications[$widget['name']]) &&
                    !$allApplications[$widget['name']]['render']
                ) {
                    continue;
                }

                $parts = explode('-', $parts[1]);
                foreach ($parts as $key => $value) {
                    if ($key == 0) continue;
                    $parts[$key] = ucfirst($value);
                }

                $method = '_' . implode('', $parts);

                $this->_params = $this->feedTabParams;

                if (method_exists($this, $method)) {
                    $applications[$widget['name']]['items'] = $this->$method();
                }
            } catch (Exception $e) {
                throw $e;
            }
        }

        return $applications;
    }

    public function getWidgetTabs($str_array,$element = null, $activeTab = null)
    {

        if (!$element) {
            return array();
        }
        $tabs = array();

        foreach ($element->getElements() as $child) {

            if($str_array['type'] == 'page') {
                $b = explode(".", $child->getName());
                $r = $b[0];
                if (!in_array($r, $str_array)) {
                    continue;

                }
            }
            if (null === $activeTab) {
                $activeTab = $child->getIdentity();
            }


            $container_class = $child->getDecorator('Container')->getClass();

            $child->clearDecorators();

            $id = $child->getIdentity();
            $title = $child->getTitle();
            $name = $child->getName();

            $childCount = null;

            if (!$title) $title = $name;

            $content = $child->render();
            if (!$child->getNoRender()) {
                $tab = array(
                    'id' => $id,
                    'name' => $name,
                    'containerClass' => $container_class,
                    'title' => $title,
                    'content' => $content . PHP_EOL
                );



                if (method_exists($child, 'getWidget')) {
                    if (method_exists($child->getWidget(), 'getChildCount')) {
                        $childCount = $child->getWidget()->getChildCount();
                    } else {
                        $childCount = -2;
                    }
                }
                $tab['childCount'] = $childCount;

                $tabs[] = $tab;
            }
        }

        return $tabs;
    }

    public function getProfileOptions($type = null)
    {
        if (!$type)
            return null;

        $options = null;

        try {
            $options = Engine_Api::_()->getApi('menus', 'core')->getNavigation($type . '_profile');
        } catch (Exception $e) {

        }

        return $options;
    }

    public function getCoverPhotoSetting($item_id, $item_type, $photo_type = 'cover')
    {
        return trim($item_type . '_' . $photo_type . '_photo_' . $item_id);
    }

    public function getCoverPhotoPositionSetting($item_id, $item_type, $photo_type = 'cover')
    {
        return $item_type . '_' . $photo_type . '_position_' . $item_id;
    }

    public function getCoverParentSetting($item_id, $item_type, $photo_type = 'cover')
    {
        return $item_type . '_' . $photo_type . '_parent_' . $item_id;
    }

    public function getTimelinePhotoObject($item_id = null, $item_type = null, $photo_type = 'cover')
    {
        if (!$item_id || !$item_type) {
            return null;
        }

        $setting = $this->getCoverPhotoSetting($item_id, $item_type, $photo_type);
        if (!$setting) {
            return null;
        }

        $settings = Engine_Api::_()->getDbTable('settings', 'core');
        $id = $settings->getSetting($setting, 0);
        $parent_type = $settings->getSetting($this->getCoverParentSetting($item_id, $item_type, $photo_type), 'storage');

        if (!$id) {
            return '';
        }
        if ($parent_type == 'album' || $parent_type == 'pagealbum') {
            $photosTable = $this->getSubjectPhotosTable($item_type, $item_id, $parent_type);

            if (!$photosTable) return null;

            $field = 'photo_id';
            if ($parent_type == 'pagealbum') {
                $field = 'pagealbumphoto_id';
            }

            $select = $photosTable->select()
                ->where($field . ' = ?', $id)
                ->limit(1);
            $photo = $photosTable->fetchRow($select);

            return ($photo) ? $photo : null;
        }

        $table = Engine_Api::_()->getDbTable('files', 'storage');
        $file = $table->getFile($id);

        if ($file) {
            $src = $file->map();
            if ($src)
                return $src;
        }

        return null;
    }

    public function getProfilePhoto($subject = null, $type = null, $id = null)
    {

        $result = array(
            'photoHref' => 'javascript://',
            'photoSrc' => ''
        );

        if (!$subject) {
            return $result;
        }

        if (!$type) $type = $subject->getType();
        if (!$id) $id = $subject->getIdentity();

        $result['photoSrc'] = Engine_Api::_()->timeline()->getNoPhoto($subject, 'thumb.profile');

        if (!empty($subject->photo_id)) {
            $photosTable = Engine_Api::_()->timeline()->getSubjectPhotosTable($type, $id);

            if (!$photosTable) {
                $result['photoSrc'] = $subject->getPhotoUrl();
                return $result;
            }

            $file = Engine_Api::_()->getItemTable('storage_file')->getFile($subject->photo_id, 'thumb.profile');
            if ($file) {
                $select = $photosTable->select()->where('file_id=?', $file->getIdentity());
                $photo = $photosTable->fetchRow($select);

                if ($photo && $type != 'offer') {
                    $result['photoHref'] = $photo->getHref();
                    $result['photoSrc'] = $photo->getHref();
                } else {
                    $result['photoSrc'] = $file->map();
                }
            }

        }

        return $result;
    }


    public function getTimelinePhoto($item_id = null, $item_type = null, $photo_type = 'cover')
    {

        $settings = Engine_Api::_()->getDbTable('settings', 'core');
        $setting_position = Engine_Api::_()->timeline()->getCoverPhotoPositionSetting($item_id, $item_type);
        $position = $settings->getSetting($setting_position);

        $position = ($position) ? unserialize($position) : array('top' => 0, 'left' => 0);

        $result = array(
            'photoHref' => 'javascript://',
            'photoSrc' => 'application/modules/Timeline/externals/images/default_cover.jpg',
            'position' => json_encode(array('top' => 0, 'left' => 0))
        );

        if (!$item_id || !$item_type) {
            return $result;
        }

        $setting = $this->getCoverPhotoSetting($item_id, $item_type, $photo_type);

        if (!$setting) {
            return $result;
        }

        $settings = Engine_Api::_()->getDbTable('settings', 'core');
        $id = $settings->getSetting($setting, 0);

        if (!$id) {
            return $result;
        }

        $parent_type = $settings->getSetting($this->getCoverParentSetting($item_id, $item_type, $photo_type), 'storage');

        if ($parent_type == 'album' || $parent_type == 'pagealbum') {

            $photosTable = $this->getSubjectPhotosTable($item_type, $item_id, $parent_type);

            if (!$photosTable) {
                return $result;
            }

            $field = 'photo_id';
            if ($parent_type == 'pagealbum') {
                $field = 'pagealbumphoto_id';
            }

            $select = $photosTable->select()
                ->where($field . ' = ?', $id)
                ->limit(1);
            $photo = $photosTable->fetchRow($select);
            if ($photo) {
                $result['photoHref'] = $photo->getHref();
                $result['position'] = json_encode($position);

                $id = $photo->file_id;
            } else {
                return $result;
            }
        }

        $table = Engine_Api::_()->getDbTable('files', 'storage');
        $file = $table->getFile($id);

        if ($file) {
            $src = $file->map();
            if ($src) {
                $result['photoSrc'] = $src;
                $result['position'] = json_encode($position);
            } else {
                $result['position'] = json_encode(array('top' => 0, 'left' => 0));
            }
        }
        return $result;
    }

    public function getSubjectAlbums($item_id = null, $item_type = null, $table = null)
    {
        $select = null;
        if (!$item_id || !$item_type || !$table) {
            return $select;
        }

        if ($item_type == 'user') {
            $select = $table->select()
                ->where('owner_id = ?', $item_id)
                ->order('view_count DESC');
            return Zend_Paginator::factory($select);
        } else if ($item_type == 'page') {

            $page = Engine_Api::_()->getItem('page', $item_id);

            $select = $table->select()
                ->where('owner_id = ?', $page->getOwner()->getIdentity())
                ->order('view_count DESC');

            $albums = $table->fetchAll($select);
            $albums = $albums->toArray();

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('pagealbum')) {
                $table = Engine_Api::_()->getItemTable('pagealbum');
                $select = $table->select()
                    ->where('page_id = ?', $page->getIdentity())
                    ->order('view_count DESC');

                $page_albums = $table->fetchAll($select);
                $page_albums = $page_albums->toArray();
                if (!empty($page_albums)) {
                    $albums = array_merge($albums, $page_albums);
                }
            }
            return Zend_Paginator::factory($albums);
        }
        return null;
    }

    public function getSubjectAlbumTable($item_type = null, $item_id = null)
    {
        if (!$item_type) {
            return null;
        }
        $table = Engine_Api::_()->getDbtable('albums', 'timeline');
        switch ($item_type) {
            case 'page':
                break;
            case 'event':
                $table = Engine_Api::_()->getDbtable('albums', 'event');
                break;
            case 'offer':
                break;
            case 'group':
                $table = Engine_Api::_()->getDbtable('albums', 'group');
                break;
            default:
                break;
        }
        return $table;
    }

    public function getSubjectPhotosTable($item_type = null, $item_id = null, $parent_type = null)
    {
        if (!$item_type) {
            return null;
        }

        $table = (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('album')) ? Engine_Api::_()->getDbtable('photos', 'album') : null;

        switch ($item_type) {
            case 'page':
                if (!$table) {
                    return null;
                }
                $page = Engine_Api::_()->getItem($item_type, $item_id);
                $tbl = Engine_Api::_()->getDbtable('albums', 'timeline');
                $album = $tbl->getSpecialPageAlbum($page->getOwner(), $page, 'page_cover');

                $album_type = $album->getType();

                $isPageAlbumsEnabled = (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('pagealbum') && ($album_type == 'pagealbum'));

                if ($isPageAlbumsEnabled && $parent_type != 'album') {
                    $table = Engine_Api::_()->getDbtable('pagealbumphotos', 'pagealbum');
                }
                break;
            case 'event':
                $table = Engine_Api::_()->getDbtable('photos', 'event');
                break;
            case 'offer':
                $table = Engine_Api::_()->getDbtable('photos', 'offers');
                break;
            case 'group':
                $table = Engine_Api::_()->getDbtable('photos', 'group');
                break;
            default:
                break;
        }

        return $table;
    }

    public function getNoPhoto($item, $type)
    {
        $type = ($type ? str_replace('.', '_', $type) : 'main');

        if (($item instanceof Core_Model_Item_Abstract)) {
            $item = $item->getType();
        } else if (!is_string($item)) {
            return '';
        }

        if (!Engine_Api::_()->hasItemType($item)) {
            return '';
        }

        // Load from registry
        if (null === $_noPhotos) {
            // Process active themes
            $themesInfo = Zend_Registry::get('Themes');
            foreach ($themesInfo as $themeName => $themeInfo) {
                if (!empty($themeInfo['nophoto'])) {
                    foreach ((array)@$themeInfo['nophoto'] as $itemType => $moreInfo) {
                        if (!is_array($moreInfo)) continue;
                        $_noPhotos[$itemType] = array_merge((array)@$_noPhotos[$itemType], $moreInfo);
                    }
                }
            }
        }

        // Use default
        $view = new Zend_View();
        if (!isset($_noPhotos[$item][$type])) {
            $shortType = $item;
            if (strpos($shortType, '_') !== false) {
                list($null, $shortType) = explode('_', $shortType, 2);
            }
            $module = Engine_Api::_()->inflect(Engine_Api::_()->getItemModule($item));
            $_noPhotos[$item][$type] = //$this->view->baseUrl() . '/' .
                $view->layout()->staticBaseUrl . 'application/modules/' .
                $module .
                '/externals/images/nophoto_' .
                $shortType . '_'
                . $type . '.png';
        }

        return $_noPhotos[$item][$type];

    }
}