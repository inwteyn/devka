<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecontest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: HecontestDatepicker.php 19.10.13 08:20 jungar $
 * @author     Jungar
 */

/**
 * @category   Application_Extensions
 * @package    Hecontest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Hecontest_View_Helper_HecontestDatepicker extends Zend_View_Helper_FormElement
{

    public function hecontestDatepicker($name, $value = null, $attibs = null)
    {
        $localeObject = Zend_Registry::get('Locale');

        $months = Zend_Locale::getTranslationList('months', $localeObject);
        $months = $months['format'][$months['default']];

        $days = Zend_Locale::getTranslationList('days', $localeObject);
        $days = $days['format'][$days['default']];

        $js_str = "
      window.addEvent('domready', function (){
        new DatePicker('input[name={$name}]', {
          pickerClass: 'hecontest-datepicker hecontest-block',
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
            ->appendFile($this->view->baseUrl() . '/application/modules/Hecontest/externals/scripts/datepicker.js')
            ->appendScript($js_str);
        $this->view->headLink()
            ->prependStylesheet($this->view->baseUrl() . '/application/css.php?request=application/modules/Hecontest/externals/styles/datepicker.css');

        return '<div class="datepicker_container ' . $name . '-container">' . $this->view->formText($name, $value, $attibs) . '</div>';

    }

}