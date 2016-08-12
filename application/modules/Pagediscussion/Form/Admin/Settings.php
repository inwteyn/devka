<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagediscussion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Settings.php 2010-07-02 19:54 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Pagediscussion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagediscussion_Form_Admin_Settings extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('PAGEDISCUSSION_ADMIN_TITLE')
      ->setDescription('PAGEDISCUSSION_ADMIN_DESCRIPTION');

    $this->addElement('Text', 'perpage_list', array(
      'label' => 'PAGEDISCUSSION_ADMIN_COUNT_TOPIC',
    ));

    $this->addElement('Text', 'perpage_post', array(
      'label' => 'PAGEDISCUSSION_ADMIN_COUNT_POST',
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }

}


