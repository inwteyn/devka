<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: ReviewRate.php 2010-07-02 19:47 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Rate_View_Helper_ReviewRateMobile extends Engine_View_Helper_HtmlElement {

  public function reviewRateMobile($score, $min = false){

    $maxRate = 5;

    $size = ($min) ? 16 : 28;

    $html = '<div style="width: '.($maxRate*$size).'px" class="pagereview_element">';

    for ( $i=0; $i<$maxRate; $i++ ){
      if ( $i+0.125 > $score ){
        $value = 'no_rate';
      } else if ( $i+0.375 > $score ){
        $value = 'quarter_rated';
      } else if ( $i+0.625 > $score ){
        $value = 'half_rated';
      } else if ( $i+0.875 > $score ){
        $value = 'fquarter_rated';
      } else {
        $value = 'rated';
      }
      $html .= '<div class="rate_star '.$value.'" id="rate_star_'.($i+1).'"></div>';
    }

    $html .= '</div>';

    return $html;

  }

}