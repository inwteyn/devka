<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: blog-content.php 2012-01-9 13:11:10 ulan $
 * @author     Ulan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

return array(
  array(
    'title' => 'Chat Box',
    'description' => 'Displays the chat box.',
    'category' => 'Chat',
    'type' => 'widget',
    'name' => 'touch.chat',
    'defaultParams' => array(
      'title' => 'Chat',
    ),
    'requirements' => array(
      'viewer',
      'no-subject',
    ),
  ),
  array(
    'title' => 'Chat Panel',
    'description' => 'Displays the chat panel. Put it in footer',
    'category' => 'Chat',
    'type' => 'widget',
    'name' => 'touch.chat-panel',
    'requirements' => array(
      'viewer',
      'no-subject',
    ),
  ),
) ?>