<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagediscussion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 2010-07-02 19:54 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Pagediscussion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagediscussion_IndexController extends Core_Controller_Action_Standard
{
  protected $_subject;
  protected $_hasSubject;
  protected $_viewer;
  protected $_hasViewer;
  protected $_isTeamMember;

  public function init()
  {
    $isPageEnabled = Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('page');

    // only ajax requests or page disabled
    if (!$isPageEnabled) {
//    if ($this->_getParam('format') != 'json' || !$isPageEnabled) {
      $this->_forward('notfound', 'error', 'core');
      return;
    }

    // page subject
    if ($page_id = $this->_getParam('page_id')) {
      $this->view->pageObject = $this->_subject = Engine_Api::_()->getItem('page', $page_id);
    }
    $this->_hasSubject = (bool)$this->_subject;

    // allowed
    if ($this->view->subject && !Engine_Api::_()->getApi('core', 'page')->isAllowedView($this->_subject)) {
       $this->_forward('notfound', 'error', 'core');
      return;
    }

    // viewer
    $this->_viewer = Engine_Api::_()->user()->getViewer();
    $this->_hasViewer = ($this->_viewer && $this->_viewer->getIdentity());

    // is team member
    $this->_isTeamMember = ($this->_subject && $this->_viewer->getIdentity())
        ? $this->_subject->isAdmin($this->_viewer)
        : false;

  }

  public function indexAction()
  {
    if (!$this->_hasSubject) { return; }

    $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('pagetopics', 'pagediscussion')
        ->getPaginator($this->_subject->getIdentity(), $this->_getParam('page', 1), $this->_getParam('ipp'));

    $allowPost = Engine_Api::_()->getApi('core', 'pagediscussion')->isAllowedPost($this->_subject);
    $this->view->canCreate = ($this->_hasViewer && $allowPost);

    $this->view->count = $paginator->getTotalItemCount();

    // js
    $topic_list = array();
    foreach ($paginator as $item) {
      $topic_list[$item->getIdentity()] = $item->title;
    }
    $this->view->topic_list = $topic_list;

    $this->view->html = $this->view->render('list.tpl');

  }

  public function topicAction()
  {
    $topic_id = $this->_getParam('topic_id');
    $post_id = $this->_getParam('post_id');

    // get topic by post
    if (!$topic_id && $post_id && $postRow = $this->getPost($post_id)){
      $topic_id = $postRow->topic_id;
    }

    $topic = $this->getTopic($topic_id);
    if ($topic)
    {
      $this->view->hasViewer = $this->_hasViewer;
      $this->view->viewer = $this->_viewer;

      $this->view->isTeamMember = $this->_isTeamMember;
      $this->view->isWatching = ($this->_hasViewer) ? $topic->isWatching( $this->_viewer->getIdentity() ) : false;
      $this->view->isOwner = $isOnwer = ($this->_hasViewer) ? $topic->getOwner()->isSelf($this->_viewer) : false;

      if (!$isOnwer)
      {
        $topic->view_count++;
        $topic->save();
      }

      $allowPost = Engine_Api::_()->getApi('core', 'pagediscussion')->isAllowedPost($this->_subject);
      $this->view->canPost = ($this->_hasViewer && $allowPost && !$topic->closed);

      $this->view->topic = $topic;
      $this->view->topic_id = $topic->getIdentity();
      $this->view->paginator = $paginator = $topic->getPostPaginator($this->_getParam('page'), $this->_getParam('post_id'));

      // js
      $post_list = array();
      foreach ($paginator as $item) {
        $post_list[$item->getIdentity()] = $item->body;
      }
      $this->view->post_list = $post_list;
      if($this->_getParam('format') == 'json')
        $this->view->html = $this->view->render('index/topic.tpl');

    } else
    {
      $this->view->result = false;
      $this->view->msg = $this->view->translate('PAGEDISCUSSION_NOTFOUND');
      $this->view->html = $this->view->render('message.tpl');
    }

  }

  public function createAction()
  {
    if (!$this->_hasViewer || !$this->_hasSubject) { return ; }

    $result = false;
    $this->view->topic_id = 0;
    $this->view->post_id = 0;

    $form = new Pagediscussion_Form_Create;
    $isValid = ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()));

    if ($isValid)
    {
      $values = $form->getValues();
      $values['page_id'] = $this->_subject->getIdentity();
      $values['user_id'] = $this->_viewer->getIdentity();
/*        $values['creation_date'] = date('Y-m-d H:i:s');
      $values['modified_date'] = date('Y-m-d H:i:s');*/

      $tbl = Engine_Api::_()->getDbTable('pagetopics', 'pagediscussion');
      $tbl_post = Engine_Api::_()->getDbTable('pageposts', 'pagediscussion');
      $tbl_watch = Engine_Api::_()->getDbTable('pagetopicwatches', 'pagediscussion');

      $db = $tbl->getAdapter();
      $db->beginTransaction();

      try
      {
        // Create Topic
        $topic = $tbl->createRow($values);
        $topic->save();

        $topic_id = $topic->getIdentity();

        $values['topic_id'] = $topic_id;

        // Create Post
        $post = $tbl_post->createRow($values);

        $post->save();

        // Create Watch
        $tbl_watch->setWatch(
          $this->_subject->getIdentity(),
          $topic->getIdentity(),
          $this->_viewer->getIdentity(),
          $values['watch']
        );


        // Add Activity
        $link = $topic->getLink(array('child_id' => $post->getIdentity()));

        $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
        $action = $activityApi->addActivity($this->_viewer, $topic->getParentPage(), 'page_topic_create', null, array('link' => $link));
        if ($action) {
          $activityApi->attachActivity($action, $post, Activity_Model_Action::ATTACH_DESCRIPTION);
        }

        // notify all teams
        $api = Engine_Api::_()->getDbtable('notifications', 'activity');
        $teamMembers = $this->_subject->getAdmins();
        foreach ($teamMembers as $member){
          if ($member->isSelf($this->_viewer)){ continue; }
          $api->addNotification($member, $this->_viewer, $topic, 'page_discussion_team', array(
            'message' => $this->view->BBCode($post->body)
          ));
        }

        // Add Page Search
        $pageApi = Engine_Api::_()->getDbTable('search', 'page');
        $pageApi->saveData(array(
          'object' => $topic->getType(),
          'object_id' => $topic->getIdentity(),
          'page_id' => $topic->page_id,
          'title' => $topic->getTitle(),
          'body' => $post->getDescription(),
          'photo_id' => 0
        ));
        $pageApi->saveData(array(
          'object' => $post->getType(),
          'object_id' => $post->getIdentity(),
          'page_id' => $post->page_id,
          'title' => $topic->getTitle(),
          'body' => $post->getDescription(),
          'photo_id' => 0
        ));

        $db->commit();
        $this->view->topic_id = $topic_id;
        $this->view->post_id = $post->getIdentity();
        $result = true;

      }
      catch (Exception $e)
      {
        $db->rollBack();
        throw $e;
      }
    }

    $msg = 'PAGEDISCUSSION_CREATE_' . ( ($result) ? 'SUCCESS' : 'ERROR' );

    $this->view->result = $result;
    $this->view->msg = $this->view->translate($msg);
    $this->view->html = $this->view->render('message.tpl');

  }

  public function renameAction()
  {
    if (!$this->_hasViewer) { return ; }

    $result = false;

    $topic = $this->getTopic($this->_getParam('topic_id'));

    if ($topic && ($this->_viewer->isOwner($topic->getOwner()) || $this->_isTeamMember))
    {
      $form = new Pagediscussion_Form_Rename;
      $isValid = ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()));

      if ($isValid)
      {
        $values = $form->getValues();
/*        $values['modified_date'] = date('Y-m-d H:i:s');*/
        unset($values['topic_id']);
        $this->view->topic_id = $topic->getIdentity();

        $result = $topic->setFromArray($values)->save();

        if ($result)
        {
          // Add Page Search
          $pageApi = Engine_Api::_()->getDbTable('search', 'page');
          $pageApi->saveData(array(
            'object' => $topic->getType(),
            'object_id' => $topic->getIdentity(),
            'page_id' => $topic->page_id,
            'title' => $topic->getTitle(),
            'body' => $topic->getDescription(),
            'photo_id' => 0
          ));

          // Update Posts
          $adapter = $pageApi->getAdapter();
          $pageApi->update(
            array('title' => $topic->getTitle()),
            array(
              $adapter->quoteInto('object_id IN (?)', (array) $topic->getChildIds()),
              $adapter->quoteInto('object = ?', 'pagediscussion_pagepost', 'STRING')
            )
          );

        }

      }
    }

    $msg = 'PAGEDISCUSSION_RENAME_' . ( ($result) ? 'SUCCESS' : 'ERROR' );

    $this->view->result = $result;
    $this->view->msg = $this->view->translate($msg);
    $this->view->html = $this->view->render('message.tpl');

  }

  public function postAction()
  {
    if (!$this->_hasViewer) { return ; }

    $result = false;
    $this->view->topic_id = 0;
    $this->view->post_id = 0;

    $topic = $this->getTopic($this->_getParam('topic_id'));

    if ($topic && !$topic->closed) {

      $form = new Pagediscussion_Form_Post;
      $isValid = ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()));

      if ($isValid)
      {
        $values = $form->getValues();
        $values['topic_id'] = $topic->getIdentity();
        $values['page_id'] = $topic->page_id;
        $values['user_id'] = $this->_viewer->getIdentity();
/*        $values['creation_date'] = date('Y-m-d H:i:s');
        $values['modified_date'] = date('Y-m-d H:i:s');*/

        $tbl = Engine_Api::_()->getDbTable('pagetopics', 'pagediscussion');
        $tbl_post = Engine_Api::_()->getDbTable('pageposts', 'pagediscussion');
        $tbl_watch = Engine_Api::_()->getDbTable('pagetopicwatches', 'pagediscussion');

        $db = $tbl->getAdapter();
        $db->beginTransaction();

        try
        {
          // Create Post
          $post = $tbl_post->createRow($values);
          $post->save();

          // Watch
          $tbl_watch->notifyAll($topic, $post, $this->_viewer);

          // Set Watch
          $tbl_watch->setWatch(
            $topic->page_id,
            $topic->getIdentity(),
            $this->_viewer->getIdentity(),
            $values['watch']
          );

          // Add Activity
          $link = $topic->getLink(array('child_id' => $post->getIdentity()));

          $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
          $action = $activityApi->addActivity($this->_viewer, $topic->getParentPage(), 'page_topic_reply', null, array('link' => $link));
          if ($action) {
            $activityApi->attachActivity($action, $post, Activity_Model_Action::ATTACH_DESCRIPTION);
          }

          // Add Page Search
          $pageApi = Engine_Api::_()->getDbTable('search', 'page');
          $pageApi->saveData(array(
            'object' => $post->getType(),
            'object_id' => $post->getIdentity(),
            'page_id' => $topic->page_id,
            'title' => $topic->getTitle(),
            'body' => $post->getDescription(),
            'photo_id' => 0
          ));

          $this->view->topic_id = $topic->getIdentity();
          $this->view->post_id = $post->getIdentity();
          $db->commit();

          $result = true;

        }
        catch (Exception $e)
        {
          $db->rollBack();
          throw $e;
        }
      }
    }

    $msg = 'PAGEDISCUSSION_POST_' . ( ($result) ? 'SUCCESS' : 'ERROR' );

    $this->view->result = $result;
    $this->view->msg = $this->view->translate($msg);
    $this->view->html = $this->view->render('message.tpl');

  }

  public function editAction()
  {
    if (!$this->_hasViewer) { return ; }

    $result = false;
    $this->view->topic_id = 0;
    $this->view->post_id = 0;

    $post = $this->getPost($this->_getParam('post_id'));

    if ($post && ($post->isOwner($this->_viewer) || $this->_isTeamMember))
    {
      $form = new Pagediscussion_Form_Edit;

      $request = $this->getRequest();
      if (!$request->isPost() || !$form->isValid($request->getPost())) { return ; }

      $values = $form->getValues();
      $values['modified_date'] = date('Y-m-d H:i:s');
      unset($values['post_id']);

      $this->view->topic_id = $post->topic_id;
      $this->view->post_id = $post->getIdentity();
      $result = $post->setFromArray($values)->save();

      if ($result)
      {
        // Add Page Search
        $pageApi = Engine_Api::_()->getDbTable('search', 'page');
        $pageApi->saveData(array(
          'object' => $post->getType(),
          'object_id' => $post->getIdentity(),
          'page_id' => $post->page_id,
          'title' => $post->getTitle(),
          'body' => $post->getDescription(),
          'photo_id' => 0
        ));

        if ($post->isFirstPost())
        {
          $select = $pageApi->select()
              ->where('object = ?', 'pagediscussion_pagetopic', 'STRING')
              ->where('object_id = ?', $post->topic_id);
          $searchRow = $pageApi->fetchRow($select);
          if ($searchRow){
            $searchRow->body = $post->getDescription();
            $searchRow->save();
          }

        }

      }

    }

    $msg = 'PAGEDISCUSSION_EDIT_' . ( ($result) ? 'SUCCESS' : 'ERROR' );

    $this->view->result = $result;
    $this->view->msg = $this->view->translate($msg);
    $this->view->html = $this->view->render('message.tpl');

  }

  public function discussionAction()
  {
    if (!$this->_hasViewer) { return ; }

    $task = $this->_getParam('task');
    $set = ($this->_getParam('set')) ? 1 : 0;

    $tbl = Engine_Api::_()->getDbTable('pagetopics', 'pagediscussion');

    $db = $tbl->getAdapter();
    $db->beginTransaction();

    $topic_id = false;
    $result = false;

    try {

      if ($topic = $this->getTopic($this->_getParam('topic_id')))
      {
        if ($task == 'watch')
        {
          $result = Engine_Api::_()->getDbTable('pagetopicwatches', 'pagediscussion')
              ->setWatch($topic->page_id, $topic->getIdentity(), $this->_viewer->getIdentity(), $set);
        }
        else if ($task == 'close' && $this->_isTeamMember)
        {
          $topic->closed = $set;
          $result = $topic->save();
        }
        else if ($task == 'sticky' && $this->_isTeamMember)
        {
          $topic->sticky = $set;
          $result = $topic->save();
        }
        else if ($task == 'deletetopic' && ($this->_viewer->isSelf($topic->getOwner()) || $this->_isTeamMember))
        {
          $result = $topic->delete();
        }
        $topic_id = ($topic) ? $topic->getIdentity() : false;

      } else if ($post = $this->getPost($this->_getParam('post_id'))) {

        if ($task == 'deletepost' && ($this->_viewer->isSelf($post->getOwner()) || $this->_isTeamMember))
        {
          $current_topic_id = $post->topic_id;

          $post->delete();

          if ($topic = $this->getTopic($current_topic_id)) {
            $topic_id = $topic->getIdentity();
          }
          $result = true;
        }

      } else {

      }

      $db->commit();

    }
    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    $this->view->topic_id = $topic_id;

    $msg = 'PAGEDISCUSSION_' . strtoupper($task) . '_' . ( ($result) ? 'SUCCESS' : 'ERROR' );

    $this->view->result = $result;
    $this->view->msg = $this->view->translate($msg);
    $this->view->html = $this->view->render('message.tpl');

  }

  protected function getTopic($topic_id)
  {
    if ($topic_id) {
      return Engine_Api::_()->getDbTable('pagetopics', 'pagediscussion')->findRow($topic_id);
    }
    return false;
  }

  protected function getPost($post_id)
  {
    if ($post_id) {
      return Engine_Api::_()->getDbTable('pageposts', 'pagediscussion')->findRow($post_id);
    }
    return false;
  }

}