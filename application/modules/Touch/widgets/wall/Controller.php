<?php
class Touch_Widget_WallController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = null;
    if( Engine_Api::_()->core()->hasSubject() ) {
      $subject = Engine_Api::_()->core()->getSubject();
      if( !$subject->authorization()->isAllowed($viewer, 'view') || !in_array($subject->getType(), Engine_Api::_()->wall()->getSupportedItems()) || $this->_getParam('from_tl',isset($_GET['from_tl'])?$_GET['from_tl']:false)) {
        return $this->setNoRender();
      }
    }

    $request = Zend_Controller_Front::getInstance()->getRequest();
    if($subject)
        $this->view->callback_url = $callback_url = $subject->getHref();

    // Get some options
    $this->view->feedOnly = $feedOnly = $request->getParam('feedOnly', false);
    $this->view->length = $length = $request->getParam('limit', Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.length', 15));
    $this->view->itemActionLimit = $itemActionLimit = Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.userlength', 5);

    $this->view->updateSettings   = Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.liveupdate');
    $this->view->viewAllLikes     = $request->getParam('viewAllLikes',    $request->getParam('show_likes',    false));
    $this->view->viewAllComments  = $request->getParam('viewAllComments', $request->getParam('show_comments', false));
    $this->view->getUpdate        = $request->getParam('getUpdate');
    $this->view->checkUpdate      = $request->getParam('checkUpdate');
    $this->view->action_id        = (int) $request->getParam('action_id');
    $this->view->comment_pagination = $request->getParam('comment_pagination', false);

    $this->view->photo_id = $photo_id = $request->getParam('photo_id', 0);
    $this->view->text = $text = $request->getParam('text', '');

    if($photo_id != 0){
      $photo = Engine_Api::_()->getItem('photo', $photo_id);
      $this->view->photo_src  = $photo_src = $photo->getPhotoUrl('thumb.normal');
    }
    $userSetting = null;
    if ($viewer->getIdentity()){
      $userSetting = Engine_Api::_()->getDbTable('userSettings', 'wall')->getUserSetting($viewer);
    }
    $this->view->userSetting = $userSetting;

    if( $feedOnly ) {
      $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    }
    if( $length > 50 ) {
      $this->view->length = $length = 50;
    }

    $config = array(
      'action_id' => (int) $request->getParam('action_id'),
      'max_id'    => (int) $request->getParam('maxid'),
      'min_id'    => (int) $request->getParam('minid'),
      'limit'     => (int) $length
    );

    if (!empty($subject)){
      $config['items'] = array(array('type' => $subject->getType(), 'id' => $subject->getIdentity()));
    }


    // Lists
    if (empty($subject) && $viewer->getIdentity()){

      $list_params = array(
        'mode' => 'recent',
        'list_id' => 0,
        'type' => ''
      );

      

      $default_type = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.list.default');
      if ($default_type != ''){ 
        $list_params['mode'] = 'type';
        $list_params['type'] = $default_type;

        if (Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.list.user_save', false)){
          $userSetting->getParams();
          $list_params['mode'] = $userSetting->mode;
          $list_params['type'] = $userSetting->type;
          $list_params['list_id'] = $userSetting->list_id;
        }

      }
      if ($request->getParam('mode')){

        if (Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.list.user_save', false)){
          $userSetting->setParams($request);
        }

        $list_params['mode'] = $request->getParam('mode', 'recent');
        $list_params['list_id'] = $request->getParam('list_id');
        $list_params['type'] = $request->getParam('type');
      }

      $this->view->list_params = $list_params;

      if ($list_params['mode'] == 'type'){

        try {

          $types = Engine_Api::_()->wall()->getManifestType('wall_type');

          if (in_array($list_params['type'], array_keys($types))){
            $typeClass = Engine_Api::_()->loadClass(@$types[$list_params['type']]['plugin']);
            if ($typeClass instanceof Wall_Plugin_Type_Abstract) {
              $config['items'] = $typeClass->getItems($viewer);
              $config['showTypes'] = $typeClass->getTypes($viewer);
            }
          }

        } catch (Exception $e){}

      } else if ($list_params['mode'] == 'list'){

        $list = Engine_Api::_()->getDbTable('lists', 'wall')->getList($list_params['list_id']);
        if ($list) {
          $config['items'] = $list->getItems();
        }

      }

      $this->view->types = array_keys(Engine_Api::_()->wall()->getManifestType('wall_type'));
      $this->view->lists = Engine_Api::_()->getDbTable('lists', 'wall')->getPaginator($viewer);

    }


    $actionTable = Engine_Api::_()->getDbtable('actions', 'wall');

    $selectCount = 0;
    $nextid = null;
    $firstid = null;
    $tmpConfig = $config;
    $activity = array();
    $endOfFeed = false;

    $friendRequests = array(); 
    $itemActionCounts = array();

    $grouped_actions = array();
    $group_types = array('friends', 'like_item_private');


    do {

      $actions = $actionTable->getActivity($viewer, $tmpConfig);
      $selectCount++;

      if (count($actions) < $length || count($actions) <= 0) {
        $endOfFeed = true;
      }

      if (count($actions) > 0) {

        foreach ($actions as $action) {

          if (null === $nextid || $action->action_id <= $nextid) {
            $nextid = $action->action_id - 1;
          }
          if( null === $firstid || $action->action_id > $firstid ) {
            $firstid = $action->action_id;
          }

          if( !$action->getTypeInfo() || !$action->getTypeInfo()->enabled ) continue;

          if (!$action->hasObjectItem()) continue;

          if (!$action->getSubject() || !$action->getSubject()->getIdentity()) continue;
          if (!$action->getObject() || !$action->getObject()->getIdentity()) continue;

          if (empty($subject)) {
            $actionSubject = $action->getSubject();
            $actionObject = $action->getObject();
            if (!isset($itemActionCounts[$actionSubject->getGuid()])) {
              $itemActionCounts[$actionSubject->getGuid()] = 1;
            } else if ($itemActionCounts[$actionSubject->getGuid()] >= $itemActionLimit) {
              continue;
            } else {
              $itemActionCounts[$actionSubject->getGuid()]++;
            }
          }
          if ($action->type == 'friends') {
            $id = $action->subject_id . '_' . $action->object_id;
            $rev_id = $action->object_id . '_' . $action->subject_id;
            if (in_array($id, $friendRequests) || in_array($rev_id, $friendRequests)) {
              continue;
            } else {
              $friendRequests[] = $id;
              $friendRequests[] = $rev_id;
            }
          }

          if (in_array($action->type, $group_types)){

            $subject_guid = $action->getSubject()->getGuid();
            $total_guid = $action->type . '_' . $subject_guid;

            if (!isset($grouped_actions[$total_guid])){
              $grouped_actions[$total_guid] = array();
            }
            $grouped_actions[$total_guid][] = $action->getObject();

            if (count($grouped_actions[$total_guid]) > 1){
              continue ;
            }

          }

          try {
            //$attachments = $action->getAttachments(); // unused var
          } catch (Exception $e) {
            continue;
          }

          if (count($activity) < $length) {
            $activity[] = $action;
            if (count($activity) == $length) {
              $actions = array();
            }
          }
        }
      }

      if ($nextid) {
        $tmpConfig['max_id'] = $nextid;
      }
      if (!empty($tmpConfig['action_id'])) {
        $actions = array();
      }

    } while (count($activity) < $length && $selectCount <= 3 && !$endOfFeed);


    foreach ($activity as $key => $action){

      if (in_array($action->type, $group_types)){

        $subject_guid = $action->getSubject()->getGuid();
        $total_guid = $action->type . '_' . $subject_guid;

        if (isset($grouped_actions[$total_guid])){
          foreach ($grouped_actions[$total_guid] as $item){
            $activity[$key]->grouped_subjects[] = $item;
          }
        }
      }
    }

    $this->view->activity = $activity;
    $this->view->activityCount = count($activity);
    $this->view->nextid = (int) $nextid;
    $this->view->firstid = $firstid;
    $this->view->endOfFeed = $endOfFeed;


    if( !empty($subject) ) {
      $this->view->subjectGuid = $subject->getGuid(false);
    }

    $this->view->enableComposer = false;
    if ($viewer->getIdentity() && !$this->_getParam('action_id')) {
      if( !$subject || ($subject instanceof Core_Model_Item_Abstract && $subject->isSelf($viewer)) ) {
        if( Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'user', 'status') ) {
          $this->view->enableComposer = true;
        }
      } else if( $subject ) {
        if( Engine_Api::_()->authorization()->isAllowed($subject, $viewer, 'comment') ) {
        $this->view->enableComposer = true;
      }
    }
    }

    $path = Zend_Controller_Front::getInstance()->getControllerDirectory('wall');
    $path = dirname($path) . '/views/scripts';
    $this->view->addScriptPath($path);



    if (!$feedOnly){ // no ajax

      // Instance
      $unique = rand(11111, 99999);
      $this->view->feed_uid = 'wall_' . $unique;

      // Composers
      $composePartials = array();
      foreach (Engine_Api::_()->wall()->getManifestType('wall_touch_composer') as $type => $config){
        if( !empty($config['auth']) && !Engine_Api::_()->authorization()->isAllowed($config['auth'][0], null, $config['auth'][1]) ) {
          continue;
        }
        $composePartials[$type] = $config['script'];
      }
      $this->view->composePartials = $composePartials;
    }


    // Composer Privacy

    $this->view->allowPrivacy = false;
    $this->view->privacy_type = $privacy_type = ($subject) ? $subject->getType() : 'user';
    $this->view->privacy = $privacy = method_exists(Engine_Api::_()->wall(), 'getPrivacy') ? Engine_Api::_()->wall()->getPrivacy($privacy_type) : false;

    if ($viewer->getIdentity() && $privacy){

      $this->view->allowPrivacy = true;
      $this->view->privacy_active = (empty($privacy[0])) ? null : $privacy[0];
      $last_privacy =false;
      if(method_exists(Engine_Api::_()->getDbTable('userSettings', 'wall'), 'getLastPrivacy'));
      $last_privacy = Engine_Api::_()->getDbTable('userSettings', 'wall')->getLastPrivacy($subject, $viewer);
      if ($last_privacy){
        $this->view->privacy_active = $last_privacy;
      }
      
    }
    $this->view->services = array_keys(Engine_Api::_()->wall()->getManifestType('wall_service'));
    $path = Zend_Controller_Front::getInstance()->getControllerDirectory('touch');
    $path = dirname($path) . '/modules/Wall/views/scripts';
    $this->view->addScriptPath($path);

  }

}