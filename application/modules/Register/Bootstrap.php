<?php

class Register_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
  public function __construct($application)
  {
    parent::__construct($application);

    // Add view helper and action helper paths
    $this->initViewHelperPath();
    $this->initActionHelperPath();
  }
}