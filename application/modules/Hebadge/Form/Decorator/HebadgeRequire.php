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



class Engine_Form_Decorator_HebadgeRequire extends Zend_Form_Decorator_Abstract
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

    if (empty($options) || empty($options['items']) || !is_array($options['items'])){
      return ;
    }

    foreach ($options['items'] as $type => $item){

      $form = null;

      if( !empty($item['adminForm']) ) {

        if( is_string($item['adminForm']) ) {
          $formClass = $item['adminForm'];
          Engine_Loader::loadClass($formClass);
          $this->view->form = $form = new $formClass();
        } else if( is_array($item['adminForm']) ) {
          $this->view->form = $form = new Hebadge_Form_Subform($item['adminForm']);
        } else {
          throw new Core_Model_Exception('Unable to load admin form class');
        }
      }

      if ($form){
        $form->setElementsBelongTo('require_' . $type);
        if (!empty($item['defaultParams'])){
          $form->populate($item['defaultParams']);
        }
        $form->addDecorator('FormErrors');

        $checkbox = new Zend_Form_Element_Checkbox($type, array('decorators' => array('ViewHelper')));
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
    $html = <<<CONTENT

      <script type="text/javascript">
        en4.core.runonce.add(function (){

          $$('.admin_badge_checkbox input[type=checkbox]').addEvent('change', function (){

            var el = $(this).getParent('.admin_badge_require_item');
            if (this.checked){
              el.getElement('.hebadge_admin_require_count input').set('disabled', false);
            } else {
              el.getElement('.hebadge_admin_require_count input').set('disabled', true);
            }
          });
        });
      </script>

CONTENT;

    $html .= '<div class="admin_badge_require">
      <div class="admin_badge_require_title">'.Zend_Registry::get('Zend_Translate')->translate('HEBADGE_FORM_REQUIRE_LABEL').'</div>';

    foreach ($this->_data as $type => $item){

      if (empty($item['item']['onlyCount'])){
        continue ;
      }

      $html .= '
        <div class="admin_badge_require_item" id="require_item_'.$type.'">
          <div class="admin_badge_checkbox">
            <div class="form-wrapper">
              <div class="hebadge_admin_require_checkbox">'.$item['element']->render().'</div>
              <div class="hebadge_admin_require_label"><label class="optional" for="require_'.$type.'">'.Zend_Registry::get('Zend_Translate')->translate('HEBADGE_ADMIN_REQUIRE_' . strtoupper($type)).'</label></div>
              <div class="hebadge_admin_require_count">
                <input type="text" value="'.$item['form']->getElement('count')->getValue().'" id="require_'.$type.'-count" name="require_'.$type.'[count]" '.(($item['element']->isChecked())?'':'disabled="disabled"').'>
              </div>
            </div>
          </div>
        </div>
      ';

    }

    $html .= '</div>';

    return $html;

  }



/*  public function render($content)
  {
    $html = <<<CONTENT

      <script type="text/javascript">
        en4.core.runonce.add(function (){
          $$('.admin_badge_require_expand').addEvent('click', function (){
            var el = $(this).getParent('.admin_badge_require_item');
            var c = el.getElement('.admin_badge_settings');
            c.setStyle('display', (c.getStyle('display') == 'none') ? 'block' : 'none');
          });
          $$('.admin_badge_checkbox input[type=checkbox]').addEvent('change', function (){
            if (this.checked){
              var el = $(this).getParent('.admin_badge_require_item');
              var c = el.getElement('.admin_badge_settings');
              c.setStyle('display', 'block');
            }
          });
        });
      </script>

CONTENT;

    $html .= '<div class="admin_badge_require">
      <div class="admin_badge_require_title">'.Zend_Registry::get('Zend_Translate')->translate('HEBADGE_FORM_REQUIRE_LABEL').'</div>';
    
    foreach ($this->_data as $type => $item){

      $html .= '
        <div class="admin_badge_require_item" id="require_item_'.$type.'">
          <div class="admin_badge_checkbox">
            <div class="form-wrapper">
              <div class="form-label"><label class="optional" for="require_'.$type.'">'.Zend_Registry::get('Zend_Translate')->translate('HEBADGE_ADMIN_REQUIRE_' . strtoupper($type)).'</label></div>
              <div class="form-element">'.$item['element']->render().'</div>
            </div>
            <a href="javascript:void(0)" class="admin_badge_require_expand">&nbsp;</a>
          </div>
          <div class="admin_badge_settings" style="display:none">'.$item['form']->render().'</div>
        </div>
      ';

    }

    $html .= '</div>';

    return $html;

  }*/

}