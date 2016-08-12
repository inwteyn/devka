<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    SocialBoost
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: AdminIndexController.php 08.10.13 14:26 Ulan T $
 * @author     Ulan T
 */

/**
 * @category   Application_Extensions
 * @package    SocialBoost
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class SocialBoost_AdminIndexController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('socialboost_admin_main', array(), 'socialboost_admin_main_settings');

    $this->view->form = $form = new SocialBoost_Form_Admin_Settings();

    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $modules = Engine_Api::_()->getDbTable('modules', 'core');
    $values = $form->getValues();

    $settings->setSetting('socialboost.facebook.app.id', $values['facebook_app_id']);
    $settings->setSetting('socialboost.admin.facebook', $values['facebook']);
    $settings->setSetting('socialboost.admin.twitter', $values['twitter']);
    $settings->setSetting('socialboost.admin.google', $values['google']);
    $settings->setSetting('socialboost.admin.days', $values['days']);

    if ($modules->isModuleEnabled('updates')) {
      $settings->setSetting('socialboost.admin.newsletter', $values['newsletter']);
    }

    $form->addNotice('Your changes have been saved.');
  }

  public function rewardsAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('socialboost_admin_main', array(), 'socialboost_admin_main_rewards');

    $form = $this->view->form = new SocialBoost_Form_Admin_Rewards();

    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $modules = Engine_Api::_()->getDbTable('modules', 'core');
    $values = $form->getValues();

    $settings->setSetting('socialboost.admin.reward', $values['reward']);

    if ($modules->isModuleEnabled('credit')) {
      $settings->setSetting('socialboost.admin.credit', $values['credit']);
      $settings->setSetting('socialboost.credit.amount', $values['credit_amount']);
    }

    if ($modules->isModuleEnabled('offers')) {
      $settings->setSetting('socialboost.admin.offers', $values['offers']);
    }

    $form->addNotice('Your changes have been saved.');
  }

  public function clearOfferAction() {
    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $settings->setSetting('socialboost.offer.id', 0);
    $redirect = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'social-boost',
      'controller'=>'index',   'action'=>'rewards'), 'admin_default', true);
    $this->redirect($redirect);
  }

  public function chooseOfferAction()
  {
    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $params = $request->getParams();


    $params['filter'] = 'upcoming';

    $select = Engine_Api::_()->getDbTable('offers', 'offers')->getOffersSelect($params);
    $select->where('o.page_id = 0');
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    if (!empty($params['page'])) {
      $paginator->setCurrentPageNumber($params['page']);
    }
    $paginator->setItemCountPerPage($paginator->getTotalItemCount());


    $this->view->isSuggestEnabled = $isSuggestEnabled = Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('suggest');

    $this->view->currentDate = $currentDate = date('Y-m-d h:i:s');

    $this->view->offer_id = $settings->getSetting('socialboost.offer.id');

    if (!$this->getRequest()->isPost()) {
      return;
    }

    $values = $this->getRequest()->getPost();
    if (isset($values['offer_id'])) {
      $settings->setSetting('socialboost.offer.id', $values['offer_id']);
    }

    $settings->setSetting('socialboost.admin.offers', 1);

    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.');
    return $this->_forward('success', 'utility', 'core', array(
      'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'social-boost',
          'controller'=>'index',   'action'=>'rewards'), 'admin_default', true),
      'messages' => Array($this->view->message)
    ));
  }
}