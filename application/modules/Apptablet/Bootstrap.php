<?php

class Apptablet_Bootstrap extends Engine_Application_Bootstrap_Abstract
{

  public function __construct($application)
  {
    parent::__construct($application);
    $this->initViewHelperPath();

  }

}