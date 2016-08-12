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


class Apptouch_View_Helper_TouchReviewRate extends Engine_View_Helper_HtmlElement {

    public function touchReviewRate($score, $min = false){

        $maxRate = 5;

        $size = ($min) ? 16 : 28;
        $html = '<div class="pagereview_element">';

        for ( $i=0; $i<$maxRate; $i++ ){
            if ( $i+0.125 > $score ){
                $value = '-star-empty-alt';
            } else if ( $i+0.375 > $score ){
                $value = '-star';
            } else if ( $i+0.625 > $score ){
                $value = '-star';
            } else if ( $i+0.875 > $score ){
                $value = '-star';
            } else {
                $value = '-star';
            }
            $html .= '<i class="rate_style ui-icon'.$value.'" id="rate_star_'.($i+1).'"></i>';
        }

        $html .= '</div>';

        return $html;

    }

}