<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagecontact
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 2011-07-13 16:01 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Pagecontact
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagecontact_IndexController extends Core_Controller_Action_Standard
{
  private $page_id;

  public function editAction()
  {
    $this->view->page_id = $page_id = $this->_getParam('page_id', 0);
    $this->view->adminContactForm = $adminContactForm = new Pagecontact_Form_AdminContact($page_id);

    if (!$this->getRequest()->isPost()) {
      return 0;
    }

    if( !$adminContactForm->isValid($params = $this->getRequest()->getPost()) ) {
      $adminContactForm->populate($params);
      return 0;
    }

    //Validation email
    $i = 0;
    $validateEmail = new Zend_Validate_EmailAddress();

    while(isset($params['extra_' . $i])) {
      $values = $params['extra_' . $i];

      if (empty($values['topic_name']) || empty($values['emails'])) {
        return $adminContactForm->addError(Zend_Registry::get('Zend_Translate')->_('PAGECONTACT_Please full fill empty fields and try again.'));
      }

      $emails = explode(',', $values['emails']);
      $j = 0;
      foreach($emails as $email) {
        $email = trim($email);
        if (!$validateEmail->isValid($email)) {
          return $adminContactForm->addError($email . ' ' . Zend_Registry::get('Zend_Translate')->_('PAGECONTACT_It is not valid email address, please correct and try again.'));
        }
        $j++;
      }
      $i++;
    }

    $descriptionTbl = Engine_Api::_()->getDbTable('descriptions', 'pagecontact');
    $page_id_isFound = $descriptionTbl->findPage_id($page_id);

    $topicsTbl = Engine_Api::_()->getDbTable('topics', 'pagecontact');
    $topics = $topicsTbl->getTopics($page_id);

    $description = $this->_getParam('description');

    // if this page new
    if(!$page_id_isFound) {
      // insert new description
      $data = array('page_id' => $page_id, 'description' => $description);
      $descriptionTbl->insert($data);

      // insert new topics
      $i = 0;
      while($this->_getParam('extra_' . $i)) {
        $values = $this->_getParam('extra_'.$i);
        $data = array('topic_name' => $values['topic_name'], 'emails' => $values['emails'], 'page_id' => $page_id);
        $topicsTbl->insert($data);
        $i++;
      }
    } else { // if this page exist
      $descriptionWhere = array('page_id = ?' => $page_id);
      $descriptionTbl->update(array('description'=> $description), $descriptionWhere);

      // update topics
      $c = 0;
      while($this->_getParam('extra_' . $c)) {
        $values = $this->_getParam('extra_'.$c);
        for ($i = 0; $i < $topics->count(); $i++) {
          if ($values['topic_id'] == $topics[$i]['topic_id']) {
            $topicsWhere = array('page_id = ?' => $page_id, 'topic_id = ?' => $values['topic_id']);
            $topicsTbl->update(array('topic_name' => $values['topic_name'], 'emails' => $values['emails']), $topicsWhere);
          }
        }
        $c++;
      }

      // insert new added topics
      $i = 0;
      while($this->_getParam('extra_' . $i)) {
        $values = $this->_getParam('extra_'.$i);
        if(!$values['topic_id']) {
          $data = array('topic_name' => $values['topic_name'], 'emails' => $values['emails'], 'page_id' => $page_id);
          $topicsTbl->insert($data);
        }
        $i++;
      }
    }
    $this->view->adminContactForm = $adminContactForm = new Pagecontact_Form_AdminContact($page_id);
    $adminContactForm->addNotice(Zend_Registry::get('Zend_Translate')->_('PAGECONTACT_Changes have been saved successfully.'));
  }

  public function deleteAction()
  {
    $topic_id = $this->_getParam('topic_id', 0);
    $page_id = $this->_getParam('page_id', 0);

    if ($topic_id) {
      $topicsTbl = Engine_Api::_()->getDbTable('topics', 'pagecontact');

      $where = array('page_id = ?'=> $page_id, 'topic_id = ?' => $topic_id);
      $topicsTbl->delete($where);
    }
  }

  public function sendAction()
  {
    $page_id = $this->_getParam('page_id');
    $topic_id = $this->_getParam('topic_id');
    $subject = $this->_getParam('subject');
    $message = $this->_getParam('message');
    $senderName = $this->_getParam('sender_name');
    $senderEmail = $this->_getParam('sender_email');

    $pagesTbl = Engine_Api::_()->getDbTable('pages', 'page');
    $select = $pagesTbl->select()
        ->from(array($pagesTbl->info('name')), array('displayname'))
        ->where('page_id = ?', $page_id);
    $query = $select->query();
    $result = $query->fetchAll();
    $pageName = $result[0]['displayname'];

    $viewer = $this->_helper->api()->user()->getViewer();
    $user_id = $viewer->getIdentity();

    $topicsTbl = Engine_Api::_()->getDbTable('topics', 'pagecontact');
    $emails = $topicsTbl->getEmails($page_id, $topic_id);

    $i = 0;
    $emails = explode(',',$emails);

    foreach($emails as $email) {
      $emails[$i] = trim($email);
      $i++;
    }

    if ($user_id != 0) {
      $senderName = $viewer['displayname'];
      $senderEmail = $viewer['email'];
    }

    foreach($emails as $email) {
      // Make params
      $mail_settings = array(
        'date' => time(),
        'page_name' => $pageName,
        'sender_name' => $senderName,
        'sender_email' => $senderEmail,
        'subject' => $subject,
        'message' => $message,
      );

      // send email
      Engine_Api::_()->getApi('mail', 'core')->sendSystem(
        $email,
        'pagecontact_template',
        $mail_settings
      );
    }
  }
}