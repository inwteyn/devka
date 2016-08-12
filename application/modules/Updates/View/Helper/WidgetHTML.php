<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: WidgetHTML.php 2010-09-09 10:15 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Updates_View_Helper_WidgetHTML extends Zend_View_Helper_Abstract
{
  public function widgetHTML($content, $items, $params = array())
  {

    $settings = Engine_Api::_()->getApi('settings', 'core');

    if ($content['name'] == 'notifications'){
      return $this->view->partial('widgetHTML/_notifications.tpl', 'updates', array('content'=>$content, 'items'=>$items, 'linkColor'=>$settings->__get('updates.links.color')));
    }

    $data = array(
      'content' => $content,
      'params' => $params,
    );

    $titleColor = $settings->__get('updates.titles.color');
    $data['linkColor'] = $settings->__get('updates.links.color');
    $data['fontColor'] = $settings->__get('updates.font.color');

    $itemsHTML = array();


    foreach ($items as $item)
    {
      try {

        $data['item'] = $item;
        $item_key = $item->getIdentity();

        $data['step'] = 'thumb';
        $itemsHTML[$item_key][0] = $this->view->partial('widgetHTML/_'.$content['name'].'.tpl', 'updates', $data);

        $data['step'] = 'details';
        $itemsHTML[$item_key][1] =  $this->view->partial('widgetHTML/_'.$content['name'].'.tpl', 'updates', $data);

        $data['step'] = 'more_link';
        $itemsHTML[$item_key][2] =  $this->view->partial('widgetHTML/_'.$content['name'].'.tpl', 'updates', $data);

      } catch(Exception $e) {
        /*print_die($e.'');*/
      }
    }
    $data['itemsHTML'] = $itemsHTML;
    $data['items'] = $items;
    $data['content'] = $content;

    try {
      $contentHTML = $this->view->partial('structure/_'.$content['structure'].'.tpl', 'updates', $data);
    } catch(Exception $e){
     // print_log($e);
      return '';
    }


    $contentSettings = '';
    if( isset($params['preview']) ) {
      $contentSettings = $this->view->partial('structure/_preview.tpl', 'updates', $content);
    }

    $html = '';
    $borderBottom = '';
    $cont_css_class = 'content-conteiner';
    $translate    = Zend_Registry::get('Zend_Translate');
    if (isset($content['unite_widget'])) {
      if ($content['parent_title']) {
        $borderBottom = "border-bottom:1px solid";
      }

      $html = '<div style="clear:both;padding-top:10px;" class="' . $cont_css_class . '">' .
                '<div  class="msgtitles" style="' . $borderBottom . $titleColor . ';color:' . $titleColor . ';font-size:14px;font-weight:bold;line-height:20px;width:100%;">' .
        $translate->translate($content['parent_title']) .
                  $contentSettings .
                  '</div>' .
                  $contentHTML .
              '</div><div style="clear:both;"></div>';
    }
    else {
      $html = '<div style="clear:both;padding-top:10px;" class="' . $cont_css_class . '">' .
                '<div  class="msgtitles" style="border-bottom:1px solid ' . $titleColor . ';color:' . $titleColor . ';font-size:14px;font-weight:bold;line-height:20px;width:100%;">' .
        $translate->translate($content['title'])  .
                  $contentSettings .
                  '</div>' .
                  $contentHTML .
              '</div><div style="clear:both;"></div>';
    }

    return $html;
  }
}