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

class Engine_Form_Decorator_ReviewRate extends Zend_Form_Decorator_Abstract
{
  protected $_placement = null;

  public function render($element)
  {

    $maxRate = 5;

    $html = '<div style="width: '.($maxRate*28).'px">';

    $score = $this->getElement()->getValue();

    for ( $i=0; $i<$maxRate; $i++ ){
      if ( $i+0.125 > $score ){
        $value = '-o';
      } else if ( $i+0.375 > $score ){
        $value = '-half-o';
      } else if ( $i+0.625 > $score ){
        $value = '-half-o';
      } else if ( $i+0.875 > $score ){
        $value = '-half-o';
      } else {
        $value = '';
      }

      $html .= '<i class="rate_style hei-lg hei-star'.$value.'" id="rate_star_'.($i + 1).'"></i>';
    }

    $html .= '</div>';

    return '
    <div id="'.$this->getOption('container').'" class="review_stars">
      <div id="title-wrapper" class="form-wrapper">
        <div id="title-label" class="form-label">
            <label class="optional">'.$this->getElement()->getLabel().'</label>
        </div>
        <div id="title-element" class="form-element">'.$element.$html.'</div>
      </div>
    </div>';

  }

}