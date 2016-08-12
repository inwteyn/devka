<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 2010-07-02 19:54 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Pageevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pageevent_IndexController extends Core_Controller_Action_Standard
{
  protected $subject;
  protected $viewer;

  public function init()
  {
    $page_enabled = Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('page');

    if (!$page_enabled){
      //if ($this->_getParam('format') != 'json')
      $this->_forward('notfound', 'error', 'core');
      return;
    }

    $page_id = $this->_getParam('page_id');
    $ipp = $this->_getParam('ipp', 10);
    $this->view->pageObject = $subject = ($page_id) ? Engine_Api::_()->getItem('page', $page_id) : null;

    if ($subject && !Engine_Api::_()->getApi('core', 'page')->isAllowedView($subject)){
      $subject = null;
    }
    $this->subject = $subject;

    $viewer = Engine_Api::_()->user()->getViewer();
    $this->viewer = ($viewer && $viewer->getIdentity()) ? $viewer : null;

  }

  public function listAction()
  {
    if (!$this->subject){ return ; }

    $this->view->isAllowedPost = Engine_Api::_()->getApi('core', 'pageevent')
        ->isAllowedPost($this->subject);

    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->isTeamMember = $this->subject->isTeamMember($viewer);
    $this->view->viewer = $viewer;

    $tbl = $this->getTable();

    $this->view->paginator = $tbl->getPaginator(
      $this->subject->getIdentity(),
      $this->_getParam('show'),
      $this->_getParam('page', 1),
      Engine_Api::_()->user()->getViewer()->getIdentity(),
      $this->_getParam('ipp')
    );

    $this->view->html = $this->view->render('list.tpl');
    $this->view->count = $tbl->getCount($this->subject->getIdentity());


  }

  public function viewAction()
  {
    $this->view->result = false;
    $this->view->message = $this->view->translate('PAGEEVENT_NOTFOUND');
    $this->view->html = $this->view->render('message.tpl');

    $event_id = $this->_getParam('id');
    if (!$event_id){ return ; }

    $event = $this->getTable()->findRow($event_id);
    if (!$event){ return ; }
    $viewer = Engine_Api::_()->user()->getViewer();

    $path = Zend_Controller_Front::getInstance()->getControllerDirectory('like');
    $path = dirname($path) . '/views/scripts';

  	$this->view->addScriptPath($path);

      if(!Engine_Api::_()->core()->hasSubject())
    Engine_Api::_()->core()->setSubject($event);

    // General
    $this->view->subject = $event;
    $this->view->viewer = $viewer;
    $this->view->event_id = $event->getIdentity();
    $this->view->isTeamMember = $event->getPage()->isAdmin($viewer);
    $this->view->isOwner = $isOwner = $viewer->isSelf($event->getOwner());
    $this->view->isLogin = (bool)$this->viewer;

    if (!$isOwner){ $event->view(); }

    // Membership
    $membership = $event->membership();
    $this->view->attending = $membership->getMemberPaginator(2);
    $this->view->maybe_attending = $membership->getMemberPaginator(1);
    $this->view->not_attending = $membership->getMemberPaginator(0);
    $this->view->count_waiting = $membership->getWaitingCount();
    $this->view->member = $membership->getRow($viewer);
    $this->view->isFriends = $membership->isFriends($viewer);

    $this->view->comment_form_id = "event-comment-form";


    // Convert Dates
    $startDateObject = new Zend_Date(strtotime($event->starttime));
    $endDateObject = new Zend_Date(strtotime($event->endtime));
    if ($this->viewer){
      $tz = $this->viewer->timezone;
      $startDateObject->setTimezone($tz);
      $endDateObject->setTimezone($tz);
    }
    $this->view->startDateObject = $startDateObject;
    $this->view->endDateObject = $endDateObject;

    // Comments
    $this->view->viewAllLikes = $this->_getParam('viewAllLikes', false);
    $this->view->likes = $event->likes()->getLikePaginator();
    $this->view->page = $page = $this->_getParam('page');
    $this->view->comments = Engine_Api::_()->getApi('core', 'pageevent')->getComments($page);
    $this->view->isAllowedComment = $event->getPage()->authorization()->isAllowed($viewer, 'comment');

    if ($this->view->isAllowedComment) {
      $this->view->form = $form = new Core_Form_Comment_Create();
      $form->addElement('Hidden', 'form_id', array('value' => 'pagereview-comment-form'));
      $form->populate(array(
        'identity' => $event->getIdentity(),
        'type' => $event->getType(),
      ));
      $this->view->likeHtml = $this->view->render('comment/list.tpl');
      $this->view->likeUrl = $this->view->url(array('action' => 'like'), 'like_comment');
      $this->view->unlikeUrl = $this->view->url(array('action' => 'unlike'), 'like_comment');
      $this->view->hintUrl = $this->view->url(array('action' => 'hint'), 'like_comment');
      $this->view->showLikesUrl = $this->view->url(array('action' => 'list'), 'like_comment');
      $this->view->postCommentUrl = $this->view->url(array('action' => 'create'), 'like_comment');
    }
    $this->view->result = true;

    if($this->_getParam('format') == 'json')
        $this->view->html = $this->view->render('index/view.tpl');
  }

  public function formAction()
  {
    $event_id = (int) $this->_getParam('id');
    $photo_id = $this->_getParam('event_photo_fileid');

    $this->view->result = false;
    $this->view->message = $this->view->translate(($event_id) ? 'PAGEEVENT_EDIT_ERROR' : 'PAGEEVENT_CREATE_ERROR');
    $this->view->html = $this->view->render('message.tpl');

    if (!$this->viewer){
      return ;
    }

    $form = new Pageevent_Form_Form($this->subject);

    if (!$this->getRequest()->isPost() || !$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    $tbl = $this->getTable();

    $event = null;

    if ($event_id)
    {
      $event = $tbl->findRow($event_id);
      if (!$event){
        return ;
      }
      $viewer = Engine_Api::_()->user()->getViewer();
      if (!$this->viewer->isSelf($event->getOwner()) && !$event->getPage()->isTeamMember($viewer)){
        return ;
      }
    } else {

      if (!$this->subject){
        return ;
      }
      if (!Engine_Api::_()->getApi('core', 'pageevent')->isAllowedPost($this->subject)){
        return ;
      }
    }

    $db = $tbl->getAdapter();
    $db->beginTransaction();
    $values = $form->getValues();

    $oldTz = date_default_timezone_get();
    date_default_timezone_set($this->viewer->timezone);
    $start = strtotime($values['starttime']);
    $end = strtotime($values['endtime']);
    date_default_timezone_set($oldTz);

    if ($start > $end){
      $this->view->message = $this->view->translate('PAGEEVENT_DATEERROR');
      $this->view->html = $this->view->render('message.tpl');
      return ;
    }

    $values['starttime'] = date('Y-m-d H:i:s', $start);
    $values['endtime'] = date('Y-m-d H:i:s', $end);

    try
    {
      if (!$event){
        $event = $tbl->createRow($values);
        $event->user_id = $this->viewer->getIdentity();
        $event->page_id = $this->subject->getIdentity();
      } else {
        $event->setFromArray($values);
      }
      $event->save();
      $page = $event->getPage();
      $auth = Engine_Api::_()->authorization()->context;

      $user = Engine_Api::_()->user()->getViewer();

      $availableLabels = array(
        'everyone' => 'Everyone',
        'registered' => 'Registered Members',
        'likes' => 'Likes, Admins and Owner',
        'team' => 'Admins and Owner Only'
      );

      if (Engine_Api::_()->getApi('settings', 'core')->__get('page.package.enabled') && $this->view->pageObject instanceof Page_Model_Page) {
        /**
         * @var $page Page_Model_Package
         */
        $package = $this->view->pageObject->getPackage();

        $view_options = $package->auth_view;
        $comment_options = $package->auth_comment;
        $posting_options = $package->auth_posting;
      }

      else {
        $comment_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('page', $user, 'auth_comment');
        $posting_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('page', $user, 'auth_posting');
      }

      $comment_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('page', $user, 'auth_comment');
      $comment_options = array_intersect_key($availableLabels, array_flip($comment_options));

      $posting_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('page', $user, 'auth_posting');
      $posting_options = array_intersect_key($availableLabels, array_flip($posting_options));

      $event->setPrivacy(array(
        'auth_view' => $values['privacy'],
        'auth_comment' => key($comment_options),
        'auth_posting' => key($posting_options)));

      if (!$event_id)
      {
        // Add Member
        $event->membership()->addMember($this->viewer)
          ->setUserApproved($this->viewer)
          ->setResourceApproved($this->viewer);

        $event->membership()
          ->getMemberInfo($this->viewer)
          ->setFromArray(array('rsvp' => 2))
          ->save();

        // Add Activity
        $api = Engine_Api::_()->getDbtable('actions', 'activity');
        $action = $api->addActivity($this->viewer, $event->getPage(), 'pagevent_create', null,array('tag' => $values['title'].' '. $values['description']));
        if ($action){
          $api->attachActivity($action, $event);
        }
      }

      if ($photo_id){
        if ($event->photo_id){
          Engine_Api::_()->getApi('core', 'pageevent')->deletePhoto($event->photo_id);
        }
        if ($photo = Engine_Api::_()->storage()->get($photo_id)){
          $event->photo_id = $photo->getIdentity();
          $event->save();
        }
      }

      $db->commit();

      $this->view->id = $event->getIdentity();
      $this->view->count = $tbl->getCount($this->subject->getIdentity());
      $this->view->result = true;
      $this->view->message = $this->view->translate(($event_id) ? 'PAGEEVENT_EDIT_SUCCESS' : 'PAGEEVENT_CREATE_SUCCESS');
      $this->view->html = $this->view->render('message.tpl');

    }
    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

  }

  public function editAction()
  {
    $this->view->result = false;
    $user = Engine_Api::_()->user()->getViewer();

    $event_id = $this->_getParam('id');
    $pageevent = Engine_Api::_()->getDbTable('pageevents', 'pageevent')->findRow($event_id);
    $page = $pageevent->getPage();

    $privacyForm = new Pageevent_Form_Form($page);

    $auth = Engine_Api::_()->authorization();

    $roles = array('team', 'likes', 'registered', 'everyone');
    $view_auth = 'team';

    foreach ($roles as $roleString) {
      $role = $roleString;

      if ($role === 'team') {
        $role = $page->getTeamList();
      }
      elseif ($role === 'likes') {
        $role = $page->getLikesList();
      }

      if ( 1 === $auth->isAllowed($pageevent, $role, 'view') ) {
          $view_auth = $roleString;
          $privacyForm->privacy->setValue($roleString);
        }

    }
    if (!$event_id){ return ; }

    $event = $this->getTable()->findRow($event_id);
    if (!$event){ return ; }

    if (!$this->viewer){ return ; }

    // Convert and re-populate times
    $start = strtotime($event->starttime);
    $end = strtotime($event->endtime);
    $oldTz = date_default_timezone_get();
    date_default_timezone_set($this->viewer->timezone);
    $start = date('Y-m-d H:i', $start);
    $end = date('Y-m-d H:i', $end);
    date_default_timezone_set($oldTz);

    $this->view->result = true;
    $event->starttime = $start;
    $event->endtime = $end;
    $this->view->event_info = $event->toArray();



    $this->view->photo = Engine_Api::_()->storage()->get($event->photo_id);
    $this->view->photo_html = $this->view->render('edit_photo.tpl');
    $this->view->view_auth = $view_auth;
  }

  public function removeAction()
  {
    $this->view->result = false;
    $this->view->message = $this->view->translate('PAGEEVENT_REMOVE_ERROR');
    $this->view->html = $this->view->render('message.tpl');

    if (!$this->viewer){ return ; }

    $event_id = $this->_getParam('id');
    if (!$event_id){ return ; }

    $tbl = $this->getTable();
    $event = $tbl->findRow($event_id);

    if (!$event){
      return ;
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$this->viewer->isSelf($event->getOwner()) && !$event->getPage()->isTeamMember($viewer)){
      return ;
    }

    $db = $tbl->getAdapter();
    $db->beginTransaction();

    try
    {
      $event->delete();
      $db->commit();

      $this->view->count = $tbl->getCount($this->subject->getIdentity());
      $this->view->result = true;
      $this->view->message = $this->view->translate('PAGEEVENT_REMOVE_SUCCESS');
      $this->view->html = $this->view->render('message.tpl');
    }
    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

  }

  public function uploadPhotoAction()
  {
    $error_msg = $this->view->translate('PAGEEVENT_UNKNOWN_ERROR');

    if (!$this->viewer){
      $this->view->status = false;
      $this->view->error = $error_msg;
      return ;
    }
    if (!$this->getRequest()->isPost() || !$this->getRequest()->getParam('Filename')){
      $this->view->status = false;
      $this->view->error = $error_msg;
      return ;
    }
    if (!isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name'])){
      $this->view->status = false;
      $this->view->error = $error_msg;
    }
/*    if (!preg_match('/\.(jpg|jpeg|gif|png|tmp)$/', $_FILES['Filedata']['tmp_name']))
    {
      $this->view->status = false;
      $this->view->error = $error_msg;
      return ;
    }*/

    $db = $this->getTable()->getAdapter();
    $db->beginTransaction();

    try
    {
      $photo = Engine_Api::_()->getApi('core', 'pageevent')->uploadPhoto($_FILES['Filedata']);
      $this->view->status = true;
      $this->view->photo_id = $photo->getIdentity();
      $this->view->photo = $photo->toArray();

      $db->commit();
    }
    catch (Exception $e)
    {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = $error_msg;
      throw $e;
    }

  }

  public function removePhotoAction()
  {
    $error_msg = $this->view->translate('PAGEEVENT_UNKNOWN_ERROR');

    if (!$this->viewer || !$this->getRequest()->isPost()){
      $this->view->status = false;
      $this->view->error = $error_msg;
    }

    $photo_id = $this->_getParam('photo_id');
    if (!$photo_id){ return ; }

    $db = $this->getTable()->getAdapter();
    $db->beginTransaction();

    try
    {
      Engine_Api::_()->getApi('core', 'pageevent')->deletePhoto($photo_id);
      $this->view->status = true;
      $db->commit();
    }
    catch (Exception $e)
    {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = $error_msg;
      throw $e;
    }

  }

  public function waitingAction()
  {
    $this->view->result = false;

    $event_id = $this->_getParam('id');

    if (!$event_id || !$this->viewer){ return ; }

    $tbl = $this->getTable();

    $event = $tbl->findRow($event_id);
    if (!$event){ return ; }

    $this->view->result = true;
    $this->view->event_id = $event_id;


    $tbl = Engine_Api::_()->getDbTable('users', 'user');
    $eventmember_tbl = Engine_Api::_()->getDbTable('pageeventmembership', 'pageevent');
    $select = $tbl->select()
        ->setIntegrityCheck(false)
        ->from(array('u' => $tbl->info('name')), array('u.*'))
        ->join(array('em' => $eventmember_tbl->info('name')), 'em.user_id = u.user_id', array('em.user_approved'))
        ->where('em.resource_id = ?', $event->getIdentity())
        ->where('em.active = 0');

    $this->view->members = $tbl->fetchAll($select);
    $this->view->html = $this->view->render('waiting.tpl');

  }

  public function resourceApproveAction()
  {
    $event_id = $this->_getParam('id');
    $user_id = $this->_getParam('user_id');
    $approve = (bool)$this->_getParam('approve');

    $this->view->result = false;

    if (!$event_id){ return ; }

    $tbl = $this->getTable();
    $event = $tbl->findRow($event_id);
    if (!$event){ return ; }

    if (!$this->viewer || (!$event->getPage()->isTeamMember($this->viewer) && !$this->viewer->isSelf($event->getOwner()))){
      return ;
    }
    $db = $tbl->getAdapter();
    $db->beginTransaction();

    try
    {
      $user = Engine_Api::_()->user()->getUser($user_id);
      if (!$user){ return ; }

      $member = $event->membership()->getRow($user);
      if (!$member){ return;  }

      if ($approve){

        $event->membership()->setResourceApproved($user);

        if ($member->active)
        {
          // Add Activity
          $api = Engine_Api::_()->getDbtable('actions', 'activity');
          $action = $api->addActivity($this->viewer, $event->getPage(), 'pagevent_join', null, array('link' => $event->__toString()));
          if ($action){
            $api->attachActivity($action, $event, Activity_Model_Action::ATTACH_DESCRIPTION);
          }
          Engine_Api::_()->getDbtable('notifications', 'activity')
            ->addNotification($user, $this->viewer, $event, 'pageevent_accepted', array('link' => $event->getHref()));
        }

      } else {
        $event->membership()->removeMember($user);
      }
      $this->view->result = true;
      $this->view->count = $event->membership()->getWaitingCount();
      $db->commit();
    }
    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

  }

  public function memberApproveAction()
  {
    $event_id = $this->_getParam('id');
    $approve = $this->_getParam('approve');

    $this->view->result = false;
    $this->view->message = $this->view->translate('PAGEEVENT_REQUEST_ERROR');

    if (!$event_id){ return ; }

    $tbl = $this->getTable();
    $event = $tbl->findRow($event_id);
    if (!$event){ return ; }

    if (!$this->viewer){
      return ;
    }
    $db = $tbl->getAdapter();
    $db->beginTransaction();

    try
    {
      $member = $event->membership()->getRow($this->viewer);
      if (!$member){ return;  }

      if ($approve){

        $event->membership()->setUserApproved($this->viewer);

        if ($member->active)
        {
          // Add Activity
          $api = Engine_Api::_()->getDbtable('actions', 'activity');
          $action = $api->addActivity($this->viewer, $event->getPage(), 'pagevent_join', null, array('link' => $event->__toString()));
          if ($action){
            $api->attachActivity($action, $event, Activity_Model_Action::ATTACH_DESCRIPTION);
          }
          Engine_Api::_()->getDbtable('notifications', 'activity')
            ->addNotification($event->getOwner(), $this->viewer, $event, 'pageevent_approved', array('link' => $event->getHref()));
        }

        $rsvp = $this->_getParam('rsvp');

        if ($rsvp !== null && in_array($rsvp, array(0,1,2))){
          $member->rsvp = $rsvp;
          $member->save();
        }

      } else {
        $event->membership()->removeMember($this->viewer);
      }

      // Set the request as handled
      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
        $this->viewer, $event, 'pageevent_invite');

      if( $notification )
      {
        $notification->mitigated = true;
        $notification->save();
      }

      $this->view->result = true;
      $this->view->message = $this->view->translate('PAGEEVENT_REQUEST_SUCCESS');
      $db->commit();
    }
    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

  }

  public function rsvpAction()
  {
    $event_id = $this->_getParam('id');
    $rsvp = (int)$this->_getParam('rsvp');

    if ($rsvp < 0 || $rsvp > 2){ $rsvp = 2; }

    $this->view->result = false;

    if (!$event_id){ return ; }

    $tbl = $this->getTable();
    $event = $tbl->findRow($event_id);
    if (!$event){ return ; }

    if (!$this->viewer){
      return ;
    }
    $db = $tbl->getAdapter();
    $db->beginTransaction();

    try
    {
      $member = $event->membership()->getRow($this->viewer);

      if (!$member)
      {
        $member = $event->membership()
            ->addMember($this->viewer)
            ->getRow($this->viewer);

        $event->membership()
          ->setUserApproved($this->viewer);

        if ($member->active)
        {
          // Add Activity
          $api = Engine_Api::_()->getDbtable('actions', 'activity');
          $action = $api->addActivity($this->viewer, $event->getPage(), 'pagevent_join', null, array('link' => $event->__toString()));
          if ($action){
            $api->attachActivity($action, $event, Activity_Model_Action::ATTACH_DESCRIPTION);
          }
          Engine_Api::_()->getDbtable('notifications', 'activity')
            ->addNotification($event->getOwner(), $this->viewer, $event, 'pageevent_approved', array('link' => $event->getHref()));
        }
      }

      $event->membership()
          ->setUserApproved($this->viewer);

      $member->rsvp = ($event->approval && !$member->active) ? 3 : $rsvp;
      $member->save();

      $db->commit();

      $this->view->result = true;
    }
    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

  }

  public function inviteAction()
  {
    $event_id = $this->_getParam('id');
    $user_ids = $this->_getParam('user_ids');

    $this->view->result = false;
    $this->view->message = $this->view->translate('PAGEEVENT_INVITE_ERROR');
    $this->view->html = $this->view->render('message.tpl');

    if (!$event_id){ return ; }

    $tbl = $this->getTable();
    $event = $tbl->findRow($event_id);
    if (!$event){ return ; }

    if (!$this->viewer){
      return ;
    }

    $is_owner = ($event->getPage()->isTeamMember($this->viewer) || !$this->viewer->isSelf($event->getOwner()));
    $is_guest = ($event->invite && $event->membership()->isMember($this->viewer, true));

    if (!$is_owner && !$is_guest){
      return ;
    }

    $db = $tbl->getAdapter();
    $db->beginTransaction();

    try
    {
      $select = $event->membership()->getInviteMembersSelect($this->viewer)
          ->where('u.user_id IN (?)', $user_ids);

      $friends = Engine_Api::_()->getDbTable('users', 'user')->fetchAll($select);

      foreach ($friends as $friend)
      {
        if ($event->membership()->getRow($friend)){
          continue;
        }
        $event->membership()
            ->addMember($friend)
            ->setResourceApproved($friend);

        $event->membership()
            ->getRow($friend)
            ->setFromArray(array('rsvp', 3))
            ->save();;

        Engine_Api::_()->getDbtable('notifications', 'activity')
            ->addNotification($friend, $this->viewer, $event, 'pageevent_invite', array('link' => $event->getHref()));
      }

      $this->view->result = true;
      $this->view->message = $this->view->translate('PAGEEVENT_INVITE_SUCCESS');
      $this->view->html = $this->view->render('message.tpl');
      $db->commit();
    }
    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

  }

  protected function getTable()
  {
    return Engine_Api::_()->getDbTable('pageevents', 'pageevent');
  }

}