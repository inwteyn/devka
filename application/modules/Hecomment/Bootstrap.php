<?php

class Hecomment_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
  public function __construct($application)
  {
    parent::__construct($application);
      $view = Zend_Registry::get('Zend_View');
      $view->headScript()
          ->appendFile($view->layout()->staticBaseUrl . 'application/modules/Hecomment/externals/scripts/core.js')
          ->appendFile($view->layout()->staticBaseUrl . 'application/modules/Wall/externals/scripts/core.js');
  }
}