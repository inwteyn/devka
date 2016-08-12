<?php

class Offers_Widget_ProfileOffersController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;

  public function indexAction()
  {
    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('page');
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

    if (!($subject instanceof Page_Model_Page)) {
      return $this->setNoRender();
    }

    if (!$subject->authorization()->isAllowed($viewer, 'view')) {
      return $this->setNoRender();
    }

    $path = Zend_Controller_Front::getInstance()->getControllerDirectory('offers');
    $path = dirname($path) . '/views/scripts';
    $this->view->addScriptPath($path);

    $params = array(
      'page_id' => $subject->getIdentity(),
      'filter' => 'upcoming'
    );

    $tbl = Engine_Api::_()->getDbTable('offers', 'offers');

    $this->view->isAllowedPost = $subject->authorization()->isAllowed($viewer, 'view');
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('offer_profile_page');
    $this->view->paginator = $paginator = $tbl->getOffersPaginator($params);

    // Form create
    $this->view->form = $form = new Offers_Form_Create();
    $form->getElement('page_id')->setValue($subject->getIdentity());

    // Form contacts
    $this->view->offerContactForm = $offerContactForm = new Offers_Form_Contacts();
    $offerContactForm->populate(Engine_Api::_()->offers()->getPageContacts($subject->getIdentity()));
//    print_die(Engine_Api::_()->offers()->getPageContacts($subject->getIdentity()));

    // Title count
    if ($this->_getAllParams('titleCount', false) && $paginator->getTotalItemCount() > 0) {
      $this->_childCount = $tbl->getCount($params);
    }

    $this->view->subject_type = $subject->getType();
  }

  public function getChildCount()
  {
    return $this->_childCount;
  }
}