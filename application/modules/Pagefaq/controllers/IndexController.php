<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagefaq
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 2011-09-28 15:18 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Pagefaq
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagefaq_IndexController extends Core_Controller_Action_Standard
{
  public function editAction()
  {
    /**
     * @var $descriptionTbl Pagefaq_Model_DbTable_Descriptions
     * @var $faqsTbl Pagefaq_Model_DbTable_Faqs
     **/

    $this->view->page_id = $page_id = $this->_getParam('page_id');
    $faqsTbl = Engine_Api::_()->getDbTable('faqs', 'pagefaq');
    $descriptionTbl = Engine_Api::_()->getDbTable('descriptions', 'pagefaq');
    $description = $descriptionTbl->getDescription($page_id);

    if ($description) {
      $description = $description->toArray();
      $description['descriptionFAQ'] = $description['description'];
    } else {
      $description = array();
    }

    $this->view->allFAQs = $faqsTbl->getAllFAQ($page_id);
    $this->view->descriptionFAQForm = $form =  new Pagefaq_Form_DescriptionFAQ();
    $form->populate($description);
    $this->view->editFAQForm = new Pagefaq_Form_EditFAQ();
    $this->view->createFAQForm = new Pagefaq_Form_EditFAQ();
  }

  public function saveAction()
  {
    $params = $this->_getAllParams();
    $faqsTbl = Engine_Api::_()->getDbTable('faqs', 'pagefaq');
    $faqsTbl->saveFAQ($params);
    $this->view->html = $faqsTbl->getFAQ($params['faq_id']);
  }

  public function deleteAction()
  {
    $faq_id = $this->_getParam('faq_id');

    $faqsTbl = Engine_Api::_()->getDbTable('faqs', 'pagefaq');
    $faqsTbl->deleteFAQ($faq_id);
    $this->view->faq_id = $faq_id;
  }

  public function savedescriptionAction()
  {
    $descriptionTbl = Engine_Api::_()->getDbTable('descriptions', 'pagefaq');
    $descriptionTbl->saveDescription($this->_getAllParams());
  }
}
