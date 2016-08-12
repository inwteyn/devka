<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: options.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

?><?php
  $type = $this->object->getType();
  $id = $this->object->getIdentity();
  $label = $this->translate("suggest_view_this_".$type);

  switch ($type) {
    case 'group':
    case 'event':
      $url = $this->url(array(
          'controller' => 'member',
          'action' => 'join',
          $type.'_id' => $id
        ), $type.'_extended');

      $params = array('class' => 'touchajax buttonlink icon_'.$type.'_join suggest_widget_link');
    break;
    case 'user':
      $url = $this->url(array(
          'controller' => 'friends',
          'action' => 'add',
            'user_id' => $id
        ), 'user_extended');

      $params = array('class' => 'touchajax buttonlink icon_friend_add suggest_widget_link', 'style' => 'float: left; margin-top: 3px;');
    break;
    default:
      $url = $this->url(array(
          'controller' => 'index',
          'action' => 'accept-suggest',
          'object_type' => $type,
          'object_id' => $id,
        ), 'suggest_general');

      $params = array('class' => 'touchajax buttonlink suggest_widget_link suggest_view_'.$type);
    break;
  }
  
  echo $this->htmlLink($url, $label, $params);
?>