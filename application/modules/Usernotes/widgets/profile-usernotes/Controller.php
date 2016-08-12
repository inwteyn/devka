<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Usernotes
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2010-07-30 18:00 vadim $
 * @author     Vadim
 */

/**
 * @category   Application_Extensions
 * @package    Usernotes
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Usernotes_Widget_ProfileUsernotesController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;

  public function indexAction()
  {
    if (!Engine_Api::_()->authorization()->isAllowed('usernotes', null, 'enabled')) {
      $this->setNoRender();
    }

    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    $subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();

    $owner_id = $viewer->getIdentity();
    $user_id = $subject->getIdentity();

    if ($subject->getType() != 'user' || $owner_id == $user_id) {
      $this->setNoRender();
    }

    $this->view->urls_js =  Zend_Json::encode(array(
      'save_note' => $this->view->url(array('module' => 'usernotes','action' => 'save'), 'default'),
      'delete_note' => $this->view->url(array('module' => 'usernotes','action' => 'delete'), 'default'),
    ));

    $title = $this->view->translate("My note about %s", $subject->getTitle());
    $this->getElement()->setTitle($title);

    $this->view->form = $form = new Usernotes_Form_Index_Create();

    $form->clearDecorators();
    $form->addDecorator('FormElements');
    $form->setAttrib('class', 'he_usernotes_profile_form');
    $form->getElement('user_id')->setValue($user_id);
    $form->setAction($this->view->url(array('usernotes','index'),'default'));
    $form->addElement('Button', 'he_usernotes_cancel', array('label' => 'Cancel'));

    $this->view->usernote = Engine_Api::_()->usernotes()->getUsernoteByOwner($owner_id, $user_id);

    if ($this->view->usernote) {
      $form->getElement('note')->setValue($this->view->usernote->note);
      $this->view->note_js = Zend_Json::encode($this->view->usernote->note);
    }

    $form->addDecorator('Form');
  }

  public function getChildCount()
  {
    return $this->_childCount;
  }
}