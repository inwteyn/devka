<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Touch_Widget_ProfileStatusController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {

		$this->view->showLikeButton = $showLikeButton = $this->_getParam('showlikebutton', 1);

		
		if ($showLikeButton)
    $this->view->isLikeEnabled = $likeEnabled = Engine_Api::_()->touch()->isModuleEnabled('like');
		else
			$this->view->isLikeEnabled = $likeEnabled = false;
    
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }

    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
  }
}