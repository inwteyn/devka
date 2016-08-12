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



class Hebadge_Widget_InfoController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $table = Engine_Api::_()->getDbTable('badges', 'hebadge');
    $viewer = Engine_Api::_()->user()->getViewer();

    $request = Zend_Controller_Front::getInstance()->getRequest();

    if (!$viewer->getIdentity()){
      return $this->setNoRender();
    }

    $this->view->info = $info = Engine_Api::_()->getDbTable('info', 'hebadge')->getInfo($viewer);

  }

}