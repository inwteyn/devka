<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Bootstrap.php 2010-07-02 19:54 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Inviter_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
  public function __construct($application)
  {
    parent::__construct($application);
    $this->initViewHelperPath();

    //Add main inviter javascript
    $headScript = new Zend_View_Helper_HeadScript();
    $headScript->appendFile('application/modules/Inviter/externals/scripts/core.js');
  }

  protected function _initFrontController()
  {
		$this->initActionHelperPath();
    Zend_Controller_Action_HelperBroker::addHelper(new Inviter_Controller_Action_Helper_Invite());
  }
}