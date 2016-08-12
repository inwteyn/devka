<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Controller.php 01.08.12 16:12 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Credit_Widget_BuyLevelController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    /**
     * @var $viewer User_Model_User
     * @var $gatewayTable Payment_Model_DbTable_Gateways
     */

    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer->getIdentity()) {
      return $this->setNoRender();
    }

    $check = Engine_Api::_()->getDbTable('permissions', 'authorization')->isAllowed('credit', $viewer, 'view_credit_home');

    if( !$check) {
      $this->setNoRender(true);
    }


    $level = Engine_Api::_()->getItem('authorization_level', $viewer->level_id);
    if( in_array($level->type, array('admin', 'moderator')) ) {
      return $this->setNoRender();
    }
    
    $gatewayTable = Engine_Api::_()->getDbtable('gateways', 'payment');
    $query = $gatewayTable->fetchAll();
    foreach ($query as $q) {
      if (($gatewayTable->getEnabledGatewayCount() == 1) && ($q->title == "2Checkout" && $q->enabled == 1)) {
        return $this->setNoRender();
      }
    }

    // Have any gateways or packages been added yet?
    if( Engine_Api::_()->getDbtable('gateways', 'payment')->getEnabledGatewayCount() <= 0 ||
        Engine_Api::_()->getDbtable('packages', 'payment')->getEnabledNonFreePackageCount() <= 0 ) {
      return $this->setNoRender();
    }

    $this->view->form = $form = new Credit_Form_Payments_BuyLevel();
    $form->getDecorator('description')->setOption('escape', false);
  }
}
