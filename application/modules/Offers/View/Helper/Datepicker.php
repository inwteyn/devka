<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Datepicker.php 2010-07-02 19:54 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Pageevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Offers_View_Helper_Datepicker extends Zend_View_Helper_FormElement
{
  public function datepicker($name, $value = null, $attibs = null)
  {
    $localeObject = Zend_Registry::get('Locale');

    $months = Zend_Locale::getTranslationList('months', $localeObject);
    $months = $months['format'][$months['default']];

    $days = Zend_Locale::getTranslationList('days', $localeObject);
    $days = $days['format'][$days['default']];

    $js_str = "
      window.addEvent('domready', function (){
        new DatePicker('input[name={$name}]', {
          pickerClass: 'datepicker_vista',
          timePicker: true,
          format: 'Y-m-d H:i',
          inputOutputFormat: 'Y-m-d H:i',
          months : " . Zend_Json::encode(array_values($months)) . ",
          days : " . Zend_Json::encode(array_values($days)) . ",
          allowEmpty: true
        });
      });
    ";

    $this->view->headScript()
        ->appendFile( $this->view->baseUrl() . '/application/modules/Offers/externals/scripts/datepicker.js')
        ->appendScript($js_str);

    return '<div class="datepicker_container '.$name.'-container">'.$this->view->formText($name, $value, $attibs).'</div>';

  }
}