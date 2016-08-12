<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Mail.php 2012-03-06 17:48 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Updates_Model_Mail extends Core_Api_Mail
{
  public function translate($key, $locale, $noDefault = false)
  {
    return $this->_translate($key, $locale, $noDefault = false);
  }
}