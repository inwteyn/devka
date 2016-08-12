<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Import.php 2010-07-02 19:54 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Inviter_Form_Import extends Engine_Form
{
  public function init()
  {
    $this->setDescription('INVITER_FROM_IMPORT_DESCRIPTION')
       ->clearDecorators()
       ->clearAttribs()
       ->setAttrib( 'id', 'invite_friends');

    $this->addElement('hidden', 'provider_box');

    $this->addElement('text', 'empty', array(
      'style' => 'display: none'
    ));

    $path = Engine_Api::_()->getModuleBootstrap('inviter')->getModulePath();
    $this->addPrefixPath('Engine_Form_Decorator_', $path . '/Form/Decorator/', 'Decorator');

    $this->addDisplayGroupPrefixPath('Engine_Form_Decorator_', $path . '/Form/Decorator/', 'Decorator');
    $this->addDisplayGroup(array_keys($this->getElements()), 'from_elements');
    
    $this->from_elements->addDecorator('DefaultProviders', array('default_providers'=>22));
  }
}