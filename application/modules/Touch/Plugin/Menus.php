<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Menus.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Touch_Plugin_Menus
{

//	Core_Search
	public function onMenuInitialize_CoreMainSearch($row)
	{
		return $this->getSearch($row);
	}

  public function getLink()
  {
    if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('touch')) {
      if (Engine_Api::_()->touch()->isTouchMode()) {
        $subject = Engine_Api::_()->core()->getSubject();
        $suggest_type = 'link_'.$subject->getType();

        if (Engine_Api::_()->suggest()->isAllowed($suggest_type) && Engine_Api::_()->user()->getViewer()->getIdentity()) {

          $script = "
					  window.suggestOptions = null;
						window.suggestOptions = {
							c: 'Touch.suggestTo',
							listType: 'all',
							m: 'suggest',
							nli: 0,
							l: 'getSuggestItems',
							p: 1,
							t: 'TOUCH_Suggest To Friends',
							ipp: 3,
							contacts: [],
							params: {button_label:en4.core.language.translate('Suggest'), object_type:'".$subject->getType()."', object_id:".$subject->getIdentity().", suggest_type:'" . $suggest_type . "', potential:". (int)($subject->getType() == 'user') ."}
					};

					var touchContacts = new HEContacts(suggestOptions);
					touchContacts.box();
          ";

          return array(
            'label' => 'Suggest To Friends',
            'icon' => 'application/modules/Suggest/externals/images/suggest.png',
            'class' => 'suggest_link full',
            'uri' => "javascript:" . $script,
          );
        } else {
          return false;
        }
      }
    } else {
      return false;
    }
  }

  public function onMenuInitialize_UserProfileSuggest($row)
  {
  	$showLink = Engine_Api::_()->getApi('settings', 'core')->getSetting('suggest.link.user');

    if (!Engine_Api::_()->core()->hasSubject() || !$showLink) {
      return false;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();

    if ($subject->isSelf($viewer)) {
      return false;
    }

    return $this->getLink();
  }

  public function onMenuInitialize_EventProfileSuggest($row)
  {
  	$showLink = Engine_Api::_()->getApi('settings', 'core')->getSetting('suggest.link.event');

    if (!Engine_Api::_()->core()->hasSubject() || !$showLink) {
      return false;
    }

    return $this->getLink();
  }

  public function onMenuInitialize_GroupProfileSuggest($row)
  {
  	$showLink = Engine_Api::_()->getApi('settings', 'core')->getSetting('suggest.link.group');

    if (!Engine_Api::_()->core()->hasSubject() || !$showLink) {
      return false;
    }

    return $this->getLink();
  }

  public function onMenuInitialize_PageProfileSuggest($row)
  {
  	$showLink = Engine_Api::_()->getApi('settings', 'core')->getSetting('suggest.link.page');

    if (!Engine_Api::_()->core()->hasSubject() || !$showLink) {
      return false;
    }

    return $this->getLink();
  }

	//Dashboard Menus
	public function onMenuInitialize_CoreDashboardProfile($row)
	{
    $viewer = Engine_Api::_()->user()->getViewer();
    if( $viewer->getIdentity() )
    {
      return array(
        'label' => $row->label,
        'uri' => $viewer->getHref(),
      );
    }
    return false;
	}

	public function onMenuInitialize_CoreDashboardMessages($row)
	{
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer->getIdentity() )
    {
      return false;
    }

    $message_count = Engine_Api::_()->messages()->getUnreadMessageCount($viewer);
    $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl() . '/';

    return array(
      'label' => Zend_Registry::get('Zend_Translate')->_($row->label) . ( $message_count ? ' (' . $message_count .')' : '' ),
      'route' => 'messages_general',
      'params' => array(
        'action' => 'inbox'
      )
    );
	}

	public function onMenuInitialize_CoreDashboardUpdates($row)
	{
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer->getIdentity() )
    {
      return false;
    }

		return array(
			'label' => Zend_Registry::get('Zend_Translate')->_($row->label),
			'route' => 'default',
			'params' => array(
				'module' => 'activity',
				'controller' => 'notifications'
			)
		);
	}

	public function onMenuInitialize_CoreDashboardSearch($row)
	{
		return $this->getSearch($row);
	}

	public function onMenuInitialize_CoreDashboardAlbum($row)
	{
    $modules = Engine_Api::_()->getDbTable('modules', 'core');
    if(
      $modules->isModuleEnabled('album') ||
      $modules->isModuleEnabled('advalbum') ||
      $modules->isModuleEnabled('sitealbum')
    )
      return true;
    else
      return false;
	}

  public function onMenuInitialize_CoreDashboardMusic($row)
 	{
     $modules = Engine_Api::_()->getDbTable('modules', 'core');
     if(
       $modules->isModuleEnabled('music') ||
       $modules->isModuleEnabled('ynmusic')
     )
       return true;
     else
       return false;
 	}

  public function onMenuInitialize_CoreDashboardVideo($row)
 	{
     $modules = Engine_Api::_()->getDbTable('modules', 'core');
     if(
       $modules->isModuleEnabled('video') ||
       $modules->isModuleEnabled('ynvideo')
     )
 		  return true;
     else
       return false;
 	}

  public function onMenuInitialize_CoreDashboardBlog($row)
 	{
     $modules = Engine_Api::_()->getDbTable('modules', 'core');
     if(
       $modules->isModuleEnabled('blog') ||
       $modules->isModuleEnabled('ynblog')
     )
       return true;
     else
       return false;
 	}

  public function onMenuInitialize_CoreDashboardEvent($row)
 	{
     $modules = Engine_Api::_()->getDbTable('modules', 'core');
     if(
       $modules->isModuleEnabled('event') ||
       $modules->isModuleEnabled('ynevent')
     )
       return true;
     else
       return false;
 	}

  public function onMenuInitialize_CoreDashboardGroup($row)
 	{
     $modules = Engine_Api::_()->getDbTable('modules', 'core');
     if(
       $modules->isModuleEnabled('group') ||
       $modules->isModuleEnabled('advgroup')
     )
 		return true;
     else
       return false;
 	}

	public function getSearch($row)
	{
		$viewer  = Engine_Api::_()->user()->getViewer();
		$request = Zend_Controller_Front::getInstance()->getRequest();

    if(!Engine_Api::_()->getApi('settings', 'core')->core_general_search && !$viewer->getIdentity())
		{
			return false;
    }

    $route = false;

		if( $viewer->getIdentity() )
		{
			$route['route']  = 'default';
			$route['params'] = array(
				'controller' => 'search',
			);
			if(  'core'  == $request->getModuleName()
				&& 'controller' == $request->getControllerName()
				&& 'index'  == $request->getActionName() )
			{
				$route['active'] = true;
			}
		}

		return $route;
	}
  public function onMenuInitialize_CoreFooterTouch($row)
  {
    $router = Zend_Controller_Front::getInstance()->getRouter();
    $return_url = $router->assemble(array());
    $home_page = (Engine_Api::_()->user()->getViewer()->getIdentity()) ? $router->assemble(array('action' => 'home'), 'user_general', true) : $router->assemble(array(), 'home', true);
    $route = array(
      'label' => Zend_Registry::get('Zend_Translate')->_($row->label),
      'enabled' => 1,
      'uri' => $router->assemble(array('mode'=>'touch'), 'touch_mode_switch').'?return_url='.$home_page.'#'.$return_url
    );
    return $route;
  }
}