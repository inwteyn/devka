<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 02.04.12 09:12 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */



class Hebadge_Widget_CreditLoaderController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $request = Zend_Controller_Front::getInstance()->getRequest();

    if (!$viewer->getIdentity()){
      return $this->setNoRender();
    }

    $this->view->owner_rank = Engine_Api::_()->getDbTable('creditbadges', 'hebadge')->getOwnerRank($viewer);
    $this->view->owner_next_rank = Engine_Api::_()->getDbTable('creditbadges', 'hebadge')->getOwnerNextRank($viewer);
    $this->view->owner_credit = $credit = Engine_Api::_()->getDbTable('creditbadges', 'hebadge')->getOwnerCredit($viewer);

    $current = 0;
    if ($credit){
      $current = $credit->earned_credit;
      if ($this->view->owner_rank){
        $current -= $this->view->owner_rank->credit;
      }
    }

    $total = $current;
    if ($this->view->owner_next_rank){
      $total = $this->view->owner_next_rank->credit;
      if ($this->view->owner_rank){
        $total -= $this->view->owner_rank->credit;
      }
    }

    $this->view->complete = 0;
    if ($current && $total){
      $this->view->complete = floor($current/$total*100);
    }

    $this->getElement()->setAttrib('id', 'content_id_' . $this->view->identity);
    $this->view->name = $this->getElement()->getName();
    $this->view->simple_name = str_replace("-", "_", str_replace(".", "_", $this->getElement()->getName()));
    $this->view->paginator_type = $this->_getParam('paginator_type');

  }

}