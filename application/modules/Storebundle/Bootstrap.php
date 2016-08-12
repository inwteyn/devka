<?php

class Storebundle_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
  public function __construct($application)
  {
    $this->initViewHelperPath();
    parent::__construct($application);
  }
}