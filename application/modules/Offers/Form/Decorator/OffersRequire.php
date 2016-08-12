<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: HebadgeRequire.php 02.04.12 09:12 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Engine_Form_Decorator_OffersRequire extends Zend_Form_Decorator_Abstract
{

    protected $_placement = null;
    protected $_data = array();

    public function getData()
    {
        return $this->_data;
    }

    public function __construct($options = null)
    {
        parent::__construct($options);

        if (empty($options) || empty($options['items']) || !is_array($options['items'])) {
            return;
        }

        foreach ($options['items'] as $type => $item) {

            $form = null;

            if (!empty($item['adminForm'])) {

                if (is_string($item['adminForm'])) {
                    $formClass = $item['adminForm'];
                    Engine_Loader::loadClass($formClass);
                    $this->view->form = $form = new $formClass();
                } else if (is_array($item['adminForm'])) {
                    $this->view->form = $form = new Offers_Form_Subform($item['adminForm']);
                } else {
                    throw new Core_Model_Exception('Unable to load admin form class');
                }
            }

            if ($form) {
                $form->setElementsBelongTo('require_' . $type);
                if (!empty($item['defaultParams'])) {
                    $form->populate($item['defaultParams']);
                }
                $form->addDecorator('FormErrors');

                $checkbox = new Zend_Form_Element_Checkbox('check_' . $type, array('decorators' => array('ViewHelper')));
                $checkbox->setBelongsTo('require');

                $this->_data[$type] = array(
                    'form' => $form,
                    'item' => $item,
                    'element' => $checkbox
                );
            }
        }

    }

    public function render($content)
    {
        $translate = Zend_Registry::get('Zend_Translate');
        $html = <<<CONTENT

      <script type="text/javascript">
        en4.core.runonce.add(function (){

          $$('.offers_checkbox input[type=checkbox]').addEvent('change', function (){

            var el = $(this).getParent('.offers_require_item');
            if (this.checked){
              el.getElement('.offers_require_count input').set('disabled', false);
            } else {
              el.getElement('.offers_require_count input').set('disabled', true);
            }
          });

          $('offers_require_enable').addEvent('change', function (){
            if (this.checked){
                $('offers_list_require').show();
            } else {
                $('offers_list_require').hide();
            }
          });

        });
      </script>

CONTENT;

        $html .= '<div class="offers_require" id="offers_require">
      <div class="offers_require_title form-label">' . $translate->translate('OFFERS_FORM_REQUIRE_LABEL') . '</div>
      <div id="offers_require_enable_box">
        <input type="checkbox" id="offers_require_enable" name="offers_require_enable">
        <label class="optional" for="offers_require_enable">'. $translate->translate('OFFERS_CREATE_REQUIRE_ENABLE') .'</label>
      </div>
      <ul id="offers_list_require" class="offers_list_require">
      ';

        foreach ($this->_data as $type => $item) {

            if (empty($item['item']['onlyCount'])) {
                continue;
            }

            $html .= '
        <li class="offers_require_item" id="require_item_' . $type . '">
          <div class="offers_checkbox">
            <div class="form-wrapper">
              <div class="form-element">
                <div class="offers_require_label"><label class="optional" for="require-check_' . $type . '">' . $translate->translate('OFFERS_CREATE_REQUIRE_' . strtoupper($type)) . '</label></div>
                <div class="offers_require_checkbox">' . $item['element']->render() . '</div>
                <div class="offers_require_count">
                  <input type="text" value="' . $item['form']->getElement('count')->getValue() . '" id="require_' . $type . '-count" name=require[' . $type . '] ' . (($item['element']->isChecked()) ? '' : 'disabled="disabled"') . '>
                </div>
              </div>
            </div>
          </div>
        </li>
      ';

        }

        $html .= '</ul></div>';

        return $html;

    }
}