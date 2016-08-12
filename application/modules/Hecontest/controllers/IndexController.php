<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecontest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 19.10.13 08:20 jungar $
 * @author     Jungar
 */

/**
 * @category   Application_Extensions
 * @package    Hecontest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Hecontest_IndexController extends Core_Controller_Action_Standard
{
    public function indexAction()
    {
        $this->_helper->content->setEnabled();
        $contestsTbl = Engine_Api::_()->getDbTable('hecontests', 'hecontest');
        $contests = $contestsTbl->getActiveContests();

        $paginator = Zend_Paginator::factory($contests);
        $ipp = isset($params['ipp']) ? $params['ipp'] : $paginator->getTotalItemCount();
        $page = isset($params['page']) ? $params['page'] : 1;
        $paginator->setItemCountPerPage($ipp);
        $paginator->setCurrentPageNumber($page);

        $this->view->contests = $paginator;

        if (!$contests) {
            return;
        }


        $this->view->ajax = $ajax = $this->_getParam('ajax');
        if ($ajax) {
            $this->_helper->getHelper("layout")->disableLayout();
        }
        $page = $this->_getParam('page', 1);


    }
    public function contestviewAction()
    {
        $this->_helper->content->setEnabled();
        $contestsTbl = Engine_Api::_()->getDbTable('hecontests', 'hecontest');
        $cid = $this->_getParam('contest_id', 0);

        $this->view->contest = $contest = $contestsTbl->getActiveContest($cid);

        if (!$contest) {
            return;
        }

        if (!$contest->allowView()) {
            $this->_redirect('members/home');
        }

        Engine_Api::_()->core()->setSubject($contest);

        $this->view->ajax = $ajax = $this->_getParam('ajax');
        if ($ajax) {
            $this->_helper->getHelper("layout")->disableLayout();
        }
        $page = $this->_getParam('page', 1);

        $this->view->participants = $paginator = $contest->getParticipants(
          array('status' => 'approved', 'order' => 'votes DESC', 'ipp' => 10, 'page' => $page)
        );
        $obj = $paginator->getPages();
        $this->view->nextPage = ($obj->next > $obj->current) ? $obj->next : -1;
    }

    public function recentAction()
    {
        $this->_helper->content->setEnabled();
        $contestsTbl = Engine_Api::_()->getDbTable('hecontests', 'hecontest');

        $this->view->contest = $contest = $contestsTbl->getRecentContest();

        if (!$contest) {
            return;
        }

        if (!$contest->allowView()) {
            $this->_redirect('members/home');
        }

        Engine_Api::_()->core()->setSubject($contest);
        $this->view->participants = $contest->getParticipants(
            array(
                'status' => 'approved',
                'order' => 'votes DESC'
            )
        );
    }

    public function uploadAction()
    {
        if (!$this->getRequest()->isPost() || !$this->getRequest()->getParam('Filename')) {
            $this->view->status = false;
            return;
        }

        if (!isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
            $this->view->status = false;
            return;
        }

        $photosTbl = Engine_Api::_()->getDbTable('photos', 'hecontest');
        $photo_id = $photosTbl->addPhoto($_FILES['Filedata'], $this->_getParam('parent_id'));

        if ($photo_id)
            $this->view->status = 1;
        $this->view->file_id = $photo_id;
    }

    public function removePhotoAction()
    {
        if ($this->getRequest()->isPost()) {
            $file_id = $this->_getAllParams('file_id', 0);
            if ($file_id) {
                $storage = Engine_Api::_()->storage();

                $file = $storage->get($file_id);

                if ($file) {
                    $file->delete();
                }
            }
        }
    }

    public function voteAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error_code = 0;
            return;
        }
        $photo_id = $this->_getParam('photo_id');
        if (!$photo_id) {
            $this->view->status = false;
            $this->view->error_code = 1;
            return;
        }
        $viewer = Engine_Api::_()->user()->getViewer();

        $photosTbl = Engine_Api::_()->getDbTable('photos', 'hecontest');
        $photo = $photosTbl->getPhoto($photo_id);
        if (!$photo->allowLike($viewer->getIdentity())) {
            $this->view->status = false;
            $this->view->error_code = 3;
            return;
        }

        $photo->vote($viewer->getIdentity());

        if (!$photo->isVoter($viewer->getIdentity())) {
            $likeTitle = 'HECONTEST_Like';
        } else {
            $likeTitle = 'HECONTEST_Unlike';
        }


        $this->view->caption = $this->view->translate($likeTitle);
        $this->view->status = true;
    }

    public function joinAction()
    {
        $this->_helper->getHelper("layout")->disableLayout();
        $format = $this->_getParam('format');
        $contest_id = $this->_getParam('contest_id');

        $viewer = Engine_Api::_()->user()->getViewer();
        $contestsTbl = Engine_Api::_()->getDbTable('hecontests', 'hecontest');
        $active = $contestsTbl->getActiveContest($contest_id);

        if (!$active->allowJoin() && !$format) {
            $this->_redirect('members/home');
        }

        if ((!$format || $format != 'json') || !$this->getRequest()->isPost() || !$active->allowJoin()) {
            return false;
        }


        $this->view->form = $form = new Hecontest_Form_Join();
        $params = $this->getRequest()->getParams();

        if (!isset($params['file_id'])) {
            $this->view->status = 0;
            $this->view->message_code = 1;
            $this->view->message = 'no file id';
            return;
        }
        if (!isset($params['description'])) {
            $this->view->status = 0;
            $this->view->message_code = 2;
            $this->view->message = 'no descr';
            return;
        }
        /*
                    if (!isset($params['terms'])) {
                        $this->view->status = 0;
                        $this->view->message_code = 3;
                        $this->view->message = 'no terms';
                        return;
                    }*/

        $photosTbl = Engine_Api::_()->getDbTable('photos', 'hecontest');
        $db = $photosTbl->getAdapter();
        $db->beginTransaction();

        if (Engine_Api::_()->getDbtable('settings', 'core')->getSetting('hecontest.settings.autoapprove', 1)) {
            $status = 'approved';
        } else {
            $status = 'pending';
        }

        try {
            $row = $photosTbl->createRow();
            $row->setFromArray(array(
                'file_id' => $params['file_id'],
                'user_id' => $viewer->getIdentity(),
                'contest_id' => $active->getIdentity(),
                'votes' => 0,
                'date_posted' => new Zend_Db_Expr('NOW()'),
                'description' => strip_tags($params['description']),
                'status' => $status,
                'parent_id' => Engine_Api::_()->user()->getViewer()->getIdentity()
            ));
            $row->save();
            $db->commit();

            $auth = Engine_Api::_()->authorization()->context;
            $auth->setAllowed($row, 'everyone', 'view', 3);

            $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');

            $action = $activityApi->addActivity($viewer, $active, 'hecontest_participate');

            if ($action) {
                $activityApi->attachActivity($action, $row);
            }

            $this->view->status = 1;
            $this->view->redirect = $this->view->url(array('action' => 'contestview', 'contest_id' => $contest_id), 'hecontest_general_view');
            return;
        } catch (Exception $e) {
            $db->rollBack();
            $this->view->status = 0;
            $this->view->exception_message = $e->getMessage();
            return;
        }
    }

    public function viewAction()
    {
        $this->_helper->getHelper("layout")->disableLayout();

        $user = Engine_Api::_()->user()->getViewer();
        $authTb = Engine_Api::_()->authorization()->getAdapter('levels');
        $view = $authTb->getAllowed('hecontest', $user, 'view');
        if (!$view) {
            return;
        }

        if (!$this->getRequest()->isPost()) {
            return;
        }
        $photo_id = $this->_getParam('photo_id');
        $contest_id = $this->_getParam('contest_id');

        if (!$photo_id || !$contest_id) {
            return;
        }

        $contestsTbl = Engine_Api::_()->getDbTable('hecontests', 'hecontest');
        $this->view->contest = $contest = $contestsTbl->getContest($contest_id);
        $this->view->photo = $photo = $contest->getParticipant($photo_id);

        $commentSelect = $photo->comments()->getCommentSelect();
        $commentSelect->group('resource_type');
        $commentSelect->order('comment_id ASC');
        $comments = Zend_Paginator::factory($commentSelect);
        $this->view->comment_count = $comments->getTotalItemCount();

        if (!$photo) {
            return;
        }

        $this->view->voters = $photo->getVoters(
            array('limit' => '16', 'ipp' => 16)
        );
        $allowSuggest = false;
        $moduleTable = Engine_Api::_()->getDbTable('modules', 'hecore');
        if ($moduleTable->isModuleEnabled('suggest')) {
            $module = $moduleTable->findByName('suggest');
            $version = explode('.', $module->version);
            if (count($version) == 3) {
                $target = explode('p', $version[2]);
                if (count($target) == 2) {
                    $allowSuggest = ($target[1] > 9);
                } else {
                    if ($target > 9) {
                        $allowSuggest = true;
                    }
                }
            }
        }

        $this->view->allowSuggest = $allowSuggest;
        Engine_Api::_()->core()->setSubject($photo);
    }

    public function finishAction()
    {
        $this->_helper->getHelper("layout")->disableLayout();

        $this->view->status = false;

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$this->_getParam('finish')) {
            return;
        }

        $contestsTbl = Engine_Api::_()->getDbTable('hecontests', 'hecontest');
        $contest = $contestsTbl->getActiveContest();

        if ($contest->timeToStop()) {
            $this->view->status = true;
            Engine_Api::_()->getItemTable('hecontest')->autoStartContest();
            return;
        }
    }
    public function buycontestAction()
    {
        $this->_helper->getHelper("layout")->disableLayout();

        $this->view->status = false;

        if (!$this->getRequest()->isPost()) {
            return;
        }
        $contest_id = $this->_getParam('contest_id',0);
        if(!$contest_id){
            return;
        }
        $buyer_id = Engine_Api::_()->user()->getViewer();
        if(!$buyer_id){
            return;
        }
        $contestsTbl = Engine_Api::_()->getDbTable('hecontests', 'hecontest');
        $paided = false;
        $contest = $contestsTbl->getActiveContest($contest_id);
        Engine_Api::_()->core()->setSubject($contest);
        if($contest){
            Engine_Api::_()->hecontest()->buyContest($buyer_id, (-1) * $contest->getPrice());
            $paided = Engine_Api::_()->getDbTable('purchaseds', 'hecontest')->setPaidedContest($contest->getIdentity(), $buyer_id->getIdentity());
        }
        if($paided){
            $this->view->status = true;
        }
        $form = new Hecontest_Form_Join();
        echo json_encode(array(
            'status' => $this->view->status,
            'form' => $form->render($this->view)
        ));
        die;

    }
}
