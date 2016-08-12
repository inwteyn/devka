<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: WallBaseUrl.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Headvancedmembers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Headvancedmembers_View_Helper_AdvmembersBaseUrl extends Zend_View_Helper_Abstract
{
  protected $version;
  public function __construct(){
    $this->version = Engine_Api::_()->getDbTable('modules', 'core')->getModule('core')->version;
  }
  public function advmembersBaseUrl()
  {
    if (version_compare($this->version, '4.1.8', '>=')){
      return $this->view->layout()->staticBaseUrl;
    } else {
      return $this->view->baseUrl() . '/';
    }

  }

}
