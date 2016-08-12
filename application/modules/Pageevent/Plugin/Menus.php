<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Menus.php 2010-07-02 19:54 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Pageevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pageevent_Plugin_Menus
{

  function onMenuInitialize_PageeventUpcoming()
  {
      $subject = Engine_Api::_()->core()->getSubject();

	  return array(
	    'label' => 'PAGEEVENT_UPCOMING',
      'href' => $subject->getHref().'/content/pageevents/',
	  	'onClick' => 'Pageevent.list("upcoming"); return false;',
	  	'route' => 'page_event'
	  );
  }

  function onMenuInitialize_PageeventPast()
  {
	  return array(
	    'label' => 'PAGEEVENT_PAST',
      'href' => 'javascript:void(0);',
	  	'onClick' => 'Pageevent.list("past");',
	  	'route' => 'page_event'
	  );
  }

  function onMenuInitialize_PageeventUser()
  {
    $subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();
    $auth = Engine_Api::_()->authorization()->context;
    $isAllowedPost = $auth->isAllowed($subject, $viewer, 'event_posting');

		if ($isAllowedPost){
      return array(
        'label' => 'PAGEEVENT_USER',
        'href' => 'javascript:void(0);',
        'onClick' => 'Pageevent.list("user");',
        'route' => 'page_event'
      );
    }
    return false;
  }

  function onMenuInitialize_PageeventCreate()
  {
    $subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();
    $auth = Engine_Api::_()->authorization()->context;
    $isAllowedPost = $auth->isAllowed($subject, $viewer, 'event_posting');

		if ($isAllowedPost){
      return array(
        'label' => 'PAGEEVENT_CREATE',
        'href' => 'javascript:void(0);',
        'onClick' => 'Pageevent.formEvent();',
        'route' => 'page_event'
      );
    }
    return false;
  }

  public function onMenuInitialize_PageeventMainManage($row)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer->getIdentity() ) {
      return false;
    }

    return true;
  }
}