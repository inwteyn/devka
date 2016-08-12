<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Survey
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Take.php 2010-07-02 19:45 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Survey
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Survey_Form_Take extends Engine_Form
{
  public $_error = array();
  
  public function init()
  {
    $this->setAttrib('name', 'survey_take');
    
    $module_path = Engine_Api::_()->getModuleBootstrap('survey')->getModulePath();
    $this->addPrefixPath('Engine_Form_Decorator_', $module_path . '/Form/Decorator/', 'decorator');

    $this->addElement('Hidden', 'survey_id', array(
      'allowEmpty' => false,
      'required' => true,
      'order' => 901
    ));
  }
}