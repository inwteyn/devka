<?php

class Offers_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
  public function __construct($application)
  {
    parent::__construct($application);
    $this->initViewHelperPath();

 	 // Add main user javascript
   $headScript = new Zend_View_Helper_HeadScript();
   $headScript->appendFile('application/modules/Offers/externals/scripts/offers.js');

    $front =  Zend_Controller_Front::getInstance();
    $plugin =  new Offers_Controller_Helper_OffersHead();
    $front->registerPlugin($plugin);
  }
}