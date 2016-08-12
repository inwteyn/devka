<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Survey
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 2010-07-02 19:27 ermek $
 * @author     Ermek
 */


/**
 * @category   Application_Extensions
 * @package    Survey
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Survey_IndexController extends Core_Controller_Action_Standard
{
  protected $_navigation;
  protected $_survey_navigation;
  protected $_survey_tabs;
  protected $_survey_options;
  protected $_survey_results;
  protected $_survey;

  public function init()
  {
    // only show to member_level if authorized

    if( !$this->_helper->requireAuth()->setAuthParams('survey', null, 'view')->isValid() )return;

    $ajaxContext = $this->_helper->getHelper('AjaxContext');
    $ajaxContext->addActionContext('delete-result', 'json')
      ->addActionContext('delete-question', 'json')
      ->initContext();
  }

  public function indexAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->navigation = $this->getNavigation();
    $this->view->form = $form = new Survey_Form_Search();

    $this->view->can_create = $this->_helper->requireAuth()->setAuthParams('survey', null, 'create')->checkRequire();

    if (!$viewer->getIdentity()) {
      $form->removeElement('show');
    }

    $form->removeElement('publish');

    // Populate form
    $this->view->categories = $categories = Engine_Api::_()->survey()->getCategories();

    foreach ($categories as $category) {
      $form->category->addMultiOption($category->category_id, $category->category_name);
    }

    // Process form
    $form->isValid($this->getRequest()->getPost());
    $values = $form->getValues();

    // Populate form data
    if (!$values && $form->isValid($this->_getAllParams())) {
      $values = $this->_getAllParams();
      $form->populate($values);
    }

    // Do the show thingy
    if (@$values['show'] == 2) {

      // Get an array of friend ids to pass to getSurveyzesPaginator
      $table = Engine_Api::_()->getDbtable('membership', 'user');
      $select = $table->select()
        ->from($table->info('name'), array('user_id'))
        ->where('resource_id = ?', $viewer->getIdentity())
        ->where('active = ?', true);

      $friends = $table->getAdapter()->fetchCol($select);
      $values['users'] = $friends;

    }

    $values['publish'] = 1;
    $values['approved'] = 1;

    $this->view->assign($values);
    $paginator = Engine_Api::_()->survey()->getSurveyzesPaginator($values);

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $items_per_page = $settings->getSetting('surveyzes.items.onpage', 10);

    $paginator->setItemCountPerPage($items_per_page);

    $this->view->browse_paginator = $paginator->setCurrentPageNumber( $values['page'] );

    if (!empty($values['category'])) {
      $this->view->categoryObject = Engine_Api::_()->survey()->getCategory($values['category']);
    }

    $this->view->rateEnabled = Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('rate');
    $this->view->theme_name = Engine_Api::_()->survey()->getCurrentTheme();
  }

  public function createAction()
  {
    if (!$this->_helper->requireUser->isValid()) {
      return;
    }

    // check if user has create rights
    if (!$this->_helper->requireAuth()->setAuthParams('survey', null, 'create')->isValid()) {
      return;
    }

    // Create navigation menu
    $this->view->navigation = $this->getNavigation();

    // Create form
    $this->view->form = $form = new Survey_Form_Create();

    // Populate with categories
    $categories = Engine_Api::_()->getDbtable('categories', 'survey')->fetchAll();

    foreach ($categories as $row) {
      $form->category_id->addMultiOption($row->category_id, $row->category_name);
    }

    if (count($form->category_id->getMultiOptions()) <= 1) {
      $form->removeElement('category_id');
    }

    // Check method/data validity
    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    // Process
    $values = $form->getValues();
    $viewer = Engine_Api::_()->user()->getViewer();

    $values['user_id'] = $viewer->getIdentity();

    $table = Engine_Api::_()->getDbtable('surveys', 'survey');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      // Create survey
      $survey = $table->createRow();
      $survey->setFromArray($values);
      $survey->approved = Engine_Api::_()->getApi('settings', 'core')->getSetting('surveyzes.approve', 1);
      $survey->save();

      // Set photo
      if (!empty($values['photo'])) {
        $survey->setPhoto($form->photo);
      }

      // Process privacy
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'everyone');

      $auth = Engine_Api::_()->authorization()->context;

      $auth_view = ($values['auth_view']) ? $values['auth_view'] : 'everyone';
      $viewMax = array_search($auth_view, $roles);

      foreach ($roles as $i => $role) {
        $auth->setAllowed($survey, $role, 'view', ($i <= $viewMax));
      }

      $auth_comment = ($values['auth_comment']) ? $values['auth_comment'] : 'everyone';
      $commentMax = array_search($auth_comment, $roles);

      foreach ($roles as $i => $role) {
        $auth->setAllowed($survey, $role, 'comment', ($i <= $commentMax));
      }

      // Add tags
      $tags = preg_split('/[,]+/', $values['tags']);
      $survey->tags()->addTagMaps($viewer, $tags);

      // Commit
      $db->commit();

      $urlOptions = array('action' => 'create-result', 'survey_id' => $survey->getIdentity());

      // Redirect
      return $this->_helper->redirector->gotoRoute($urlOptions, 'survey_specific', true);
    }

    catch (Engine_Image_Exception $e)
    {
      $db->rollBack();
      $form->addError(Zend_Registry::get('Zend_Translate')->_('survey_The image you selected was too large.'));
    }

    catch (Exception $e)
    {
      $db->rollBack();
      $form->addError(Zend_Registry::get('Zend_Translate')->_('The image you selected was damaged.'));
    }
  }

  public function editAction()
  {
    if (!$this->_helper->requireUser()->isValid()) {
      return;
    }
    $survey_id = $this->_getParam('survey_id');
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->survey = $this->_survey =  $survey = Engine_Api::_()->getItem('surveys', $survey_id);

    if ($survey && !Engine_Api::_()->core()->hasSubject('survey')) {
      Engine_Api::_()->core()->setSubject($survey);
    }

    if (!$this->_helper->requireSubject()->isValid()) {
      return;
    }



    if ($viewer->getIdentity() != $survey->user_id ) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    $navigation = $this->getNavigation(true);
    $this->view->navigation = $navigation;

    $this->generateSurveyNavigation('edit');

    $this->view->form = $form = new Survey_Form_Edit();

    // Populate with categories
    $categories = Engine_Api::_()->getDbtable('categories', 'survey')->fetchAll();
    foreach ($categories as $row) {
      $form->category_id->addMultiOption($row->category_id, $row->category_name);
    }

    if (count($form->category_id->getMultiOptions()) <= 1) {
      $form->removeElement('category_id');
    }

    if (!$this->getRequest()->isPost() || $this->_getParam('saved'))
    {
      // prepare tags
      $surveyTags = $survey->tags()->getTagMaps();

      $tagString = '';
      foreach ($surveyTags as $tagmap)
      {
        if ($tagString !== '') {
          $tagString .= ', ';
        }

        $tagString .= $tagmap->getTag()->getTitle();
      }

      $this->view->tagNamePrepared = $tagString;
      $form->tags->setValue($tagString);

      $form->populate($survey->toArray());

      // Populate auth
      $auth = Engine_Api::_()->authorization()->context;

      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'everyone');

      foreach ($roles as $role)
      {
        if (1 === $auth->isAllowed($survey, $role, 'view')) {
          $form->auth_view->setValue($role);
        }
        if (1 === $auth->isAllowed($survey, $role, 'comment')) {
          $form->auth_comment->setValue($role);
        }
      }

      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    // Process

    // handle save for tags
    $values = $form->getValues();
    $tags = preg_split('/[,]+/', $values['tags']);

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try
    {
      // Set survey info
      $survey->setFromArray($values);
      $survey->modified_date = new Zend_Db_Expr('NOW()');
      $survey->save();

      if (!empty($values['photo'])) {
        $survey->setPhoto($form->photo);
      }

      // Process privacy
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'everyone');

      $auth_view = ($values['auth_view']) ? $values['auth_view'] : 'everyone';
      $viewMax = array_search($auth_view, $roles);

      foreach ($roles as $i => $role) {
        $auth->setAllowed($survey, $role, 'view', ($i <= $viewMax));
      }

      $auth_comment = ($values['auth_comment']) ? $values['auth_comment'] : 'everyone';
      $commentMax = array_search($auth_comment, $roles);

      foreach ($roles as $i => $role) {
        $auth->setAllowed($survey, $role, 'comment', ($i <= $commentMax));
      }

      // Add tags
      $survey->tags()->setTagMaps($viewer, $tags);

      if ($survey->published == 1) {
        $actionsTable = Engine_Api::_()->getDbtable('actions', 'activity');
        $action = $actionsTable->getActionsByObject($survey);

        if (count($action->toArray()) > 0) {
          // Rebuild privacy
          foreach ($actionsTable->getActionsByObject($survey) as $action) {
            $actionsTable->resetActivityBindings($action);
          }
        }
      }

      $db->commit();

      $form->addNotice(Zend_Registry::get('Zend_Translate')->_('You changes has been saved successfully'));
    }

    catch (Engine_Image_Exception $e)
    {
      $db->rollBack();
      $form->addError(Zend_Registry::get('Zend_Translate')->_('survey_The image you selected was too large.'));
    }

    catch (Exception $e)
    {
      $db->rollBack();
      $form->addError(Zend_Registry::get('Zend_Translate')->_('The image you selected was damaged.'));
    }
  }

  public function createResultAction()
  {
    if (!$this->_helper->requireUser()->isValid()) {
      return;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $survey_id = $this->_getParam('survey_id');

    $this->view->survey = $this->_survey = $survey = Engine_Api::_()->getItem('surveys', $survey_id);

    if (!Engine_Api::_()->core()->hasSubject('survey')) {
      Engine_Api::_()->core()->setSubject($survey);
    }



    if ($viewer->getIdentity() != $survey->user_id ) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    $navigation = $this->getNavigation(true);
    $this->view->navigation = $navigation;

    $this->generateSurveyNavigation('create-result');

    $surveyResults = $survey->getResultList();
    $this->view->assign('surveyResults', $surveyResults);

    // Assign URLS
    $urlOptions = array('survey_id' => $survey_id, 'result_id' => 'result_id');
    $this->view->edit_url = $this->_helper->url->url($urlOptions, 'survey_edit_result');

    $urlOptions = array('survey_id' => $survey_id, 'result_id' => 'result_id', 'format' => 'json');
    $this->view->delete_url = $this->_helper->url->url($urlOptions, 'survey_delete_result');

    $this->view->form = $form = new Survey_Form_CreateResult();

    // Save survey entry
    $saved = $this->_getParam('saved');

    if (!$this->getRequest()->isPost() || $saved)
    {
      $form->survey_id->setValue($survey_id);
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost()))
    {
      return;
    }

    // Process form
    $values = $form->getValues();

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try
    {
      $survey->modified_date = new Zend_Db_Expr('NOW()');
      $survey->published = ($survey->published == 1) ? (int)$survey->isCompleted() : $survey->published;
      $survey->save();

      $db->commit();
    }
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    $table = Engine_Api::_()->getDbTable('results', 'survey');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $result = $table->createRow();
      $result->setFromArray($values);
      $result->save();

      if (!empty($values['photo'])) {
        $result->setPhoto($form->photo);
      }

      $db->commit();

      $urlOptions = array('action' => 'create-result', 'survey_id' => $survey->getIdentity());

      return $this->_helper->redirector->gotoRoute($urlOptions, 'survey_specific', true);
    }
    catch( Exception $e )
    {
      $db->rollBack();
      $form->addError(Zend_Registry::get('Zend_Translate')->_('The image you selected was damaged.'));
    }
  }

  public function deleteResultAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->survey_id = $survey_id = $this->_getParam('survey_id');
    $this->view->result_id = $result_id = $this->_getParam('result_id');

    // Send to view script if not POST
    if (!$this->getRequest()->isPost()) {
      return;
    }

    $survey = Engine_Api::_()->getItem('surveys', $survey_id);
    $result = Engine_Api::_()->getDbtable('results', 'survey')->findRow($result_id);

    $canCreate =  $this->_helper->requireAuth()->setAuthParams($survey, null, 'create')->isValid();

    if ($survey->user_id == $viewer->getIdentity() || $canCreate) {
      $db = Engine_Api::_()->getDbtable('results', 'survey')->getAdapter();
      $db->beginTransaction();

      try {
        $this->view->result_id = $result->result_id;
        $result->delete();
        $db->commit();

        if (!$survey->isCompleted() && $survey->published) {
          $survey->published = 0;
          $survey->save();
        }

        // tell smoothbox to close
        $this->view->status  = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('survey_This result has been removed.');

      } catch (Exception $e) {
        $db->rollback();
        $this->view->status = false;
      }
    } else {
      // neither the item owner, nor the item subject.  Denied!
      $this->_helper->redirector->gotoRoute(array(), 'core_home');
    }

    return;
  }

  public function editResultAction()
  {
    if (!$this->_helper->requireUser()->isValid()) {
      return;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $survey_id = $this->_getParam('survey_id');
    $result_id = $this->_getParam('result_id');

    $this->_survey = $survey = Engine_Api::_()->getItem('surveys', $survey_id);
    $result = Engine_Api::_()->getDbTable('results', 'survey')->findRow($result_id);

    if (!Engine_Api::_()->core()->hasSubject('survey')) {
      Engine_Api::_()->core()->setSubject($survey);
    }



    if ($viewer->getIdentity() != $survey->user_id ) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    $navigation = $this->getNavigation(true);
    $this->view->navigation = $navigation;

    $this->generateSurveyNavigation('create-result');

    $this->view->form = $form = new Survey_Form_EditResult();

    // Save survey entry
    $saved = $this->_getParam('saved');

    if (!$this->getRequest()->isPost() || $saved)
    {
      $form->populate($result->toArray());
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost()))
    {
      return;
    }

    // Process form
    $values = $form->getValues();

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try
    {
      $survey->modified_date = new Zend_Db_Expr('NOW()');
      $survey->published = ($survey->published == 1) ? (int)$survey->isCompleted() : $survey->published;
      $survey->save();

      $db->commit();
    }
    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    $table = Engine_Api::_()->getDbTable('results', 'survey');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $result->setFromArray($values);
      $result->save();

      if (!empty($values['photo'])) {
        $result->setPhoto($form->photo);
      }

      $db->commit();

      $urlOptions = array('action' => 'create-result', 'survey_id' => $survey->getIdentity());

      return $this->_helper->redirector->gotoRoute($urlOptions, 'survey_specific', true);
    }
    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }
  }

  public function createQuestionAction()
  {
    if (!$this->_helper->requireUser()->isValid()) {
      return;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $survey_id = $this->_getParam('survey_id');

    $this->view->survey = $this->_survey = $survey = Engine_Api::_()->getItem('surveys', $survey_id);

    if (!Engine_Api::_()->core()->hasSubject('survey')) {
      Engine_Api::_()->core()->setSubject($survey);
    }



    if ($viewer->getIdentity() != $survey->user_id ) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    $navigation = $this->getNavigation(true);
    $this->view->navigation = $navigation;

    $this->generateSurveyNavigation('create-question');

    $surveyResults = $survey->getResultList();
    $this->view->assign('surveyResults', $surveyResults);

    $surveyQuestions = $survey->getQuestionList(true);
    $this->view->assign('surveyQuestions', $surveyQuestions);

    //Assign URLS
    $urlOptions = array('survey_id' => $survey_id, 'question_id' => 'question_id');
    $this->view->edit_url = $this->_helper->url->url($urlOptions, 'survey_edit_question');

    $urlOptions = array('survey_id' => $survey_id, 'question_id' => 'question_id', 'format' => 'json');
    $this->view->delete_url = $this->_helper->url->url($urlOptions, 'survey_delete_question');

    $this->view->form = $form = new Survey_Form_CreateQuestion();

    // array of result titles
    $result_list = array();

    // Add Answers fields
    $order = 2;
    $question_answers = array();
    foreach ($surveyResults as $surveyResult) {
      $result_list[$surveyResult->getIdentity()] = $surveyResult->title;

      $form->addElement('Text', 'answer_' . $surveyResult->getIdentity(), array(
        'label' => $surveyResult->title . ' -> ',
        'allowEmpty' => false,
        'required' => true,
        'order' => $order,
        'class' => 'result_answer',
        'filters' => array(
        new Engine_Filter_Censor(),
          'StripTags',
          new Engine_Filter_StringLength(array('max' => '255'))
      )));

      $question_answers[] = 'answer_' . $surveyResult->getIdentity();

      $order++;
    }

    $form->addDisplayGroup($question_answers, 'question_answers', array('order' => $order));

    $this->view->assign('result_list', $result_list);

    // Save survey entry
    $saved = $this->_getParam('saved');

    if (!$this->getRequest()->isPost() || $saved)
    {
      $form->survey_id->setValue($survey_id);
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost()))
    {
      return;
    }

    // Process form
    $values = $form->getValues();

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try
    {
      $survey->modified_date = new Zend_Db_Expr('NOW()');
      $survey->published = ($survey->published == 1) ? (int)$survey->isCompleted() : $survey->published;
      $survey->save();

      $db->commit();
    }
    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    $table = Engine_Api::_()->getDbTable('questions', 'survey');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $question = $table->createRow();
      $question->setFromArray($values);
      $question->save();

      if (!empty($values['photo'])) {
        $question->setPhoto($form->photo);
      }

      // Save answers
      $answerTable = Engine_Api::_()->getDbtable('answers', 'survey');
      foreach ($surveyResults as $result) {
        $answer_info = array();
        $answer_info['question_id'] = $question->getIdentity();
        $answer_info['result_id'] = $result->getIdentity();
        $answer_info['label'] = $values['answer_' . $answer_info['result_id']];

        $answer = $answerTable->createRow();
        $answer->setFromArray($answer_info);
        $answer->save();
      }

      $db->commit();

      $urlOptions = array('action' => 'create-question', 'survey_id' => $survey->getIdentity());

      return $this->_helper->redirector->gotoRoute($urlOptions, 'survey_specific', true);
    }
    catch (Exception $e)
    {
      $db->rollBack();
      $form->addError(Zend_Registry::get('Zend_Translate')->_('The image you selected was damaged.'));
    }
  }

  public function deleteQuestionAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->survey_id = $survey_id = $this->_getParam('survey_id');
    $this->view->question_id = $question_id = $this->_getParam('question_id');

    // Send to view script if not POST
    if (!$this->getRequest()->isPost()) {
      return;
    }

    $survey = Engine_Api::_()->getItem('surveys', $survey_id);
    $question = Engine_Api::_()->getDbtable('questions', 'survey')->findRow($question_id);



    if ($survey->user_id == $viewer->getIdentity() ) {
      $db = Engine_Api::_()->getDbtable('questions', 'survey')->getAdapter();
      $db->beginTransaction();

      try {
        $this->view->question_id = $question->question_id;
        $question->delete();

        if (!$survey->isCompleted() && $survey->published) {
          $survey->published = 0;
          $survey->save();
        }

        $db->commit();

        // tell smoothbox to close
        $this->view->status  = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('survey_This question has been removed.');

      } catch (Exception $e) {
        $db->rollback();
        $this->view->status = false;
      }
    } else {
      // neither the item owner, nor the item subject.  Denied!
      $this->_helper->redirector->gotoRoute(array(), 'core_home');
    }

    return;
  }

  public function editQuestionAction()
  {
    if (!$this->_helper->requireUser()->isValid()) {
      return;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $survey_id = $this->_getParam('survey_id');
    $question_id = $this->_getParam('question_id');

    $this->_survey = $survey = Engine_Api::_()->getItem('surveys', $survey_id);
    $question = Engine_Api::_()->getDbtable('questions', 'survey')->findRow($question_id);

    if (!Engine_Api::_()->core()->hasSubject('survey')) {
      Engine_Api::_()->core()->setSubject($survey);
    }



    if ($viewer->getIdentity() != $survey->user_id ) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    $this->view->assign('question', $question);

    $navigation = $this->getNavigation(true);
    $this->view->navigation = $navigation;

    $this->generateSurveyNavigation('create-question');

    $surveyResults = $survey->getResultList();
    $this->view->assign('surveyResults', $surveyResults);

    $this->view->form = $form = new Survey_Form_EditQuestion();

    $answers = $question->getAnswers();

    $answerList = array();
    foreach ($answers as $answer) {
      $answerList[$answer->result_id] = $answer;
    }

    // Add Answers fields
    $order = 2;
    foreach ($surveyResults as $surveyResult) {
      $result_id = $surveyResult->getIdentity();
      $answer = isset($answerList[$result_id]) ? $answerList[$result_id] : array();

      $form->addElement('Text', 'answer_' . $result_id, array(
        'label' => $surveyResult->title,
        'value' => ($answer) ? $answer->label : '',
        'allowEmpty' => false,
        'required' => true,
        'order' => $order++,
        'filters' => array(
          new Engine_Filter_Censor(),
          'StripTags',
          new Engine_Filter_StringLength(array('max' => '255'))
      )));

      $form->addElement('Hidden', 'answer_id_' . $result_id, array(
        'value' => ($answer) ? $answer->answer_id : '',
        'allowEmpty' => false,
        'required' => ($answer) ? true : false,
        'order' => $order++,
        'filters' => array(
        new Engine_Filter_Censor(),
          'StripTags',
          new Engine_Filter_StringLength(array('max' => '255'))
      )));
    }

    // Save survey entry
    $saved = $this->_getParam('saved');

    if (!$this->getRequest()->isPost() || $saved)
    {
      $form->populate($question->toArray());

      return;
    }

    if (!$form->isValid($this->getRequest()->getPost()))
    {
      return;
    }

    // Process form
    $values = $form->getValues();

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try
    {
      $survey->modified_date = new Zend_Db_Expr('NOW()');
      $survey->published = ($survey->published == 1) ? (int)$survey->isCompleted() : $survey->published;
      $survey->save();

      $db->commit();
    }
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    $table = Engine_Api::_()->getDbTable('questions', 'survey');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $question->setFromArray($values);
      $question->save();

      if (!empty($values['photo'])) {
        $question->setPhoto($form->photo);
      }

      // Save answers
      $answerTable = Engine_Api::_()->getDbtable('answers', 'survey');
      foreach ($surveyResults as $result) {
        $answer_info = array();
        $answer_info['question_id'] = $question->getIdentity();
        $answer_info['result_id'] = $result->getIdentity();
        $answer_info['label'] = $values['answer_' . $answer_info['result_id']];

        $answer_key = 'answer_id_' . $answer_info['result_id'];

        if (isset($values[$answer_key]) && $values[$answer_key]) {
          $answer = $answerTable->findRow($values[$answer_key]);
        } else {
          $answer = $answerTable->createRow();
        }

        $answer->setFromArray($answer_info);
        $answer->save();
      }

      $db->commit();

      $urlOptions = array('action' => 'create-question', 'survey_id' => $survey->getIdentity());

      return $this->_helper->redirector->gotoRoute($urlOptions, 'survey_specific', true);
    }
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
  }

  public function publishAction()
  {
    if (!$this->_helper->requireUser()->isValid()) {
      return;
    }

    $survey_id = $this->_getParam('survey_id');
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->survey = $this->_survey = $survey = Engine_Api::_()->getItem('surveys', $survey_id);

    if (!Engine_Api::_()->core()->hasSubject('survey')) {
      Engine_Api::_()->core()->setSubject($survey);
    }

    if (!$this->_helper->requireSubject()->isValid()) {
      return;
    }



    if ($viewer->getIdentity() != $survey->user_id ) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    $navigation = $this->getNavigation(true);
    $this->view->navigation = $navigation;

    $this->generateSurveyNavigation('publish');

    $this->view->form = $form = new Survey_Form_Publish();

    if (!$this->getRequest()->isPost() || $this->_getParam('saved'))
    {
      $form->survey_id->setValue($survey_id);
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    // Process
    $values = $form->getValues();

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try
    {

      $survey->published = $values['published'];
      $survey->modified_date = new Zend_Db_Expr('NOW()');
      $survey->save();

      if ($survey->published == 1)
      {
        $actionsTable = Engine_Api::_()->getDbtable('actions', 'activity');

        $select = $actionsTable->select()
          ->where('type = ?', 'survey_new')
          ->where('subject_id = ?', $viewer->getIdentity())
          ->where('object_id = ?', $survey->getIdentity());

        $action = $actionsTable->fetchRow($select);

        if ($action != null) {
          $action->deleteItem();
        }
        $db->commit();

        $action = $actionsTable->addActivity($viewer, $survey, 'survey_new');

        // make sure action exists before attaching the survey to the activity
        if ($action != null)
        {
          $actionsTable->attachActivity($action, $survey);
        }
      }



      return $this->_helper->redirector->gotoRoute(array('survey_id' => $survey->getIdentity()), 'survey_manage', true);
    }

    catch (Exception $e)
    {
      $db->rollBack();
      print_die($e.'');
      throw $e;
    }
  }

  public function manageAction()
  {
  	if (!$this->_helper->requireUser()->isValid()) {
  	  return;
  	}

    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->level_id = Engine_Api::_()->user()->getViewer()->level_id;
    $this->view->navigation = $this->getNavigation();
    $this->view->form = $form = new Survey_Form_Search();
    $this->view->can_create = $this->_helper->requireAuth()->setAuthParams('survey', null, 'create')->checkRequire();

    $form->removeElement('show');

    $this->view->edit_url = $this->_helper->url->url(array('action' => 'edit', 'survey_id' => 'survey_id'), 'survey_specific');
    $this->view->delete_url = $this->_helper->url->url(array('action' => 'delete', 'survey_id' => 'survey_id'), 'survey_specific');

    // Populate form
    $this->view->categories = $categories = Engine_Api::_()->survey()->getCategories();
    foreach ($categories as $category)
    {
      $form->category->addMultiOption($category->category_id, $category->category_name);
    }

    // Process form
    $form->isValid($this->getRequest()->getPost());
    $values = $form->getValues();
    $values['user_id'] = $viewer->getIdentity();

    // Get paginator
    $this->view->paginator = $paginator = Engine_Api::_()->survey()->getSurveyzesPaginator($values);
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $items_per_page = $settings->getSetting('surveyzes.items.onpage', 5);
    $paginator->setItemCountPerPage($items_per_page);
    $this->view->paginator = $paginator->setCurrentPageNumber($values['page']);
    $this->view->theme_name = Engine_Api::_()->survey()->getCurrentTheme();
  }

  public function deleteAction()
  {
    $survey_id = $this->_getParam('survey_id');
    $this->view->survey = $survey = Engine_Api::_()->getItem('surveys', $survey_id);

    if (!$this->_helper->requireAuth()->setAuthParams($survey, null, 'delete')->isValid()) {
      return;
    };

    // Make form
    $this->view->form = $form = new Survey_Form_Delete();

    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    // Process form
    $db = Engine_Api::_()->getDbtable('surveys', 'survey')->getAdapter();
    $db->beginTransaction();

    try
    {
      $survey->delete();

      $db->commit();
    }
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    $this->_redirectCustom(array('route' => 'survey_manage'));
  }

  public function takeAction()
  {
    $survey_id = $this->_getParam('survey_id');
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->survey = $survey = Engine_Api::_()->getItem('surveys', $survey_id);

    if (!$survey || $survey->published == 0) {
      $this->_helper->redirector->gotoRoute(array(), 'survey_browse');
    }

    if (!$this->_helper->requireAuth()->setAuthParams('survey', null, 'take')->isValid()) {
      return;
    }

    if ($viewer->getIdentity() == 0) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    $surveyQuestions = $survey->getQuestionList(true);
    $this->view->assign('question_count', $surveyQuestions->count());

    $this->view->form = $form = new Survey_Form_Take();

    $number = 1;
    foreach ($surveyQuestions as $question)
    {
      $options = array();
      $option_ids = array();
      foreach ($question->answers as $answer) {
        $options[$answer->answer_id] = $answer->label;
        $option_ids[] = $answer->answer_id;
      }

      shuffle($option_ids);

      $multiOptions = array();
      foreach ($option_ids as $option_id) {
      	$multiOptions[$option_id] = $options[$option_id];
      }

      $form->addElement('Radio', 'question_' . $question->question_id, array(
        'class' => 'survey_answer',
        'required' => true,
        'multiOptions' => $multiOptions
      ));

      $photo_src = $question->getPhotoUrl();

      if ($photo_src) {
        $photo_options = array('title' => Zend_Registry::get('Zend_Translate')
          ->_('View fullsize'), 'onclick' => "he_show_image('$photo_src', $(this).getElement('img'))");
        $photo = $this->view->htmlLink('javascript://', $this->view->itemPhoto($question, 'thumb.normal'), $photo_options);
      } else {
        $photo = '';
      }

      $form->getElement('question_' . $question->question_id)
        ->addDecorator('SurveyQuestion', array('number' => $number++, 'label' => $question->text, 'photo' => $photo));
    }

    // Save survey entry
    $saved = $this->_getParam('saved');

    if (!$this->getRequest()->isPost() || $saved)
    {
      $form->survey_id->setValue($survey_id);
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost()))
    {
      return;
    }

     // Process form
    $values = $form->getValues();
    unset($values['survey_id']);
    $answer_ids = array_values($values);

    $table = Engine_Api::_()->getDbtable('takes', 'survey');
    $db = $table->getAdapter();

    try
    {
      $result_id = $survey->fetchTakeResult($answer_ids);
      $take = $survey->getUserResult($viewer->getIdentity());

      if (!$take) {
        $take = $table->createRow();

        $survey->take_count++;
        $survey->save();
      }

      $take_info = array();
      $take_info['survey_id'] = $survey_id;
      $take_info['user_id'] = $viewer->getIdentity();
      $take_info['result_id'] = $result_id;
      $take_info['took_date'] = new Zend_Db_Expr('NOW()');

      $take->setFromArray($take_info);
      $take->save();

      $choiceTable = Engine_Api::_()->getDbtable('choices', 'survey');
      $choiceTable->deleteUserChoices($survey_id, $viewer->getIdentity());

      foreach ($answer_ids as $answer_id) {
        $choice = $choiceTable->createRow();
        $choice->setFromArray(array(
          'survey_id' => $survey_id,
          'user_id' => $viewer->getIdentity(),
          'answer_id' => $answer_id));

        $choice->save();
      }

      $actionsTable = Engine_Api::_()->getDbtable('actions', 'activity');

      $select = $actionsTable->select()
        ->where('type = ?', 'survey_take')
        ->where('subject_id = ?', $viewer->getIdentity())
        ->where('object_id = ?', $survey->getIdentity());

      $action = $actionsTable->fetchRow($select);

      if ($action != null) {
        $action->deleteItem();
      }

      $action = $actionsTable->addActivity($viewer, $survey, 'survey_take');

      // make sure action exists before attaching the survey to the activity
      if ($action != null) {
        $actionsTable->attachActivity($action, $survey);
      }

      $db->commit();

      $slug = trim(preg_replace('/-+/', '-', preg_replace('/[^a-z0-9-]+/i', '-', strtolower($survey->getTitle()))), '-');

      $urlOptions = array('survey_id' => $survey->getIdentity(), 'slug' => $slug);

      return $this->_helper->redirector->gotoRoute($urlOptions, 'survey_view', true);
    }
    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }
  }

  public function viewAction()
  {

    $survey_id = $this->_getParam('survey_id');

    $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->survey = $survey = Engine_Api::_()->getItem('surveys', $survey_id);

    if (!empty($survey)) {
      Engine_Api::_()->core()->setSubject($survey);
    }

    if (!$this->_helper->requireSubject()->isValid()) {
      return;
    }


    $is_owner = ($survey->getOwner()->getIdentity() == $viewer->getIdentity());

    if (!$survey->published && $is_owner) {
      $urlOptions = array('action' => 'create-result', 'survey_id' => $survey->getIdentity());
      return $this->_helper->redirector->gotoRoute($urlOptions, 'survey_specific', true);
    } else if (!$survey->published) {
      return $this->_helper->redirector->gotoRoute(array(), 'survey_browse', true);
    }

    if (!$is_owner) {
      $survey->view_count++;
      $survey->save();
    }

    $this->view->userTake = $userTake = $survey->getUserResult($viewer->getIdentity());
    $this->view->surveyResults = $surveyResults = $survey->getResultList();
    $this->view->takeResults = $tookResults = $survey->getTakerList();

    if ($userTake) {
      $this->view->userResult = $surveyResults->getRowMatching('result_id', $userTake->result_id);

      $firstMatches = $survey->userMatches($viewer->getIdentity(), 1);
      $this->view->firstMatchCount = $firstMatches['count'];
      $this->view->firstMatches = $firstMatches['users'];

      $secondMatches = $survey->userMatches($viewer->getIdentity(), 2);
      $this->view->secondMatchCount = $secondMatches['count'];
      $this->view->secondMatches = $secondMatches['users'];
    }

    $survey_results = $surveyResults->toArray();
    $result_list = array();

    foreach ($survey_results as $survey_result) {
      $survey_result['tooks'] = $tookResults->getRowsMatching('result_id', $survey_result['result_id']);
      $survey_result['took_count'] = count($survey_result['tooks']);

      $result_list[$survey_result['result_id']] = $survey_result;
    }

    $this->view->assign('result_list', $result_list);

    $this->view->can_take = $this->_helper->requireAuth()->setAuthParams('survey', null, 'take')->checkRequire();
    $this->view->can_comment = $this->_helper->requireAuth()->setAuthParams($survey, null, 'comment')->checkRequire();
    //$this->view->can_comment = Engine_Api::_()->authorization()->context->isAllowed($survey, $viewer, 'comment');

    $this->view->survey_tabs = $this->getSurveyTabs();
    $this->view->survey_options = $this->getSurveyOptions();
    $this->view->take_url = $this->_helper->url->url(array(
      'action' => 'take',
      'survey_id' => $survey_id), 'survey_specific');

    $this->view->chart_data_url = $this->_helper->url->url(array(
      'module' => 'survey',
      'controller' => 'index',
      'action' => 'chart-data',
      'survey_id' => $survey_id,
      'bg_color' => 'bg_color_value',
      'color' => 'color_value',
      'no_cache' => uniqid('')), 'default', false);

    $this->view->rateEnabled = $rateEnabled = Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('rate');

    if ($rateEnabled) {
      $this->view->can_rate = (boolean)$userTake;
      $this->view->error_msg = ($is_owner)
        ? Zend_Registry::get('Zend_Translate')->_('Sorry, you cannot rate own content.')
        : Zend_Registry::get('Zend_Translate')->_('Please take this survey to continue.');
    }

    $this->view->maxShowUsers = Engine_Api::_()->getApi('settings', 'core')->getSetting('surveyzes.max.showusers', 14);
    $this->view->theme_name = Engine_Api::_()->survey()->getCurrentTheme();
  }

  public function chartDataAction()
  {
    // Create base chart
    require_once 'OFC/OFC_Chart.php';

    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

  	$survey_id = $this->_getParam('survey_id');
  	$bg_color = $this->_getParam('bg_color');
  	$color = $this->_getParam('color');

    $survey = Engine_Api::_()->getItem('surveys', $survey_id);

    $surveyResults = $survey->getResultList();
    $survey_results = $surveyResults->toArray();

    $tookResults = $survey->getTakerList();
    $chartData = array();
    foreach ($survey_results as $survey_result) {
      $resultTooks = $tookResults->getRowsMatching('result_id', $survey_result['result_id']);
      $survey_result['took_count'] = count($resultTooks);

      if ($survey_result['took_count'] > 0) {
        $chartData[] = array(
          'value' => $survey_result['took_count'],
          'label' => $this->view->string()->truncate($survey_result['title'], 10, '...')
        );
      }
    }

    $options = array('tip' => Zend_Registry::get('Zend_Translate')->_('{$val} of {$total} results<br>{$percent}'));

    if ($bg_color && strlen($bg_color) > 0) {
      $options['bg_colour'] = "#$bg_color";
    }

    $color = ($color && strlen($color) == 3)
      ? '#' . $color[0] . $color[0] . $color[1] . $color[1] . $color[2] . $color[2]
      : $color;
    $color = ($color && strlen($color) > 0) ? "#$color" : '#5F5F5F';

    if ($chartData) {
      $title = array('text' => $survey->getTitle(), 'style' => "color: $color; font-weight: bold; font-size: 17px;");
    } else {
      $title = array('text' => Zend_Registry::get('Zend_Translate')->_('survey_There are no results'), 'style' => "color: $color; font-size: 13px; padding-top: 100px;");
    }

    $chartDataJS = $this->generateOFC_Chart($title, $chartData, $options);

    $this->getResponse()->setBody($chartDataJS);
  }

  // Utility

  public function getNavigation($active = false)
  {
    if (is_null($this->_navigation))
    {
      $translate = Zend_Registry::get('Zend_Translate');
      $navigation = $this->_navigation = new Zend_Navigation();

      if (Engine_Api::_()->user()->getViewer()->getIdentity())
      {
        $navigation->addPage(array(
          'label' => $translate->_('Browse Surveyzes'),
          'route' => 'survey_browse',
          'module' => 'survey',
          'controller' => 'index',
          'action' => 'index'
        ));

        $navigation->addPage(array(
          'label' => $translate->_('My Surveyzes'),
          'route' => 'survey_manage',
          'module' => 'survey',
          'controller' => 'index',
          'action' => 'manage',
          'active' => $active
        ));

        if ($this->_helper->requireAuth()->setAuthParams('survey', null, 'create')->checkRequire()) {
          $navigation->addPage(array(
            'label' => $translate->_('Create new survey'),
            'route' => 'survey_create',
            'module' => 'survey',
            'controller' => 'index',
            'action' => 'create'
          ));
        }
      }
    }
    return $this->_navigation;
  }

  public function getSurveyNavigation($survey_id, $active_tab, $available_step)
  {
    if (!is_null($this->_survey_navigation)) {
      return $this->_survey_navigation;
    }

    $translate = Zend_Registry::get('Zend_Translate');
    $navigation = $this->_survey_navigation = new Zend_Navigation();

    if (Engine_Api::_()->user()->getViewer()->getIdentity())
    {
      $navigation->addPage(array(
        'label' => 'survey_Basics',
        'route' => 'survey_specific',
        'module' => 'survey',
        'controller' => 'index',
        'action' => 'edit',
        'active' => ($active_tab == 'edit'),
        'params' => array('survey_id' => $survey_id)
      ));

      $navigation->addPage(array(
        'label' => 'survey_Results',
        'route' => 'survey_specific',
        'module' => 'survey',
        'controller' => 'index',
        'action' => 'create-result',
        'active' => ($active_tab == 'create-result'),
        'class' => ($available_step > 1) ? '' : 'disabled',
        'params' => array('survey_id' => $survey_id)

      ));

      $navigation->addPage(array(
        'label' => 'survey_Questions',
        'route' => 'survey_specific',
        'module' => 'survey',
        'controller' => 'index',
        'action' => 'create-question',
        'active' => ($active_tab == 'create-question'),
        'class' => ($available_step > 2) ? '' : 'disabled',
        'params' => array('survey_id' => $survey_id)
      ));

      $navigation->addPage(array(
        'label' => 'survey_Publish',
        'route' => 'survey_specific',
        'module' => 'survey',
        'controller' => 'index',
        'action' => 'publish',
        'active' => ($active_tab == 'publish'),
        'class' => ($available_step > 3) ? '' : 'disabled',
        'params' => array('survey_id' => $survey_id)
      ));
    }

    return $this->_survey_navigation;
  }

  public function generateSurveyNavigation($active_tab = 'edit')
  {
    $surveyStatus = $this->_survey->getSurveyStatus();

    $settings = Engine_Api::_()->getApi('settings', 'core');

    $this->view->minResultCount = $minResultCount = (int)$settings->getSetting('surveyzes.min.result.count', 2);
    $this->view->minQuestionCount = $minQuestionCount = (int)$settings->getSetting('surveyzes.min.question.count', 1);

    $step_info = array('error' => 0, 'message' => '', 'redirect' => '');

    $step = 4;

    if ($surveyStatus['result_count'] < $minResultCount)
    {
      $step = 2;
      $step_info['message'] = $this->view->translate(array(
        'survey_You need to create at least %s result', 'You need to create at least %s results', $minResultCount),
      $minResultCount);
    }
    elseif ($surveyStatus['question_count'] < $minQuestionCount)
    {
      $step = 3;
      $step_info['message'] = $this->view->translate(array(
        'survey_You need to create at least %s question', 'You need to create at least %s questions', $minQuestionCount),
      $minQuestionCount);
    }
    elseif ($surveyStatus['result_count'] * $surveyStatus['question_count'] > $surveyStatus['answer_count'])
    {
      $step = 3;
      $step_info['message'] = $this->view->translate('survey_You need to fill out all answers');
    }

    $step_info['next_error'] = 1;

    switch ($active_tab) {
      case 'edit':
        if ($step < 1) {
          $step_info['error'] = 1;
          $step_info['redirect'] = $this->_helper->url->url(array(), 'survey_create');
        }
        if ($step > 1) {
        $urlOptions = array('action' => 'create-result', 'survey_id' => $this->_survey->survey_id);

        $step_info['next_error'] = 0;
        $step_info['next'] = $this->_helper->url->url($urlOptions, 'survey_specific');
        }
        break;

      case 'create-result':
        if ($step < 2) {
          $step_info['error'] = 1;
          $step_info['redirect'] = $this->_helper->url->url(array('survey_id' => $this->_survey->survey_id), 'edit');
        }
        if ($step > 2) {
          $urlOptions = array('action' => 'create-question', 'survey_id' => $this->_survey->survey_id);

          $step_info['next_error'] = 0;
          $step_info['next'] = $this->_helper->url->url($urlOptions, 'survey_specific');
        }
        break;

      case 'create-question':
        if ($step < 3) {
          $urlOptions = array('action' => 'create-result', 'survey_id' => $this->_survey->survey_id);

          $step_info['error'] = 1;
          $step_info['redirect'] = $this->_helper->url->url($urlOptions, 'survey_specific');
        }
        if ($step > 3) {
          $urlOptions = array('action' => 'publish', 'survey_id' => $this->_survey->survey_id);

          $step_info['next_error'] = 0;
          $step_info['next'] = $this->_helper->url->url($urlOptions, 'survey_specific');
        }
        break;

      case 'publish':
        if ($step < 3) {
          $urlOptions = array('action' => 'create-result', 'survey_id' => $this->_survey->survey_id);

          $step_info['error'] = 1;
          $step_info['redirect'] = $this->_helper->url->url($urlOptions, 'survey_specific');
        }
        else if ($step < 4) {
          $urlOptions = array('action' => 'create-question', 'survey_id' => $this->_survey->survey_id);

          $step_info['error'] = 1;
          $step_info['redirect'] = $this->_helper->url->url($urlOptions, 'survey_specific');
        }
        break;

      default:
        break;
    }

    $this->view->assign('step_info', Zend_Json::encode($step_info));

    $survey_navigation = $this->getSurveyNavigation($this->_survey->survey_id, $active_tab, $step);

    $this->view->survey_navigation = $survey_navigation;
  }

  public function getSurveyTabs()
  {
    if (!is_null($this->_survey_tabs)) {
      return $this->_survey_tabs;
    }

    $navigation = $this->_survey_tabs = new Zend_Navigation();
    $translate = Zend_Registry::get('Zend_Translate');

    if (Engine_Api::_()->user()->getViewer()->getIdentity() && $this->view->userTake)
    {
      $navigation->addPage(array(
        'label' => $translate->_('survey_My Matches'),
        'uri' => 'javascript://',
        'id' => 'matches',
        'class' => 'survey_tab',
        'active' => true
      ));
    }

    $navigation->addPage(array(
      'label' => $translate->_('Survey Results'),
      'uri' => 'javascript://',
      'id' => 'results',
      'class' => 'survey_tab',
      'active' => !(Engine_Api::_()->user()->getViewer()->getIdentity() && $this->view->userTake)
    ));

    $navigation->addPage(array(
      'label' => $translate->_('survey_Who Took This Survey'),
      'uri' => 'javascript://',
      'id' => 'tooks',
      'class' => 'survey_tab',
    ));

    if ($this->view->can_comment) {
      $navigation->addPage(array(
        'label' => $translate->_('Comments'),
        'uri' => 'javascript://',
        'id' => 'comments',
        'class' => 'survey_tab',
      ));
    }

    return $this->_survey_tabs;
  }

  public function getSurveyOptions()
  {
    if (!is_null($this->_survey_options)) {
      return $this->_survey_options;
    }

    $survey = $this->view->survey;
    $viewer = Engine_Api::_()->user()->getViewer();
    $navigation = $this->_survey_options = new Zend_Navigation();

    if ($viewer->getIdentity() && $viewer->getIdentity() == $survey->user_id) {
      $navigation->addPage(array(
        'label' => 'Edit Survey',
        'icon' => 'application/modules/Survey/externals/images/edit.png',
        'uri' => $this->_helper->url->url(array('action' => 'edit', 'survey_id' => $survey->getIdentity()), 'survey_specific'),
      ));

      $navigation->addPage(array(
        'label' => 'Delete Survey',
        'icon' => 'application/modules/Survey/externals/images/delete.png',
        'uri' => $this->_helper->url->url(array('action' => 'delete', 'survey_id' => $survey->getIdentity()), 'survey_specific'),
      ));
    }

    if ($viewer->getIdentity() && $survey->approved == 1) {
      $navigation->addPage(array(
        'label' => 'survey_Share Survey',
        'icon' => 'application/modules/Survey/externals/images/share.png',
        'class' => 'smoothbox',
        'route' => 'default',
        'params' => array(
          'module' => 'activity',
          'controller' => 'index',
          'action' => 'share',
          'type' => $survey->getType(),
          'id' => $survey->getIdentity(),
          'format' => 'smoothbox',
        ),
      ));
    }

    return $this->_survey_options;
  }

  public function generateOFC_Chart($title, $values = array(), $options = array())
  {
    $elements = array();

    if ($options && isset($options['tip'])) {
      $elements['tip'] = $options['tip'];
    }

    $elements['colours'] = isset($options['colours'])
      ? $options['colours']
      : array('#385D8A', '#8C3836', '#71893F', '#357D91', '#B66D31', '#426DA1', '#A44340',
        '#849F4B', '#6C548A', '#3F92A9', '#D37F3A', '#4B7BB4', '#B74C49', '#94B255', '#7A5F9A', '#47A4BD',
        '#A1B4D4', '#D6A1A0');

    $elements['alpha'] = isset($options['alpha']) ? $options['alpha'] : 0.8;
    $elements['start_angle'] = isset($options['start_angle']) ? $options['start_angle'] : 135;
    $elements['border'] = isset($options['border']) ? $options['border'] : 2;
    $elements['animate'] = isset($options['border']) ? $options['border'] : true;
    $elements['values'] = $values;
    $elements['type'] = 'pie';

    $chart = array();

    $chart['elements'][] = $elements;
    $chart['bg_colour'] = isset($options['bg_colour']) ? $options['bg_colour'] : "#E9F4FA";
    $chart['title'] = ($title && is_array($title))
      ? array('text' => $title['text'], 'style' => $title['style'])
      : array('text' => $title);

    return Zend_Json::encode($chart);
  }
}