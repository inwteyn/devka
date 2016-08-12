<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagevideo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminSettingsController.php 2010-09-20 17:46 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Pagevideo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagevideo_AdminSettingsController extends Core_Controller_Action_Admin
{
	public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('page_admin_main', array(), 'page_admin_main_addons');
  }
  
	public function indexAction()
  {
  	$this->view->form = $form = new Pagevideo_Form_Admin_Global();
  	$settings = Engine_Api::_()->getApi('settings', 'core');

    $form->video_ffmpeg_path->setValue($settings->getSetting('video.ffmpeg.path'));
    $form->video_jobs->setValue($settings->getSetting('pagevideo.jobs'));
    $form->video_page->setValue($settings->getSetting('pagevideo.page'));
    $form->allow->setValue($settings->getSetting('page.browse.pagevideo'));
  	
  	if (!$this->getRequest()->isPost()){
  		return ;
  	}
  	
    if (!$form->isValid($this->getRequest()->getPost())){
      return ;
    }
    
    // Check license
    $hecoreApi = Engine_Api::_()->getApi('core', 'hecore');
    $product_result = $hecoreApi->checkProduct('page_videos');
    
    if (isset($product_result['result']) && !$product_result['result']){
      $form->addError($product_result['message']);
      $this->view->headScript()->appendScript($product_result['script']);
      return;
    }
  	
  	$value = $form->getValue('video_ffmpeg_path');
  	$settings->setSetting('video.ffmpeg.path', $value);
  	$form->video_ffmpeg_path->setValue($value);
  	
  	$value = $form->getValue('video_jobs');
    $settings->setSetting('pagevideo.jobs', $value);
  	$form->video_jobs->setValue($value);
  	
  	$value = $form->getValue('video_page');
    $settings->setSetting('pagevideo.page', $value);
    $form->video_page->setValue($value);

    $value = $form->getValue('allow');
    $settings->setSetting('page.browse.pagevideo', $value);
    $form->allow->setValue($value);

    $form->addNotice('Your changes have been saved.');
  }
}