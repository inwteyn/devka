<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: PagesController.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Core_PagesController extends Touch_Controller_Action_Standard
{
  public function __call($methodName, array $arguments)
  {
    // Not an action
    if( 'Action' != substr($methodName, -6) )
    {
      throw new Zend_Controller_Action_Exception(sprintf('Method "%s" does not exist and was not trapped in __call()',
                                                        $methodName), 500);
    }

    // Get page
    $action = substr($methodName, 0, strlen($methodName) - 6);

    // Have to un inflect
    if( is_string($action) ) {
      $actionNormal = strtolower(preg_replace('/([A-Z])/', '-\1', $action));
      // @todo This may be temporary
      $actionNormal = str_replace('-', '_', $actionNormal);
    }
    
    // Get page object
    $pageTable = Engine_Api::_()->getDbtable('pages', 'touch');
    $pageSelect = $pageTable->select();

    if( is_numeric($actionNormal) )
    {
      $pageSelect->where('page_id = ?', $actionNormal);
    }
    else
    {
      $pageSelect
        ->orWhere('name = ?', str_replace('-', '_', $actionNormal))
        ->orWhere('url = ?', str_replace('_', '-', $actionNormal));
    }
    $pageObject = $pageTable->fetchRow($pageSelect);

    // Page found
    if( null !== $pageObject )
    {
      $content = Engine_Content::getInstance();
    	$content->setStorage($pageTable);
		  $this->_helper->content->setContent($content);
      $this->_helper->content->setContentName($pageObject->page_id)->setEnabled();
      // Render
      $this->_helper->content
          ->setNoRender()
          ->setEnabled();
      return;
    }
    // Missing page
    throw new Zend_Controller_Action_Exception(sprintf('Action "%s" does not exist and was not trapped in __call()', $action), 404);
  }
}