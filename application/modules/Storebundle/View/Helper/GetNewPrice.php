<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: DownloadButton.php 7244 2011-09-01 01:49:53Z mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Core
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Storebundle_View_Helper_GetNewPrice extends Zend_View_Helper_Abstract
{
  public function getNewPrice( $price = 0, $discount = 0)
  {
    if(!$discount) {
      return $price;
    }

    $nPrice = $price - ($price * $discount) / 100;

    return $nPrice;
  }
}