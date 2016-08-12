<?php

class Checkin_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
  public function __construct($application)
  {
    parent::__construct($application);
    $this->initViewHelperPath();
    $isEventEnabled = Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('heevent');
    if (!$isEventEnabled) {
      $view = Zend_Registry::get('Zend_View');
      $http_protocol = (_ENGINE_SSL ? 'https://' : 'http://');
      $view->headScript()
        ->appendFile($http_protocol . 'maps.googleapis.com/maps/api/js?sensor=false&libraries=places');
    }
  }
}