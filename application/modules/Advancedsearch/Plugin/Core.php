<?php
/***/
class Advancedsearch_Plugin_Core extends Zend_Controller_Plugin_Abstract {
  public function onRenderLayoutDefault()
  {
    $view = Zend_Registry::get('Zend_View');
    $types = Engine_Api::_()->advancedsearch()->getAvailableTypes();
    $asTypes = '';

    $translate = Zend_Registry::get('Zend_Translate');
    foreach ($types as $type) {
            /*if (isset($itemIcons[$type])) $icon = $itemIcons[$type];
            else $icon = 'hei hei-globe';*/
            $asTypes .= '<div class="as_type_global_container"><span data-type="' . $type . '">'
              . $translate->translate(strtoupper('ITEM_TYPE_' . $type))
              . '</span><div style="clear: both"></div></div>';
    }
    $script = "var ASTypes = '$asTypes';";

    $view->headScript()
      ->appendScript($script);
    $view->headTranslate(array(
      'AS_Nothing found'
    ));
  }
}
