<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminBadgesController.php 02.04.12 09:12 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */



class Hebadge_AdminLevelBadgesController extends Core_Controller_Action_Admin
{

  public function indexAction()
  {
//    Engine_Api::_()->hebadge()->addLevelBadge();
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('hebadge_admin_main', array(), 'hebadge_badges');

    $table = Engine_Api::_()->getDbTable('badges', 'hebadge');

    $params =array();
    $params['levels'] = "1";

    $this->view->paginator = $paginator = $table->getPaginator($params, null, true);
    $paginator->setItemCountPerPage(20);
    $paginator->setCurrentPageNumber($this->_getParam('page'));


  }

  public function createAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('hebadge_admin_main', array(), 'hebadge_badges');

    $this->view->form = $form = new Hebadge_Form_Admin_Levelbadge_Create();

    // populate icon
    $form->getElement('photo')->getDecorator('hebadgeLevelPhoto')->setOptions(array('type' => 'thumb.profile'));
    $form->getElement('icon')->getDecorator('hebadgeLevelPhoto')->setOptions(array('type' => 'thumb.icon'));

    if (!$this->getRequest()->isPost()){
      return;
    }
    if (!$form->isValid($this->getRequest()->getPost())){
      return;
    }

    $values = $form->getValues();
    
    $levelValues = $this->getLevelValues($values['levels']);

    $values['level_type'] = 1;

    $table = Engine_Api::_()->getDbTable('badges', 'hebadge');

    $badge = $table->createRow();
    $badge->setFromArray($values);
    $badge->save();
    
    $badge->setBadgeLevels($levelValues); //Set badge levels

    if (!empty($values['photo'])){
      $badge->setPhoto($form->photo);
    }
    if (!empty($values['icon'])){
      $badge->setIcon($form->icon);
    }

    return $this->_helper->redirector->gotoRoute(array('module' => 'hebadge', 'controller' => 'levelbadges', 'action' => 'edit', 'badge_id' => $badge->badge_id), 'admin_default', true);

  }

  public function editAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('hebadge_admin_main', array(), 'hebadge_badges');

    $this->view->form = $form = new Hebadge_Form_Admin_Levelbadge_Edit();

    $badge = Engine_Api::_()->getItem('hebadge_badge', $this->_getParam('badge_id'));

    if (!$badge){
      return $this->_helper->redirector->gotoRoute(array('module' => 'hebadge', 'controller' => 'levelbadges', 'action' => 'index'), 'admin_default', true);
    }
    $populate_values = $badge->toArray();
    
    $badgeLevels = $badge->getBadgeLevels();    //Get Badge levels
    $populateLevels = $this->getPopulateLevels($badgeLevels);
    $populate_values['levels'] = $populateLevels;

    $form->populate($populate_values);

    // populate icon
    if ($badge->photo_id){
      $form->getElement('photo')->getDecorator('hebadgeLevelPhoto')->setOptions(array('item' => $badge, 'type' => 'thumb.profile'));
      $form->getElement('icon')->getDecorator('hebadgeLevelPhoto')->setOptions(array('item' => $badge, 'type' => 'thumb.icon'));
    } else {
      $form->getElement('photo')->getDecorator('hebadgeLevelPhoto')->setOptions(array('type' => 'thumb.profile'));
      $form->getElement('icon')->getDecorator('hebadgeLevelPhoto')->setOptions(array('type' => 'thumb.icon'));
    }

    if (!$this->getRequest()->isPost()){
      return;
    }
    if (!$form->isValid($this->getRequest()->getPost())){
      return;
    }

    $values = $form->getValues();
    $values['level_type'] = 1;
    $levelValues = $this->getLevelValues($values['levels']);

    $badge->setFromArray($values);
    $badge->save();

    $badge->setBadgeLevels($levelValues); //Set badge levels

    if (!empty($values['photo'])){
      $badge->setPhoto($form->photo);
    }
    if (!empty($values['icon'])){
      $badge->setIcon($form->icon);
    }

    // set after submit
    if ($badge->photo_id){
      $form->getElement('photo')->getDecorator('hebadgeLevelPhoto')->setOptions(array('item' => $badge, 'type' => 'thumb.profile'));
      $form->getElement('icon')->getDecorator('hebadgeLevelPhoto')->setOptions(array('item' => $badge, 'type' => 'thumb.icon'));
    } else {
      $form->getElement('photo')->getDecorator('hebadgeLevelPhoto')->setOptions(array('type' => 'thumb.profile'));
      $form->getElement('icon')->getDecorator('hebadgeLevelPhoto')->setOptions(array('type' => 'thumb.icon'));
    }

    $form->addNotice('Your changes have been saved.');

  }

  public function removeAction()
  {
    $badge = Engine_Api::_()->getItem('hebadge_badge', $this->_getParam('badge_id'));

    if ($badge){

      if ($this->getRequest()->isPost()){

        $badge->deleteLevelBadge();

        $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array('')
        ));

      }

    }

    $this->renderScript('admin-levelbadges/delete.tpl');

  }

  public function removePhotoAction()
  {
    if( defined('_ENGINE_ADMIN_NEUTER') && _ENGINE_ADMIN_NEUTER ) {
      return $this->_helper->redirector->gotoRoute(array('module' => 'hebadge', 'controller' => 'badges', 'action' => 'index'), 'admin_default', true);
    }


    $badge = Engine_Api::_()->getItem('hebadge_badge', $this->_getParam('badge_id'));

    if (!$badge){
      return $this->_helper->redirector->gotoRoute(array('module' => 'hebadge', 'controller' => 'levelbadges', 'action' => 'index'), 'admin_default', true);
    }

    if ($this->_getParam('type') == 'icon'){
      $badge->removeIcon();
    } else {
      $badge->removePhoto();
    }

    return $this->_helper->redirector->gotoRoute(array('module' => 'hebadge', 'controller' => 'levelbadges', 'action' => 'edit', 'badge_id' => $badge->badge_id), 'admin_default', true);


  }

  //Function for receiving populate of a massia for the form
  private function getPopulateLevels($items)
  {
    $levels_array = array();
    $populate_values = array();
    $levels = Engine_Api::_()->getItemTable('authorization_level')->fetchAll();

    //We create an array of the selected levels
    if ($items) {
      foreach ($items as $item) {
        array_push($levels_array, $item->level_id);
      }
    }

    //We create populate an array for the form
    if ($levels) {
      foreach ($levels as $level) {
        if (in_array($level->level_id, $levels_array)) {
          $populate_values['level_' . $level->getIdentity()] = '1';
        } else {
          $populate_values['level_' . $level->getIdentity()] = 'unchecked';
        }
      }
    }
    return $populate_values;
  }


  //Function for obtaining values of the levels selected on the form
  private function getLevelValues($formLevels)
  {
    $levels_array = array();
    $levels = Engine_Api::_()->getItemTable('authorization_level')->fetchAll();

        //We select those levels which were marked
    if ($levels) {
      foreach ($levels as $level) {
        if ($formLevels['level_' . $level->level_id] == 1) {
          array_push($levels_array, $level->level_id);
        }
      }
    }
    return $levels_array;
  }

}