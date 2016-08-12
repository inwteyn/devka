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

class Touch_Widget_UserLoginOrSignupController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
        // Do not show if logged in
    if( Engine_Api::_()->user()->getViewer()->getIdentity() ) {
      $this->setNoRender();
      return;
    }

    // Display form
    $form = $this->view->form = new Touch_Form_Login();;
    $form->setTitle(null)->setDescription(null);
    $form->removeElement('forgot');

    // Facebook login
    if( 'none' == Engine_Api::_()->getApi('settings', 'core')->core_facebook_enable ) {
      $form->removeElement('facebook');
    }

  }
  
  public function getCacheKey()
  {
    return false;
  }
}