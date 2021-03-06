<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Photoviewer
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Photoviewer.php 08.02.13 10:28 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Photoviewer
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.comdelete
 */

define('PHOTOVIEWER_DEBUG', true);

class Photoviewer_Controller_Plugin_Photoviewer extends Zend_Controller_Plugin_Abstract
{
  public function postDispatch(Zend_Controller_Request_Abstract $request)
  {
    $settings = Engine_Api::_()->getDbTable('settings', 'core');

    // Photo Viewer is disabled
    if (!$settings->getSetting('photoviewer.enable' , 1)){
      return ;
    }

    // if our touch is active
    if (Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('apptouch') && Engine_Api::_()->apptouch()->isApptouchMode()){
      return ;
    }


    /**
     * @var $view Zend_View
     */
    $view = Zend_Registry::get('Zend_View');

    $is_jquery = false;
    foreach ($view->headScript()->getContainer() as $item){
      if (!isset($item->attributes) || empty($item->attributes['src'])){
        continue ;
      }
      if (strpos($item->attributes['src'], 'jquery') !== false){
        $is_jquery = true;
      }
    }

    // if jquery is not connected before
    if (!$is_jquery){
      $view->headScript()->appendFile($view->layout()->staticBaseUrl . 'application/modules/Photoviewer/externals/scripts/jquery-1.9.0.min.js');
    }
    $content = <<<CONTENT
  var photoViewerJquery = jQuery.noConflict();
CONTENT;

    $view->headScript()->appendScript($content);

    $view->headScript()->appendFile($view->layout()->staticBaseUrl . 'application/modules/Photoviewer/externals/scripts/jquery.mousewheel.js');
    $view->headScript()->appendFile($view->layout()->staticBaseUrl . 'application/modules/Photoviewer/externals/scripts/jquery.jscrollpane.min.js');


    $view->headScript()->appendFile($view->layout()->staticBaseUrl . 'application/modules/Photoviewer/externals/scripts/PhotoViewer'.(PHOTOVIEWER_DEBUG ? '' : '.min').'.js');


    $slideshow_time = $settings->getSetting('photoviewer.slideshowtime' , 3);

    if ($slideshow_time < 1 || $slideshow_time > 100){ // between 1 and 10 sec
      $slideshow_time = 3;
    }
    $slideshow_time = $slideshow_time*1000;


    $content = <<<CONTENT
jQuery(window).ready(function (){
  PhotoViewer.options.slideshow_time = {$slideshow_time};
  PhotoViewer.bindPhotoViewer();
  window.wpViewerTimer = setInterval(function (){
    PhotoViewer.bindPhotoViewer();
  }, 3000);
});

CONTENT;

    $view->headScript()->appendScript($content);

    $view->headLink()->appendStylesheet($view->layout()->staticBaseUrl . 'application/modules/Photoviewer/externals/css/jquery.jscrollpane.css');
    $view->headLink()->appendStylesheet($view->layout()->staticBaseUrl . 'application/modules/Photoviewer/externals/styles/PhotoViewer'.(PHOTOVIEWER_DEBUG ? '' : '.min').'.css');

    // Tagging
    $view->headScript()
      ->appendFile($view->layout()->staticBaseUrl . 'externals/moolasso/Lasso.js')
      ->appendFile($view->layout()->staticBaseUrl . 'externals/moolasso/Lasso.Crop.js')
      ->appendFile($view->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
      ->appendFile($view->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
      ->appendFile($view->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
      ->appendFile($view->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js')
      ->appendFile($view->layout()->staticBaseUrl . 'externals/tagger/tagger.js');



    // Translates
    $view->headTranslate(array(
      "PHOTOVIEWER_All photos",
      "PHOTOVIEWER_Slideshow",
      "PHOTOVIEWER_from",
      "PHOTOVIEWER_Play",
      "PHOTOVIEWER_Pause",
      "PHOTOVIEWER_Repeat",
      "PHOTOVIEWER_actions",
      "PHOTOVIEWER_loading",
      "Save",
      "Cancel",
      "delete"
    ));


  }


}