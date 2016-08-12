<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: BadgesHead.php 02.04.12 09:12 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */



class Offers_Controller_Helper_OffersHead extends Zend_Controller_Plugin_Abstract
{
  public function postDispatch(Zend_Controller_Request_Abstract $request)
  {


    try {

      // poll passed
      if ($request->getModuleName() == 'poll' && $request->getControllerName() == 'poll' && $request->getActionName() == 'vote'){
        $api = Engine_Api::_()->offers()->getRequireClass('pollpassed');
        $api->check(Engine_Api::_()->user()->getViewer());
      }

      // poll passed
      if ($request->getModuleName() == 'quiz' && $request->getControllerName() == 'index' && $request->getActionName() == 'take'){
        $api = Engine_Api::_()->offers()->getRequireClass('quizpassed');
        $api->check(Engine_Api::_()->user()->getViewer());
      }

      // store order
      if ($request->getModuleName() == 'store' && $request->getControllerName() == 'transaction' && $request->getActionName() == 'finish'){
        $api = Engine_Api::_()->offers()->getRequireClass('storeorder');
        $api->check(Engine_Api::_()->user()->getViewer());
      }

      // invite
      if ($request->getModuleName() == 'invite' && $request->getControllerName() == 'index' && $request->getActionName() == 'index'){
        $api = Engine_Api::_()->offers()->getRequireClass('invite');
        $api->check(Engine_Api::_()->user()->getViewer());
      }

    } catch (Exception $e){
      //die( $e->__toString() );
      throw $e;
    }






  }





}