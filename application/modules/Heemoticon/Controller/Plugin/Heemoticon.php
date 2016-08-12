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
 * @package    Heemoticon
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.comdelete
 */



class Heemoticon_Controller_Plugin_Heemoticon extends Zend_Controller_Plugin_Abstract
{
  public function postDispatch(Zend_Controller_Request_Abstract $request)
  {
    $view = Zend_Registry::get('Zend_View');


    $smiles = $view->wallSmiles()->getJson();
    $view->headScript()->appendFile($view->layout()->staticBaseUrl . 'application/modules/Heemoticon/externals/scripts/core.js');
    $content = <<<CONTENT
    window.addEvent('domready', function(){
      var Heemoticons =  new Heemoticon();
      Heemoticons.smiles = {$smiles};
      if (document.getElementsByClassName('wallFeed')) {
          setInterval(function () {
              Heemoticons.initSmiles();
          }, 200);
      }
      });

CONTENT;
    $view->headScript()->appendScript($content);
    $view->headTranslate(array(
      'Your level does not permit to see this collection.',
      'Remove',
      'Add',
    ));

  }


}