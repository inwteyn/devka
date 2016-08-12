<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advnotifications
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Core.php 08.10.13 14:26 Ulan T $
 * @author     Ulan T
 */

/**
 * @category   Application_Extensions
 * @package    Advnotifications
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Advnotifications_Plugin_Core
{
  public function onRenderLayoutDefault($event)
  {
    $view = $event->getPayload();
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$viewer || !$viewer->getIdentity()) {
      return;
    }
    $script = <<<EOF
  window.addEvent('domready', function(){
    advNotificationUpdater = new AdvNotificationUpdateHandler();
    advNotificationUpdater.start();
    window._advNotificationUpdater = advNotificationUpdater;
  });
EOF;
    $view->headScript()
      ->appendFile($view->layout()->staticBaseUrl . 'application/modules/Advnotifications/externals/scripts/core.js')
      ->appendScript($script);
  }
}
