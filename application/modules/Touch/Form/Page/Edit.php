<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Edit.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Touch_Form_Page_Edit extends Touch_Form_Page_Create
{
  public function init()
  {
    parent::init();
    $this->setTitle('Edit Page entry')
      ->setDescription('Edit your entry below, then click "Post Entry" to publish the entry on your page.');
    $this->submit->setLabel('Save Changes');
  }
}