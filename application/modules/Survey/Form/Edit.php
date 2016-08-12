<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Survey
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Edit.php 2010-07-02 19:47 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Survey
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Survey_Form_Edit extends Survey_Form_Create
{
  public function init()
  {
    parent::init();
    
    $this->setTitle('Edit Survey')
      ->setDescription('Edit survey description');

    $this->submit->setLabel('Save Changes')->setName('saved');
  }
}