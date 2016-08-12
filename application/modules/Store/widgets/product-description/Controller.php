<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2011-08-19 17:07:11 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Store_Widget_ProductDescriptionController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
      if (Engine_Api::_()->core()->hasSubject()) {
        /**
         * @var $subject Store_Model_Product
         */
        $subject = Engine_Api::_()->core()->getSubject();

        if (!$subject instanceof Store_Model_Product) {
          return $this->setNoRender();
        }
      } else {
        return $this->setNoRender();
      }

       
      $this->view->product = $subject;
    }
}