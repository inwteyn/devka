<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2011-02-14 07:29:38 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    

class Touch_Widget_PageSearchController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
		$this->view->subject = $subject = Engine_Api::_()->core()->getSubject('page');
		$this->getElement()->setTitle('');

    $this->view->form = $form = new Touch_Form_Search();
    $form->addElement('hidden', 'page_id', array(
      'value' => $subject->getIdentity()
    ));
  }
}