<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: ItemRate.php 2010-07-02 19:53 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Rate_View_Helper_QuickProductRate extends Engine_View_Helper_HtmlElement
{
  public function quickProductRate($product = null, $isProfile = false, $showText = true)
  {
    if (!$product || get_class($product) != 'Store_Model_Product') {
      return '';
    }
    $translate = Zend_Registry::get('Zend_Translate');

    $maxRate = 5; // todo change stars count

    $rate_info = Engine_Api::_()->getDbTable('productreviews', 'rate')->getScore($product->getIdentity());

    if (!is_array($rate_info)) {
      return false;
    }

    $stars_str = $this->view->reviewRate($rate_info['item_score'], true);
    $count_review = false;

    if ($rate_info['count']) {
      $count_review = $this->view->translate(array('based on %s review', 'based on %s reviews', $rate_info['count']), '<b>' . $rate_info['count'] . '</b>');
      if(!$showText) {
        $count_review = '<span class="store_product_rate_count">(' . $rate_info['count'] . ')</span>';
      }
    }

    $text = ($isProfile) ? '' : $translate->translate('STORE_Quick reviews');
    $size = ($isProfile) ? ' font-size: 12px;' : '';

    $score_str = '<div class="he_rate_small_cont"><div class="rate_stars_cont">' .
      '<div style="float: left; margin-right: 10px; font-size: 18px;">' . $text . '</div>' .
      '<div style="margin-top: 3px; float: left;'.$size.'">' .
      $stars_str .
      $count_review .
      '</div></div></div> <div style="clear: both"></div>';

    return $score_str;
  }
}