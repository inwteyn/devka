<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecontest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Event.php 19.10.13 08:20 jungar $
 * @author     Jungar
 */

/**
 * @category   Application_Extensions
 * @package    Hecontest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 25.09.13
 * Time: 11:17
 * To change this template use File | Settings | File Templates.
 */
class Hecontest_Model_Hecontest extends Core_Model_Item_Abstract
{
    public function getParent($resourceType = null)
    {
        $user = Engine_Api::_()->getItem('user', $this->user_id);
        if(!$user->getIdentity()) {
            return null;
        }
        return $user;
    }

    public function getOwner($resourceType = null)
    {
        $user = Engine_Api::_()->getItem('user', $this->user_id);
        if(!$user->getIdentity()) {
            return null;
        }
        return $user;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getHref()
    {
            $action = 'contestview';
            $route_name = 'hecontest_general_view';


            $params = array(
              'route' => $route_name,
              'action' => $action,
              'contest_id' => $this->getIdentity(),
              'reset' => true,
            );


        $route = $params['route'];
        $reset = $params['reset'];
        unset($params['route']);
        unset($params['reset']);
        return Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, $reset);
    }
    public function getDescription()
    {
        if( isset($this->description) )
        {
            return htmlspecialchars(strip_tags($this->description));
        }
        return '';
    }
    public function getAdminHref()
    {
        $params = array(
            'route' => 'admin_default',
            'module' => 'hecontest',
            'controller' => 'index',
            'action' => 'view',
            'reset' => true,
            'hecontest_id' => $this->getIdentity(),
        );
        $route = $params['route'];
        $reset = $params['reset'];
        unset($params['route']);
        unset($params['reset']);
        return Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, $reset);
    }

    public function getSponsor()
    {
        return $this->sponsor;
    }

    public function getWinner()
    {
        $table = Engine_Api::_()->getDbTable('photos', 'hecontest');
        $select = $table->select()
            ->where('contest_id=?', $this->getIdentity())
            ->where('status=?', 'approved')
            ->order('votes DESC')
            ->limit(1);

        $winner = $table->fetchRow($select);
        return $winner;
    }

    public function isActive()
    {
        return $this->is_active;
    }

    public function getSponsorArray()
    {
        $page = $this->getSponsorPage();

        if ($page) {
            $sponsor = array(
                'type' => 0,
                'sponsor' => array(
                    'object' => $page,
                    'title' => $page->getTitle(),
                    'href' => $page->getHref()
                )
            );
        } else {
            $sponsor = array(
                'type' => 1,
                'sponsor' => array(
                    'title' => $this->sponsor,
                    'href' => ($this->sponsor_href) ? $this->sponsor_href : 'javascript://'
                )
            );
        }
        return $sponsor;
    }

    public function getSponsorPage()
    {
        if (!$this->sponsor_type) {
            if (Engine_Api::_()->hecontest()->isPageEnabled()) {
                $pagesTbl = Engine_Api::_()->getDbTable('pages', 'page');
                $page = $pagesTbl->fetchRow($pagesTbl->select()->where('url = ?', $this->sponsor_url));
                return $page;
            }
        }
        return null;
    }

    public function getSponsorHtml($id)
    {
        $view = Zend_Registry::get('Zend_View');
        if ($this->sponsor_type) { // just link
            $href = $this->sponsor_href;
            $title = $this->sponsor;
            $rows = <<<ROWS
        <td valign="middle"><a target="_blank" href="{$href}">{$title}</a></td>
ROWS;
        } else { // page
            $page = $this->getSponsorPage();
            if (!$page) {
                return '<td valign="middle">no page</td>';
            }
            $title = $page->getTitle();
            $href = $page->getHref();
            $file = Engine_Api::_()->getItemTable('storage_file')->getFile($page->photo_id, 'thumb.icon');
            if ($file) {
                $photoUrl = $file->map();
            } else {
                $photoUrl = '';
            }
            $like = $view->hecontestLikeButton($page, $id);
            $rows = <<<ROWS
<td valign="middle">
    <a target="_blank" href="{$title}"><img src="{$photoUrl}"></a>
</td>
<td style="width: 90px; overflow: hidden; padding-left:5px;" valign="middle"><a target="_blank" href="{$href}">{$title}</a></td>
<td>{$like}</td>
ROWS;
        }

        $html = <<<HTML
<div>
<table>
    <tr>
        {$rows}
    </tr>
</table>
</div>
HTML;

        return $html;
    }

    public function getParticipant($id)
    {
        $photosTbl = Engine_Api::_()->getDbTable('photos', 'hecontest');
        $select = $photosTbl->select()
            ->where('contest_id=?', $this->getIdentity())
            ->where('photo_id=?', $id);

        $row = $photosTbl->fetchRow($select);
        return $row;
    }

    public function getParticipants($params = array())
    {
        $photosTbl = Engine_Api::_()->getDbTable('photos', 'hecontest');

        $status = isset($params['status']) ? $params['status'] : null;

        $select = $photosTbl->select()
            ->where('contest_id=?', $this->getIdentity());

        if (isset($params['order'])) {
            $select->order($params['order']);
        }

        if ($status) {
            $select->where('status=?', $status);
        }

        if (isset($params['limit'])) {
            $select->limit($params['limit']);
        }

        $participants = $photosTbl->fetchAll($select);
        $paginator = Zend_Paginator::factory($participants);
        $ipp = isset($params['ipp']) ? $params['ipp'] : $paginator->getTotalItemCount();
        $page = isset($params['page']) ? $params['page'] : 1;
        $paginator->setItemCountPerPage($ipp);
        $paginator->setCurrentPageNumber($page);


        return $paginator;
    }

    public function getParticipantsCount()
    {
        $photosTbl = Engine_Api::_()->getDbTable('photos', 'hecontest');
        $select = $photosTbl->select()->where('contest_id=?', $this->getIdentity());
        $participants = $photosTbl->fetchAll($select);
        return count($participants);
    }

    public function setPhoto($photo)
    {
        if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo->getFileName();
            //      $fileName = $file;
        } else if ($photo instanceof Storage_Model_File) {
            $file = $photo->temporary();
            //      $fileName = $photo->name;
        } else if ($photo instanceof Core_Model_Item_Abstract && !empty($photo->file_id)) {
            $tmpRow = Engine_Api::_()->getItem('storage_file', $photo->file_id);
            $file = $tmpRow->temporary();
            //      $fileName = $tmpRow->name;
        } else if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
            //      $fileName = $photo['name'];
        } else if (is_string($photo) && file_exists($photo)) {
            $file = $photo;
            //      $fileName = $photo;
        } else {
            return;
            //throw new User_Model_Exception('invalid argument passed to setPhoto');
        }
        if (!$file) {
            return;
        }

        $name = basename($file);
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $params = array(
            'parent_id' => $this->getIdentity(),
            'parent_type' => 'hecontest_prize_photo'
        );
        // Save
        $storage = Engine_Api::_()->storage();
        $this->removePhotos();

        // Resize image (main)
        $image = Engine_Image::factory();
        $image->open($file)
            ->resize(612, 306)
            ->write($path . '/m_' . $name)
            ->destroy();

        // Resize image (2xtile)
        $image = Engine_Image::factory();
        $image->open($file);
        $defRatio = 4 / 3;
        $imgRatio = $image->width / $image->height;
        $size = array('w' => $image->width, 'h' => $image->height);
        $x = 0;
        $y = 0;
        if ($defRatio < $imgRatio) {
            $size['w'] = $image->height * $defRatio;
            $x = ($image->width - $size['w']) / 2;
        } else {
            $size['h'] = $image->width / $defRatio;
            $y = ($image->height - $size['h']) / 2;
        }
        $image->resample($x, $y, $size['w'], $size['h'], 480, 360)
            ->write($path . '/p_' . $name)
            ->destroy();

        // Resize image (tile)
        /*$image = Engine_Image::factory();
        $image->open($file);*/

        $image = Engine_Image::factory();
        $image->open($file);

        $ratio = $image->width / $image->height;

        $newW = 240;
        $newH = $image->width / $ratio;

        $image->resize($newW, $newH)
            ->write($path . '/in_' . $name)
            ->destroy();

        // Resize image (icon)
        $image = Engine_Image::factory();
        $image->open($file);
        $size = min($image->height, $image->width);
        $x = ($image->width - $size) / 2;
        $y = ($image->height - $size) / 2;

        $image->resample($x, $y, $size, $size, 48, 48)
            ->write($path . '/is_' . $name)
            ->destroy();

        // Store
        $iMain = $storage->create($path . '/m_' . $name, $params);
        $iProfile = $storage->create($path . '/p_' . $name, $params);
        $iIconNormal = $storage->create($path . '/in_' . $name, $params);
        $iSquare = $storage->create($path . '/is_' . $name, $params);

        $iMain->bridge($iProfile, 'thumb.profile');
        //    $iMain->bridge($iProfile, 'thumb.2xtile');
        $iMain->bridge($iIconNormal, 'thumb.normal');
        //    $iMain->bridge($iIconNormal, 'thumb.tile');
        $iMain->bridge($iSquare, 'thumb.icon');

        // Remove temp files
        @unlink($path . '/p_' . $name);
        @unlink($path . '/m_' . $name);
        @unlink($path . '/in_' . $name);
        @unlink($path . '/is_' . $name);

        // Update row
        $this->prize_photo = $iMain->file_id;
        $this->save();
        return $this;
    }
    public function setPhotoMain($photo)
    {
        if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo->getFileName();
            //      $fileName = $file;
        } else if ($photo instanceof Storage_Model_File) {
            $file = $photo->temporary();
            //      $fileName = $photo->name;
        } else if ($photo instanceof Core_Model_Item_Abstract && !empty($photo->file_id)) {
            $tmpRow = Engine_Api::_()->getItem('storage_file', $photo->file_id);
            $file = $tmpRow->temporary();
            //      $fileName = $tmpRow->name;
        } else if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
            //      $fileName = $photo['name'];
        } else if (is_string($photo) && file_exists($photo)) {
            $file = $photo;
            //      $fileName = $photo;
        } else {
            return;
            //throw new User_Model_Exception('invalid argument passed to setPhoto');
        }
        if (!$file) {
            return;
        }

        $name = basename($file);
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $params = array(
            'parent_id' => $this->getIdentity(),
            'parent_type' => 'hecontest_prize_photo'
        );
        // Save
        $storage = Engine_Api::_()->storage();
        $this->removePhotos();

        // Resize image (main)
        $image = Engine_Image::factory();
        $image->open($file)
            ->resize(612, 306)
            ->write($path . '/m_' . $name)
            ->destroy();

        // Resize image (2xtile)
        $image = Engine_Image::factory();
        $image->open($file);
        $defRatio = 4 / 3;
        $imgRatio = $image->width / $image->height;
        $size = array('w' => $image->width, 'h' => $image->height);
        $x = 0;
        $y = 0;
        if ($defRatio < $imgRatio) {
            $size['w'] = $image->height * $defRatio;
            $x = ($image->width - $size['w']) / 2;
        } else {
            $size['h'] = $image->width / $defRatio;
            $y = ($image->height - $size['h']) / 2;
        }
        $image->resample($x, $y, $size['w'], $size['h'], 480, 360)
            ->write($path . '/p_' . $name)
            ->destroy();

        // Resize image (tile)
        /*$image = Engine_Image::factory();
        $image->open($file);*/

        $image = Engine_Image::factory();
        $image->open($file);

        $ratio = $image->width / $image->height;

        $newW = 240;
        $newH = $image->width / $ratio;

        $image->resize($newW, $newH)
            ->write($path . '/in_' . $name)
            ->destroy();

        // Resize image (icon)
        $image = Engine_Image::factory();
        $image->open($file);
        $size = min($image->height, $image->width);
        $x = ($image->width - $size) / 2;
        $y = ($image->height - $size) / 2;

        $image->resample($x, $y, $size, $size, 48, 48)
            ->write($path . '/is_' . $name)
            ->destroy();

        // Store
        $iMain = $storage->create($path . '/m_' . $name, $params);
        $iProfile = $storage->create($path . '/p_' . $name, $params);
        $iIconNormal = $storage->create($path . '/in_' . $name, $params);
        $iSquare = $storage->create($path . '/is_' . $name, $params);

        $iMain->bridge($iProfile, 'thumb.profile');
        //    $iMain->bridge($iProfile, 'thumb.2xtile');
        $iMain->bridge($iIconNormal, 'thumb.normal');
        //    $iMain->bridge($iIconNormal, 'thumb.tile');
        $iMain->bridge($iSquare, 'thumb.icon');

        // Remove temp files
        @unlink($path . '/p_' . $name);
        @unlink($path . '/m_' . $name);
        @unlink($path . '/in_' . $name);
        @unlink($path . '/is_' . $name);

        // Update row
        $this->photo_id = $iMain->file_id;
        $this->save();
        return $this;
    }
    public function getPrice(){
        if($this->price_credit>0){
            return $this->price_credit;
        }else{
            return 0;
        }
    }
    public function removePhotos()
    {
        if (isset($this->prize_photo) && $this->prize_photo != 0) {
            $storage = Engine_Api::_()->storage();
            $file = $storage->get($this->prize_photo);
            if ($file !== null) {
                $file->delete();
            }
            $file = $storage->get($this->prize_photo, 'thumb.profile');
            if ($file !== null) {
                $file->delete();
            }
            $file = $storage->get($this->prize_photo, 'thumb.normal');
            if ($file !== null) {
                $file->delete();
            }
            $file = $storage->get($this->prize_photo, 'thumb.icon');
            if ($file !== null) {
                $file->delete();
            }
        }
    }

    public function getPhotoUrl($itemType = "hecontest_prize_photo", $type = "thumb.normal")
    {
        if (empty($this->photo_id)) {
            $view = Zend_Registry::get('Zend_View');
            return $view->layout()->staticBaseUrl . 'application/modules/Hecontest/externals/images/hecontest-no-prize-photo.png';
        }

        $file = Engine_Api::_()->getItemTable('storage_file')->getFile($this->photo_id, $type);
        if (!$file) {
            return null;
        }

        return $file->map();
    }

    public function isParticipant($user_id = 0)
    {
        if (!$user_id)
            return false;

        $photosTbl = Engine_Api::_()->getDbTable('photos', 'hecontest');
        $select = $photosTbl->select()
            ->from($photosTbl, array('count(*) as cnt'))
            ->where('user_id=?', $user_id)
            ->where('contest_id=?', $this->getIdentity());
        $result = $photosTbl->fetchRow($select);
        return $result->cnt;
    }

    public function destroy()
    {
        $photosTbl = Engine_Api::_()->getDbTable('photos', 'hecontest');
        $photos = $photosTbl->fetchAll($photosTbl->select()->where('contest_id=?', $this->getIdentity()));

        foreach ($photos as $item) {
            $item->destroy();
        }

        $filesTbl = Engine_Api::_()->getItemTable('storage_file');

        $select = $filesTbl->select()
            ->where('parent_file_id = ?', $this->prize_photo);
        $mini = $filesTbl->fetchRow($select);
        $mini->delete();

        $select = $filesTbl->select()
            ->where('file_id = ?', $this->prize_photo);
        $file = $filesTbl->fetchRow($select);
        $file->delete();

        $this->delete();
    }

    public function setActive()
    {

        $contestsTbl = Engine_Api::_()->getItemTable('hecontest');
        //$contestsTbl->deactivateAll();


        $page = $this->getSponsorPage();
        if ($page) {
            $this->injectWidget($page->getIdentity());
        }
        $this->is_active = 1;
        $this->is_recent = 0;
        $this->save();

        $auth = Engine_Api::_()->authorization()->context;
        $auth->setAllowed($this, 'everyone', 'view', 3);

        $viewer = Engine_Api::_()->user()->getViewer();
        $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
        $action = $activityApi->addActivity($viewer, $this, 'hecontest_begins');
        if ($action) {
            $activityApi->attachActivity($action, $this);
        }
    }

    public function injectWidget($page_id = 0)
    {
        if (!$page_id) {
            return;
        }

        if ($this->hasWidget($page_id)) {
            return;
        }

        $contentTbl = Engine_Api::_()->getDbTable('content', 'page');
        $select = $contentTbl->select()
            ->where('page_id=?', $page_id)
            ->where('type=?', 'container');
        $containers = $contentTbl->fetchAll($select);
        $left = 0;
        $right = 0;
        foreach ($containers as $item) {
            if ($item->name == 'left') {
                $left = $item->content_id;
            }
            if ($item->name == 'right') {
                $right = $item->content_id;
            }
        }
        if ($right || $left) {
            $params = array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'hecontest.view-contest',
                'parent_content_id' => ($right) ? $right : $left,
                'order' => 999
            );

            $db = $contentTbl->getAdapter();
            $db->beginTransaction();
            try {
                $row = $contentTbl->createRow($params);
                $row->save();
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
            }
        }
    }

    public function hasWidget($page_id)
    {
        $contentTbl = Engine_Api::_()->getDbTable('content', 'page');
        $select = $contentTbl->select()
            ->where('page_id=?', $page_id)
            ->where('name LIKE ?', 'hecontest%');
        $containers = $contentTbl->fetchAll($select);
        return count($containers);
    }

    public function removeWidget($page_id)
    {
        $contentTbl = Engine_Api::_()->getDbTable('content', 'page');
        $select = $contentTbl->select()
            ->where('page_id=?', $page_id)
            ->where('name LIKE ?', 'hecontest%');
        $containers = $contentTbl->fetchAll($select);
        foreach ($containers as $item) {
            $item->delete();
        }
    }

    public function setRecent()
    {
        $page = $this->getSponsorPage();
        if ($page) {
            $this->removeWidget($page->getIdentity());
        }

        $this->is_recent = 1;
        $this->is_active = 0;
        $this->save();

        $winner = $this->getWinner();

        if (!$winner) {
            return;
        }
        $user = $winner->getUser();

        /*$auth = Engine_Api::_()->authorization()->context;
        $auth->setAllowed($user, 'everyone', 'view', 3);*/

        //
        $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
        $action = $activityApi->addActivity($user, $this, 'hecontest_win');
        if ($action) {
            $activityApi->attachActivity($action, $winner);
        }
    }

    public function timeToStart()
    {
        $activationDate = strtotime($this->date_begin);
        $activationDay = date('d', $activationDate);
        $today = date('d', time());

        if ($activationDay == $today) {
            return true;
        }

        return false;
    }

    public function timeToStop()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        date_default_timezone_set($viewer->getIdentity() ? $viewer->timezone : @$_COOKIE['timezone']);
        $expirationDate = strtotime($this->date_end);
        $now = time();
        if (($now - $expirationDate) >= 0) {
            print_log('it is time to stop');
            $this->setRecent();
            return true;
        }

        return false;
    }

    public function timeToFinish()
    {
        $expirationDate = new DateTime($this->date_end);
        $now = new DateTime(date('Y-n-d G:i:s', time()));

        $result = $now->diff($expirationDate);

        return $result;
    }

    public function allowView($viewer = null)
    {
        $user = Engine_Api::_()->user()->getViewer();
        $authTb = Engine_Api::_()->authorization()->getAdapter('levels');
        $view = $authTb->getAllowed('hecontest', $user, 'view');

        return $view;
    }

    public function allowJoin($viewer = null)
    {
        $user = Engine_Api::_()->user()->getViewer();
        $authTb = Engine_Api::_()->authorization()->getAdapter('levels');
        $view = $authTb->getAllowed('hecontest', $user, 'participate');

        return $view;
    }
}
