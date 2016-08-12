<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: BadgesHead.php 02.04.12 09:12 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */



class Hebadge_Controller_Helper_BadgesHead extends Zend_Controller_Plugin_Abstract
{
  public function postDispatch(Zend_Controller_Request_Abstract $request)
  {

    $headScript = new Zend_View_Helper_HeadScript();
    $headScript->appendFile('application/modules/Hebadge/externals/scripts/core.js');

    $view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');

    $helperItemPhoto = $view->view->getHelper('itemPhoto');

    if ($helperItemPhoto instanceof Hebadge_View_Helper_ItemPhoto){

      $script = <<<CONTENT
        //en4.core.runonce.add(function (){
        window.addEvent('domready', function (){
          Hebadge.attachBadge();
        });
CONTENT;

      $headScript->appendScript($script);

    }


    try {

      // poll passed
      if ($request->getModuleName() == 'poll' && $request->getControllerName() == 'poll' && $request->getActionName() == 'vote'){
        $api = Engine_Api::_()->hebadge()->getRequireClass('pollpassed');
        $api->check(Engine_Api::_()->user()->getViewer());
      }

      // poll passed
      if ($request->getModuleName() == 'quiz' && $request->getControllerName() == 'index' && $request->getActionName() == 'take'){
        $api = Engine_Api::_()->hebadge()->getRequireClass('quizpassed');
        $api->check(Engine_Api::_()->user()->getViewer());
      }

      // store order
      if ($request->getModuleName() == 'store' && $request->getControllerName() == 'transaction' && $request->getActionName() == 'finish'){
        $api = Engine_Api::_()->hebadge()->getRequireClass('storeorder');
        $api->check(Engine_Api::_()->user()->getViewer());
      }

      // invite
      if ($request->getModuleName() == 'invite' && $request->getControllerName() == 'index' && $request->getActionName() == 'index'){
        $api = Engine_Api::_()->hebadge()->getRequireClass('invite');
        $api->check(Engine_Api::_()->user()->getViewer());
      }

    } catch (Exception $e){
      //die( $e->__toString() );
      throw $e;
    }






  }





}