<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: UpdatesTimeSelects.php 2010-09-09 10:15 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
 
class Engine_Form_Decorator_AvailableVariables extends Zend_Form_Decorator_Abstract
{
	protected $_placement = null;
	
	public function render($content)
  {
    $view = Zend_Registry::get('Zend_View');
    $widgetTb = Engine_Api::_()->getDbtable('widgets', 'updates');
    $widgets = $widgetTb->getWidgets();
    $newsletter = Engine_Api::_()->getDbtable('modules', 'core')->getModule('updates');
    
    unset($widgets[$newsletter->title]);
    
    $widgetsHTML = '';
    foreach ($widgets as $name => $wids)
    {
      $widgetsHTML .=
      '<tr class="widget_variables" style="display:none">' .
      '<td class="varibale_module_title" valign="top" colspan="2">' .
      $name .
      '</td>' .
      '</tr>';

      foreach($wids as $widget)
      {
        $widgetsHTML .=
        '<tr class="widget_variables" style="display:none">' .
        '<td class="varibale_item" valign="top">' .
        '[' . $widget['name']. ']' .
        '</td>' .
        '<td class="varibale_item_description" valign="top">' .
        $widget['description'] . '<br/>' .
        $view->translate("UPDATES_Options: title, count. Example: [%s title='%s' count='%s']", array($widget['name'], $widget['params']['title'], $widget['params']['count'])) . 
        '</td>' .
        '</tr>';
      }
    }

    $html = $content . '<br/>' .
      '<div style="margin-bottom: 10px;font-size: 11px; color:green" valign="top">' .
      $view->translate('UPDATES_Available Variables') . ':' .
      '</div>' .
      '<table style="max-width: 600px;">' .

      '<tr>' .
      '<td class="varibale_module_title" valign="top" colspan="2">' .
      $newsletter->title .
      '</td>' .
      '</tr>' .

      '<tr>' .
      '<td class="varibale_item" valign="top">' .
      '[displayname]' .
      '</td>' .
      '<td class="varibale_item_description" valign="top">' .
      $view->translate('UPDATES_Displays recipient displayname') .
      '</td>' .
      '</tr>' .

      '<tr>' .
      '<td class="varibale_item" valign="top">' .
      '[email]' .
      '</td>' .
      '<td class="varibale_item_description" valign="top">' .
      $view->translate('UPDATES_EMAIL_VARIABLE_DESCRIPTION') .
      '</td>' .
      '</tr>' .

      '<tr>' .
      '<td class="varibale_item" valign="top">' .
      '[notifications]' .
      '</td>' .
      '<td class="varibale_item_description" valign="top">' .
      $view->translate('UPDATES_NOTIFICATION_VARIABLE_DESCRIPTION') .
      '</td>' .
      '</tr>' .

      '<tr>' .
      '<td class="varibale_item" valign="top">' .
      '[profile_url]' .
      '</td>' .
      '<td class="varibale_item_description" valign="top">' .
      $view->translate('UPDATES_PROFILE_URL_VARIABLE_DESCRIPTION') .
      '</td>' .
      '</tr>' .

      '<tr>' .
      '<td class="varibale_item" valign="top">' .
      '[site_url]' .
      '</td>' .
      '<td class="varibale_item_description" valign="top">' .
      $view->translate("UPDATES_SITE_URL_VARIABLE_DESCRIPTION", array($_SERVER['HTTP_HOST'], $_SERVER['HTTP_HOST'])) .
      '</td>' .
      '</tr>' .

      '<tr>' .
      '<td class="varibale_item" valign="top">' .
      '[unsubscribe_url]' .
      '</td>' .
      '<td class="varibale_item_description" valign="top">' .
      $view->translate("UPDATES_UNSUBSCRIBE_URL_VARIABLE_DESCRIPTION") .
      '</td>' .
      '</tr>' .

      '<tr>' .
      '<td class="varibale_item" valign="top">' .
      '[contact_url]' .
      '</td>' .
      '<td class="varibale_item_description" valign="top">' .
      $view->translate("UPDATES_CONTACT_URL_VARIABLE_DESCRIPTION") .
      '</td>' .
      '</tr>' .


      $widgetsHTML .

      '<tr>' .
      '<td valign="top" colspan="2" style="padding-top: 2px;">' .
        '<a href="javascript://" id="show_link" onclick="$$(\'.widget_variables\').setStyle(\'display\', \'\'); $(this).setStyle(\'display\', \'none\'); $(\'hide_link\').setStyle(\'display\', \'\');">' .
          $view->translate("UPDATES_more") . '&gt;&gt;' .
        '</a>' .
        '<a href="javascript://" id="hide_link" onclick="$$(\'.widget_variables\').setStyle(\'display\', \'none\'); $(this).setStyle(\'display\', \'none\'); $(\'show_link\').setStyle(\'display\', \'\');" style="display:none">' .
          '&lt;&lt;' .$view->translate("UPDATES_hide") .
        '</a>' .
      '</td>' .
      '</tr>' .
      '</table>';
    
    return $html;
  }
}
