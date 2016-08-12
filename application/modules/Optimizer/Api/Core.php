<?php

class Optimizer_Api_Core extends Core_Api_Abstract
{

  protected $_ajax_widgets = array();
  protected $_allowedWidgets = null;

  public function addAjaxWidget($content_id, $params)
  {
    $this->_ajax_widgets[] = array(
      'content_id' => $content_id,
      'params' => $params
    );
    return $this;
  }

  public function getAjaxWidgets()
  {
    //return $this->_ajax_widgets;

    // Sort by priority
    $allowed_widgets = $this->getAllowedWidgets();
    $new_array = array();

    $high = 1;
    $normal = 10000;
    foreach ($this->_ajax_widgets as $key => $widget){
      if (!empty($widget['params']) && !empty($widget['params']['name'])){
          $key = (array_search($widget['params']['name'], $allowed_widgets)) ? $high++ : $normal++;
      }
      $new_array[$key] = $widget;
    }

    ksort($new_array);
    $new_array_2 = array();
    foreach ($new_array as $widget){
      $new_array_2[] = $widget;
    }

    return $new_array_2;
  }


  public function getWhitelist()
  {
    $whitelist = array();
    $new_list = array();
    @include './application/modules/Optimizer/settings/whitelist.php';

    foreach ($whitelist as $item){
      // generate short key
      $key = substr(md5($item), 0, 3) . '.js';
      $new_list[$key] = $item;
    }

    return $new_list;
  }

  public function getExternalFiles()
  {
    $external_files = array();
    @include './application/modules/Optimizer/settings/external-files.php';
    return $external_files;
  }


  public function getAllowedPages()
  {
    $allowedPages = array();
    @include './application/modules/Optimizer/settings/allowed-widgets.php';
    return $allowedPages;
  }

  public function getAllowedWidgets()
  {
    if (empty($this->_allowedWidgets)){
      $allowedWidgets = array();
      @include './application/modules/Optimizer/settings/allowed-widgets.php';
      $this->_allowedWidgets = $allowedWidgets;
    }
    return $this->_allowedWidgets;

  }

  public function onRenderLayoutDefault($event)
  {
    // Arg should be an instance of Zend_View
    $view = $event->getPayload();

    if (!($view instanceof Zend_View)) {
      return ;
    }

    $style = <<<CSS_STYLE
@-webkit-keyframes loading{
  0% {-webkit-transform: translateX(-30px); opacity: 0}
  25% {opacity: 1}
  50% {-webkit-transform: translateX(30px); opacity: 0}
  100% {opacity: 0}
}
@-moz-keyframes loading{
  0% {-moz-transform: translateX(-30px); opacity: 0}
  25% {opacity: 1}
  50% {-moz-transform: translateX(30px); opacity: 0}
  100% {opacity: 0}
}
@-o- loading{
  0% {-o-transform: translateX(-30px); opacity: 0}
  25% {opacity: 1}
  50keyframes% {-o-transform: translateX(30px); opacity: 0}
  100% {opacity: 0}
}
@-ms-keyframes loading{
  0% {-ms-transform: translateX(-30px); opacity: 0}
  25% {opacity: 1}
  50% {-ms-transform: translateX(30px); opacity: 0}
  100% {opacity: 0}
}
@keyframes loading{
  0% {-ms-transform: translateX(-30px); opacity: 0}
  25% {opacity: 1}
  50% {-ms-transform: translateX(30px); opacity: 0}
  100% {opacity: 0}
}
CSS_STYLE;

    $view->headStyle()->appendStyle($style);
  }

}