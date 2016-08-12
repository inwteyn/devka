<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Standard.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


abstract class Touch_Controller_Action_Standard extends Core_Controller_Action_Standard
{
  public function is_iPhoneUploading()
	{
		$this->view->chash = $this->_getParam('chash');
    $is = ((preg_match('/imageuploader/', Engine_Api::_()->touch()->getUserAgent()) ||
          (preg_match('/picup/', Engine_Api::_()->touch()->getUserAgent()) && preg_match('/cfnetwork/', Engine_Api::_()->touch()->getUserAgent()))) &&
    			$this->_getParam('owner_id', false)
    			);
		return $is;
	}
  protected function initContent($hide_tpl = false){
    // Create content
    $content = Engine_Content::getInstance();
    $table = Engine_Api::_()->getDbtable('pages', 'touch');
    $content->setStorage($table);
    $this->_helper->content->setContent($content);

    if($hide_tpl)
      $this->_helper->content->setNoRender();

    // Render content
      $this->_helper->content->setEnabled();
  }
  public function postDispatch()
  {
    parent::postDispatch();
    $layoutHelper = $this->_helper->layout;
    if( 'default' == $layoutHelper->getLayout() && $this->_getParam('module', false) )
    {
      // Increment page views and referrer
      Engine_Api::_()->getDbtable('statistics', 'core')->increment('touch.core.views');
    }
  }

}