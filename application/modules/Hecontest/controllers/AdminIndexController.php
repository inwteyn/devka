<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecontest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminIndexController.php 19.10.13 08:20 jungar $
 * @author     Jungar
 */

/**
 * @category   Application_Extensions
 * @package    Hecontest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Hecontest_AdminIndexController extends Core_Controller_Action_Admin
{
    public function init()
    {

    }

    public function indexAction()
    {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('hecontest_admin_main', array(), 'hecontest_admin_main_contests');

        $page = $this->_getParam("page", 1);

        $contestTbl = Engine_Api::_()->getDbTable('hecontests', 'hecontest');
        $this->view->paginator = $paginator = $contestTbl->getContests(
            array(
                'page' => $page,
                'ipp' => 0
            )
        );
    }

    public function actionAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->view->status = 0;
            $this->view->error_code = 0;
            return;
        }
        $act = $this->_getParam('status');
        $id = $this->_getParam('id');

        if (!$act || !$id) {
            $this->view->status = 0;
            $this->view->error_code = 1;
            return;
        }

        $photosTbl = Engine_Api::_()->getDbTable('photos', 'hecontest');
        $photo = $photosTbl->getPhoto($id);
        if (!$photo) {
            $this->view->status = 0;
            $this->view->error_code = 2;
            return;
        }

        $db = $photosTbl->getAdapter();
        $db->beginTransaction();
        try {
            if ($act == 'remove') {
                $this->view->status = 1;
                $photo->destroy();
            } else {
                $recipient = $photo->getUser();
                $mailSettings = array(
                    'contest_name' => $photo->getContest()->getTitle(),
                    'contest_link' => $photo->getContest()->getHref(),
                    'user' => $recipient->getTitle()
                );
                if ($act == 'approved') {
                    $status = 'hecontest_approve';
                } else { // pending
                    $status = 'hecontest_reject';
                }
                Engine_Api::_()->getApi('mail', 'core')->sendSystem(
                    $recipient->email,
                    $status,
                    $mailSettings
                );
                // @TODO send email here
                $photo->status = $act;
                $photo->save();
                $this->view->status = 1;
                $this->view->message = $photo->status;
            }
            $db->commit();

        } catch (Exception $e) {
            $db->rollback();
            $this->view->status = 0;
            $this->view->error_code = 3;
        }
    }

    public function detailsAction()
    {
        $id = $this->_getParam('id', 0);

        $photosTbl = Engine_Api::_()->getDbTable('photos', 'hecontest');
        $photo = $photosTbl->getPhoto($id);

        if (!$photo) {
            $this->view->status = false;
            return;
        }

        $this->view->status = true;
        $this->view->img = $photo->getPhotoUrl();
        $this->view->descr = $photo->description;
    }

    public function settingsAction()
    {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('hecontest_admin_main', array(), 'hecontest_admin_main_settings');

        $this->view->form = $form = new Hecontest_Form_Admin_Settings();

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }
        $settingsTbl = Engine_Api::_()->getDbTable('settings', 'core');
        $values = $form->getValues();
        foreach ($values as $key => $value) {
            $settingsTbl->setSetting($key, $value);
        }

        $this->view->form = $form = new Hecontest_Form_Admin_Settings();
        $form->addNotice('Your changes have been saved.');
    }

    public function levelSettingsAction()
    {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('hecontest_admin_main', array(), 'hecontest_admin_main_level_settings');

        if (null !== ($id = $this->_getParam('level_id'))) {
            $level = Engine_Api::_()->getItem('authorization_level', $id);
        } else {
            $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
        }

        if (!$level instanceof Authorization_Model_Level) {
            throw new Engine_Exception('missing level');
        }

        $id = $level->level_id;

        $this->view->form = $form = new Hecontest_Form_Admin_Level();
        $form->setLevelId($id);

        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        if (!$this->getRequest()->isPost()) {
            $form->populate($permissionsTable->getAllowed('hecontest', $id, array_keys($form->getValues())));
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $params = $form->getValues();

        $db = $permissionsTable->getAdapter();
        $db->beginTransaction();
        $values['view'] = $params['view'];
        $values['vote'] = $params['vote'];
        $values['participate'] = $params['participate'];

        try {
            $permissionsTable->setAllowed('hecontest', $id, $values);
            $db->commit();
        } catch(Exception $e) {
            $db->rollBack();
        }
    }

    public function createAction()
    {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('hecontest_admin_main', array(), 'hecontest_admin_main_create');

        $this->view->form = $form = new Hecontest_Form_Admin_Create();
        $this->view->pageEnabled = Engine_Api::_()->hecontest()->isPageEnabled();

        if (!$this->getRequest()->isPost()) {
            return;
        }
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        $values = $form->getValues();
        $values['user_id'] = $viewer->getIdentity();

        /*print_arr($values);
        $oldTz = date_default_timezone_get();
        date_default_timezone_set($viewer->timezone);
        $start = strtotime($values['date_begin']);
        $end = strtotime($values['date_end']);
        date_default_timezone_set($oldTz);
        $values['date_begin'] = date('Y-m-d H:i:s', $start);
        $values['date_end'] = date('Y-m-d H:i:s', $end);*/

        $contestsTbl = Engine_Api::_()->getDbTable('hecontests', 'hecontest');
        $db = $contestsTbl->getAdapter();
        $db->beginTransaction();
        try {
            $contest = $contestsTbl->createRow();
            $contest->setFromArray($values);
            $contest->save();

            $contest->setPhoto($form->photo);
            $contest->setPhotoMain($form->photo_main);

            if ($contest->timeToStart()) {
                $contest->setActive();
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }

    public function viewAction()
    {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('hecontest_admin_main', array(), '');

        $contest_id = $this->_getParam('hecontest_id', null);

        if (!$contest_id) {
            $this->view->error = "HECONTEST_There is no such contest.";
            return;
        }

        $this->view->contest = $contest = Engine_Api::_()->getItem('hecontest', $contest_id);

        if (!$contest) {
            $this->view->error = "HECONTEST_There is no such contest.";
            return;
        }
        $page = $this->_getParam('page', 1);
        $this->view->paginator = $participants = $contest->getParticipants(array(
            'page' => $page,
            'ipp' => 10
        ));
    }

    public function editAction()
    {
        $this->view->pageEnabled = Engine_Api::_()->hecontest()->isPageEnabled();

        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('hecontest_admin_main', array(), '');

        $contest_id = $this->_getParam('hecontest_id', null);
        if (!$contest_id) {
            $this->view->error = "HECONTEST_There is no such contest.";
            return;
        }
        $this->view->contest = $contest = Engine_Api::_()->getItem('hecontest', $contest_id);

        if (!$contest) {
            $this->view->error = "HECONTEST_There is no such contest.";
            return;
        }
        $form = new Hecontest_Form_Admin_Create(true);
        $form->setTitle("Edit Contest");

        $form->populate($contest->toArray());

        $this->view->form = $form;

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $values = $form->getValues();
        $contest->setFromArray($values);
        $contestsTbl = Engine_Api::_()->getDbTable('hecontests', 'hecontest');
        $db = $contestsTbl->getAdapter();
        $db->beginTransaction();

        try {
            $contest->save();
            $contest->setPhoto($form->photo);
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function activateAction()
    {
        $format = $this->_getParam('format', null);
        if (!$format || $format != 'smoothbox') {
            $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('hecontest_admin_main', array(), '');
        }

        $contest_id = $this->_getParam('hecontest_id', null);
        $contest = Engine_Api::_()->getItem('hecontest', $contest_id);
        $activate = $this->_getParam('activate', 1);

        $this->view->form = $form = new Hecontest_Form_Admin_Action($activate);

        if ($this->getRequest()->isPost()) {
            $msg = 'activated';
            if ($activate == 1) {
                $contest->setActive();
            } else {
                $contest->setRecent();
                $msg = 'deactivated';
            }

            return $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh' => true,
                'format' => 'smoothbox',
                'messages' => array("Contest has been successfully {$msg}.")
            ));
        }
    }

    public function deleteAction()
    {
        $format = $this->_getParam('format', null);
        if (!$format || $format != 'smoothbox') {
            $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('hecontest_admin_main', array(), '');
        }

        $contest_id = $this->_getParam('hecontest_id', null);
        $contest = Engine_Api::_()->getItem('hecontest', $contest_id);

        $this->view->form = $form = new Hecontest_Form_Admin_Delete();

        if ($this->getRequest()->isPost()) {
            $contestsTbl = Engine_Api::_()->getDbTable('hecontests', 'hecontest');
            $db = $contestsTbl->getAdapter();
            $db->beginTransaction();

            try {
                $contest->delete();
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            return $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh' => true,
                'format' => 'smoothbox',
                'messages' => array('Contest has been successfully deleted.')
            ));
        }
    }

    public function deleteSelectedAction()
    {
        if ($this->getRequest()->isPost()) {
            $params = $this->_getAllParams();

            $contestsTbl = Engine_Api::_()->getDbTable('hecontests', 'hecontest');
            $db = $contestsTbl->getAdapter();

            foreach ($params as $key => $value) {
                if (strstr($key, "contest")) {
                    $contest = Engine_Api::_()->getItem('hecontest', $value);
                    $db->beginTransaction();
                    try {
                        $contest->delete();
                        $db->commit();
                    } catch (Exception $e) {
                        $db->rollBack();
                        throw $e;
                    }
                }
            }
        }
        return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }

    public function pageAutocompleterAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->view->status = 0;
            $this->view->error_code = 0;
            return;
        }

        $isPageEnabled = Engine_Api::_()->hecontest()->isPageEnabled();
        if (!$isPageEnabled) {
            $this->view->status = 0;
            $this->view->error_code = 1;
            $this->view->message = 'Pages module is disabled';
            return;
        }
        $pagesTbl = Engine_Api::_()->getDbTable('pages', 'page');
        $target = $this->_getParam('target');

        $select = $pagesTbl->select()->where('title like ?', $target . '%');
        $pages = $pagesTbl->fetchAll($select);
        if (!count($pages)) {
            $this->view->status = 0;
            $this->view->error_code = 2;
            $this->view->message = 'No pages';
            return;
        }

        $result = array();
        foreach ($pages as $page) {
            $result[] = array(
                'name' => $page->getTitle(),
                'url' => $page->url
            );
        }
        $this->view->pages = $result;
        $this->view->status = 1;
        return;
    }
}
