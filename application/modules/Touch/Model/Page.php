<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Page.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Touch_Model_Page extends Core_Model_Item_Abstract
{
  protected $_searchTriggers = false;

  /**
   * Gets an absolute URL to the page to view this item
   *
   * @return string
   */
  public function getHref($params = array())
  {
    // identified
    if( !empty($this->url) ) {
      $id = str_replace(array('_', ' '), '-', $this->url);
    } else if( !empty($this->name) ) {
      $id = str_replace(array('_', ' '), '-', $this->name);
    } else {
      $id = $this->page_id;
    }

    $params = array_merge(array(
      'route' => 'copy_touch_themes',
      'action' => 't_clone',
      'reset' => true,
      'module' => 'touch',
      'controller' => 't_clone',
      'action' => $id
    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  }
  public function getTitle()
  {
      return $this->title;
  }
  
}