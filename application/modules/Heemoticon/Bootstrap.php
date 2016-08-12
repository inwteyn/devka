<?php

class Heemoticon_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
  public function __construct($application)
  {
    parent::__construct($application);
    if(Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('wall')){
    $this->initViewHelperPath();
    Zend_Controller_Front::getInstance()->registerPlugin(new Heemoticon_Controller_Plugin_Heemoticon());
    }

  }
}