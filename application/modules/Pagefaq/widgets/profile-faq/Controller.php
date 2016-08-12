<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagefaq
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2011-09-28 15:28 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Pagefaq
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
 

class Pagefaq_Widget_ProfileFAQController extends Engine_Content_Widget_Abstract
{
	protected $_childCount;

  public function indexAction()
  {
	  $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('page');
    $this->view->page_id = $page_id = $subject->getIdentity();

    if (!in_array('pagefaq', (array)$subject->getAllowedFeatures())) {
      return $this->setNoRender();
    }

    $this->view->description = Engine_Api::_()->getDbTable('descriptions', 'pagefaq')->getDescription($page_id);
    $this->view->allFAQs = $allFAQs = Engine_Api::_()->getDbTable('faqs', 'pagefaq')->getAllFAQ($page_id);

    $this->_childCount = $allFAQs->count();

    $content_info = $subject->getContentInfo();
    if(!empty($content_info['content']) && $content_info['content'] == 'pagefaq') {
        $this->view->init_js_str = "
          tabContainerSwitch($$('.tab_layout_pagefaq_profile_faq a')[0], 'generic_layout_container layout_pagefaq_profile_faq');
          ";
    }
  }

  public function getChildCount()
  {
    return $this->_childCount;
  }
}