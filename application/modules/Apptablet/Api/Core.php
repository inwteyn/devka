<?php

class Apptablet_Api_Core extends Core_Api_Abstract
{

  public function isActive()
  {
    // Detect the Tablet
    return Engine_Api::_()->apptouch()->isTablet() || (isset($_GET['apptouch-site-mode']) && $_GET['apptouch-site-mode'] == 'apptablet');
  }


}