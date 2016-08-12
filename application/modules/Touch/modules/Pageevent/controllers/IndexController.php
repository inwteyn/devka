<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Pageevent_IndexController extends Touch_Controller_Action_Standard
{
  protected $_subject;
  protected $viewer;

  public function init()
  {
    $page_id = (int)$this->_getParam('page_id');
    $subject = null;
    $navigation = new Zend_Navigation();

    if ($page_id){
      $subject = Engine_Api::_()->getDbTable('pages', 'page')->findRow($page_id);
    }

    if ($subject && !Engine_Api::_()->getApi('core', 'page')->isAllowedView($subject)){
      $subject = null;
    }

    $viewer = Engine_Api::_()->user()->getViewer();

    if ($subject){

      Engine_Api::_()->core()->setSubject($subject);

      $navigation->addPage(array(
        'label' => 'PAGEEVENT_UPCOMING',
        'route' => 'page_event',
        'action' => 'index',
        'params' => array(
          'page_id' => $subject->getIdentity()
        )
      ));

      $navigation->addPage(array(
        'label' => 'PAGEEVENT_PAST',
        'route' => 'page_event',
        'action' => 'past',
        'params' => array(
          'page_id' => $subject->getIdentity()
        )
      ));

      if ($subject->authorization()->isAllowed($viewer, 'posting')){

        $navigation->addPage(array(
          'label' => 'PAGEEVENT_USER',
          'route' => 'page_event',
          'action' => 'manage',
          'params' => array(
            'page_id' => $subject->getIdentity()
          )
        ));

      }

    }

    $this->_subject = $this->view->subject = $subject;
    $this->view->navigation = $navigation;

  }

  public function indexAction()
  {
    if (!$this->_subject){
      return $this->_forward('index', 'index', 'page');

    }
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->isTeamMember = $this->_subject->isTeamMember($viewer);
    $this->view->viewer = $viewer;
    $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('pageevent', null, 'create');
    // Prepare data{ -=: By Ulan :=-
    $this->view->paginator = $this->getPaginator('up');
    // }Prepare data -=: By Ulan :=-
  }

  public function pastAction()
  {
    if (!$this->_subject){
      return $this->_forward('index', 'index', 'page');

    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->isTeamMember = $this->_subject->isTeamMember($viewer);
    $this->view->viewer = $viewer;
    // Prepare data{ -=: By Ulan :=-
    $this->view->paginator = $this->getPaginator('past');
    // }Prepare data -=: By Ulan :=-

  }

  public function manageAction()
  {
    if (!$this->_subject){
      return $this->_forward('index', 'index', 'page');

    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->isTeamMember = $this->_subject->isTeamMember($viewer);
    $this->view->viewer = $viewer;

    // Prepare data{ -=: By Ulan :=-
    $this->view->paginator = $this->getPaginator('user', Engine_Api::_()->user()->getViewer()->getIdentity());
    // }Prepare data -=: By Ulan :=-

  }

  public function deleteAction()
  {
    $event_id = (int)$this->_getParam('event_id');

    $this->view->form = $form = new Touch_Form_Standard;

    $form->setTitle('PAGEEVENT_DELETE_TITLE')
      ->setDescription('PAGEEVENT_DELETE_DESCRIPTION')
      ->setAttrib('class', 'global_form_popup')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ->setMethod('POST');

    $form->addElement('Button', 'submit', array(
      'label' => 'Delete',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $form->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => urldecode($this->_getParam('return_url')),
      'decorators' => array(
        'ViewHelper'
      )
    ));

    $form->addDisplayGroup(array('submit', 'cancel'), 'buttons');

    $form->setAction($this->view->url(array(
      'action' => 'delete',
      'event_id' => $event_id,
      'return_url' => $this->_getParam('return_url')
    ), 'page_event'));

    if (!$this->getRequest()->isPost() || !$form->isValid($this->getRequest()->getPost())){
      return ;
    }

    $table = $this->getTable();
    $db = $table->getAdapter();
    $db->beginTransaction();

    $event = $table->findRow($event_id);
    $subject = $event->getParent();
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$viewer->isSelf($event->getOwner()) && !$subject->isTeamMember($viewer)){
      return ;
    }

    try
    {
      $search_api = Engine_Api::_()->getDbTable('search', 'page');
      $search_api->deleteData($event);
      $event->delete();
      $db->commit();
    }
    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

//    $return_url = $this->view->url(array(
//      'action' => 'manage',
//      'page_id' => $subject->getIdentity()
//    ), 'page_event', true);
//
//    return $this->_forward('success', 'utility', 'touch', array(
//      'messages' => $this->view->translate('PAGEEVENT_REMOVE_SUCCESS'),
//      'return_url'=>$return_url,
//    ));

    $this->_redirectCustom(array('route' => 'page_event', 'action' => 'manage', 'page_id' => $subject->getIdentity()));


  }

  public function createAction()
  {
      if ($this->is_iPhoneUploading()){
        if (!isset($_FILES['picup-image-upload'])){
          return ;
        }
        $file = $_FILES['picup-image-upload'];
        $file = $this->fileUpload($file, $this->_getParam('owner_id'));
        $this->view->photo_name = (isset($file['name'])) ? $file['name'] : '';
        $this->view->photo_id = $file->file_id;
        return;
      }

    if( !$this->_helper->requireUser->isValid() ) return;
//    if( !$this->_helper->requireAuth()->setAuthParams('pageevent', null, 'create')->isValid() ) return;

    $viewer = Engine_Api::_()->user()->getViewer();
    $page_id = $this->_getParam('page_id');
    $subject = ($page_id) ? Engine_Api::_()->getDbTable('pages', 'page')->findRow($page_id) : null;

    $this->view->form = $form = new Touch_Form_Pageevent_Create;

    // Not post/invalid
    if( !$this->getRequest()->isPost() )
    {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      return;
    }

    $values = $form->getValues();

    $values['user_id'] = $viewer->getIdentity();


    // Convert times
    $oldTz = date_default_timezone_get();
    date_default_timezone_set($viewer->timezone);
    $start = strtotime($values['starttime']);
    $end = strtotime($values['endtime']);
    date_default_timezone_set($oldTz);
    $values['starttime'] = date('Y-m-d H:i:s', $start);
    $values['endtime'] = date('Y-m-d H:i:s', $end);

    $db = $this->getTable()->getAdapter();
    $db->beginTransaction();

    try{
        $table = $this->_helper->api()->getDbTable('pageevents', 'pageevent');

        $pageevent = $table->createRow();
        $pageevent->page_id = $this->_subject->getIdentity();
        $pageevent->setFromArray($values);
        $pageevent->save();
      if(!$values['photo_id'])
      {
        if ($photo = Engine_Api::_()->storage()->get($values['photo_id']))
        {
          $pageevent->photo_id = $photo->getIdentity();
          $pageevent->save();
        }
      }else
      // Add photo
      if( !empty($values['photo']) ) {
              $photo = Engine_Api::_()->getApi('core', 'pageevent')->uploadPhoto($form->photo);
      }
      $db->commit();
    }
    catch(Engine_Image_Exception $e){
        $db->rollBack();

        $form->addError(Zend_Registry::get('Zend_Translate')->_('The image you selected was too large.'));
        throw $e;
    }
    catch(Exception $e)
    {
        $db->rollBack();
        throw $e;
    }


      

    return $this->_forward('success', 'utility', 'touch', array(
      'messages' =>array($this->view->translate("TOUCH_EVENT_FORM_CREATE_SUCCESS") ),
      'parentRedirect' => $this->view->url(array('action' => 'view', 'page_id' => $page_id), 'page_view', true),
    ));

  }

  public function editAction()
  {
      
  }

  public function viewAction()
  {
    $event_id = $this->_getParam('event_id');
    if (!$event_id){
      return $this->_forward('index', 'index', 'page');

    }

    $event = $this->getTable()->findRow($event_id);
    if (!$event){
      return $this->_forward('index', 'index', 'page');

    }

    $viewer = Engine_Api::_()->user()->getViewer();

    Engine_Api::_()->core()->setSubject($event);

    // General
    $this->view->event = $event;
    $this->view->subject = $event->getParent();
    $this->view->owner = $event->getOwner();
    $this->view->viewer = $viewer;
    $this->view->event_id = $event->getIdentity();
    $this->view->isTeamMember = $event->getPage()->isTeamMember($viewer);
    $this->view->isOwner = $isOwner = $viewer->isSelf($event->getOwner());
    $this->view->isLogin = (bool)$viewer->getIdentity();

    if (!$isOwner){
      $event->view();
    }

    // Membership
    $membership = $event->membership();
    $this->view->attending = $membership->getMemberPaginator(2);
    $this->view->maybe_attending = $membership->getMemberPaginator(1);
    $this->view->not_attending = $membership->getMemberPaginator(0);
    $this->view->count_waiting = $membership->getWaitingCount();
    $this->view->member = $membership->getRow($viewer);
    $this->view->isFriends = $membership->isFriends($viewer);

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

  }

  public function rsvpAction()
  {
    $event_id = $this->_getParam('event_id');
    $rsvp = (int)$this->_getParam('rsvp');

    if ($rsvp < 0 || $rsvp > 2){ $rsvp = 2; }

    $this->view->result = false;

    if (!$event_id){
      return $this->_forward('index', 'index', 'page');

    }

    $table = $this->getTable();
    $event = $table->findRow($event_id);
    if (!$event){
      return $this->_forward('index', 'index', 'page');

    }

    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$viewer->getIdentity()){
      return $this->_forward('index', 'index', 'page');

    }
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $member = $event->membership()->getRow($viewer);

      if (!$member)
      {
        $member = $event->membership()
            ->addMember($viewer)
            ->getRow($viewer);

        $event->membership()
          ->setUserApproved($viewer);

        if ($member->active)
        {
          // Add Activity
          $api = Engine_Api::_()->getDbtable('actions', 'activity');
          $action = $api->addActivity($viewer, $event->getPage(), 'pagevent_join', null, array('link' => $event->__toString(), 'is_mobile' => true));
          if ($action){
            $api->attachActivity($action, $event, Activity_Model_Action::ATTACH_DESCRIPTION);
          }
          Engine_Api::_()->getDbtable('notifications', 'activity')
            ->addNotification($event->getOwner(), $viewer, $event, 'pageevent_approved', array('link' => $event->getHref()));
        }
      }

      $event->membership()
          ->setUserApproved($viewer);

      $member->rsvp = ($event->approval && !$member->active) ? 3 : $rsvp;
      $member->save();

      $db->commit();
      $this->view->result = true;
      $this->view->member = $member;
      $this->view->event_id = $event_id;
    // Membership
    $membership = $event->membership();
    $this->view->attending = $membership->getMemberPaginator(2);
    $this->view->maybe_attending = $membership->getMemberPaginator(1);
    $this->view->not_attending = $membership->getMemberPaginator(0);
    $this->view->count_waiting = $membership->getWaitingCount();
    $this->view->member = $membership->getRow($viewer);
    $this->view->isFriends = $membership->isFriends($viewer);

    }
    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }
  }

  public function memberApproveAction()
  {
    $event_id = $this->_getParam('event_id');
    $approve = $this->_getParam('approve');

    $this->view->result = false;
    $this->view->message = $this->view->translate('PAGEEVENT_REQUEST_ERROR');

    if (!$event_id){
      return $this->_forward('index', 'index', 'page');

    }

    $table = $this->getTable();
    $event = $table->findRow($event_id);
    if (!$event){
      return $this->_forward('index', 'index', 'page');

    }

    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$viewer->getIdentity()){
      return $this->_forward('index', 'index', 'page');

    }
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $member = $event->membership()->getRow($viewer);
      if (!$member){ return;  }

      if ($approve){

        $event->membership()->setUserApproved($viewer);

        if ($member->active)
        {
          // Add Activity
          $api = Engine_Api::_()->getDbtable('actions', 'activity');
          $action = $api->addActivity($viewer, $event->getPage(), 'pagevent_join', null, array('link' => $event->__toString()));
          if ($action){
            $api->attachActivity($action, $event, Activity_Model_Action::ATTACH_DESCRIPTION);
          }
          Engine_Api::_()->getDbtable('notifications', 'activity')
            ->addNotification($event->getOwner(), $viewer, $event, 'pageevent_approved', array('link' => $event->getHref()));
        }

        $rsvp = $this->_getParam('rsvp');

        if ($rsvp !== null && in_array($rsvp, array(0,1,2))){
          $member->rsvp = $rsvp;
          $member->save();
        }

      } else {
        $event->membership()->removeMember($viewer);
      }

      // Set the request as handled
      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
        $viewer, $event, 'pageevent_invite');

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

    if ($this->_getParam('return_url')){
      $return_url = urldecode($this->_getParam('return_url'));
    } else {
      $return_url = $this->view->url(array(
        'action' => 'view',
        'event_id' => $event->getIdentity()
      ), 'page_event');
    }

    return $this->_redirect($return_url);

  }

  public function waitingAction()
  {
    $this->view->result = false;

    $event_id = $this->_getParam('event_id');

    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$event_id || !$viewer->getIdentity()){
      return $this->_forward('index', 'index', 'page');

    }

    $table = $this->getTable();

    $event = $table->findRow($event_id);
    if (!$event){
      return $this->_forward('index', 'index', 'page');

    }

    $this->view->result = true;
    $this->view->event_id = $event_id;
    $this->view->subject = $event->getParent();
    $this->view->event = $event;


    $table = Engine_Api::_()->getDbTable('users', 'user');
    $eventmember_tbl = Engine_Api::_()->getDbTable('pageeventmembership', 'pageevent');
    $select = $table->select()
        ->setIntegrityCheck(false)
        ->from(array('u' => $table->info('name')), array('u.*'))
        ->join(array('em' => $eventmember_tbl->info('name')), 'em.user_id = u.user_id', array('em.user_approved'))
        ->where('em.resource_id = ?', $event->getIdentity())
        ->where('em.active = 0');

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

  }

  public function resourceApproveAction()
  {
    $event_id = $this->_getParam('event_id');
    $user_id = $this->_getParam('user_id');
    $approve = (bool)$this->_getParam('approve');

    $this->view->result = false;

    if (!$event_id){
      return $this->_forward('index', 'index', 'page');

    }

    $table = $this->getTable();
    $event = $table->findRow($event_id);
    if (!$event){
      return $this->_forward('index', 'index', 'page');

    }

    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$viewer->getIdentity() || (!$event->getPage()->isTeamMember($viewer) && !$viewer->isSelf($event->getOwner()))){
      return $this->_forward('index', 'index', 'page');

    }
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $user = Engine_Api::_()->user()->getUser($user_id);
      if (!$user){
        return $this->_forward('index', 'index', 'page');

      }

      $member = $event->membership()->getRow($user);
      if (!$member){
        return $this->_forward('index', 'index', 'page');

      }

      if ($approve){

        $event->membership()->setResourceApproved($user);

        if ($member->active)
        {
          // Add Activity
          $api = Engine_Api::_()->getDbtable('actions', 'activity');
          $action = $api->addActivity($viewer, $event->getPage(), 'pagevent_join', null, array('link' => $event->__toString()));
          if ($action){
            $api->attachActivity($action, $event, Activity_Model_Action::ATTACH_DESCRIPTION);
          }
          Engine_Api::_()->getDbtable('notifications', 'activity')
            ->addNotification($user, $viewer, $event, 'pageevent_accepted', array('link' => $event->getHref()));
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

    return $this->_redirect($this->view->url(array(
      'action' => 'waiting',
      'event_id' => $event->getIdentity()
    ), 'page_event'));

  }

  protected function getTable()
  {
    return Engine_Api::_()->getDbTable('pageevents', 'pageevent');
  }

  public function fileUpload($file, $user_id)
  {
        $user = Engine_Api::_()->getItem('user', $user_id);
        if (!$user) {
            return;
        }
        try {
            $params = array(
                'parent_type' => 'temporary',
                'parent_id' => 0,
                'user_id' => $user->getIdentity()
            );
            return Engine_Api::_()->storage()->create($file, $params);

        } catch (Exception $e) {
            return;
        }

    }
  protected function getPaginator($show, $viewer_id = 0, $page = 1)
  {
    $table = $this->getTable();
    $this->view->form_filter = $form = new Touch_Form_Search();
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $this->view->form_value = $this->_getParam('search');
    }
    $select = $table->select()
        ->where('page_id = ?', $this->_subject->getIdentity());

    // Show Past Events
    if ($show == 'past'){
      $select->where('endtime < FROM_UNIXTIME(?)', time());
    }
    // Show User Events
    else if ($show == 'user' && $viewer_id)
    {
      $select->where('user_id = ?', $viewer_id);
    }
    // Show Upcoming Events
    else {
      $select->where('endtime > FROM_UNIXTIME(?)', time());
    }

    $select->order('starttime ASC')
           ->order('modified_date DESC');
    if ($this->_getParam('search', false)) {
      $select->where('title LIKE ? OR description LIKE ?', '%' . $this->_getParam('search') . '%');
    }
    $paginator = Zend_Paginator::factory($select);
    $paginator->setCurrentPageNumber($page);
    $paginator->setItemCountPerPage(5);
    $paginator->setCurrentPageNumber($this->_getParam('page'));
    return $paginator;
  }
}
