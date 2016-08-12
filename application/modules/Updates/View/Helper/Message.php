<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Message.php 2010-09-09 10:15 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
 
class Updates_View_Helper_Message extends Zend_View_Helper_Abstract
{
  public function message($contents)
  {
  	$settings = Engine_Api::_()->getApi('settings', 'core');

  	$data = array(
      'contents' => $contents,
  		'linkColor'=>$settings->__get('updates.links.color'),
  		'titleColor'=>$settings->__get('updates.titles.color'),
  		'backgroundColor'=>$settings->__get('updates.background.color'),
  		'fontColor'=>$settings->__get('updates.font.color'),
      'mailService'=>$settings->__get('updates.mailservice'),
    );
    
	return $this->view->partial('structure/_message.tpl', 'updates', $data);
  }
}
