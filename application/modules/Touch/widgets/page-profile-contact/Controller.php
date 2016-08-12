<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagecontact
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2011-07-13 16:01 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Pagecontact
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Touch_Widget_PageProfileContactController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    if(!Engine_Api::_()->touch()->isModuleEnabled('pagecontact'))
      return $this->setNoRender();
    $subject = Engine_Api::_()->core()->getSubject('page');
    $this->view->page_id = $page_id = $subject->getIdentity();

    if (!in_array('pagecontact', (array)$subject->getAllowedFeatures())) {
      $this->setNoRender();
    }

    $topicsTbl = Engine_Api::_()->getDbTable('topics', 'pagecontact');
    $topics = $topicsTbl->getTopics($page_id);

    if ($topics && !$topics->count()) {
      $this->setNoRender();
    }

    $descriptionTbl = Engine_Api::_()->getDbTable('descriptions', 'pagecontact');
    $description = $descriptionTbl->getDescription($page_id);
    $this->view->contactForm = $contactForm = new Pagecontact_Form_Contact($page_id);
    $contactForm->loadDefaultDecorators();
    $contactForm->setDescription($description);
    $contactForm->getDecorator('Description')->setOption('escape', false);
  }
}
